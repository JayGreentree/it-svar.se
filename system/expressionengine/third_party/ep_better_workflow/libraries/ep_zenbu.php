<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Ep_zenbu: Better Workflow extension for Zenbu
 * 
 * ----------------------------------------------------------------------------------------------
 * @package	EE2 
 * @subpackage	ThirdParty
 * @author	Nicolas Bottari
 * @link	http://electricputty.co.uk 
 * @copyright	Copyright (c) 2011 Electric Putty Ltd.
 * 
 * I (Nicolas Bottari) am not responsible for any
 * damage, data loss, etc caused directly or indirectly by the use of this extension. 
 * @link	http://nicolasbottari.com/eecms/zenbu/
 * @docs	http://nicolasbottari.com/eecms/docs/zenbu/
 *
 * Special thanks to Mark Croxton for providing the initial code
 * @link	http://twitter.com/#!/croxton	
 */


class Ep_zenbu {



	var $settings = array();



	function Ep_zenbu($settings='')
	{		
		$this->EE =& get_instance();
		$this->settings = $settings;
		$this->assigned_channels = (isset($this->EE->session->userdata['assigned_channels'])) ? $this->EE->session->userdata['assigned_channels'] : null;
	}



	/**
	*	===============================
	*	function zenbu_filter_by_status
	*	===============================
	*	Enables the addition of extra queries when filtering entries by status
	*	@param	int		$channel_id	The currently selected channel_id
	*	@param	string	$status		The currently selected status
	*	@param	array	$rule		The current entry filter rule array
	*	@param	string	$where		The partial query string
	*	@return void
	*/
	function filter_by_status($channel_id, $status, $rule, $where)
	{
		// Exit if we're not using Zenbu
		if($this->settings['advanced']['redirect_on_action'] != 'zenbu')
		{
			return $where;
		}		
		
		// Exit if query string is empty
		if(empty($where))
		{
			return $where;
		}
		
		$ep_sql = "SELECT entry_id FROM exp_ep_entry_drafts ";
						
		if($rule['cond'] == 'is')
		{
			$ep_sql .= "WHERE status = '{$status}' ";
		} 
		elseif($rule['cond'] == 'isnot') 
		{
			$ep_sql .= "WHERE status != '{$status}' ";
		}
		
		if( ! empty($channel_id) && count($channel_id) == 1 )
		{
			$ep_sql .= "AND channel_id = {$channel_id}";
		} 
		else 
		{
			$ep_channel_ids_str = implode(', ', array_flip($this->assigned_channels));
			$ep_sql .= "AND channel_id IN ({$ep_channel_ids_str})";
		}
		
		$ep_res=$this->EE->db->query($ep_sql); 
		$ep_entries = array();

		foreach($ep_res->result() as $ep_row)
		{
				$ep_entries[] = $ep_row->entry_id;
		}
		
		// Add parenthesis and build OR... string if entries are found in exp_ep_entry_drafts 
		if (!empty($ep_entries))
		{
			$where = '(' . $where . ' OR ';
			// add to main query
			foreach($ep_entries as $key => $entry_id)
			{
				$where .= 'exp_channel_titles.entry_id = ' . $entry_id . ' OR ';
			}
			$where = substr($where, 0, -4) . ')';
		}

		return $where;		
	}



	/**
	*	===============================
	*	function zenbu_modify_status_display
	*	===============================
	*	Modifies the display of the "status" column in the entry listing
	*	@param	string	$output			The output string to be displayed in the Zenbu column
	*	@param	array	$entry_array	An array containing all the entry_ids of the entry listing results
	*	@param	int		$entry_id		The entry ID of the current entry row
	*	@param	array	$statuses		An array containing all entry status data
	*	@return string	$output			The final output to be displayed in the Zenbu column
	*/
	function modify_status_display($output, $entry_array, $row, $statuses)
	{
		// Exit if we're not using Zenbu
		if($this->settings['advanced']['redirect_on_action'] != 'zenbu')
		{
			return $output;
		}

		$entry_id = isset($row['entry_id']) ? $row['entry_id'] : 0;
		
		if( ! isset($this->EE->session->cache['zenbu']['bwf_statuses']))
		{
			$ep_entry_ids_str = implode(', ', $entry_array);
			$ep_res = $this->EE->db->query("SELECT entry_id, status FROM exp_ep_entry_drafts WHERE entry_id IN ({$ep_entry_ids_str})");
			$ep_status_rows = array();
			
			foreach($ep_res->result() as $ep_row)
			{
			 	$ep_status_rows[$ep_row->entry_id] = $ep_row->status;
			}
			
			$this->EE->session->cache['zenbu']['bwf_statuses'] = $ep_status_rows;
		}
		
		if (isset($this->EE->session->cache['zenbu']['bwf_statuses'][$entry_id]))
		{
			$status_row = $this->EE->session->cache['zenbu']['bwf_statuses'][$entry_id];
			
			// Make sure this status appears in our array - sometimes it doesn't
			if(isset($statuses[$status_row]['cell_output']))
			{
				$ep_status_cell_output = $statuses[$status_row]['cell_output'];
			}
			else
			{
				$ep_status_cell_output = $status_row;	
			}
			
			if (!empty($output))
			{
				$output .= ', '.$ep_status_cell_output;
			}
			else
			{
				$output = $ep_status_cell_output;
			}
		}
		
		return $output;
		
	}


	
	/**
	*	===========================================
	*	Extension Hook zenbu_modify_title_display
	*	===========================================
	*
	*	Modifies the display of the "title" column in the entry listing
	*	@param	string	$output			The output string to be displayed in the Zenbu column
	*	@param	array	$entry_array	An array containing all the entry_ids of the entry listing results
	*	@param	int		$entry_id		The entry ID of the current entry row
	*	@return string	$output			The final output to be displayed in the Zenbu column
	*/
	function modify_title_display($output, $entry_array, $row)
	{
		// Exit if we're not using Zenbu
		if($this->settings['advanced']['redirect_on_action'] != 'zenbu')
		{
			return $output;
		}
	
		$this->EE->cp->add_to_head('<link media="screen, projection" rel="stylesheet" type="text/css" href="'.$this->EE->config->item('theme_folder_url').'third_party/ep_better_workflow/stylesheets/bwf.css" />');

		$entry_id = isset($row['entry_id']) ? $row['entry_id'] : 0;
		$this->EE->load->helper('html');
		if( ! isset($this->EE->session->cache['zenbu']['bwf_statuses']))
		{
			$ep_entry_ids_str = implode(', ', $entry_array);
			$ep_res = $this->EE->db->query("SELECT entry_id, status FROM exp_ep_entry_drafts WHERE entry_id IN ({$ep_entry_ids_str})");
			$ep_status_rows = array();
			
			foreach($ep_res->result() as $ep_row)
			{
			 	$ep_status_rows[$ep_row->entry_id] = $ep_row->status;
			}
			$this->EE->session->cache['zenbu']['bwf_statuses'] = $ep_status_rows;
		}
		
		if( isset($this->EE->session->cache['zenbu']['bwf_statuses'][$entry_id]))
		{
			$status_row = $this->EE->session->cache['zenbu']['bwf_statuses'][$entry_id];
			if ($status_row == "draft" && $row['status'] == "open")
			{
				$output = '<div class="bwf_zenbu_status bwf_zenbu_draft_open">'.$output.'</div>';
			}
			elseif ($status_row == "submitted" && $row['status'] == "open")
			{
				$output = '<div class="bwf_zenbu_status bwf_zenbu_submitted_open">'.$output.'</div>';
			}
		}
		else
		{
			if ($row['status'] == "draft")
			{
				$output = '<div class="bwf_zenbu_status bwf_zenbu_draft">'.$output.'</div>';
			}
			elseif ($row['status'] == "submitted")
			{
					$output = '<div class="bwf_zenbu_status bwf_zenbu_submitted">'.$output.'</div>';
			}	
		}

		return $output;
	}



}
