<?php if (!defined('BASEPATH')) die('No direct script access allowed');

/**
 * Channel Forms NAME field
 *
 * @package			DevDemon_Forms
 * @author			DevDemon <http://www.devdemon.com> - Lead Developer @ Parscale Media
 * @copyright 		Copyright (c) 2007-2011 Parscale Media <http://www.parscale.com>
 * @license 		http://www.devdemon.com/license/
 * @link			http://www.devdemon.com/forms/
 * @see				http://expressionengine.com/user_guide/development/fieldtypes.html
 */
class FormsField_name extends FormsField
{

	/**
	 * Field info - Required
	 *
	 * @access public
	 * @var array
	 */
	public $info = array(
		'title'		=>	'Name',
		'name' 		=>	'name',
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

		$this->default_settings['show_prefix'] = 'no';
		$this->default_settings['show_suffix'] = 'no';
		$this->default_settings['prefix_select'] = 'no';
		$this->default_settings['master_for'] = array('mailinglist', 'billing', 'shipping');
	}

	// ********************************************************************************* //

	public function render_field($field=array(), $template=TRUE, $data)
	{
		$options = array();
		$options['name'] = '';
		$options['value'] = '';
		$options['class'] = 'text ';

		$settings = array_merge($this->default_settings, $field['settings']);

		// -----------------------------------------
		// Add JS Validation support
		// -----------------------------------------
		if ($template == TRUE)
		{
			if ($field['required'] == TRUE)
			{
				$options['class'] .= ' required validate[required] ';
			}
		}

		// If in publish field, lets disable it
		if ($template == FALSE) $options['readonly'] = 'readonly';

		$out = '<div class="dfinput_names">';

		// -----------------------------------------
		// Show Prefix
		// -----------------------------------------
		if (isset($settings['show_prefix']) == TRUE && $settings['show_prefix'] == 'yes')
		{
			if ($template)
			{
				$options['name'] = $field['form_name'].'[prefix]';

				// Form data?
				if (isset($data['prefix'])) $options['value'] = $data['prefix'];
			}

			$out .= '<div class="dfinput_left name_prefix">';

			// Dropdown?
			if (isset($settings['prefix_select']) == TRUE && $settings['prefix_select'] == 'yes')
			{
				$arr = array();
				$arr['Mr.'] = 'Mr';
				$arr['Mrs.'] = 'Mrs';
				$arr['Ms.'] = 'Ms';
				$arr['Miss.'] = 'Miss';
				$arr['Dr.'] = 'Dr. (Doctor)';
				$arr['Prof.'] = 'Prof. (Professor)';
				//$arr['Gov.'] = 'Gov. (Governor)';
				//$arr['Hon.'] = 'Hon. (Honorable)';

				$out .=		form_dropdown($options['name'], $arr, $options['value'], ' style="width:90%;" id="'.$field['form_elem_id'].'_prefix" ');
			}
			else
			{
				$options['id'] = $field['form_elem_id'].'_prefix';
				$out .=		form_input($options);
			}
			$out .= 	'<label for="'.$field['form_elem_id'].'_prefix'.'">' . $this->EE->lang->line('form:prefix') . '</label>';
			$out .= '</div>';
		}

		// -----------------------------------------
		// Show First Name
		// -----------------------------------------
		if ($template)
		{
			$options['name'] = $field['form_name'].'[first_name]';

			// Form data?
			if (isset($data['first_name'])) $options['value'] = $data['first_name'];
		}

		$options['id'] = $field['form_elem_id'].'_first';
		$out .= '<div class="dfinput_left name_first">';
		$out .=		form_input($options);
		$out .= 	'<label for="'.$field['form_elem_id'].'_first'.'">' . $this->EE->lang->line('form:first_name') . '</label>';
		$out .= '</div>';

		// -----------------------------------------
		// Show Last Name
		// -----------------------------------------
		if ($template)
		{
			$options['name'] = $field['form_name'].'[last_name]';

			// Form data?
			if (isset($data['last_name'])) $options['value'] = $data['last_name'];
		}

		$options['id'] = $field['form_elem_id'].'_last';
		$out .= '<div class="dfinput_left name_last">';
		$out .=		form_input($options);
		$out .= 	'<label for="'.$field['form_elem_id'].'_last'.'">' . $this->EE->lang->line('form:last_name') . '</label>';
		$out .= '</div>';

		// -----------------------------------------
		// Show Suffix
		// -----------------------------------------
		if (isset($settings['show_suffix']) == TRUE && $settings['show_suffix'] == 'yes')
		{
			if ($template)
			{
				$options['name'] = $field['form_name'].'[suffix]';

				// Form data?
				if (isset($data['suffix'])) $options['value'] = $data['suffix'];
			}

			// Remove the required classes
			$options['class'] = 'text';
			$options['id'] = $field['form_elem_id'].'_suffix';

			$out .= '<div class="dfinput_left name_suffix">';
			$out .=		form_input($options);
			$out .= 	'<label for="'.$field['form_elem_id'].'_suffix'.'">' . $this->EE->lang->line('form:suffix') . '</label>';
			$out .= '</div>';
		}

		$out .= '<br clear="all">';
		$out .= '</div>'; // dfinput_names

		return $out;
	}

	// ********************************************************************************* //

	public function validate($field=array(), $data)
	{
		$settings = array_merge($this->default_settings, $field['settings']);

		if (in_array('mailinglist', $settings['master_for']))
		{
			$this->EE->forms->master_info['name'] = $data;
		}

		if (in_array('billing', $settings['master_for']))
		{
			$this->EE->forms->master_info['billing_name'] = $data;
		}

		if (in_array('shipping', $settings['master_for']))
		{
			$this->EE->forms->master_info['shipping_name'] = $data;
		}

		// DO we need to check for required?
		if ($field['required'] != 1) return TRUE;

		// Prepare the error
		$error = array('type' => 'general', 'msg' => $this->EE->lang->line('form:error:required_field'));

		// Prefix
		if (isset($settings['show_prefix']) == TRUE && $settings['show_prefix'] == 'yes')
		{
			if ($data['prefix'] == FALSE)
			{
				//return $error;
			}
		}

		// First Name
		if ($data['first_name'] == FALSE)
		{
			return $error;
		}

		// Last Name
		if ($data['last_name'] == FALSE)
		{
			return $error;
		}

		// Suffix
		if (isset($settings['show_suffix']) == TRUE && $settings['show_suffix'] == 'yes')
		{
			if ($data['suffix'] == FALSE)
			{
				//return $error;
			}
		}


		return TRUE;
	}

	// ********************************************************************************* //

	public function save($field, $data)
	{
		return serialize($data);
	}

	// ********************************************************************************* //

	public function output_data($field, $data, $type)
	{
		$data = @unserialize($data);

		$prefix = (isset($data['prefix'])) ? $data['prefix'] : '';
		$first_name = (isset($data['first_name'])) ? $data['first_name'] : '';
		$last_name = (isset($data['last_name'])) ? $data['last_name'] : '';
		$suffix = (isset($data['suffix'])) ? $data['suffix'] : '';

		return ($prefix . ' ' . $first_name . ' ' . $last_name . ' ' . $suffix);
	}

	// ********************************************************************************* //

	public function field_settings($settings, $template)
	{
		$vData = array_merge($this->default_settings, $settings);
		return $this->EE->load->view('fields/name', $vData, TRUE);
	}

	// ********************************************************************************* //

}

/* End of file field.name.php */
/* Location: ./system/expressionengine/third_party/forms/fields/field.name.php */
