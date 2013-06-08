<?php if (!defined('BASEPATH')) die('No direct script access allowed');

/**
 * Channel Forms NUMBER field
 *
 * @package			DevDemon_Forms
 * @author			DevDemon <http://www.devdemon.com> - Lead Developer @ Parscale Media
 * @copyright 		Copyright (c) 2007-2011 Parscale Media <http://www.parscale.com>
 * @license 		http://www.devdemon.com/license/
 * @link			http://www.devdemon.com/forms/
 * @see				http://expressionengine.com/user_guide/development/fieldtypes.html
 */
class FormsField_numbers extends FormsField
{

	/**
	 * Field info - Required
	 *
	 * @access public
	 * @var array
	 */
	public $info = array(
		'title'		=>	'Numbers',
		'name' 		=>	'numbers',
		'category'	=>	'power_tools',
		'version'	=>	'1.0',
	);

	/**
	 * Constructor
	 *
	 * @access public
	 *
	 * Calls the parent constructor
	 */
	public function __construct()
	{
		parent::__construct();
	}

	// ********************************************************************************* //

	public function render_field($field=array(), $template=TRUE, $data)
	{
		$options = array();
		$options['name'] = $field['form_name'];
		$options['class'] = 'text';
		$options['id'] = $field['form_elem_id'];
		$exra_validation = '';

		if ((isset($field['settings']['range_min']) == TRUE && $field['settings']['range_min'] > 0))
		{
			$exra_validation .= ',min['.$field['settings']['range_min'].']';
		}

		if ((isset($field['settings']['range_max']) == TRUE && $field['settings']['range_max'] > 0))
		{
			$exra_validation .= ',max['.$field['settings']['range_max'].']';
		}

		// -----------------------------------------
		// If in publish field, lets disable it
		// -----------------------------------------
		if ($template == FALSE)
		{
			$options['readonly'] = 'readonly';
			$options['name'] = '';
		}

		// -----------------------------------------
		// Add JS Validation support
		// -----------------------------------------
		if ($template == TRUE)
		{
			if ($field['required'] == TRUE)
			{
				$options['class'] .= ' required validate[required,custom[number]'.$exra_validation.'] ';
			}
		}

		// -----------------------------------------
		// Placeholder Text
		// -----------------------------------------
		if (isset($field['settings']['placeholder']) == TRUE)
		{
			$options['placeholder'] = $field['settings']['placeholder'];
			$options['data-placeholder'] = $field['settings']['placeholder'];
		}

		// Form data?
		if ($data != FALSE) $options['value'] = $data;

		// -----------------------------------------
		// Render
		// -----------------------------------------
		$out =	form_input($options);

		return $out;
	}

	// ********************************************************************************* //

	public function validate($field=array(), $data)
	{
		// Prepare the error
		$error = array('type' => 'general', 'msg' => $this->EE->lang->line('form:not_number'));

		// Is this 0?
		if ($data === '0') return TRUE;

		// Is empty.. Kill it
		if ($data == FALSE) {
			if ($field['required'] == TRUE) return $error;
			else return TRUE;
		}

		$result = preg_match('/^[+-]?'. // start marker and sign prefix
		'(((([0-9]+)|([0-9]{1,4}(,[0-9]{3,4})+)))?(\\.[0-9])?([0-9]*)|'. // american
		'((([0-9]+)|([0-9]{1,4}(\\.[0-9]{3,4})+)))?(,[0-9])?([0-9]*))'. // world
		'(e[0-9]+)?'. // exponent
		'$/', // end marker
		$data);

		if ($result == 0) return $error;

		// Range..
		if ((isset($field['settings']['range_min']) == TRUE && $field['settings']['range_min'] > 0) && $data < $field['settings']['range_min'])
		{
			return array('type' => 'general', 'msg' => $this->EE->lang->line('form:range_min_error') . $field['settings']['range_min']);
		}

		if ((isset($field['settings']['range_max']) == TRUE && $field['settings']['range_max'] > 0) && $data > $field['settings']['range_max'])
		{
			return array('type' => 'general', 'msg' => $this->EE->lang->line('form:range_max_error') . $field['settings']['range_max']);
		}

		return TRUE;
	}

	// ********************************************************************************* //

	public function save($field=array(), $data)
	{
		// Find the occurance!
		$comma = strpos($data, ',');
		$dot = strpos($data, '.');

		// Are they both there?
		if ($comma !== FALSE && $dot !== FALSE)
		{
			// 1,111.11
			if ($dot > $comma) $data = str_replace(',', '', $data);

			// 1.111,11
			if ($comma > $dot)
			{
				$data = str_replace('.', '', $data);
				$data = str_replace(',', '.', $data);
			}
		}

		// Replace all spaces!
		$data = str_replace(' ', '', $data);
		return (string) $data;
	}

	// ********************************************************************************* //

	public function output_data($field=array(), $data, $type='html')
	{
		$thousands_sep = (isset($field['settings']['thousands_sep']) == TRUE) ? $field['settings']['thousands_sep'] : '';
		$dec_point = (isset($field['settings']['dec_point']) == TRUE) ? $field['settings']['dec_point'] : '';
		$decimals = (isset($field['settings']['decimals']) == TRUE) ? $field['settings']['decimals'] : '';

		if (strlen($data) == 0) return '';

		// Return!
		return number_format((float)$data, $decimals, $dec_point, $thousands_sep);
	}

	// ********************************************************************************* //

	public function field_settings($settings=array(), $template=TRUE)
	{
		$vData = $settings;

		return $this->EE->load->view('fields/numbers', $vData, TRUE);
	}

	// ********************************************************************************* //

}

/* End of file field.numbers.php */
/* Location: ./system/expressionengine/third_party/forms/fields/field.numbers.php */
