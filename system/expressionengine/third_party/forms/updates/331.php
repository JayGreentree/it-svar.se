<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class FormsUpdate_331
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
		$this->EE->load->library('forms_helper');
	}

	// ********************************************************************************* //

	public function do_update()
	{
		// Add the fentry_hash Column
		if ($this->EE->db->field_exists('fentry_hash', 'forms_entries') == FALSE)
		{
			$fields = array( 'fentry_hash'	=> array('type' => 'VARCHAR',	'constraint' => 35, 'default' => '') );
			$this->EE->dbforge->add_column('forms_entries', $fields, 'fentry_id');
		}

		// Grab all form entries and calculate their hash
		$query = $this->EE->db->select('fentry_id')->from('exp_forms_entries')->get();

		foreach ($query->result() as $row)
		{
			for ($i=0; $i < 50; $i++) {
				$fentry_hash = $this->EE->forms_helper->uuid(false);
				$query = $this->EE->db->query("SELECT fentry_id FROM exp_forms_entries WHERE fentry_hash = '{$fentry_hash}'");
				if ($query->num_rows() == 0) break;
			}

			$this->EE->db->set('fentry_hash', $fentry_hash);
			$this->EE->db->where('fentry_id', $row->fentry_id);
			$this->EE->db->update('exp_forms_entries');
		}
	}

	// ********************************************************************************* //

}

/* End of file 321.php */
/* Location: ./system/expressionengine/third_party/forms/updates/321.php */
