<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Low Variables Type Class
 *
 * The Low Variables Type base class, to be extended by other classes
 *
 * @package        low_variables
 * @author         Lodewijk Schutte <hi@gotolow.com>
 * @link           http://gotolow.com/addons/low-variables
 * @copyright      Copyright (c) 2009-2013, Low
 */

class Low_variables_type
{
	// --------------------------------------------------------------------
	// PROPERTIES
	// --------------------------------------------------------------------

	/**
	 * Init default settings
	 *
	 * @access     public
	 * @var        array
	 */
	public $default_settings = array();

	/**
	 * Separators
	 *
	 * @access     public
	 * @var        array
	 */
	public $separators = array(
		'newline' => "\n",
		'pipe'    => '|',
		'comma'   => ','
	);

	/**
	 * Interfaces for multiple selects
	 *
	 * @access     public
	 * @var        array
	 */
	public $multi_interfaces = array(
		'select',
		'drag-list'
	);

	// --------------------------------------------------------------------

	/**
	 * EE superobject (deprecated, leave for backward compatibility)
	 *
	 * @access     protected
	 * @var        object
	 */
	protected $EE;

	/**
	 * Site id shortcut
	 *
	 * @access     protected
	 * @var        int
	 */
	protected $site_id;

	// --------------------------------------------------------------------

	/**
	 * Variable type
	 *
	 * @access     private
	 * @var        string
	 */
	private $type;

	/**
	 * Assets loaded
	 *
	 * @access     private
	 * @var        bool
	 */
	private $assets_loaded = FALSE;

	// --------------------------------------------------------------------
	// METHODS
	// --------------------------------------------------------------------

	/**
	 * Constructor
	 *
	 * @access     public
	 * @param      bool
	 * @return     void
	 */
	public function __construct($get_language_file = TRUE)
	{
		// -------------------------------------
		//  Get global instance (deprecated; leave for custom, leave for backward compatibility)
		// -------------------------------------

		$this->EE =& get_instance();

		// -------------------------------------
		//  Set site shortcut
		// -------------------------------------

		$this->site_id = ee()->config->item('site_id');

		// -------------------------------------
		//  Load stuff
		// -------------------------------------

		ee()->load->helper('form');

		// -------------------------------------
		//  Get the class name, which equals the type
		// -------------------------------------

		$this->type = strtolower(get_class($this));

		// -------------------------------------
		//  Get language files for this type
		// -------------------------------------

		if ($get_language_file)
		{
			// @todo: load variable type language file, but check if it exists first.
			// For now, you have to explicitly set it in $this->language_files
			// The below code cannot fail silently, so just fuhgeddaboudit...

			// ee()->lang->loadfile($this->type, 'low_variables');

			// get extra language file, if specified
			if (isset($this->language_files) && is_array($this->language_files))
			{
				foreach($this->language_files AS $file)
				{
					ee()->lang->loadfile($file, 'low_variables');
				}
			}
		}

		// -------------------------------------
		//  Set custom name, if it's there
		// -------------------------------------

		if ( isset($this->info['name']) && (($name = lang($this->type.'_type_name')) != $this->type.'_type_name')  )
		{
			$this->info['name'] = $name;
		}

	}

	// --------------------------------------------------------------------

	/**
	 * Load assets for this file type
	 *
	 * @access     public
	 * @return     bool
	 */
	public function load_assets()
	{
		// -------------------------------------
		//  Load assets or bail?
		// -------------------------------------

		if ( ! (isset($this->assets) && is_array($this->assets) && count($this->assets) && !$this->assets_loaded) )
		{
			return FALSE;
		}

		// -------------------------------------
		//  Set url for assets
		// -------------------------------------

		$this->asset_url = 	ee()->config->item('theme_folder_url') . "third_party/low_variables/types/{$this->type}/";

		// -------------------------------------
		//  Load CSS files
		// -------------------------------------

		if (isset($this->assets['css']))
		{
			$css = (!is_array($this->assets['css'])) ? array($this->assets['css']) : $this->assets['css'];

			foreach ($css AS $file)
			{
				ee()->cp->add_to_head('<link rel="stylesheet" type="text/css" href="'.$this->asset_url.$file.'" />');
			}
		}

		// -------------------------------------
		//  Load JS files
		// -------------------------------------

		if (isset($this->assets['js']))
		{
			$js = (!is_array($this->assets['js'])) ? array($this->assets['js']) : $this->assets['js'];

			foreach ($js AS $file)
			{
				ee()->cp->add_to_foot('<script type="text/javascript" src="'.$this->asset_url.$file.'"></script>');
			}
		}

		// -------------------------------------
		//  Set loaded to TRUE
		// -------------------------------------

		return ($this->assets_loaded = TRUE);
	}

	// --------------------------------------------------------------------

	/**
	 * Insert manual js, used per instance of this type
	 *
	 * @return	void
	 */
	protected function insert_js($js)
	{
		ee()->cp->add_to_foot(NL.'<script type="text/javascript">'.NL.$js.NL.'</script>'.NL);
	}

	// --------------------------------------------------------------------

	/**
	 * Insert manual css, used per instance of this type
	 *
	 * @return	void
	 */
	protected function insert_css($css)
	{
		ee()->cp->add_to_head(NL.'<style type="text/css">'.NL.$css.NL.'</style>'.NL);
	}

	// --------------------------------------------------------------------

	/**
	 * Get setting from settings array, fallback to default, fallback to FALSE
	 *
	 * @param	string	$key		Name of setting to get
	 * @param	string	$settings	Current settings array
	 * @return	mixed
	 */
	public function get_setting($key, $settings = array())
	{
		if (isset($settings[$key]))
		{
			$val = $settings[$key];
		}
		elseif (isset($this->default_settings[$key]))
		{
			$val = $this->default_settings[$key];
		}
		else
		{
			$val = FALSE;
		}

		return $val;
	}

	// --------------------------------------------------------------------

	/**
	 * Explode variable options to array
	 *
	 * @param	string	$options
	 * @return	array
	 */
	public function explode_options($options = '')
	{
		// -------------------------------------
		//  Initiate output
		// -------------------------------------

		$return_data = array();

		// -------------------------------------
		//  Explode data on new line
		// -------------------------------------

		foreach (explode("\n", trim($options)) AS $option)
		{
			// -------------------------------------
			//  Allow for "key : value" options
			// -------------------------------------

			$option = explode(' : ', $option, 2);

			if (count($option) == 2)
			{
				$key = $option[0];
				$val = $option[1];
			}
			else
			{
				$key = $val = $option[0];
			}

			// -------------------------------------
			//  Add item to return data
			// -------------------------------------

			$return_data[$key] = $val;
		}

		// -------------------------------------
		//  Return exploded data
		// -------------------------------------

		return $return_data;
	}

	// --------------------------------------------------------------------

	/**
	 * Takes a DB result set, returns 'flat' array with key => val
	 *
	 * @param	array	$result_set		array with arrays (1 per result)
	 * @param	string	$key			key value for flat array
	 * @param	string	$val			value value for flat array
	 * @return	array
	 */
	protected function flatten_results($result_set, $key, $val)
	{
		return low_flatten_results($result_set, $val, $key);
	}

	// --------------------------------------------------------------------

	/**
	 * Return the nested array name for input fields
	 *
	 * @param	string	$setting_name		Name of the setting
	 * @param	bool	$multiple			Add brackets at the end
	 * @return	string
	 */
	protected function input_name($setting_name, $multiple = FALSE)
	{
		return 'variable_settings['.$this->type.']['.strtolower($setting_name).']'.($multiple?'[]':'');
	}

	// --------------------------------------------------------------------

	/**
	 * Return radiobuttons for separator choice
	 *
	 * @param	string	$selected		Name of the setting
	 * @return	string
	 */
	protected function separator_select($selected = 'newline')
	{
		$r = '';

		foreach ($this->separators AS $sep => $char)
		{
			$r .= '<label class="low-radio">'
				. 	form_radio($this->input_name('separator'), $sep, ($sep == $selected))
				. 	ee()->lang->line($sep)
				. '</label>';
		}

		return $r;
	}

	// --------------------------------------------------------------------

	/**
	 * Return select box for multi-interface choice
	 *
	 * @param	string	$selected		Name of the setting
	 * @return	string
	 * @since	1.1.5
	 */
	protected function interface_select($selected = 'select', $extra = array())
	{
		$options = array();

		foreach ($this->multi_interfaces AS $interface)
		{
			$options[$interface] = ee()->lang->line($interface);
		}

		if ($extra)
		{
			foreach ($extra AS $key => $val)
			{
				$options[$key] = $val;
			}
		}

		return form_dropdown($this->input_name('multi_interface'), $options, $selected);
	}

	// --------------------------------------------------------------------

	/**
	 * Return the html for drag 'n drop 'n sortable lists
	 *
	 * @param	int		$var_id			ID of the variable
	 * @param	array	$rows			Associative array containing all possible values
	 * @param	array	$selected		Array containing selected keys
	 * @return	string
	 * @since	1.1.5
	 */
	protected function drag_lists($var_id, $rows, $selected = array(), $protect = TRUE)
	{
		// Initiate 'off' and 'on' lists
		$off = $on = array();

		// Sub-template of item in either list
		// Use hidden input type to set values
		$item = '<li><input type="hidden" name="%s" value="%s" />%s</li>';

		// -------------------------------------
		//  Do we have selected values?
		//  Then add them to $on first, in the correct order
		// -------------------------------------

		if (count($selected))
		{
			foreach ($selected AS $id)
			{
				// Skip unknown items
				if (!isset($rows[$id])) continue;

				$label = $protect ? htmlspecialchars($rows[$id]) : $rows[$id];

				// Add item to On list
				$on[] = sprintf($item, "var[{$var_id}][]", htmlspecialchars($id), $label);

				// Unset item so it doesn't get added to the Off list
				unset($rows[$id]);
			}
		}

		// -------------------------------------
		//  Add rows to left list
		// -------------------------------------

		if (count($rows))
		{
			foreach ($rows AS $key => $val)
			{
				$label = $protect ? htmlspecialchars($val) : $val;

				// Set input name to empty, so it isn't sent when submitting the form
				// We'll use JavaScript to set the correct name when the item is dragged to the other list
				$off[] = sprintf($item, '', htmlspecialchars($key), $label);
			}
		}

		// -------------------------------------
		//  Return sub-template of list sub-templates
		// -------------------------------------

		$r  = '<div class="low-drag-lists" id="low-drag-lists-'.$var_id.'">'
			.	'<ul class="low-off">'.implode(NL, $off).'</ul>'
			.	'<ul class="low-on">'.implode(NL, $on).'</ul>'
			. '</div>';

		return $r;

	}

	// --------------------------------------------------------------------

	/**
	 * Return the html for (multiple) select element
	 *
	 * @param	int		$var_id			ID of the variable
	 * @param	array	$rows			Associative array containing all possible values
	 * @param	array	$selected		Array containing selected keys
	 * @param	bool	$multiple		Return a multiple select element or not
	 * @return	string
	 * @since	1.2.0
	 */
	protected function select_element($var_id, $rows, $selected = array(), $multiple = FALSE, $none = TRUE)
	{
		// Determine element name
		$name = "var[{$var_id}]";

		// No optgroups when there's only one
		if (count($rows) == 1 && is_array(current($rows)))
		{
			$rows = current($rows);
		}

		// Entify the lot
		$rows = form_prep($rows);

		// Make sure $selected is an array
		if ( ! is_array($selected))
		{
			$selected = array($selected);
		}

		if ($multiple)
		{
			return form_multiselect($name.'[]', $rows, $selected);
		}
		else
		{
			// Add 'None' option on top of drop down
			if ($none)
			{
				$rows = array('' => '-- '.ee()->lang->line('none').' --') + $rows;
			}

			// Get the first selected element.
			// Otherwise we'll get a multiple select
			$selected = empty($selected) ? FALSE : $selected[0];

			return form_dropdown($name, $rows, $selected);
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Return the html for label + description for a variable
	 *
	 * @param	string	$name			name or label of variable
	 * @param	string	$notes			Optional notes or description
	 * @return	string
	 * @since	1.2.0
	 */
	protected function setting_label($name, $notes = '')
	{
		$r = '<strong class="low-label">'.htmlspecialchars($name).'</strong>';

		if ($notes) $r .= '<div class="low-var-notes">'.$notes.'</div>';

		return $r;
	}

}