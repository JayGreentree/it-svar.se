<?php if( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * devot:ee Monitor Accessory
 *
 * @package     ExpressionEngine
 * @subpackage  Add-ons
 * @category    Accessories
 * @author      Visual Chefs, LLC
 * @copyright   Copyright (c) 2011-2013, Visual Chefs, LLC
 */
class Devotee_acc
{
	/**
	 * Accessory information
	 */
	public $name        = 'devot:ee Monitor';
	public $id          = 'devot-ee';
	public $version     = '1.2.3';
	public $description = 'Monitor your add-ons for updates.';
	public $sections    = array();

	/**
	 * CodeIgniter super object
	 *
	 * @var  CI_Controller
	 */
	protected $EE;

	/**
	 * The list of add-ons
	 *
	 * @var  array
	 */
	protected $_addons = array();

	/**
	 * The list of ignored add-ons (subpackages)
	 *
	 * @var  array
	 */
	protected $_ignored_addons = array();

	/**
	 * Path to cache file
	 *
	 * @var  string
	 */
	protected $_cache_path;

	/**
	 * TTL for cache
	 *
	 * @var  int
	 */
	protected $_cache_time;

	/**
	 * URL to the theme files
	 *
	 * @var  string
	 */
	protected $theme_url;

	/**
	 * Constructor
	 *
	 * @return  void
	 */
	public function __construct()
	{
		$this->EE =& get_instance();

		// Set cache settings
		$this->_cache_path = $this->EE->config->item('devotee_monitor_cachepath') ? $this->EE->config->item('devotee_monitor_cachepath') : APPPATH . 'cache/devotee/';
		$this->_cache_time = 60 * 60; // 1 hour

		// Create cache folder if it doesn't exist
		if( ! is_dir($this->_cache_path))
		{
			mkdir($this->_cache_path, DIR_WRITE_MODE);
		}

		// Set theme URL
		$this->theme_url	= defined( 'URL_THIRD_THEMES' )
					? URL_THIRD_THEMES . '/devotee/'
					: $this->EE->config->item('theme_folder_url') . 'third_party/devotee/';

		// Include the ignored_addons array
		require PATH_THIRD.'devotee/ignored_addons.php';
		$this->_ignored_addons = is_array($this->EE->config->item('devotee_monitor_ignored_addons')) ? array_merge($ignored_addons, $this->EE->config->item('devotee_monitor_ignored_addons') ) : $ignored_addons;
	}

	/**
	 * Install accessory
	 *
	 * @return  bool
	 */
	public function install()
	{
		$this->EE->load->dbforge();

		// Create the settings table
		$this->EE->dbforge->add_field('id int(10) unsigned NOT NULL AUTO_INCREMENT');
		$this->EE->dbforge->add_field('member_id int(10) unsigned NOT NULL');
		$this->EE->dbforge->add_field('package varchar(100) NOT NULL');
		$this->EE->dbforge->add_key('id', TRUE);
		$this->EE->dbforge->add_key('member_id');
		$this->EE->dbforge->create_table('devotee_hidden_addons', TRUE);

		return TRUE;
	}

	/**
	 * Update accessory
	 *
	 * @return  bool
	 */
	public function update()
	{
		// Run the install process to make sure that tables are created
		if(version_compare($this->version, '1.2.0', '>='))
		{
			$this->install();
		}

		return TRUE;
	}

	/**
	 * Uninstall accessory
	 *
	 * @return  bool
	 */
	public function uninstall()
	{
		$this->EE->load->dbforge();

		// Drop the settings table
		$this->EE->dbforge->drop_table('devotee_hidden_addons');

		return TRUE;
	}

	/**
	 * Set accessory sections
	 *
	 * @return  void
	 */
	public function set_sections()
	{
		$this->sections['Add-on Information'] = $this->_init();

		// Add theme assets to CP
		$this->EE->cp->add_to_head('<link rel="stylesheet" href="' . $this->theme_url . 'styles/accessory.css?v=' . $this->version . '" />');
		$this->EE->cp->add_to_head('<script type="text/javascript" src="' . $this->theme_url . 'scripts/accessory.js?v=' . $this->version . '"></script>');
	}

	/**
	 * Initial view of the accessory... allows us to load it through Ajax
	 *
	 * @return  string
	 */
	protected function _init()
	{
		$link = htmlspecialchars_decode(BASE . AMP . 'C=addons_accessories' . AMP . 'M=process_request' . AMP . 'accessory=devotee' . AMP . 'method=process_load');

		return $this->EE->load->view('init', array(
			'link' => $link,
			'cp'   => $this->EE->cp,
		), TRUE);
	}

	/**
	 * Get installed add-on information
	 *
	 * @return  string
	 */
	protected function _get_addons($show_hidden_addons = FALSE)
	{
		$this->EE->load->helper(array('file', 'language'));

		// Setup cache file
		$cache_file = $this->_cache_path . 'addons';

		// If cache is still good, use it
		// Otherwise, fetch new data
		if(file_exists($cache_file) AND filemtime($cache_file) > (time() - $this->_cache_time))
		{
			$updates = read_file($cache_file);
		}
		elseif($this->EE->input->get('C') == 'addons_plugins')
		{
			return $this->EE->load->view('error', array(
				'error' => 'Sorry, but the plugins page causes issues when pulling add-on information.',
				'cp' => $this->EE->cp
			), TRUE);
		}
		else
		{
			$this->EE->load->helper('directory');
			$this->EE->load->library('addons');
			$this->EE->load->model('addons_model');
			$this->EE->load->library('api');

			// Scan third_party folder
			$map = directory_map(PATH_THIRD, 2);

			// Bail out if nothing found
			if($map === FALSE)
			{
				return 'No third-party add-ons were found.';
			}

			// Get fieldtypes because the add-ons library doesn't give all the info
			$this->EE->api->instantiate('channel_fields');
			$fieldtypes = $this->EE->api_channel_fields->fetch_all_fieldtypes();

			// Set third-party add-ons
			$third_party = array_intersect_key($this->EE->addons->_packages, $map);

			// Get all installed add-ons
			$installed = array(
				'modules'     => $this->EE->addons->get_installed('modules'),
				'plugins'     => $this->EE->addons_model->get_plugins(),
				'extensions'  => $this->EE->addons->get_installed('extensions'),
				'fieldtypes'  => $this->EE->addons->get_installed('fieldtypes'),
				'accessories' => $this->EE->addons->get_installed('accessories')
			);

			// Loop through each third-party package
			foreach($third_party as $package => $types)
			{
				// Skip this if we already have it
				if(array_key_exists($package, $this->_addons))
				{
					continue;
				}

				// Check if this is a module
				if(array_key_exists($package, $installed['modules']))
				{
					$addon = $installed['modules'][$package];

					// Fix weird EE name issue
					$this->EE->lang->loadfile(( ! isset($this->lang_overrides[$package])) ? $package : $this->lang_overrides[$package]);
					$name = (lang(strtolower($package) . '_module_name') != FALSE) ? lang(strtolower($package) . '_module_name') : $addon['name'];

					$this->_set_addon_info($package, $name, $addon['module_version'], $types);
				}
				// Check if this is a plugin
				elseif(array_key_exists($package, $installed['plugins']))
				{
					$addon = $installed['plugins'][$package];
					$this->_set_addon_info($package, $addon['pi_name'], $addon['pi_version'], $types);
				}
				// Check if this is an extension
				elseif(array_key_exists($package, $installed['extensions']))
				{
					$addon = $installed['extensions'][$package];
					$this->_set_addon_info($package, $addon['name'], $addon['version'], $types);
				}
				// Check if this is a fieldtype
				elseif(array_key_exists($package, $installed['fieldtypes']))
				{
					$addon = $fieldtypes[$package];
					$this->_set_addon_info($package, $addon['name'], $addon['version'], $types);
				}
				// Check if this is an accessory
				elseif(array_key_exists($package, $installed['accessories']))
				{
					$addon = $installed['accessories'][$package];

					// We need to load the class if it's not devot:ee to get more info
					// Otherwise, we already have the info
					if($package != 'devotee')
					{
						if( ! class_exists($addon['class']))
						{
							require_once PATH_THIRD . "{$package}/acc.{$package}.php";
						}

						$acc = new $addon['class']();
					}
					else
					{
						$acc = array(
							'name'    => $this->name,
							'version' => $this->version
						);
						$acc = (object) $acc;
					}

					if(isset($acc))
					{
						$this->_set_addon_info($package, $acc->name, $acc->version, $types);

						unset($acc);
					}
				}
			}

			// Remove ignored add-ons from the _addons data member prior to fetching updates
			foreach ($this->_ignored_addons as $index => $package)
				unset($this->_addons[$package]);

			// Check updates
			$updates = $this->_get_updates();

			$updates_decoded = json_decode($updates);
			if( ! $updates_decoded)
			{
				return $this->EE->load->view('error', array(
					'error' => 'Sorry, but something went wrong. Please try again later.',
					'cp' => $this->EE->cp
				), TRUE);
			}
			elseif( ! empty($updates_decoded->error))
			{
				return $this->EE->load->view('error', array(
					'error' => $updates_decoded->error,
					'cp' => $this->EE->cp
				), TRUE);
			}

			// Write to cache
			write_file($cache_file, $updates);
		}

		// Hidden add-ons
		$hidden_addon_query = $this->EE->db->select('package')
			->where('member_id', $this->EE->session->userdata('member_id'))
			->get('devotee_hidden_addons');

		$hidden_addons = array();
		foreach($hidden_addon_query->result() as $hid_ad)
		{
			$hidden_addons[] = $hid_ad->package;
		}

		// Return the view
		return $this->EE->load->view('accessory', array(
			'updates'       => json_decode($updates),
			'last_check'    => filemtime($cache_file),
			'hidden_addons' => $hidden_addons,
			'show_hidden'	=> $show_hidden_addons,
			'cp'			=> $this->EE->cp
		), TRUE);
	}

	/**
	 * Set add-on info
	 *
	 * @param   string  The package name
	 * @param   string  The actual add-on name
	 * @param   string  The version number
	 * @param   array   Add-on types (module, plugin, etc.)
	 * @return  void
	 */
	protected function _set_addon_info($package, $name, $version, $types)
	{
		$this->_addons[$package] = array(
			'name'    => $name,
			'version' => $version,
			'types'   => $this->_abbreviate_types(array_keys($types))
		);
	}

	/**
	 * Get update info from the API
	 *
	 * @return  string
	 */
	protected function _get_updates()
	{
		$data = array(
			'data' => $this->_addons,
			'site' => md5( $this->EE->config->item('site_label') ),
		);

		$ch = curl_init('http://monitor.devot-ee.com/');
		curl_setopt_array($ch, array(
			CURLOPT_POST           => TRUE,
			CURLOPT_CONNECTTIMEOUT => 2,
			CURLOPT_TIMEOUT        => 5,
			CURLOPT_RETURNTRANSFER => TRUE,
			CURLOPT_POSTFIELDS     => json_encode($data),
			CURLOPT_HTTPHEADER     => array(
				'Content-type: application/json'
			)
		));
		$response = curl_exec($ch);
		curl_close($ch);

		if( ! $response)
		{
			$response = json_encode(array(
				'error' => 'The API could not be reached. Please try again later.'
			));
		}

		return $response;
	}

	/**
	 * Create an abbreviated list of add-on types, and designate whether the current add-on
	 * is of a particular type
	 *
	 * @param   array  The add-on types
	 * @return  array
	 */
	protected function _abbreviate_types($types = array())
	{
		$available_types = array(
			'module'    => 'MOD',
			'extension' => 'EXT',
			'plugin'    => 'PLG',
			'fieldtype' => 'FLD',
			'accessory' => 'ACC'
		);

		$abbrevs = array();

		foreach($available_types as $key => $abbrev)
		{
			$abbrevs[$abbrev] = (in_array($key, $types)) ? TRUE : FALSE;
		}

		return $abbrevs;
	}

	/**
	 * AJAX method for loading the initial view
	 *
	 * @return  void
	 */
	public function process_load()
	{
		$this->process_refresh(FALSE);
	}

	/**
	 * AJAX method for clearing cache and reloading the add-ons list
	 *
	 * @param   bool  Whether to delete the cache
	 * @return  void
	 */
	public function process_refresh($delete_cache = TRUE, $display_hidden_addons = FALSE)
	{
		if(AJAX_REQUEST)
		{
			// Delete cache
			if($delete_cache)
			{
				$this->EE->functions->delete_directory(APPPATH . 'cache/devotee');
			}

			// Output HTML from the view
			echo $this->_get_addons($display_hidden_addons);
			exit;
		}
	}

	/**
	 * AJAX method for hiding certain add-ons from the accessory
	 *
	 * @return  void
	 */
	public function process_hide_addon()
	{
		if(AJAX_REQUEST)
		{
			// Add setting to database
			$this->EE->db->insert('devotee_hidden_addons', array(
				'member_id' => $this->EE->session->userdata('member_id'),
				'package'   => $this->EE->input->get('package')
			));

			// Refresh view
			$this->process_refresh(FALSE);
		}
	}

	/**
	 * AJAX method for including hidden add-ons in the list of installed
	 * add-ons.
	 *
	 * @return	void
	 */
	public function process_display_hidden_addons()
	{
		if(AJAX_REQUEST)
		{
			$this->process_refresh(FALSE, TRUE);
		}
	}

	/**
	 * AJAX method for removing an add-on from the devotee_hidden_addons
	 * table so that is shows up in the add-on list normally.
	 *
	 * @return  void
	 */
	public function process_unhide_addon()
	{
		if(AJAX_REQUEST)
		{
			// Fetch the add-on package name from the URL
			$package_name = $this->EE->input->get('package');

			// Delete hidden add-on setting for user
			$this->EE->db->where('package', $package_name)->where('member_id', $this->EE->session->userdata('member_id'))->delete('devotee_hidden_addons');

			// Refresh view
			$this->process_refresh(FALSE);
		}
	}
}
