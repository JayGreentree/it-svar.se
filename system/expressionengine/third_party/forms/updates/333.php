<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class FormsUpdate_333
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
		if ($this->EE->db->field_exists('debug_info', 'forms_entries') == FALSE)
		{
			$fields = array( 'debug_info'	=> array('type' => 'TEXT') );
			$this->EE->dbforge->add_column('forms_entries', $fields, 'email');
		}
	}

	// ********************************************************************************* //

}

/* End of file 333.php */
/* Location: ./system/expressionengine/third_party/forms/updates/333.php */
