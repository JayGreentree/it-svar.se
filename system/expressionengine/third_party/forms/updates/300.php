<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class FormsUpdate_300
{

	/**
	 * Constructor
	 *
	 * @access public
	 *
	 * Calls the parent constructor
	 */
	public function __construct()
	{
		$this->EE =& get_instance();

		// Load dbforge
		$this->EE->load->dbforge();
	}

	// ********************************************************************************* //

	public function do_update()
	{
		// Add the parent_id Column
		if ($this->EE->db->field_exists('parent_id', 'forms_fields') == FALSE)
		{
			$fields = array( 'parent_id'	=> array('type' => 'INT',	'unsigned' => TRUE, 'default' => 0) );
			$this->EE->dbforge->add_column('forms_fields', $fields, 'ee_field_id');
		}

		// Add the parent_id Column
		if ($this->EE->db->field_exists('column_number', 'forms_fields') == FALSE)
		{
			$fields = array( 'column_number'	=> array('type' => 'SMALLINT',	'unsigned' => TRUE, 'default' => 0) );
			$this->EE->dbforge->add_column('forms_fields', $fields, 'parent_id');
		}

		// Add the parent_id Column
		if ($this->EE->db->field_exists('conditionals', 'forms_fields') == FALSE)
		{
			$fields = array( 'conditionals'	=> array('type' => 'TEXT') );
			$this->EE->dbforge->add_column('forms_fields', $fields, 'no_dupes');
		}

		// Add the show_label Column
		if ($this->EE->db->field_exists('show_label', 'forms_fields') == FALSE)
		{
			$fields = array( 'show_label'	=> array('type' => 'TINYINT',	'unsigned' => TRUE, 'default' => 1) );
			$this->EE->dbforge->add_column('forms_fields', $fields, 'required');
		}

		// Add the label_position Column
		if ($this->EE->db->field_exists('label_position', 'forms_fields') == FALSE)
		{
			$fields = array( 'label_position'	=> array('type' => 'VARCHAR',	'constraint' => 100, 'default' => 'auto') );
			$this->EE->dbforge->add_column('forms_fields', $fields, 'show_label');
		}

	}

	// ********************************************************************************* //

}

/* End of file 300.php */
/* Location: ./system/expressionengine/third_party/forms/updates/300.php */