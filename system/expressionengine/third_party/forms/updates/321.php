<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class FormsUpdate_321
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
		// Grab all date fields
		$query = $this->EE->db->select('field_id, field_settings')->from('exp_forms_fields')->where('field_type', 'date')->get();

		foreach ($query->result() as $row)
		{
			$field_settings = unserialize($row->field_settings);

			if (isset($field_settings['date_input_type']) === TRUE &&  $field_settings['date_input_type'] == 'datepicker')
			{
				// Grab all field data
				$q2 = $this->EE->db->select('fentry_id, fid_'.$row->field_id)->from('exp_forms_entries')->where('fid_'.$row->field_id.' !=', '')->get();

				foreach ($q2->result() as $entry)
				{
					$data = @unserialize($entry->fid_7);

					if (is_array($data) == FALSE)
					{
						$data = explode('/', $entry->fid_7);

						if ($field_settings['datepicker_format'] == 'european')
						{
							$data = array('day' => $data[0], 'month' => $data[1], 'year' => $data[2]);
						}
						else
						{
							$data = array('day' => $data[1], 'month' => $data[0], 'year' => $data[2]);
						}

						$data = serialize($data);

						$this->EE->db->set('fid_'.$row->field_id, $data);
						$this->EE->db->where('fentry_id', $entry->fentry_id);
						$this->EE->db->update('exp_forms_entries');
					}
				}
			}
			else
			{
				continue;
			}
		}

	}

	// ********************************************************************************* //

}

/* End of file 321.php */
/* Location: ./system/expressionengine/third_party/forms/updates/321.php */
