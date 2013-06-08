<?php if (!defined('BASEPATH')) die('No direct script access allowed');

/**
 * Channel Forms EMAIL field
 *
 * @package			DevDemon_Forms
 * @author			DevDemon <http://www.devdemon.com> - Lead Developer @ Parscale Media
 * @copyright 		Copyright (c) 2007-2011 Parscale Media <http://www.parscale.com>
 * @license 		http://www.devdemon.com/license/
 * @link			http://www.devdemon.com/forms/
 * @see				http://expressionengine.com/user_guide/development/fieldtypes.html
 */
class FormsField_email extends FormsField
{

	/**
	 * Field info - Required
	 *
	 * @access public
	 * @var array
	 */
	public $info = array(
		'title'		=>	'Email',
		'name' 		=>	'email',
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

		$this->default_settings['master_email'] = 'no';
		$this->default_settings['placeholder'] = '';
		$this->default_settings['use_member_email'] = 'yes';
		$this->default_settings['hide_if_member_email'] = 'no';
		$this->default_settings['add_email_to'] = array();
	}

	// ********************************************************************************* //

	public function render_field($field=array(), $template=TRUE, $data='')
	{
		$options = array();
		$options['name'] = $field['form_name'];
		$options['class'] = 'text ';
		$options['type'] = 'email';
		$options['id'] = $field['form_elem_id'];

		$settings = array_merge($this->default_settings, $field['settings']);

		// -----------------------------------------
		// If in publish field, lets disable it
		// -----------------------------------------
		if ($template == FALSE)
		{
			$options['readonly'] = 'readonly';
			$options['name'] = '';
		}

		// -----------------------------------------
		// Placeholder Text
		// -----------------------------------------
		if (isset($settings['placeholder']) == TRUE)
		{
			$options['placeholder'] = $settings['placeholder'];
			$options['data-placeholder'] = $settings['placeholder'];
		}

		// -----------------------------------------
		// Add JS Validation support
		// -----------------------------------------
		if ($template == TRUE)
		{
			if ($field['required'] == TRUE)
			{
				$options['class'] .= ' required validate[required,custom[email]] ';
			}
			else
			{
				$options['class'] .= 'validate[custom[email]] ';
			}
		}

		// -----------------------------------------
		// Use Member Email?
		// -----------------------------------------
		if ($settings['use_member_email'] == 'yes' && $this->EE->session->userdata['member_id'] > 0 && $template === TRUE)
		{
			$options['value'] = $this->EE->session->userdata['email'];

			// Hide this field?
			if ($settings['hide_if_member_email'] == 'yes')
			{
				$this->hidden_field = TRUE;
				$this->hidden_field_value = $this->EE->session->userdata['email'];
				return;
			}
		}

		// Form data?
		if ($data != FALSE) $options['value'] = $data;
		else $options['value'] = '';

		// -----------------------------------------
		// Render
		// -----------------------------------------
		$out =	form_input($options);

		return $out;
	}

	// ********************************************************************************* //

	public function validate($field=array(), $data)
	{
		$settings = array_merge($this->default_settings, $field['settings']);

		// Prepare the error
		$error = $this->EE->lang->line('form:not_email');

		// Is empty.. Kill it
		if ($data == FALSE) {
			if ($field['required'] == TRUE) return $error;
			else return TRUE;
		}

		$result = preg_match("/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix", $data);

		if ($result == 0) return $error;

		// Master Email?
		if ($settings['master_email'] == 'yes')
		{
			$this->EE->forms->master_info['email'] = $data;
			$this->EE->session->userdata['email'] = $data;
		}

		// Add Email to?
		if (isset($settings['add_email_to']) === TRUE && empty($settings['add_email_to']) === FALSE)
		{
			foreach ($settings['add_email_to'] as $section)
			{
				if (isset($this->EE->forms->master_info[$section]) === FALSE) $this->EE->forms->master_info[$section] = array();
				$this->EE->forms->master_info[$section][] = $data;
			}

		}

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
		$vData = array_merge($this->default_settings, $settings);
		return $this->EE->load->view('fields/email', $vData, TRUE);
	}

	// ********************************************************************************* //


}

/* End of file field.email.php */
/* Location: ./system/expressionengine/third_party/forms/fields/field.email.php */
