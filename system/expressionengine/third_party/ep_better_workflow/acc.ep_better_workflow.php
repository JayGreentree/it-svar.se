<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


require_once reduce_double_slashes(PATH_THIRD . '/ep_better_workflow/config.php');


/**
 * EP Better Workflow
 * ----------------------------------------------------------------------------------------------
 *
 * @package	ExpressionEngine
 * @subpackage	Addons
 * @category	Module
 * @author	Rob Hodges / Malcolm Elsworth
 * @link	http://betterworkflow.electricputty.co.uk/
 * @copyright	Copyright (c) 2012 Electric Putty Ltd.
 */


class Ep_better_workflow_acc {


	public $name		= BWF_NAME;
	public $version		= BWF_VER;
	public $id		= 'ep_better_workflow';
	public $description	= 'See the latest items requiring approval';
	public $sections	= array();
	
	private $_base_url;
	
	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->EE =& get_instance();
		$this->_base_url = BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=ep_better_workflow';
		
		// -------------------------------------------
		// Define URL_THIRD_THEMES / PATH_THIRD_THEMES constant for pre version 2.4	
		// -------------------------------------------
		defined('URL_THIRD_THEMES') OR define('URL_THIRD_THEMES', $this->EE->config->item('theme_folder_url').'third_party/');
	}



	/**
	 * Set Sections
	 *
	 * @return 	void
	 */
	public function set_sections()
	{
		
		$this->sections['Entries requiring approval'] = $this->_submitted_list();
		
	}



	function _submitted_list() {
	
		// Get site ID
		$site_id = $this->EE->config->item('site_id');
		
		// Load Approval list model
		$this->EE->load->model('ep_approval_list');
		
		// Pass site ID to the approval list model
		$this->EE->ep_approval_list->site_id = $site_id;
		
		// Grab the BWF member groups and assignments
		$member_groups = $this->EE->ep_approval_list->workflow_settings('members');
		$bwf_asssigned_channels = $this->EE->ep_approval_list->workflow_settings('channels');
		
		// Is the setting table populated?
		if (empty($member_groups)) return "<p>Better Workflow has not been configured yet.</p>";
		
		// Are there any channels assigned to BWF?
		if (empty($bwf_asssigned_channels)) return "<p>Better Workflow has not been assigned to any channels yet.</p>";
		
		// Get current logged in member ID, then check it against BWF settings to see if they are allowed to publish entries
		// Grab current logged in member group ID
		$current_member_group = $this->EE->session->userdata('group_id'); 

		// Grab current logged in member channel assignments
		$current_assigned_member_group_channels = $this->EE->session->userdata['assigned_channels'];

		$publishers = array();
		foreach ($member_groups as $member => $setting) {
			if ($setting['role'] == "Publisher") {
				$publishers[] = substr($member, 3);
			}
		}
		
		// Load the CSS
		$this->EE->cp->add_to_head('<link media="screen, projection" rel="stylesheet" type="text/css" href="'.URL_THIRD_THEMES.'ep_better_workflow/stylesheets/bwf.css" />');
		
		// Compare the array of publisher IDs vs the current logged in member group
		if(in_array($current_member_group, $publishers)) {
							
			$submitted_entries_data = array();
			$submitted_entries_order = array();
			
			$this->EE->ep_approval_list->get_approval_required_entries($current_assigned_member_group_channels, $submitted_entries_data, $submitted_entries_order);
			
			// If we don't have any entries / drafts to display
			if(empty($submitted_entries_order) || count($submitted_entries_order) == 0)
			{
				return "<p>There are currently no items requiring approval.</p>";
			}
			else
			{
				return $this->_build_submitted_list($submitted_entries_data, $submitted_entries_order);
			}
		} 
		else
		{	
			return "<p>You are not authorised to publish entries.</p>";
		}
	}



	function _build_submitted_list($submitted_entries_data, $submitted_entries_order) {
		
		// Start our return string
		$data = "<ul id=\"bwf_acc_entry_list\">\n";
		
		$i = 1;
		$j = 1;
		
		foreach ($submitted_entries_order as $key=>$value)
		{
			// We only want the first nine records
			if($j < 10)
			{
				$edit_url = BASE.AMP.'C=content_publish'.AMP.'M=entry_form'.AMP.'channel_id='.$submitted_entries_data[$key]['channel_id'].AMP.'entry_id='.$submitted_entries_data[$key]['entry_id'];

				if($i == 1) {
					$data .= "<li class=\"bwf_acc_first ";
				} elseif ($i == 3) {
					$data .= "<li class=\"bwf_acc_last ";
				} else {
					$data .= "<li class=\"";
				}
				
				if($submitted_entries_data[$key]['type'] == 'entry') {
					$data .= "bwf_acc_status_submitted\">\n";
				} else {
					$data .= "bwf_acc_status_open_submitted\">\n";
				}
				
				$data .= "<a href=".$edit_url.">";
					$data .= "<p>".$submitted_entries_data[$key]['title']."</p>\n";
					$data .= "<ul class=\"bwf_acc_metadata\">\n";
						$data .= "<li>".$this->EE->ep_approval_list->get_channel_name($submitted_entries_data[$key]['channel_id'])."</li>\n";
						$data .= "<li>".$this->EE->ep_approval_list->get_author_name($submitted_entries_data[$key]['author_id'])."</li>\n";
						$data .= "<li class=\"bwf_acc_metadata_last\">Last edited: ".gmdate('F jS Y', $submitted_entries_data[$key]['edit_date'])."</li>\n";
					$data .= "</ul>\n";
				$data .= "</a></li>\n";
			}

			if($i == 3) { 
				$i = 1; 
			} else { 
				$i++; 
			}
			$j++;
		}
		
		$data .= "</ul>\n";
		$data .= "<p><a href=\"".BASE.AMP."C=content_edit\">See all entries</a></p>";
		
		return $data;
		
	}



	function update()
	{
		return TRUE;
	}
	
}
/* End of file mcp.ep_better_workflow.php */
/* Location: /system/expressionengine/third_party/ep_better_workflow/mcp.ep_better_workflow.php */