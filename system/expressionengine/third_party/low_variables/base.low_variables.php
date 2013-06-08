<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// include config file
include(PATH_THIRD.'low_variables/config.php');

/**
 * Low Variables Base Class
 *
 * @package        low_variables
 * @author         Lodewijk Schutte <hi@gotolow.com>
 * @link           http://gotolow.com/addons/low-variables
 * @copyright      Copyright (c) 2009-2013, Low
 */
class Low_variables_base
{
	// --------------------------------------------------------------------
	// PROPERTIES
	// --------------------------------------------------------------------

	/**
	 * Add-on name
	 *
	 * @var        string
	 * @access     public
	 */
	public $name = LOW_VAR_NAME;

	/**
	 * Add-on version
	 *
	 * @var        string
	 * @access     public
	 */
	public $version = LOW_VAR_VERSION;

	/**
	 * URL to module docs
	 *
	 * @var        string
	 * @access     public
	 */
	public $docs_url = LOW_VAR_DOCS;

	/**
	 * Settings array
	 *
	 * @var        array
	 * @access     public
	 */
	public $settings = array();

	// --------------------------------------------------------------------

	/**
	 * Package name
	 *
	 * @var        string
	 * @access     protected
	 */
	protected $package = LOW_VAR_PACKAGE;

	/**
	 * Site id shortcut
	 *
	 * @var        int
	 * @access     protected
	 */
	protected $site_id;

	/**
	 * Base url for module
	 *
	 * @var        string
	 * @access     protected
	 */
	protected $base_url;

	/**
	 * Base url for extension
	 *
	 * @var        string
	 * @access     protected
	 */
	protected $ext_url;

	/**
	 * Data array for views
	 *
	 * @var        array
	 * @access     protected
	 */
	protected $data = array();

	/**
	 * Default settings array
	 *
	 * @var        array
	 * @access     protected
	 */
	protected $default_settings = array(
		'license_key'          => '',
		'can_manage'           => array(1),
		'clear_cache'          => 'n',
		'register_globals'     => 'n',
		'register_member_data' => 'n',
		'save_as_files'        => 'n',
		'file_path'            => '',
		'enabled_types'        => array(LOW_VAR_DEFAULT_TYPE)
	);

	/**
	 * Custom config settings
	 *
	 * @var        array
	 * @access     protected
	 */
	protected $cfg = array();

	// --------------------------------------------------------------------

	/**
	 * Custom config settings
	 *
	 * @var        array
	 * @access     private
	 */
	private $cfg_keys = array('save_as_files', 'file_path');

	/**
	 * Variable file name extension
	 *
	 * @var        string
	 * @access     private
	 */
	private $var_ext = '.html';

	/**
	 * Control Panel assets
	 *
	 * @var        array
	 * @access     private
	 */
	private $mcp_assets = array(
		'styles/low_variables.css',
		'scripts/low_variables.js'
	);

	// --------------------------------------------------------------------
	// METHODS
	// --------------------------------------------------------------------

	/**
	 * Constructor
	 *
	 * @access     public
	 * @return     void
	 */
	public function __construct()
	{
		// -------------------------------------
		//  Define the package path
		// -------------------------------------

		ee()->load->add_package_path(PATH_THIRD.$this->package);

		// -------------------------------------
		//  Load helper
		// -------------------------------------

		ee()->load->helper($this->package);

		// -------------------------------------
		//  Get site shortcut
		// -------------------------------------

		$this->site_id = ee()->config->item('site_id');

		// -------------------------------------
		//  Get custom config items
		// -------------------------------------

		foreach ($this->cfg_keys AS $item)
		{
			$this->cfg[$item] = ee()->config->item($this->package.'_'.$item);
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Apply Config overrides to $this->settings
	 *
	 * @access     protected
	 * @return     void
	 */
	protected function apply_config_overrides()
	{
		// Check custom config values
		foreach ($this->cfg AS $key => $val)
		{
			if ($val !== FALSE)
			{
				$this->settings[$key] = $val;
			}
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Get settings
	 *
	 * @access     protected
	 * @param      array
	 * @return     array
	 */
	protected function get_settings($settings = array())
	{
		if ( ! $settings)
		{
			// Check cache
			if (($this->settings = low_get_cache($this->package, 'settings')) === FALSE)
			{
				// Not in cache? Get from DB and add to cache
				$query = ee()->db->select('settings')
				       ->from('extensions')
				       ->where('class', $this->package.'_ext')
				       ->limit(1)
				       ->get();

				$this->settings = (array) @unserialize($query->row('settings'));

				// Add to cache
				low_set_cache($this->package, 'settings', $this->settings);
			}
		}
		else
		{
			$this->settings = $settings;
		}

		// Always fallback to default settings
		$this->settings = array_merge($this->default_settings, $this->settings);

		return $this->settings;
	}

	/**
	 * Is current user a variable manager?
	 *
	 * @access     protected
	 * @return     bool
	 */
	protected function is_manager()
	{
		return in_array(ee()->session->userdata['group_id'], $this->settings['can_manage']);
	}

	// --------------------------------------------------------------------

	/**
	 * Sync variables and var files
	 *
	 * @access     protected
	 * @param      array
	 * @return     void
	 */
	protected function sync_files($vars = array())
	{
		// -------------------------------------
		//  If vars are not given, get them from the DB
		// -------------------------------------

		if ( ! $vars)
		{
			$query = ee()->db->select('ee.variable_id, ee.variable_name, ee.variable_data, low.edit_date')
			       ->from('global_variables AS ee, low_variables AS low')
			       ->where('ee.variable_id = low.variable_id')
			       ->where('ee.site_id', $this->site_id)
			       ->where('low.save_as_file', 'y')
			       ->get();

			$vars = $query->result_array();
		}

		// -------------------------------------
		//  Still no vars? Exit
		// -------------------------------------

		if ( ! $vars) return;

		// -------------------------------------
		//  Check if right directory exists
		// -------------------------------------

		$path = $this->_get_var_filepath();

		if ( ! @is_dir($path))
		{
			if ( ! @mkdir($path, DIR_WRITE_MODE))
			{
				return FALSE;
			}
			@chmod($path, DIR_WRITE_MODE);
		}

		// -------------------------------------
		//  Load file helper
		// -------------------------------------

		ee()->load->helper('file');

		// -------------------------------------
		//  Get existing files only for CP requests for performance reasons
		// -------------------------------------

		$files = (REQ == 'CP') ? get_filenames($path) : array();

		// -------------------------------------
		//  Loop thru save_as_file-variables
		// -------------------------------------

		foreach ($vars AS $row)
		{
			// Determine this var's file name
			$file  = $this->_get_var_filename($row['variable_name']);
			$name  = $this->_get_var_filename($row['variable_name'], FALSE);
			$write = FALSE;

			// Check if file exists
			if (file_exists($file))
			{
				// If it does exist, check it's modified date
				$info = get_file_info($file, 'date');

				// If file is younger than DB, read file and update DB
				if ($info['date'] > $row['edit_date'])
				{
					// Update native table with file data
					ee()->db->update(
						'global_variables',
						array('variable_data' => read_file($file)),
						"variable_id = '{$row['variable_id']}'"
					);

					// Update low_variables table
					ee()->db->update(
						'low_variables',
						array('edit_date' => $info['date']),
						"variable_id = '{$row['variable_id']}'"
					);
				}
				elseif ($info['date'] < $row['edit_date'])
				{
					// Write to file if server file is older than DB
					$write = TRUE;
				}
			}
			else
			{
				// File doesn't exist - write new file
				$write = TRUE;
			}

			// Write to file, if necessary
			if ($write)
			{
				write_file($file, $row['variable_data']);
				@chmod($file, FILE_WRITE_MODE);
			}

			// Remove reference in the files list
			if (($key = array_search($name, $files)) !== FALSE)
			{
				unset($files[$key]);
			}

		} // End foreach var

		// -------------------------------------
		//  Delete rogue files
		// -------------------------------------

		foreach ($files AS $filename)
		{
			@unlink($path.$filename);
		}
	}

	/**
	 * Get (full) filename for given var
	 *
	 * @access     private
	 * @param      string
	 * @param      bool
	 * @return     string
	 */
	private function _get_var_filename($var_name, $full = TRUE)
	{
		$filename = $var_name . $this->var_ext;

		if ($full)
		{
			$filename = $this->_get_var_filepath() . $filename;
		}

		return $filename;
	}

	/**
	 * Get file path for saving var files for this site
	 *
	 * @access     private
	 * @return     string
	 */
	private function _get_var_filepath()
	{
		return rtrim($this->settings['file_path'], '/').'/'.ee()->config->item('site_short_name').'/';
	}

	// --------------------------------------------------------------------

	/**
	 * Sets base url for views
	 *
	 * @access     protected
	 * @return     void
	 */
	protected function set_base_url()
	{
		$this->data['base_url'] = $this->base_url = BASE.AMP.'C=addons_modules&amp;M=show_module_cp&amp;module='.$this->package;
		$this->data['ext_url'] = $this->ext_url = BASE.AMP.'C=addons_extensions&amp;M=extension_settings&amp;file='.$this->package;
	}

	/**
	 * View add-on page
	 *
	 * @access     protected
	 * @param      string
	 * @return     string
	 */
	protected function view($file)
	{
		// -------------------------------------
		//  Load CSS and JS
		// -------------------------------------

		$this->_load_assets();

		// -------------------------------------
		//  Add feedback msg to output
		// -------------------------------------

		if ($this->data['message'] = ee()->session->flashdata('msg'))
		{
			ee()->javascript->output(array(
				'$.ee_notice("'.lang($this->data['message']).'",{type:"success",open:true});',
				'window.setTimeout(function(){$.ee_notice.destroy()}, 2000);'
			));
		}

		// -------------------------------------
		//  Add menu to page if manager
		// -------------------------------------

		if ($this->is_manager())
		{
			// Check if there's a group_id there
			$from_group = '';

			if ($group_id = ee()->input->get('group_id'))
			{
				$from_group = AMP.'from='.$group_id;
			}

			ee()->cp->set_right_nav(array(
				'low_variables_module_name' => $this->base_url,
				'manage_variables'          => $this->base_url.AMP.'method=manage',
				'create_new'                => $this->base_url.AMP.'method=manage&amp;id=new'.$from_group,
				'create_new_group'          => $this->base_url.AMP.'method=edit_group&amp;id=new',
				'extension_settings'        => $this->ext_url
			));
		}

		return ee()->load->view($file, $this->data, TRUE);
	}

	// --------------------------------------------------------------------

	/**
	 * Load assets: extra JS and CSS
	 *
	 * @access     private
	 * @return     void
	 */
	private function _load_assets()
	{
		// -------------------------------------
		//  Define placeholder
		// -------------------------------------

		$header = $footer = array();

		// -------------------------------------
		//  Loop through assets
		// -------------------------------------

		$asset_url = ((defined('URL_THIRD_THEMES'))
		           ? URL_THIRD_THEMES
		           : ee()->config->item('theme_folder_url') . 'third_party/')
		           . $this->package . '/';

		foreach ($this->mcp_assets AS $file)
		{
			// location on server
			$file_url = $asset_url.$file.'?v='.LOW_VAR_VERSION;

			if (substr($file, -3) == 'css')
			{
				$header[] = '<link charset="utf-8" type="text/css" href="'.$file_url.'" rel="stylesheet" media="screen" />';
			}
			elseif (substr($file, -2) == 'js')
			{
				$footer[] = '<script charset="utf-8" type="text/javascript" src="'.$file_url.'"></script>';
			}
		}

		// -------------------------------------
		//  Add combined assets to header/footer
		// -------------------------------------

		if ($header) ee()->cp->add_to_head(implode(NL, $header));
		if ($footer) ee()->cp->add_to_foot(implode(NL, $footer));
	}

	// --------------------------------------------------------------------

	/**
	 * Get array of Variable Types
	 *
	 * This method can be called directly throughout the package with $this->get_types()
	 *
	 * @param	mixed	$which		FALSE for complete list or array containing which types to get
	 * @return	array
	 */
	protected function get_types($which = FALSE)
	{
		// -------------------------------------
		//  Initiate return value
		// -------------------------------------

		$types = array();

		// -------------------------------------
		//  Load libraries
		// -------------------------------------

		ee()->load->library('addons');
		ee()->load->library('low_variables_type');
		ee()->load->library('low_fieldtype_bridge');

		// -------------------------------------
		//  Set variable types path
		// -------------------------------------

		$types_path = PATH_THIRD.'low_variables/types/';

		// -------------------------------------
		//  If path is not valid, bail
		// -------------------------------------

		if ( ! is_dir($types_path) ) return;

		// -------------------------------------
		//  Read dir, create instances
		// -------------------------------------

		$dir = opendir($types_path);
		while (($type = readdir($dir)) !== FALSE)
		{
			// skip these
			if ($type == '.' || $type == '..' || !is_dir($types_path.$type)) continue;

			// if given, only get the given ones
			if (is_array($which) && ! in_array($type, $which)) continue;

			// determine file name
			$file = 'vt.'.$type.EXT;
			$path = $types_path.$type.'/';

			if ( ! class_exists($type) && file_exists($path.$file) )
			{
				include($path.$file);
			}

			// Got class? Get its details without instantiating it
			if (class_exists($type))
			{
				$vars = get_class_vars($type);

				$types[$type] = array(
					'path'         => $path,
					'file'         => $file,
					'name'         => (isset($vars['info']['name']) ? $vars['info']['name'] : $type),
					'class'        => ucfirst($type),
					'version'      => (isset($vars['info']['version']) ? $vars['info']['version'] : ''),
					'is_default'   => ($type == LOW_VAR_DEFAULT_TYPE),
					'is_fieldtype' => FALSE
				);
			}
		}

		// clean up
		closedir($dir);
		unset($dir);

		// -------------------------------------
		//  Get fieldtypes
		// -------------------------------------

		foreach (ee()->addons->get_installed('fieldtypes') AS $package => $ftype)
		{
			// if given, only get the given ones
			if (is_array($which) && ! in_array($ftype['class'], $which) && ! in_array($package, $which)) continue;

			// Include EE Fieldtype class
			if ( ! class_exists('EE_Fieldtype'))
			{
				include_once (APPPATH.'fieldtypes/EE_Fieldtype'.EXT);
			}

			if ( ! class_exists($ftype['class']) && file_exists($ftype['path'].$ftype['file']))
			{
				include_once ($ftype['path'].$ftype['file']);
			}

			// Check if fieldtype is compatible
			if (method_exists($ftype['class'], 'display_var_field'))
			{
				$vars = get_class_vars($ftype['class']);

				$types[$ftype['name']] = array(
					'path'         => $ftype['path'],
					'file'         => $ftype['file'],
					'name'         => (isset($vars['info']['name']) ? $vars['info']['name'] : $ftype['name']),
					'class'        => $ftype['class'],
					'version'      => $ftype['version'],
					'is_default'   => ($type == LOW_VAR_DEFAULT_TYPE),
					'is_fieldtype' => TRUE
				);
			}
		}

		// Sort types by alpha
		uasort($types, create_function(
			'$a, $b',
			'return strcasecmp($a["name"], $b["name"]);'
		));

		return $types;
	}

} // End class Low_variables_base