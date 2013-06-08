<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Low_text_input extends Low_variables_type {

	public $info = array(
		'name'		=> 'Text Input',
		'version'	=> LOW_VAR_VERSION
	);

	public $default_settings = array(
		'maxlength'      => '',
		'size'           => 'medium',
		'pattern'        => '',
		'text_direction' => 'ltr'
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
		//  Check current value from settings
		// -------------------------------------

		$maxlength = $this->get_setting('maxlength', $var_settings);
		$size = $this->get_setting('size', $var_settings);
		$pattern = $this->get_setting('pattern', $var_settings);
		$text_direction = $this->get_setting('text_direction', $var_settings);

		// -------------------------------------
		//  Build rows for values
		// -------------------------------------

		$r[] = array(
			$this->setting_label(lang('variable_maxlength')),
			form_input(array(
				'name' => $this->input_name('maxlength'),
				'value' => $maxlength,
				'size' => '4',
				'maxlength' => '4',
				'class' => 'x-small'
			))
		);

		$r[] = array(
			$this->setting_label(lang('variable_size')),
			form_dropdown($this->input_name('size'), array(
				'large' => lang('large'),
				'medium' => lang('medium'),
				'small' => lang('small'),
				'x-small' => lang('x-small')
			), $size)
		);

		$r[] = array(
			$this->setting_label(lang('variable_pattern'), lang('variable_pattern_help')),
			form_input(array(
				'name' => $this->input_name('pattern'),
				'value' => $pattern,
				'class' => 'medium'
			))
		);

		// -------------------------------------
		//  Build settings text_direction
		// -------------------------------------

		$dir_options = '';

		foreach (array('ltr', 'rtl') AS $dir)
		{
			$dir_options
				.='<label class="low-radio">'
				. form_radio($this->input_name('text_direction'), $dir, ($this->get_setting('text_direction', $var_settings) == $dir))
				. ' '.lang("text_direction_{$dir}")
				. '</label>';
		}

		$r[] = array(
			$this->setting_label(lang('text_direction')),
			$dir_options
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
		//  Check current value from settings
		// -------------------------------------

		$maxlength = $this->get_setting('maxlength', $var_settings);
		$size = $this->get_setting('size', $var_settings);

		// -------------------------------------
		//  Return input field
		// -------------------------------------

		return form_input(array(
			'name' => "var[{$var_id}]",
			'value' => $var_data,
			'maxlength' => $maxlength,
			'class' => $size,
			'dir' => $this->get_setting('text_direction', $var_settings)
		));
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
		// -------------------------------------
		//  Check if pattern is defined
		// -------------------------------------

		if (($pattern = $this->get_setting('pattern', $var_settings)) && !preg_match($pattern, $var_data, $match))
		{
			$this->error_msg = 'invalid_value';
			$var_data = FALSE;
		}

		return $var_data;
	}

}