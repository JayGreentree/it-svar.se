<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Forms ACT File
 *
 * @package			DevDemon_Forms
 * @author			DevDemon <http://www.devdemon.com> - Lead Developer @ Parscale Media
 * @copyright 		Copyright (c) 2007-2010 Parscale Media <http://www.parscale.com>
 * @license 		http://www.devdemon.com/license/
 * @link			http://www.devdemon.com/forms/
 */
class Forms_ACT
{

	/**
	 * Constructor
	 *
	 * @access public
	 *
	 * Calls the parent constructor
	 */
	public function __construct()
	{
		$this->EE =& get_instance();
		$this->site_id = $this->EE->config->item('site_id');
		$this->EE->load->library('forms_helper');
		$this->EE->load->model('forms_model');
		$this->EE->lang->loadfile('forms');
		$this->EE->config->load('forms_config');

		$this->EE->forms_helper->is_ajax();
	}

	// ********************************************************************************* //

	public function form_submission()
	{
		error_reporting(E_ALL);
		@ini_set('display_errors', 1);

		//----------------------------------------
		// Check POST
		//----------------------------------------
		foreach ($_POST as $key => $val)
		{
			$_POST[$key] = $this->EE->security->xss_clean($val);
		}

		//----------------------------------------
		// Standard Fields
		//----------------------------------------
		$this->EE->forms->data = array();
		$this->EE->forms->cart = array();
		$this->EE->forms->master_info = array();
		$this->EE->forms->fields = array();
		$this->EE->forms->dbfields = array();
		$this->EE->forms->ip_address = $this->EE->forms_helper->getUserIp();
		$this->EE->forms->ip_int = sprintf("%u", ip2long($this->EE->forms->ip_address));
		$this->EE->forms->finaldata = array();
		$this->EE->forms->ignored_fields = array('pagebreak', 'html', 'fieldset', 'columns_2', 'columns_3', 'columns_4');
		$this->EE->forms->module_settings = $this->EE->forms_helper->grab_settings($this->site_id);

		//----------------------------------------
		// Check Fields
		//----------------------------------------
		if (isset($_POST['fields']) == FALSE OR empty($_POST['fields']) == TRUE)
		{
			return $this->return_error('missing_data', $this->EE->lang->line('form:error:missing_data') . '(MISSING_FIELDS)' );
		}

		//----------------------------------------
		// Form Data
		//----------------------------------------
		$FDATA = @unserialize($this->EE->forms_helper->decode_string($this->EE->input->post('FDATA')));

		if ($FDATA != FALSE)
		{
			$this->EE->forms->data = $FDATA;

			// Same IP?
			if ($this->EE->forms->data['ip_address'] != $this->EE->forms->ip_address && $this->EE->forms->data['ignore_ip'] == 'no')
			{
				return $this->return_error('missing_data', $this->EE->lang->line('form:error:missing_data') . '(DIFFERENT_IP)' );
			}
		}
		else
		{
			return $this->return_error('missing_data', $this->EE->lang->line('form:error:missing_data') . '(FORM_DATA_NOT_SUBMITTED)' );
		}

		// Params
		$this->EE->forms->params = (isset($this->EE->forms->data['params']) === TRUE) ? $this->EE->forms->data['params'] : array();

		//----------------------------------------
		// Is the user banned?
		//----------------------------------------
		if ($this->EE->session->userdata['is_banned'] == TRUE)
		{
			return $this->return_error('not_authorized', $this->EE->lang->line('form:error:not_authorized') . ' (BANNED)');
		}

		//----------------------------------------
		// Is the IP address and User Agent required?
		//----------------------------------------
		if ($this->EE->config->item('require_ip_for_posting') == 'y')
		{
			if ($this->EE->forms->ip_address == '0.0.0.0' OR $this->EE->session->userdata['user_agent'] == '')
			{
				return $this->return_error('not_authorized', $this->EE->lang->line('form:error:not_authorized') . ' (NO_IP)');
			}
		}

		//----------------------------------------
		// Is the nation of the user banend?
		//----------------------------------------
		if ( $this->EE->session->nation_ban_check(FALSE) === FALSE && $this->EE->config->item('ip2nation') == 'y')
		{
			return $this->return_error('not_authorized', $this->EE->lang->line('form:error:not_authorized') . ' (NATION)');
		}

		//----------------------------------------
		// Blacklist/Whitelist Check
		//----------------------------------------
		if ($this->EE->blacklist->blacklisted == 'y' && $this->EE->blacklist->whitelisted == 'n')
		{
			return $this->return_error('not_authorized', $this->EE->lang->line('form:error:not_authorized') . ' (BLACKLIST)');
		}

		//----------------------------------------
		// Get Form Fields
		//----------------------------------------
		$this->EE->db->select('*');
		$this->EE->db->from('exp_forms_fields');
		$this->EE->db->where('form_id', $this->EE->forms->data['form_id']);
		$this->EE->db->order_by('field_order', 'ASC');

		// Limit to spefic fields?
		if (isset($this->EE->forms->data['fields_shown']) && empty($this->EE->forms->data['fields_shown']) == FALSE)
		{
			$this->EE->db->where_in('field_id', $this->EE->forms->data['fields_shown']);
		}

		$query = $this->EE->db->get();
		$pagebreaks = 0;

		//$this->EE->firephp->log($query->result_array());

		foreach ($query->result_array() as $field)
		{
			if (in_array($field['field_type'], array('fieldset', 'columns_2', 'columns_3', 'columns_4', 'html')) === TRUE) {
				continue;
			}

			// Lets count the pagebreaks
			if ($field['field_type'] == 'pagebreak')
			{
				$pagebreaks++;
				continue;
			}

			// If we have pagebreaks, we only want fields from the current pages
			if ($pagebreaks > 0 && ($pagebreaks+1) > $this->EE->forms->data['current_page']) continue;

			$field['settings'] = @unserialize($field['field_settings']);
			$this->EE->forms->dbfields[] = $field;
		}

		//$this->EE->firephp->log($this->EE->forms->dbfields);
		//exit(print_r($this->EE->forms->dbfields));

		//----------------------------------------
		// Run field validations!
		//----------------------------------------
		$this->EE->forms->errors = array();
		foreach ($this->EE->forms->dbfields as $key => $field)
		{
			// Is our Data there?
			$data = (isset($_POST['fields'][$field['field_id']]) == TRUE) ? $_POST['fields'][$field['field_id']] : '';

			//----------------------------------------
			// Is it really empty?
			//----------------------------------------
			$is_empty = FALSE;
			if (is_string($data) == TRUE)
			{
				$data = trim($data);
				$data = $this->process_field_string($data);

				if (strlen($data) == 0) $is_empty = TRUE;
			}
			else
			{
				$is_empty = empty($data);
			}

			//----------------------------------------
			// Parse Conditionals
			//----------------------------------------
			if (isset($field['conditionals']) == TRUE && $field['conditionals'] != FALSE)
			{
				$field['conditionals'] = @unserialize($field['conditionals']);
				if (is_array($field['conditionals']) == FALSE) $field['conditionals'] = array();
			}
			else $field['conditionals'] = array();

			$shown = $this->EE->formsfields[$field['field_type']]->process_field_conditionals($field);

			//$this->EE->firephp->log("Shown: {$field['field_id']}-{$field['title']}: " . (($shown) ? 'Yes' : 'No') );
			//$this->EE->firephp->log("Empty: {$field['title']}: " . (($is_empty) ? 'Yes' : 'No') );
			//$this->EE->firephp->log($field);

			//----------------------------------------
			// Was it shown? Then lets parse conditionals!
			//----------------------------------------
			if ($shown == TRUE)
			{
				// Is this field required?
				if ($field['required'] == 1 && $is_empty == TRUE)
				{
					$this->EE->forms->errors[] = array('type' => 'required', 'msg' => $this->EE->forms->module_settings['messages']['required_field'], 'field_id' => $field['field_id']);
					continue;
				}

				// Validate!
				$result = $this->EE->formsfields[$field['field_type']]->validate($field, $data);

				// Validation Error?
				if ($result !== TRUE && is_array($result) == TRUE)
				{
					// Pass our error along!
					$result['field_id'] = $field['field_id'];
					$this->EE->forms->errors[] = $result;
				}
				elseif ($result !== TRUE && is_array($result) == FALSE)
				{
					// General Validation Errror then
					$this->EE->forms->errors[] = array('type' => 'general', 'msg' => $result, 'field_id' => $field['field_id']);
				}
			}
			else
			{
				unset($this->EE->forms->dbfields[$key]);
			}
		}

		//$this->EE->firephp->log($this->EE->forms->errors);
		//$this->EE->firephp->log($this->EE->forms->dbfields);

		//----------------------------------------
		// Snaptcha
		//----------------------------------------
		if ( isset($this->EE->forms->data['form_settings']['snaptcha']) == TRUE && $this->EE->forms->data['form_settings']['snaptcha'] == 'yes')
		{
			// Does the file exist?
			if (isset($this->EE->extensions->version_numbers['Snaptcha_ext']) == TRUE)
			{
				require_once(PATH_THIRD.'snaptcha/ext.snaptcha.php');
				$SNAP = new Snaptcha_ext();

				// We need to find the settings
				foreach ($this->EE->extensions->extensions['insert_comment_start'] as $priority => $exts)
				{
					// Loop over all extension
					foreach ($exts as $name => $ext)
					{
						if ($name == 'Snaptcha_ext')
						{
							// Store the Snaptcha field settings
							$SNAP->settings = unserialize($ext[1]);
						}
					}
				}

				// Validate! (using the freeform name. How akward is that)
				$fake_error = array();
				$fake_error = $SNAP->freeform_validate($fake_error);

				if (empty($fake_error) == FALSE)
				{
					$this->EE->forms->errors[] = array('type' => 'captcha', 'msg' => $fake_error[0], 'field_id' => 0);
				}
			}
		}

		$this->handle_errors();

		//----------------------------------------
		// Run PRE SAVE CHECK (duplicate code, blah)
		//----------------------------------------
		$this->EE->forms->errors = array();
		foreach ($this->EE->forms->dbfields as $key => $field)
		{
			// Is our Data there?
			$data = (isset($_POST['fields'][$field['field_id']]) == TRUE) ? $_POST['fields'][$field['field_id']] : '';

			//----------------------------------------
			// Is it really empty?
			//----------------------------------------
			$is_empty = FALSE;
			if (is_string($data) == TRUE)
			{
				$data = trim($data);
				if (strlen($data) == 0) $is_empty = TRUE;
			}
			else
			{
				$is_empty = empty($data);
			}

			//----------------------------------------
			// Parse Conditionals
			//----------------------------------------
			if (isset($field['conditionals']) == TRUE && $field['conditionals'] != FALSE)
			{
				$field['conditionals'] = @unserialize($field['conditionals']);
				if (is_array($field['conditionals']) == FALSE) $field['conditionals'] = array();
			}
			else $field['conditionals'] = array();

			$shown = $this->EE->formsfields[$field['field_type']]->process_field_conditionals($field);

			//----------------------------------------
			// Was it shown? Then lets parse conditionals!
			//----------------------------------------
			if ($shown == TRUE)
			{
				// Validate!
				$result = $this->EE->formsfields[$field['field_type']]->precheck_save($field, $data);

				// Validation Error?
				if ($result !== TRUE && is_array($result) == TRUE)
				{
					// Pass our error along!
					$result['field_id'] = $field['field_id'];
					$this->EE->forms->errors[] = $result;
				}
				elseif ($result !== TRUE && is_array($result) == FALSE)
				{
					// General Validation Errror then
					$this->EE->forms->errors[] = array('type' => 'general', 'msg' => $result, 'field_id' => $field['field_id']);
				}
			}
			else
			{
				unset($this->EE->forms->dbfields[$key]);
			}
		}

		$this->handle_errors();

		//----------------------------------------
		// Are we paging?
		//----------------------------------------
		if ($this->EE->forms->data['paging'] == TRUE && $this->EE->forms->data['current_page'] < ($this->EE->forms->data['total_pages']))
		{
			$_POST['page'] = $this->EE->forms->data['current_page'] +1;

			$this->return_inline_template();
			exit();
		}

		// -------------------------------------------
		// 'forms_submit_data_format_start' hook.
		//  - Executes before we run pre-save routines
		//  Use our forms object:  $this->EE->forms
		//
			$edata = $this->EE->extensions->call('forms_submit_data_format_start');
			if ($this->EE->extensions->end_script === TRUE) return;
		//
		// -------------------------------------------

		//----------------------------------------
		// Run Presave Routines
		//----------------------------------------
		foreach ($this->EE->forms->dbfields as $field)
		{
			// Is our Data there?
			$data = (isset($_POST['fields'][ $field['field_id'] ]) == TRUE) ? $_POST['fields'][ $field['field_id'] ] : '';
			if (is_string($data) == TRUE) trim($data);

			// Run each fields save() method
			$data = $this->EE->formsfields[$field['field_type']]->save($field, $data);

			$this->EE->forms->finaldata[ $field['field_id'] ] = $data;
			unset($_POST['fields'][ $field['field_id'] ]);
		}

		// -------------------------------------------
		// 'forms_submit_data_format_end' hook.
		//  - Executes after we run pre-save routines
		//  Use our forms object:  $this->EE->forms
		//
			$edata = $this->EE->extensions->call('forms_submit_data_format_end');
			if ($this->EE->extensions->end_script === TRUE) return;
		//
		// -------------------------------------------

		//----------------------------------------
		// Third Party
		//----------------------------------------
		$this->process_third_party_submission();

		// Why you would want to do this is weird!
		if (isset($this->EE->forms->data['form_settings']['save_fentry']) === TRUE)
		{
			if ($this->EE->forms->data['form_settings']['save_fentry'] == 'no') $this->process_return_user();
		}

		//----------------------------------------
		// Figure Out the users Country!
		//----------------------------------------
		$country = '';
		if ($this->EE->config->item('ip2nation') == 'y')
		{
			if ( version_compare(APP_VER, '2.5.2', '>=') )
			{
				$addr = $this->EE->forms->ip_address;

				// all IPv4 go to IPv6 mapped
				if (strpos($addr, ':') === FALSE && strpos($addr, '.') !== FALSE)
				{
					$addr = '::'.$addr;
				}
				$addr = inet_pton($addr);

				$query = $this->EE->db
				->select('country')
				->where("ip_range_low <= '".$addr."'", '', FALSE)
				->where("ip_range_high >= '".$addr."'", '', FALSE)
				->order_by('ip_range_low', 'desc')
				->limit(1, 0)
				->get('exp_ip2nation');

				if ($query->num_rows() > 0) $country = $query->row('country');
			}
			else
			{
				$query = $this->EE->db->query("SELECT country FROM exp_ip2nation WHERE ip < INET_ATON('".$this->EE->db->escape_str($this->EE->forms->ip_address)."') ORDER BY ip DESC LIMIT 0,1");
				$country = $query->row('country');
			}

		}
		else
		{
			if (function_exists('dns_get_record') == TRUE)
			{
				$reverse_ip = implode('.',array_reverse(explode('.',$this->EE->forms->ip_address)));
				$DNS_resolver = '.lookup.ip2.cc';
				$lookup = @dns_get_record($reverse_ip.$DNS_resolver, DNS_TXT);
				$country = isset($lookup[0]['txt']) ? strtolower($lookup[0]['txt']) : FALSE;

				if ($country == FALSE)
				{
					$content = $this->EE->forms_helper->fetch_url_file('http://www.geoplugin.net/php.gp?ip='.$this->EE->forms->ip_address);
					$geoip = @unserialize($content);
					$country = strtolower($geoip['geoplugin_countryCode']);
				}
			}
			else
			{
				$content = $this->EE->forms_helper->fetch_url_file('http://www.geoplugin.net/php.gp?ip='.$this->EE->forms->ip_address);
				$geoip = @unserialize($content);
				$country = strtolower($geoip['geoplugin_countryCode']);
			}
		}

		if ($country == FALSE) $country = 'xx';

		// -------------------------------------------
		// 'forms_submit_save_start' hook.
		//  - Executes before we send emails & save to DB
		//  Use our forms object:  $this->EE->forms
		//
			$edata = $this->EE->extensions->call('forms_submit_save_start');
			if ($this->EE->extensions->end_script === TRUE) return;
		//
		// -------------------------------------------

		// Check if it already exists

		for ($i=0; $i < 50; $i++) {
			$fentry_hash = $this->EE->forms_helper->uuid(false);
			$query = $this->EE->db->query("SELECT fentry_id FROM exp_forms_entries WHERE fentry_hash = '{$fentry_hash}'");
			if ($query->num_rows() == 0) break;
		}


		//----------------------------------------
		// Save the submission
		//----------------------------------------
		$this->EE->db->set('fentry_hash', $fentry_hash);
		$this->EE->db->set('form_id', $this->EE->forms->data['form_id']);
		$this->EE->db->set('site_id', $this->site_id);
		$this->EE->db->set('member_id', $this->EE->session->userdata('member_id'));
		$this->EE->db->set('ip_address', $this->EE->forms->ip_int);
		$this->EE->db->set('date', $this->EE->localize->now);
		$this->EE->db->set('country', $country);

		//----------------------------------------
		// Save all field data
		//----------------------------------------
		foreach ($this->EE->forms->finaldata as $field_id => $data)
		{
			$this->EE->db->set('fid_'.$field_id, $data);
		}

		$this->EE->db->insert('exp_forms_entries');
		$fentry_id = $this->EE->db->insert_id();

		// Storre the Fentry ID for email parsing.
		$this->fentry_id = $fentry_id;
		$this->fentry_hash = $fentry_hash;


		//----------------------------------------
		// Update Form Data
		//----------------------------------------
		$this->EE->db->set('total_submissions', '(total_submissions+1)', FALSE);
		$this->EE->db->set('date_last_entry', $this->EE->localize->now);
		$this->EE->db->where('form_id', $this->EE->forms->data['form_id']);
		$this->EE->db->update('exp_forms');

		//exit();

		//----------------------------------------
		// Send Emails!
		//----------------------------------------
		$this->process_emails();

		// -------------------------------------------
		// 'forms_submit_save_end' hook.
		//  - Executes after we saved the submission & before we return the user
		//  Use our forms object:  $this->EE->forms
		//
			$edata = $this->EE->extensions->call('forms_submit_save_end', $fentry_id);
			if ($this->EE->extensions->end_script === TRUE) return;
		//
		// -------------------------------------------

		$this->process_return_user($fentry_id);
	}

	// ********************************************************************************* //

	private function return_inline_template()
    {
        //----------------------------------------
        // Are we using Pages or Structure?
        //----------------------------------------
        $template = (string) $this->EE->config->item('template');
        $template_group = (string) $this->EE->config->item('template_group');

        // Look for a page in the pages module
        if ($template_group == '' && $template == '')
        {
            $pages      = $this->EE->config->item('site_pages');
            $site_id    = $this->EE->config->item('site_id');
            $entry_id   = FALSE;

            // If we have pages, we'll look for an entry id
            if ($pages && isset($pages[$site_id]['uris']))
            {
                $match_uri = '/'.trim($this->EE->uri->uri_string, '/'); // will result in '/' if uri_string is blank
                $page_uris = $pages[$site_id]['uris'];

                // trim page uris in case there's a trailing slash on any of them
                foreach ($page_uris as $index => $value)
                {
                    $page_uris[$index] = '/'.trim($value, '/');
                }

                // case insensitive URI comparison
                $entry_id = array_search(strtolower($match_uri), array_map('strtolower', $page_uris));

                if ( ! $entry_id AND $match_uri != '/')
                {
                    $entry_id = array_search($match_uri.'/', $page_uris);
                }
            }

            // Found an entry - grab related template
            if ($entry_id)
            {
                $qry = $this->EE->db->select('t.template_name, tg.group_name')
                    ->from(array('templates t', 'template_groups tg'))
                    ->where('t.group_id', 'tg.group_id', FALSE)
                    ->where('t.template_id', $pages[$site_id]['templates'][$entry_id])
                    ->get();

                if ($qry->num_rows() > 0)
                {
                    /*
                        We do it this way so that we are not messing with
                        any of the segment variables, which should reflect
                        the actual URL and not our Pages redirect. We also
                        set a new QSTR variable so that we are not
                        interfering with other module's besides the Channel
                        module (which will use the new Pages_QSTR when available).
                    */
                    $template = $qry->row('template_name');
                    $template_group = $qry->row('group_name');
                    $this->EE->uri->page_query_string = $entry_id;
                }
            }
        }

        require_once APPPATH.'libraries/Template.php';
        $this->EE->TMPL = new EE_Template();
        $this->EE->TMPL->run_template_engine($template_group, $template);
        $this->EE->output->_display();
        exit();
    }

    // ********************************************************************************* //

	private function handle_errors()
	{
		//----------------------------------------
		// Handle Errors
		//----------------------------------------
		if (empty($this->EE->forms->errors) == FALSE)
		{
			//----------------------------------------
			// AJAX Request? (always comes first!)
			//----------------------------------------
			if (IS_AJAX == TRUE)
			{
				$out = array();
				$out['success'] = 'no';
				$out['type'] = 'validation';
				$out['errors'] = $this->EE->forms->errors;
				exit( $this->EE->forms_helper->generate_json($out) );
			}

			//----------------------------------------
			// Normal Errror Message?
			//----------------------------------------
			if ($this->EE->forms->data['display_error'] == 'default')
			{
				$error = array();
				foreach ($this->EE->forms->errors as $err) $error[] = $err['msg'];
				$this->EE->output->show_user_error('submission', $error);
			}
			else
			{
				//----------------------------------------
				// Are we using Pages or Structure?
				//----------------------------------------
				$template = (string)$this->EE->config->item('template');
				$template_group = (string) $this->EE->config->item('template_group');

				// Look for a page in the pages module
				if ($template_group == '' && $template == '')
				{
					$pages		= $this->EE->config->item('site_pages');
					$site_id	= $this->EE->config->item('site_id');
					$entry_id	= FALSE;

					// If we have pages, we'll look for an entry id
					if ($pages && isset($pages[$site_id]['uris']))
					{
						$match_uri = '/'.trim($this->EE->uri->uri_string, '/');	// will result in '/' if uri_string is blank
						$page_uris = $pages[$site_id]['uris'];

						$entry_id = array_search($match_uri, $page_uris);

						if ( ! $entry_id AND $match_uri != '/')
						{
							$entry_id = array_search($match_uri.'/', $page_uris);
						}
					}

					// Found an entry - grab related template
					if ($entry_id)
					{
						$qry = $this->EE->db->select('t.template_name, tg.group_name')
											->from(array('templates t', 'template_groups tg'))
											->where('t.group_id', 'tg.group_id', FALSE)
											->where('t.template_id',
												$pages[$site_id]['templates'][$entry_id])
											->get();

						if ($qry->num_rows() > 0)
						{
							/*
								We do it this way so that we are not messing with
								any of the segment variables, which should reflect
								the actual URL and not our Pages redirect. We also
								set a new QSTR variable so that we are not
								interfering with other module's besides the Channel
								module (which will use the new Pages_QSTR when available).
							*/
							$template = $qry->row('template_name');
							$template_group = $qry->row('group_name');
							$this->EE->uri->page_query_string = $entry_id;

							// DOes the structure exist?
							if (isset($this->EE->extensions->OBJ['Structure_ext']) == TRUE)
							{
								$this->EE->extensions->OBJ['Structure_ext']->sessions_start(null);
							}
						}
					}
				}


				// Format the Errors Array
				$_POST['forms_errors'] = array();
				$_POST['forms_global_errors'] = array();

				foreach ($this->EE->forms->errors as $err)
				{
					if ($err['field_id'] == 0) $_POST['forms_global_errors'][] = $err;
					else $_POST['forms_errors'][ $err['field_id'] ] = $err;
				}

				// Are we paging?
				if ($this->EE->forms->data['paging'] == TRUE && $this->EE->forms->data['current_page'] > 1)
				{
					$_POST['page'] = $this->EE->forms->data['current_page'];
					$this->EE->session->cache['forms']['current_page'] = $this->EE->forms->data['current_page'];

				}

				// Remove unwanted crap
				unset($_POST['ACT'], $this->EE->forms->dbfields, $this->EE->forms->data);

				require_once APPPATH.'libraries/Template.php';
				$this->EE->TMPL = new EE_Template();
				$this->EE->TMPL->run_template_engine($template_group, $template);
				$this->EE->output->_display();
				exit();
			}
		}
	}

	// ********************************************************************************* //

	protected function return_error($type, $msg)
	{
		// Ajax Response?
		if (IS_AJAX == TRUE)
		{
			$out = '{"success":"no", "type": "'.$type.'", "body": "'.$msg.'"}';
		}
		else
		{
			return $this->EE->output->show_user_error('submission', $msg);
		}

		return $out;
	}

	// ********************************************************************************* //

	private function process_emails()
	{
		// Load Email Library
		$this->EE->load->library('email');
		$tparams = $this->EE->forms->params;

		$debug = array();

		//----------------------------------------
		// CRLF & Newline
		//----------------------------------------
		$newline = "\n";
		$crlf = "\n";

		// EE Ones?
		if ($this->EE->config->item('email_newline') != false) {
			$newline = $this->EE->config->item('email_newline');
		}

		if ($this->EE->config->item('email_crlf') != false) {
			$crlf = $this->EE->config->item('email_crlf');
		}

		// Forms Specific one?
		$conf = $this->EE->config->item('forms');
		if (is_array($conf) === true) {

			if (isset($conf['email']['newline']) === true) {
				$newline = $conf['email']['newline'];
			}

			if (isset($conf['email']['crlf']) === true) {
				$crlf = $conf['email']['crlf'];
			}
		}

		//----------------------------------------
		// Send Admin?
		//----------------------------------------
		if ((int)$this->EE->forms->data['admin_template'] !== 0)
		{
			// Grab our template!
			$this->EE->db->select('*');
			$this->EE->db->from('exp_forms_email_templates');
			if ($this->EE->forms->data['admin_template'] > 0) $this->EE->db->where('template_id', $this->EE->forms->data['admin_template']);
			else $this->EE->db->where('form_id', $this->EE->forms->data['form_id']);
			$this->EE->db->where('template_type', 'admin');
			$query = $this->EE->db->get();

			// Store it for easy
			$email = $query->row();

			// Kill the db object
			$query->free_result();
			unset($query);

			//----------------------------------------
			// Template Params Override
			//----------------------------------------
			if (isset($tparams['notify_admin_email']) === TRUE) $email->email_to = $tparams['notify_admin_email'];
			if (isset($tparams['notify_admin_from_name']) === TRUE) $email->email_from = $tparams['notify_admin_from_name'];
			if (isset($tparams['notify_admin_from_email']) === TRUE) $email->email_from_email = $tparams['notify_admin_from_email'];
			if (isset($tparams['notify_admin_cc']) === TRUE) $email->email_cc = $tparams['notify_admin_cc'];
			if (isset($tparams['notify_admin_subject']) === TRUE) $email->email_subject = $tparams['notify_admin_subject'];
			if (isset($tparams['notify_admin_bcc']) === TRUE) $email->email_bcc = $tparams['notify_admin_bcc'];
			if (isset($tparams['notify_admin_replyto_name']) === TRUE) $email->email_reply_to = $tparams['notify_admin_replyto_name'];
			if (isset($tparams['notify_admin_replyto_email']) === TRUE) $email->email_reply_to_email = $tparams['notify_admin_replyto_email'];
			if (isset($tparams['notify_admin_replyto_author']) === TRUE) $email->reply_to_author = $tparams['notify_admin_replyto_author'];

			//----------------------------------------
			// Send Email!
			//----------------------------------------
			$this->EE->email->EE_initialize();

			$to = $email->email_to;
			if (isset($this->EE->session->cache['Forms']['EmailAdminOverride']) == TRUE)
			{
				$to = $this->EE->session->cache['Forms']['EmailAdminOverride'];
			}

			$email->email_cc = explode(',', $email->email_cc);
			foreach($email->email_cc as &$val) { $this->parse_forms_vars(trim($val)); }
			if (isset($this->EE->forms->master_info['admin_email_cc']) === TRUE && is_array($this->EE->forms->master_info['admin_email_cc']) === TRUE)
			{
				foreach ($this->EE->forms->master_info['admin_email_cc'] as $email_val) $email->email_cc[] = $email_val;
			}

			$email->email_bcc = explode(',', $email->email_bcc);
			foreach($email->email_bcc as &$val) { $this->parse_forms_vars(trim($val)); }
			if (isset($this->EE->forms->master_info['admin_email_bcc']) === TRUE && is_array($this->EE->forms->master_info['admin_email_bcc']) === TRUE)
			{
				foreach ($this->EE->forms->master_info['admin_email_bcc'] as $email_val) $email->email_bcc[] = $email_val;
			}

			//----------------------------------------
			// Custom Reply To?
			//----------------------------------------
			if ($email->reply_to_author == 'yes')
			{
				$this->EE->email->reply_to($this->EE->session->userdata['email']);
			}
			else
			{
				$this->EE->email->reply_to( $this->parse_forms_vars($email->email_reply_to_email), $this->parse_forms_vars($email->email_reply_to) );
			}

			$this->EE->email->wordwrap = ($email->email_wordwrap == 'yes') ? TRUE : FALSE;
			$this->EE->email->mailtype = $email->email_type;
			$this->EE->email->from( $this->parse_forms_vars($email->email_from_email), $this->parse_forms_vars($email->email_from));
			$this->EE->email->to( $to );
			$this->EE->email->subject( $this->parse_forms_vars($email->email_subject) );
			$this->EE->email->cc( $this->parse_forms_vars($email->email_cc) );
			$this->EE->email->bcc( $this->parse_forms_vars($email->email_bcc) );
			$this->EE->email->message( $this->parse_email_template($email) );
			$this->EE->email->set_newline($newline);
			$this->EE->email->set_crlf($crlf);

			if ($email->email_type == 'html') $this->EE->email->set_alt_message( $this->parse_email_template($email, TRUE) );

			// Handle Attachtments!
			if ($email->email_attachments == 'yes')
			{
				if (isset($this->EE->session->cache['Forms']['UploadedFiles']) == TRUE && is_array($this->EE->session->cache['Forms']['UploadedFiles']) == TRUE)
				{
					foreach($this->EE->session->cache['Forms']['UploadedFiles'] as $file)
					{
						$this->EE->email->attach($file);
					}
				}
			}


			// Send the Email!
			$this->EE->email->send();
			$debug['admin'] = $this->EE->email->print_debugger();

			//var_dump( ($this->EE->email->newline ==  $this->EE->config->item('email_newline')) );
			//var_dump( ($this->EE->email->crlf ==  $this->EE->config->item('email_crlf')) );
			//var_dump($this->EE->email->wordwrap);
			//var_dump($email);
			//exit($this->EE->email->print_debugger());

			// Clear all email vars (incl. attachments)
			$this->EE->email->clear(TRUE);
		}

		//----------------------------------------
		// Send User!
		//----------------------------------------
		if ((int)$this->EE->forms->data['user_template'] !== 0 && $this->EE->session->userdata['email'] != FALSE)
		{
			// Grab our template!
			$this->EE->db->select('*');
			$this->EE->db->from('exp_forms_email_templates');
			if ($this->EE->forms->data['user_template'] > 0) $this->EE->db->where('template_id', $this->EE->forms->data['user_template']);
			else $this->EE->db->where('form_id', $this->EE->forms->data['form_id']);
			$this->EE->db->where('template_type', 'user');
			$query = $this->EE->db->get();

			// Store it for easy
			$email = $query->row();

			// Kill the db object
			$query->free_result();
			unset($query);

			//----------------------------------------
			// Template Params Override
			//----------------------------------------
			if (isset($tparams['notify_user_email']) === TRUE) $email->email_to = $tparams['notify_user_email'];
			if (isset($tparams['notify_user_from_name']) === TRUE) $email->email_from = $tparams['notify_user_from_name'];
			if (isset($tparams['notify_user_from_email']) === TRUE) $email->email_from_email = $tparams['notify_user_from_email'];
			if (isset($tparams['notify_user_cc']) === TRUE) $email->email_cc = $tparams['notify_user_cc'];
			if (isset($tparams['notify_user_subject']) === TRUE) $email->email_subject = $tparams['notify_user_subject'];
			if (isset($tparams['notify_user_bcc']) === TRUE) $email->email_bcc = $tparams['notify_user_bcc'];
			if (isset($tparams['notify_user_replyto_name']) === TRUE) $email->email_reply_to = $tparams['notify_user_replyto_name'];
			if (isset($tparams['notify_user_replyto_email']) === TRUE) $email->email_reply_to_email = $tparams['notify_user_replyto_email'];
			if (isset($tparams['notify_user_replyto_author']) === TRUE) $email->reply_to_author = $tparams['notify_user_replyto_author'];

			$email->email_cc = explode(',', $email->email_cc);
			foreach($email->email_cc as &$val) { $this->parse_forms_vars(trim($val)); }
			if (isset($this->EE->forms->master_info['user_email_cc']) === TRUE && is_array($this->EE->forms->master_info['user_email_cc']) === TRUE)
			{
				foreach ($this->EE->forms->master_info['user_email_cc'] as $email_val) $email->email_cc[] = $email_val;
			}

			$email->email_bcc = explode(',', $email->email_bcc);
			foreach($email->email_bcc as &$val) { $this->parse_forms_vars(trim($val)); }
			if (isset($this->EE->forms->master_info['user_email_bcc']) === TRUE && is_array($this->EE->forms->master_info['user_email_bcc']) === TRUE)
			{
				foreach ($this->EE->forms->master_info['user_email_bcc'] as $email_val) $email->email_bcc[] = $email_val;
			}

			//----------------------------------------
			// Send Email!
			//----------------------------------------
			$this->EE->email->EE_initialize();

			$this->EE->email->wordwrap = ($email->email_wordwrap == 'yes') ? TRUE : FALSE;
			$this->EE->email->mailtype = $email->email_type;
			$this->EE->email->from( $this->parse_forms_vars($email->email_from_email), $this->parse_forms_vars($email->email_from) );
			$this->EE->email->reply_to( $this->parse_forms_vars($email->email_reply_to_email), $this->parse_forms_vars($email->email_reply_to) );
			$this->EE->email->to($this->EE->session->userdata['email']);
			$this->EE->email->subject( $this->parse_forms_vars($email->email_subject) );
			$this->EE->email->cc( $this->parse_forms_vars($email->email_cc) );
			$this->EE->email->bcc( $this->parse_forms_vars($email->email_bcc) );
			$this->EE->email->message( $this->parse_email_template($email) );
			$this->EE->email->set_newline($newline);
			$this->EE->email->set_crlf($crlf);

			if ($email->email_type == 'html') $this->EE->email->set_alt_message( $this->parse_email_template($email, TRUE) );

			// Handle Attachtments!
			if ($email->email_attachments == 'yes')
			{
				if (isset($this->EE->session->cache['Forms']['UploadedFiles']) == TRUE && is_array($this->EE->session->cache['Forms']['UploadedFiles']) == TRUE)
				{
					foreach($this->EE->session->cache['Forms']['UploadedFiles'] as $file)
					{
						$this->EE->email->attach($file);
					}
				}
			}

			// Send the Email!
			$ret = $this->EE->email->send();
			$debug['user'] = $this->EE->email->print_debugger();

			//print_r($email);
			//print_r($this->EE->email->print_debugger());

			// Clear all email vars (incl. attachments)
			$this->EE->email->clear(TRUE);
		}

		if (isset($this->EE->forms->data['form_settings']['debug_mode']) === TRUE && $this->EE->forms->data['form_settings']['debug_mode'] == 'yes')
		{
			$this->EE->db->set('debug_info', serialize(array('email' => $debug)));
			$this->EE->db->where('fentry_id', $this->fentry_id);
			$this->EE->db->update('exp_forms_entries');
		}

	}

	// ********************************************************************************* //

	private function parse_email_template($email_template, $alt_body=FALSE)
	{
		$out = '';

		// What Email Type? (for form fields display method)
		$email_type = $email_template->email_type;
		if ($alt_body == TRUE) $email_type = 'text';

		//----------------------------------------
		// Get the template body
		//----------------------------------------
		if ($alt_body == TRUE)
		{
			$out = $email_template->alt_template;
		}
		elseif ($email_template->ee_template_id > 0)
		{
			$query = $this->EE->db->select('template_data')->from('exp_templates')->where('template_id', $email_template->ee_template_id)->get();
			$out = $query->row('template_data');
		}
		else
		{
			$out = $email_template->template;
		}

		// Empty? Nothing to do then!
		if ($out == FALSE) return '';

		//----------------------------------------
		// Parse available variables!
		//----------------------------------------
		$out = $this->parse_forms_vars($out, $email_type);

		//----------------------------------------
		// Loop over all fields?
		//----------------------------------------
		if (strpos($out, '{form:fields}') !== FALSE)
		{
			// Grab the data between the pairs
			$tagdata = $this->EE->forms_helper->fetch_data_between_var_pairs('form:fields', $out);

			$final = '';
			$count = 0;

			// Loop over all fields
			foreach ($this->EE->forms->dbfields as $field)
			{
				if (in_array($field['field_type'], $this->EE->forms->ignored_fields) === TRUE) continue;

				$row = '';
				$count++;

				// Create the VARS
				$vars = array();
				$vars['{field:label}'] = $field['title'];
				$vars['{field:short_name}'] = $field['url_title'];
				$vars['{field:value}'] = $this->EE->formsfields[ $field['field_type'] ]->output_data($field, $this->EE->forms->finaldata[ $field['field_id'] ], $email_type);
				$vars['{field:count}'] = $count;

				// Convert back to html entities
				$vars['{field:value}'] = html_entity_decode($vars['{field:value}'], ENT_QUOTES, 'UTF-8');

				// Parse them
				$row = str_replace(array_keys($vars), array_values($vars), $tagdata);

				$final .= $row;
			}

			// Replace the var pair!
			$out = $this->EE->forms_helper->swap_var_pairs('form:fields', $final, $out);
		}

		//----------------------------------------
		// Allows template parsing!
		//----------------------------------------
		if (class_exists('EE_Template') == FALSE) require_once APPPATH.'libraries/Template.php';
		$this->EE->TMPL = new EE_Template();
		$this->EE->TMPL->parse($out, FALSE, $this->site_id);
		$out = $this->EE->TMPL->final_template;

		return $out;
	}

	// ********************************************************************************* //

	private function parse_forms_vars($string, $format='text')
	{
		$vars = array();
		$vars['{form:label}'] = $this->EE->forms->data['form_title'];
		$vars['{form:short_name}'] = $this->EE->forms->data['form_url_title'];
		$vars['{form:id}'] = $this->EE->forms->data['form_id'];
		$vars['{user:referrer}'] = (isset($_SERVER['HTTP_REFERER']) == TRUE) ? $_SERVER['HTTP_REFERER'] : '';
		$vars['{date:usa}'] = $this->EE->forms_helper->formatDate('%m/%d/%Y', $this->EE->localize->now);
		$vars['{date:eu}'] = $this->EE->forms_helper->formatDate('%d/%m/%Y', $this->EE->localize->now);
		$vars['{datetime:usa}'] = $this->EE->forms_helper->formatDate('%m/%d/%Y %h:%i %A', $this->EE->localize->now);
		$vars['{datetime:eu}'] =  $this->EE->forms_helper->formatDate('%d/%m/%Y %H:%i', $this->EE->localize->now);

		if (isset($this->fentry_id) === TRUE)
		{
			$vars['{fentry_id}'] = $this->fentry_id;
		}

		if (isset($this->fentry_hash) === TRUE)
		{
			$vars['{fentry_hash}'] = $this->fentry_hash;
		}

		// Parse it!
		$string = str_replace(array_keys($vars), array_values($vars), $string);

		// Parse all user session data too
		foreach($this->EE->session->userdata as $var => $val)
		{
			// Val has arrays? Ignore them!
			if (is_array($val) == TRUE) continue;

			$string = str_replace('{user:'.$var.'}', $val, $string);
		}

		foreach($this->EE->forms->data as $var => $val)
		{
			// Val has arrays? Ignore them!
			if (is_array($val) == TRUE) continue;

			$string = str_replace('{form:'.$var.'}', $val, $string);
		}

		foreach ($this->EE->forms->dbfields as $field)
		{
			if (in_array($field['field_type'], $this->EE->forms->ignored_fields) === TRUE) continue;

			$string = str_replace('{field:'.$field['url_title'].'}', $this->EE->formsfields[ $field['field_type'] ]->output_data($field, $this->EE->forms->finaldata[ $field['field_id'] ], $format), $string);
		}

		return $string;
	}

	// ********************************************************************************* //

	private function process_third_party_submission()
	{
		$fsettings = $this->EE->forms->data['form_settings'];
		if (isset($fsettings['third_party']['flow']) === FALSE) return;
		if ($fsettings['third_party']['flow'] == 'disabled') return;
		if (trim($fsettings['third_party']['url']) == FALSE) return;
		if (function_exists('curl_init') === FALSE) return;

		if (isset($fsettings['third_party']['field_identifier']) === TRUE)
		{
			if ($fsettings['third_party']['field_identifier'] == 'field_name')
			{
				// Grab all fields
				$dbfields = array();
				$query = $this->EE->db->select('field_id, url_title')->from('exp_forms_fields')->where_in('field_id', array_keys($this->EE->forms->finaldata))->get();
				foreach ($query->result() as $key => $value)
				{
					$dbfields[ $value->field_id ] = $value->url_title;
				}

				$post_data = array();
				foreach($this->EE->forms->finaldata as $field_id => $field_value)
				{
					if (isset($dbfields[$field_id]) === TRUE)
					{
						$post_data[ $dbfields[$field_id] ] = $field_value;
					}
				}
			}
			else
			{
				$post_data = $this->EE->forms->finaldata;
			}
		}

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $fsettings['third_party']['url']);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_USERAGENT, $this->EE->input->server('HTTP_USER_AGENT'));
		curl_setopt($ch, CURLOPT_REFERER, $this->EE->input->server('HTTP_REFERER'));
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_POST, TRUE);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
		$response = @curl_exec($ch);
		curl_close($ch);
	}

	// ********************************************************************************* //

	private function process_return_user($fentry_id=0)
	{
		//----------------------------------------
		// Return the USER
		//----------------------------------------
		$RET = $this->EE->forms->data['return'];

		// Is there an override?
		if ($this->EE->input->post('override_return') != false) {
			$RET = $this->EE->input->post('override_return');
		}

		// Parse Fentry ID
		$RET = str_replace('%FENTRY_ID%', $fentry_id, $RET);
		$RET = str_replace('%FENTRY_HASH%', $this->fentry_hash, $RET);

		if (IS_AJAX == TRUE)
		{
			$out = '{"success":"yes", "body": ""}';
			exit($out);
		}
		else
		{
			$this->EE->load->helper('string');

			$RET = $this->parse_forms_vars($RET);

			// Do we need to create an URL?
			if (strpos($RET, 'http://') === FALSE && strpos($RET, 'https://') === FALSE)
			{
				$RET = $this->EE->functions->create_url(trim_slashes($RET));
				$RET = reduce_double_slashes($RET);
			}

			//----------------------------------------
			// Confirmation Message?
			//----------------------------------------
			if (isset($this->EE->forms->data['form_settings']['confirmation']['when']) == TRUE)
			{
				$when = $this->EE->forms->data['form_settings']['confirmation']['when'];

				// Show Before Redirect?
				if ($when == 'before_redirect')
				{
					// Build success message
					$data = array(	'title' 	=> $this->EE->forms->module_settings['messages']['form_submit_success_heading'],
									'heading'	=> $this->EE->forms->module_settings['messages']['form_submit_success_heading'],
									'content'	=> $this->EE->forms->data['form_settings']['confirmation']['text'],
									'redirect'	=> $RET,
									'rate' => ((isset($this->EE->forms->data['form_settings']['confirmation']['redirect_timeout']) == FALSE) ? $this->EE->forms->data['form_settings']['confirmation']['redirect_timeout'] : 2),
									//'link'		=> array($RET, $site_name)
								 );
					$this->EE->output->show_message($data);
				}

				// Show Only?
				if ($when == 'show_only')
				{
					// Build success message
					$data = array(	'title' 	=> $this->EE->forms->module_settings['messages']['form_submit_success_heading'],
									'heading'	=> $this->EE->forms->module_settings['messages']['form_submit_success_heading'],
									'content'	=> $this->EE->forms->data['form_settings']['confirmation']['text']
								 );

					$this->EE->output->show_message($data);
				}

				// Just Redirect?
				if ($when == 'after_redirect' OR $when == 'disabled')
				{
					if ($when == 'after_redirect') $this->EE->session->set_flashdata('forms:show_confirm', 'yes');
					$this->EE->functions->redirect($RET);
				}
			}
		}
	}


	// ********************************************************************************* //

	private function process_field_string($string)
	{
		$string = $this->EE->security->xss_clean($string);
		$string = htmlentities($string, ENT_QUOTES, 'UTF-8');

		return $string;
	}

	// ********************************************************************************* //


} // END CLASS

/* End of file act.forms.php  */
/* Location: ./system/expressionengine/third_party/forms/act.forms.php */
