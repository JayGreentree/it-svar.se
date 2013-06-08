<?php if (!defined('BASEPATH')) die('No direct script access allowed');

/**
 * Channel Forms CAPTCHA field
 *
 * @package			DevDemon_Forms
 * @author			DevDemon <http://www.devdemon.com> - Lead Developer @ Parscale Media
 * @copyright 		Copyright (c) 2007-2011 Parscale Media <http://www.parscale.com>
 * @license 		http://www.devdemon.com/license/
 * @link			http://www.devdemon.com/forms/
 * @see				http://expressionengine.com/user_guide/development/fieldtypes.html
 */
class FormsField_captcha extends FormsField
{

	/**
	 * Field info - Required
	 *
	 * @access public
	 * @var array
	 */
	public $info = array(
		'title'		=>	'CAPTCHA',
		'name' 		=>	'captcha',
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

	}

	// ********************************************************************************* //

	public function render_field($field=array(), $template=TRUE, $data)
	{
		$out = '';

		// -----------------------------------------
		// Default Settings
		// -----------------------------------------
		if (isset($field['settings']['type']) == FALSE) $field['settings']['type'] = 'simple';
		if (isset($field['settings']['captcha_for_members']) == FALSE) $field['settings']['captcha_for_members'] = 'no';

		// -----------------------------------------
		// Ignore Logged in Members?
		// -----------------------------------------
		if ($field['settings']['captcha_for_members'] == 'no' && $this->EE->session->userdata['member_id'] > 0 && $template == TRUE)
		{
			$this->show_field = FALSE;
			return;
		}

		// -----------------------------------------
		// SSL?
		// -----------------------------------------
		$is_ssl = FALSE;
		if ((isset($_SERVER['HTTPS']) === TRUE && empty($_SERVER['HTTPS']) === FALSE) OR (isset($_SERVER['HTTP_HTTPS']) === TRUE && empty($_SERVER['HTTP_HTTPS']) === FALSE))
		{
			$is_ssl = TRUE;
		}

		// -----------------------------------------
		// Render!
		// -----------------------------------------
		switch ($field['settings']['type'])
		{
			case 'simple':
				$out = $this->render_simple($field, $template, $is_ssl);
				break;
			case 'recaptcha':
				$out = $this->render_recaptcha($field, $template, $is_ssl);
				break;
			default:
				$out = $this->render_simple($field, $template, $is_ssl);
				break;
		}

		return $out;
	}

	// ********************************************************************************* //

	public function validate($field=array(), $data)
	{
		// Grab Module Settings
		$settings = $this->EE->forms_helper->grab_settings($this->site_id);

		// -----------------------------------------
		// Default Settings
		// -----------------------------------------
		if (isset($field['settings']['type']) == FALSE) $field['settings']['type'] = 'simple';
		if (isset($field['settings']['captcha_for_members']) == FALSE) $field['settings']['captcha_for_members'] = 'no';

		// -----------------------------------------
		// Ignore Logged in Members?
		// -----------------------------------------
		if ($field['settings']['captcha_for_members'] == 'no' && $this->EE->session->userdata['member_id'] > 0)
		{
			return TRUE;
		}

		// -----------------------------------------
		// Simple Captcha
		// -----------------------------------------
		if ($field['settings']['type'] == 'simple')
		{
			if ($data == FALSE)
			{
				return $this->EE->lang->line('captcha_required');
			}
			else
			{
				$this->EE->db->where('word', $data);
				$this->EE->db->where('ip_address', $this->EE->input->ip_address());
				$this->EE->db->where('date > UNIX_TIMESTAMP()-7200', NULL, FALSE);

				$result = $this->EE->db->count_all_results('captcha');

				if ($result == 0)
				{
					return $this->EE->lang->line('captcha_incorrect');
				}

				// Delete old ones
				$this->EE->db->query("DELETE FROM exp_captcha WHERE (word='".$this->EE->db->escape_str($data)."' AND ip_address = '".$this->EE->input->ip_address()."') OR date < UNIX_TIMESTAMP()-7200");
			}
		}

		// -----------------------------------------
		// Recaptcha
		// -----------------------------------------
		if ($field['settings']['type'] == 'recaptcha')
		{
			// Include our class
			if (class_exists('ReCaptchaResponse') == FALSE)
			{
				require_once PATH_THIRD . 'forms/libraries/recaptcha/recaptchalib.php';
			}

			// -----------------------------------------
			// Check the answer
			// -----------------------------------------
			$resp = recaptcha_check_answer($settings['recaptcha']['private'], $this->EE->input->ip_address(), $this->EE->input->post('recaptcha_challenge_field'), $this->EE->input->post('recaptcha_response_field'));

			if (!$resp->is_valid)
			{
				if ($resp->error == 'incorrect-captcha-sol')
				{
					return $this->EE->lang->line('form:recaptcha_error');
				}
				else
				{
					return $resp->error;
				}
			}
		}

		return TRUE;
	}

	// ********************************************************************************* //

	public function field_settings($settings=array(), $pbf=FALSE)
	{
		$vData = $settings;

		return $this->EE->load->view('fields/captcha', $vData, TRUE);
	}

	// ********************************************************************************* //

	private function render_recaptcha($field=array(), $template=TRUE, $is_ssl=FALSE)
	{
		// Grab Module Settings
		$settings = $this->EE->forms_helper->grab_settings($this->site_id);

		// By Default it's the red one!
		if (isset($field['settings']['recaptcha']['theme']) == FALSE) $field['settings']['recaptcha']['theme'] = 'red';
		if (isset($field['settings']['recaptcha']['lang']) == FALSE) $field['settings']['recaptcha']['lang'] = 'en';

		// -----------------------------------------
		// MCP Render
		// -----------------------------------------
		if ($template !== TRUE)
		{
			switch ($field['settings']['recaptcha']['theme'])
			{
				case 'red':
					return '<img src="'.FORMS_THEME_URL.'img/captcha_recaptcha_red.png">';
					break;
				case 'white':
					return '<img src="'.FORMS_THEME_URL.'img/captcha_recaptcha_white.png">';
					break;
				case 'blackglass':
					return '<img src="'.FORMS_THEME_URL.'img/captcha_recaptcha_blackglass.png">';
					break;
				case 'clean':
					return '<img src="'.FORMS_THEME_URL.'img/captcha_recaptcha_clean.png">';
					break;
				default:
					return '<img src="'.FORMS_THEME_URL.'img/captcha_recaptcha_red.png">';
					break;
			}
		}


		// -----------------------------------------
		// Template Render!
		// -----------------------------------------
		$out = $this->EE->forms_helper->output_js_buffer("<script type='text/javascript'>
		var RecaptchaOptions = {
		theme: '{$field['settings']['recaptcha']['theme']}',
		lang: '{$field['settings']['recaptcha']['lang']}'
		};
		</script>");

		if ($is_ssl == TRUE)
		{
			$out .= $this->EE->forms_helper->output_js_buffer('<script type="text/javascript" src="https://www.google.com/recaptcha/api/challenge?k='.$settings['recaptcha']['public'].'&hl='.$field['settings']['recaptcha']['lang'].'"></script>');
		}
		else
		{
			$out .= $this->EE->forms_helper->output_js_buffer('<script type="text/javascript" src="http://www.google.com/recaptcha/api/challenge?k='.$settings['recaptcha']['public'].'&hl='.$field['settings']['recaptcha']['lang'].'"></script>');
		}

		return $out;

	}

	// ********************************************************************************* //

	private function render_simple($field=array(), $template=TRUE, $is_ssl=FALSE)
	{
		// -----------------------------------------
		// MCP Render
		// -----------------------------------------
		if ($template !== TRUE)
		{
			return '<img src="'.FORMS_THEME_URL.'img/captcha_ee.jpg">';
		}

		// -----------------------------------------
		// Template Render!
		// -----------------------------------------

		// Override this!
		$this->EE->config->config['captcha_require_members'] = 'y';

		// Create Captcha
		$img = $this->EE->functions->create_captcha();

		// Add SSL is needed
		if ($is_ssl == TRUE)
		{
			$img = str_replace('http://', 'https://', $img);
		}

		$options = array();
		$options['name'] = $field['form_name'];
		$options['style'] = 'width:150px';

		$out = '';
		$out .= '<div class="dfinput_full">';
		$out .=		$img.'<br />';
		$out .= 	'<label>' . $this->EE->lang->line('form:recaptch_simple_instr') . '</label>';
		$out .=		form_input($options);
		$out .= '</div>';

		return $out;
	}

	// ********************************************************************************* //
}

/* End of file field.captcha.php */
/* Location: ./system/expressionengine/third_party/forms/fields/field.captcha.php */
