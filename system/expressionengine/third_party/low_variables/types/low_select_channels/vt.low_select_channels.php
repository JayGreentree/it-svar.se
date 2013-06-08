<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Low_select_channels extends Low_variables_type {

	public $info = array(
		'name'		=> 'Select Channels',
		'version'	=> LOW_VAR_VERSION
	);

	public $default_settings = array(
		'multiple'			=> 'y',
		'channel_ids'		=> array(),
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
		//  Build setting: Channel ids
		//  First, get all channels for this site
		// -------------------------------------

		$query = ee()->db->query("SELECT channel_id, channel_title FROM exp_channels
							WHERE site_id = '".ee()->db->escape_str(ee()->config->item('site_id'))."'
							ORDER BY channel_title ASC");

		$all_channels = $this->flatten_results($query->result_array(), 'channel_id', 'channel_title');

		// -------------------------------------
		//  Then, get current channel ids from settings
		// -------------------------------------

		$current = $this->get_setting('channel_ids', $var_settings);

		$r[] = array(
			$this->setting_label(lang('channel_ids')),
			form_multiselect($this->input_name('channel_ids', TRUE), $all_channels, $current)
		);

		// -------------------------------------
		//  Build setting: multiple?
		// -------------------------------------

		$multiple = $this->get_setting('multiple', $var_settings);

		$r[] = array(
			$this->setting_label(lang('allow_multiple_channels')),
			'<label class="low-checkbox">'.form_checkbox($this->input_name('multiple'), 'y', $multiple, 'class="low-allow-multiple"').
			lang('allow_multiple_channels_label').'</label>'
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

		$channel_ids = $this->get_setting('channel_ids', $var_settings);
		$multiple = $this->get_setting('multiple', $var_settings);
		$separator = $this->get_setting('separator', $var_settings);
		$multi_interface = $this->get_setting('multi_interface', $var_settings);

		// -------------------------------------
		//  Prep current data
		// -------------------------------------

		$current = explode($this->separators[$separator], $var_data);

		// -------------------------------------
		//  No channel ids? Bail.
		// -------------------------------------

		if (empty($channel_ids))
		{
			return lang('no_channels_selected');
		}

		// -------------------------------------
		//  Get categories
		// -------------------------------------

		$sql_ids = implode(',', ee()->db->escape_str($channel_ids));
		$sql_site = ee()->db->escape_str(ee()->config->item('site_id'));

		$sql = "SELECT
				channel_name, channel_title
			FROM
				exp_channels
			WHERE
				channel_id IN ({$sql_ids})
			AND
				site_id = '{$sql_site}'
			ORDER BY
				channel_title ASC
		";
		$query = ee()->db->query($sql);

		// -------------------------------------
		//  Compose nested category array
		// -------------------------------------

		$channels = $this->flatten_results($query->result_array(), 'channel_name', 'channel_title');

		// -------------------------------------
		//  Create select element
		// -------------------------------------

		// Create select element
		if ($multiple && $multi_interface == 'drag-list')
		{
			$r = $this->drag_lists($var_id, $channels, $current);
		}
		else
		{
			$r = $this->select_element($var_id, $channels, $current, ($multiple == 'y'));
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
	function save_input($var_id, $var_data, $var_settings)
	{
		return is_array($var_data) ? implode($this->separators[$this->get_setting('separator', $var_settings)], $var_data) : $var_data;
	}

	// --------------------------------------------------------------------

}