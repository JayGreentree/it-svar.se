<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Low_select extends Low_variables_type {

	public $info = array(
		'name'		=> 'Select',
		'version'	=> LOW_VAR_VERSION
	);

	public $default_settings = array(
		'multiple'	=> 'n',
		'options'	=> '',
		'separator'	=> 'newline'
	);

	// --------------------------------------------------------------------

	/**
	 * Display settings sub-form for this variable type
	 *
	 * @param	mixed	$var_id			The id of the variable: 'new' or numeric
	 * @param	array	$var_settings	The settings of the variable
	 * @return	array	
	 */
	function display_settings($var_id, $var_settings)
	{
		// -------------------------------------
		//  Init return value
		// -------------------------------------

		$r = array();

		// -------------------------------------
		//  Build setting: options
		// -------------------------------------

		$options = $this->get_setting('options', $var_settings);

		$r[] = array(
			$this->setting_label(lang('variable_options'), lang('variable_options_help')),
			form_textarea(array(
				'name' => $this->input_name('options'),
				'value' => $options,
				'rows' => '7',
				'cols' => '40',
				'style' => 'width:75%'
			))
		);

		// -------------------------------------
		//  Build setting: multiple?
		// -------------------------------------

		$multiple = $this->get_setting('multiple', $var_settings);

		$r[] = array(
			$this->setting_label(lang('allow_multiple_items')),
			'<label class="low-checkbox">'.form_checkbox($this->input_name('multiple'), 'y', ($multiple == 'y'), 'class="low-allow-multiple"')
			.lang('allow_multiple_items_label').'</label>'
		);

		// -------------------------------------
		//  Build setting: separator
		// -------------------------------------

		$separator = $this->get_setting('separator', $var_settings);

		$r[] = array(
			$this->setting_label(lang('separator_character')),
			$this->separator_select($separator)
		);

		// -------------------------------------
		//  Build setting: multi interface
		// -------------------------------------

		$multi_interface = $this->get_setting('multi_interface', $var_settings);

		$r[] = array(
			$this->setting_label(lang('multi_interface')),
			$this->interface_select($multi_interface)
		);

		// -------------------------------------
		//  Return output
		// -------------------------------------

		return $r;
	}

	// --------------------------------------------------------------------

	/**
	 * Display input field for regular user
	 *
	 * @param	int		$var_id			The id of the variable
	 * @param	string	$var_data		The value of the variable
	 * @param	array	$var_settings	The settings of the variable
	 * @return	string
	 */
	function display_input($var_id, $var_data, $var_settings)
	{
		// -------------------------------------
		//  Prep options
		// -------------------------------------

		$options = $this->get_setting('options', $var_settings);
		$options = $this->explode_options($options);

		// -------------------------------------
		//  Prep var data and var name
		// -------------------------------------

		$multi = FALSE;

		if ($this->get_setting('multiple', $var_settings) == 'y')
		{
			$var_data = explode($this->separators[$this->get_setting('separator', $var_settings)], $var_data);
			$multi = TRUE;
		}

		// -------------------------------------
		//  Build interface
		// -------------------------------------

		$multi_interface = $this->get_setting('multi_interface', $var_settings);

		if ($multi && $multi_interface == 'drag-list')
		{
			$r = $this->drag_lists($var_id, $options, $var_data);
		}
		else
		{
			$r = $this->select_element($var_id, $options, $var_data, $multi);
		}

		// -------------------------------------
		//  Return select element
		// -------------------------------------

		return $r;
	}

	// --------------------------------------------------------------------

	/**
	 * Prep variable data for saving
	 *
	 * @param	int		$var_id			The id of the variable
	 * @param	mixed	$var_data		The value of the variable, array or string
	 * @param	array	$var_settings	The settings of the variable
	 * @return	string
	 */
	function save_input($var_id, $var_data, $var_settings)
	{
		return is_array($var_data) ? implode($this->separators[$this->get_setting('separator', $var_settings)], $var_data) : $var_data;
	}

}