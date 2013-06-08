<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Ep_status_transition
 *
 * ----------------------------------------------------------------------------------------------
 * @package	EE2 
 * @subpackage	ThirdParty
 * @author	Andrea Fiore / Malcolm Elsworth 
 * @link	http://electricputty.co.uk 
 * @copyright	Copyright (c) 2011 Electric Putty Ltd.
 *	
 */

class Ep_status_transition
{

	private $editor_group_ids	= array();
	private $publisher_group_ids	= array();
	private $channel_ids		= array();
	private $channel_templates	= array();
	private $session_group_id	= NULL;
	private $settings		= NULL;
	private $status			= NULL;
	private $db_operation		= NULL;
	private $role			= NULL;
	private $edit_view_url		= 'C=content_edit';

	/**
	* Instantiate the statusTransition class in the client
	*
	*
	*/
	function Ep_status_transition($session_group_id,$settings=array())
	{
		$this->EE =& get_instance(); 
		$this->settings = $settings;
		$this->_parse_settings();

		// -------------------------------------------
		// Load the library file and instantiate logger
		// -------------------------------------------
		require_once reduce_double_slashes(PATH_THIRD . '/ep_better_workflow/libraries/ep_bwf_logger.php');
		$this->action_logger = new Ep_workflow_logger($this->settings['advanced']['log_events']);

		// -------------------------------------------
		// Define the current user's role
		// -------------------------------------------
		$this->session_group_id = $session_group_id;

		if (in_array($session_group_id,$this->publisher_group_ids))
		{
			$this->role = 'publisher';
		}
		elseif(in_array($session_group_id,$this->editor_group_ids))
		{
			$this->role = 'editor';
		}

		// --------------------------------------------------------
		// Override the edit_view_url if we're using Zenbu or Structure
		// --------------------------------------------------------
		if($this->settings['advanced']['redirect_on_action'] == 'zenbu')
		{
			$channel_id = $this->EE->input->get('channel_id');
			$this->edit_view_url = 'C=addons_modules&M=show_module_cp&module=zenbu&channel_id='.$channel_id;
		} else if($this->settings['advanced']['redirect_on_action'] == 'structure')
		{
			$this->edit_view_url = 'C=addons_modules&M=show_module_cp&module=structure';
		}
	}



	function _parse_settings(){
		$settings = $this->settings;
		foreach(array('channels','groups') as $k)
		{
			if (isset($settings[$k]))
			{
				foreach($settings[$k] as $kk => $vv)
				{
					$id = preg_replace('/^id_/','',$kk);
					if ($k == 'channels')
					{
						if (isset($vv['uses_workflow']) && strtolower($vv['uses_workflow']) == 'yes' ) $this->channel_ids[]=$id; 
						if (isset($vv['template'])) $this->channel_templates['id_'.$id] = $vv['template'];
					}

					if ($k == 'groups')
					{
						if (isset($vv['role']) && strtolower($vv['role']) == 'editor' ) $this->editor_group_ids[]=$id; 
						if (isset($vv['role']) && strtolower($vv['role']) == 'publisher' ) $this->publisher_group_ids[]=$id;
					}
				}
			}
			else
			{
				// list($file,$line) = array(__FILE__,__LINE__);
				// show_error("Ep_status_transition($file, line: $line): You need to set workflow enabled Channels and Groups [link to settings here]") ;
			}
		}
	}



	function instantiate_js_class($entry_data=NULL)
	{
		$this->EE->load->library('javascript');
		$this->EE->load->model('ep_entry_draft');

		$entry_id	= ($entry_data && isset($entry_data['entry_id']))? $entry_data['entry_id'] : NULL;
		$channel_id	= ($entry_data && isset($entry_data['channel_id'])) ? $entry_data['channel_id'] : NULL;
		$url_title 	= ($entry_data && isset($entry_data['url_title'])) ? $entry_data['url_title'] : NULL;

		$this->draft_data = (is_numeric($entry_id))? $this->EE->ep_entry_draft->get_by_entry_id($entry_id) : NULL;
		
		// Load the state options for the current role
		$this->EE->load->model('ep_roles');
		$this->EE->ep_roles->initialise($this->EE->config->item('site_id'), $this->role);
		$states = $this->EE->ep_roles->get_role_states();

		// Build up array of arguments to pass to the JavaScript constructor
		$js_args=array();
		
		// User role
		$js_args['userRole'] = $this->role;
		
		//entryId
		$js_args['entryId'] = $entry_id;
		
		//url_title
		$js_args['urlTitle'] = $url_title;
		
		//entryExists
		$js_args['entryExists'] = ($entry_id != NULL) ? true : false;
		
		//entryStatus
		$js_args['entryStatus'] = ($entry_data && isset($entry_data['status'])) ? "{$entry_data['status']}" : NULL;
		
		//draftExists
		$js_args['draftExists'] = ($this->draft_data != NULL) ? true : false;
		
		//draftStatus
		$js_args['draftStatus'] = ($this->draft_data && isset($this->draft_data->status)) ? "{$this->draft_data->status}" : NULL;
		
		//baseURL
		$index_php = ($this->settings['advanced']['remove_index_php'] == 'yes') ? '/' : '/index.php/';
		$js_args['baseURL'] = substr(reduce_double_slashes($this->EE->config->item('site_url') . $index_php), 0, -1);

		//previewTemplate
		$preview_url = '/'.$this->channel_templates['id_'.$channel_id];
		if($this->settings['advanced']['preview_last_segement'] == 'URL Title')
		{
			$js_args['previewTemplate'] = ($url_title != NULL) ? $preview_url.'/'.$url_title : $preview_url.'/undefined';				
		}
		else
		{
			$js_args['previewTemplate'] = ($entry_id != NULL) ? $preview_url.'/'.$entry_id : $preview_url.'/undefined';			
		}
		$js_args['previewLastSegment'] = $this->settings['advanced']['preview_last_segement'];
		
		//Display full url on preview window
		$js_args['showFullUrl'] = ($this->settings['advanced']['display_full_preview_url'] == 'yes') ? true : false;

		//Preview on new entry
		$js_args['disablePreview'] = $this->settings['advanced']['disable_preview'];
		
		//Show full error messages
		$js_args['showErrors'] = ($this->settings['advanced']['show_errors'] == 'yes') ? true : false;
		
		//EE Version number
		$js_args['EEVersion'] = APP_VER;

		//Ignore page/structure URL
		$js_args['ignorePageUrl'] = ($this->settings['advanced']['ignore_page_url'] == 'yes') ? true : false;
		
		// Log events?
		$js_args['logEvents'] = ($this->settings['advanced']['log_events'] == 'yes') ? true : false;
		
		// Enable external previews
		$js_args['externalPreviews'] = ($this->settings['advanced']['enable_external_previews'] == 'yes') ? true : false;

		// Convert the settings and state arrays to JSON
		$constructor_args = $this->EE->javascript->generate_json($js_args, TRUE);
		$state_args = $this->EE->javascript->generate_json($states, TRUE);
		
		$out=  <<<EOD
		jQuery(function($) {
		  Bwf._transitionInstance = new Bwf.StatusTransition($constructor_args);
		  Bwf._transitionInstance.roleOptions = $state_args;
		  Bwf._transitionInstance.render();
		});
EOD;

	return $out;
	}



	function process_button_input(&$entry_meta, &$entry_data, $input=NULL)
	{
		// Logging
		$this->action_logger->add_to_log("ep_status_transition: process_button_input()");

		$input or $input = $_REQUEST;

		// Set a flag so we only call the notify method once
		$notify_users_called = false;
				
		foreach($input as $k => $v)
		{
			// -----------------------------------------------------------------------------------------------------------
			// Do we need to send a notification for this transition
			// -----------------------------------------------------------------------------------------------------------
			if (preg_match('/_epBwfNotify/',$k))
			{
				// Make sure we only call this once
				if( ! $notify_users_called )
				{
					$this->_notify_users($entry_data['channel_id'], $entry_data['entry_id'], $entry_meta['title']);
					$notify_users_called = true;
				}
			}

			// -----------------------------------------------------------------------------------------------------------
			// Here we check to see if:
			// 1. We are creating a new entry
			// 2. We are updating an existing entry
			// 3. We are creating a draft
			// 4. We are updating an existing draft (this also applies to 'submitted for approval' and 'revert to draft')
			// 5. We are turning a draft into an entry
			// 6. We are discarding a draft
			
			// Conditions 1 and 2, we are creating or updating an entry - this includes 'Submit for approval'
			if (preg_match('/^epBwfEntry_/',$k))
			{
				// Loggin
				$this->action_logger->add_to_log("ep_status_transition: process_button_input(), Conditions 1 and 2, we are creating or updating an entry - this includes 'Submit for approval'");
				
				// Kill any extraneous relationship data
				$this->EE->load->model('ep_rels');
				$this->EE->ep_rels->delete_existing_relationships($entry_data['entry_id'], $entry_data);
				break;
			}

			// Conditions 3 and 4, we are creating or updating a draft - this includes 'Submit for approval' and 'revert to draft'
			elseif(preg_match('/^epBwfDraft_create_/',$k) || preg_match('/^epBwfDraft_update_/',$k))
			{
				// Loggin
				$this->action_logger->add_to_log("ep_status_transition: process_button_input(), Conditions 3 and 4, we are creating or updating a draft - this includes 'Submit for approval' and 'revert to draft'");
			
				$this->_process_draft_button_input($entry_meta ,$entry_data, $k, $v);
				break;
			}
			
			// Condition 5, we are converting a draft into an entry
			elseif(preg_match('/^epBwfDraft_replace_/',$k))
			{
				// Loggin
				$this->action_logger->add_to_log("ep_status_transition: process_button_input(), Condition 5, we are converting a draft into an entry");
				
				// By the time we get to here the rels for our updated entry have been built so we need to
				// delete any existing relationshiops _not_ in the entry data
				$this->EE->load->model('ep_rels');
				$this->EE->ep_rels->delete_existing_relationships($entry_data['entry_id'], $entry_data);
			
				// Delete the existing draft
				$this->EE->load->model('ep_entry_draft');
				$this->EE->ep_entry_draft->delete(array('entry_id' => $entry_data['entry_id']));
				
				// Get the status from the button value
				@list($status,$db_operation) = explode('|',$v);
				
				// Double check we have a status here - there seems to be an IE8 issue where somethimes this is returned blank
				// This seems to be causing issues - need a more robust method of ensuring we have a status
				if($status == '' or is_null($status) or strtolower($status) == 'publish') $status = 'open';
				
				return $status;
				break;
			}
			
			// Condition 6, we are deleting a draft and reverting to the live version
			elseif(preg_match('/^epBwfDraft_delete_/',$k))
			{
				// Loggin
				$this->action_logger->add_to_log("ep_status_transition: process_button_input(), Condition 6, we are deleting a draft and reverting to the live version");
			
				// Delete the existing draft
				$this->EE->load->model('ep_entry_draft');
				$this->EE->ep_entry_draft->delete(array( 'entry_id' => $entry_data['entry_id']));
				
				$this->EE->session->set_flashdata('message_success', "Draft version of '{$entry_meta['title']}' has been deleted"); 
				$this->EE->functions->redirect(BASE.AMP.$this->edit_view_url);
				die();
			}
		}
	}



	/**
	* Checks if channel uses workflow and if user has sufficient permissions. 
	* Called from channel_has_workflow() method in ext. file
	* @return {boolean} 
	*/
	function has_workflow($channel_id)
	{
		$session_group_id = $this->session_group_id;
		$out = ($this->role && in_array($channel_id, $this->channel_ids));

		//echo "-> role: {$this->role} <br/>";
		//echo "-> session_group_id:". var_export($channel_id) ." <br/>";
		//echo "-> channel_ids: ". var_export($this->channel_ids,TRUE) . "<br/>";
		//echo "-> out: $out <br/>";

		$this->action_logger->add_to_log("ep_status_transition: has_workflow(): Check whether a given channel uses BWF");
		return $out;
	}



	/**
	* Private function to send notification emails to all members of selected group. 
	*
	* return void
	*/
	function _notify_users($channel_id, $entry_id, $entry_title)
	{		
		// If we have a member group set for this channel
		if(isset($this->settings['channels']['id_'.$channel_id]['notification_group']) && $this->settings['channels']['id_'.$channel_id]['notification_group'] != '')
		{
			// Check we have an entry ID, if this is a brand new entry we may not
			if($entry_id == 0 || is_null($entry_id) || $entry_id == '')
			{
				// If we don't have an entry ID, set a session flag to call this method later
				$this->EE->session->cache['ep_better_workflow']['notify_users'] = TRUE;
				return;
			}
			
			// Set a flag to send notification emails, if the hook is used this can be set to false
			$send_notifications = true;
			
			// Hook for 3rd party devs to write their own notification processes
			// -------------------------------------------
			if ($this->EE->extensions->active_hook('bwf_notify_users'))
			{
				$send_notifications = $this->EE->extensions->call('bwf_notify_users', $channel_id, $entry_id, $entry_title);
			}
			// -------------------------------------------
			
			// If the hook call returns false stop here
			if (!$send_notifications) return;

			$notification_group = $this->settings['channels']['id_'.$channel_id]['notification_group'];
			
			// Get all members from this group
			$this->EE->load->model('ep_members');
			$notifications = $this->EE->ep_members->get_all_members_from_group($notification_group);
			
			// If we didn't get any members
			if($notifications == FALSE) return;
			
			// Load the email library and text helper
			$this->EE->load->library('email');
			$this->EE->load->helper('text');

			$this->EE->email->wordwrap = true;
			$this->EE->email->mailtype = 'text';
			
			// Create the URL to the entry's publish view
			$s = 0;
			if ($this->EE->config->item('admin_session_type') != 'c')
			{
				$s = $this->EE->session->userdata('session_id', 0);
			}
			$review_url = reduce_double_slashes($this->EE->config->item('cp_url')."?S=".$s.AMP."D=cp".AMP."C=content_publish".AMP."M=entry_form".AMP."channel_id={$channel_id}".AMP."entry_id={$entry_id}");
			
			// Build up the message content
			$the_message = "";
			$the_message .= "Dear [MEM_NAME]\n\n";
			$the_message .= $this->EE->session->userdata['screen_name'] . " has submitted the entry '{$entry_title}' for approval.\n\n";
			$the_message .= "To review it please log into your control panel\n\n";
			$the_message .= $review_url."\n\n";
			$the_message .= "Thanks\n";

			// Subject line and from address
			$the_subject = $this->EE->config->item('site_name').': An entry has been submitted for approval';
			$the_from = $this->EE->config->item('webmaster_email');
			
			// Send the email to all members of the selected group
			foreach ($notifications->result_array() as $row)
			{
				$this->EE->email->initialize();
				$this->EE->email->from($the_from);
				$this->EE->email->to($row['email']); 
				$this->EE->email->subject($the_subject);
				$this->EE->email->message(entities_to_ascii(str_replace("[MEM_NAME]", $row['screen_name'] , $the_message)));
				$this->EE->email->Send();
				
				// Logging
				$this->action_logger->add_to_log("ep_status_transition: _notify_users(): Notification email sent to : ".$row['email']);
			}
		}
		else
		{
			// Logging
			$this->action_logger->add_to_log("ep_status_transition: _notify_users(): No member group found for channel: ".$channel_id);
		}
	}



	function _process_draft_button_input($entry_meta, $entry_data=NULL, $btn_name, $btn_value)
	{
		$this->EE->load->model('ep_entry_draft');

		@list($status,$db_operation) = explode('|',$btn_value);

		switch($db_operation)
		{
			case 'create':
			$this->action_logger->add_to_log("ep_status_transition: _process_draft_button_input(), Desc: ENTRY ID: ". $entry_data['entry_id']. " | CASE: create | STATUS:".$status);
			$this->_create_update_draft($entry_meta, $entry_data, $status, 'create', $btn_name);
			break;

			case 'delete':
			$this->action_logger->add_to_log("ep_status_transition: _process_draft_button_input(), Desc: ENTRY ID: ". $entry_data['entry_id']. " | CASE: delete | STATUS:".$status);
			$this->EE->ep_entry_draft->delete(array( 'entry_id' => $entry_data['entry_id']));
			$this->EE->session->set_flashdata('message_success', "Deleted draft for entry {$entry_meta['title']} "); 
			$this->EE->functions->redirect(BASE.AMP.$this->edit_view_url);  
			die();
			break;
 
			case 'update':
			$this->action_logger->add_to_log("ep_status_transition: _process_draft_button_input(), Desc: ENTRY ID: ". $entry_data['entry_id']. " | CASE: update | STATUS:".$status);
			$this->_create_update_draft($entry_meta, $entry_data, $status, 'update', $btn_name);
			break; 
			
			case 'default':
			show_error("Unknown db operation, doing nothing.." . __FILE__ . ", line:" . __LINE__);
		}
	}



	function _create_or_update_entry($entry_meta, $entry_data, $btn_value=null)
	{
		$this->EE->load->library('api');
		$this->EE->api->instantiate('channel_entries');

		if ($btn_value)
		{
			$status = $btn_value;
			$entry_data['revision_post']['status'] = $btn_value;
		}

		if (isset($entry_data['entry_id']) && !empty($entry_data['entry_id']))
		{
			$success = @$this->EE->api_channel_entries->update_entry((INT) $entry_data['entry_id'],$entry_data['revision_post']);
		}
		else
		{
			$success = @$this->EE->api_channel_entries->submit_new_entry($entry_data['channel_id'], $entry_data['revision_post']);
		}

		if (!$success)
		{
			$errors = $this->EE->api_channel_entries->errors;
			if (!isset($_REQUEST['bwf_ajax_new_entry'])) show_error('An Error Occurred Updating the Entry: <pre>' . var_export( $errors, true ) . '</pre>');
		}		
		
		// Check to see if this is a preview or a normal save (required for 2.2 beta release)
		if (!$this->EE->input->is_ajax_request())
		{
			$this->EE->session->set_flashdata('message_success', "Live entry '{$entry_meta['title']}' has been replaced with its draft version"); 
			$this->EE->functions->redirect(BASE.AMP.$this->edit_view_url);
		}
	}



	function _create_update_draft($entry_meta, $entry_data, $status, $create_or_update, $btn_name)
	{
		$this->action_logger->add_to_log("ep_status_transition: _create_update_draft(), Desc: ENTRY ID: ". $entry_data['entry_id']. " CASE: ".$create_or_update. " BUTTON NAME: ".$btn_name);

		$data = array_merge($entry_meta,$entry_data);
		
		#echo "Raw data";
		#var_dump($data);

		// Flatten data if necessary
		$data = $this->_flatten_data($data);

		// Standardise data if necessary
		$data = $this->_standardise_data($data);
		
		#echo "Normalised data";
		#var_dump($data);
		#die();

		// Does a draft already exist for this entry?
		// We now need to find all the Relationship fields and create new entries in the relationship table and
		// If we have any cached data - share this with the model
		$this->EE->load->model('ep_entry_draft');
		$this->EE->load->model('ep_rels');
		$this->EE->ep_rels->has_draft = $this->EE->ep_entry_draft->get_by_entry_id($data['entry_id']);

		if(isset($this->EE->session->cache['ep_better_workflow']['rel_fields']))
		{
			$this->EE->ep_rels->rel_fields = $this->EE->session->cache['ep_better_workflow']['rel_fields'];
		}

		$data = $this->EE->ep_rels->build_relationships($data);

		if ($status)
		{
			$data['status'] = $status;
			if (isset($data['revision_post']['status'])) $data['revision_post']['status'] = $status;
		}

		$data['draft_data'] = serialize($data);

		switch($create_or_update)
		{ 
			case 'create':
			$this->EE->ep_entry_draft->create($data);
			break;
			
			case 'update':
			$this->EE->ep_entry_draft->update(array('entry_id' => $data['entry_id']), $data);
			break;
		}
		
		// Check to see if this is a preview or a normal save (required for 2.2 beta release)
		if (!$this->EE->input->is_ajax_request())
		{
			$this->EE->session->set_flashdata('message_success', "Draft has been {$create_or_update}d for entry {$entry_meta['title']} ");
			
			// Redirect according to process
			if(preg_match('/^epBwfDraft_revert_to_draft/',$btn_name))
			{
				// If we are reverting to a draft - redirect the user back to the publish form
				$this->EE->functions->redirect(BASE.AMP.'C=content_publish'.AMP.'M=entry_form'.AMP.'channel_id='.$data['channel_id'].AMP.'entry_id='.$data['entry_id']);
			}
			else
			{
				// Otherwise send them back to the edit list
				$this->EE->functions->redirect(BASE.AMP.$this->edit_view_url);
				
				// For the future, we could send them to the publish view page with a preview a la Live Look?
				//$this->EE->functions->redirect(BASE.AMP.'C=content_publish'.AMP.'M=view_entry'.AMP.'channel_id='.$data['channel_id'].AMP.'entry_id='.$data['entry_id']);
			}
		}
		die(); //interrupt the EE execution flow in order to prevent the live entry from being updated    
	}



	private function _flatten_data($data)
	{
		$r = array();
		foreach ($data as $key => $value)
		{
			// We need to consolidate the values in the nested Revision Post array
			if($key == 'revision_post' && is_array($value))
			{
				$r = $this->_consolidate_data($r, $value);
			}
			else
			{
				if(strrpos($key, "date") === false)
				{
					$r[$key] = $value;
				}
				else
				{
					$r[$key] = (INT)$value;
				}
			}
		}
		return $r;
	}



	private function _consolidate_data($arr, $data)
	{
		foreach ($data as $key => $value)
		{
			// Don't replace any date information with what is nested in the revision_post array
			// Also, don't include the second nested revision_post
			if(strrpos($key, "date") === false)
			{
				if($key != 'revision_post') $arr[$key] = $value;
			}
		}
		return $arr;
	}



	/**
	* Private function to standardise the way the file data is stored in our draft data in response to the file manager changes implemented in v2.1.5. 
	*
	* return updated data array
	*/
	private function _standardise_data($data)
	{
		// Empty array
		$arr = array();
		$dir_fields = array();

		foreach ($data as $key => $value)
		{
			// Do we have a directory associated with this input
			if(isset($data[$key.'_directory']))
			{
				array_push($dir_fields, $key.'_directory');
				$directory = $data[$key.'_directory'];
				$value = '{filedir_'.$directory.'}'.$value;
			}

			// If this field is in the dir_fields array, we don't want it
			if(!in_array($key, $dir_fields))
			{
				$arr[$key] = $value;
			}
		}
		return $arr;
	}



}
