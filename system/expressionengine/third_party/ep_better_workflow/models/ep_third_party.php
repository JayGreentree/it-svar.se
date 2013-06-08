<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Ep_third_party Model
 *
 * ----------------------------------------------------------------------------------------------
 * @package	EE2 
 * @subpackage	ThirdParty
 * @author	Andrea Fiore / Malcolm Elsworth 
 * @link	http://electricputty.co.uk 
 * @copyright	Copyright (c) 2011 Electric Putty Ltd.
 *
 */

class Ep_third_party extends CI_Model {



	function Ep_third_party()
	{
		parent::__construct();
	}



	function get_field_type($field_id) 
	{
		$this->db->select('*');
		return $this->db->get_where('channel_fields', array('field_id' => $field_id));
	}



	function get_all_fields($field_type) 
	{
		$this->db->select('field_id, field_related_to, field_related_id');
		return $this->db->get_where('channel_fields', array('field_type' => $field_type));
	}



	function update_draft_data($entry_id, $field_id, $field_type, $row_id, $row_order, $col_id, $col_data)
	{
		$this->db->insert('ep_entry_drafts_thirdparty', array('entry_id'=>$entry_id, 'field_id'=>$field_id, 'type'=>$field_type, 'row_id'=>$row_id, 'row_order'=>$row_order, 'col_id'=>$col_id, 'data'=>$col_data));
	}



	function delete_draft_data($where)
	{
		$this->db->delete('ep_entry_drafts_thirdparty', $where);
	}


}
