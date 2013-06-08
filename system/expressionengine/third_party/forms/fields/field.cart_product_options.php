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
class FormsField_cart_product_options extends FormsField
{

	/**
	 * Field info - Required
	 *
	 * @access public
	 * @var array
	 */
	public $info = array(
		'title'		=>	'Product Options',
		'name' 		=>	'cart_product_options',
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

		$this->default_settings['choices'][]  = array('label' => 'Red', 'value' => 'Red', 'price' => '1.00');
		$this->default_settings['choices'][]  = array('label' => 'Green', 'value' => 'Green' , 'price' => '2.00');
		$this->default_settings['choices'][]  = array('label' => 'Yellow', 'value' => 'Yellow', 'price' => '3.00');
		$this->default_settings['field_type'] = 'radio';
		$this->default_settings['display_tmpl'] = '{label} - ${price}';
		$this->default_settings['default_choice'] = -1;
		$this->default_settings['values_enabled'] = 'no';
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
		if (empty($data) === TRUE) $data = array('option' => '');

		// -----------------------------------------
		// Radio Field Type
		// -----------------------------------------
		if ($settings['field_type'] == 'radio')
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
					if ($data['option'] == $value) $checked = TRUE;
					else $checked = FALSE;
				}

				// Format the label
				$choice['label'] = str_replace(array('{label}', '{price}'), array($choice['label'], $choice['price']), $settings['display_tmpl']);

				$out .= '<li data-price="'.$choice['price'].'">' . form_radio($field['form_name'].'[option]', $value, $checked, ' class="'.$class.'" ') . '&nbsp; '.$choice['label'].'</li>';
			}
			$out .= '</ul>';
		}

		// -----------------------------------------
		// Checkbox Field Type
		// -----------------------------------------
		elseif ($settings['field_type'] == 'checkbox')
		{
			$out = '<ul class="checkboxes">';
			foreach ($settings['choices'] as $number => $choice)
			{
				// What Value are we going to use?
				$value = ($use_values ? $choice['value'] : $choice['label']);
				$hash = dechex(crc32($value));

				// Check checked!
				$checked = FALSE;
				if ($settings['default_choice'] === $number) $checked = TRUE;

				// Now Check for returned val!
				if ($check_submit == TRUE)
				{
					if (isset($data[$hash]['option']) === TRUE) $checked = TRUE;
					else $checked = FALSE;
				}

				// Format the label
				$choice['label'] = str_replace(array('{label}', '{price}'), array($choice['label'], $choice['price']), $settings['display_tmpl']);

				$out .= '<li data-price="'.$choice['price'].'">' . form_checkbox($field['form_name'].'['.$hash.'][option]', $value, $checked, ' class="'.$class.'" ') . '&nbsp; '.$choice['label'].'</li>';
			}
			$out .= '</ul>';
		}

		// -----------------------------------------
		// Drop Down Fieldtype
		// -----------------------------------------
		elseif ($settings['field_type'] == 'select')
		{
			$out = '<select name="'.$field['form_name'].'[option]">';
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

	public function validate($field=array(), $data)
	{
		$settings = array_merge($this->default_settings, $field['settings']);
		$use_values = ($settings['values_enabled'] == 'yes') ? TRUE : FALSE;
		$this->options = array();

		// Radio Field?
		if ( ($settings['field_type'] == 'radio' || $settings['field_type'] == 'select') && isset($data['option']) === TRUE)
		{
			foreach ($settings['choices'] as $number => $choice)
			{
				// What Value are we going to use?
				$value = ($use_values ? $choice['value'] : $choice['label']);
				if ($data['option'] == $value)
				{
					$option = array('option' => $choice['label'], 'price' => $choice['price']);
					$this->options[] = $option;
				}
			}
		}

		// Checkbox Field?
		if ($settings['field_type'] == 'checkbox' && is_array($data) === TRUE)
		{
			foreach ($settings['choices'] as $number => $choice)
			{
				// What Value are we going to use?
				$value = ($use_values ? $choice['value'] : $choice['label']);
				$hash = dechex(crc32($value));

				if (isset($data[$hash]['option']) === TRUE)
				{
					$option = array('option' => $choice['label'], 'price' => $choice['price']);
					$this->options[] = $option;
				}
			}
		}

		//$this->EE->firephp->log($data);
		//$this->EE->firephp->log($options);
		//
		//return FALSE;

		if (isset($this->EE->forms->cart['products']) === FALSE) return TRUE;

		foreach ($this->EE->forms->cart['products'] as &$product)
		{
			if (isset($product['option']) === FALSE)
			{
				$product['option'] = $this->options;

				$arr = array();
				foreach ($this->options as $pr) $arr[] = $pr['option'];
				$product['product'] .= ' - ' . implode(',', $arr);
			}
		}

		return TRUE;
	}

	// ********************************************************************************* //

	public function save($field=array(), $data)
	{
		$this->validate($field, $data);
		return serialize($this->options);
	}

	// ********************************************************************************* //

	public function field_settings($settings=array(), $template=TRUE)
	{
		$vData = array_merge($this->default_settings, $settings);
		$vData['default_choice'] = (int) $vData['default_choice'];

		return $this->EE->load->view('fields/cart_product_options', $vData, TRUE);
	}

	// ********************************************************************************* //

	public function output_data($field, $data, $type='html')
	{
		$data = @unserialize($data);
		$out = '';

		if (is_array($data) === FALSE) return '';

		foreach ($data as $option)
		{
			$out .= "{$option['option']} - &#36;{$option['price']}<br />";
		}

		// -----------------------------------------
		// Template? or Email
		// -----------------------------------------
		if ($type == 'html' OR $type == 'text')
		{
			// Remove the BR's and add linebreaks instead
			if ($type == 'text')
			{
				$out = str_replace('<br />', chr(10), $out);
			}
		}
		else
		{
			if ($type == 'text')
			{
				$out = str_replace('<br />', chr(10), $out);
			}
		}

		return $out;
	}

	// ********************************************************************************* //

}

/* End of file field.cart_product_options.php */
/* Location: ./system/expressionengine/third_party/forms/fields/field.cart_product_options.php */
