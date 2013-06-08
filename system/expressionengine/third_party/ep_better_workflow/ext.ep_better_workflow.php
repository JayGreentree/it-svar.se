<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


require_once reduce_double_slashes(PATH_THIRD.'ep_better_workflow/config.php');


/**
 * EP Better Workflow
 * ----------------------------------------------------------------------------------------------
 * Enables the assignment of 'editor' and 'publisher' roles to member groups
 * Enables the creation of both 'entries' and 'drafts'
 *
 * Editors:	Can create, modify and preview new entries. Then submit these for approval.
 * Publishers:	Can create, modify and preview new entries as well as publishing these live
 *
 * Editors:	Can create, modify and preview draft versions of live entries. Modification to these versions will not effect the live entry.
 * Publishers:	Can create, modify and preview draft versions of live entries as well as publishing these live.
 *
 *
 * Third Party Compatibility modules
 * ----------------------------------------------------------------------------------------------
 * See documentation for more information: betterworkflow.electricputty.co.uk/documentation.html
 *
 * ----------------------------------------------------------------------------------------------
 * @package	EE2 
 * @subpackage	ThirdParty
 * @authors	Andrea Fiore / Malcolm Elsworth / Rob Hodges / Andy Lulham
 * @link	http://electricputty.co.uk 
 * @copyright	Copyright (c) 2012 Electric Putty Ltd.
 *
 */


class Ep_better_workflow_ext {


	var $name			= BWF_NAME;
	var $version			= BWF_VER;
	var $description		= 'Enables the assignment of editor and publisher roles to member groups. Enables the creation of drafts versions of live entries which can be modified independently.';
	var $settings_exist		= 'y';
	var $docs_url			= 'http://betterworkflow.electricputty.co.uk/';

	var $settings			= array();
	var $js_libs  			= array('better-workflow.js','status-transition.js','buttons-ui.js','preview.js','ajax-save-callbacks.js');
					
	var $bwf_statuses		= array('closed','draft','submitted','open');
	var $bwf_roles			= array('Unassigned','Editor','Publisher');
	
	// Create a static class for settings. Thanks again to Mark Croxton @croxton
	static protected $config 	= array();
	
	// Global flag to determine whether we are dealing with an EE 'entry' or a BWF 'draft'
	var $is_draft 			= false;
	var $is_preview			= false;
	var $is_clone			= false;
	
	var $preview_entry_id;
	
	var $session_cookie_name;


	/**
	* Instantiate the Ep_better_workflow_ext class
	*
	*
	*/
	function Ep_better_workflow_ext()
	{
		$this->EE =& get_instance();

		#var_dump($this->settings['bwf_channels']);
		
		// -------------------------------------------
		// Define URL_THIRD_THEMES / PATH_THIRD_THEMES constant for pre version 2.4	
		// -------------------------------------------
		defined('URL_THIRD_THEMES') OR define('URL_THIRD_THEMES', $this->EE->config->item('theme_folder_url').'third_party/');
		defined('PATH_THIRD_THEMES') OR define('PATH_THIRD_THEMES', $this->EE->config->item('theme_folder_path').'third_party/');
		
		// -------------------------------------------
		// Set the package path to keep EE 2.1.5 happy - Thanks to @cwcrawley
		// -------------------------------------------
		$this->EE->load->add_package_path(PATH_THIRD.'ep_better_workflow/');
		
		// -------------------------------------------
		// Load settings
		// -------------------------------------------
		$this->_load_settings();

		// -------------------------------------------
		// Load the libraries
		// -------------------------------------------
		require_once reduce_double_slashes(PATH_THIRD . '/ep_better_workflow/libraries/ep_bwf_logger.php');
		require_once reduce_double_slashes(PATH_THIRD . '/ep_better_workflow/libraries/ep_status_transition.php');
		require_once reduce_double_slashes(PATH_THIRD . '/ep_better_workflow/libraries/ep_bwf_ajax.php');
		require_once reduce_double_slashes(PATH_THIRD . '/ep_better_workflow/libraries/ep_draft_utils.php');
		require_once reduce_double_slashes(PATH_THIRD . '/ep_better_workflow/libraries/ep_dates.php');
		require_once reduce_double_slashes(PATH_THIRD . '/ep_better_workflow/libraries/ep_relationships.php');
		require_once reduce_double_slashes(PATH_THIRD . '/ep_better_workflow/libraries/ep_zenbu.php');
		require_once reduce_double_slashes(PATH_THIRD . '/ep_better_workflow/libraries/ep_files.php');
		require_once reduce_double_slashes(PATH_THIRD . '/ep_better_workflow/libraries/ep_third_party_fieldtypes.php');
		
		// -------------------------------------------
		// Make sure we get the correct session id - this was one *tricky* bug to diagnose
		// -------------------------------------------
		$this->session_cookie_name = ($this->EE->config->item('cookie_prefix') != '') ? $this->EE->config->item('cookie_prefix').'_sessionid' : 'exp_sessionid';

		// -------------------------------------------
		// AJAX method to return Page URI
		// -------------------------------------------
		if (isset($_REQUEST['ajax_structure_get_entry_url']))
		{
			$this->_structure_get_entry_url();
		}

		// -------------------------------------------
		// AJAX method to set auth token for preview
		// This is necessary to allow cross-domain previewing in MSM
		// -------------------------------------------
		if (isset($_REQUEST['ajax_set_auth_token']))
		{
			$this->_set_auth_token();
		}

		// -------------------------------------------
		// AJAX method to entry IDs for edit view
		// -------------------------------------------
		if ($this->_is_cp_content_edit_ajax_call()) 
		{
			$this->_cp_content_edit_ajax_response();
		}

		// -------------------------------------------
		// Instantiate libraries
		// -------------------------------------------
		if (isset($this->EE->session))
		{
			$this->ep_draft_utils 			= new Ep_draft_utils();
			$this->status_transition		= new Ep_status_transition($this->EE->session->userdata['group_id'],$this->settings);
			$this->action_logger			= new Ep_workflow_logger($this->settings['advanced']['log_events']);
			$this->ep_relationships 		= new Ep_relationships();
			$this->ep_dates				= new Ep_dates();
			$this->ep_zenbu				= new Ep_zenbu($this->settings);
			$this->ep_files				= new Ep_files($this->settings);
			$this->ep_third_party_fieldtypes	= new Ep_third_party_fieldtypes($this->settings);
		}

		// -------------------------------------------
		//  Prepare Cache
		// -------------------------------------------
		if (! isset($this->EE->session->cache['ep_better_workflow']))
		{
			$this->EE->session->cache['ep_better_workflow'] = array();
		}
		$this->cache =& $this->EE->session->cache['ep_better_workflow'];

		// -------------------------------------------
		//  Are we dealing with a clone (MX Cloner)
		// -------------------------------------------		
		$this->is_clone = ($this->EE->input->get('clone') == 'y') ? true : false;

		// -------------------------------------------
		//  Are we dealing with a preview
		// -------------------------------------------	
		$this->is_preview = ($this->EE->input->get('bwf_dp') != '') ? true : false;
		$this->preview_entry_id	= ($this->EE->input->get('bwf_entry_id') != '') ? $this->EE->input->get('bwf_entry_id') : 0;
	}



	/**
	* Standard settings_form method
	* Check to see if this is called via an AJAX request
	* 	If AJAX it can:
	*		- calls the process to chect the status of existing entries
	*		- deletes the log file
	*		- returns the log file for display in a modal
	* If not is returns the standard settigs form
	*
	*/
	function settings_form($current)
	{
		// If it IS an AJAX request process the required action
		if (AJAX_REQUEST)
		{ 
			if (isset($_REQUEST['ajax_check_existing_channel_entries']))
			{
				$out = array('response' => 'ok');
				$this->EE->load->model('ep_statuses');
				$statuses = $this->EE->ep_statuses->check_channel_entries($_REQUEST['channel_id'], $this->bwf_statuses);
				if ($statuses->num_rows() > 0)
				{
					$s = array();
					foreach ($statuses->result_array() as $row)
					{
						$s[] = array('status' => $row['status']);
					}
					$out['response'] = 'fail';
					$out['statuses'] = $s;
				}
				$this->_return_json($out);
			}
			
			// If $_REQUEST is a delete action
			if (isset($_REQUEST['ajax_delete_log_file']))
			{
				// Delete the log file
				$this->_delete_log_file();
			}
			
			// If $_REQUEST is a view action
			if (isset($_REQUEST['ajax_fetch_log_file']))
			{
				// Fetch the log file
				$this->_fetch_log_file();
			}
		}
		else
		{
			return $this->display_settings();
		}
	}



	/**
	* Non AJAX settings request - simply display the settings from
	*
	*
	*/
	function display_settings()
	{
		$this->EE->load->helper('form');
		$this->EE->load->library('table');
		
		$this->EE->load->model('ep_settings');
		$this->EE->ep_settings->settings = $this->settings;
		$this->EE->ep_settings->name_name = $this->name;
		$this->EE->ep_settings->site_id = $this->EE->config->item('site_id');
		$this->EE->ep_settings->roles = $this->bwf_roles;
		
		$vars = $this->EE->ep_settings->prep_settings();
		
		// Load the settings view css / javascript
		$this->EE->cp->add_to_foot('<link media="screen, projection" rel="stylesheet" type="text/css" href="'.$this->_theme_url().'stylesheets/cp.css?v='.BWF_VER.'" />');
		$this->EE->cp->add_to_foot('<script type="text/javascript" src="'.$this->_theme_url().'javascript/cp.js?v='.BWF_VER.'"></script>');
		
		// If logging is on, show control buttons for clearing and viewing log
		if ($this->EE->ep_settings->settings['advanced']['log_events'] == 'yes') {
		
			$this->EE->cp->set_right_nav(
				array(
					'View log' => 'ajax_fetch_log_file',
					'Clear log' => 'ajax_delete_log_file'
				)
			);
		};
		
		return $this->EE->load->view('index', $vars, TRUE);
	}	



	function save_settings()
	{
		$this->EE->load->model('ep_settings');
		$this->EE->ep_settings->settings = $this->settings;
		$this->EE->ep_settings->class_name = __CLASS__;
		$this->EE->ep_settings->site_id = $this->EE->config->item('site_id');
		$this->EE->ep_settings->save_settings($_POST);
	}



	// -----------------------------------------------------------------------------------------------------------
	// Many thanks to @croxton for rewriting the below to utilise a static class
	// -----------------------------------------------------------------------------------------------------------
	function _load_settings()
	{
		// Set the site ID
		$site_id = $this->EE->config->item('site_id');
		
		// Load the settings model
		$this->EE->load->model('ep_settings');
		
		if (! isset(self::$config['ep_better_workflow_settings_'.$site_id]))
		{
			$this->EE->ep_settings->class_name = __CLASS__;
			$this->EE->ep_settings->site_id = $site_id;
			
			self::$config['ep_better_workflow_settings_'.$site_id] = $this->EE->ep_settings->get_settings();
		}
		if ( ! empty(self::$config['ep_better_workflow_settings_'.$site_id])) 
		{
			$this->settings = unserialize(self::$config['ep_better_workflow_settings_'.$site_id]);
		}

		// Add the site_id so we're MSM compatible
		$this->settings['site_id'] = $site_id;
		
		// Add the status colours to the settings (Some day we may make these editable)
		$this->settings['status_color_closed'] = '990000';
		$this->settings['status_color_draft'] = 'B59A42';
		$this->settings['status_color_submitted'] = '3E6C89';
		$this->settings['status_color_open'] = '009933';
		
		// Set the default status group
		$this->settings['bwf_default_status'] = 'draft';
		
		// Loop through all the advanced settings and if they are not set assign the default value
		foreach($this->EE->ep_settings->advanced_opts as $opt)
		{
			if(!isset($this->settings['advanced'][$opt[0]])) $this->settings['advanced'][$opt[0]] = $opt[3];
		}
	}



	// -----------------------------------------------------------------------------------------------------------
	// HOOK IMPLEMENTATIONS
	// -----------------------------------------------------------------------------------------------------------

	/***
	* Implements session start
	*
	* This has to be here in order for the extension to be executed in all the different sections of the 
	* EE admin area.
	*/
	function on_sessions_start($session)
	{
		// Has anyone else used this hook and made any changes to the session object?
		$session = ($this->EE->extensions->last_call != '' ? $this->EE->extensions->last_call : $session);
		
		// As this hook seems to be called without instantiating the extension...
		if(!property_exists('Ep_better_workflow_ext', 'action_logger')) $this->action_logger = new Ep_workflow_logger($this->settings['advanced']['log_events']);

		// Logging
		$this->action_logger->add_to_log("[BREAK]");
		$this->action_logger->add_to_log("ext.ep_better_workflow: on_sessions_start()");

		// Check to see if we have a record in the status table set against the current session id that we need to switch back
		$status_switching = $this->_switch_entry_status_back();
		$this->action_logger->add_to_log("ext.ep_better_workflow: on_sessions_start(): ".$status_switching);
		
		// Check to see if we need to delete any tokens
		$this->EE->load->model('ep_auth');
		$token_deleting = $this->EE->ep_auth->delete_tokens();
		$this->action_logger->add_to_log("ext.ep_better_workflow: on_sessions_start(): ".$token_deleting);
		
		$this->action_logger->add_to_log("ext.ep_better_workflow: on_sessions_start(): Is this a preview request: ".$this->is_preview);
		
		// IF this is a preview, check to see if the entry we are previewing needs its status switched
		if ($this->is_preview) $this->_switch_status_if_entry_closed();
		
		// Add a global variable to enable templates to do things with this value
		$this->EE->config->_global_vars = array_merge(array('is_bwf_preview' => $this->is_preview), $this->EE->config->_global_vars);
		
		// Now return the session object unchanged 
		return $session;
	}


	/***
	* Hook added in v2.4.0 this is triggered at the end of the template parsing
	* We can use this to revert the entry's status post preview (one of three methods which will try and do this)
	* We only want to know when the main (not embedded) template is complete to we check the $is_embed before triggering our status resetting
	*/
	function on_template_post_parse($html, $is_embed, $site_id)
	{		
		// Logging
		$this->action_logger->add_to_log("ext.ep_better_workflow: on_template_post_parse(): is_embed: ".$is_embed);

		if(!$is_embed)
		{
			$status_switching = $this->_switch_entry_status_back();
			
			// Logging
			$this->action_logger->add_to_log("ext.ep_better_workflow: on_template_post_parse(): ".$status_switching);
		}

		return $html;
	}



	/***
	* Implements EE hook publish_form_channel_preferences
	* 
	* Inject the status transition javascript 
	* when creating a new entry. 
	*/
	function on_publish_form_channel_preferences($prefs)
	{		
		// do nothing if channel does not use workflow OR this is a clone          
		if (!isset($prefs['channel_id']) or !$this->_channel_has_workflow($prefs['channel_id']) or $this->is_clone ) return $prefs ;

		if (!isset($_REQUEST['entry_id']) or (isset($_REQUEST['entry_id']) && empty($_REQUEST['entry_id'])))
		{
			$snippet = $this->status_transition->instantiate_js_class(array('channel_id' => $prefs['channel_id']));
			$this->_append_stylesheet();
			$this->_append_javascripts($snippet);
		}
		return $prefs;
	}



	/***
	* Implements EE Hook publish_form_data
	* Replaces entry data with draft data and injects status transition javascript.
	*/ 
	function on_publish_form_entry_data( $result=array() )
	{
		// Has anyone else used this hook and made any changes to the results array?
		$result = ($this->EE->extensions->last_call != '' ? $this->EE->extensions->last_call : $result);
	
		//echo("Original entry data");
		//var_dump($result);

		// Logging
		$this->action_logger->add_to_log("ext.ep_better_workflow: on_publish_form_entry_data(): Start - Check to see if this channel uses BWF");
		
		if (isset($result['channel_id']))
		{
			if ($this->_channel_has_workflow($result['channel_id']) && !$this->is_clone)
			{
				// Build the JavaScript settings and inject into page
				// This function also assigns a value to the 'draft_data' object
				$snippet = $this->status_transition->instantiate_js_class($result);
				$this->_append_stylesheet();
				$this->_append_javascripts($snippet);
		
				// Replace 'entry' data with 'draft' data when a draft exists
				$result = ( is_object($this->status_transition->draft_data) ) ? $this->ep_draft_utils->load_draft($this->status_transition->draft_data, $result, $this->is_draft) : $result;
				
				// Is this a BWF draft (e.g. not an EE entry) that we're loading into the CP?
				// If so set a session variable to everyone knows it
				if($this->is_draft) 
				{
					// Try and clean up and messy file field data
					$result = $this->ep_files->clean_up_native_file_field_data($result);
					
					$this->cache['is_draft'] = true;
					$this->action_logger->add_to_log("ext.ep_better_workflow: on_publish_form_entry_data(): Load draft data into publish form for entry: ". $result['entry_id']);
				}
			}
		}
		//echo("EP draft data");
		//var_dump($result);
		
		return $result;
	}



	/***
	* Implements EE hook 'entry_submission_ready'
	* Process entry status transitions.
	*/
	function on_entry_submission_start($channel_id=0, $autosave=FALSE)
	{
		// Logging
		$this->action_logger->add_to_log("[BREAK]");
		$this->action_logger->add_to_log("ext.ep_better_workflow: on_entry_submission_start()");
	
		if( ! $channel_id || $autosave === TRUE ) return;
	
		// If this channel is governed by workflow
		if ($this->_channel_has_workflow($channel_id))
		{
			
			// -----------------------------------------------------------------------------------------------------------
			// We need to store the *complete* revision post array, because EE2.2 strips out third-party data 
			// -----------------------------------------------------------------------------------------------------------
			if(isset($this->EE->api_channel_entries->data['revision_post']))
			{
				$this->cache['revision_post'] = $this->EE->api_channel_entries->data['revision_post'];
			}

			// -----------------------------------------------------------------------------------------------------------
			// And we are creating or updating a draft
			// -----------------------------------------------------------------------------------------------------------
			if($this->_creating_or_updating_draft($this->EE->api_channel_entries->data))
			{
				
				// Logging
				$this->action_logger->add_to_log("ext.ep_better_workflow: on_entry_submission_start() CONDITION: Creating or updating a draft");
		

				// -----------------------------------------------------------------------------------------------------------
				// Use the relationships library and update any relatonship fields found in the data array
				// -----------------------------------------------------------------------------------------------------------
				$this->EE->api_channel_entries->data = $this->ep_relationships->update_rel_data($this->EE->api_channel_entries->data);


				// -----------------------------------------------------------------------------------------------------------
				// Save the data we extracted from the data array, we'll want to re-instate this later
				// -----------------------------------------------------------------------------------------------------------
				$this->cache['rel_data'] = $this->ep_relationships->rel_data;
				$this->cache['rel_fields'] = $this->ep_relationships->rel_fields;


				// Logging
				$c = count($this->EE->api_channel_entries->data);
				$this->action_logger->add_to_log("ext.ep_better_workflow: on_entry_submission_start(): There are now ".$c." items in the data array");
			}
			else
			{
				// -----------------------------------------------------------------------------------------------------------
				// Is this an ajax request from the preview engine to create a new entry?
				// -----------------------------------------------------------------------------------------------------------
				if (isset($_REQUEST['bwf_ajax_new_entry']))
				{
					// -----------------------------------------------------------------------------------------------------------
					// Use the relationships library and remove any relatonship fields found in the data array
					// Failure to do this will result in bad records being created in the DB
					// -----------------------------------------------------------------------------------------------------------
					$this->EE->api_channel_entries->data = $this->ep_relationships->update_rel_data($this->EE->api_channel_entries->data);

					// -----------------------------------------------------------------------------------------------------------
					// Save the data we extracted from the data array, we'll want to re-instate this later
					// -----------------------------------------------------------------------------------------------------------
					$this->cache['rel_data'] = $this->ep_relationships->rel_data;
					$this->cache['rel_fields'] = $this->ep_relationships->rel_fields;

					// Logging
					$this->action_logger->add_to_log("ext.ep_better_workflow: on_entry_submission_start() CONDITION: This is an AJAX save to create a new entry so we're removing relationship data");
				}

				// Logging
				$this->action_logger->add_to_log("ext.ep_better_workflow: on_entry_submission_start() CONDITION: Just passing through - this is a standard EE entry save");
			}
		}
	}



	/***
	* Implements EE hook 'entry_submission_ready'
	* Process entry status transitions.
	*/
	function on_entry_submission_ready($entry_meta, $entry_data)
	{
		
		// Check to see if we have a valid entry ID to use for logging - If this is an API request this key will not exist
		$this_entry_id = (isset($entry_data['entry_id'])) ? $entry_data['entry_id'] : 0;

		// Logging
		$this->action_logger->add_to_log("[BREAK]");
		$this->action_logger->add_to_log("ext.ep_better_workflow: on_entry_submission_ready(): entry_id: ". $this_entry_id);

		if ($this->_channel_has_workflow($entry_meta['channel_id']))
		{
			// -----------------------------------------------------------------------------------------------------------
			// Determine the current draft action - creating, updating, discarding or publishing?
			// -----------------------------------------------------------------------------------------------------------
			$draft_action = $this->_get_draft_action(array_merge($entry_meta, $entry_data));


			// -----------------------------------------------------------------------------------------------------------
			// And we are creating or updating a draft?
			// -----------------------------------------------------------------------------------------------------------
			if($this->_creating_or_updating_draft(array_merge($entry_meta, $entry_data)))
			{
				
				// Logging
				$this->action_logger->add_to_log("ext.ep_better_workflow: on_entry_submission_ready() DRAFT ACTION: '{$draft_action}' a draft");


				// -----------------------------------------------------------------------------------------------------------
				// If we have a cached version of the revision_post data, re-instate it here
				// -----------------------------------------------------------------------------------------------------------
				if(isset($this->cache['revision_post']))
				{
					$entry_data['revision_post'] = $this->cache['revision_post'];
				}


				// -----------------------------------------------------------------------------------------------------------
				// Use the relationships library and re-instate the relationships we removed before
				// -----------------------------------------------------------------------------------------------------------
				if (isset($this->cache['rel_data']) && count($this->cache['rel_data'])>0)
				{
					$entry_data = $this->ep_relationships->reinstate_relationships_to_data($entry_data, $this->cache['rel_data']);

					// Logging
					$this->action_logger->add_to_log("ext.ep_better_workflow: on_entry_submission_ready(): Relationships reinstated");
				}


				// -----------------------------------------------------------------------------------------------------------
				// Send the data to the third party field types and let them create records for the draft
				// -----------------------------------------------------------------------------------------------------------
				$entry_data = $this->ep_third_party_fieldtypes->process_draft_data($entry_data, $draft_action);


				// -----------------------------------------------------------------------------------------------------------
				// Check for dates amongst the custom fields - they may need to be converted to UNIX format
				// -----------------------------------------------------------------------------------------------------------
				$entry_data = $this->ep_dates->dates_in_data_to_unix($entry_data);


				// -----------------------------------------------------------------------------------------------------------
				// Safecracker file types don't appear in the revision post array, so the data for these fields need to be copied over
				// Sometimes native file field store a referece to an empty direcotry in the revision post, so we need to remove this before we save
				// -----------------------------------------------------------------------------------------------------------
				$entry_data = $this->ep_files->copy_safecracker_file_data_to_revision_post($entry_data);
				$entry_data = $this->ep_files->clean_up_native_file_field_data($entry_data);
			}
			else
			{
				// Logging
				$this->action_logger->add_to_log("ext.ep_better_workflow: on_entry_submission_ready() CONDITION: Just passing through - this is a standard EE entry save");
			}


			// -----------------------------------------------------------------------------------------------------------
			// Is this an ajax request from the preview engine to create a new entry?
			// -----------------------------------------------------------------------------------------------------------
			if (isset($_REQUEST['bwf_ajax_new_entry']))
			{
				// Logging
				$this->action_logger->add_to_log("ext.ep_better_workflow: on_entry_submission_ready(): Create new entry via AJAX");

				// -----------------------------------------------------------------------------------------------------------
				// If we have a cached version of the revision_post data, re-instate it here
				// -----------------------------------------------------------------------------------------------------------
				if(isset($this->cache['revision_post']))
				{
					$entry_data['revision_post'] = $this->cache['revision_post'];
				}

				// -----------------------------------------------------------------------------------------------------------
				// Use the relationships library and re-instate the relationships we removed before
				// -----------------------------------------------------------------------------------------------------------
				if (isset($this->cache['rel_data']) && count($this->cache['rel_data'])>0)
				{
					$entry_data = $this->ep_relationships->reinstate_relationships_to_data($entry_data, $this->cache['rel_data']);

					// Logging
					$this->action_logger->add_to_log("ext.ep_better_workflow: on_entry_submission_ready(): Relationships reinstated");
				}

				// -----------------------------------------------------------------------------------------------------------
				// Instantiate bwf_ajax object and create new entry
				// -----------------------------------------------------------------------------------------------------------
				$this->ajax_request = new Ep_bwf_ajax($this->settings);
				$this->ajax_request->_create_new_entry($entry_meta, $entry_data, 'draft'); //dies 
			}


			// -----------------------------------------------------------------------------------------------------------
			// Are we converting a draft to an entry (publishing) or are we discarding a draft
			// -----------------------------------------------------------------------------------------------------------
			if($draft_action == 'publish' || $draft_action == 'discard')
			{
				// Remove the is_draft flag, we don't need this if we're publishing or discarding
				unset($this->cache['is_draft']);
				
				// Logging
				$this->action_logger->add_to_log("ext.ep_better_workflow: on_entry_submission_ready(): We are {$draft_action}ing this draft so unset the is_draft flag");
				
				// -----------------------------------------------------------------------------------------------------------
				// Tell all third party field types to update their data accordingly
				// -----------------------------------------------------------------------------------------------------------
				$entry_data = $this->ep_third_party_fieldtypes->process_draft_data($entry_data, $draft_action);
			}


			// -----------------------------------------------------------------------------------------------------------
			// Now process the submissions (Create or update)
			// -----------------------------------------------------------------------------------------------------------
			$this->cache['new_status'] = $this->status_transition->process_button_input($entry_meta, $entry_data);
			
			// Logging
			$this->action_logger->add_to_log("ext.ep_better_workflow: on_entry_submission_ready() DATA TEST: New status ".$this->cache['new_status']);
		}
	}



	/***
	* Implements EE hook 'entry_submission_end'
	* Over-rides status settings on submit
	*/
	function on_entry_submission_end($entry_id, $entry_meta, $entry_data)
	{	
		// Logging
		$this->action_logger->add_to_log("[BREAK]");
		$this->action_logger->add_to_log("ext.ep_better_workflow: on_entry_submission_end(): Entry: ". $entry_id);

		$status = null;

		// -----------------------------------------------------------------------------------------------------------
		// If we are creating or updating an Entry...
		// -----------------------------------------------------------------------------------------------------------
		foreach($_POST as $k => $v) 
		{
			if (preg_match('/^epBwfEntry/',$k))
			{
				$status = array_pop(explode('|',$v));
				break;
			}
		}
		
		// Logging
		$this->action_logger->add_to_log("ext.ep_better_workflow: on_entry_submission_end(): Status: ". $status);

		// -----------------------------------------------------------------------------------------------------------
		// If we are turning a draft into an entry
		// -----------------------------------------------------------------------------------------------------------
		if (isset($this->cache['new_status']))
		{
			$status = $this->cache['new_status'];
		}

		if ($status && $this->_channel_has_workflow($entry_meta['channel_id']))
		{
			// Logging
			$this->action_logger->add_to_log("ext.ep_better_workflow: on_entry_submission_end(): We're updating the entries status to Status: ". $status);
			
			$this->EE->db->where("entry_id = $entry_id"); 
			$this->EE->db->update('channel_titles',array('status' => $status));
		}
		
		// Logging
		$this->action_logger->add_to_log("[BREAK]");

		// -----------------------------------------------------------------------------------------------------------
		// Quick check to see if we need to try and send the nitification emails again
		// -----------------------------------------------------------------------------------------------------------
		if (isset($this->cache['notify_users']))
		{			
			$this->status_transition->_notify_users($entry_meta['channel_id'], $entry_id, $entry_meta['title']);
					
			// Logging
			$this->action_logger->add_to_log("ext.ep_better_workflow: on_entry_submission_end(): Calling _notify_users method again - we should now have a valid entry ID");
		}
	}



	/**
	* Implements hook 'channel_entries_query_result'
	*
	* Opportunity to modify the channel entries query result array before the parsing loop starts
	* One of these is called for each channel_entries tag in the template
	*/
	function on_channel_entries_query_result($channel, $res)
	{
		// Logging
		$this->action_logger->add_to_log("[BREAK]");
		$this->action_logger->add_to_log("ext.ep_better_workflow: on_channel_entries_query_result(): Start - the template has begun a new channel entries tag");

		// Reset the cached flags in case this isn't the first time we've hit this functions
		$this->cache['is_preview'] = false;
		$this->cache['preview_entry_id'] = 0;

		// Logging
		$this->action_logger->add_to_log("ext.ep_better_workflow: on_channel_entries_query_result(): Is preview: ".$this->is_preview);

		// Check to see if the required params are in the Querystring
		if ($this->is_preview)
		{
			// Logging
			$this->action_logger->add_to_log("ext.ep_better_workflow: on_channel_entries_query_result(): This is a preview request - now check to see if it contains a valid auth token");

			// Only contine if we have a valid token
			if($this->_request_has_valid_auth_token())
			{
				// We might have lots of entries in this results array
				// We need to loop through them and see if the one we are trying to preview is present
				if(is_array($res))
				{
					// Logging
					$this->action_logger->add_to_log("ext.ep_better_workflow: on_channel_entries_query_result(): This is a preview so loop through the results array and see if our preview_entry_id [{$this->preview_entry_id}] is present");

					foreach($res as $entry)
					{
						if($entry['entry_id'] == $this->preview_entry_id)
						{
							// Logging
							$this->action_logger->add_to_log("ext.ep_better_workflow: on_channel_entries_query_result(): We have found the entry we're trying to preview - entry_id[{$this->preview_entry_id}] - now check to see if it has any draft data");

							// -----------------------------------------------------------------------------------------------------------
							// Load the entry draft model and see if this entry has a draft, and if so is this a preview request
							// -----------------------------------------------------------------------------------------------------------
							$this->EE->load->model('ep_entry_draft');
							$draft = $this->EE->ep_entry_draft->get_by_entry_id($entry['entry_id']);
							if ($draft)
							{
								// Logging
								$this->action_logger->add_to_log("ext.ep_better_workflow: on_channel_entries_query_result(): We have found some draft data for entry_id[{$this->preview_entry_id}]");
								$this->action_logger->add_to_log("ext.ep_better_workflow: on_channel_entries_query_result(): Set the is_preview and is_preview session vars to 'true' and the preview_entry_id to '{$this->preview_entry_id}'");

								$this->cache['is_draft'] = true;
								$this->cache['is_preview'] = true;
								$this->cache['preview_entry_id'] = $this->preview_entry_id;
								$this->cache['preview_entry_data'] = $draft;
								
								// Add a global variable to enable templates to do things with this value
								$this->EE->config->_global_vars = array_merge(array('is_bwf_draft' => true), $this->EE->config->_global_vars);
							}
						}
					}
				}
			}
			else
			{
				$this->action_logger->add_to_log("ext.ep_better_workflow: on_channel_entries_query_result(): Validation failed");
			}
		}
		return $res;
	}



	/**
	* Implements EE hook 'channel_entries_row'
	*
	* Used to preview entries.
	* One of these is called for each entry returned by the channel_entries tag
	*/
	function on_channel_entries_row($channel, $row)
	{
		// Logging
		$this->action_logger->add_to_log("ext.ep_better_workflow: on_channel_entries_row(): Start");
		$this->action_logger->add_to_log("ext.ep_better_workflow: on_channel_entries_row(): Entry ID '{$row['entry_id']}' - preview ID '{$this->cache['preview_entry_id']}'");

		// -----------------------------------------------------------------------------------------------------------
		// If we are previewing load the draft data
		// -----------------------------------------------------------------------------------------------------------
		if((isset($this->cache['is_preview']) && $this->cache['is_preview']) && $row['entry_id'] == $this->cache['preview_entry_id'])
		{
			// Logging
			$this->action_logger->add_to_log("ext.ep_better_workflow: on_channel_entries_row(): Yes, this is the entry we want to preview so return its draft data");

			$row = $this->ep_draft_utils->load_draft($this->cache['preview_entry_data'], $row, $this->is_draft);

			// Logging
			$this->action_logger->add_to_log("ext.ep_better_workflow: on_channel_entries_row(): Set the is_preview session so everyone else knows what we're doing");

			$this->cache['is_preview'] = true;
			$this->cache['is_draft'] = true;
		}
		else
		{
			// Logging
			$this->action_logger->add_to_log("ext.ep_better_workflow: on_channel_entries_row(): No, this is a normal request so just return the live entry data");
		}

		// Logging
		$this->action_logger->add_to_log("[BREAK]");

		return $row;
	}



	/** 
	 * Implements Zenbu's hooks
	 */
	function on_zenbu_modify_title_display($output, $entry_array, $row)
	{
		return $this->ep_zenbu->modify_title_display($output, $entry_array, $row);
	}

	function on_zenbu_modify_status_display($output, $entry_array, $row, $statuses)
	{
		return $this->ep_zenbu->modify_status_display($output, $entry_array, $row, $statuses);
	}

	function on_zenbu_filter_by_status($channel_id, $status, $rule, $where)
	{
		return $this->ep_zenbu->filter_by_status($channel_id, $status, $rule, $where);
	}



	// -----------------------------------------------------------------------------------------------------------
	// END HOOK IMPLEMENTATIONS
	// -----------------------------------------------------------------------------------------------------------



	/**
	 * Helper test to check if a channel has workflow and if the user has access to the workflow functionality.
	 * return {boolean} $hasWorkFlow
	 */
	function _channel_has_workflow($channel_id)
	{
		if (! isset($this->cache['channel_has_workflow']))
		{
			$this->cache['channel_has_workflow'] = $this->status_transition->has_workflow($channel_id);
		}
		return $this->cache['channel_has_workflow'];
	}



	/**
	 * Helper test to check if we are working on a draft rather than an entry. 
	 * return {boolean} $hasWorkFlow
	 */
	function _creating_or_updating_draft($entry_data)
	{
		foreach($entry_data as $k => $v)
		{
			if( 
				preg_match('/^epBwfDraft_delete_/',$k) ||  
				preg_match('/^epBwfDraft_create_/',$k) || 
				preg_match('/^epBwfDraft_update_/',$k)
				)
			{
				return true;
			}
		}
		return false;
	}



	function _get_draft_action($entry_data)
	{
		foreach($entry_data as $k => $v)
		{
			if(preg_match('/^epBwfDraft_replace_/',$k))
			{
				return 'publish';
			}
			if(preg_match('/^epBwfDraft_delete_/',$k))
			{
				return 'discard';
			}
			if(preg_match('/^epBwfDraft_create_/',$k))
			{
				return 'create';
			}
			if(preg_match('/^epBwfDraft_update_/',$k))
			{
				return 'update';
			}
		}
		return false;
	}



	/*
	 * Implements the cp_js_end hook and uses it to inject workflow 
	 * javascripts and stylesheet to the control panel entry list table.
	 *
	 */
	function on_cp_js_end($data)
	{
		return $this->EE->extensions->last_call . $this->_cp_content_js_snippet();
	}



	function _cp_content_js_snippet()
	{
		$scripts = array('better-workflow.js?'.BWF_VER,'entry-list-observer.js?'.BWF_VER);
		$ajax_endpoint = str_replace('&C=javascript&M=load&file=ext_scripts', '&C=content_edit', $_SERVER['REQUEST_URI']);
		$ee_version = APP_VER;

		foreach($scripts as $script)
		{
			$script_tags[] = <<<EOD
			document.write('\x3Cscript type="text/javascript" src="{$this->_theme_url()}javascript/$script">\x3C/script>');
EOD;
		}

		$script_tags = implode("\n\r", $script_tags);
		return  <<<EOD
		if (/D=cp&C=content_edit/.test(document.location.href)) {
			$script_tags
			jQuery( function ($) {
				$('head').append('<link rel="stylesheet" type="text/css" href="{$this->_theme_url()}stylesheets/bwf.css"  />')
				var ajaxEndPoint = '{$ajax_endpoint}&ep_bwf_getEntryInfo';
				var eeVersion = '{$ee_version}';
				tableObserver = new Bwf.EntryListObserver(ajaxEndPoint,eeVersion);
				tableObserver.observeFilters();
			});
		}
EOD;

	}



	function _is_cp_content_edit()
	{
		return isset($_REQUEST['D']) && $_REQUEST['D'] == 'cp' && isset($_REQUEST['C']) && $_REQUEST['C'] == 'content_edit';
	}



	function _is_cp_content_edit_ajax_call(){
		return ($this->_is_cp_content_edit() && isset($_REQUEST['ep_bwf_getEntryInfo']));
	}



	function _request_has_valid_auth_token()
	{
		$site_id = $this->settings['site_id'];

		// Logging
		$this->action_logger->add_to_log("ext.ep_better_workflow: _check_auth_token(): method called");
		
		// Now we know the request if for a preview, check to see if it contains a valid auth token
		if (!isset(self::$config['is_valid_preview_request_'.$site_id]))
		{
			$token_id	= (isset($_GET['bwf_token_id'])) ? $_GET['bwf_token_id'] : 0;
			$token		= (isset($_GET['bwf_token'])) ? $_GET['bwf_token'] : 0;

			// Logging
			$this->action_logger->add_to_log("ext.ep_better_workflow: _check_auth_token(): Check for valid auth token: Token ID: " . $token_id . " Token: ". $token);

			// -----------------------------------------------------------------------------------------------------------
			// Load the auth model and check that we have a valid auth token in the URL
			// -----------------------------------------------------------------------------------------------------------
			$this->EE->load->model('ep_auth');
			$this->EE->ep_auth->log_events = $this->settings['advanced']['log_events'];
			$this->EE->ep_auth->enable_external_previews = $this->settings['advanced']['enable_external_previews'];
						
			// -----------------------------------------------------------------------------------------------------------
			// Set the config variable so we only have to do this check once
			// -----------------------------------------------------------------------------------------------------------
			self::$config['is_valid_preview_request_'.$site_id] = $this->EE->ep_auth->is_valid_request($token_id, $token);
		}
		
		// Logging
		$this->action_logger->add_to_log("ext.ep_better_workflow: _check_auth_token(): Current value of is_valid_preview_request: " . self::$config['is_valid_preview_request_'.$site_id]);
		
		return self::$config['is_valid_preview_request_'.$site_id];
	}



	/***
	* Implements a way around for previewing non-open entries
	*/
	function _switch_status_if_entry_closed()
	{
		// As this hook seems to be called without instantiating the extension...
		if(!property_exists('Ep_better_workflow_ext', 'action_logger')) $this->action_logger = new Ep_workflow_logger($this->settings['advanced']['log_events']);

		// Logging
		$this->action_logger->add_to_log("ext.ep_better_workflow: _switch_status_if_entry_closed(): Start - check that the current user has permission to access a preview");

		// Only contine if we have a valid token
		if($this->_request_has_valid_auth_token())
		{
			$entry_id = (INT) $_GET['bwf_entry_id'];

			// Logging
			$this->action_logger->add_to_log("ext.ep_better_workflow: _switch_status_if_entry_closed(): Check that requested entry - {$entry_id} - exists and is not in a live status");

			$query = $this->EE->db->get_where('channel_titles',array('entry_id' => $entry_id));
			$entry= array_shift($query->result());

			if ($query->num_rows() > 0 && $entry && $entry->status != 'open' )
			{					
				$this->EE->load->model('ep_statuses');
				$this->EE->ep_statuses->set_entry_status($entry_id);
				
				// Save the entry_id, status and entry date for the entry we are modifying so we can change it back as soon as the preview has rendered
				self::$config['ep_bwf_status_swap_entry_id_'.$this->settings['site_id']] = $entry_id;
				self::$config['ep_bwf_status_swap_entry_status_'.$this->settings['site_id']] = $entry->status;
				self::$config['ep_bwf_status_swap_entry_date_'.$this->settings['site_id']] = $entry->entry_date;

				// Logging
				$this->action_logger->add_to_log("ext.ep_better_workflow: _switch_status_if_entry_closed(): All checks worked out - setting entry_status to an 'as-open' status (was {$entry->status} before) and saving config variables for switch back");
			}
		}
		$this->action_logger->add_to_log("[BREAK]");
	}



	/***
	* Function to re-set the entry's meta data (status / entry date)
	* Called by 'template_post_parse' and 'sessions_start' hooks (belt and braces)
	*/
	function _switch_entry_status_back()
	{
		// Logging
		$this->action_logger->add_to_log("ext.ep_better_workflow: _switch_entry_status_back()");
		
		$site_id = $this->settings['site_id'];
		$status_switching = "No status switching required";
			
		if(isset(self::$config['ep_bwf_status_swap_entry_id_'.$site_id]))
		{
			$ss_entry_id = self::$config['ep_bwf_status_swap_entry_id_'.$site_id];
			$ss_entry_status = self::$config['ep_bwf_status_swap_entry_status_'.$site_id];
			$ss_entry_date = self::$config['ep_bwf_status_swap_entry_date_'.$site_id];

			$this->action_logger->add_to_log("ext.ep_better_workflow: _switch_entry_status_back(): SAVED ENTRY ID: ".$ss_entry_id);
			$this->action_logger->add_to_log("ext.ep_better_workflow: _switch_entry_status_back(): SAVED ENTRY STATUS: ".$ss_entry_status);
			$this->action_logger->add_to_log("ext.ep_better_workflow: _switch_entry_status_back(): SAVED ENTRY DATE: ".$ss_entry_date);

			$this->EE->load->model('ep_statuses');
			$status_switching = $this->EE->ep_statuses->revert_entry_status($ss_entry_id, $ss_entry_status, $ss_entry_date);
				
			// Unset the config variables so we only do this once
			unset(self::$config['ep_bwf_status_swap_entry_id_'.$site_id]);
			unset(self::$config['ep_bwf_status_swap_entry_status_'.$site_id]);
			unset(self::$config['ep_bwf_status_swap_entry_date_'.$site_id]);
		}
		return $status_switching;
	}

	
	/**
	* Get log text file and prep for insertion in 
	* modal window
	*/
	function _fetch_log_file()
	{
		// Get contents of log file and return as a string
		$log_contents = file_get_contents(BWF_LOG_FILE);
		
		// Add BR tags
		$log_contents = nl2br($log_contents);
		
		die($log_contents);
	}
	
	/**
	* Delete log text file
	*/
	function _delete_log_file()
	{		
		$out = array();
		
		// Replace contents of log file with nothing, clearing the log
		$clear_log_file = file_put_contents(BWF_LOG_FILE, "");
		
		// Check the return value equals 0
		if ($clear_log_file === 0) {
			$out = array('response' => 'ok');
		} else {
			$out = array('response' => 'fail');
		}		
		$this->_return_json($out);
	}
	
	/**
	 * Structure compatibility plugin
	 * Returns Page URI for a given entry
	 * Will also return a URL if pages is installed
	 */
	function _structure_get_entry_url()
	{
		$out = array('structure_url' => null); 

		//1. Check if Structure or Pages is enabled 
		$this->EE->db->where('module_name', 'Structure');
		$this->EE->db->or_where('module_name', 'Pages'); 
		if ( count($this->EE->db->get('modules')->result()) > 0 )
		{
			//2. if yes, get the structure url
			$site_pages = $this->EE->config->item('site_pages');
			$site_pages = $site_pages[$this->settings['site_id']];

			if (@isset($site_pages['uris'][$_REQUEST['entry_id']])) 
			{
				$out['structure_url'] = $site_pages['uris'][@$_REQUEST['entry_id']];
			}
		}
		$this->_return_json($out);
	}



	/**
	 * Auth token 
	 * Sets and returns auth token value and ID 
	 * This enables cross domain previewing for MSM installations
	 */
	function _set_auth_token()
	{
		$out = array('token_id' => null, 'token' => null); 
		$this->EE->load->model('ep_auth');
		$token_data = explode("|", $this->EE->ep_auth->set_token());
		$out['token_id'] = $token_data[0];
		$out['token'] = $token_data[1];
		$this->_return_json($out);
	}



	function _cp_content_edit_ajax_response()
	{
		$entry_ids = $_POST['entryIds'];

		// To make sure we're dealing with integers re-cast each item in this array
		for($i=0;$i < count($entry_ids);$i++)
		{
			$entry_ids[$i] = (INT) $entry_ids[$i];
		}

		$this->EE->load->model('ep_entry_draft');
		$this->EE->ep_entry_draft->bwf_settings = $this->settings;
		$data = $this->EE->ep_entry_draft->get_in_entry_ids($entry_ids, array('entry_id','status'));
		$this->_return_json($data);
	}



	function get_addon_version($type)
	{
		$this->EE->db->select('version');
		$rs = $this->EE->db->get_where('fieldtypes',array('name' => $type));
		if($rs->num_rows()) return $rs->row()->version;
		return 0;
	}



	function _append_stylesheet()
	{
		$this->EE->cp->add_to_head('<link rel="stylesheet" type="text/css" href="'.$this->_theme_url().'stylesheets/bwf.css?'.$this->version.'" />');
	}



	function _append_javascripts($snippet=null, $exclude=array())
	{
		$to_include = array_diff($this->js_libs, $exclude);

		foreach($to_include as $script)
		{
			$this->EE->cp->add_to_head('<script type="text/javascript" src="'.$this->_theme_url().'javascript/'.$script.'?'.BWF_VER.'"></script>');
		}
		
		if ($snippet)
		{
			$this->EE->cp->add_to_head(implode("\n", array(
			'<script>' .
			'// <![CDATA[',
			$snippet .
			'// ]]>',
			'</script>'
			)));
		}
	}



	// Spit out the JSON output then die
	private function _return_json($data)
	{
		$this->EE->load->library('javascript');
		header('ContentType: application/json');
		die($this->EE->javascript->generate_json($data, TRUE));
	}



	/**
	 * Activate Extension
	 * @return void
	 */
	function activate_extension()
	{
		// -----------------------------------------------------------------------------------------------------------
		// Load the activate model and then register the hooks and create the tables
		// -----------------------------------------------------------------------------------------------------------
		$this->EE->load->model('ep_activate');
		$this->EE->ep_activate->class_name = __CLASS__;
		$this->EE->ep_activate->version = $this->version;
		$this->EE->ep_activate->register_hooks();
		$this->EE->ep_activate->create_tables();
		
		// -----------------------------------------------------------------------------------------------------------
		// Populate the ep_roles table
		// -----------------------------------------------------------------------------------------------------------
		$this->EE->load->model('ep_roles');
		$this->EE->ep_roles->initialise($this->EE->config->item('site_id'));
		$this->EE->ep_roles->populate_roles();

		// -----------------------------------------------------------------------------------------------------------
		// Load the status model and create the status group (For the current site)
		// -----------------------------------------------------------------------------------------------------------
		$this->EE->load->model('ep_statuses');
		$status_group_id = $this->EE->ep_statuses->create_status_group($this->name);
		
		$this->EE->ep_statuses->insert_status($status_group_id, 'closed', '1', $this->settings['status_color_closed']);
		$this->EE->ep_statuses->insert_status($status_group_id, 'draft', '2', $this->settings['status_color_draft']);
		$this->EE->ep_statuses->insert_status($status_group_id, 'submitted', '3', $this->settings['status_color_submitted']);
		$this->EE->ep_statuses->insert_status($status_group_id, 'open', '4', $this->settings['status_color_open']);
	}



	/**
	 * Disable Extension
	 * @return void
	 */
	function disable_extension()
	{
		// -----------------------------------------------------------------------------------------------------------
		// Load the activate model and then drop the tables and delete the extension record
		// -----------------------------------------------------------------------------------------------------------
		$this->EE->load->model('ep_activate');
		$this->EE->ep_activate->class_name = __CLASS__;
		$this->EE->ep_activate->remove_bwf();
		
		// -----------------------------------------------------------------------------------------------------------
		// Load the status model and remove the status group (Do we need to do this?)
		// -----------------------------------------------------------------------------------------------------------
		$this->EE->load->model('ep_statuses');
		$this->EE->ep_statuses->remove_status_group($this->name);
	}



	/**
	 * Update Extension
	 * @return 	mixed	void on update / false if none
	 */
	function update_extension($current = '')
	{
		// -----------------------------------------------------------------------------------------------------------
		// Load the activate model and update the DB where necessary
		// -----------------------------------------------------------------------------------------------------------
		$this->EE->load->model('ep_activate');
		$this->EE->ep_activate->class_name = __CLASS__;
		$this->EE->ep_activate->version = $this->version;
		$this->EE->ep_activate->current = $current;
		$this->EE->ep_activate->update_bwf();
	}



	/**
	 * Theme URL
	 * @return string (Tidy up the URL)
	 */
	private function _theme_url()
	{
		return reduce_double_slashes(URL_THIRD_THEMES.'/ep_better_workflow/');
	}


}
