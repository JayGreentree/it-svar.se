<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// include base class
if ( ! class_exists('Low_variables_base'))
{
	require_once(PATH_THIRD.'low_variables/base.low_variables.php');
}

/**
 * Low Variables Extension class
 *
 * @package        low_variables
 * @author         Lodewijk Schutte <hi@gotolow.com>
 * @link           http://gotolow.com/addons/low-variables
 * @copyright      Copyright (c) 2009-2013, Low
 */
class Low_variables_ext extends Low_variables_base {

	// --------------------------------------------------------------------
	// PROPERTIES
	// --------------------------------------------------------------------

	/**
	 * Do settings exist?
	 *
	 * @var        string	y|n
	 * @access     public
	 */
	public $settings_exist = 'y';

	/**
	 * Move installation to MOD
	 *
	 * @var        array
	 * @access     public
	 */
	public $required_by = array('module');

	// --------------------------------------------------------------------
	// METHODS
	// --------------------------------------------------------------------

	/**
	 * Constructor
	 *
	 * @access     public
	 * @param      mixed
	 * @return     void
	 */
	public function __construct($settings = array())
	{
		// Call Base constructor
		parent::__construct();

		// Assign current settings
		$this->settings = array_merge($this->default_settings, $settings);

		// And overwite given settings with the ones defined in config.php
		$this->apply_config_overrides();
	}

	// --------------------------------------------------------------------

	/**
	 * Extension settings form
	 *
	 * @access     public
	 * @param      array
	 * @return     string
	 */
	public function settings_form($settings = array())
	{
		$this->set_base_url();

		// -------------------------------------
		//  Get member groups; exclude guests, pending and banned
		// -------------------------------------

		$query = ee()->db->select('group_id, group_title')
		       ->from('member_groups')
		       ->where_not_in('group_id', array(2, 3, 4))
		       ->order_by('group_title', 'asc')
		       ->get();

		$groups = low_flatten_results($query->result_array(), 'group_title', 'group_id');

		// -------------------------------------
		// Merge given settings with default settings
		// -------------------------------------

		$this->get_settings($settings);
		$this->apply_config_overrides();

		// -------------------------------------
		// Add more data to settings array for display
		// -------------------------------------

		$this->data = $this->settings;
		$this->data['member_groups']  = $groups;
		$this->data['variable_types'] = $this->get_types();
		$this->data['cfg']            = $this->cfg;
		$this->data['clear_cache_options'] = array(
			'n'       => lang('no'),
			'opt_in'  => lang('clear_cache_opt_in'),
			'opt_out' => lang('clear_cache_opt_out'),
			'y'       => lang('yes')
		);
		$this->data['register_globals_options'] = array(
			'n' => lang('no'),
			'y' => lang('register_globals_before'),
			'a' => lang('register_globals_after')
		);

		// -------------------------------------
		//  Build output
		// -------------------------------------

		//ee()->cp->set_variable('cp_page_title', ee()->lang->line('extension_settings'));
		ee()->cp->set_breadcrumb($this->base_url, ee()->lang->line('low_variables_module_name'));

		// -------------------------------------
		//  Load view
		// -------------------------------------

		return $this->view('ext_settings');
	}

	// --------------------------------------------------------------------

	/**
	 * Save extension settings
	 *
	 * @access     public
	 * @return     void
	 */
	public function save_settings()
	{
		// -------------------------------------
		// Loop through default settings,
		// put POST values in settings array
		// -------------------------------------

		foreach ($this->default_settings AS $key => $val)
		{
			$this->settings[$key] = ee()->input->post($key);
		}

		// -------------------------------------
		// Then apply config overrides
		// -------------------------------------

		$this->apply_config_overrides();

		// -------------------------------------
		// Make sure enabled_types is an array
		// -------------------------------------

		if ( ! is_array($this->settings['enabled_types']) )
		{
			$this->settings['enabled_types'] = array();
		}

		// -------------------------------------
		// Make sure enabled_types always contains the default type
		// -------------------------------------

		if ( ! in_array(LOW_VAR_DEFAULT_TYPE, $this->settings['enabled_types']))
		{
			$this->settings['enabled_types'][] = LOW_VAR_DEFAULT_TYPE;
		}

		// -------------------------------------
		// Make sure can_manage is an array
		// -------------------------------------

		if ( ! is_array($this->settings['can_manage']))
		{
			$this->settings['can_manage'] = array();
		}

		// -------------------------------------
		// Save the serialized settings in DB
		// -------------------------------------

		ee()->db->update(
			'extensions',
			array('settings' => serialize($this->settings)),
			"class = '".ucfirst($this->package)."_ext'"
		);

		// -------------------------------------
		// Redirect back to extension page
		// -------------------------------------

		$this->set_base_url();
		ee()->session->set_flashdata('msg', 'settings_saved');
		ee()->functions->redirect($this->ext_url);
		exit;
	}

	// --------------------------------------------------------------------

	/**
	 * Optionally adds variables to Global Vars for early parsing
	 *
	 * @access     public
	 * @param      object
	 * @return     object
	 */
	public function sessions_end($SESS)
	{
		// -------------------------------------
		//  Add extension settings to session cache
		// -------------------------------------

		$SESS->cache[$this->package]['settings'] = $this->settings;

		// -------------------------------------
		//  Do we have to sync files?
		// -------------------------------------

		if ($this->settings['save_as_files'] == 'y')
		{
			// Only if we're displaying the site or the module in the CP
			if (REQ == 'PAGE' || (REQ == 'CP' && ee()->input->get('module') == $this->package))
			{
				$this->sync_files();
			}
		}

		// -------------------------------------
		//  Check app version to see what to do
		// -------------------------------------

		if (version_compare(APP_VER, '2.4.0', '<') && REQ == 'PAGE' && $this->settings['register_globals'] != 'n')
		{
			$this->_add_vars($SESS);
		}

		return $SESS;
	}

	/**
	 * Add early parsed variables to config->_global_vars() array
	 *
	 * @access     public
	 * @param      array
	 * @return     array
	 */
	public function template_fetch_template($row)
	{
		// -------------------------------------------
		// Get the latest version of $row
		// -------------------------------------------

		if (ee()->extensions->last_call !== FALSE)
		{
			$row = ee()->extensions->last_call;
		}

		// -------------------------------------------
		// Call add_vars method
		// -------------------------------------------

		if ($this->settings['register_globals'] != 'n')
		{
			$this->_add_vars();
		}

		// Play nice, return it
		return $row;
	}

	/**
	 * Add early parsed variables to config->_global_vars() array
	 *
	 * @access     private
	 * @return     void
	 */
	private function _add_vars($session = FALSE)
	{
		// -------------------------------------
		//  Define static var to keep track of
		//  whether we've added vars already...
		// -------------------------------------

		static $added;

		// ...if so, just bail out
		if ($added) return;

		// -------------------------------------
		//  Initiate data array
		// -------------------------------------

		$early = array();

		// -------------------------------------
		//  Get global variables to parse early, ordered the way they're displayed in the CP
		// -------------------------------------

		$query = ee()->db->select(array('ee.variable_name', 'ee.variable_data'))
		       ->from('global_variables AS ee')
		       ->join('low_variables AS low', 'ee.variable_id = low.variable_id')
		       //->join('low_variable_groups AS lvg', 'low.group_id = lvg.group_id', 'left')
		       ->where('ee.site_id', $this->site_id)
		       ->where('low.early_parsing', 'y')
		       //->order_by('lvg.group_order')
		       ->order_by('low.group_id')
		       ->order_by('low.variable_order')
		       ->get();

		$early = low_flatten_results($query->result_array(), 'variable_data', 'variable_name');

		// -------------------------------------
		//  Are we registering member data?
		// -------------------------------------

		if ($this->settings['register_member_data'] == 'y')
		{
			// Variables to set
			$keys = array('member_id', 'group_id', 'group_description', 'username', 'screen_name',
			              'email', 'ip_address', 'location', 'total_entries', 'total_comments',
			              'private_messages', 'total_forum_posts', 'total_forum_topics');

			// Add logged_in_... vars to early parsing arrat
			foreach ($keys AS $key)
			{
				$early['logged_in_'.$key] = ($session) ? @$session->userdata[$key] : ee()->session->userdata($key);
			}
		}

		// -------------------------------------
		//  Look for existing language variable, set user language to it
		//  Disabled for now
		// -------------------------------------

		// if (isset(ee()->config->_global_vars['global:language']))
		// {
		// 	$SESS->userdata['language'] = ee()->config->_global_vars['global:language'];
		// }

		// -------------------------------------
		//  Add variables to early parsed global vars
		//  Option: make it a setting to switch order around?
		// -------------------------------------

		if ($early)
		{
			ee()->config->_global_vars
				= ($this->settings['register_globals'] == 'y')
				? array_merge($early, ee()->config->_global_vars)
				: array_merge(ee()->config->_global_vars, $early);
		}

		// Remember that we've added the vars so we don't do it again
		$added = TRUE;
	}

	// --------------------------------------------------------------------

} // End Class low_variables_ext

/* End of file ext.low_variables.php */