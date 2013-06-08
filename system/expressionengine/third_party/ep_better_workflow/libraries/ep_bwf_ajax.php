<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Ep_ajax_create_entry
 * 
 * To avoid a never ending loop we can't use the channel entries API to create a new entry via an AJAX request on preview
 * so we use as much of the API as we can but dodge the hook calls
 *
 * ----------------------------------------------------------------------------------------------
 * @package	EE2 
 * @subpackage	ThirdParty
 * @author	Andrea Fiore / Malcolm Elsworth 
 * @link	http://electricputty.co.uk 
 * @copyright	Copyright (c) 2011 Electric Putty Ltd.
 *
 */

class Ep_bwf_ajax {


	private $settings = NULL;


	/**
	* Instantiate the ajax_create_entry class in the client
	*
	*
	*/
	function Ep_bwf_ajax($settings=array())
	{
		$this->EE =& get_instance();
		$this->settings = $settings;
		
		// -------------------------------------------
		// Load the library file and instantiate logger
		// -------------------------------------------
		require_once reduce_double_slashes(PATH_THIRD . '/ep_better_workflow/libraries/ep_bwf_logger.php');
		$this->action_logger = new Ep_workflow_logger($this->settings['advanced']['log_events']);
	}



	public function _create_new_entry($entry_meta, $entry_data, $status)
	{
		// Logging
		$this->action_logger->add_to_log("ep_bwf_ajax: _create_new_entry(): We are creating a new entry using the AJAX call");
		
		// Set the status
		$entry_data['revision_post']['status'] = $status;
		
		// Define the output array
		$out = array();
		
		// Remove the 'bwf_ajax_new_entry' value so we don't get stuck in a loop when we hit the 'on_entry_submission_ready' hook again
		unset($_POST['bwf_ajax_new_entry']);
		unset($_REQUEST['bwf_ajax_new_entry']);
		unset($entry_data['bwf_ajax_new_entry']);
		unset($entry_data['revision_post']['bwf_ajax_new_entry']);
		
		// Because the revision_post post part of this is the only bit we can trust we're sending this to the submit_new_entry() method in api_channel_entries
		// This will be expecting a revision_post sub array so add one here
		$entry_data['revision_post']['revision_post'] = '';
		
		// Load the Channel entries API
		$this->EE->load->library('api');
		$this->EE->api->instantiate('channel_entries');
		
		// Try and create a new entry
		$success = $this->EE->api_channel_entries->submit_new_entry($entry_data['channel_id'], $entry_data['revision_post']);
		
		// Logging
		$this->action_logger->add_to_log("ep_bwf_ajax: _create_new_entry(): The success massage from the Channel Entries API is: ".$success);
		
		// If we weren't successful output the error information
		if (!$success)
		{
			$errors = $this->EE->api_channel_entries->errors;
			if (!isset($_REQUEST['bwf_ajax_new_entry'])) show_error('An Error Occurred Updating the Entry: <pre>' . var_export( $errors, true ) . '</pre>');
		}

		// If we did manage to create a new entry, grab in the new entry_id and url_title and send them back as JSON
		if ($success)
		{
			$r = array_shift($this->EE->db->query(
			"SELECT entry_id, url_title FROM exp_channel_titles ORDER BY entry_id DESC LIMIT 1"
			)->result());
			
			$out['new_entry_id'] = ($r && isset($r->entry_id)) ? $r->entry_id : null;
			$out['new_url_title'] = ($r && isset($r->url_title)) ? $r->url_title : null; 
		}

		$this->EE->load->library('javascript');
			
		header('ContentType: application/json');
		die($this->EE->javascript->generate_json($out, TRUE));	
	}

}