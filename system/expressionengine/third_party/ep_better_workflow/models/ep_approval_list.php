<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


/**
 * Ep_approval_list
 *
 * @package	ep_better_workflow
 * @subpackage	ThirdParty
 * @subpackage	Addons
 * @category	Module
 * @author		Rob Hodges / Malcolm Elsworth
 * @link 		http://electricputty.co.uk 
 * @copyright	Copyright (c) 2011 Electric Putty Ltd.
 */

class Ep_approval_list extends CI_Model {

	var $site_id;

	function Ep_approval_list()
	{
		parent::__construct();
	}



	function workflow_settings($request)
	{
		// Check that the settings table exists
		if($this->db->table_exists('ep_settings'))
		{
			$this->db->where('site_id', $this->site_id);
			$get_settings = $this->db->get('ep_settings',1,0)->row();
			
			if(count($get_settings) == 0) return '';
			
			// Unserialize the settings
			$get_settings = unserialize($get_settings->settings);
			
			// Choose what array you want from the settings
			if ($request == 'channels') {
				return $get_settings['bwf_channels'];
			} elseif ($request == 'members') {
				return $get_settings['groups'];
			}
		}
		return '';
	}



	function get_approval_required_entries($current_assigned_member_group_channels, &$submitted_entries_data, &$submitted_entries_order)
	{	
		// Submitted entries counter
		$sec = 0;
		
		// Get channel IDs only of channels who use workflow, and compare it against current users assigned channels
		$workflow_channels = array();
	
		foreach ($this->workflow_settings('channels') as $bwf_channel)
		{
			if (in_array($bwf_channel, array_keys($current_assigned_member_group_channels)))
			{
				$workflow_channels[] .= $bwf_channel;
			}
		}
		
		// Second check to make sure there are any valid channels (assigned channel gets deleted/unassigned from user)
		if (empty($workflow_channels)) return;
		
		$workflow_channels = implode(',', $workflow_channels);
		
		// Get all the 'entries' with a status of 'submitted'
		$entries_query = "SELECT ct.entry_id, ct.channel_id, ct.author_id, ct.title, ct.edit_date
		FROM exp_channel_titles ct
		WHERE ct.channel_id IN  (".$workflow_channels.") AND ct.site_id=".$this->site_id." AND ct.status='submitted'
		ORDER BY ct.edit_date DESC
		LIMIT 9";
		
		$query = $this->db->query($entries_query);
		
		if ($query->num_rows() > 0)
		{
			foreach ($query->result_array() as $row)
			{
				$submitted_entries_data['e_'.$sec] = array(
					'entry_id' => $row['entry_id'], 
					'channel_id' => $row['channel_id'],
					'author_id' => $row['author_id'],
					'title' => $row['title'], 
					'edit_date' => (STRING)strtotime($row['edit_date']),
					'type' => 'entry'
				);
				$submitted_entries_order['e_'.$sec] = (STRING)strtotime($row['edit_date']);
				$sec++;
			}
		}
		
		// Get all the 'drafts' with a status of 'submitted'
		$drafts_query = "SELECT ed.entry_id, ed.channel_id, ed.author_id, ct.title, ed.edit_date
		FROM exp_channel_titles ct
		LEFT OUTER JOIN exp_ep_entry_drafts ed ON ed.entry_id = ct.entry_id
		WHERE ed.channel_id IN  (".$workflow_channels.") AND ed.site_id=".$this->site_id." AND ed.status='submitted'
		ORDER BY ed.edit_date DESC
		LIMIT 9";
			
		$query = $this->db->query($drafts_query);
		
		if ($query->num_rows() > 0)
		{
			foreach ($query->result_array() as $row)
			{
				$submitted_entries_data['e_'.$sec] = array(
					'entry_id' => $row['entry_id'], 
					'channel_id' => $row['channel_id'],
					'author_id' => $row['author_id'],
					'title' => $row['title'], 
					'edit_date' => $row['edit_date'],
					'type' => 'draft'
				);
				$submitted_entries_order['e_'.$sec] = $row['edit_date'];
				$sec++;
			}
		}

		// Order the submission in date order, newest first
		arsort($submitted_entries_order);
	}



	function get_channel_name($channel_id)
	{
		$this->db->select('channel_title');
		$this->db->from('channels');
		$this->db->where('channel_id', $channel_id);
		$this->db->limit(1);
		$query = $this->db->get();
		return $query->row('channel_title');
	}



	function get_author_name($member_id)
	{
		$this->db->select('screen_name');
		$this->db->from('members');
		$this->db->where('member_id', $member_id);
		$this->db->limit(1);
		$query = $this->db->get();
		return $query->row('screen_name');
	}

}