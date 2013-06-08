<?php if (!defined('BASEPATH')) die('No direct script access allowed');

/**
 * Channel Forms EMAIL ROUTE field
 *
 * @package			DevDemon_Forms
 * @author			DevDemon <http://www.devdemon.com> - Lead Developer @ Parscale Media
 * @copyright 		Copyright (c) 2007-2011 Parscale Media <http://www.parscale.com>
 * @license 		http://www.devdemon.com/license/
 * @link			http://www.devdemon.com/forms/
 * @see				http://expressionengine.com/user_guide/development/fieldtypes.html
 */
class FormsField_email_route extends FormsField
{

	/**
	 * Field info - Required
	 *
	 * @access public
	 * @var array
	 */
	public $info = array(
		'title'		=>	'Email Route',
		'name' 		=>	'email_route',
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

		$this->default_settings['emails']['labels'] = array('Sales', 'Support', 'Info');
		$this->default_settings['emails']['values'] = array('sales@domain.com', 'support@domain.com', 'info@domain.com');
		$this->default_settings['emails']['default'] = 'sales@domain.com';
	}

	// ********************************************************************************* //

	public function render_field($field=array(), $template=TRUE, $data)
	{
		$field['settings'] = array_merge($this->default_settings, $field['settings']);

		$options = array();
		$options['name'] = $field['form_name'];
		$options['value'] = '';
		$options['class'] = '';

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
		// If in publish field, lets disable it
		// -----------------------------------------
		if ($template == FALSE)
		{
			$options['readonly'] = 'readonly';
			$options['name'] = '';
		}

		// -----------------------------------------
		// Items
		// -----------------------------------------
		$items = array();

		// Create the array
		foreach ($field['settings']['emails']['labels'] as $key => $val)
		{
			$items[ $key.sha1($field['settings']['emails']['values'][$key]) ] = $val;
		}

		// The default selection
		$options['value'] = (isset($field['settings']['emails']['default'])) ? $field['settings']['emails']['default'] : '';

		// Form data?
		if ($data != FALSE) $options['value'] = $data;

		// -----------------------------------------
		// Render
		// -----------------------------------------
		$out =	form_dropdown($options['name'], $items, $options['value'], ' class="'.$options['class'].'" id="'.$field['form_elem_id'].'" ');

		return $out;
	}

	// ********************************************************************************* //

	public function save($field=array(), $data)
	{
		$selected = '';

		$field['settings'] = array_merge($this->default_settings, $field['settings']);

		foreach($field['settings']['emails']['values'] as $key => $val)
		{
			$hash = $key.sha1($val);

			if ($data == $hash)
			{
				if (isset($field['settings']['emails']['labels'][$key])) $selected = $field['settings']['emails']['labels'][$key];
				$this->EE->session->cache['Forms']['EmailAdminOverride'] = $val;
			}
		}

		return $selected;
	}

	// ********************************************************************************* //

	public function field_settings($settings=array(), $template=TRUE)
	{
		$vData = $settings;

		if (isset($vData['emails']['labels']) == FALSE OR is_array($vData['emails']['labels']) == FALSE)
		{
			$vData['emails']['labels'] = array('Sales', 'Support', 'Info');
			$vData['emails']['values'] = array('sales@domain.com', 'support@domain.com', 'info@domain.com');
			$vData['emails']['default'] = 'sales@domain.com';
		}

		return $this->EE->load->view('fields/email_route', $vData, TRUE);
	}

	// ********************************************************************************* //

	public function save_settings($settings=array())
	{
		//$settings = array_merge($this->default_settings, $settings);

		if (isset($settings['emails']['labels']) == FALSE) $settings = $this->default_settings;

		foreach($settings['emails']['labels'] as $key => &$val)
		{
			$val = trim($val);
			if ($val == FALSE) unset($settings['emails']['labels'][$key]);
		}

		foreach($settings['emails']['values'] as $key => &$val)
		{
			$val = trim($val);
			if ($val == FALSE)
			{
				if (isset($settings['emails']['labels'][$key]) == FALSE) unset($settings['emails']['values'][$key]);
				else $val = $settings['emails']['labels'][$key];
			}
		}

		return $settings;
	}

	// ********************************************************************************* //


}

/* End of file field.email_route.php */
/* Location: ./system/expressionengine/third_party/forms/fields/field.email_route.php */
