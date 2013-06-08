<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Ep_statuses Model
 *
 * ----------------------------------------------------------------------------------------------
 * @package	EE2 
 * @subpackage	ThirdParty
 * @author	Andrea Fiore / Malcolm Elsworth 
 * @link	http://electricputty.co.uk 
 * @copyright	Copyright (c) 2011 Electric Putty Ltd.
 *
 */

class Ep_statuses extends CI_Model {



	var $status_color_closed;
	var $status_color_draft;
	var $status_color_submitted;
	var $status_color_open;
	var $preview_status;


	function Ep_statuses()
	{
		parent::__construct();
		$this->preview_status = $this->config->item('bwf_preview_status');
		if(!$this->preview_status) {
			$this->preview_status = 'open';
		}
	}



	function create_status_group($group_name)
	{
		$data = array(
			'group_name'	=> $group_name,
			'site_id'	=> $this->config->item('site_id')
		);
		$this->db->insert('status_groups', $data);
		return $this->db->insert_id();
	}



	function insert_status($group_id, $status_name, $status_order, $status_colour)
	{
		$data = array(
			'site_id'	=> $this->config->item('site_id'),
			'group_id'	=> $group_id,
			'status'	=> $status_name,
			'status_order'	=> $status_order,
			'highlight'	=> $status_colour
		);
		$this->db->insert('statuses', $data);
		return true;
	}



	function remove_status_group($group_name)
	{
		$this->db->from('status_groups');
		$this->db->where('group_name', $group_name);
		$this->db->delete();
		return TRUE;
	}



	function get_status_group_id($group_name)
	{
		$this->db->where('group_name', $group_name);
		$this->db->where('site_id', $this->config->item('site_id'));
		$status_group = $this->db->get('status_groups');
		if ($status_group->num_rows() == 0)
		{
			// If we can't find the status group it may be because we have switched sites since installation
			return $this->_duplicate_status_group($group_name);
		}
		else
		{
			return $status_group->row('group_id');
		}
	}



	function get_open_status_id($group_id)
	{
		$this->db->where('group_id', $group_id);
		$this->db->where('status', 'open');
		$this->db->where('site_id', $this->config->item('site_id'));
		$statuses = $this->db->get('statuses');
		if ($statuses->num_rows() == 0)
		{
			return FALSE;
		}
		else
		{
			return $statuses->row('status_id');
		}
	}



	function check_channel_entries($channel_id, $bwf_statuses)
	{
		if($channel_id == '') return FALSE;
	
		$i=0;
		
		$sql = "SELECT DISTINCT status from exp_channel_titles
		WHERE channel_id = '{$channel_id}' AND (";
		foreach($bwf_statuses as $status)
		{
			if($i>0) $sql .= " AND ";
			$sql .= " status <> BINARY('".$status."')";
			$i++;
		}
		$sql .= ");";
		
		return $this->db->query($sql);
	}



	function set_entry_status($entry_id)
	{
		// Set the entry date to yesterday to overcome the annoying future entries bugs
		$this_time_yesterday = strtotime('-1 day', time());
		
		$this->db->where('entry_id',$entry_id);
		$this->db->update('channel_titles',array('status' => $this->preview_status, 'entry_date' => $this_time_yesterday));
		return true;
	}



	function revert_entry_status($ss_entry_id, $ss_entry_status, $ss_entry_date)
	{
		$this->db->where('entry_id',$ss_entry_id);
		$this->db->update('channel_titles',array('status' => $ss_entry_status, 'entry_date' => $ss_entry_date));
		return "Entry ID '".$ss_entry_id."' status reverted to '".$ss_entry_status."'";		
	}



	private function _duplicate_status_group($group_name)
	{
		$status_group_id = $this->create_status_group($group_name);
		
		$this->insert_status($status_group_id, 'closed', '1', $this->status_color_closed);
		$this->insert_status($status_group_id, 'draft', '2', $this->status_color_draft);
		$this->insert_status($status_group_id, 'submitted', '3', $this->status_color_submitted);
		$this->insert_status($status_group_id, 'open', '4', $this->status_color_open);
		
		return $status_group_id;
	}


}