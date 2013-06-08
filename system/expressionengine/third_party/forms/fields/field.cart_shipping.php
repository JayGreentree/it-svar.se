<?php if (!defined('BASEPATH')) die('No direct script access allowed');

/**
 * Channel Forms PRODUCT field
 *
 * @package			DevDemon_Forms
 * @author			DevDemon <http://www.devdemon.com> - Lead Developer @ Parscale Media
 * @copyright 		Copyright (c) 2007-2011 Parscale Media <http://www.parscale.com>
 * @license 		http://www.devdemon.com/license/
 * @link			http://www.devdemon.com/forms/
 * @see				http://expressionengine.com/user_guide/development/fieldtypes.html
 */
class FormsField_cart_shipping extends FormsField
{

	/**
	 * Field info - Required
	 *
	 * @access public
	 * @var array
	 */
	public $info = array(
		'title'		=>	'Shipping',
		'name' 		=>	'cart_shipping',
		'category'	=>	'cart_tools',
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

		$this->default_settings['choices'][]  = array('label' => 'First Shipping Method', 'value' => 'First Shipping Method', 'price' => '1.00');
		$this->default_settings['choices'][]  = array('label' => 'Second Shipping Method', 'value' => 'Second Shipping Method' , 'price' => '2.00');
		$this->default_settings['choices'][]  = array('label' => 'Third Shipping Method', 'value' => 'Third Shipping Method', 'price' => '3.00');
		$this->default_settings['values_enabled'] = 'no';
		$this->default_settings['default_choice'] = -1;
		$this->default_settings['field_type'] = 'single';
		$this->default_settings['display_tmpl'] = '{label} - ${price}';
		$this->default_settings['shipping_label'] = 'My Shipping Method';
		$this->default_settings['shipping_price'] = '1.00';
	}

	// ********************************************************************************* //

	public function render_field($field=array(), $template=TRUE, $data='')
	{
		// Some Defaults
		$class = '';
		$settings = array_merge($this->default_settings, $field['settings']);
		$settings['default_choice'] = (int) $settings['default_choice'];
		$use_values = ($settings['values_enabled'] == 'yes') ? TRUE : FALSE;

		// -----------------------------------------
		// Add JS Validation support
		// -----------------------------------------
		if ($template == TRUE)
		{

		}

		// Do we have any previous submits!
		$check_submit = FALSE;
		if (empty($data) === FALSE) $check_submit = TRUE;
		if (empty($data) === TRUE) $data = '';

		// -----------------------------------------
		// Hidden
		// -----------------------------------------
		if ($settings['field_type'] == 'single')
		{
			$out = str_replace(array('{label}', '{price}'), array($settings['shipping_label'], $settings['shipping_price']), $settings['display_tmpl']);
			$out .= '<input type="hidden" name="'.$field['form_name'].'" value="single" class="single_ship" data-price="'.$settings['shipping_price'].'">';
			return $out;
		}

		// -----------------------------------------
		// Radio Field Type
		// -----------------------------------------
		elseif ($settings['field_type'] == 'radio')
		{
			$out = '<ul class="radios">';
			foreach ($settings['choices'] as $number => $choice)
			{
				// What Value are we going to use?
				$value = ($use_values ? $choice['value'] : $choice['label']);

				// Check checked!
				$checked = FALSE;
				if ($settings['default_choice'] === $number) $checked = TRUE;

				// Now Check for returned val!
				if ($check_submit == TRUE)
				{
					if ($data == $value) $checked = TRUE;
					else $checked = FALSE;
				}

				// Format the label
				$choice['label'] = str_replace(array('{label}', '{price}'), array($choice['label'], $choice['price']), $settings['display_tmpl']);

				$out .= '<li class="single_ship" data-price="'.$choice['price'].'">' . form_radio($field['form_name'], $value, $checked, ' class="'.$class.'" ') . '&nbsp; '.$choice['label'].'</li>';
			}
			$out .= '</ul>';
		}

		// -----------------------------------------
		// Drop Down Fieldtype
		// -----------------------------------------
		elseif ($settings['field_type'] == 'select')
		{

			$out = '<select name="'.$field['form_name'].'">';
			foreach ($settings['choices'] as $number => $choice)
			{
				// What Value are we going to use?
				$value = ($use_values ? $choice['value'] : $choice['label']);

				// Format the label
				$choice['label'] = str_replace(array('{label}', '{price}'), array($choice['label'], $choice['price']), $settings['display_tmpl']);

				if ($data == FALSE && $settings['default_choice'] === $number) $data = $value;

				// Check checked!
				$checked = '';
				if ($settings['default_choice'] === $number) $checked = 'selected';

				// Now Check for returned val!
				if ($data == $value) $checked = 'selected';

				$out .= '<option value="'.$value.'" data-price="'.$choice['price'].'" '.$checked.'>'.$choice['label'].'</option>';
			}

			$out .= '</select>';
		}

		return $out;
	}

	// ********************************************************************************* //

	public function validate($field, $data)
	{
		$settings = array_merge($this->default_settings, $field['settings']);
		$use_values = ($settings['values_enabled'] == 'yes') ? TRUE : FALSE;

		// Radio Field?
		if ( ($settings['field_type'] == 'radio' || $settings['field_type'] == 'select'))
		{
			foreach ($settings['choices'] as $number => $choice)
			{
				// What Value are we going to use?
				$value = ($use_values ? $choice['value'] : $choice['label']);
				if ($data == $value)
				{
					$this->EE->forms->cart['shipping'] = array('label' => $choice['label'], 'price' => $choice['price']);
				}
			}
		}

		// Single
		if ($settings['field_type'] == 'single' && $data == 'single')
		{
			$this->EE->forms->cart['shipping'] = array('label' => $settings['shipping_label'], 'price' => $settings['shipping_price']);
		}

		return TRUE;
	}

	// ********************************************************************************* //

	public function save($field, $data)
	{
		$settings = array_merge($this->default_settings, $field['settings']);

		if (isset($this->EE->forms->cart['shipping']) == TRUE)
		{
			$out = str_replace(array('{label}', '{price}'), array($this->EE->forms->cart['shipping']['label'], $this->EE->forms->cart['shipping']['price']), $settings['display_tmpl']);
			return $out;
		}

		return '';
	}

	// ********************************************************************************* //

	public function field_settings($settings=array(), $template=TRUE)
	{
		$vData = array_merge($this->default_settings, $settings);
		$vData['default_choice'] = (int) $vData['default_choice'];

		return $this->EE->load->view('fields/cart_shipping', $vData, TRUE);
	}

	// ********************************************************************************* //


}

/* End of file field.cart_shipping.php */
/* Location: ./system/expressionengine/third_party/forms/fields/field.cart_shipping.php */
