<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Ep_third_party_fieldtypes
 * 
 * ----------------------------------------------------------------------------------------------
 * @package	EE2 
 * @subpackage	ThirdParty
 * @author	Andrea Fiore / Malcolm Elsworth 
 * @link	http://electricputty.co.uk 
 * @copyright	Copyright (c) 2011 Electric Putty Ltd.
 * 	
 */

class Ep_third_party_fieldtypes 
{

	private $settings = NULL;

	/**
	* Instantiate the BetterWorkflow Logger class in the client
	*/
	function Ep_third_party_fieldtypes($settings = array())
	{
		$this->EE =& get_instance();
		$this->settings = $settings;
		
		// -------------------------------------------
		// Load the library file and instantiate logger
		// -------------------------------------------
		require_once reduce_double_slashes(PATH_THIRD . '/ep_better_workflow/libraries/ep_bwf_logger.php');
		$this->action_logger = new Ep_workflow_logger($this->settings['advanced']['log_events']);
		
		// Load the third party model
		$this->EE->load->model('ep_third_party');
	}



	function process_draft_data($data, $draft_action)
	{
		// Loggin
		$this->action_logger->add_to_log("ep_third_party_fieldtypes: process_draft_data(): Draft_action ".$draft_action);

		// We only want to loop through the revision_post part of the data array
		$revision_post_data = $data['revision_post'];
		
		// loop through all the items in the data array
		foreach($revision_post_data as $key => $value)
		{
			// We're only interested in custom fields
			if(preg_match('/^field_id/',$key))
			{
				// Convert field name to id
				$field_id = substr($key, 9);
				
				// Get the field data
				$field_data = $this->EE->ep_third_party->get_field_type($field_id);
				
				// Get the field type
				$field_type = $field_data->row('field_type');
				
				// Try and instantiate the fieldtype class
				$ft = $this->_init_fieldtype_obj($field_type);
				
				// Were we successful?
				if(is_object($ft))
				{
					$this->action_logger->add_to_log("ep_third_party_fieldtypes: process_draft_data(): Field type " . $field_type);
				
					// Get the field settings
					$fieldtype_settings = $this->_get_fieldtype_settings($field_data, $data);
					
					// Add the channel and entry IDs to the settings data
					$fieldtype_settings['entry_id'] = $data['entry_id'];
					$fieldtype_settings['channel_id'] = $data['channel_id'];

					// Pass some values to the field type's settings array
					$ft->settings = $fieldtype_settings;
				
					// Switch on action
					switch ($draft_action)
					{
						// Are we creating or updating a draft
						case 'create':
						case 'update':
						if (method_exists($ft, 'draft_save'))
						{
							// If the fieldtype has a BWF specific draft_save() method, use this
							$revision_post_data[$key] = $ft->draft_save($value, $draft_action);
						}
						else
						{
							// If not, as long as the field type doesn't have a post_save() method, use the native save() method so we get the data in the right format
							if(!method_exists($ft, 'post_save') && method_exists($ft, 'save'))
							{
								$revision_post_data[$key] = $ft->save($value);
							}
						}
						break;

						// Are we turning a 'draft' into an 'entry'
						case 'publish':
						if (method_exists($ft, 'draft_publish'))
						{
							$ft->draft_publish();
						}
						break;
						
						case 'discard':
						if (method_exists($ft, 'draft_discard'))
						{
							$ft->draft_discard();
						}
						break;
						
					}
				}
			}
		}
		
		// Rebuild the data array
		$data['revision_post'] = $revision_post_data;

		// Send back the updated data array
		return $data;
	}



	private function _init_fieldtype_obj($field_type)
	{
		$class = ucfirst(strtolower($field_type)).'_ft';
		$this->EE->api_channel_fields->include_handler($field_type);
		
		if (class_exists($class))
		{
			// Instantiate the field type
			return new $class();
		}
		else
		{
			return null;
		}
	}



	private function _get_fieldtype_settings($field_data, $draft_data)
	{

		$_dst_enabled = ($this->EE->session->userdata('daylight_savings') == 'y' ? TRUE : FALSE);

		$field_settings = array();

		foreach ($field_data->result_array() as $row)
		{
			$field_fmt	= $row['field_fmt'];
			$field_dt 	= '';
			$field_data	= '';
			$dst_enabled	= '';
						
			$field_data 	= (isset($draft_data['field_id_'.$row['field_id']])) ? $draft_data['field_id_'.$row['field_id']] : $field_data;				
			$field_dt	= (isset($draft_data['field_dt_'.$row['field_id']])) ? $draft_data['field_dt_'.$row['field_id']] : 'y';
			$field_fmt	= (isset($draft_data['field_ft_'.$row['field_id']])) ? $draft_data['field_ft_'.$row['field_id']] : $field_fmt;						

			$settings = array(
				'field_instructions'	=> trim($row['field_instructions']),
				'field_text_direction'	=> ($row['field_text_direction'] == 'rtl') ? 'rtl' : 'ltr',
				'field_fmt'		=> $field_fmt,
				'field_dt'		=> $field_dt,
				'field_data'		=> $field_data,
				'field_name'		=> 'field_id_'.$row['field_id'],
				'dst_enabled'		=> $_dst_enabled
			);
			
			$ft_settings = array();

			if (isset($row['field_settings']) && strlen($row['field_settings']))
			{
				$ft_settings = unserialize(base64_decode($row['field_settings']));
			}
						
			$settings = array_merge($row, $settings, $ft_settings);
		}

		return $settings;
	}

}