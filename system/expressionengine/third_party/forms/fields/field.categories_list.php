<?php if (!defined('BASEPATH')) die('No direct script access allowed');

/**
 * Channel Forms CATEGORIES LIST field
 *
 * @package			DevDemon_Forms
 * @author			DevDemon <http://www.devdemon.com> - Lead Developer @ Parscale Media
 * @copyright 		Copyright (c) 2007-2011 Parscale Media <http://www.parscale.com>
 * @license 		http://www.devdemon.com/license/
 * @link			http://www.devdemon.com/forms/
 * @see				http://expressionengine.com/user_guide/development/fieldtypes.html
 */
class FormsField_categories_list extends FormsField
{

	/**
	 * Field info - Required
	 *
	 * @access public
	 * @var array
	 */
	public $info = array(
		'title'		=>	'Categories List',
		'name' 		=>	'categories_list',
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

		$this->default_settings['cat_groups'] = 'yes';
		$this->default_settings['grouped'] = 'yes';
		$this->default_settings['store'] = 'cat_name';
		$this->default_settings['form_element'] = 'select';
	}

	// ********************************************************************************* //

	public function render_field($field=array(), $template=TRUE, $data)
	{
		$settings = array_merge($this->default_settings, $field['settings']);
		$class = '';

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

		// -----------------------------------------
		// Do we have any previous submits!
		// -----------------------------------------
		$check_submit = FALSE;
		if (is_array($data) == TRUE && empty($data) == FALSE)
		{
			$data = array_flip($data);
			$check_submit = TRUE;
		}


		if ($settings['form_element'] == 'radio')
		{
			$field['settings']['grouped'] = 'no';
			$entries = $this->get_categories($field['settings']);

			$out = '';
			$out .= '<ul class="radios">';

			foreach ($entries as $storage => $label)
			{
				// Check checked!
				$checked = FALSE;
				if ($data == $storage) $checked = TRUE;

				$out .= '<li><label>' . form_radio($field['form_name'], $storage, $checked, ' class="'.$class.'" ') . '&nbsp; '.$label.'</label></li>';
			}

			$out .= '</ul>';
		}
		elseif ($settings['form_element'] == 'checkbox')
		{
			$field['settings']['grouped'] = 'no';
			$entries = $this->get_categories($field['settings']);

			$out = '';
			$out .= '<ul class="checkboxes">';

			foreach ($entries as $storage => $label)
			{
				// Check checked!
				$checked = FALSE;

				// Now Check for returned val!
				if ($check_submit == TRUE)
				{
					if (isset($data[$storage]) == TRUE) $checked = TRUE;
					else $checked = FALSE;
				}

				$out .= '<li><label>' . form_checkbox($field['form_name'].'[]', $storage, $checked, ' class="'.$class.'" ') . '&nbsp; '.$label.'</label></li>';
			}

			$out .= '</ul>';
		}
		elseif ($settings['form_element'] == 'multiselect')
		{
			$entries = $this->get_categories($field['settings']);
			$out = form_multiselect($field['form_name'].'[]', $entries, $data , ' class="chzn-select '.$class.'" ' );

			if ( isset($this->EE->session->cache['DevDemon']['JS']['jquery.chosen']) == FALSE && $template == TRUE)
			{
				$url = $this->EE->forms_helper->define_theme_url();
				$out .= $this->EE->forms_helper->output_js_buffer('<script src="' . $url . 'chosen/jquery.chosen.js" type="text/javascript"></script>');
				$out .= '<link rel="stylesheet" href="' . FORMS_THEME_URL . 'chosen/chosen.css" type="text/css" media="print, projection, screen" />';
			}

			if ($template == FALSE) $out .= '<script type="text/javascript">jQuery("select.chzn-select").css({width:"80%"}).chosen();</script>';
			else $out .= $this->EE->forms_helper->output_js_buffer('<script type="text/javascript">jQuery("select.chzn-select").chosen();</script>');
		}
		else
		{
			$entries = $this->get_categories($field['settings']);
			$out = form_dropdown($field['form_name'], $entries, $data , ' class=" '.$class.'" id="'.$field['form_elem_id'].'" ' );
		}

		return $out;
	}

	// ********************************************************************************* //

	public function field_settings($settings=array(), $template=TRUE)
	{
		$vData = array_merge($this->default_settings, $settings);

		// -----------------------------------------
		// What Category Groups Exist?
		// -----------------------------------------
		$vData['category_groups'] = array();
		$query = $this->EE->db->select('group_id, group_name')->from('exp_category_groups')->where('site_id', $this->site_id)->order_by('group_name', 'ASC')->get();

		foreach ($query->result() as $row)
		{
			$vData['category_groups'][$row->group_id] = $row->group_name;
		}

		return $this->EE->load->view('fields/categories_list', $vData, TRUE);
	}

	// ********************************************************************************* //

	public function save($field=array(), $data)
	{
		return is_array($data) ? serialize($data) : $data;
	}

	// ********************************************************************************* //

	public function output_data($field=array(), $data, $type='template')
	{
		$arr_data = @unserialize($data);

		if (is_array($arr_data) === FALSE)
		{
			return $data;
		}

		if (isset($this->EE->TMPL->tagparams['format_multiple']) == TRUE)
		{
			$out = '';
			foreach ($arr_data as $val)
			{
				if ($val == FALSE) continue;
				$out .= str_replace('%value%', $val, $this->EE->TMPL->tagparams['format_multiple']);
			}
			return $out;
		}

		return implode(', ', $arr_data);
	}

	// ********************************************************************************* //

	private function get_categories($settings)
	{
	    // What to store?
	    $store = 'cat_name';
	    if (isset($settings['store']) == TRUE && $settings['store'] == 'cat_id')
	    {
	        $store = 'cat_id';
	    }

		$this->EE->db->select('c.cat_id, c.cat_name');
		$this->EE->db->from('exp_categories c');

		if (isset($settings['cat_groups']) == TRUE && empty($settings['cat_groups']) == FALSE)
		{
			$this->EE->db->where_in('c.group_id', $settings['cat_groups']);
		}

		if (isset($settings['grouped']) == TRUE && $settings['grouped'] == 'yes')
		{
			$grouped = TRUE;
			$this->EE->db->join('exp_category_groups cg', 'c.group_id = cg.group_id', 'left');
			$this->EE->db->select('cg.group_name');
			$this->EE->db->order_by('cg.group_name', 'ASC');
			$this->EE->db->order_by('c.cat_name', 'ASC');
		}
		else
		{
			$grouped = FALSE;
			$this->EE->db->order_by('c.cat_name', 'ASC');
		}

		$this->EE->db->where('c.site_id', $this->site_id);

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
			    $to_store = ($store == 'cat_name') ? $row->cat_name : $row->cat_id;
			    $out[$row->group_name][$to_store] = $row->cat_name;
			}
		}
		else
		{
			foreach ($query->result() as $row)
			{
				$to_store = ($store == 'cat_name') ? $row->cat_name : $row->cat_id;
				$out[$to_store] = $row->cat_name;
			}
		}

		return $out;
	}

	// ********************************************************************************* //

}

/* End of file field.categories_list.php */
/* Location: ./system/expressionengine/third_party/forms/fields/field.categories_list.php */
