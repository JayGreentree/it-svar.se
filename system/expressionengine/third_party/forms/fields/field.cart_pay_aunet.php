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
class FormsField_cart_pay_aunet extends FormsField
{

	/**
	 * Field info - Required
	 *
	 * @access public
	 * @var array
	 */
	public $info = array(
		'title'		=>	'Payment: Authorize.Net',
		'name' 		=>	'cart_pay_aunet',
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

		$this->form_settings = $this->EE->forms_helper->grab_settings($this->EE->config->item('site_id'));

		// Is it installed?
		if (isset($this->form_settings['aunet']['api_login_id']) === FALSE OR $this->form_settings['aunet']['api_login_id'] == FALSE)
		{
			unset($this->info);
		}

		$this->default_settings['test_mode'] = 'yes';
		$this->default_settings['cc'] = array('visa', 'mc', 'amex');
		$this->default_settings['email_customer'] = 'default';
		$this->default_settings['ignore_req_if_zero'] = 'no';
	}

	// ********************************************************************************* //

	public function render_field($field=array(), $template=TRUE, $data='')
	{
		// Some Defaults
		$settings = array_merge($this->default_settings, $field['settings']);
		$out = '';
		$req_class = '';
		$options = array();
		$options['name'] = '';
		$options['class'] = 'text ';
		$ccs = array('visa' => 'VISA', 'mc' => 'MasterCard', 'amex' => 'American Express', 'discover' => 'Discover', 'jcb' => 'JCB');

		// Test Mode?
		if ($settings['test_mode'] == 'yes')
		{
			$data = array();
			$data['name'] = 'TEST TRANSACTION';
			$data['number'] = '4007000000027';
			$data['exp_month'] = date('m', strtotime('+1 month'));
			$data['exp_year'] = date('Y', strtotime('+1 years'));
			$data['ccv'] = '999';
		}

		// -----------------------------------------
		// Add JS Validation support
		// -----------------------------------------
		if ($template == TRUE)
		{
			$req_class .= ' required validate[required] ';
		}

		// Do we have any previous submits!
		if (empty($data) === TRUE) $data = '';

		// CC Name
		$options['value'] = isset($data['name']) ? $data['name'] : '';
		$options['name'] = $field['form_name'].'[name]';
		$options['class'] = $req_class .' text';
		$out .= '<div class="dfinput_full cc_name">';
		$out .=		form_input($options);
		$out .= 	'<label>' . $this->EE->lang->line('f:cc_name') . '</label>';
		$out .= '</div>';

		// CC Number
		$options['value'] = isset($data['number']) ? $data['number'] : '';
		$options['name'] = $field['form_name'].'[number]';
		$options['class'] = $req_class .' text';
		$out .= '<div class="dfinput_full cc_number">';

		$out .= '<div class="cc_logos">';
		foreach ($ccs as $key => $value)
		{
			$display = (in_array($key, $settings['cc'])) ? '' : 'display:none;';
			$out .= '<div class="cc_icons cc-'.$key.'" style="'.$display.'">'.$value.'</div>';
		}
		$out .= '</div>';

		$out .=		form_input($options);
		$out .= 	'<label>' . $this->EE->lang->line('f:cc_number') . '</label>';
		$out .= '</div>';

		// CC Details
		$out .= '<div class="dfinput_full cc_cardinfo">';

			$out .= '<div class="info_block">';
				$options['value'] = isset($data['exp_month']) ? $data['exp_month'] : '';
				$options['name'] = $field['form_name'].'[exp_month]';
				$options['class'] = $req_class . ' exp_month';
				$out .= form_dropdown($options['name'], array(''=>lang('f:month'),'01'=>'01','02'=>'02','03'=>'03','04'=>'04','05'=>'05','06'=>'06','07'=>'07','08'=>'08','09'=>'09','10'=>'10','11'=>'11','12'=>'12'), $options['value'], ' class="'.$options['class'].'" ');

				$options['value'] = isset($data['exp_year']) ? $data['exp_year'] : '';
				$options['name'] = $field['form_name'].'[exp_year]';
				$options['class'] = $req_class .' exp_year';
				$arr = array('' => lang('f:year'));
				for ($i=date('Y'); $i < date('Y', strtotime('+20 years')); $i++)
				{
					$arr[$i] = $i;
				}
				$out .= form_dropdown($options['name'], $arr, $options['value'], ' class="'.$options['class'].'" ');

				$out .= 	'<label>' . $this->EE->lang->line('f:exp_date') . '</label>';
			$out .= '</div>';

			$out .= '<div class="info_block">';
				$options['value'] = isset($data['ccv']) ? $data['ccv'] : '';
				$options['name'] = $field['form_name'].'[ccv]';
				$options['class'] = $req_class .' text';
				$out .= '<div class="cc_ccv">';
				$out .=		form_input($options);
				$out .=		'<span class="ccv_img"></span>';
				$out .= 	'<label>' . $this->EE->lang->line('f:ccv') . '</label>';
				$out .= '</div>';
			$out .= '</div>';



		$out .= '</div>';

		return $out;
	}

	// ********************************************************************************* //

	public function validate($field, $data)
	{
		$settings = array_merge($this->default_settings, $field['settings']);

		$total = $this->get_cart_total();

		if ($settings['ignore_req_if_zero'] == 'yes' && $total == '0.00' && $field['required'] == true)
		{
			$field['required'] = false;
		}

		//var_dump($total);exit();

		if ($field['required'] && (isset($this->EE->forms->cart['products']) === FALSE || empty($this->EE->forms->cart['products']) === TRUE))
		{
			return $this->EE->lang->line('f:error:no_products');
		}

		return TRUE;
	}

	// ********************************************************************************* //

	public function precheck_save($field, $data)
	{
		$settings = array_merge($this->default_settings, $field['settings']);

		$total = $this->get_cart_total();

		if ($settings['ignore_req_if_zero'] == 'yes' && $total == '0.00' && $field['required'] == true)
		{
			$field['required'] = false;
		}

		// Any Products?
		if (isset($this->EE->forms->cart['products']) == FALSE OR empty($this->EE->forms->cart['products']))
		{
			if ($field['required']) return $this->EE->lang->line('f:error:no_products');
			else return TRUE;
		}

		if ($total == 0)
		{
			if ($field['required']) return $this->EE->lang->line('f:error:no_products');
			else return TRUE;
		}

		// Init
		require_once PATH_THIRD.'/forms/libraries/AuthorizeNet/AuthorizeNet.php';
		$AUNET = new AuthorizeNetAIM($this->form_settings['aunet']['api_login_id'], $this->form_settings['aunet']['transaction_key']);

		// Test Mode?
		/*if ($settings['test_mode'] == 'yes') $AUNET->setSandbox(TRUE);
		else $AUNET->setSandbox(FALSE);*/

		$AUNET->setSandbox(FALSE);
		if ($settings['test_mode'] == 'yes') $AUNET->test_request = 'TRUE';

		$this->prepare_trans($AUNET, $field, $data);
		$response = $AUNET->authorizeAndCapture();

		//$this->EE->firephp->log($AUNET);
		//return 'test';

		if ($response->approved)
		{
			$this->EE->forms->cart_transaction = $response;
			return TRUE;
		}
		else
		{
			return $response->response_reason_text;
		}
	}

	// ********************************************************************************* //

	public function save($field, $data)
	{
		if (isset($this->EE->forms->cart_transaction) === FALSE) return serialize(array());
		$data = (array) $this->EE->forms->cart_transaction;
		return serialize($data);
	}

	// ********************************************************************************* //

	public function field_settings($settings, $template=TRUE)
	{
		$vData = array_merge($this->default_settings, $settings);
		return $this->EE->load->view('fields/cart_pay_aunet', $vData, TRUE);
	}

	// ********************************************************************************* //

	public function output_data($field, $data, $type='html')
	{
		$data = @unserialize($data);
		$out = '';

		if (!$data) return 'NO_ORDER';

		// -----------------------------------------
		// Template? or Email
		// -----------------------------------------
		if ($type == 'html' OR $type == 'text')
		{
			$out .= 'APPROVED<br />';
			if (isset($data['amount']) === TRUE) $out .= 'Total: &#36;' . $data['amount'] . ' <br />';
			if (isset($data['transaction_id']) === TRUE) $out .= 'ID: ' . $data['transaction_id'] . ' <br />';

			// Remove the BR's and add linebreaks instead
			if ($type == 'text')
			{
				$out = str_replace('<br />', chr(10), $out);
				$out = str_replace('&#36;', '&', $out);
			}
		}
		else
		{
			$out .= 'APPROVED: &#36;' . $data['amount'];
		}

		return $out;
	}

	// ********************************************************************************* //

	private function prepare_trans(&$AUNET, $field, $data)
	{
		$AUNET->amount = $this->get_cart_total();
		$AUNET->card_num = $data['number'];
		$AUNET->exp_date = $data['exp_month'].'/'.$data['exp_year'];
		$AUNET->card_code = $data['ccv'];
		$AUNET->customer_ip = (isset($_SERVER['HTTP_X_FORWARDED_FOR']) == TRUE) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $this->EE->input->ip_address();

		// Master Name?
		if (isset($this->EE->forms->master_info['name']) === TRUE)
		{
			$AUNET->first_name = $this->EE->forms->master_info['name']['first_name'];
			$AUNET->last_name = $this->EE->forms->master_info['name']['last_name'];
		}
		else
		{
			$name = explode(' ', $data['name']);
			$AUNET->first_name = $name[0];
			$AUNET->last_name = isset($name[1]) ? $name[1] : '';
		}

		// Email?
		if (isset($this->EE->forms->master_info['email']) === TRUE)
		{
			$AUNET->email = $this->EE->forms->master_info['email'];
		}
		else
		{
			if (isset($this->EE->session->userdata['email'])) $AUNET->email = $this->EE->session->userdata['email'];
		}

		// Freight?
		if (isset($this->EE->forms->cart['shipping']['price'])) $AUNET->freight = $this->EE->forms->cart['shipping']['price'];

		// Description
		$arr = array();
		foreach ($this->EE->forms->cart['products'] as $count => $product)
		{
			$qty = isset($product['qty']) ? $product['qty'] : 1;
			$name = substr($product['product'], 0, 30);

			// WTF? http://community.developer.authorize.net/t5/Integration-and-Testing/x-line-item-integration-in-PHP/td-p/9654
			if ($count < 1) $AUNET->line_item = "{$count}<|>{$name}<|><|>{$qty}<|>{$product['product_total']}<|>N";
			else $AUNET->addLineItem($count, $name,'', $qty, $product['product_total'], 'N');

			$arr[] = $product['product'];
		}

		$AUNET->description = substr( implode(', ', $arr) , 0, 250);
		$AUNET->invoice_num = $field['form_id'] .'_'.$this->EE->localize->now;

		// Email Cust
		if (isset($field['settings']['email_customer']) === TRUE && $field['settings']['email_customer'] == 'no')
		{
			$AUNET->email_customer = 'N';
		}
		elseif (isset($field['settings']['email_customer']) === TRUE && $field['settings']['email_customer'] == 'yes')
		{
			$AUNET->email_customer = 'Y';
		}

		// Billing Address
		if (isset($this->EE->forms->master_info['billing_address']) === TRUE)
		{
			$temp = $this->EE->forms->master_info['billing_address'];
			$AUNET->address = isset($temp['address']) ? $temp['address'] : '';
			$AUNET->zip = isset($temp['zip']) ? $temp['zip'] : '';
			$AUNET->city = isset($temp['city']) ? $temp['city'] : '';
			$AUNET->state = isset($temp['state']) ? $temp['state'] : '';
			$AUNET->country = isset($temp['country']) ? $temp['country'] : '';
		}

		// Billing Name
		if (isset($this->EE->forms->master_info['billing_name']) === TRUE)
		{
			$AUNET->first_name = $this->EE->forms->master_info['billing_name']['first_name'];
			$AUNET->last_name = $this->EE->forms->master_info['billing_name']['last_name'];
		}

		// Shipping Address
		if (isset($this->EE->forms->master_info['shipping_address']) === TRUE)
		{
			$temp2 = $this->EE->forms->master_info['shipping_address'];
			$AUNET->ship_to_address = isset($temp2['address']) ? $temp2['address'] : '';
			$AUNET->ship_to_zip = isset($temp2['zip']) ? $temp2['zip'] : '';
			$AUNET->ship_to_city = isset($temp2['city']) ? $temp2['city'] : '';
			$AUNET->ship_to_state = isset($temp2['state']) ? $temp2['state'] : '';
			$AUNET->ship_to_country = isset($temp2['country']) ? $temp2['country'] : '';
		}

		// Billing Name
		if (isset($this->EE->forms->master_info['shipping_name']) === TRUE)
		{
			$AUNET->ship_to_first_name = $this->EE->forms->master_info['shipping_name']['first_name'];
			$AUNET->ship_to_last_name = $this->EE->forms->master_info['shipping_name']['last_name'];
		}
	}

	// ********************************************************************************* //

}

/* End of file field.cart_pay_aunet.php */
/* Location: ./system/expressionengine/third_party/forms/fields/field.cart_pay_aunet.php */
