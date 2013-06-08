<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Low_rte extends Low_variables_type {

	public $info = array(
		'name'    => 'Textarea (Rich Text)',
		'version' => LOW_VAR_VERSION
	);

	public $default_settings = array(
		'rows'           => '10',
		'text_direction' => 'ltr'
	);

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
		//  Return output
		// -------------------------------------

		return $r;
	}

	/**
	 * Display Low Variables field
	 *
	 * @param mixed $data the variable data
	 *
	 * @return string    the field's display
	 */
	public function display_input($var_id, $var_data, $var_settings)
	{
		// Only supported in 2.5.3+
		if (version_compare(APP_VER, '2.5.3') < 0)
		{
			return '<em>The RTE for Low Variables requires ExpressionEngine 2.5.3+</em>';
		}

		// Local cache
		static $loaded;

		// Load the RTE lib
		ee()->load->add_package_path(PATH_MOD.'rte');
		ee()->load->library('rte_lib');

		//add the RTE js if it hasn't already been added
		if ($loaded !== TRUE)
		{
			// Load JS lib
			ee()->load->library('javascript');

			// Add RTE JS to CP
			ee()->javascript->output(
				ee()->rte_lib->build_js(0, '.WysiHat-field', NULL, TRUE)
			);

			// Add FileManager JS to CP
			ee()->load->library(array('filemanager', 'file_field'));
			ee()->file_field->browser();

			$loaded = TRUE;
		}

		// Translate settings
		$settings = array(
			'field_ta_rows'        => $this->get_setting('rows', $var_settings),
			'field_text_direction' => $this->get_setting('text_direction', $var_settings),
			'field_fmt'            => 'none'
		);

		$field_id = 'var_'.$var_id;

		//do this once to properly prep the data,
		//otherwise HTML special chars get wrongly converted
		form_prep($var_data, $field_id);

		//use the channel field display_field method
		$field = ee()->rte_lib->display_field($var_data, $field_id, $settings);

		return preg_replace('/name="var_(\d+)"/i', 'name="var[$1]"', $field);
	}

	/**
	 * Save Low Variable field
	 *
	 * @param mixed $data the var data
	 *
	 * @return string    the data to save to the database
	 */
	public function save_input($var_id, $var_data, $var_settings)
	{
		// Load the RTE lib
		ee()->load->add_package_path(PATH_MOD.'rte');
		ee()->load->library('rte_lib');

		return ee()->rte_lib->save_field($var_data);
	}
}
// End of vt.low_rte.php