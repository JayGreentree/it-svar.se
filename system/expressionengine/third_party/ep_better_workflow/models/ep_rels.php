<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Ep_rels Model
 *
 * ----------------------------------------------------------------------------------------------
 * @package	EE2 
 * @subpackage	ThirdParty
 * @author	Andrea Fiore / Malcolm Elsworth 
 * @link	http://electricputty.co.uk 
 * @copyright	Copyright (c) 2011 Electric Putty Ltd.
 *
 */

class Ep_rels extends CI_Model {



	var $has_draft;



	function Ep_rels()
	{
		parent::__construct();
	}



	function get_all_fields($field_type) 
	{	
		$this->db->select('field_id, field_related_to, field_related_id');
		return $this->db->get_where('channel_fields', array('field_type' => $field_type));
	}



	function get_fields_in_data($data)
	{
		$all_rel_fields = $this->get_all_fields('rel');
		$rel_fields_in_data = array();
		$looper = 0;

		if ($all_rel_fields->num_rows() > 0)
		{
			foreach ($all_rel_fields->result_array() as $row)
			{
				$this_field_id = 'field_id_'.$row['field_id'];
				if (isset($data[$this_field_id]))
				{
					$rel_fields_in_data[] = $this_field_id ;
				}
			}
		}
		return $rel_fields_in_data;
	}



	function get_current_data($this_field_id, $this_entry_id)
	{
		$rel_id = "";
		
		$this->db->select($this_field_id);
		$query = $this->db->get_where('channel_data', array('entry_id' => $this_entry_id));
		
		if ($query->num_rows() > 0)
		{
			foreach ($query->result_array() as $row)
			{
				$rel_id = $row[$this_field_id];
			}
		}

		// In version 2.1.1, the check for an existing, unchanged relationship in _build_relationships 
		// compared the rel_id in channel data with the submitted rel_child_id - this would *never* match 
		// So we grabbed the rel_id from the saved sata and inserted this to trick the function into not updating anything ...
		// By 2.1.3 this was fixed so that the check compared the saved rel_child_id against the submitted data,
		// We need to branch the code here to cater for the older version
		if (version_compare(APP_VER, '2.1.1', '>'))
		{
			return $this->_get_current_rel_child_id($rel_id);
		}
		else
		{
			return $rel_id;
		}
	}



	function _get_current_rel_child_id($rel_id)
	{
		$entry_id = '';

		$this->db->select('rel_child_id');
		$query = $this->db->get_where('relationships', array('rel_id' => $rel_id));
		if ($query->num_rows() > 0)
		{
			foreach ($query->result_array() as $row)
			{
				$entry_id = $row['rel_child_id'];
			}
		}
		
		return $entry_id;
	}



	// This is pretty much lifted from the Channel Entries API but as that method is private 
	// and doesn't return the data we need we've added it here
	function build_relationships($data)
	{
		$entry_id = $data['entry_id'];
		
		// If we have a draft, delete all existing relationships for it ready to create new ones.
		if($this->has_draft)
		{
			// Unserialise the data and get the stored value for this relationship
			$stored_data = unserialize($this->has_draft->draft_data);

			// Do we have any cached relationship fields...
			if (isset($this->rel_fields) && count($this->rel_fields)>0)
			{
				foreach($this->rel_fields as $arr)
				{
					$this->_delete_relationship($stored_data[$arr['id']], $entry_id);
				}
			}
		}
		
		// Now we're all clean, create the new relationships and update the data array
		if (isset($this->rel_fields) && count($this->rel_fields)>0)
		{
			foreach($this->rel_fields as $arr)
			{
				$reldata = array(
					'type'		=> $arr['type'],
					'parent_id'	=> $data['entry_id'], 
					'child_id'	=> $data[$arr['id']],
					'related_id'	=> $data['channel_id']
				);
				
				$new_rel_id = '';
				
				// If we don't have a relationship child ID, don't try and build the relationship
				if($reldata['child_id'] != '')
				{
					//var_dump($reldata);
					//echo("<br />");
					//echo("Field name: ".$arr['id']);
					//echo("<br />");
					//echo("Data current value: ".$data[$arr['id']]);
					//echo("<br />");

					$new_rel_id = $this->_insert_relationship($reldata);
					//$new_rel_id = $this->functions->compile_relationship($reldata, FALSE);
				}
				 
				$data[$arr['id']] = (STRING)$new_rel_id;
			}
		}

		return $data;
	}



	function delete_existing_relationships($entry_id = '', $entry_data = array())
	{
		// Build up an array of rel ids we want to keep
		$current_rel_ids = array();

		// Get the field ids for all the relationships in this channel
		$channel_rel_fields = $this->get_fields_in_data($entry_data);

		// Loop through all the relationship field in the data array...
		if (count($channel_rel_fields)>0)
		{
			foreach($channel_rel_fields as $field_id)
			{
				$current_rel_ids[] = $entry_data[$field_id];
			}

			// Now delete any relationships for this entry which don't appear in the array as long as we have some
			if(!empty($current_rel_ids) && count($current_rel_ids)>0)
			{
				$this->db->where(array('rel_parent_id' => $entry_id));
				$this->db->where_not_in('rel_id', $current_rel_ids);
				$this->db->delete('relationships');
			}

		}

		// Now kill any rel_data or reverse_rel data for the relationships for this entry
		if($entry_id != '')
		{
			$data = array('rel_data' => '', 'reverse_rel_data' => '');
			$this->db->where('rel_parent_id', $entry_id);
			$this->db->update('relationships', $data); 
		}
	}



	private function _delete_relationship($rel_id = '', $entry_id = '')
	{
		// TODO: Run a test to ensure the live entry isn't using this relationship
		$this->db->where_in('rel_id', $rel_id);
		$this->db->delete('relationships');
	}



	private function _insert_relationship($data)
	{
		// Now passing in two empty stings so MySQL servers set to strict error handling don't grumble about the not-null fields
		$sql = "INSERT INTO exp_relationships (rel_parent_id, rel_child_id, rel_type, rel_data, reverse_rel_data) VALUES ('".$data['parent_id']."', '".$data['child_id']."', '".$data['type']."', '', '')";		
		$this->db->query($sql);
		return $this->db->insert_id();
	}



}

