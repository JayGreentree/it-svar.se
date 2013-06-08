<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Ep_roles Model
 *
 * ----------------------------------------------------------------------------------------------
 * @package	EE2 
 * @subpackage	ThirdParty
 * @author	Malcolm Elsworth 
 * @link	http://electricputty.co.uk 
 * @copyright	Copyright (c) 2011 Electric Putty Ltd.
 *
 */

class Ep_roles extends CI_Model {
  	var $role;
	var $site_id;
  
	function initialise($site_id, $role=null)
	{
		$this->site_id = $site_id;
		$this->role = $role;
	}

	function get_role_states($role=null)
	{
		if(!$role) {
			$role = $this->role;
		}
		
		// Check that the roles table exists
		if($this->db->table_exists('ep_roles'))
		{
			$this->db->where('site_id', $this->site_id);
			$roles = $this->db->get('ep_roles');
			
			// If we don't have any role information for this site
			// Re-run the populate method to set up the default settings for this site_id
			if ($roles->num_rows() == 0)
			{
				$states = $this->_clone_roles($role);
			}
			else
			{
				$results = $roles->result_array();
				foreach($results as $row)
				{
					if($row['role'] == $role)
					{
						$states = $row['states'];	
					}
				}
			}
			$states = unserialize($states);
			return $this->_translate_state_text($states);
		}
		return '';
	}
		
	function populate_roles()
	{
	
		// Build up array of options for the publisher role in each status
		$states = array();

		$states['null|null']['label'] = 'bwf_status_label_draft';
		$states['null|null']['buttons'][] = array('open' ,'epBwfEntry', 'create', 'bwf_btn_publish', false);
		$states['null|null']['buttons'][] = array('draft' ,'epBwfEntry', 'create', 'bwf_btn_save_as_draft', false);

		$states['draft|null']['label'] = 'bwf_status_label_draft';
		$states['draft|null']['buttons'][] = array('open' ,'epBwfEntry', 'update', 'bwf_btn_publish', false);
		$states['draft|null']['buttons'][] = array('draft' ,'epBwfEntry', 'update', 'bwf_btn_save_as_draft', false);
 
		$states['submitted|null']['label'] = 'bwf_status_label_submitted';
		$states['submitted|null']['buttons'][] = array('open' ,'epBwfEntry', 'update', 'bwf_btn_publish', false);
		$states['submitted|null']['buttons'][] = array('draft' ,'epBwfEntry', 'update', 'bwf_btn_revert_to_draft', false);
 
		$states['open|null']['label'] = 'bwf_status_label_live';
		$states['open|null']['buttons'][] = array('closed' ,'epBwfEntry', 'update', 'bwf_btn_archive', false);
		$states['open|null']['buttons'][] = array('open' ,'epBwfEntry', 'update', 'bwf_btn_publish', false);
		$states['open|null']['buttons'][] = array('draft' ,'epBwfDraft', 'create', 'bwf_btn_save_as_draft', false);
 
		$states['open|draft']['label'] = 'bwf_status_label_draft_live';
		$states['open|draft']['buttons'][] = array('open' ,'epBwfDraft', 'replace', 'bwf_btn_publish', false);
		$states['open|draft']['buttons'][] = array('draft' ,'epBwfDraft', 'update', 'bwf_btn_save_as_draft', false);
		$states['open|draft']['buttons'][] = array(null ,'epBwfDraft', 'delete', 'bwf_btn_discard_draft', false);
 
		$states['open|submitted']['label'] = 'bwf_status_label_submitted_live';
		$states['open|submitted']['buttons'][] = array('open' ,'epBwfDraft', 'replace', 'bwf_btn_publish', false);
		$states['open|submitted']['buttons'][] = array('draft' ,'epBwfDraft', 'update', 'bwf_btn_revert_to_draft', false);
 
		$states['closed|null']['label'] = 'bwf_status_label_closed';
		$states['closed|null']['buttons'][] = array('open' ,'epBwfEntry', 'update', 'bwf_btn_publish', false);
		$states['closed|null']['buttons'][] = array('draft' ,'epBwfEntry', 'update', 'bwf_btn_save_as_draft', false);
		
		$this->_update_role('publisher', $states);

		// Build up array of options for the editor role in each status
		$states = array();

		$states['null|null']['label'] = 'bwf_status_label_draft';
		$states['null|null']['buttons'][] = array('submitted' ,'epBwfEntry', 'create', 'bwf_btn_submit', true);
		$states['null|null']['buttons'][] = array('draft' ,'epBwfEntry', 'create', 'bwf_btn_save_as_draft', false);

		$states['draft|null']['label'] = 'bwf_status_label_draft';
		$states['draft|null']['buttons'][] = array('submitted' ,'epBwfEntry', 'update', 'bwf_btn_submit', true);
		$states['draft|null']['buttons'][] = array('draft' ,'epBwfEntry', 'update', 'bwf_btn_save_as_draft', false);
 
		$states['submitted|null']['label'] = 'bwf_status_label_submitted';
		$states['submitted|null']['can_edit'] = false;
		$states['submitted|null']['can_preview'] = false;
		$states['submitted|null']['buttons'][] = array('draft' ,'epBwfEntry', 'update', 'bwf_btn_revert_to_draft', false);
 
		$states['open|null']['label'] = 'bwf_status_label_live';
		$states['open|null']['buttons'][] = array('submitted' ,'epBwfDraft', 'create', 'bwf_btn_submit', true);
		$states['open|null']['buttons'][] = array('draft' ,'epBwfDraft', 'create', 'bwf_btn_save_as_draft', false);
 
		$states['open|draft']['label'] = 'bwf_status_label_draft_live';
		$states['open|draft']['buttons'][] = array('submitted' ,'epBwfDraft', 'update', 'bwf_btn_submit', true);
		$states['open|draft']['buttons'][] = array('draft' ,'epBwfDraft', 'update', 'bwf_btn_save_as_draft', false);
		$states['open|draft']['buttons'][] = array(null ,'epBwfDraft', 'delete', 'bwf_btn_discard_draft', false);
 
		$states['open|submitted']['label'] = 'bwf_status_label_submitted_live';
		$states['open|submitted']['can_edit'] = false;
		$states['open|submitted']['can_preview'] = false;
		$states['open|submitted']['buttons'][] = array('draft' ,'epBwfDraft', 'update', 'bwf_btn_revert_to_draft', false);
 
		$states['closed|null']['label'] = 'bwf_status_label_archived';
		$states['closed|null']['buttons'][] = array('submitted' ,'epBwfEntry', 'update', 'bwf_btn_submit', true);
		$states['closed|null']['buttons'][] = array('draft' ,'epBwfEntry', 'update', 'bwf_btn_save_as_draft', false);
		
		$this->_update_role('editor', $states);
	}
	
	private function _update_role($role, $states)
	{
		// Check to see if we already have this role stored
		$role_states = $this->db->get_where('ep_roles', array('site_id' => $this->site_id, 'role' => $role));

		// If we *don't*, create a record
		if ($role_states->num_rows() == 0)
		{
			$data = array(
				'site_id' => $this->site_id,
				'role' => $role,
				'states' => serialize($states)
			);
			$this->db->insert('ep_roles', $data);
		}
		
		// If we do, update them
		else
		{	
			$this->db->where('site_id', $this->site_id);
			$this->db->where('role', $role);
			$this->db->update('ep_roles', array('states' => serialize($states)));
		}
	}

	private function _clone_roles($role)
	{
		$this->populate_roles();

		$this->db->select('states');
		$this->db->where('site_id', $this->site_id);
		$this->db->where('role', $role);
		$query = $this->db->get('ep_roles');
		
		return $query->row()->states;
	}
	
	private function _translate_state_text($states)
	{
		$this->lang->loadfile('ep_better_workflow');
		foreach($states as $i=>$state) {
			foreach($state['buttons'] as $j=>$button) {
				// translate button text
				$state['buttons'][$j][3] = $this->lang->line($button[3]);
			}
			$state['label'] = $this->lang->line($state['label']);
			$states[$i] = $state;
		}
		return $states;
	}
}