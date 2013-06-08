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
class FormsField_cart_product extends FormsField
{

	/**
	 * Field info - Required
	 *
	 * @access public
	 * @var array
	 */
	public $info = array(
		'title'		=>	'Product',
		'name' 		=>	'cart_product',
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

		$this->default_settings['choices'][]  = array('label' => 'First Product', 'value' => 'First Product', 'price' => '1.00');
		$this->default_settings['choices'][]  = array('label' => 'Second Product', 'value' => 'Second Product' , 'price' => '2.00');
		$this->default_settings['choices'][]  = array('label' => 'Third Product', 'value' => 'Third Product', 'price' => '3.00');
		$this->default_settings['values_enabled'] = 'no';
		$this->default_settings['default_choice'] = -1;
		$this->default_settings['field_type'] = 'single';
		$this->default_settings['display_tmpl'] = '{qty} x {label} - ${price}';
		$this->default_settings['product_label'] = 'My Product';
		$this->default_settings['product_price'] = '1.00';
		$this->default_settings['default_qty'] = '1';
		$this->default_settings['show_qty'] = 'yes';
		$this->default_settings['product_price_field'] = '';
		$this->default_settings['product_label_field'] = '';
	}

	// ********************************************************************************* //

	public function render_field($field=array(), $template=TRUE, $data='')
	{
		// Some Defaults
		$class = ''; $out = '';
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
		if (empty($data) === TRUE) $data = array('product' => '');

		// -----------------------------------------
		// Single
		// -----------------------------------------
		if ($settings['field_type'] == 'single')
		{
			// Qty Field
			$qty = '';
			if ($settings['show_qty'] == 'yes')
			{
				$options = array();
				$options['class'] = 'text cart_quantity cart_qty_small';
				$options['name'] = $field['form_name'].'[qty]';
				$options['value'] = (isset($data['qty']) ) ? $data['qty'] : $settings['default_qty'];
				$qty = form_input($options);
			}

			// Format the label
			$out = str_replace(array('{label}', '{price}', '{qty}'), array($settings['product_label'], $settings['product_price'], $qty), $settings['display_tmpl']);
			$out .= '<input type="hidden" name="'.$field['form_name'].'[product]" value="single" class="single_product" data-price="'.$settings['product_price'].'">';
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
				$hash = dechex(crc32($value));

				// Check checked!
				$checked = FALSE;
				if ($settings['default_choice'] === $number) $checked = TRUE;

				// Now Check for returned val!
				if ($check_submit == TRUE)
				{
					if ($data['product'] == $value) $checked = TRUE;
					else $checked = FALSE;
				}

				// Qty Field
				$qty = '';
				if ($settings['show_qty'] == 'yes')
				{
					$options = array();
					$options['class'] = 'text cart_quantity cart_qty_small';
					$options['name'] = $field['form_name'].'[qty]['.$hash.']';
					$options['value'] = (isset($data['qty'][$hash]) ) ? $data['qty'][$hash] : $settings['default_qty'];
					$qty = form_input($options);
				}

				// Format the label
				$choice['label'] = str_replace(array('{label}', '{price}', '{qty}'), array($choice['label'], $choice['price'], $qty), $settings['display_tmpl']);

				$out .= '<li class="single_product" data-price="'.$choice['price'].'">' . form_radio($field['form_name'].'[product]', $value, $checked, ' class="'.$class.'" ') . '&nbsp; '.$choice['label'].'</li>';
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
					if (isset($data[$hash]['product']) === TRUE) $checked = TRUE;
					else $checked = FALSE;
				}

				// Qty Field
				$qty = '';
				if ($settings['show_qty'] == 'yes')
				{
					$options = array();
					$options['class'] = 'text cart_quantity cart_qty_small';
					$options['name'] = $field['form_name'].'['.$hash.'][qty]';
					$options['value'] = (isset($data[$hash]['qty']) ) ? $data[$hash]['qty'] : $settings['default_qty'];
					$qty = form_input($options);
				}

				// Format the label
				$choice['label'] = str_replace(array('{label}', '{price}', '{qty}'), array($choice['label'], $choice['price'], $qty), $settings['display_tmpl']);

				$out .= '<li class="single_product" data-price="'.$choice['price'].'">' . form_checkbox($field['form_name'].'['.$hash.'][product]', $value, $checked, ' class="'.$class.'" ') . '&nbsp; '.$choice['label'].'</li>';
			}
			$out .= '</ul>';
		}

		// -----------------------------------------
		// Drop Down Fieldtype
		// -----------------------------------------
		elseif ($settings['field_type'] == 'select')
		{
			$out = '<select name="'.$field['form_name'].'[product]">';
			foreach ($settings['choices'] as $number => $choice)
			{
				// What Value are we going to use?
				$value = ($use_values ? $choice['value'] : $choice['label']);

				// Format the label
				$choice['label'] = str_replace(array('{label}', '{price}'), array($choice['label'], $choice['price']), $settings['display_tmpl']);

				if ($data['product'] == FALSE && $settings['default_choice'] === $number) $data['product'] = $value;

				// Check checked!
				$checked = '';
				if ($settings['default_choice'] === $number) $checked = 'selected';

				// Now Check for returned val!
				if ($data['product'] == $value) $checked = 'selected';

				$out .= '<option value="'.$value.'" data-price="'.$choice['price'].'" '.$checked.'>'.$choice['label'].'</option>';
			}

			$out .= '</select>';
		}

		// -----------------------------------------
		// User Defined Price
		// -----------------------------------------
		elseif ($settings['field_type'] == 'user_price')
		{
			if (isset($data['price']) == FALSE) $data = array('price' => $settings['product_price']);
			$out = form_input($field['form_name'].'[price]', $data['price'], 'class="user_defined"');
		}

		// -----------------------------------------
		// Hidden
		// -----------------------------------------
		elseif ($settings['field_type'] == 'hidden')
		{
			if ($template === TRUE)
			{
				$this->hidden_field = TRUE;
				$this->hidden_field_value = $settings['product_label'];
				$this->hidden_field_value = 'HIDDEN';
				return;
			}

			$out = form_input($field['form_name'], 'HIDDEN', 'disabled');
		}

		// -----------------------------------------
		// Entry Product
		// -----------------------------------------
		if ($settings['field_type'] == 'entry_product')
		{
			// Qty Field
			$qty = '';
			if ($settings['show_qty'] == 'yes')
			{
				$options = array();
				$options['class'] = 'text cart_quantity cart_qty_small';
				$options['name'] = $field['form_name'].'[qty]';
				$options['value'] = (isset($data['qty']) ) ? $data['qty'] : $settings['default_qty'];
				$qty = form_input($options);
			}

			if (!$template)
			{
				return str_replace(array('{label}', '{price}', '{qty}'), array($settings['product_label'], $settings['product_price'], $qty), $settings['display_tmpl']);
			}

			// Get the entry_id
			$entry_id = trim($this->EE->input->get_post('entry_id'));
			if (!$entry_id)
			{
				$segs = $this->EE->uri->segment_array();
				$entry_id = trim(end($segs));
			}

			// Not a number? maybe url title then!
			if (!ctype_digit((string)$entry_id)) {
				$query = $this->EE->db->select('entry_id')->from('exp_channel_titles')->where('url_title', $entry_id)->get();
				$entry_id = $query->row('entry_id');
			}

			if (isset($data['entry_id']) === TRUE) $entry_id = $data['entry_id'];

			// Get Entry
			$query = $this->EE->db->select('title')->from('exp_channel_titles')->where('entry_id', $entry_id)->get();
			if ($query->num_rows() == 0)
			{
				return '<strong style="color:red">No Entry Found</strong>';
			}

			// Product Label
			if ($settings['product_label_field'] == 'entry_title')
			{
				$settings['product_label'] = $query->row('title');
			}
			else
			{
				$query = $this->EE->db->select('field_id_'.$settings['product_label_field'])->from('exp_channel_data')->where('entry_id', $entry_id)->get();
				$settings['product_label'] = $query->row('field_id_'.$settings['product_label_field']);
			}

			// Product Price
			$query = $this->EE->db->select('field_id_'.$settings['product_price_field'])->from('exp_channel_data')->where('entry_id', $entry_id)->get();
			$settings['product_price'] = $query->row('field_id_'.$settings['product_price_field']);
			if ($settings['product_price']) $settings['product_price'] = number_format($settings['product_price'], 2);

			// Format the label
			$out = str_replace(array('{label}', '{price}', '{qty}'), array($settings['product_label'], $settings['product_price'], $qty), $settings['display_tmpl']);
			$out .= '<input type="hidden" name="'.$field['form_name'].'[product]" value="entry_product" class="single_product" data-price="'.$settings['product_price'].'">';
			$out .= '<input type="hidden" name="'.$field['form_name'].'[entry_id]" value="'.$entry_id.'" >';
			return $out;
		}


		return $out;
	}

	// ********************************************************************************* //

	public function validate($field, $data)
	{
		$settings = array_merge($this->default_settings, $field['settings']);
		$use_values = ($settings['values_enabled'] == 'yes') ? TRUE : FALSE;
		$products = array();

		// Radio Field?
		if ( ($settings['field_type'] == 'radio' || $settings['field_type'] == 'select') && isset($data['product']) === TRUE)
		{
			foreach ($settings['choices'] as $number => $choice)
			{
				// What Value are we going to use?
				$value = ($use_values ? $choice['value'] : $choice['label']);
				if ($data['product'] == $value)
				{
					$product = array('product' => $choice['label'], 'price' => $choice['price']);
					if (isset($data['qty'][ dechex(crc32($data['product'])) ]) === TRUE) $product['qty'] = $data['qty'][ dechex(crc32($data['product'])) ];
					$products[] = $product;
				}
			}
		}

		// Checkbox Field?
		if ($settings['field_type'] == 'checkbox')
		{
			foreach ($settings['choices'] as $number => $choice)
			{
				// What Value are we going to use?
				$value = ($use_values ? $choice['value'] : $choice['label']);
				$hash = dechex(crc32($value));

				if (isset($data[$hash]['product']) === TRUE)
				{
					$product = array('product' => $choice['label'], 'price' => $choice['price']);
					if (isset($data[$hash]['qty']) === TRUE) $product['qty'] = $data[$hash]['qty'];
					$products[] = $product;
				}
			}
		}

		// Single
		if ($settings['field_type'] == 'single' && $data['product'] == 'single')
		{
			$product = array('product' => $settings['product_label'], 'price' => $settings['product_price']);
			if (isset($data['qty']) === TRUE) $product['qty'] = $data['qty'];
			$products[] = $product;
		}

		// User Price
		if ($settings['field_type'] == 'user_price' && isset($data['price']))
		{
			$product = array('product' => $settings['product_label'], 'price' => $data['price']);
			$products[] = $product;
		}

		// Hidden
		if ($settings['field_type'] == 'hidden')
		{
			$products[] = array('product' => $settings['product_label'], 'price' => $settings['product_price']);
		}

		// Entry Product
		if ($settings['field_type'] == 'entry_product' && $data['product'] == 'entry_product')
		{
			$entry_id = $data['entry_id'];


			// Get Entry
			$query = $this->EE->db->select('title')->from('exp_channel_titles')->where('entry_id', $entry_id)->get();
			if ($query->num_rows() == 0)
			{
				return TRUE;
			}

			// Product Label
			if ($settings['product_label_field'] == 'entry_title')
			{
				$settings['product_label'] = $query->row('title');
			}
			else
			{
				$query = $this->EE->db->select('field_id_'.$settings['product_label_field'])->from('exp_channel_data')->where('entry_id', $entry_id)->get();
				$settings['product_label'] = $query->row('field_id_'.$settings['product_label_field']);
			}

			// Product Price
			$query = $this->EE->db->select('field_id_'.$settings['product_price_field'])->from('exp_channel_data')->where('entry_id', $entry_id)->get();
			$settings['product_price'] = $query->row('field_id_'.$settings['product_price_field']);
			if ($settings['product_price']) $settings['product_price'] = number_format($settings['product_price'], 2);


			$product = array('product' => $settings['product_label'], 'price' => $settings['product_price']);
			if (isset($data['qty']) === TRUE) $product['qty'] = $data['qty'];
			$products[] = $product;
		}

		//$this->EE->firephp->log($data);
		//$this->EE->firephp->log($products);
		//exit();
		//return FALSE;

		foreach ($products as $pr)
		{
			if (isset($pr['qty']) === TRUE && ($pr['qty'] == FALSE || !(bool)preg_match( '/^[0-9]+$/', $pr['qty']))) {
				if ($field['required']) return $this->EE->lang->line('f:invalid_qty');
				continue;
			}

			$this->EE->forms->cart['products'][] = $pr;
		}

		return TRUE;
	}

	// ********************************************************************************* //

	public function save($field, $data)
	{
		$temp = (isset($this->EE->forms->cart['products'])) ? $this->EE->forms->cart['products'] : array();
		unset($this->EE->forms->cart['products']);

		$this->validate($field, $data);
		$arr = (isset($this->EE->forms->cart['products'])) ? $this->EE->forms->cart['products'] : array();

		$this->EE->forms->cart['products'] = $temp;

		return serialize($arr);
	}

	// ********************************************************************************* //

	public function field_settings($settings=array(), $template=TRUE)
	{
		$vData = array_merge($this->default_settings, $settings);
		$vData['default_choice'] = (int) $vData['default_choice'];

		// get all fields
		$vData['dbfields'] = array();
		$query = $this->EE->db->select('fg.group_name, cf.field_id, cf.field_label')->from('exp_channel_fields cf')->join('exp_field_groups fg', 'fg.group_id = cf.group_id', 'left')->order_by('fg.group_name', 'ASC')->order_by('cf.field_label', 'ASC')->get();

		foreach ($query->result() as $row)
		{
			$vData['dbfields'][ $row->group_name ][$row->field_id] = $row->field_label;
		}

		return $this->EE->load->view('fields/cart_product', $vData, TRUE);
	}

	// ********************************************************************************* //

	public function output_data($field, $data, $type='html')
	{
		$data = @unserialize($data);
		$out = '';

		if (is_array($data) === FALSE) return '';

		foreach ($data as $product)
		{
			$qty = isset($product['qty']) ? $product['qty'] : 1;
			$total = number_format( ($qty * $product['price']), 2);
			$out .= "{$qty} x {$product['product']} - &#36;{$product['price']} (&#36;{$total}) <br />";
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
				$out = str_replace('&#36;', '$', $out);
			}
		}
		else
		{
			if ($type == 'text')
			{
				$out = str_replace('<br />', chr(10), $out);
				$out = str_replace('&#36;', '$', $out);
			}
		}

		return $out;
	}

	// ********************************************************************************* //


}

/* End of file field.cart_product.php */
/* Location: ./system/expressionengine/third_party/forms/fields/field.cart_product.php */
