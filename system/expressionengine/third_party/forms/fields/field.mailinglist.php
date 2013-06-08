<?php if (!defined('BASEPATH')) die('No direct script access allowed');

/**
 * Channel Forms MAILINGLIST field
 *
 * @package			DevDemon_Forms
 * @author			DevDemon <http://www.devdemon.com> - Lead Developer @ Parscale Media
 * @copyright 		Copyright (c) 2007-2011 Parscale Media <http://www.parscale.com>
 * @license 		http://www.devdemon.com/license/
 * @link			http://www.devdemon.com/forms/
 * @see				http://expressionengine.com/user_guide/development/fieldtypes.html
 */
class FormsField_mailinglist extends FormsField
{

	/**
	 * Field info - Required
	 *
	 * @access public
	 * @var array
	 */
	public $info = array(
		'title'		=>	'Mailinglist',
		'name' 		=>	'mailinglist',
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
		if (isset($field['settings']['subscribe_text']) == FALSE)
		{
			$field['settings']['subscribe_text'] = $this->EE->lang->line('form:subrtext');
		}

		if ($data == FALSE && isset($field['settings']['checked']) === TRUE && $field['settings']['checked'] == 'yes') $data = 'yes';

		if ($data == 'yes') $data = TRUE;
		else $data = FALSE;

		$out = '<ul class="checkboxes">';
		$out .= '<li><label>' . form_checkbox($field['form_name'], 'yes', $data) .'&nbsp;'. $field['settings']['subscribe_text'] . '</label></li>';
		$out .= '</ul>';

		return $out;
	}

	// ********************************************************************************* //

	public function save($field=array(), $data)
	{
		if ($data != 'yes') return 'no';
		$email = $this->EE->session->userdata('email');

		// -----------------------------------------
		// EE Mailing List?
		// -----------------------------------------
		if (isset($field['settings']['type']) == TRUE && $field['settings']['type'] == 'ee')
		{
			// Lets doublecheck
			if (isset($field['settings']['ee']['list']) == TRUE && $field['settings']['ee']['list'] != FALSE)
			{
				// Kill duplicate emails from authorization queue.  This prevents an error if a user
				// signs up but never activates their email, then signs up again.  Note- check for list_id
				// as they may be signing up for two different llists
				$this->EE->db->where('email', $email);
				$this->EE->db->where('list_id', $field['settings']['ee']['list']);
				$this->EE->db->delete('exp_mailing_list_queue');

				// Already subscribed?
				$this->EE->db->select('COUNT(*) AS count', FALSE);
				$this->EE->db->from('exp_mailing_list');
				$this->EE->db->where('email', $email);
				$this->EE->db->where('list_id', $field['settings']['ee']['list']);
				$query = $this->EE->db->get();

				if ($query->row('count') == 0)
				{
					$code = $this->EE->functions->random('alnum', 10);
					$this->EE->db->set('list_id', $field['settings']['ee']['list']);
					$this->EE->db->set('authcode', $code);
					$this->EE->db->set('email', $email);
					$this->EE->db->set('ip_address', $this->EE->input->ip_address());
					$this->EE->db->insert('exp_mailing_list');
				}

				return 'yes';
			}
		}

		// -----------------------------------------
		// Mailchimp?
		// -----------------------------------------
		if (isset($field['settings']['type']) == TRUE && $field['settings']['type'] == 'mailchimp')
		{
			// Grab Module Settings
			$settings = $this->EE->forms_helper->grab_settings($this->site_id);

			// Lets doublecheck
			if (isset($field['settings']['mailchimp']['list']) == TRUE && $field['settings']['mailchimp']['list'] != FALSE)
			{
				// Include the class
				if (class_exists('MCAPI') == FALSE) include PATH_THIRD."forms/libraries/mailchimp/MCAPI.class.php";
				$CHIMP = new MCAPI($settings['mailchimp']['api_key']);

				// Already in the list?
				$retval = $CHIMP->listMemberInfo($field['settings']['mailchimp']['list'], array($email));

				if ($retval['errors'] > 0)
				{
					// Custom Fields
					$fields = array();

					if (isset($this->EE->forms->master_info['name']['first_name']) == TRUE)
					{
						$fields['FNAME'] = $this->EE->forms->master_info['name']['first_name'];
					}

					if (isset($this->EE->forms->master_info['name']['last_name']) == TRUE)
					{
						$fields['LNAME'] = $this->EE->forms->master_info['name']['last_name'];
					}

					// Lets subscribe them!
					$retval = $CHIMP->listSubscribe($field['settings']['mailchimp']['list'], $email, $fields, 'html', FALSE);
				}

				return 'yes';
			}
		}

		// -----------------------------------------
		// Campaign Monitor
		// -----------------------------------------
		if (isset($field['settings']['type']) == TRUE && $field['settings']['type'] == 'createsend')
		{
			// Grab Module Settings
			$settings = $this->EE->forms_helper->grab_settings($this->site_id);

			// Lets doublecheck
			if (isset($field['settings']['createsend']['list']) == TRUE && $field['settings']['createsend']['list'] != FALSE)
			{
				// Include the class
				if (class_exists('CS_REST_General') == FALSE) include PATH_THIRD."forms/libraries/createsend/csrest_general.php";
				if (class_exists('CS_REST_Subscribers') == FALSE) include PATH_THIRD."forms/libraries/createsend/csrest_subscribers.php";

				$CSS = new CS_REST_Subscribers($field['settings']['createsend']['list'], $settings['createsend']['api_key']);

				// Already subsribed?
				if (! $CSS->get($email)->was_successful())
				{
					$fields = array();
					$fields['EmailAddress'] = $email;
					$fields['Name'] = '';

					if (isset($this->EE->forms->master_info['name']['first_name']) == TRUE)
					{
						$fields['Name'] .= $this->EE->forms->master_info['name']['first_name'];
					}

					if (isset($this->EE->forms->master_info['name']['last_name']) == TRUE)
					{
						$fields['Name'] .= ' ' . $this->EE->forms->master_info['name']['last_name'];
					}

					$result = $CSS->add($fields);
				}

				return 'yes';
			}
		}


		return 'yes';
	}

	// ********************************************************************************* //

	public function field_settings($settings=array(), $pbf=FALSE)
	{
		$vData = $settings;

		// Grab Module Settings
		$settings = $this->EE->forms_helper->grab_settings($this->site_id);

		// -----------------------------------------
		// Grab all Mailinglist Module Settings
		// -----------------------------------------
		$vData['ee']['lists'] = array();
		if ($this->EE->db->table_exists('exp_mailing_lists') == TRUE)
		{
			$query = $this->EE->db->select('list_title, list_id')->from('exp_mailing_lists')->order_by('list_title', 'ASC')->get();

			foreach ($query->result() as $row)
			{
				$vData['ee']['lists'][ $row->list_id ] = $row->list_title;
			}
		}

		// -----------------------------------------
		// Grab All Mailchimp Lists
		// -----------------------------------------
		$vData['mailchimp']['lists'] = array();
		if (isset($settings['mailchimp']['api_key']) == TRUE && $settings['mailchimp']['api_key'] != FALSE)
		{
			// Include the class
			if (class_exists('MCAPI') == FALSE) include PATH_THIRD."forms/libraries/mailchimp/MCAPI.class.php";
			$CHIMP = new MCAPI($settings['mailchimp']['api_key']);

			// Grab all lists
			$response = $CHIMP->lists(array(), 0, 100);

			//$this->EE->firephp->log($response);

			// Check the response
			if (isset($response['data']) == TRUE && empty($response['data']) == FALSE)
			{
				foreach($response['data'] as $list)
				{
					$groups = $CHIMP->listInterestGroupings($list['id']);
					$vData['mailchimp']['lists'][ $list['id'] ] =  $list['name'];
				}
			}
		}

		// -----------------------------------------
		// Grab All Campaign Monitor Lists
		// -----------------------------------------
		$vData['createsend']['lists'] = array();
		if (isset($settings['createsend']['api_key']) == TRUE && $settings['createsend']['api_key'] != FALSE && isset($settings['createsend']['client_api_key']) == TRUE && $settings['createsend']['client_api_key'] != FALSE)
		{
			// Include the client class
			if (class_exists('CS_REST_Clients') == FALSE) include PATH_THIRD."forms/libraries/createsend/csrest_clients.php";

			// Client Object
			$CSC = new CS_REST_Clients($settings['createsend']['client_api_key'], $settings['createsend']['api_key']);

			// Grab all lists from this client
			$lists = $CSC->get_lists();

			if ($lists->was_successful())
			{
				foreach ($lists->response as $list)
				{
					$vData['createsend']['lists'][ $list->ListID ] = $list->Name;
				}
			}
		}

		// Default!
		if (isset($vData['type']) == FALSE)
		{
			if (empty($vData['ee']['lists']) == FALSE) $vData['type'] = 'ee';
			elseif (empty($vData['mailchimp']['lists']) == FALSE) $vData['type'] = 'mailchimp';
			elseif (empty($vData['createsend']['lists']) == FALSE) $vData['type'] = 'createsend';
		}


		return $this->EE->load->view('fields/mailinglist', $vData, TRUE);
	}

	// ********************************************************************************* //

}

/* End of file field.mailinglist.php */
/* Location: ./system/expressionengine/third_party/forms/fields/field.mailinglist.php */
