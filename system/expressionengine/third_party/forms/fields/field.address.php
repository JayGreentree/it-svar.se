<?php if (!defined('BASEPATH')) die('No direct script access allowed');

/**
 * Channel Forms ADDRESS field
 *
 * @package			DevDemon_Forms
 * @author			DevDemon <http://www.devdemon.com> - Lead Developer @ Parscale Media
 * @copyright 		Copyright (c) 2007-2011 Parscale Media <http://www.parscale.com>
 * @license 		http://www.devdemon.com/license/
 * @link			http://www.devdemon.com/forms/
 * @see				http://expressionengine.com/user_guide/development/fieldtypes.html
 */
class FormsField_address extends FormsField
{

	/**
	 * Field info - Required
	 *
	 * @access public
	 * @var array
	 */
	public $info = array(
		'title'		=>	'Address',
		'name' 		=>	'address',
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


		$this->default_settings['hide_address2'] = 'no';
		$this->default_settings['hide_state'] = 'no';
		$this->default_settings['hide_zip'] = 'no';
		$this->default_settings['hide_country'] = 'no';
		$this->default_settings['default_country'] = 'United States';
		$this->default_settings['master_for'] = array('mailinglist', 'billing', 'shipping');
	}

	// ********************************************************************************* //

	public function render_field($field=array(), $template=TRUE, $data)
	{
		$options = array();
		$options['name'] = '';
		$options['class'] = 'text ';

		$settings = array_merge($this->default_settings, $field['settings']);

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

		// If in publish field, lets disable it
		if ($template == FALSE) $options['readonly'] = 'readonly';

		$out = '';

		// -----------------------------------------
		// Address
		// -----------------------------------------
		if ($template)
		{
			$options['name'] = $field['form_name'].'[address]';
			$options['id'] = $field['form_elem_id'].'_address';

			// Form data?
			if (isset($data['address'])) $options['value'] = $data['address'];
		}
		$out .= '<div class="dfinput_full address_street">';
		$out .=		form_input($options);
		$out .= 	'<label for="'.$field['form_elem_id'].'_address'.'">' . $this->EE->lang->line('form:street_addr') . '</label>';
		$out .= '</div>';

		// -----------------------------------------
		// Address 2
		// -----------------------------------------
		if (isset($settings['hide_address2']) == TRUE && $settings['hide_address2'] != 'yes')
		{
			if ($template)
			{
				$options['class'] = 'text';
				$options['name'] = $field['form_name'].'[address2]';
				$options['id'] = $field['form_elem_id'].'_address2';

				// Form data?
				if (isset($data['address2'])) $options['value'] = $data['address2'];
			}
			$out .= '<div class="dfinput_full address_street2">';
			$out .=		form_input($options);
			$out .= 	'<label for="'.$field['form_elem_id'].'_address2'.'">' . $this->EE->lang->line('form:address2') . '</label>';
			$out .= '</div>';
		}

		// -----------------------------------------
		// City
		// -----------------------------------------
		if ($template)
		{
			$options['name'] = $field['form_name'].'[city]';
			$options['id'] = $field['form_elem_id'].'_city';

			// Form data?
			if (isset($data['city'])) $options['value'] = $data['city'];
		}
		$out .= '<div class="dfinput_left address_city">';
		$out .=		form_input($options);
		$out .= 	'<label for="'.$field['form_elem_id'].'_city'.'">' . $this->EE->lang->line('form:city') . '</label>';
		$out .= '</div>';

		// -----------------------------------------
		// State
		// -----------------------------------------
		if (isset($settings['hide_state']) == TRUE && $settings['hide_state'] != 'yes')
		{
			if ($template)
			{
				$options['name'] = $field['form_name'].'[state]';
				$options['id'] = $field['form_elem_id'].'_state';

				// Form data?
				if (isset($data['state'])) $options['value'] = $data['state'];
			}
			$out .= '<div class="dfinput_right address_state">';
			$out .=		form_input($options);
			$out .= 	'<label for="'.$field['form_elem_id'].'_state'.'">' . $this->EE->lang->line('form:state_region') . '</label>';
			$out .= '</div>';
		}

		// -----------------------------------------
		// ZIP
		// -----------------------------------------
		if (isset($settings['hide_zip']) == TRUE && $settings['hide_zip'] != 'yes')
		{
			if ($template)
			{
				$options['name'] = $field['form_name'].'[zip]';
				$options['id'] = $field['form_elem_id'].'_zip';

				// Form data?
				if (isset($data['zip'])) $options['value'] = $data['zip'];
			}
			$out .= '<div class="dfinput_left address_zip">';
			$out .=		form_input($options);
			$out .= 	'<label for="'.$field['form_elem_id'].'_zip'.'">' . $this->EE->lang->line('form:zip_postal') . '</label>';
			$out .= '</div>';
		}

		// -----------------------------------------
		// COUNTRY
		// -----------------------------------------
		if (isset($settings['hide_country']) == TRUE && $settings['hide_country'] != 'yes')
		{
			include(PATH_THIRD . 'forms/config/forms_countries.php');

			if ($template) $options['name'] = $field['form_name'].'[country]';

			$out .= '<div class="dfinput_right address_country">';
			$out .=		'<select name="'.$options['name'].'" id="'.$field['form_elem_id'].'_country'.'">';

			// Form data?
			$selected_country = (isset($data['country']) === TRUE) ? $data['country'] : $settings['default_country'];

			foreach($cf_countries_list_eng as $country)
			{
				$selected = ($selected_country == $country) ? 'selected' : '';

				$out .= "<option {$selected}>{$country}</option>";
			}



			$out .=		'</select>';
			$out .= 	'<label for="'.$field['form_elem_id'].'_country'.'">' . $this->EE->lang->line('form:country') . '</label>';
			$out .= '</div>';
		}

		$out .= '<br clear="all">';

		return $out;
	}

	// ********************************************************************************* //

	public function validate($field, $data)
	{
		$settings = array_merge($this->default_settings, $field['settings']);

		if (in_array('mailinglist', $settings['master_for']))
		{
			$this->EE->forms->master_info['address'] = $data;
		}

		if (in_array('billing', $settings['master_for']))
		{
			$this->EE->forms->master_info['billing_address'] = $data;
		}

		if (in_array('shipping', $settings['master_for']))
		{
			$this->EE->forms->master_info['shipping_address'] = $data;
		}

		// DO we need to check for required?
		if ($field['required'] != 1) return TRUE;

		// Prepare the error
		$error = array('type' => 'general', 'msg' => $this->EE->lang->line('form:error:required_field'));

		// Address
		if ($data['address'] == FALSE)
		{
			return $error;
		}

		// Address 2
		if (isset($settings['hide_address2']) == TRUE && $settings['hide_address2'] != 'yes')
		{
			if ($data['address2'] == FALSE)
			{
				// No need since Address 2 may be left empty!
				//return $error;
			}
		}

		// City
		if ($data['city'] == FALSE)
		{
			return $error;
		}

		// State
		if (isset($settings['hide_state']) == TRUE && $settings['hide_state'] != 'yes')
		{
			if ($data['state'] == FALSE)
			{
				return $error;
			}
		}

		// Zip
		if (isset($settings['hide_zip']) == TRUE && $settings['hide_zip'] != 'yes')
		{
			if ($data['zip'] == FALSE)
			{
				return $error;
			}
		}

		// Country
		if (isset($settings['hide_country']) == TRUE && $settings['hide_country'] != 'yes')
		{
			if ($data['country'] == FALSE)
			{
				return $error;
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

	public function output_data($field=array(), $data, $type='html')
	{
		// TODO: LOCALIZE THIS?

		$data = @unserialize($data);
		$out = '';

		// -----------------------------------------
		// Template? or Email
		// -----------------------------------------
		if ($type == 'html' OR $type == 'text')
		{
			if (isset($data['address']) === TRUE) $out .= 'Address: ' . $data['address'] . ' <br />';
			if (isset($data['address2']) === TRUE) $out .= 'Address 2: ' . $data['address2'] . ' <br />';
			if (isset($data['city']) === TRUE) $out .= 'City: ' . $data['city'] . ' <br />';
			if (isset($data['state']) === TRUE) $out .= 'State: ' . $data['state'] . ' <br />';
			if (isset($data['zip']) === TRUE) $out .= 'Zip: ' . $data['zip'] . ' <br />';
			if (isset($data['country']) === TRUE) $out .= 'Country: ' . $data['country'] . ' <br />';

			// Remove the BR's and add linebreaks instead
			if ($type == 'text')
			{
				$out = str_replace('<br />', chr(10), $out);
			}
		}
		else
		{
			if (isset($data['address']) === TRUE) $out .= $data['address'];
		}

		return $out;
	}

	// ********************************************************************************* //

	public function field_settings($settings, $template)
	{
		$vData = array_merge($this->default_settings, $settings);

		include(PATH_THIRD . 'forms/config/forms_countries.php');
		$vData['countries'] = array();
		foreach ($cf_countries_list_eng as $key => $value) {
			$vData['countries'][$value] = $value;
		}

		return $this->EE->load->view('fields/address', $vData, TRUE);
	}

	// ********************************************************************************* //

}

/* End of file field.address.php */
/* Location: ./system/expressionengine/third_party/forms/fields/field.address.php */
