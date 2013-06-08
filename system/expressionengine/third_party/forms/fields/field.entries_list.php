<?php if (!defined('BASEPATH')) die('No direct script access allowed');

/**
 * Channel Forms ENTRIES LIST field
 *
 * @package			DevDemon_Forms
 * @author			DevDemon <http://www.devdemon.com> - Lead Developer @ Parscale Media
 * @copyright 		Copyright (c) 2007-2011 Parscale Media <http://www.parscale.com>
 * @license 		http://www.devdemon.com/license/
 * @link			http://www.devdemon.com/forms/
 * @see				http://expressionengine.com/user_guide/development/fieldtypes.html
 */
class FormsField_entries_list extends FormsField
{

	/**
	 * Field info - Required
	 *
	 * @access public
	 * @var array
	 */
	public $info = array(
		'title' 	=>	'Entries List',
		'name' 		=>	'entries_list',
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

		$this->default_settings['channels'] = array();
		$this->default_settings['grouped'] = 'no';
		$this->default_settings['store'] = 'entry_title';
		$this->default_settings['form_element'] = 'select';
		$this->default_settings['order_by'] = 'title';
		$this->default_settings['sort'] = 'asc';
		$this->default_settings['show_expired'] = 'no';
	}

	// ********************************************************************************* //

	public function render_field($field=array(), $template=TRUE, $data)
	{
		$class = '';

		// Some Defaults
		$settings = array_merge($this->default_settings, $field['settings']);

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
			$entries = $this->get_entries($field['settings']);

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
			$entries = $this->get_entries($field['settings']);

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
			$entries = $this->get_entries($field['settings']);
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
			$entries = $this->get_entries($field['settings']);
			$out = form_dropdown($field['form_name'], $entries, $data , ' class=" '.$class.'" id="'.$field['form_elem_id'].'" ' );
		}


		return $out;
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

	public function field_settings($settings=array(), $template=TRUE)
	{
		$vData = array_merge($this->default_settings, $settings);

		// -----------------------------------------
		// What Channels Exists?
		// -----------------------------------------
		$vData['fboards'] = array();
		$query = $this->EE->db->select('channel_id, channel_title')->from('exp_channels')->order_by('channel_title', 'ASC')->get();

		foreach ($query->result() as $row)
		{
			$vData['dbchannels'][$row->channel_id] = $row->channel_title;
		}

		return $this->EE->load->view('fields/entries_list', $vData, TRUE);
	}

	// ********************************************************************************* //

	private function get_entries($settings)
	{
	    $settings = array_merge($this->default_settings, $settings);

		$this->EE->db->select('ct.entry_id, ct.title, ct.url_title');
		$this->EE->db->from('exp_channel_titles ct');

		if (isset($settings['channels']) == TRUE && empty($settings['channels']) == FALSE)
		{
			$this->EE->db->where_in('ct.channel_id', $settings['channels']);
		}

		// Grouped?
		if (isset($settings['grouped']) == TRUE && $settings['grouped'] == 'yes')
		{
			$grouped = TRUE;
			$this->EE->db->join('exp_channels c', 'c.channel_id = ct.channel_id', 'left');
			$this->EE->db->select('c.channel_title');
			$this->EE->db->order_by('c.channel_title', 'ASC');
		}
		else
		{
			$grouped = FALSE;
		}

		if ($settings['show_expired'] == 'no') {
			$this->EE->db->where('(ct.expiration_date = 0 OR ct.expiration_date > '.$this->EE->localize->now.')', null, false);
		}

		// Order By?
		switch ($settings['order_by'])
		{
			case 'title':
				$this->EE->db->order_by('ct.title', $settings['sort']);
				break;
			case 'date':
				$this->EE->db->order_by('ct.entry_date', $settings['sort']);
				break;
		}

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
					case 'entry_id':
						$out[$row->channel_title][$row->entry_id] = $row->title;
						break;
					case 'entry_title':
						$out[$row->channel_title][$row->title] = $row->title;
						break;
					case 'entry_url_title':
						$out[$row->channel_title][$row->url_title] = $row->title;
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
					case 'entry_id':
						$out[$row->entry_id] = $row->title;
						break;
					case 'entry_title':
						$out[$row->title] = $row->title;
						break;
					case 'entry_url_title':
						$out[$row->url_title] = $row->title;
						break;
				}
			}
		}

		return $out;
	}

	// ********************************************************************************* //

}

/* End of file field.entries_list.php */
/* Location: ./system/expressionengine/third_party/forms/fields/field.entries_list.php */
