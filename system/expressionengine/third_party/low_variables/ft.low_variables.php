<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// include config file
include(PATH_THIRD.'low_variables/config.php');

/**
 * Low Variables Fieldtype class
 *
 * @package        low_variables
 * @author         Lodewijk Schutte <hi@gotolow.com>
 * @link           http://gotolow.com/addons/low-variables
 * @copyright      Copyright (c) 2009-2013, Low
 */

class Low_variables_ft extends EE_Fieldtype {

	// --------------------------------------------------------------------
	//  PROPERTIES
	// --------------------------------------------------------------------

	/**
	 * Info array
	 *
	 * @access     public
	 * @var        array
	 */
	public $info = array(
		'name'    => LOW_VAR_NAME,
		'version' => LOW_VAR_VERSION
	);

	/**
	 * Does fieldtype work in var pair
	 *
	 * @access     public
	 * @var        bool
	 */
	public $has_array_data = TRUE;

	// --------------------------------------------------------------------

	/**
	 * Default settings
	 *
	 * @access     private
	 * @var        array
	 */
	private $default_settings = array(
		'lv_ft_multiple' => FALSE,
		'lv_ft_groups'   => array()
	);

	/**
	 * Package
	 */
	private $package = LOW_VAR_PACKAGE;

	// --------------------------------------------------------------------
	//  METHODS
	// --------------------------------------------------------------------

	/**
	 * Constructor
	 *
	 * @return	void
	 */
	public function __construct()
	{
		parent::__construct();

		// -------------------------------------
		//  Package path
		// -------------------------------------

		ee()->load->add_package_path(PATH_THIRD.$this->package);

		// -------------------------------------
		//  Load helper
		// -------------------------------------

		ee()->load->helper($this->package);
		ee()->lang->loadfile($this->package);
	}

	// --------------------------------------------------------------------

	/**
	 * Display field settings
	 *
	 * @param	array	field settings
	 * @return	string
	 */
	public function display_settings($settings = array())
	{
		$rows = $this->_get_html_settings($settings);

		foreach ($rows AS $row)
		{
			ee()->table->add_row($row);
		}
	}

	/**
	 * Display cell settings
	 *
	 * @param	array	field settings
	 * @return	string
	 */
	public function display_cell_settings($settings = array())
	{
		return $this->_get_html_settings($settings);
	}

	/**
	 * Return array with html for setting forms
	 *
	 * @param	array	field settings
	 * @return	string
	 */
	private function _get_html_settings($settings = array())
	{
		// -------------------------------------
		//  Make sure we have all settings
		// -------------------------------------

		foreach ($this->default_settings AS $key => $val)
		{
			if ( ! array_key_exists($key, $settings))
			{
				$settings[$key] = $val;
			}
		}

		// -------------------------------------
		//  Get variable groups
		// -------------------------------------

		if ( ! ($groups = low_get_cache($this->package, 'groups')))
		{
			$query = ee()->db->select('group_id, group_label')
			       ->from('low_variable_groups')
			       ->where('site_id', ee()->config->item('site_id'))
			       ->order_by('group_order', 'asc')
			       ->get();

			$groups = low_flatten_results($query->result_array(), 'group_label', 'group_id');

			// Add to cache
			low_set_cache($this->package, 'groups', $groups);
		}

		// Make sure there's an array group here
		if ( ! $groups) $groups = array();

		// Add Ungrouped items
		$groups += array('0' => lang('ungrouped'));

		// -------------------------------------
		//  Build per-setting HTML
		// -------------------------------------

		$output = array();

		// Multiple selections?
		$output[] = array(
			lang('lv_ft_multiple'),
			form_checkbox('lv_ft_multiple', 'y', ($settings['lv_ft_multiple'] == 'y'))
		);

		// Variable groups
		$output[] = array(
			lang('lv_ft_groups'),
			form_multiselect('lv_ft_groups[]', $groups, $settings['lv_ft_groups'])
		);

		// Return the settings
		return $output;
	}

	// --------------------------------------------------------------------

	/**
	 * Save field settings
	 *
	 * @access	public
	 * @return	array
	 */
	public function save_settings($data)
	{
		$settings = array();

		foreach ($this->default_settings AS $key => $val)
		{
			if (($settings[$key] = ee()->input->post($key)) === FALSE)
			{
				$settings[$key] = $val;
			}
		}

		return $settings;
	}

	// --------------------------------------------------------------------

	/**
	 * Display field in publish form
	 *
	 * @param	string	Current value for field
	 * @return	string	HTML containing input field
	 */
	public function display_field($data = '', $cell = FALSE)
	{
		// -------------------------------------
		//  What's the field name?
		// -------------------------------------

		$field_name = $cell ? $this->cell_name : $this->field_name;

		// -------------------------------------
		//  We need groups!
		// -------------------------------------

		if (empty($this->settings['lv_ft_groups']))
		{
			return lang('no_variable_group_selected');
		}

		// -------------------------------------
		//  Get all variable groups
		// -------------------------------------

		if ( ! ($groups = low_get_cache($this->package, 'groups')))
		{
			$query = ee()->db->select('group_id, group_label')
			       ->from('low_variable_groups')
			       ->where('site_id', ee()->config->item('site_id'))
			       ->order_by('group_order')
			       ->get();

			$groups = low_flatten_results($query->result_array(), 'group_label', 'group_id');

			if ( ! $groups) $groups = array();
			$groups += array('0' => lang('ungrouped'));

			low_set_cache($this->package, 'groups', $groups);
		}

		// -------------------------------------
		//  Get variables from groups
		// -------------------------------------

		$query = ee()->db->select('ee.variable_name, low.variable_label, low.group_id')
		       ->from('global_variables ee')
		       ->join('low_variables low', 'ee.variable_id = low.variable_id')
		       ->where('ee.site_id', ee()->config->item('site_id'))
		       ->where_in('low.group_id', $this->settings['lv_ft_groups'])
		       ->where('low.early_parsing', 'n')
		       ->where('low.is_hidden', 'n')
		       ->order_by('low.variable_order', 'asc')
		       ->get();

		// Initiate arrays to get vars by
		$unordered_vars = $vars = array();

		// Loop through found vars and group by group label
		foreach ($query->result_array() AS $row)
		{
			$unordered_vars[$row['group_id']][$row['variable_name']] = $row['variable_label'];
		}

		// Loop through groups (which are in the right order)
		// and group the vars by group label to easily create optgroups and such
		foreach ($groups AS $group_id => $group_label)
		{
			if (isset($unordered_vars[$group_id]))
			{
				$vars[$group_label] = $unordered_vars[$group_id];
			}
		}

		// Reduce to 1 dimensional array
		if (count($vars) === 1)
		{
			$vars = $vars[key($vars)];
		}

		// clean up
		unset($unordered_vars);

		// -------------------------------------
		//  Multiple?
		// -------------------------------------

		if (@$this->settings['lv_ft_multiple'] == 'y')
		{
			// Init arrays for checkboxes
			$boxes = array();
			$data  = explode("\n", $data);

			// Loop thru vars and create checkbox in a label
			foreach ($vars AS $key => $val)
			{
				if (is_array($val))
				{
					$boxes[] = "<div style=\"margin:1em 0 .5em\"><strong>{$key}</strong></div>";

					foreach ($val AS $k => $v)
					{
						$boxes[] = $this->_box($field_name, $k, in_array($k, $data), $v);
					}
				}
				else
				{
					$boxes[] = $this->_box($field_name, $key, in_array($key, $data), $val);
				}
			}

			// return string of checkboxes
			return implode("\n", $boxes);
		}
		else
		{
			$vars = array('' => '--') + $vars;
			return form_dropdown($field_name, $vars, $data);
		}
	}

	/**
	 * Display field in Matrix
	 *
	 * @param	string	Current value for field
	 * @return	string	HTML containing input field
	 */
	public function display_cell($data = '')
	{
		return $this->display_field($data, TRUE);
	}

	// --------------------------------------------------------------------

	/**
	 * Return prepped field data to save
	 *
	 * @param	mixed	Posted data
	 * @return	string	Data to save
	 */
	public function save($data = '')
	{
		if (is_array($data))
		{
			$data = implode("\n", $data);
		}

		return $data;
	}

	/**
	 * Return prepped cell data to save
	 *
	 * @param	mixed	Posted data
	 * @return	string	Data to save
	 */
	public function save_cell($data = '')
	{
		return $this->save($data);
	}

	// --------------------------------------------------------------------

	/**
	* Display tag in template
	*
	* @param	string	Current value for field
	* @param	array	Tag parameters
	* @param	bool
	* @return	string
	*/
	public function replace_tag($data, $params = array(), $tagdata = FALSE)
	{
		// -------------------------------------
		//  Init output
		// -------------------------------------

		$it = '';

		// -------------------------------------
		//  Build output depending on tagdata
		// -------------------------------------

		if ($tagdata)
		{
			foreach (explode("\n", $data) AS $var)
			{
				$it .= str_replace(LD.'var'.RD, $var, $tagdata);
			}
		}
		else
		{
			$it = $data;
		}

		// Please
		return $it;
	}

	/**
	* Display {var_name:var}
	*
	* @param	string	Current value for field
	* @param	array	Tag parameters
	* @return	string
	*/
	public function replace_var($data, $params)
	{
		return LD.$data.RD;
	}

	// --------------------------------------------------------------------

	/**
	* Return checkbox
	*
	* @param	string	Name of the field
	* @param	string	Value of the field
	* @param	bool	Checked or not
	* @param	string	The checkbox label
	* @return	string
	*/
	public function _box($name = '', $value = '', $checked = FALSE, $label = '')
	{
		return '<label>'
		     . form_checkbox($name.'[]', $value, $checked)
		     . " {$label}</label>";
	}

}
// END Low_variables_ft class