<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Ep_auth Model
 *
 * ----------------------------------------------------------------------------------------------
 * @package	EE2 
 * @subpackage	ThirdParty
 * @author	Malcolm Elsworth 
 * @link	http://electricputty.co.uk 
 * @copyright	Copyright (c) 2011 Electric Putty Ltd.
 *
 */

class Ep_auth extends CI_Model {


	var $log_events = false;
	var $enable_external_previews = false;
	var $preview_token_ttl;
	var $preview_token_length;


	function Ep_auth()
	{
		parent::__construct();

		// Set the auth token TTL
		$this->preview_token_ttl = $this->config->item('bwf_preview_token_ttl');
		if(!$this->preview_token_ttl) $this->preview_token_ttl = '24';
		
		// Set the auth token string length
		$this->preview_token_length = $this->config->item('bwf_preview_token_length');
		if(!$this->preview_token_length) $this->preview_token_length = '25';

		// -------------------------------------------
		// Load the logging library file
		// -------------------------------------------
		require_once reduce_double_slashes(PATH_THIRD . '/ep_better_workflow/libraries/ep_bwf_logger.php');
	}



	function set_token()
	{
		$token = $this->_generate_token($this->preview_token_length);
		$data = array('token' => $token, 'timestamp' => time());
		$this->db->insert('ep_entry_drafts_auth', $data);
		$token_id = $this->db->insert_id();
		return $token_id.'|'.$token;
	}



	function is_valid_request($token_id, $token)
	{
		// Store this for later, the original value seems to get changed before we use it
		$del_token_id = $token_id;
		
		$this->action_logger = new Ep_workflow_logger($this->log_events);
		
		// Is this a valid token
		if($token_id =! 0 && ($token == $this->_get_token_value($token_id)))
		{
			// Logging
			$this->action_logger->add_to_log("model:ep_auth: is_valid_request(): Passed token validity - is_valid_preview_request set to 'true'");
			$theReturn = true;
		}
		else
		{
			// Logging
			$this->action_logger->add_to_log("model:ep_auth: is_valid_request(): Failed token validity - is_valid_preview_request set to 'false'");
			$theReturn = false;
		}

		// Do we want to delete this token right now?
		if ($this->enable_external_previews == 'yes')
		{
    			// Logging
    			$this->action_logger->add_to_log("model:ep_auth: is_valid_request(): We have enabled external previews so don't delete this token right now");
    		}
    		else
    		{
  			// Logging
  			$this->action_logger->add_to_log("model:ep_auth: is_valid_request(): Now we have checked the token and set the session variable we can delete the token ID: '{$del_token_id}'");
      			
      			// Delete the auth token
    			$this->delete_token($del_token_id);  
    		}
		
		return $theReturn;
	}


	// This function will delete any tokens which are more than the TTL old
	function delete_tokens()
	{
		// Set time 
		$delete_time = strtotime('-'.$this->preview_token_ttl.' hours', time());
		
		// Get any tokens which are older than the TTL
		$this->db->where('timestamp <', $delete_time);
		$tokens = $this->db->get('ep_entry_drafts_auth');
		if ($tokens->num_rows() == 0)
		{
			return "No auth tokens to delete";
		}
		else
		{
			foreach($tokens->result_array() as $row)
			{
				$this->delete_token($row['ep_auth_id']);
			}
			return $tokens->num_rows() . " auth token(s) deleted";
		}
	}



	function delete_token($token_id)
	{
		$this->db->from('ep_entry_drafts_auth');
		$this->db->where('ep_auth_id', $token_id);
		$this->db->delete();
	}



	private function _get_token_value($token_id)
	{
		$this->db->where('ep_auth_id', $token_id);
		$data = $this->db->get('ep_entry_drafts_auth');
		if ($data->num_rows() == 0)
		{
			return "";
		}
		else
		{
			foreach($data->result_array() as $row)
			{
				return $row['token'];
			}
		}
	}



	private function _generate_token($length)
	{
		$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
		$size = strlen( $chars );
		$token = '';
		for( $i = 0; $i < $length; $i++ )
		{
			$token .= $chars[ rand( 0, $size - 1 ) ];
		}
		return $token;
	}

}