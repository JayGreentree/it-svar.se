<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Ep_files
 *
 * ----------------------------------------------------------------------------------------------
 * @package	EE2 
 * @subpackage	ThirdParty
 * @author	Malcolm Elsworth 
 * @link	http://electricputty.co.uk 
 * @copyright	Copyright (c) 2011 Electric Putty Ltd.
 *
 */

class Ep_files {



	private $settings = NULL;



	function Ep_files($settings=array())
	{		
		$this->EE =& get_instance();
		$this->settings = $settings;

		// -------------------------------------------
		// Load the library file and instantiate logger
		// -------------------------------------------
		require_once reduce_double_slashes(PATH_THIRD . '/ep_better_workflow/libraries/ep_bwf_logger.php');
		$this->action_logger = new Ep_workflow_logger($this->settings['advanced']['log_events']);
	}



	function copy_safecracker_file_data_to_revision_post($data)
	{
		// Load the third party model
		$this->EE->load->model('ep_third_party');
		
		// Get all the playa fields
		$sc_file_fields = $this->EE->ep_third_party->get_all_fields('safecracker_file');
		
		$this->action_logger->add_to_log("ep_safecracker_file: copy_data_to_revision_post(): Checking for safecracker_file fields");
		
		if ($sc_file_fields->num_rows() > 0)
		{
			$this->action_logger->add_to_log("ep_safecracker_file: copy_data_to_revision_post(): Found {$sc_file_fields->num_rows()} safecracker_file fields");
			
			foreach ($sc_file_fields->result_array() as $row)
			{
				$this_entry_id = $data['entry_id'];
				$this_field_id = $row['field_id'];
				$this_field_name = 'field_id_'.$row['field_id'];

				// If this field is defined in our data array copy its value to the revision post
				if (isset($data[$this_field_name]))
				{
					$this->action_logger->add_to_log("ep_safecracker_file: copy_data_to_revision_post(): Field {$this_field_name} found in current data array - data: {$data[$this_field_name]}");
					
					$data['revision_post'][$this_field_name] = $data[$this_field_name];
				}
			}
		}
		return $data;
	}



	function clean_up_native_file_field_data($data)
	{		
		// Load the third party model
		$this->EE->load->model('ep_third_party');
		
		// Get all the fields
		$file_fields = $this->EE->ep_third_party->get_all_fields('file');
		
		$this->action_logger->add_to_log("ep_files: clean_up_data(): Checking for file fields");
		
		if ($file_fields->num_rows() > 0)
		{
			foreach ($file_fields->result_array() as $row)
			{
				$this_field_name = 'field_id_'.$row['field_id'];
			
				// See if you can find a reference to a directory in the revision post
				if(isset($data['revision_post'][$this_field_name.'_directory']))
				{
					if ($data['revision_post'][$this_field_name.'_directory'] == '')
					{
						$this->action_logger->add_to_log("ep_files: clean_up_data(): Found an empty directory  reference so we'll remove it");
						unset($data['revision_post'][$this_field_name.'_directory']);
					}
				}
				
				// See if you can find a reference to a directory
				if(isset($data[$this_field_name]))
				{
					if ($data[$this_field_name] == '{filedir_}')
					{
						$this->action_logger->add_to_log("ep_files: clean_up_data(): Found an empty directory  reference so we'll remove it");
						unset($data[$this_field_name]);
					}
				}
				
			}
		}
		return $data;
	}


}