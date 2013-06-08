<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// include base class
if ( ! class_exists('Low_variables_base'))
{
	require_once(PATH_THIRD.'low_variables/base.low_variables.php');
}

/**
 * Low Variables UPD class
 *
 * @package        low_variables
 * @author         Lodewijk Schutte <hi@gotolow.com>
 * @link           http://gotolow.com/addons/low-variables
 * @copyright      Copyright (c) 2009-2013, Low
 */
class Low_variables_upd extends Low_variables_base {

	// --------------------------------------------------------------------
	// PROPERTIES
	// --------------------------------------------------------------------

	/**
	 * Extension hooks
	 *
	 * @var        array
	 * @access     private
	 */
	private $hooks = array(
		'sessions_end',
		'template_fetch_template'
	);

	/**
	 * Class name
	 *
	 * @var        string
	 * @access     private
	 */
	private $class_name;

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
		// Call parent constructor
		parent::__construct();

		// Set class name
		$this->class_name = ucfirst(LOW_VAR_PACKAGE);
	}

	// --------------------------------------------------------------------

	/**
	 * Install the module
	 *
	 * @access      public
	 * @return      bool
	 */
	function install()
	{
		// --------------------------------------
		// Install tables
		// --------------------------------------

		ee()->db->query(
			sprintf("CREATE TABLE IF NOT EXISTS `exp_low_variables` (
				`variable_id` int(6) unsigned NOT NULL,
				`group_id` int(6) unsigned default 0 NOT NULL,
				`variable_label` varchar(100) default '' NOT NULL,
				`variable_notes` text NOT NULL,
				`variable_type` varchar(50) default '%s' NOT NULL,
				`variable_settings` text NOT NULL,
				`variable_order` int(4) unsigned default 0 NOT NULL,
				`early_parsing` char(1) default 'n' NOT NULL,
				`is_hidden` char(1) default 'n' NOT NULL,
				`save_as_file` char(1) default 'n' NOT NULL,
				`edit_date` int(10) unsigned default 0 NOT NULL,
				PRIMARY KEY (`variable_id`),
				KEY (`group_id`)
			) CHARACTER SET utf8 COLLATE utf8_general_ci",
			LOW_VAR_DEFAULT_TYPE)
		);

		$this->_create_groups_table();

		// --------------------------------------
		// Add row to modules table
		// --------------------------------------

		ee()->db->insert('exp_modules', array(
			'module_name'    => $this->class_name,
			'module_version' => LOW_VAR_VERSION,
			'has_cp_backend' => 'y'
		));

		// --------------------------------------
		// Add row to modules table
		// --------------------------------------

		foreach ($this->hooks AS $hook)
		{
			$this->_add_hook($hook);
		}

		return TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Uninstall the module
	 *
	 * @return	bool
	 */
	function uninstall()
	{
		// get module id
		ee()->db->select('module_id');
		ee()->db->from('modules');
		ee()->db->where('module_name', $this->class_name);
		$query = ee()->db->get();

		// remove references from module_member_groups
		ee()->db->where('module_id', $query->row('module_id'));
		ee()->db->delete('module_member_groups');

		// remove references from modules
		ee()->db->where('module_name', $this->class_name);
		ee()->db->delete('modules');

		// remove reference from extension
		ee()->db->where('class', $this->class_name.'_ext');
		ee()->db->delete('extensions');

		// Drop custom tables
		ee()->db->query("DROP TABLE IF EXISTS `exp_low_variables`");
		ee()->db->query("DROP TABLE IF EXISTS `exp_low_variable_groups`");

		return TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Update the module
	 *
	 * @return	bool
	 */
	function update($current = '')
	{
		// -------------------------------------
		//  Same version? A-okay, daddy-o!
		// -------------------------------------

		if ($current == '' OR version_compare($current, $this->version) === 0)
		{
			return FALSE;
		}

		// Extension data
		$ext_data = array('version' => $this->version);

		// -------------------------------------
		//  Upgrade to 1.2.5
		// -------------------------------------

		if (version_compare($current, '1.2.5', '<'))
		{
			$this->settings['enabled_types'] = array_keys($this->get_types());

			$ext_data['settings'] = serialize($this->settings);
		}

		// -------------------------------------
		//  Upgrade to 1.3.2
		// -------------------------------------

		if (version_compare($current, '1.3.2', '<'))
		{
			$this->_v132();
		}

		// -------------------------------------
		//  Upgrade to 1.3.4
		// -------------------------------------

		if (version_compare($current, '1.3.4', '<'))
		{
			$this->_v134();
		}

		// -------------------------------------
		//  Upgrade to 2.0.0
		// -------------------------------------

		if (version_compare($current, '2.0.0', '<'))
		{
			$this->_v200();
		}

		// -------------------------------------
		//  Upgrade to 2.0.0
		// -------------------------------------

		if (version_compare($current, '2.1.0', '<'))
		{
			$this->_add_hook('template_fetch_template');
		}

		// Update the extension in the DB
		ee()->db->update('extensions', $ext_data, "class = '".$this->class_name."_ext'");

		// Return TRUE to update version number in DB
		return TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Do update to 1.3.2
	 */
	private function _v132()
	{
		// Add group_id foreign key in table
		ee()->db->query("ALTER TABLE `exp_low_variables` ADD `group_id` INT(6) UNSIGNED default 0 NOT NULL AFTER `variable_id`");
		$this->_create_groups_table();

		// Pre-populate groups, only if settings are found
		if ($settings = low_get_cache(LOW_VAR_PACKAGE, 'settings'))
		{
			// Do not pre-populate groups if group settings was not Y
			if (isset($settings['group']) && $settings['group'] != 'y') return;

			// Initiate groups array
			$groups = array();

			// Get all variables that have a low variables reference
			$sql = "SELECT ee.variable_id AS var_id, ee.variable_name AS var_name, ee.site_id
					FROM exp_global_variables AS ee, exp_low_variables AS low
					WHERE ee.variable_id = low.variable_id";
			$query = ee()->db->query($sql);

			// Loop through each variable, see if group applies
			foreach ($query->result_array() AS $row)
			{
				// strip off prefix
				if ($settings['prefix'])
				{
					$row['var_name'] = preg_replace('#^'.preg_quote($settings['prefix']).'_#', '', $row['var_name']);
				}

				// Get faux group name
				$tmp = explode('_', $row['var_name'], 2);
				$group = $tmp[0];
				unset($tmp);

				// Create new group if it does not exist
				if ( ! array_key_exists($group, $groups))
				{
					ee()->db->insert('exp_low_variable_groups', array(
						'group_label' => ucfirst($group),
						'site_id' => $row['site_id']
					));
					$groups[$group] = ee()->db->insert_id();
				}

				// Update Low Variable
				ee()->db->update('exp_low_variables', array(
					'group_id' => $groups[$group]
				), "variable_id = '{$row['var_id']}'");
			}
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Do update to 1.3.4
	 */
	private function _v134()
	{
		// Add group_id foreign key in table
		ee()->db->query("ALTER TABLE `exp_low_variables` ADD `is_hidden` CHAR(1) NOT NULL DEFAULT 'n'");

		// Set new attribute, only if settings are found
		if ($settings = low_get_cache(LOW_VAR_PACKAGE, 'settings'))
		{
			// Only update variables if prefix was filled in
			if ($prefix_length = strlen(@$settings['prefix']))
			{
				$sql = "SELECT variable_id FROM `exp_global_variables` WHERE LEFT(variable_name, {$prefix_length}) = '".ee()->db->escape_str($settings['prefix'])."'";
				$query = ee()->db->query($sql);
				if ($ids = low_flatten_results($query->result_array(), 'variable_id'))
				{
					// Hide wich vars
					$sql_in = $settings['with_prefixed'] == 'show' ? 'NOT IN' : 'IN';

					// Execute query
					ee()->db->query("UPDATE `exp_low_variables` SET is_hidden = 'y' WHERE variable_id {$sql_in} (".implode(',', $ids).")");
				}
			}

			// Update settings
			unset($settings['prefix'], $settings['with_prefixed'], $settings['ignore_prefixes']);
			ee()->db->query("UPDATE `exp_extensions` SET settings = '".ee()->db->escape_str(serialize($settings))."' WHERE class = 'Low_variables_ext'");
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Do update to 2.0.0
	 */
	private function _v200()
	{
		// Add extra table attrs
		ee()->db->query("ALTER TABLE `exp_low_variables` ADD `save_as_file` char(1) NOT NULL DEFAULT 'n'");
		ee()->db->query("ALTER TABLE `exp_low_variables` ADD `edit_date` int(10) unsigned default 0 NOT NULL");

		// Change settings to smaller array
		$query = ee()->db->select('variable_id, variable_type, variable_settings')->from('low_variables')->get();

		foreach ($query->result_array() AS $row)
		{
			$settings = unserialize($row['variable_settings']);
			$settings = low_array_encode($settings[$row['variable_type']]);

			ee()->db->where('variable_id', $row['variable_id']);
			ee()->db->update('low_variables', array('variable_settings' => $settings));
		}

	}

	// --------------------------------------------------------------------

	/**
	 * Create groups table
	 */
	private function _create_groups_table()
	{
		ee()->db->query("CREATE TABLE IF NOT EXISTS `exp_low_variable_groups` (
					`group_id` int(6) unsigned NOT NULL AUTO_INCREMENT,
					`site_id` int(6) unsigned default 1 NOT NULL,
					`group_label` varchar(100) default '' NOT NULL,
					`group_notes` text NOT NULL,
					`group_order` int(4) unsigned default 0 NOT NULL,
					PRIMARY KEY (`group_id`))
					CHARACTER SET utf8 COLLATE utf8_general_ci");
	}

	// --------------------------------------------------------------------

	/**
	 * Add extension hook
	 *
	 * @access     private
	 * @param      string
	 * @return     void
	 */
	private function _add_hook($name)
	{
		ee()->db->insert('extensions',
			array(
				'class'    => $this->class_name.'_ext',
				'method'   => $name,
				'hook'     => $name,
				'settings' => serialize($this->settings),
				'priority' => 2,
				'version'  => $this->version,
				'enabled'  => 'y'
			)
		);
	}

} // End Class Low_variables_upd

/* End of file upd.low_variables.php */