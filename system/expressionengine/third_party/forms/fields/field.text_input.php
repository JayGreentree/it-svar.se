<?php if (!defined('BASEPATH')) die('No direct script access allowed');

/**
 * Channel Forms TEXT INPUT field
 *
 * @package			DevDemon_Forms
 * @author			DevDemon <http://www.devdemon.com> - Lead Developer @ Parscale Media
 * @copyright 		Copyright (c) 2007-2011 Parscale Media <http://www.parscale.com>
 * @license 		http://www.devdemon.com/license/
 * @link			http://www.devdemon.com/forms/
 * @see				http://expressionengine.com/user_guide/development/fieldtypes.html
 */
class FormsField_text_input extends FormsField
{

	/**
	 * Field info - Required
	 *
	 * @access public
	 * @var array
	 */
	public $info = array(
		'title'		=>	'Text Box',
		'name' 		=>	'text_input',
		'category'	=>	'form_tools',
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
		$options['class'] = 'text ';
		$options['id'] = $field['form_elem_id'];

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
				$options['class'] .= 'required validate[required] ';
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

		// -----------------------------------------
		// Max Chars
		// -----------------------------------------
		if (isset($field['settings']['max_chars']) == TRUE)
		{
			$options['maxlength'] = $field['settings']['max_chars'];
		}

		// -----------------------------------------
		// Default Value
		// -----------------------------------------
		if (isset($field['settings']['default_value']) == TRUE)
		{
			$options['value'] = $this->parse_default_value($field['settings']['default_value']);
		}


		// Form data?
		if ($data != FALSE) $options['value'] = $data;

		// -----------------------------------------
		// Normal Input ? Or Password Field
		// -----------------------------------------

		if (isset($field['settings']['password_field']) == TRUE && $field['settings']['password_field'] == 'yes')
		{
			$out =	form_password($options);
		}
		else
		{
			$out =	form_input($options);
		}


		return $out;
	}

	// ********************************************************************************* //

	public function validate($field=array(), $data)
	{
		return TRUE;
	}

	// ********************************************************************************* //

	public function save($field=array(), $data)
	{
		return (string) $data;
	}

	// ********************************************************************************* //

	public function field_settings($settings=array(), $template=TRUE)
	{
		$vData = $settings;

		return $this->EE->load->view('fields/text_input', $vData, TRUE);
	}

	// ********************************************************************************* //

	private function parse_default_value($out)
	{
		//----------------------------------------
		// Parse available variables!
		//----------------------------------------
		$vars = array();
		$vars['{user:referrer}'] = (isset($_SERVER['HTTP_REFERER']) == TRUE) ? $_SERVER['HTTP_REFERER'] : '';
		$vars['{date:usa}'] = $this->EE->forms_helper->formatDate('%m/%d/%Y', $this->EE->localize->now, true);
		$vars['{date:eu}'] = $this->EE->forms_helper->formatDate('%d/%m/%Y', $this->EE->localize->now, true);
		$vars['{datetime:usa}'] = $this->EE->forms_helper->formatDate('%m/%d/%Y %h:%i %A', $this->EE->localize->now, true);
		$vars['{datetime:eu}'] =  $this->EE->forms_helper->formatDate('%d/%m/%Y %H:%i', $this->EE->localize->now, true);

		// Parse it!
		$out = str_replace(array_keys($vars), array_values($vars), $out);

		// Parse all user session data too
		foreach($this->EE->session->userdata as $var => $val)
		{
			// Val has arrays? Ignore them!
			if (is_array($val) == TRUE) continue;

			$out = str_replace('{user:'.$var.'}', $val, $out);
		}

		return $out;
	}

	// ********************************************************************************* //

}

/* End of file field.text_input.php */
/* Location: ./system/expressionengine/third_party/forms/fields/field.text_input.php */
