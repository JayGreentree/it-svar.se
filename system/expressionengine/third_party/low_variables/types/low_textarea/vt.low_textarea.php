<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Low_textarea extends Low_variables_type {

	public $info = array(
		'name'    => 'Textarea',
		'version' => LOW_VAR_VERSION
	);

	public $default_settings = array(
		'rows'           => '3',
		'text_direction' => 'ltr',
		'code_format'    => FALSE
	);

	// --------------------------------------------------------------------

	/**
	 * Display settings sub-form for this variable type
	 *
	 * @param      mixed     $var_id        The id of the variable: 'new' or numeric
	 * @param      array     $var_settings  The settings of the variable
	 * @return     array
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

		$rows = $this->get_setting('rows', $var_settings);

		// -------------------------------------
		//  Build settings for rows
		// -------------------------------------

		$r[] = array(
			$this->setting_label(lang('variable_rows')),
			form_input(array(
				'name' => $this->input_name('rows'),
				'value' => $rows,
				'maxlength' => '4',
				'class' => 'x-small'
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
		//  Build settings for code format
		// -------------------------------------

		$r[] = array(
			$this->setting_label(lang('enable_code_format')),
			'<label class="low-checkbox">'.form_checkbox($this->input_name('code_format'), 'y', $this->get_setting('code_format', $var_settings)).' '.
			lang('use_code_format').'</label>'
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
	 * @param      int       $var_id        The id of the variable
	 * @param      string    $var_data      The value of the variable
	 * @param      array     $var_settings  The settings of the variable
	 * @return     string
	 */
	function display_input($var_id, $var_data, $var_settings)
	{
		// -------------------------------------
		//  Check current value from settings
		// -------------------------------------

		$rows = $this->get_setting('rows', $var_settings);

		// -------------------------------------
		//  Set class name for textarea
		// -------------------------------------

		$class = 'large'. ($this->get_setting('code_format', $var_settings) ? ' low_code_format' : '');

		// -------------------------------------
		//  Return input field
		// -------------------------------------

		return form_textarea(array(
			'name'  => "var[{$var_id}]",
			'value' => $var_data,
			'rows'  => $rows,
			'cols'  => '40',
			'class' => $class,
			'dir'   => $this->get_setting('text_direction', $var_settings)
		));
	}

	// --------------------------------------------------------------------

	/**
	 * Display output, possible formatting, extra processing
	 *
	 * @param      string    $tagdata  Current tagdata
	 * @param      array     $var      Variable row
	 * @return     string
	 */
	function display_output($tagdata, $var)
	{
		$var_data = $var['variable_data'];

		// -------------------------------------
		//  Check for extra vars to be pre-parsed
		// -------------------------------------

		$prefix = 'preparse:';
		$offset = strlen($prefix);
		$extra  = array();

		foreach (ee()->TMPL->tagparams AS $key => $val)
		{
			if (substr($key, 0, $offset) == $prefix)
			{
				$extra[substr($key, $offset)] = $val;
			}
		}

		if ($extra)
		{
			ee()->TMPL->log_item('Low Variables: Low_textarea preparse keys: '.implode('|', array_keys($extra)));
			ee()->TMPL->log_item('Low Variables: Low_textarea preparse values: '.implode('|', $extra));
			$var_data = ee()->TMPL->parse_variables_row($var_data, $extra);
		}

		// -------------------------------------
		//  Is there a formatting parameter? (default to depricated 'format' param that could cause errors)
		//  If so, apply the given format
		// -------------------------------------

		if ($format = ee()->TMPL->fetch_param('formatting', ee()->TMPL->fetch_param('format')))
		{
			ee()->TMPL->log_item("Low Variables: Low_textarea applying {$format} formatting");

			ee()->load->library('typography');

			// Set options
			$options = array('text_format' => $format);

			// Allow for html_format
			if ($html = ee()->TMPL->fetch_param('html'))
			{
				$options['html_format'] = $html;
			}

			// Run the Typo method
			$var_data = ee()->typography->parse_type($var_data, $options);
		}

		// -------------------------------------
		// return (formatted) data
		// -------------------------------------

		return (empty($tagdata)
			? $var_data
			: str_replace(LD.$var['variable_name'].RD, $var_data, $tagdata));
	}

	// --------------------------------------------------------------------

}
// End of vt.low_textarea.php