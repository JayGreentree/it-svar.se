<?php if (!defined('BASEPATH')) die('No direct script access allowed');

/**
 * Channel Forms FORUMS LIST field
 *
 * @package			DevDemon_Forms
 * @author			DevDemon <http://www.devdemon.com> - Lead Developer @ Parscale Media
 * @copyright 		Copyright (c) 2007-2011 Parscale Media <http://www.parscale.com>
 * @license 		http://www.devdemon.com/license/
 * @link			http://www.devdemon.com/forms/
 * @see				http://expressionengine.com/user_guide/development/fieldtypes.html
 */
class FormsField_forums_list extends FormsField
{

	/**
	 * Field info - Required
	 *
	 * @access public
	 * @var array
	 */
	public $info = array(
		'title'		=>	'Forums List',
		'name' 		=>	'forums_list',
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

		// Is it installed?
		if ($this->EE->db->table_exists('exp_forum_boards') == TRUE)
		{
			// -----------------------------------------
			// Default Settings
			// -----------------------------------------
			if (isset($field['settings']['grouped']) == FALSE) $field['settings']['grouped'] = 'no';
			if (isset($field['settings']['store']) == FALSE) $field['settings']['store'] = 'forum_name';

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

			$out = form_dropdown($field['form_name'], $this->get_forums($field['settings']), $data , ' class="'.$class.'" id="'.$field['form_elem_id'].'" ' );

			return $out;
		}
	}

	// ********************************************************************************* //

	public function field_settings($settings=array(), $template=TRUE)
	{
		$vData = $settings;

		$vData['fboards'] = array();
		$vData['fcats'] = array();

		// Is it installed?
		if ($this->EE->db->table_exists('exp_forum_boards') == TRUE)
		{
			// -----------------------------------------
			// What Category Exist?
			// -----------------------------------------

			$query = $this->EE->db->select('board_id, board_label')->from('exp_forum_boards')->order_by('board_label', 'ASC')->get();

			foreach ($query->result() as $row)
			{
				$vData['fboards'][$row->board_id] = $row->board_label;
			}

			// -----------------------------------------
			// What Category Exist?
			// -----------------------------------------

			$query = $this->EE->db->select('forum_id, forum_name')->from('exp_forums')->where('forum_is_cat', 'y')->order_by('forum_name', 'ASC')->get();

			foreach ($query->result() as $row)
			{
				$vData['fcats'][$row->forum_id] = $row->forum_name;
			}
		}

		// -----------------------------------------
		// Settings
		// -----------------------------------------
		if (isset($vData['board']) == FALSE) $vData['board'] = '';
		if (isset($vData['categories']) == FALSE) $vData['categories'] = '';
		if (isset($vData['grouped']) == FALSE) $vData['grouped'] = 'yes';
		if (isset($vData['store']) == FALSE) $vData['store'] = 'forum_name';

		return $this->EE->load->view('fields/forums_list', $vData, TRUE);
	}

	// ********************************************************************************* //

	private function get_forums($settings)
	{
	    // What to store?
	    if (isset($settings['store']) == FALSE) $settings['store'] = 'screen_name';
	    if (isset($settings['board']) == FALSE) $settings['board'] = 1;

		$this->EE->db->select('f.forum_id, f.forum_name');
		$this->EE->db->from('exp_forums f');

		if (isset($settings['categories']) == TRUE && empty($settings['categories']) == FALSE)
		{
			$this->EE->db->where_in('f.forum_parent', $settings['categories']);
		}

		// Grouped?
		if (isset($settings['grouped']) == TRUE && $settings['grouped'] == 'yes')
		{
			$grouped = TRUE;
			$this->EE->db->join('exp_forums ff', 'f.forum_parent = ff.forum_id', 'left');
			$this->EE->db->select('ff.forum_name AS forum_name_parent');
			$this->EE->db->order_by('ff.forum_order', 'ASC');
			$this->EE->db->order_by('f.forum_order', 'ASC');
		}
		else
		{
			$grouped = FALSE;
			$this->EE->db->order_by('f.forum_order', 'ASC');
		}

		$this->EE->db->where('f.forum_is_cat', 'n');
		$this->EE->db->where('f.board_id', $settings['board']);

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
				$store = ($settings['store'] == 'forum_name') ? $row->forum_name : $row->forum_id;
				$out[$row->forum_name_parent][$store] = $row->forum_name;
			}
		}
		else
		{
			foreach ($query->result() as $row)
			{
				$store = ($settings['store'] == 'forum_name') ? $row->forum_name : $row->forum_id;
				$out[$store] = $row->forum_name;
			}
		}

		return $out;
	}

	// ********************************************************************************* //

}

/* End of file field.forums_list.php */
/* Location: ./system/expressionengine/third_party/forms/fields/field.forums_list.php */
