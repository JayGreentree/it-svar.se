<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Low_checkbox_group extends Low_variables_type {

	public $info = array(
		'name'    => 'Checkbox Group',
		'version' => LOW_VAR_VERSION
	);

	public $default_settings = array(
		'options'   => '',
		'separator' => 'newline'
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
		return array(
			array(
				$this->setting_label(lang('variable_options'), lang('variable_options_help')),
				form_textarea(array(
					'name'  => $this->input_name('options'),
					'value' => $this->get_setting('options', $var_settings),
					'rows'  => '7',
					'cols'  => '40',
					'style' => 'width:75%'
				))
			),
			array(
				$this->setting_label(lang('separator_character')),
				$this->separator_select($this->get_setting('separator', $var_settings))
			)
		);
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
		//  Check current value from settings
		// -------------------------------------

		$options = $this->get_setting('options', $var_settings);
		$options = $this->explode_options($options);

		// -------------------------------------
		//  Prep current data
		// -------------------------------------

		$current = explode($this->separators[$this->get_setting('separator', $var_settings)], $var_data);

		// -------------------------------------
		//  Init return value
		// -------------------------------------

		$r = '';

		// -------------------------------------
		//  Build checkboxes
		// -------------------------------------

		foreach ($options AS $key => $val)
		{
			$checked = in_array($key, $current) ? TRUE : FALSE;
			$r .= '<label class="low-checkbox">'
				.	form_checkbox("var[{$var_id}][]", $key, $checked)
				.	htmlspecialchars($val)
				. '</label>';
		}

		// -------------------------------------
		//  Return checkboxes
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
		return is_array($var_data) ? implode($this->separators[$this->get_setting('separator', $var_settings)], $var_data) : '';
	}

}