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
class FormsField_cart_quantity extends FormsField
{

	/**
	 * Field info - Required
	 *
	 * @access public
	 * @var array
	 */
	public $info = array(
		'title'		=>	'Quantity',
		'name' 		=>	'cart_quantity',
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

		$this->default_settings['field_type'] = 'select';
		$this->default_settings['default_val'] = '1';
		$this->default_settings['min'] = '1';
		$this->default_settings['max'] = '5';
	}

	// ********************************************************************************* //

	public function render_field($field=array(), $template=TRUE, $data='')
	{
		// Some Defaults
		$class = ''; $out = '';
		$settings = array_merge($this->default_settings, $field['settings']);

		$exra_validation = '';

		if ((isset($settings['min']) == TRUE && $settings['min'] > 0))
		{
			$exra_validation .= ',min['.$settings['min'].']';
		}

		if ((isset($settings['max']) == TRUE && $settings['max'] > 0))
		{
			$exra_validation .= ',max['.$settings['max'].']';
		}

		// -----------------------------------------
		// Add JS Validation support
		// -----------------------------------------
		if ($template == TRUE)
		{
			$class .= ' required validate[required,custom[number]'.$exra_validation.'] ';
		}

		// Do we have any previous submits!
		if (empty($data) === TRUE) $data = '';

		// -----------------------------------------
		// INput
		// -----------------------------------------
		if ($settings['field_type'] == 'input')
		{
			if ($data == FALSE) $data = $settings['default_val'];
			$out = form_input($field['form_name'], $data, ' class="'.$class.'" ');
			return $out;
		}

		// -----------------------------------------
		// Drop Down Fieldtype
		// -----------------------------------------
		elseif ($settings['field_type'] == 'select')
		{
			$items = array();
			for ($i=$settings['min']; $i <= $settings['max']; $i++)
			{
				$items[$i] = $i;
				if ($data == FALSE && $settings['default_val'] == $i) $data = $i;
			}

			$out = form_dropdown($field['form_name'], $items, $data, ' class="'.$class.'" ');
		}

		// -----------------------------------------
		// Hidden
		// -----------------------------------------
		elseif ($settings['field_type'] == 'hidden')
		{
			if ($template === TRUE)
			{
				$this->hidden_field = TRUE;
				$this->hidden_field_value = $settings['default_val'];
				if ($data != FALSE) $this->hidden_field_value = $data;
				return;
			}

			$out = form_input($field['form_name'], $data);
		}


		return $out;
	}

	// ********************************************************************************* //

	public function validate($field=array(), $data)
	{
		$settings = array_merge($this->default_settings, $field['settings']);
		if ($data == FALSE || !(bool)preg_match( '/^[0-9]+$/', $data))
		{
			return $this->EE->lang->line('f:invalid_qty');
		}

		if ($data > $settings['max']) return $this->EE->lang->line('f:error:max_qty') . $settings['max'];
		if ($data < $settings['min']) return $this->EE->lang->line('f:error:min_qty') . $settings['min'];

		if (isset($this->EE->forms->cart['products']) === TRUE)
		{
			foreach ($this->EE->forms->cart['products'] as &$product)
			{
				if (isset($product['qty']) === FALSE) $product['qty'] = (int) $data;
			}
		}

		//$this->EE->firephp->log($data);
		//$this->EE->firephp->log($this->EE->forms->cart['products']);
		//exit();

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
		return $this->EE->load->view('fields/cart_quantity', $vData, TRUE);
	}

	// ********************************************************************************* //


}

/* End of file field.cart_quantity.php */
/* Location: ./system/expressionengine/third_party/forms/fields/field.cart_quantity.php */
