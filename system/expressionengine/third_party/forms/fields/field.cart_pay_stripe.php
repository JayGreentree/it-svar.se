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
class FormsField_cart_pay_stripe extends FormsField
{

	/**
	 * Field info - Required
	 *
	 * @access public
	 * @var array
	 */
	public $info = array(
		'title'		=>	'Payment: Stripe',
		'name' 		=>	'cart_pay_stripe',
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
		if (isset($this->form_settings['stripe']['live']['secret']) === FALSE OR $this->form_settings['stripe']['live']['secret'] == FALSE)
		{
			unset($this->info);
		}

		$this->default_settings['test_mode'] = 'yes';
		$this->default_settings['cc'] = array('visa', 'mc', 'amex');
		$this->default_settings['currency'] = 'usd';
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

		if ($settings['test_mode'] == 'yes') $public_key = $this->form_settings['stripe']['test']['public'];
		else $public_key = $this->form_settings['stripe']['live']['public'];

		// Test Mode?
		if ($settings['test_mode'] == 'yes')
		{
			$data = array();
			$data['name'] = 'TEST TRANSACTION';
			$data['number'] = '4242424242424242';
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

			$final_js = '';
			$final_js .= '<script type="text/javascript" src="https://js.stripe.com/v1/"></script>';
			$final_js .= <<<EOT

			<script type="text/javascript">
			function StripeSubmitHandler(event){
				var StripeForm = $('#stripe_form').closest('form');
				var Parent = $('#stripe_form').closest('.dform_element');
				StripeForm.find('.dform_error').remove();

				// disable the submit button to prevent repeated clicks
				StripeForm.find('.submit_button .submit').attr('disabled', 'disabled');

			    Stripe.createToken({
			        number: StripeForm.find('.cc_number').find('input').val(),
			        cvc: StripeForm.find('.cc_ccv').find('input').val(),
			        exp_month: StripeForm.find('.exp_month').val(),
			        exp_year: StripeForm.find('.exp_year').val(),
			        name: StripeForm.find('.cc_name input').val()
			    }, stripeResponseHandler);

		    	// prevent the form from submitting with the default action
		    	return false;
			};

			function stripeResponseHandler(status, response) {
				var StripeForm = $('#stripe_form').closest('form');
			    if (response.error) {
					// show the errors on the form
					$('#stripe_form').closest('.dform_element').prepend('<div class="dform_error">'+response.error.message+'</div>');
					StripeForm.find('.submit_button .submit').removeAttr('disabled');
			    } else {
					// token contains id, last4, and card type
					var token = response['id'];
					// insert the token into the form so it gets submitted to the server
					StripeForm.append('<input type="hidden" name="stripe_token" value="' + token + '"/>');
					// and submit

					StripeForm.get(0).submit();
			    }
			};

			$(document).ready(function() {
				var StripeForm = $('#stripe_form').closest('form');
				if (! StripeForm.data('validator')) $('#stripe_form').closest('form').submit(StripeSubmitHandler);
				else {
					StripeForm.data().validator.settings.submitHandler = function(form) {
						StripeForm = $(form);
						StripeSubmitHandler();
					};
				}
			});

			Stripe.setPublishableKey("{$public_key}");

			</script>

EOT;

			$out .= $this->EE->forms_helper->output_js_buffer($final_js);
		}

		// Do we have any previous submits!
		if (empty($data) === TRUE) $data = '';

		// START STRIPE
		$out .= '<div id="stripe_form">';

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
		$options['name'] = ''; //$field['form_name'].'[number]';
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


		$out .= '</div> <!-- STRIPE FORM -->';

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

		if (isset($_POST['stripe_token']) === FALSE OR $_POST['stripe_token'] == FALSE)
		{
			return $this->EE->lang->line('f:error:no_stripe_token');
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

		$cart_total = $this->get_cart_total();

		if ($cart_total == 0)
		{
			if ($field['required']) return $this->EE->lang->line('f:error:no_products');
			else return TRUE;
		}

		$settings = array_merge($this->default_settings, $field['settings']);

		if ($settings['test_mode'] == 'yes') $secret_key = $this->form_settings['stripe']['test']['secret'];
		else $secret_key = $this->form_settings['stripe']['live']['secret'];

		// Init
		require_once PATH_THIRD.'/forms/libraries/stripe/Stripe.php';
		Stripe::setApiKey($secret_key);

		// Description
		$arr = array();
		foreach ($this->EE->forms->cart['products'] as $count => $product)
		{
			$arr[] = $product['product'];
		}
		$description = substr( implode(', ', $arr) , 0, 250);

		// Amount
		$amount = $cart_total;
		$amount = number_format(($amount*100), 0, '', '');

		try {
			$transaction = Stripe_Charge::create(array( "amount" => $amount, "currency" => $settings['currency'], "card" => $this->EE->input->post('stripe_token'), "description" => $description) );
		} catch (Exception $e) {
			return $e->getMessage();
		}

		//$this->EE->firephp->log($transaction);
		$this->EE->forms->cart_transaction = $transaction;
		return TRUE;
	}

	// ********************************************************************************* //

	public function save($field=array(), $data)
	{
		if (isset($this->EE->forms->cart_transaction) === FALSE) return serialize(array());

		$data = array();
		$data['id'] = $this->EE->forms->cart_transaction->id;
		$data['amount'] = $this->EE->forms->cart_transaction->amount;
		$data['fee'] = $this->EE->forms->cart_transaction->fee;
		$data['created'] = $this->EE->forms->cart_transaction->created;
		$data['card'] = array();
		$data['card']['last4'] = $this->EE->forms->cart_transaction->card->last4;

		return serialize($data);
	}

	// ********************************************************************************* //

	public function field_settings($settings=array(), $template=TRUE)
	{
		$vData = array_merge($this->default_settings, $settings);
		return $this->EE->load->view('fields/cart_pay_stripe', $vData, TRUE);
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
			if (isset($data['amount']) === TRUE) $out .= 'Total: &#36;' . number_format(($data['amount']/100), 2) . ' <br />';
			if (isset($data['id']) === TRUE) $out .= 'ID: ' . $data['id'] . ' <br />';

			// Remove the BR's and add linebreaks instead
			if ($type == 'text')
			{
				$out = str_replace('<br />', chr(10), $out);
				$out = str_replace('&#36;', '&', $out);
			}
		}
		else
		{
			$out .= 'APPROVED: &#36;' . number_format(($data['amount']/100), 2);
		}

		return $out;
	}

	// ********************************************************************************* //

}

/* End of file field.cart_pay_stripe.php */
/* Location: ./system/expressionengine/third_party/forms/fields/field.cart_pay_stripe.php */
