<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Low_select_entries extends Low_variables_type {

	public $info = array(
		'name'    => 'Select Entries',
		'version' => LOW_VAR_VERSION
	);

	public $default_settings = array(
		'show_future'     => 'y',
		'show_expired'    => '',
		'channels'        => array(),
		'categories'      => array(),
		'statuses'        => array(),
		'limit'           => '0',
		'orderby'         => 'title',
		'sort'            => 'asc',
		'multiple'        => 'y',
		'separator'       => 'pipe',
		'multi_interface' => 'select'
	);

	// --------------------------------------------------------------------

	/**
	 * Display settings sub-form for this variable type
	 *
	 * @param	mixed	$var_id			The id of the variable: 'new' or numeric
	 * @param	array	$var_settings	The settings of the variable
	 * @return	array
	 */
	public function display_settings($var_id, $var_settings)
	{
		// -------------------------------------
		//  Init return value
		// -------------------------------------

		$r = array();

		// -------------------------------------
		//  Build setting: Future & Expired entries
		// -------------------------------------

		$r[] = array(
			$this->setting_label(lang('future_entries')),
			'<label class="low-checkbox">'.form_checkbox(
				$this->input_name('show_future'),
				'y',
				($this->get_setting('show_future', $var_settings) == 'y')
			).lang('show_future').'</label>'
		);

		$r[] = array(
			$this->setting_label(lang('expired_entries')),
			'<label class="low-checkbox">'.form_checkbox(
				$this->input_name('show_expired'),
				'y',
				($this->get_setting('show_expired', $var_settings) == 'y')
			).lang('show_expired').'</label>'
		);

		// -------------------------------------
		//  Build setting: channels
		// -------------------------------------

		$query = ee()->db->select(array('channel_id', 'channel_title'))
		       ->from('channels')
		       ->where('site_id', $this->site_id)
		       ->order_by('channel_title', 'asc')
		       ->get();

		$all_channels = array('' => lang('select_any')) + low_flatten_results($query->result_array(), 'channel_title', 'channel_id');
		if ( ! ($selected = $this->get_setting('channels', $var_settings)))
		{
			$selected = array('');
		}

		$r[] = array(
			$this->setting_label(lang('channels')),
			form_multiselect($this->input_name('channels', TRUE), $all_channels, $selected, ' style="min-width:45%"')
		);

		// -------------------------------------
		//  Build setting: categories
		// -------------------------------------

		$query = ee()->db->select(array('group_id', 'group_name'))
		       ->from('category_groups')
		       ->where('site_id', $this->site_id)
		       ->order_by('group_name', 'asc')
		       ->get();

		if ($query->num_rows())
		{
			// Load and instantiate category API
			ee()->load->library('api');
			ee()->api->instantiate('channel_categories');

			// Init category arrays
			$all_categories = array('' => lang('select_any'));

			// Loop through groups and create category trees for each of those
			foreach ($query->result_array() AS $row)
			{
				$all_categories[$row['group_name']] = $this->_cat_tree($row['group_id']);
			}

			// Get selected, fallback to any
			if ( ! ($selected = $this->get_setting('categories', $var_settings)))
			{
				$selected = array('');
			}

			// Add the category option to the settings
			$r[] = array(
				$this->setting_label(lang('categories')),
				form_multiselect($this->input_name('categories', TRUE), $all_categories, $selected, ' style="min-width:45%"')
			);
		}

		// -------------------------------------
		//  Build setting: statuses
		// -------------------------------------

		// Get all statuses in groups
		$query = ee()->db->select(array('s.status', 'sg.group_name'))
		       ->from(array('statuses AS s', 'status_groups sg'))
		       ->where('s.group_id = sg.group_id')
		       ->where('s.site_id', $this->site_id)
		       ->where_not_in('status', array('open', 'closed'))
		       ->order_by('sg.group_name', 'asc')
		       ->order_by('s.status_order', 'asc')
		       ->get();

		// Init all statuses with any, open and closed
		$all_statuses = array(
			'' => lang('select_any'),
			'open' => lang('open'),
			'closed' => lang('closed')
		);

		// Add grouped statuses to all statuses
		foreach ($query->result_array() AS $row)
		{
			$all_statuses[$row['group_name']][$row['status']] = $row['status'];
		}

		// Check selected, fallback to any
		if ( ! ($selected = $this->get_setting('statuses', $var_settings)))
		{
			$selected = array('');
		}

		// Add to settings
		$r[] = array(
			$this->setting_label(lang('statuses')),
			form_multiselect($this->input_name('statuses', TRUE), $all_statuses, $selected, ' style="min-width:45%"')
		);

		// -------------------------------------
		//  Build setting: orderby & sort
		// -------------------------------------

		$orderby = form_dropdown(
			$this->input_name('orderby'),
			array(
				'title'      => lang('title'),
				'entry_date' => lang('entry_date')
			),
			$this->get_setting('orderby', $var_settings)
		);

		$sort = form_dropdown(
			$this->input_name('sort'),
			array(
				'asc'  => lang('order_asc'),
				'desc' => lang('order_desc')
			),
			$this->get_setting('sort', $var_settings)
		);

		$r[] = array(
			$this->setting_label(lang('orderby')),
			$orderby.' '.lang('in').' '.$sort
		);

		// -------------------------------------
		//  Build setting: limit
		// -------------------------------------

		$r[] = array(
			$this->setting_label(lang('limit')),
			form_dropdown(
				$this->input_name('limit'),
				array(
					'0'    => lang('all'),
					'25'   => '25',
					'50'   => '50',
					'100'  => '100',
					'250'  => '250',
					'500'  => '500',
					'1000' => '1000'
				),
				$this->get_setting('limit', $var_settings)
			)
		);

		// -------------------------------------
		//  Build setting: multiple?
		// -------------------------------------

		$multiple = $this->get_setting('multiple', $var_settings);

		$r[] = array(
			$this->setting_label(lang('allow_multiple_entries')),
			'<label class="low-checkbox">'.form_checkbox($this->input_name('multiple'), 'y', $multiple, 'class="low-allow-multiple"').
			lang('allow_multiple_entries_label').'</label>'
		);

		// -------------------------------------
		//  Build setting: separator
		// -------------------------------------

		$separator = $this->get_setting('separator', $var_settings);

		$r[] = array(
			$this->setting_label(lang('separator_character')),
			$this->separator_select($separator)
		);

		// -------------------------------------
		//  Build setting: multi interface
		// -------------------------------------

		$multi_interface = $this->get_setting('multi_interface', $var_settings);

		$r[] = array(
			$this->setting_label(lang('multi_interface')),
			$this->interface_select($multi_interface)
		);

		// -------------------------------------
		//  Return output
		// -------------------------------------

		return $r;
	}

	/**
	 * Prep category tree for select
	 */
	private function _cat_tree($group_id)
	{
		$select = array();

		if ($tree = ee()->api_channel_categories->category_tree($group_id))
		{
			foreach ($tree AS $cat)
			{
				$indent = ($cat[5] > 1) ? str_repeat(NBS.NBS, $cat[5]) : '';
				$select[$cat[0]] = $indent.$cat[1];
			}
		}

		return $select;
	}

	// --------------------------------------------------------------------

	/**
	 * Display input field for regular user
	 *
	 * @param	int		$var_id			The id of the variable
	 * @param	string	$var_data		The value of the variable
	 * @param	array	$var_settings	The settings of the variable
	 * @return	string
	 */
	public function display_input($var_id, $var_data, $var_settings)
	{
		// -------------------------------------
		//  Prep options
		// -------------------------------------

		$settings = array();

		foreach ($this->default_settings AS $key => $val)
		{
			$settings[$key] = $this->get_setting($key, $var_settings);
		}

		// -------------------------------------
		//  Prep current data
		// -------------------------------------

		$current = explode($this->separators[$settings['separator']], $var_data);
		$now = ee()->localize->now;

		// -------------------------------------
		//  Get entries
		// -------------------------------------

		ee()->db->select(array('t.entry_id', 't.title'))
		             ->from('channel_titles AS t');

		// Filter out future entries
		if ($settings['show_future'] != 'y')
		{
			ee()->db->where('t.entry_date <=', $now);
		}

		// Filter out expired entries
		if ($settings['show_expired'] != 'y')
		{
			ee()->db->where("(t.expiration_date > {$now} OR t.expiration_date = 0)");
		}

		// Filter by channel
		if ($filtered_channels = array_filter((array) $settings['channels']))
		{
			ee()->db->where_in('t.channel_id', $filtered_channels);
		}

		// Filter by category
		if ($filtered_categories = array_filter((array) $settings['categories']))
		{
			ee()->db->join('category_posts AS cp', 't.entry_id = cp.entry_id');
			ee()->db->where_in('cp.cat_id', $filtered_categories);
		}

		// Filter by status
		if ($filtered_statuses = array_filter((array) $settings['statuses']))
		{
			ee()->db->where_in('t.status', $filtered_statuses);
		}

		// Order by custom order
		ee()->db->order_by($settings['orderby'], $settings['sort']);

		// Limit entries
		if ($settings['limit'])
		{
			ee()->db->limit($settings['limit']);
		}

		$query = ee()->db->get();
		$entries = low_flatten_results($query->result_array(), 'title', 'entry_id');

		// -------------------------------------
		//  Create interface
		// -------------------------------------

		if ($settings['multiple'] == 'y' && $settings['multi_interface'] == 'drag-list')
		{
			$r = $this->drag_lists($var_id, $entries, $current);
		}
		else
		{
			$r = $this->select_element($var_id, $entries, $current, ($settings['multiple'] == 'y'));
		}

		// -------------------------------------
		//  Return select element
		// -------------------------------------

		return $r;
	}

	// --------------------------------------------------------------------

	/**
	 * Prep variable data for saving
	 *
	 * @param	int		$var_id			The id of the variable
	 * @param	mixed	$var_data		The value of the variable, array or string
	 * @param	array	$var_settings	The settings of the variable
	 * @return	string
	 */
	public function save_input($var_id, $var_data, $var_settings)
	{
		return is_array($var_data) ? implode($this->separators[$this->get_setting('separator', $var_settings)], $var_data) : $var_data;
	}

}