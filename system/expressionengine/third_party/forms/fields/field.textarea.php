<?php if (!defined('BASEPATH')) die('No direct script access allowed');

/**
 * Channel Forms Textarea field
 *
 * @package			DevDemon_Forms
 * @author			DevDemon <http://www.devdemon.com> - Lead Developer @ Parscale Media
 * @copyright 		Copyright (c) 2007-2011 Parscale Media <http://www.parscale.com>
 * @license 		http://www.devdemon.com/license/
 * @link			http://www.devdemon.com/forms/
 * @see				http://expressionengine.com/user_guide/development/fieldtypes.html
 */
class FormsField_textarea extends FormsField
{

	/**
	 * Field info - Required
	 *
	 * @access public
	 * @var array
	 */
	public $info = array(
		'title'		=>	'Text Area',
		'name' 		=>	'textarea',
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
		$options['class'] = '';
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
		// Rows
		// -----------------------------------------
		if (isset($field['settings']['rows']) == TRUE)
		{
			$options['rows'] = $field['settings']['rows'];
		}

		// -----------------------------------------
		// Cols
		// -----------------------------------------
		if (isset($field['settings']['cols']) == TRUE)
		{
			$options['cols'] = $field['settings']['cols'];
		}

		// -----------------------------------------
		// Disabled
		// -----------------------------------------
		if (isset($field['settings']['disabled']) == TRUE && $field['settings']['disabled'] == 'yes')
		{
			$options['readonly'] = 'readonly';
		}

		// -----------------------------------------
		// Default text
		// -----------------------------------------
		if (isset($field['settings']['default_text']) == TRUE && $field['settings']['default_text'] != FALSE)
		{
			$options['value'] = $field['settings']['default_text'];
		}

		// Form data?
		if ($data != FALSE) $options['value'] = $data;

		// -----------------------------------------
		// Render
		// -----------------------------------------
		$out =	form_textarea($options);


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

		return $this->EE->load->view('fields/textarea', $vData, TRUE);
	}

	// ********************************************************************************* //

}

/* End of file field.textarea.php */
/* Location: ./system/expressionengine/third_party/forms/fields/field.textarea.php */
