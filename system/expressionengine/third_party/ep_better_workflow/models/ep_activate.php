<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Ep_install Model
 *
 * ----------------------------------------------------------------------------------------------
 * @package	EE2 
 * @subpackage	ThirdParty
 * @author	Malcolm Elsworth 
 * @link	http://electricputty.co.uk 
 * @copyright	Copyright (c) 2011 Electric Putty Ltd.
 *
 */

class Ep_activate extends CI_Model {


	var $class_name;
	var $version;
	var $current;


	function Ep_activate()
	{
		parent::__construct();
	}



	function register_hooks()
	{
		$hooks = array();

		// Core EE hooks
		$hooks[] = array('insert','on_sessions_start','sessions_start',8);
		$hooks[] = array('insert','on_entry_submission_start','entry_submission_start',10);
		$hooks[] = array('insert','on_entry_submission_ready','entry_submission_ready',10);
		$hooks[] = array('insert','on_entry_submission_end','entry_submission_end',10);
		$hooks[] = array('insert','on_publish_form_entry_data','publish_form_entry_data',10);
		$hooks[] = array('insert','on_publish_form_channel_preferences','publish_form_channel_preferences',10);
		$hooks[] = array('insert','on_channel_entries_row','channel_entries_row',10);
		$hooks[] = array('insert','on_channel_entries_query_result','channel_entries_query_result',8);
		$hooks[] = array('insert','on_cp_js_end','cp_js_end',10);
		$hooks[] = array('insert','on_template_post_parse', 'template_post_parse',100);

		// Zenbu hooks
		$hooks[] = array('insert','on_zenbu_filter_by_status','zenbu_filter_by_status',100);
		$hooks[] = array('insert','on_zenbu_modify_status_display','zenbu_modify_status_display',100);
		$hooks[] = array('insert','on_zenbu_modify_title_display','zenbu_modify_title_display',100);

		$this->_process_hooks($hooks);
	}



	function create_tables()
	{
		$this->load->dbforge();
		$ep_entry_drafts_fields = array(
			'ep_entry_drafts_id' => array(
				'type' => 'int',
				'constraint' => '10',
				'unsigned' => TRUE,
				'auto_increment' => TRUE,),
			'entry_id' => array(
				'type' => 'int',
				'constraint' => '10',
				'null' => FALSE,),
			'author_id' => array(
				'type' => 'int',
				'constraint' => '10',
				'null' => FALSE,),
			'channel_id' => array(
				'type' => 'int',
				'constraint' => '10',
				'null' => FALSE,),
				'site_id' => array(
				'type' => 'int',
				'constraint' => '10',
				'null' => FALSE,),
			'status' => array(
				'type' => 'varchar',
				'constraint' => '255',
				'null' => FALSE,),
			'url_title' => array(
				'type' => 'varchar',
				'constraint' => '255',
				'null' => FALSE,),
			'draft_data' => array(
				'type' => 'text',),
			'expiration_date' => array(
				'type' => 'int',
				'constraint' => '10',
				'null' => FALSE,),
			'edit_date' => array(
				'type' => 'int',
				'constraint' => '10',
				'null' => FALSE,),
			'entry_date' => array(
				'type' => 'int',
				'constraint' => '10',
				'null' => FALSE,),
			'publish_date' => array(
				'type' => 'int',
				'constraint' => '10',
				'null' => FALSE,),
		);
		$this->dbforge->add_field($ep_entry_drafts_fields);
		$this->dbforge->add_key('ep_entry_drafts_id', TRUE);
		$this->dbforge->create_table('ep_entry_drafts', TRUE);


		$ep_entry_drafts_auth_fields = array(
			'ep_auth_id' => array(
				'type' => 'int',
				'constraint' => '10',
				'unsigned' => TRUE,
				'auto_increment' => TRUE,),
			'token' => array(
				'type' => 'varchar',
				'constraint' => '255',
				'null' => FALSE,),
			'timestamp' => array(
				'type' => 'int',
				'constraint' => '10',
				'null' => FALSE,),
		);
		$this->dbforge->add_field($ep_entry_drafts_auth_fields);
		$this->dbforge->add_key('ep_auth_id', TRUE);
		$this->dbforge->create_table('ep_entry_drafts_auth', TRUE);


		$ep_settings_fields = array(
			'site_id' => array(
				'type' => 'int',
				'constraint' => '10',
				'null' => FALSE,),
			'class' => array(
				'type' => 'varchar',
				'constraint' => '255',
				'null' => FALSE,),
			'settings' => array(
				'type' => 'text'),
		);
		$this->dbforge->add_field($ep_settings_fields);
		$this->dbforge->add_key('site_id', TRUE);
		$this->dbforge->create_table('ep_settings', TRUE);


		$ep_roles_fields = array(
			'site_id' => array(
				'type' => 'int',
				'constraint' => '10',
				'null' => FALSE,),
			'role' => array(
				'type' => 'varchar',
				'constraint' => '255',
				'null' => FALSE,),
			'states' => array(
				'type' => 'text'),
		);
		$this->dbforge->add_field($ep_roles_fields);
		$this->dbforge->add_key(array('site_id', 'role'), TRUE);
		$this->dbforge->create_table('ep_roles', TRUE);
	}



	function remove_bwf()
	{
		$this->db->where('class', $this->class_name);
		$this->db->delete('extensions');
		
		$this->load->dbforge();
		$this->dbforge->drop_table('ep_entry_drafts');
		$this->dbforge->drop_table('ep_entry_drafts_thirdparty');
		$this->dbforge->drop_table('ep_entry_drafts_auth');
		$this->dbforge->drop_table('ep_roles');
		
		// Delete the settings from ep_settings
		// But do not drop the table as it may be being used by other ep Add-Ons
		$this->db->where('class', $this->class_name);
		$this->db->delete('ep_settings');
	}



	function update_bwf()
	{
		if ($this->current == '' OR $this->current == $this->version)
		{
			return FALSE;
		}
		
		if ($this->current < $this->version)
		{
			// Run the table creation - this will only add the tables that don't alreay exist
			$this->create_tables();
			
			// Collect all db and hook update instructions
			$db_updates = array();
			$hook_updates = array();
			
			// Load models
			$this->load->model('ep_settings');
			$this->load->model('ep_roles');
			
			// Update to version 1.1 - Register zenbu hooks
			$hook_updates[] = array('insert','on_zenbu_filter_by_status', 'zenbu_filter_by_status', 100);
			$hook_updates[] = array('insert','on_zenbu_modify_status_display', 'zenbu_modify_status_display', 100);
			$hook_updates[] = array('insert','on_zenbu_modify_title_display', 'zenbu_modify_title_display', 100);
			
			// Update to version 1.2 - Update hooks priority to play nicely with Transcribe
			$hook_updates[] = array('update','on_sessions_start','sessions_start', 8);
			$hook_updates[] = array('update','on_channel_entries_query_result','channel_entries_query_result', 8);

			// Update to version 1.3 - Register new Playa hook
			$hook_updates[] = array('insert','on_playa_fetch_rels_query','playa_fetch_rels_query', 10);
						
			// Update to version 1.3
			$this->ep_settings->update_setting(array('advanced', 'enable_preview_on_new_entries'), array('advanced', 'disable_preview_on_new_entries'), array('yes' => 'no', 'no' => 'yes'));
			$this->ep_settings->update_setting(array('advanced', 'use_zenbu'), array('advanced', 'redirect_on_action'), array('yes' => 'zenbu', 'no' => 'EE Edit List'));
		
			$this->ep_roles->initialise($this->config->item('site_id'));
			$this->ep_roles->populate_roles();
			
			// Update to version 1.4
			$hook_updates[] = array('insert','on_template_post_parse', 'template_post_parse', 100);
			$db_updates[] = array('add_column', 'ep_entry_drafts_auth', array('timestamp' => array('type' => 'int', 'constraint' => '10', 'null' => FALSE)), '1.4');
			$db_updates[] = array('drop_table', 'ep_entry_status', '', '1.4');
			
			// Update to version 1.4.5 - Remove native Matrix support
			$hook_updates[] = array('delete','on_matrix_data_query','matrix_data_query',null);
			
			// Update to version 1.5.2
			$this->ep_settings->update_setting(array('advanced', 'disable_preview_on_new_entries'), array('advanced', 'disable_preview'), array('yes' => 'For new entries', 'no' => 'Never'));

			// Update to version 1.5.3 - Remove native Playa support
			$hook_updates[] = array('delete','on_playa_data_query','playa_data_query',null);
			$hook_updates[] = array('delete','on_playa_fetch_rels_query','playa_fetch_rels_query',null);
			
			// Update to version 1.6 - Remove proprietary third party table and add publish date to ep_entry_drafts: we'll need this soon ;-)
			$db_updates[] = array('drop_table', 'ep_entry_drafts_thirdparty', '', '1.6');
			$db_updates[] = array('add_column', 'ep_entry_drafts', array('publish_date' => array('type' => 'int','constraint' => '10','null' => FALSE)), '1.6');


			// ----------------------------------------------------------------------------------------------
			// Perform all updates
			// ----------------------------------------------------------------------------------------------
			$this->_process_hooks($hook_updates);
			$this->_update_tables($db_updates);
		}
		
		// Update the version number
		$this->db->where('class', $this->class_name);
		$this->db->update('extensions', array('version' => $this->version));
	}



	private function _process_hooks($hooks = array())
	{		
		foreach ($hooks as $hook){
			$data = array(
				'class'		=> $this->class_name,
				'method'	=> $hook[1],
				'hook'		=> $hook[2],
				'settings'	=> serialize(array()),
				'priority'	=> $hook[3],
				'version'	=> $this->version,
				'enabled'	=> 'y'
				);
				
			// Check to see if hook already exists
			$has_hook = $this->db->get_where('extensions', array('class' => $this->class_name, 'method' => $hook[1], 'hook' => $hook[2]));

			// If we are inserting a new hook 
			switch($hook[0]) {
			
				case 'insert':
					if ($has_hook->num_rows() == 0)
					{
						$this->db->insert('extensions', $data);
					}
				break;
				
				case 'delete':
					$this->db->delete('extensions', array('class' => $this->class_name, 'method' => $hook[1], 'hook' => $hook[2]));
				break;
				
				case 'update':
					if ($has_hook->num_rows() > 0)
					{
						$this->db->where(array('class' => $this->class_name, 'method' => $hook[1], 'hook' => $hook[2]));
						$this->db->update('extensions', $data);
					}
				break;
			}
		}
	}


	private function _update_tables($db_updates = array())
	{
		// Do we have any work to do here?
		if(count($db_updates)>0)
		{
			// Load DB Forge
			$this->load->dbforge();
			foreach ($db_updates as $update)
			{
				// Do we need to perform this database change
				if($this->current < $update[3])
				{
					switch ($update[0])
					{
						// Adding a column
						case 'add_column':
							$this->dbforge->add_column($update[1], $update[2]);
							break;

						// Drop a table
						case 'drop_table':
							$this->dbforge->drop_table($update[1]);
							break;
					}
				}
			}
		}
	}


}