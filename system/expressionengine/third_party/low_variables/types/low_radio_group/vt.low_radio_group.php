<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Low_radio_group extends Low_variables_type {

	public $info = array(
		'name'    => 'Radio Group',
		'version' => LOW_VAR_VERSION
	);

	public $default_settings = array(
		'options' => ''
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
		return array(array(
			$this->setting_label(lang('variable_options'), lang('variable_options_help')),
			form_textarea(array(
				'name'  => $this->input_name('options'),
				'value' => $this->get_setting('options', $var_settings),
				'rows'  => '7',
				'cols'  => '40',
				'style' => 'width:75%'
			))
		));
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
		//  Init return value
		// -------------------------------------

		$r = '';

		// -------------------------------------
		//  Check current value from settings
		// -------------------------------------

		$options = $this->get_setting('options', $var_settings);
		$options = $this->explode_options($options);

		// -------------------------------------
		//  Build checkboxes
		// -------------------------------------

		foreach ($options AS $key => $val)
		{
			$r .= '<label class="low-radio">'
				.	form_radio("var[{$var_id}]", $key, ($key == $var_data))
				.	htmlspecialchars($val)
				. '</label>';
		}

		// -------------------------------------
		//  Return checkboxes
		// -------------------------------------

		return $r;
	}

}