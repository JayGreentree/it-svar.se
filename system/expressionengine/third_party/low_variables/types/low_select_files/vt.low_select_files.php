<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Low_select_files extends Low_variables_type {

	public $info = array(
		'name'		=> 'Select Files',
		'version'	=> LOW_VAR_VERSION
	);

	public $default_settings = array(
		'multiple'	=> 'n',
		'folders'	=> array(1),
		'separator'	=> 'newline',
		'upload'	=> ''
	);

	public $language_files = array(
		'upload'
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
		//  Check current value from settings
		// -------------------------------------

		$folders = $this->get_setting('folders', $var_settings);

		// -------------------------------------
		//  Get all folders
		// -------------------------------------

		$all_folders = low_flatten_results($this->_get_upload_preferences(), 'name', 'id');

		// -------------------------------------
		//  Build options setting
		// -------------------------------------

		$r[] = array(
			$this->setting_label(lang('file_folders')),
			form_multiselect($this->input_name('folders', TRUE), $all_folders, $folders)
		);

		// -------------------------------------
		//  Build setting: Allow uploads?
		// -------------------------------------

		$upload_folders = array('0' => lang('no_uploads')) + $all_folders;
		$upload = $this->get_setting('upload', $var_settings);

		$r[] = array(
			$this->setting_label(lang('upload_folder'), lang('upload_folder_help')),
			form_dropdown($this->input_name('upload'), $upload_folders, $upload)
		);

		// -------------------------------------
		//  Build setting: multiple?
		// -------------------------------------

		$multiple = $this->get_setting('multiple', $var_settings);

		$r[] = array(
			$this->setting_label(lang('allow_multiple_files')),
			'<label class="low-checkbox">'.form_checkbox($this->input_name('multiple'), 'y', $multiple, 'class="low-allow-multiple"').
			lang('allow_multiple_files_label').'</label>'
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
			$this->interface_select($multi_interface, array('drag-list-thumbs' => lang('drag-list-thumbs')))
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
		// Load Tools model, former cp.filebrowser.php
		ee()->load->model('file_model');

		// get settings
		$multi = $this->get_setting('multiple', $var_settings);
		$multi_interface = $this->get_setting('multi_interface', $var_settings);
		$use_thumbs = FALSE;

		// -------------------------------------
		//  Prep current data
		// -------------------------------------

		$current = explode($this->separators[$this->get_setting('separator', $var_settings)], $var_data);

		// -------------------------------------
		//  Prep options
		// -------------------------------------

		if ( ! ($folders = $this->get_setting('folders', $var_settings)) )
		{
			// no folder found error message
			return lang('no_folders_selected');
		}

		// -------------------------------------
		//  Check if folder details are cached
		// -------------------------------------

		if ( ! ($cached_dirs = low_get_cache(LOW_VAR_PACKAGE, 'dirs')))
		{
			$cached_dirs = array();
		}

		$cached_ids = array_keys($cached_dirs);

		// -------------------------------------
		//  Get still missing folders
		// -------------------------------------

		if ($missing_dirs = array_diff($folders, $cached_ids))
		{
			// Get prefs from DB or config file
			$dirs = array();

			foreach ($missing_dirs AS $dir_id)
			{
				$dirs[$dir_id] = $this->_get_upload_preferences(NULL, $dir_id);
			}

			// Loop through dirs and get filenames for each dir.
			foreach ($dirs AS &$dir)
			{
				$files = ee()->file_model->get_raw_files($dir['server_path'], $dir['allowed_types']);

				foreach ($files AS $file)
				{
					$dir['files'][] = array(
						'name'  => $file['name'],
						'url'   => $dir['url'].$file['name'],
						'thumb' => (file_exists($dir['server_path'].'_thumbs/'.$file['name'])
						         ? $dir['url'].'_thumbs/'.$file['name']
						         : FALSE)
					);
				}
			}

			// Add to cached dirs and re-register cache
			$cached_dirs = $cached_dirs + $dirs;
			low_set_cache(LOW_VAR_PACKAGE, 'dirs', $cached_dirs);
		}

		// -------------------------------------
		//  Prep filelist and thumbs arrays
		// -------------------------------------

		$filelist = $thumbs = array();

		if ($multi_interface == 'drag-list-thumbs')
		{
			$multi_interface = 'drag-list';
			$use_thumbs = TRUE;
		}

		// Prep thumb templatlet
		$thumb_tmpl = '<span class="low-img"><img src="%s" alt="" style="vertical-align:middle" /></span> %s';

		// -------------------------------------
		//  Loop through dirs and their files
		// -------------------------------------

		foreach ($folders AS $folder_id)
		{
			if ( ! isset($cached_dirs[$folder_id])) continue;

			$dir = $cached_dirs[$folder_id];

			if (empty($dir['files'])) continue;

			foreach ($dir['files'] AS $file)
			{
				$label = $file['name'];

				// Assumed thumb location
				if ($use_thumbs && $file['thumb'] !== FALSE)
				{
					$label = sprintf($thumb_tmpl, $file['thumb'], $file['name']);
				}

				if ($multi == 'y' && $multi_interface == 'drag-list')
				{
					$filelist[$file['url']] = $label;
				}
				else
				{
					$filelist[$dir['name']][$file['url']] = $file['name'];
				}
			}
		}

		// -------------------------------------
		//  Create interface
		// -------------------------------------

		if ($multi == 'y' && $multi_interface == 'drag-list')
		{
			$r = $this->drag_lists($var_id, $filelist, $current, FALSE);

			if ($use_thumbs)
			{
				$r = str_replace('class="low-drag-lists"', 'class="low-drag-lists images"', $r);
			}
		}
		else
		{
			$r = $this->select_element($var_id, $filelist, $current, ($multi == 'y'));
		}

		// -------------------------------------
		//  Add upload file thing?
		// -------------------------------------

		if ($upload = $this->get_setting('upload', $var_settings))
		{
			$upload_html = '<div class="low-new-file" id="low-new-file-%s"><a href="#" class="low-add"><b>+</b> %s</a> <span>%s</span></div>';
			$r .= sprintf($upload_html, $var_id, lang('upload_new_file'), lang('save_to_upload'));
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
		// Include upload library
		ee()->load->library('upload');

		// Get upload setting
		$upload = $this->get_setting('upload', $var_settings);

		// -------------------------------------
		//  Is there a valid upload for this var id?
		// -------------------------------------

		if ($upload && isset($_FILES['newfile']['name'][$var_id]) && !empty($_FILES['newfile']['name'][$var_id]))
		{
			// -------------------------------------
			//  Fetch upload folder from cache or DB
			// -------------------------------------

			$upload_cache = low_get_cache(LOW_VAR_PACKAGE, 'uploads');

			if (isset($upload_cache[$upload]))
			{
				$folder = $upload_cache[$upload];
			}
			else
			{
				// Fetch record from DB
				if ($folder = $this->_get_upload_preferences(NULL, $upload))
				{
					// get folder and register to session cache
					$upload_cache[$upload] = $folder;
					low_set_cache(LOW_VAR_PACKAGE, 'uploads', $upload_cache);
				}
				else
				{
					// -------------------------------------
					//  Bail out if folder wasn't found
					// -------------------------------------

					$this->error_msg = 'folder_not_found';
					return FALSE;
				}
			}

			unset($upload_cache);

			// -------------------------------------
			//  Reset and fill $_FILES['userfile']
			// -------------------------------------

			$_FILES['userfile'] = array();

			// Get uploaded files details from $_FILES
			foreach ($_FILES['newfile'] AS $key => $val)
			{
				if (isset($val[$var_id]))
				{
					$_FILES['userfile'][$key] = $val[$var_id];
				}
			}

			// -------------------------------------
			//  Set parameters according to folder prefs
			// -------------------------------------

			$config = array(
				'upload_path'	=> $folder['server_path'],
				'allowed_types'	=> (($folder['allowed_types'] == 'img') ? 'gif|jpg|jpeg|png|jpe' : '*'),
				'max_size'		=> $folder['max_size'],
				'max_width'		=> $folder['max_width'],
				'max_height'	=> $folder['max_height']
			);

			ee()->upload->initialize($config);

			// -------------------------------------
			//  Upload the file
			// -------------------------------------

			if ( ! ee()->upload->do_upload() )
			{
				// Set error msg and bail if unsuccessful
				$this->error_msg = ee()->upload->error_msg;
				return FALSE;
			}

			// get the new file's full path; the data we're going to save
			$newfile = $folder['url'].ee()->upload->file_name;

			if (is_array($var_data))
			{
				// add it to the selected files
				$var_data[] = $newfile;
			}
			else
			{
				// or replace single value
				$var_data = $newfile;
			}

			// Create thumbnail for this
			ee()->load->library('filemanager');
			ee()->load->model('file_model');
			$folder['file_name']  = ee()->upload->file_name;
			$folder['dimensions'] = NULL;
			ee()->filemanager->create_thumb($folder['server_path'].ee()->upload->file_name, $folder);

			// Add to native DB table
			ee()->file_model->save_file(array(
				'site_id' => $this->site_id,
				'title' => ee()->upload->file_name,
				'upload_location_id' => $upload,
				'rel_path' => ee()->upload->upload_path,
				'mime_type' => ee()->upload->file_type,
				'file_name' => ee()->upload->file_name,
				'file_size' => ee()->upload->file_size,
				'uploaded_by_member_id' => ee()->session->userdata['member_id'],
				'upload_date' => ee()->localize->now
			));

		} // END if upload?

		// Return new value
		return is_array($var_data) ? implode($this->separators[$this->get_setting('separator', $var_settings)], $var_data) : $var_data;

	}

	// --------------------------------------------------------------------

	/**
	 * Get Upload Preferences (Cross-compatible between ExpressionEngine 2.0 and 2.4) - By Brandon Kelly
	 *
	 * @param  int $group_id Member group ID specified when returning allowed upload directories only for that member group
	 * @param  int $id       Specific ID of upload destination to return
	 * @return array         Result array of DB object, possibly merged with custom file upload settings (if on EE 2.4+)
	 */
	private function _get_upload_preferences($group_id = NULL, $id = NULL)
	{
		if (version_compare(APP_VER, '2.4', '>='))
		{
			ee()->load->model('file_upload_preferences_model');
			return ee()->file_upload_preferences_model->get_file_upload_preferences($group_id, $id);
		}

		if (version_compare(APP_VER, '2.1.5', '>='))
		{
			ee()->load->model('file_upload_preferences_model');
			$result = ee()->file_upload_preferences_model->get_upload_preferences($group_id, $id);
		}
		else
		{
			ee()->load->model('tools_model');
			$result = ee()->tools_model->get_upload_preferences($group_id, $id);
		}

		// If an $id was passed, just return that directory's preferences
		if ( ! empty($id))
		{
			return $result->row_array();
		}

		// Use upload destination ID as key for row for easy traversing
		return low_associate_results($result->result_array(), 'id');
	}

}