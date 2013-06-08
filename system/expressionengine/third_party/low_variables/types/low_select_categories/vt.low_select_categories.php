<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Low_select_categories extends Low_variables_type {

	public $info = array(
		'name'		=> 'Select Categories',
		'version'	=> LOW_VAR_VERSION
	);

	public $default_settings = array(
		'multiple'			=> 'y',
		'category_groups'	=> array(),
		'separator'			=> 'pipe'
	);

	// --------------------------------------------------------------------

	/**
	 * Display settings sub-form for this variable type
	 *
	 * @param	mixed	$var_id			The id of the variable: 'new' or numeric
	 * @param	array	$var_settings	The settings of the variable
	 * @return	array
	 */
	function display_settings($var_id, $var_settings)
	{
		// -------------------------------------
		//  Init return value
		// -------------------------------------

		$r = array();

		// -------------------------------------
		//  Build setting: category groups
		//  First, get all groups for this site
		// -------------------------------------

		$query = ee()->db->select('group_id, group_name')
		       ->from('category_groups')
		       ->where('site_id', ee()->config->item('site_id'))
		       ->order_by('group_name')
		       ->get();

		$all_groups = $this->flatten_results($query->result_array(), 'group_id', 'group_name');

		// -------------------------------------
		//  Then, get current groups from settings
		// -------------------------------------

		$current = $this->get_setting('category_groups', $var_settings);

		$r[] = array(
			$this->setting_label(lang('category_groups')),
			form_multiselect($this->input_name('category_groups', TRUE), $all_groups, $current)
		);

		// -------------------------------------
		//  Build setting: multiple?
		// -------------------------------------

		$multiple = $this->get_setting('multiple', $var_settings);

		$r[] = array(
			$this->setting_label(lang('allow_multiple_categories')),
			'<label class="low-checkbox">'.form_checkbox($this->input_name('multiple'), 'y', $multiple, 'class="low-allow-multiple"').
			lang('allow_multiple_categories_label').'</label>'
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

	// --------------------------------------------------------------------

	/**
	 * Display input field for regular user
	 *
	 * @param	int		$var_id			The id of the variable
	 * @param	string	$var_data		The value of the variable
	 * @param	array	$var_settings	The settings of the variable
	 * @return	string
	 */
	function display_input($var_id, $var_data, $var_settings)
	{
		// -------------------------------------
		//  Prep options
		// -------------------------------------

		$category_groups = $this->get_setting('category_groups', $var_settings);
		$multiple = $this->get_setting('multiple', $var_settings);
		$separator = $this->get_setting('separator', $var_settings);
		$multi_interface = $this->get_setting('multi_interface', $var_settings);

		// -------------------------------------
		//  Prep current data
		// -------------------------------------

		$current = explode($this->separators[$separator], $var_data);

		// -------------------------------------
		//  No groups? Bail.
		// -------------------------------------

		if (empty($category_groups))
		{
			return lang('no_category_groups_selected');
		}

		// -------------------------------------
		//  Load category API and fetch categories
		// -------------------------------------

		ee()->load->library('api');
		ee()->api->instantiate('channel_categories');

		$categories = ee()->api_channel_categories->category_tree($category_groups);

		// -------------------------------------
		//  Compose nested category array
		// -------------------------------------

		$cats = array();

		if ($multiple == 'y' && $multi_interface == 'drag-list')
		{
			// This will return a flat list of categories
			$cats = low_flatten_results($categories, '1', '0');

			// So we need to sort alphabetically again
			asort($cats);

			// Then assign to output
			$r = $this->drag_lists($var_id, $cats, $current);
		}
		else
		{
			// Loop thru tree and create nested array accordingly
			foreach ($categories AS $row)
			{
				// Category name
				$cat = $row['1'];

				// Check indent level
				if ($row['5'] > 1)
				{
					$cat = str_repeat('-', $row['5'] - 1).' '.$cat;
				}

				$cats[$row['3']][$row['0']] = $cat;
			}

			// Sort if multiple groups
			if (count($cats) > 1)
			{
				ksort($cats);
			}

			// Assign regular select element to output
			$r = $this->select_element($var_id, $cats, $current, ($multiple == 'y'));
		}

		// -------------------------------------
		//  Return interface
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
	function save_input($var_id, $var_data, $var_settings)
	{
		return is_array($var_data) ? implode($this->separators[$this->get_setting('separator', $var_settings)], $var_data) : $var_data;
	}

}