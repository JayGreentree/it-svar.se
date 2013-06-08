<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Low_table extends Low_variables_type {

	public $info = array(
		'name'    => 'Table',
		'version' => LOW_VAR_VERSION
	);

	public $default_settings = array(
		'columns'  => 'Column 1 | Column 2'
	);

	// --------------------------------------------------------------------

	/**
	 * Display settings sub-form for this variable type
	 */
	public function display_settings($var_id, $var_settings)
	{
		return array(array(
			$this->setting_label(lang('columns'), lang('columns_help')),
			form_input(array(
				'name'  => $this->input_name('columns'),
				'value' => $this->get_setting('columns', $var_settings),
				'class' => 'large'
			))
		));
	}

	/**
	 * Display input field for regular user
	 */
	public function display_input($var_id, $var_data, $var_settings)
	{
		// Get current settings for table
		$cols = $this->get_setting('columns', $var_settings);
		$cols = array_map('trim', explode('|', $cols));

		// Return the view based on these vars
		return ee()->load->view('mcp_vt_table', array(
			'var_id'  => $var_id,
			'columns' => $cols,
			'col_count' => count($cols),
			'rows'    => $this->_get_data($var_data)
		), TRUE);
	}

	/**
	 * Prep variable data for saving
	 */
	public function save_input($var_id, $var_data, $var_settings)
	{
		// Initiate rows data
		$rows = array();
		$data = '';

		if ( ! empty($var_data) && is_array($var_data))
		{
			// Loop through posted data and strip out empty rows
			foreach ($var_data AS $row)
			{
				$row = array_map('trim', $row);

				if (count(array_filter($row)))
				{
					$rows[] = $row;
				}
			}

			// Overwrite data if there are rows present
			if ($rows)
			{
				$cols = $this->get_setting('columns', $var_settings);
				$data = ee()->load->view('mod_vt_table', array(
					'var_id'  => $var_id,
					'columns' => array_map('trim', explode('|', $cols)),
					'rows'    => $rows,
					'encoded_rows' => low_array_encode($rows)
				), TRUE);
			}
		}

		return $data;
	}

	/**
	 * Display output, possible formatting
	 */
	public function display_output($tagdata, $var)
	{
		// Extract array from var data to see if this is valid
		if ($rows = $this->_get_data($var['variable_data']))
		{
			// Do we actually have tagdata? If not, just return the whole table
			if ($tagdata)
			{
				// Initiate array for the view
				$data = array();

				// Change order of rows if sort is 'desc' or 'random'
				if (($sort = ee()->TMPL->fetch_param('sort', 'asc')) != 'asc')
				{
					switch ($sort)
					{
						case 'desc':
							$rows = array_reverse($rows);
							break;
						case 'random':
							shuffle($rows);
							break;
					}
				}

				// Limit the rows
				if (($limit = ee()->TMPL->fetch_param('limit')) && is_numeric($limit))
				{
					$rows = array_slice($rows, 0, $limit);
				}

				// Loop through rows
				foreach ($rows AS $row_nr => $row)
				{
					// Init row cells
					$cells = array();

					// For each cell, add {cell_x} to the row cells
					foreach ($row AS $cell_nr => $cell_content)
					{
						$cells['cell_'.($cell_nr + 1)] = $cell_content;
					}

					// Add the cells to the view data
					$data[] = $cells;
				}

				// Return parsed template
				return ee()->TMPL->parse_variables($tagdata, $data);
			}
			else
			{
				return $var['variable_data'];
			}
		}
		else
		{
			return ee()->TMPL->no_results();
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Get encoded data from variable contents
	 */
	private function _get_data($data)
	{
		if (preg_match('/<!\-\-(.*?)\-\->/', $data, $match) && ($rows = low_array_decode($match[1])))
		{
			return $rows;
		}
		else
		{
			return array();
		}
	}
}