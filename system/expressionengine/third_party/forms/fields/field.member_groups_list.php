<?php if (!defined('BASEPATH')) die('No direct script access allowed');

/**
 * Channel Forms MEMBER GROUPS LIST field
 *
 * @package			DevDemon_Forms
 * @author			DevDemon <http://www.devdemon.com> - Lead Developer @ Parscale Media
 * @copyright 		Copyright (c) 2007-2011 Parscale Media <http://www.parscale.com>
 * @license 		http://www.devdemon.com/license/
 * @link			http://www.devdemon.com/forms/
 * @see				http://expressionengine.com/user_guide/development/fieldtypes.html
 */
class FormsField_member_groups_list extends FormsField
{

	/**
	 * Field info - Required
	 *
	 * @access public
	 * @var array
	 */
	public $info = array(
		'title'		=>	'Member Groups List',
		'name' 		=>	'member_groups_list',
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
		if (isset($field['settings']['store']) == FALSE) $field['settings']['store'] = 'group_name';

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

		$out = form_dropdown($field['form_name'], $this->get_groups($field['settings']), $data , ' class="'.$class.'" id="'.$field['form_elem_id'].'" ' );

		return $out;
	}

	// ********************************************************************************* //

	public function field_settings($settings=array(), $template=TRUE)
	{
		$vData = $settings;

		// -----------------------------------------
		// Settings
		// -----------------------------------------
		if (isset($vData['store']) == FALSE) $vData['store'] = 'group_title';

		return $this->EE->load->view('fields/member_groups_list', $vData, TRUE);
	}

	// ********************************************************************************* //

	private function get_groups($settings=array())
	{
		$groups = array();

		if (isset($settings['store']) == FALSE) $settings['store'] = 'group_name';

		$this->EE->db->select('group_title, group_id');
		$this->EE->db->from('exp_member_groups');
		$this->EE->db->where('site_id', $this->site_id);
		$this->EE->db->order_by('group_title', 'ASC');
		$query = $this->EE->db->get();

		if ($query->num_rows() == 0)
		{
			return array();
		}

		foreach ($query->result() as $row)
		{
			$to_store = ($settings['store'] == 'group_title') ? $row->group_title : $row->group_id;
			$groups[$to_store] = $row->group_title;
		}

		return $groups;
	}

	// ********************************************************************************* //

}

/* End of file field.member_groups_list.php */
/* Location: ./system/expressionengine/third_party/forms/fields/field.member_groups_list.php */
