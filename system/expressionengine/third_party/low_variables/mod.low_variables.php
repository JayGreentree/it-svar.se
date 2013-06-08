<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// include base class
if ( ! class_exists('Low_variables_base'))
{
	require_once(PATH_THIRD.'low_variables/base.low_variables.php');
}

/**
 * Low Variables Module Class
 *
 * Class to be used in templates
 *
 * @package        low_variables
 * @author         Lodewijk Schutte <hi@gotolow.com>
 * @link           http://gotolow.com/addons/low-variables
 * @copyright      Copyright (c) 2009-2013, Low
 */

class Low_variables extends Low_variables_base
{
	// --------------------------------------------------------------------
	//  PROPERTIES
	// --------------------------------------------------------------------

	/**
	 * Return data
	 *
	 * @access     public
	 * @var        string
	 */
	public $return_data = '';

	// --------------------------------------------------------------------

	/**
	 * Variables placeholder
	 *
	 * @access     private
	 * @var        array
	 */
	private $vars = array();

	/**
	 * Var types placeholder
	 *
	 * @access     private
	 * @var        array
	 */
	private $types = array();

	// --------------------------------------------------------------------
	//  METHODS
	// --------------------------------------------------------------------

	/**
	 * Parse global template variables, alias for single use
	 *
	 * @access     public
	 * @return     string
	 * @see        parse()
	 */
	public function single()
	{
		return $this->parse('');
	}

	/**
	 * Parse global template variables, alias for pair use
	 *
	 * @access     public
	 * @return     string
	 * @see        parse()
	 */
	public function pair()
	{
		return $this->parse(ee()->TMPL->tagdata);
	}

	/**
	 * Parse global template variables, call type class if necessary
	 *
	 * @access     public
	 * @param      string
	 * @return     string
	 */
	public function parse($tagdata = FALSE)
	{
		// -------------------------------------
		//  Get site id and var name from var param
		// -------------------------------------

		list($var, $site_id) = $this->_get_var_param();

		// -------------------------------------
		//  Set returndata
		// -------------------------------------

		if ($tagdata === FALSE)
		{
			$tagdata = ee()->TMPL->tagdata;
		}

		// -------------------------------------
		//  Store vars in $this->vars
		// -------------------------------------

		$this->_get_variables($var, $site_id);

		// -------------------------------------
		//  Store var types in $this->types
		// -------------------------------------

		$this->_get_types();

		if ($var && array_key_exists($var, $this->vars))
		{
			// Parse single variable
			// The var we're dealing with here:
			$the_var = $this->vars[$var];

			// Get variable type
			$type = $the_var['variable_type'];

			// Get its object
			if (($OBJ = $this->_get_type_object($type)) !== FALSE)
			{
				$default = TRUE;

				// Check fieldtype bridge / default stuff first
				if (method_exists($OBJ, 'display_output'))
				{
					ee()->TMPL->log_item('Low Variables: Calling '.get_class($OBJ).'::display_output()');

					if (($output = $OBJ->display_output($tagdata, $the_var)) !== FALSE)
					{
						$tagdata = $output;
						$default = FALSE;
					}
				}

				// Are we doing default parsing?
				if ($default)
				{
					ee()->TMPL->log_item('Low Variables: '.$type.'::display_output() not found or FALSE, default parsing now');

					// Check separator value
					$sep = $OBJ->get_setting('separator', $the_var['variable_settings']);

					// Check for multiple values
					if (ee()->TMPL->fetch_param('multiple') == 'yes' && ($sep !== FALSE))
					{
						if (strlen($the_var['variable_data']))
						{
							// Convert variable data to array
							$value_array   = explode($OBJ->separators[$sep], $the_var['variable_data']);
							$total_results = count($value_array);

							// Get labels, if present
							if ($value_labels = $OBJ->get_setting('options', $the_var['variable_settings']))
							{
								$value_labels = $OBJ->explode_options($value_labels);
							}

							// Limit results?
							if (($limit = ee()->TMPL->fetch_param('limit')) && is_numeric($limit) && $total_results > $limit)
							{
								$value_array = array_slice($value_array, 0, $limit);
							}

							// Initiate variables array for template
							$data = array();
							$i = 0;
							$total = count($value_array);

							// Fill variables array with rows
							foreach ($value_array AS $value)
							{
								$data_label = isset($value_labels[$value]) ? $value_labels[$value] : '';
								$data[] = array(
									$var.':data'       => $value,
									$var.':label'      => $the_var['variable_label'],
									$var.':data_label' => $data_label,
									$var.':count'      => ++$i,
									$var.':total_results' => $total,
									// Legacy vars
									'value' => $value,
									$var    => $value,
									'label' => $data_label
								);
							}

							// Parse template
							$tagdata = ee()->TMPL->parse_variables($tagdata, $data);
						}
						else
						{
							// No values -- show No Results
							$tagdata = ee()->TMPL->no_results();
						}
					}
					else
					{
						// replace tagdata normally
						$tagdata = $this->_simple_parse($tagdata, $var);
					}
				}
			}
			// Clean up
			unset($OBJ);
		}
		else
		{
			// No single var was given, so just replace all vars with their values
			ee()->TMPL->log_item('Low Variables: Replacing all variables inside tag pair with their data');
			$tagdata = $this->_simple_parse($tagdata);
		}

		// -------------------------------------
		//  Return parsed data
		// -------------------------------------

		return $tagdata;
	}

	/**
	 * Return the label for a given var
	 *
	 * Usage: {exp:low_variables:label var="my_variable_name"}
	 *
	 * @access     public
	 * @return     string
	 */
	public function label()
	{
		// -------------------------------------
		//  Get site id and var name from var param
		// -------------------------------------

		list($var, $site_id) = $this->_get_var_param();

		// -------------------------------------
		//  Store vars in $this->vars
		// -------------------------------------

		$this->_get_variables($var, $site_id);

		// -------------------------------------
		//  Return the label, if present
		// -------------------------------------

		return isset($this->vars[$var]) ? $this->vars[$var]['variable_label'] : '';
	}

	/**
	 * Fetch and return all options from var settings
	 *
	 * @access     public
	 * @return     string
	 */
	public function options()
	{
		// -------------------------------------
		//  Get site id and var name from var param
		// -------------------------------------

		list($var, $site_id) = $this->_get_var_param();

		// -------------------------------------
		//  Store vars in $this->vars
		// -------------------------------------

		$this->_get_variables($var, $site_id);
		$this->_get_types();

		// -------------------------------------
		//  Initiate return data
		// -------------------------------------

		$this->return_data = ee()->TMPL->tagdata;

		// -------------------------------------
		//  Get parameter
		// -------------------------------------

		if ( ! $var || ! isset($this->vars[$var]))
		{
			ee()->TMPL->log_item('Low Variables: No valid var-parameter found, returning raw data');

			return $this->return_data;
		}

		$the_var = $this->vars[$var];
		$type    = $the_var['variable_type'];
		$options = FALSE;

		// -------------------------------------
		//  Get variable options
		// -------------------------------------

		if (($OBJ = $this->_get_type_object($type)) !== FALSE)
		{
			$options = isset($the_var['variable_settings']['options'])
			         ? $OBJ->explode_options($the_var['variable_settings']['options'])
			         : FALSE;
		}

		// -------------------------------------
		//  No options? Bail out
		// -------------------------------------

		if ( ! $options)
		{
			ee()->TMPL->log_item('Low Variables: No options found, returning no_results');
			$this->return_data = ee()->TMPL->no_results();
		}

		// -------------------------------------
		//  Get variable options and parse 'em
		// -------------------------------------

		// Check separator value
		$sep = $OBJ->get_setting('separator', $the_var['variable_settings']);
		$sep = $OBJ->separators[$sep];

		// Check if separator exists for multi-values variable data
		$current = ($sep) ? explode($sep, $the_var['variable_data']) : array($the_var['variable_data']);

		// Initiate variables array
		$data = array();

		// loop through options, populate variables array
		foreach($options AS $key => $val)
		{
			$data[] = array(
				$var.':data' => $key,
				$var.':label' => $the_var['variable_label'],
				$var.':data_label' => $val,

				'active' => (in_array($key, $current)?'y':''),
				'checked' => (in_array($key, $current)?' checked="checked"':''),
				'selected' => (in_array($key, $current)?' selected="selected"':''),

				// Legacy
				'value' => $key,
				'label' => $val
			);
		}

		// Parse template
		$this->return_data = ee()->TMPL->parse_variables($this->return_data, $data);

		// return parsed data
		return $this->return_data;
	}

	// --------------------------------------------------------------------
	//  PRIVATE METHODS
	// --------------------------------------------------------------------

	/**
	 * Get the site id and cleaned var from a var="" parameter value
	 *
	 * @access     private
	 * @return     array
	 */
	private function _get_var_param()
	{
		// -------------------------------------
		//  Get the var parameter value
		// -------------------------------------

		$var = ee()->TMPL->fetch_param('var', FALSE);

		// -------------------------------------
		//  Default site id to current site id
		// -------------------------------------

		$site_id = $this->site_id;

		// -------------------------------------
		//  Get site id based on site_name:var_name value
		// -------------------------------------

		if ( ! empty($var) && ($pos = strpos($var, ':')) !== FALSE)
		{
			// Get the part before the :
			$prefix = substr($var, 0, $pos);

			// If MSM is enabled and if site name is not current site, fetch its id from cache or DB
			if (ee()->config->item('multiple_sites_enabled') == 'y')
			{
				// Check cache, if not set, execute query and register to cache
				if (($sites = low_get_cache($this->package, 'sites')) === FALSE)
				{
					$query = ee()->db->query("SELECT site_id, site_name FROM exp_sites");
					$sites = low_flatten_results($query->result_array(), 'site_id', 'site_name');
					low_set_cache($this->package, 'sites', $sites);
				}

				// If the prefix is a site name, strip it from the var name
				// and use its id to return
				if (array_key_exists($prefix, $sites))
				{
					$var = substr($var, $pos + 1);
					$site_id = $sites[$prefix];

					ee()->TMPL->log_item("Low Variables: Found var {$var} in site {$prefix}");
				}
			}
		}

		// -------------------------------------
		//  Return the site id and cleaned var name
		// ------------------------------------

		return array($var, $site_id);
	}

	/**
	 * Get variables for given site from cache or DB
	 *
	 * @access     private
	 * @param      mixed     [bool|string|array]
	 * @param      int
	 * @return     array
	 */
	private function _get_variables($var = FALSE, $site_id = FALSE)
	{
		// -------------------------------------
		//  If no site id is given, use current
		// -------------------------------------

		if ($site_id == FALSE)
		{
			$site_id = $this->site_id;
		}

		// -------------------------------------
		//  Get cached vars
		// -------------------------------------

		$var_cache = low_get_cache($this->package, 'vars');

		if (isset($var_cache[$site_id]))
		{
			ee()->TMPL->log_item('Low Variables: Getting variables from Session Cache');

			$this->vars = $var_cache[$site_id];
		}
		else
		{
			ee()->TMPL->log_item('Low Variables: Getting variables from Database');

			// -------------------------------------
			//  Query DB
			// -------------------------------------

			$select = array(
				'ee.variable_id',
				'ee.variable_name',
				'ee.variable_data',
				'ee.site_id',
				'low.variable_label',
				'low.variable_type',
				'low.variable_settings'
			);

			ee()->db->select($select)
			             ->from(array('global_variables ee', 'low_variables low'))
			             ->where('ee.variable_id = low.variable_id')
			             ->where('ee.site_id', $site_id);

			// Limit by given vars
			// if ($var != FALSE)
			// {
			// 	$where = is_array($var) ? 'where_in' : 'where';
			// 	ee()->db->$where('ee.variable_name', $var);
			// }

			$rows = ee()->db->get()->result_array();

			// -------------------------------------
			//  Get results
			// -------------------------------------

			foreach ($rows AS $row)
			{
				// Prep settings
				$row['variable_settings'] = low_array_decode($row['variable_settings']);

				// Add prep'd row to data array
				$this->vars[$row['variable_name']] = $row;
			}

			// -------------------------------------
			//  Register to cache
			// -------------------------------------

			$var_cache[$site_id] = $this->vars;
			low_set_cache($this->package, 'vars', $var_cache);
		}

		unset($var_cache);
		return $this->vars;
	}

	/**
	 * Get variables types from cache or settings
	 *
	 * @access     private
	 * @return     array
	 */
	private function _get_types()
	{
		if (($this->types = low_get_cache($this->package, 'types')) === FALSE)
		{
			$this->get_settings();
			$this->types = $this->get_types($this->settings['enabled_types']);
			low_set_cache($this->package, 'types', $this->types);
		}

		return $this->types;
	}

	/**
	 * Create an object from given variable name or type
	 *
	 * @access     private
	 * @param      string
	 * @return     mixed     [object|FALSE]
	 */
	private function _get_type_object($type = FALSE)
	{
		// -------------------------------------
		//  Bail out if type is not supported
		// -------------------------------------

		if ( ! isset($this->types[$type]))
		{
			ee()->TMPL->log_item("Low Variables: Variable type {$type} is not installed or enabled");
			return FALSE;
		}

		// -------------------------------------
		//  Get variable type properties for easy reference
		// -------------------------------------

		$props = $this->types[$type];

		// -------------------------------------
		//  If class doesn't exist, include its file
		// -------------------------------------

		if ( ! class_exists($props['class']))
		{
			if (file_exists($props['path'].$props['file']))
			{
				ee()->TMPL->log_item('Low Variables: Including type class '.$props['class']);
				include_once $props['path'].$props['file'];
			}
			else
			{
				ee()->TMPL->log_item("Low Variables: Could not include files for type {$type}");
				return FALSE;
			}
		}

		// -------------------------------------
		//  Create object and return it
		// -------------------------------------

		return ($props['is_fieldtype'] === TRUE) ? new Low_fieldtype_bridge($props) : new $props['class'];
	}

	/**
	 * Simple default parsing of tagdata with given vars
	 *
	 * @access     private
	 * @param      string
	 * @param      string
	 * @return     string
	 */
	private function _simple_parse($tagdata = '', $var = FALSE)
	{
		// -------------------------------------
		//  If variable is given, limit data to that var
		// -------------------------------------

		if ($var && array_key_exists($var, $this->vars))
		{
			$vars = array($var => $this->vars[$var]);
		}
		else
		{
			$vars = $this->vars;
		}

		// -------------------------------------
		//  If there's tagdata, process the vars
		//  if not, just return the variable data
		// -------------------------------------

		if ($tagdata)
		{
			$data = array();

			foreach ($vars AS $key => $row)
			{
				// {my_var} {my_var:data} and {my_var:label}
				$data[$key] = $data[$key.':data'] = $row['variable_data'];
				$data[$key.':label'] = $row['variable_label'];
			}

			$tagdata = ee()->TMPL->parse_variables_row($tagdata, $data);
		}
		elseif (isset($this->vars[$var]))
		{
			$tagdata = $this->vars[$var]['variable_data'];
		}

		return $tagdata;
	}

	// --------------------------------------------------------------------

} // End class

/* End of file mod.low_variables.php */