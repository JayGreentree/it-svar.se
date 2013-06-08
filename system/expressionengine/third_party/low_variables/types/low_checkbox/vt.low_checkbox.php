<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Low_checkbox extends Low_variables_type {

	public $info = array(
		'name'    => 'Checkbox',
		'version' => LOW_VAR_VERSION
	);

	public $default_settings = array(
		'label' => ''
	);

	// --------------------------------------------------------------------

	/**
	 * Display settings sub-form for this variable type
	 */
	function display_settings($var_id, $var_settings)
	{
		return array(array(
			lang('variable_checkbox_label'),
			form_input(array(
				'name'  => $this->input_name('label'),
				'value' => $this->get_setting('label', $var_settings),
				'class' => 'medium'
			))
		));
	}

	/**
	 * Display input field for regular user
	 */
	function display_input($var_id, $var_data, $var_settings)
	{
		return ''
			. '<label class="low-checkbox">'
			.  form_checkbox("var[{$var_id}]", 'y', ($var_data == 'y'))
			.  htmlspecialchars($this->get_setting('label', $var_settings))
			. '</label>';
	}
}