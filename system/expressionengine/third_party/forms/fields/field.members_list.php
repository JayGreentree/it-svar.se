<?php if (!defined('BASEPATH')) die('No direct script access allowed');

/**
 * Channel Forms MEMBERS LIST field
 *
 * @package			DevDemon_Forms
 * @author			DevDemon <http://www.devdemon.com> - Lead Developer @ Parscale Media
 * @copyright 		Copyright (c) 2007-2011 Parscale Media <http://www.parscale.com>
 * @license 		http://www.devdemon.com/license/
 * @link			http://www.devdemon.com/forms/
 * @see				http://expressionengine.com/user_guide/development/fieldtypes.html
 */
class FormsField_members_list extends FormsField
{

	/**
	 * Field info - Required
	 *
	 * @access public
	 * @var array
	 */
	public $info = array(
		'title' 	=>	'Members List',
		'name' 		=>	'members_list',
		'category'	=>	'list_tools',
		'version'	=>	'1.0',
	);

	/**
	 * Constructor
	 *
	 * @access public
	 *
	 * Calls the parent constructor
	 */
	public function __construct()
	{
		parent::__construct();
	}

	// ********************************************************************************* //

	public function render_field($field=array(), $template=TRUE, $data)
	{
		$class = '';

		// -----------------------------------------
		// Default Settings
		// -----------------------------------------
		if (isset($field['settings']['grouped']) == FALSE) $field['settings']['grouped'] = 'no';
		if (isset($field['settings']['store']) == FALSE) $field['settings']['store'] = 'screen_name';

		// -----------------------------------------
		// Add JS Validation support
		// -----------------------------------------
		if ($template == TRUE)
		{
			if ($field['required'] == TRUE)
			{
				$class .= ' required validate[required] ';
			}
		}

		$out = form_dropdown($field['form_name'], $this->get_members($field['settings']), $data , ' class="'.$class.'" id="'.$field['form_elem_id'].'" ' );

		return $out;
	}

	// ********************************************************************************* //

	public function field_settings($settings=array(), $template=TRUE)
	{
		$vData = $settings;

		// -----------------------------------------
		// What Category Groups Exist?
		// -----------------------------------------
		$vData['mgroups'] = array();
		$query = $this->EE->db->select('group_id, group_title')->from('exp_member_groups')->where('site_id', $this->site_id)->order_by('group_title', 'ASC')->get();

		foreach ($query->result() as $row)
		{
			$vData['mgroups'][$row->group_id] = $row->group_title;
		}

		// -----------------------------------------
		// Settings
		// -----------------------------------------
		if (isset($vData['member_groups']) == FALSE) $vData['member_groups'] = '';
		if (isset($vData['grouped']) == FALSE) $vData['grouped'] = 'yes';
		if (isset($vData['store']) == FALSE) $vData['store'] = 'screen_name';

		return $this->EE->load->view('fields/members_list', $vData, TRUE);
	}

	// ********************************************************************************* //

	private function get_members($settings)
	{
	    // What to store?
	    if (isset($settings['store']) == FALSE) $settings['store'] = 'screen_name';

		$this->EE->db->select('m.member_id, m.email, m.username, m.screen_name');
		$this->EE->db->from('exp_members m');

		if (isset($settings['member_groups']) == TRUE && empty($settings['member_groups']) == FALSE)
		{
			$this->EE->db->where_in('m.group_id', $settings['member_groups']);
		}

		// Grouped?
		if (isset($settings['grouped']) == TRUE && $settings['grouped'] == 'yes')
		{
			$grouped = TRUE;
			$this->EE->db->join('exp_member_groups mg', 'm.group_id = mg.group_id', 'left');
			$this->EE->db->select('mg.group_title');
			$this->EE->db->order_by('mg.group_title', 'ASC');
			$this->EE->db->order_by('m.screen_name', 'ASC');
			$this->EE->db->where('mg.site_id', $this->site_id);
		}
		else
		{
			$grouped = FALSE;
			$this->EE->db->order_by('m.screen_name', 'ASC');
		}

		// Limit by group?

		$query = $this->EE->db->get();

		if ($query->num_rows() == 0)
		{
			return array();
		}

		$out = array();

		// Do we need to group them?
		if ($grouped == TRUE)
		{
			foreach ($query->result() as $row)
			{
				switch ($settings['store'])
				{
					case 'member_id':
						$out[$row->group_title][$row->member_id] = $row->screen_name;
						break;
					case 'email':
						$out[$row->group_title][$row->email] = $row->screen_name;
						break;
					case 'username':
						$out[$row->group_title][$row->username] = $row->screen_name;
						break;
					case 'screen_name':
						$out[$row->group_title][$row->screen_name] = $row->screen_name;
						break;
				}
			}
		}
		else
		{
			foreach ($query->result() as $row)
			{
				switch ($settings['store'])
				{
					case 'member_id':
						$out[$row->member_id] = $row->screen_name;
						break;
					case 'email':
						$out[$row->email] = $row->screen_name;
						break;
					case 'username':
						$out[$row->username] = $row->screen_name;
						break;
					case 'screen_name':
						$out[$row->screen_name] = $row->screen_name;
						break;
				}
			}
		}

		return $out;
	}

	// ********************************************************************************* //

}

/* End of file field.members_list.php */
/* Location: ./system/expressionengine/third_party/forms/fields/field.members_list.php */
