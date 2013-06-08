<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class FormsUpdate_324
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
		$fields = $this->EE->db->field_data('exp_forms_fields');

		foreach ($fields as $field)
		{
			if ($field->name == 'description' && $field->type == 'string')
			{
				$fields = array('description' => array( 'name' => 'description', 'type' => 'TEXT'));
				$this->EE->dbforge->modify_column('forms_fields', $fields);
			}
		}
	}

	// ********************************************************************************* //

}

/* End of file 321.php */
/* Location: ./system/expressionengine/third_party/forms/updates/321.php */
