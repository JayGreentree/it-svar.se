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
class FormsField_cart_pay_payflowpro extends FormsField
{

	/**
	 * Field info - Required
	 *
	 * @access public
	 * @var array
	 */
	public $info = array(
		'title'		=>	'Payment: Payflow Pro',
		'name' 		=>	'cart_pay_payflowpro',
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
		if (isset($this->form_settings['payflow_pro']['password']) === FALSE OR $this->form_settings['payflow_pro']['password'] == FALSE)
		{
			unset($this->info);
		}

		$this->default_settings['test_mode'] = 'yes';
		$this->default_settings['cc'] = array('visa', 'mc', 'amex');
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
			$data['number'] = '4111111111111111';
			$data['exp_month'] = date('m', strtotime('+1 month'));
			$data['exp_year'] = date('y', strtotime('+1 years'));
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
					$arr[ substr($i, 2) ] = $i;
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

		if ($field['required'] && (isset($this->EE->forms->cart['products']) === FALSE || empty($this->EE->forms->cart['products']) === TRUE))
		{
			return $this->EE->lang->line('f:error:no_products');
		}

		return TRUE;
	}

	// ********************************************************************************* //

	public function precheck_save($field, $data)
	{
		// Any Products?
		if (isset($this->EE->forms->cart['products']) == FALSE OR empty($this->EE->forms->cart['products']))
		{
			if ($field['required']) return $this->EE->lang->line('f:error:no_products');
			else return TRUE;
		}

		$settings = array_merge($this->default_settings, $field['settings']);
		$amount = $this->get_cart_total();

		// Init
		require_once PATH_THIRD.'/forms/libraries/payflow/payflow.php';

		try {
			$PP = new PayFlowTransaction();

			// Test Mode?
			if ($settings['test_mode'] == 'yes') $PP->environment = 'test';
			else $PP->environment = 'live';

			$PP->PARTNER = $this->form_settings['payflow_pro']['partner'];
			$PP->USER = $this->form_settings['payflow_pro']['username'];
			$PP->PWD = $this->form_settings['payflow_pro']['password'];
			$PP->VENDOR = $this->form_settings['payflow_pro']['vendor'];

			$PP->TENDER = 'C'; //sets to a cc transaction
			$PP->ACCT = $data['number']; //cc number
			$PP->CVV2 = $data['ccv']; //cc number
			$PP->TRXTYPE = 'S'; //txn type: sale
			$PP->AMT = $amount; // Amount
			$PP->EXPDATE = $data['exp_month'].$data['exp_year']; //4 digit expiration date

			// Master Name?
			if (isset($this->EE->forms->master_info['name']) === TRUE)
			{
				$PP->FIRSTNAME = $this->EE->forms->master_info['name']['first_name'];
				$PP->LASTNAME = $this->EE->forms->master_info['name']['last_name'];
			}
			else
			{
				$name = explode(' ', $data['name']);
				$PP->FIRSTNAME = $name[0];
				$PP->LASTNAME = isset($name[1]) ? $name[1] : '';
			}

			// Billing Address
			if (isset($this->EE->forms->master_info['billing_address']) === TRUE)
			{
				$temp = $this->EE->forms->master_info['billing_address'];
				$PP->STREET = isset($temp['address']) ? $temp['address'] : '';
				$PP->ZIP = isset($temp['zip']) ? $temp['zip'] : '';
				$PP->CITY = isset($temp['city']) ? $temp['city'] : '';
				$PP->STATE = isset($temp['state']) ? $temp['state'] : '';
				//$AUNET->country = isset($temp['country']) ? $temp['country'] : '';
			}

			// Billing Name
			if (isset($this->EE->forms->master_info['billing_name']) === TRUE)
			{
				$PP->FIRSTNAME = $this->EE->forms->master_info['billing_name']['first_name'];
				$PP->LASTNAME = $this->EE->forms->master_info['billing_name']['last_name'];
			}

			$PP->COUNTRY = 'US';

			// Shipping Address
			if (isset($this->EE->forms->master_info['shipping_address']) === TRUE)
			{
				$temp = $this->EE->forms->master_info['shipping_address'];
				$PP->SHIPTOSTREET = isset($temp['address']) ? $temp['address'] : '';
				$PP->SHIPTOZIP = isset($temp['zip']) ? $temp['zip'] : '';
				$PP->SHIPTOCITY = isset($temp['city']) ? $temp['city'] : '';
				$PP->SHIPTOSTATE = isset($temp['state']) ? $temp['state'] : '';
				$PP->SHIPTOCOUNTRY = 840;
			}

			// Billing Name
			if (isset($this->EE->forms->master_info['shipping_name']) === TRUE)
			{
				$PP->SHIPTOFIRSTNAME = $this->EE->forms->master_info['shipping_name']['first_name'];
				$PP->SHIPTOLASTNAME = $this->EE->forms->master_info['shipping_name']['last_name'];
			}

			//$PP->debug = true; //uncomment to see debugging information
			//$PP->avs_addr_required = 1; //set to 1 to enable AVS address checking, 2 to force "Y" response
			//$PP->avs_zip_required = 1; //set to 1 to enable AVS zip code checking, 2 to force "Y" response
			$PP->cvv2_required = 1; //set to 1 to enable cvv2 checking, 2 to force "Y" response
			//$PP->fraud_protection = true; //uncomment to enable fraud protection

			$PP->process();

			//$this->EE->firephp->log($PP->raw_response);
			//$this->EE->firephp->log($PP->response_arr);
			//return ' ddd';

			if ($PP->txn_successful)
			{
				$data['amount'] = $amount;
				unset($data['number'], $data['ccv']);
				$this->EE->forms->cart_transaction = array('data' => $data, 'trans' => $PP->response_arr);
				return TRUE;
			}
			else
			{
				return 'Your transaction was not approved!';
			}

		} catch (Exception $e) {
			return $e->getMessage();
		}

		return FALSE;
	}

	// ********************************************************************************* //

	public function save($field, $data)
	{
		if (isset($this->EE->forms->cart_transaction) === FALSE) return serialize(array());

		$data = (array) $this->EE->forms->cart_transaction;
		return serialize($data);
	}

	// ********************************************************************************* //

	public function field_settings($settings=array(), $template=TRUE)
	{
		$vData = array_merge($this->default_settings, $settings);
		return $this->EE->load->view('fields/cart_pay_payflowpro', $vData, TRUE);
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
			if (isset($data['data']['amount']) === TRUE) $out .= 'Total: &#36;' . $data['data']['amount'] . ' <br />';
			if (isset($data['trans']['PNREF']) === TRUE) $out .= 'PNREF: ' . $data['trans']['PNREF'] . ' <br />';
			if (isset($data['trans']['AUTHCODE']) === TRUE) $out .= 'AUTHCODE: ' . $data['trans']['AUTHCODE'] . ' <br />';

			// Remove the BR's and add linebreaks instead
			if ($type == 'text')
			{
				$out = str_replace('<br />', chr(10), $out);
				$out = str_replace('&#36;', '&', $out);
			}
		}
		else
		{
			$out .= 'APPROVED: &#36;' . $data['data']['amount'];
		}

		return $out;
	}

	// ********************************************************************************* //

}

/* End of file field.cart_pay_payflowpro.php */
/* Location: ./system/expressionengine/third_party/forms/fields/field.cart_pay_payflowpro.php */
