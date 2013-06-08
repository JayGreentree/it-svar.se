<?php if (!defined('BASEPATH')) die('No direct script access allowed');

// include config file
require_once dirname(dirname(__FILE__)).'/editor/config.php';

/**
 * Install / Uninstall and updates the modules
 *
 * @package			DevDemon_Editor
 * @author			DevDemon <http://www.devdemon.com> - Lead Developer @ Parscale Media
 * @copyright 		Copyright (c) 2007-2012 Parscale Media <http://www.parscale.com>
 * @license 		http://www.devdemon.com/license/
 * @link			http://www.devdemon.com/editor/
 * @see				http://expressionengine.com/user_guide/development/module_tutorial.html#update_file
 */
class Editor_upd
{
	/**
	 * Module version
	 *
	 * @var string
	 * @access public
	 */
	public $version		=	EDITOR_VERSION;

	/**
	 * Module Short Name
	 *
	 * @var string
	 * @access private
	 */
	private $module_name	=	EDITOR_CLASS_NAME;

	/**
	 * Has Control Panel Backend?
	 *
	 * @var string
	 * @access private
	 */
	private $has_cp_backend = 'y';

	/**
	 * Has Publish Fields?
	 *
	 * @var string
	 * @access private
	 */
	private $has_publish_fields = 'n';


	/**
	 * Constructor
	 *
	 * @access public
	 * @return void
	 */
	public function __construct()
	{
		$this->EE =& get_instance();
	}

	// ********************************************************************************* //

	/**
	 * Installs the module
	 *
	 * Installs the module, adding a record to the exp_modules table,
	 * creates and populates and necessary database tables,
	 * adds any necessary records to the exp_actions table,
	 * and if custom tabs are to be used, adds those fields to any saved publish layouts
	 *
	 * @access public
	 * @return boolean
	 **/
	public function install()
	{
		// Load dbforge
		$this->EE->load->dbforge();

		//----------------------------------------
		// EXP_MODULES
		//----------------------------------------
		$module = array(	'module_name' => ucfirst($this->module_name),
							'module_version' => $this->version,
							'has_cp_backend' => 'y',
							'has_publish_fields' => 'n' );

		$this->EE->db->insert('modules', $module);

		//----------------------------------------
		// EXP_EDITOR_CONFIGS
		//----------------------------------------
		$fields = array(
			'config_id'			=> array('type' => 'INT',		'unsigned' => TRUE,	'auto_increment' => TRUE),
			'site_id'			=> array('type' => 'SMALLINT',	'unsigned' => TRUE,	'default' => 1),
			'config_label'		=> array('type' => 'VARCHAR',	'constraint' => 250, 'default' => ''),
			'config_settings'	=> array('type' => 'TEXT'),
		);

		$this->EE->dbforge->add_field($fields);
		$this->EE->dbforge->add_key('config_id', TRUE);
		$this->EE->dbforge->create_table('editor_configs', TRUE);

		//----------------------------------------
		// EXP_EDITOR_BUTTONS
		//----------------------------------------
		$fields = array(
			'id'				=> array('type' => 'INT',		'unsigned' => TRUE,	'auto_increment' => TRUE),
			'site_id'			=> array('type' => 'SMALLINT',	'unsigned' => TRUE,	'default' => 1),
			'config_id'			=> array('type' => 'INT',		'unsigned' => TRUE,	'default' => 1),
			'lowvar_id'			=> array('type' => 'INT',		'unsigned' => TRUE,	'default' => 1),
			'matrixcol_id'		=> array('type' => 'INT',		'unsigned' => TRUE,	'default' => 1),
			'button_class'		=> array('type' => 'VARCHAR',	'constraint' => 250, 'default' => ''),
			'button_settings'	=> array('type' => 'TEXT'),
		);

		$this->EE->dbforge->add_field($fields);
		$this->EE->dbforge->add_key('id', TRUE);
		$this->EE->dbforge->add_key('button_class');
		$this->EE->dbforge->add_key('config_id');
		$this->EE->dbforge->create_table('editor_buttons', TRUE);

		//----------------------------------------
		// Actions
		//----------------------------------------
		$module = array( 'class' => ucfirst($this->module_name), 'method' => 'ACT_file_upload');
		$this->EE->db->insert('actions', $module);

		//----------------------------------------
		// EXP_MODULES
		// The settings column, Ellislab should have put this one in long ago.
		// No need for a seperate preferences table for each module.
		//----------------------------------------
		if ($this->EE->db->field_exists('settings', 'modules') == FALSE)
		{
			$this->EE->dbforge->add_column('modules', array('settings' => array('type' => 'TEXT') ) );
		}

		//----------------------------------------
		// Add default configs
		//----------------------------------------
		$this->EE->db->query("INSERT INTO exp_editor_configs VALUES ('1', '1', 'Minimum', 'YToyMDp7czo3OiJidXR0b25zIjthOjc6e2k6MDtzOjEwOiJmb3JtYXR0aW5nIjtpOjE7czoxOiJ8IjtpOjI7czo0OiJib2xkIjtpOjM7czo2OiJpdGFsaWMiO2k6NDtzOjE6InwiO2k6NTtzOjQ6ImxpbmsiO2k6NjtzOjE6InwiO31zOjE0OiJ1cGxvYWRfc2VydmljZSI7czo1OiJsb2NhbCI7czoyMDoiZmlsZV91cGxvYWRfbG9jYXRpb24iO3M6MToiMCI7czoyMToiaW1hZ2VfdXBsb2FkX2xvY2F0aW9uIjtzOjE6IjAiO3M6MjoiczMiO2E6NDp7czo0OiJmaWxlIjthOjE6e3M6NjoiYnVja2V0IjtzOjA6IiI7fXM6NToiaW1hZ2UiO2E6MTp7czo2OiJidWNrZXQiO3M6MDoiIjt9czoxNDoiYXdzX2FjY2Vzc19rZXkiO3M6MDoiIjtzOjE0OiJhd3Nfc2VjcmV0X2tleSI7czowOiIiO31zOjY6ImhlaWdodCI7czozOiIyMDAiO3M6OToiZGlyZWN0aW9uIjtzOjM6Imx0ciI7czo3OiJ0b29sYmFyIjtzOjM6InllcyI7czo2OiJzb3VyY2UiO3M6MzoieWVzIjtzOjU6ImZvY3VzIjtzOjI6Im5vIjtzOjEwOiJhdXRvcmVzaXplIjtzOjM6InllcyI7czo1OiJmaXhlZCI7czoyOiJubyI7czoxMjoiY29udmVydGxpbmtzIjtzOjM6InllcyI7czoxMToiY29udmVydGRpdnMiO3M6MzoieWVzIjtzOjc6Im92ZXJsYXkiO3M6MzoieWVzIjtzOjEzOiJvYnNlcnZlaW1hZ2VzIjtzOjM6InllcyI7czozOiJhaXIiO3M6Mjoibm8iO3M6Mzoid3ltIjtzOjI6Im5vIjtzOjE4OiJhbGxvd2VkdGFnc19vcHRpb24iO3M6NzoiZGVmYXVsdCI7czoxMToiYWxsb3dlZHRhZ3MiO2E6MDp7fX0=')");
		$this->EE->db->query("INSERT INTO exp_editor_configs VALUES ('2', '1', 'Standard', 'YToyMDp7czo3OiJidXR0b25zIjthOjEzOntpOjA7czo0OiJodG1sIjtpOjE7czoxOiJ8IjtpOjI7czoxMDoiZm9ybWF0dGluZyI7aTozO3M6MToifCI7aTo0O3M6NDoiYm9sZCI7aTo1O3M6NjoiaXRhbGljIjtpOjY7czo3OiJkZWxldGVkIjtpOjc7czoxOiJ8IjtpOjg7czo0OiJsaW5rIjtpOjk7czo0OiJmaWxlIjtpOjEwO3M6NToiaW1hZ2UiO2k6MTE7czo1OiJ2aWRlbyI7aToxMjtzOjE6InwiO31zOjE0OiJ1cGxvYWRfc2VydmljZSI7czo1OiJsb2NhbCI7czoyMDoiZmlsZV91cGxvYWRfbG9jYXRpb24iO3M6MToiMCI7czoyMToiaW1hZ2VfdXBsb2FkX2xvY2F0aW9uIjtzOjE6IjAiO3M6MjoiczMiO2E6NDp7czo0OiJmaWxlIjthOjE6e3M6NjoiYnVja2V0IjtzOjA6IiI7fXM6NToiaW1hZ2UiO2E6MTp7czo2OiJidWNrZXQiO3M6MDoiIjt9czoxNDoiYXdzX2FjY2Vzc19rZXkiO3M6MDoiIjtzOjE0OiJhd3Nfc2VjcmV0X2tleSI7czowOiIiO31zOjY6ImhlaWdodCI7czozOiIyMDAiO3M6OToiZGlyZWN0aW9uIjtzOjM6Imx0ciI7czo3OiJ0b29sYmFyIjtzOjM6InllcyI7czo2OiJzb3VyY2UiO3M6MzoieWVzIjtzOjU6ImZvY3VzIjtzOjI6Im5vIjtzOjEwOiJhdXRvcmVzaXplIjtzOjM6InllcyI7czo1OiJmaXhlZCI7czoyOiJubyI7czoxMjoiY29udmVydGxpbmtzIjtzOjM6InllcyI7czoxMToiY29udmVydGRpdnMiO3M6MzoieWVzIjtzOjc6Im92ZXJsYXkiO3M6MzoieWVzIjtzOjEzOiJvYnNlcnZlaW1hZ2VzIjtzOjM6InllcyI7czozOiJhaXIiO3M6Mjoibm8iO3M6Mzoid3ltIjtzOjI6Im5vIjtzOjE4OiJhbGxvd2VkdGFnc19vcHRpb24iO3M6NzoiZGVmYXVsdCI7czoxMToiYWxsb3dlZHRhZ3MiO2E6MDp7fX0=');");
		$this->EE->db->query("INSERT INTO exp_editor_configs VALUES ('3', '1', 'Full', 'YToyNDp7czo3OiJidXR0b25zIjthOjMwOntpOjA7czo0OiJodG1sIjtpOjE7czoxOiJ8IjtpOjI7czoxMDoiZm9ybWF0dGluZyI7aTozO3M6MToifCI7aTo0O3M6NDoiYm9sZCI7aTo1O3M6NjoiaXRhbGljIjtpOjY7czo3OiJkZWxldGVkIjtpOjc7czoxOiJ8IjtpOjg7czoxMzoidW5vcmRlcmVkbGlzdCI7aTo5O3M6MTE6Im9yZGVyZWRsaXN0IjtpOjEwO3M6Nzoib3V0ZGVudCI7aToxMTtzOjY6ImluZGVudCI7aToxMjtzOjE6InwiO2k6MTM7czo0OiJsaW5rIjtpOjE0O3M6NToiaW1hZ2UiO2k6MTU7czo1OiJ2aWRlbyI7aToxNjtzOjQ6ImZpbGUiO2k6MTc7czo1OiJ0YWJsZSI7aToxODtzOjE6InwiO2k6MTk7czo5OiJmb250Y29sb3IiO2k6MjA7czo5OiJiYWNrY29sb3IiO2k6MjE7czoxOiJ8IjtpOjIyO3M6OToiYWxpZ25sZWZ0IjtpOjIzO3M6MTE6ImFsaWduY2VudGVyIjtpOjI0O3M6MTA6ImFsaWducmlnaHQiO2k6MjU7czo3OiJqdXN0aWZ5IjtpOjI2O3M6MToifCI7aToyNztzOjE0OiJob3Jpem9udGFscnVsZSI7aToyODtzOjExOiJwYXN0ZV9wbGFpbiI7aToyOTtzOjE6InwiO31zOjE0OiJ1cGxvYWRfc2VydmljZSI7czo1OiJsb2NhbCI7czoyMDoiZmlsZV91cGxvYWRfbG9jYXRpb24iO3M6MToiMCI7czoyMToiaW1hZ2VfdXBsb2FkX2xvY2F0aW9uIjtzOjE6IjAiO3M6MTQ6ImltYWdlX2Jyb3dzaW5nIjtzOjM6InllcyI7czoyOiJzMyI7YTo0OntzOjQ6ImZpbGUiO2E6MTp7czo2OiJidWNrZXQiO3M6MDoiIjt9czo1OiJpbWFnZSI7YToxOntzOjY6ImJ1Y2tldCI7czowOiIiO31zOjE0OiJhd3NfYWNjZXNzX2tleSI7czowOiIiO3M6MTQ6ImF3c19zZWNyZXRfa2V5IjtzOjA6IiI7fXM6NjoiaGVpZ2h0IjtzOjM6IjIwMCI7czo5OiJkaXJlY3Rpb24iO3M6MzoibHRyIjtzOjc6InRvb2xiYXIiO3M6MzoieWVzIjtzOjY6InNvdXJjZSI7czozOiJ5ZXMiO3M6NToiZm9jdXMiO3M6Mjoibm8iO3M6MTA6ImF1dG9yZXNpemUiO3M6MzoieWVzIjtzOjU6ImZpeGVkIjtzOjI6Im5vIjtzOjEyOiJjb252ZXJ0bGlua3MiO3M6MzoieWVzIjtzOjExOiJjb252ZXJ0ZGl2cyI7czozOiJ5ZXMiO3M6Nzoib3ZlcmxheSI7czozOiJ5ZXMiO3M6MTM6Im9ic2VydmVpbWFnZXMiO3M6MzoieWVzIjtzOjM6ImFpciI7czoyOiJubyI7czozOiJ3eW0iO3M6Mjoibm8iO3M6MTg6ImFsbG93ZWR0YWdzX29wdGlvbiI7czo3OiJkZWZhdWx0IjtzOjExOiJhbGxvd2VkdGFncyI7YTowOnt9czoxNDoiZm9ybWF0dGluZ3RhZ3MiO2E6Nzp7aTowO3M6MToicCI7aToxO3M6MTA6ImJsb2NrcXVvdGUiO2k6MjtzOjM6InByZSI7aTozO3M6MjoiaDEiO2k6NDtzOjI6ImgyIjtpOjU7czoyOiJoMyI7aTo2O3M6MjoiaDQiO31zOjg6Imxhbmd1YWdlIjtzOjI6ImVuIjtzOjg6ImNzc19maWxlIjtzOjA6IiI7fQ==')");
		$this->EE->db->query("INSERT INTO exp_editor_configs VALUES ('4', '1', 'Full (Visual Mode)', 'YToyMzp7czo3OiJidXR0b25zIjthOjMwOntpOjA7czo0OiJodG1sIjtpOjE7czoxOiJ8IjtpOjI7czoxMDoiZm9ybWF0dGluZyI7aTozO3M6MToifCI7aTo0O3M6NDoiYm9sZCI7aTo1O3M6NjoiaXRhbGljIjtpOjY7czo3OiJkZWxldGVkIjtpOjc7czoxOiJ8IjtpOjg7czoxMzoidW5vcmRlcmVkbGlzdCI7aTo5O3M6MTE6Im9yZGVyZWRsaXN0IjtpOjEwO3M6Nzoib3V0ZGVudCI7aToxMTtzOjY6ImluZGVudCI7aToxMjtzOjE6InwiO2k6MTM7czo0OiJsaW5rIjtpOjE0O3M6NToiaW1hZ2UiO2k6MTU7czo1OiJ2aWRlbyI7aToxNjtzOjQ6ImZpbGUiO2k6MTc7czo1OiJ0YWJsZSI7aToxODtzOjE6InwiO2k6MTk7czo5OiJmb250Y29sb3IiO2k6MjA7czo5OiJiYWNrY29sb3IiO2k6MjE7czoxOiJ8IjtpOjIyO3M6OToiYWxpZ25sZWZ0IjtpOjIzO3M6MTE6ImFsaWduY2VudGVyIjtpOjI0O3M6MTA6ImFsaWducmlnaHQiO2k6MjU7czo3OiJqdXN0aWZ5IjtpOjI2O3M6MToifCI7aToyNztzOjE0OiJob3Jpem9udGFscnVsZSI7aToyODtzOjExOiJwYXN0ZV9wbGFpbiI7aToyOTtzOjE6InwiO31zOjE0OiJ1cGxvYWRfc2VydmljZSI7czo1OiJsb2NhbCI7czoyMDoiZmlsZV91cGxvYWRfbG9jYXRpb24iO3M6MToiMCI7czoyMToiaW1hZ2VfdXBsb2FkX2xvY2F0aW9uIjtzOjE6IjAiO3M6MTQ6ImltYWdlX2Jyb3dzaW5nIjtzOjM6InllcyI7czoyOiJzMyI7YTo0OntzOjQ6ImZpbGUiO2E6MTp7czo2OiJidWNrZXQiO3M6MDoiIjt9czo1OiJpbWFnZSI7YToxOntzOjY6ImJ1Y2tldCI7czowOiIiO31zOjE0OiJhd3NfYWNjZXNzX2tleSI7czowOiIiO3M6MTQ6ImF3c19zZWNyZXRfa2V5IjtzOjA6IiI7fXM6NjoiaGVpZ2h0IjtzOjM6IjIwMCI7czo5OiJkaXJlY3Rpb24iO3M6MzoibHRyIjtzOjc6InRvb2xiYXIiO3M6MzoieWVzIjtzOjY6InNvdXJjZSI7czozOiJ5ZXMiO3M6NToiZm9jdXMiO3M6Mjoibm8iO3M6MTA6ImF1dG9yZXNpemUiO3M6MzoieWVzIjtzOjU6ImZpeGVkIjtzOjI6Im5vIjtzOjEyOiJjb252ZXJ0bGlua3MiO3M6MzoieWVzIjtzOjExOiJjb252ZXJ0ZGl2cyI7czozOiJ5ZXMiO3M6Nzoib3ZlcmxheSI7czozOiJ5ZXMiO3M6MTM6Im9ic2VydmVpbWFnZXMiO3M6MzoieWVzIjtzOjM6ImFpciI7czoyOiJubyI7czozOiJ3eW0iO3M6MzoieWVzIjtzOjE4OiJhbGxvd2VkdGFnc19vcHRpb24iO3M6NzoiZGVmYXVsdCI7czoxMToiYWxsb3dlZHRhZ3MiO2E6MDp7fXM6MTQ6ImZvcm1hdHRpbmd0YWdzIjthOjc6e2k6MDtzOjE6InAiO2k6MTtzOjEwOiJibG9ja3F1b3RlIjtpOjI7czozOiJwcmUiO2k6MztzOjI6ImgxIjtpOjQ7czoyOiJoMiI7aTo1O3M6MjoiaDMiO2k6NjtzOjI6Img0Ijt9czo4OiJsYW5ndWFnZSI7czoyOiJlbiI7fQ==')");
		$this->EE->db->query("INSERT INTO exp_editor_configs VALUES ('5', '1', 'Standard (Visual Mode)', 'YToyMDp7czo3OiJidXR0b25zIjthOjEzOntpOjA7czo0OiJodG1sIjtpOjE7czoxOiJ8IjtpOjI7czoxMDoiZm9ybWF0dGluZyI7aTozO3M6MToifCI7aTo0O3M6NDoiYm9sZCI7aTo1O3M6NjoiaXRhbGljIjtpOjY7czo3OiJkZWxldGVkIjtpOjc7czoxOiJ8IjtpOjg7czo0OiJsaW5rIjtpOjk7czo0OiJmaWxlIjtpOjEwO3M6NToiaW1hZ2UiO2k6MTE7czo1OiJ2aWRlbyI7aToxMjtzOjE6InwiO31zOjE0OiJ1cGxvYWRfc2VydmljZSI7czo1OiJsb2NhbCI7czoyMDoiZmlsZV91cGxvYWRfbG9jYXRpb24iO3M6MToiMCI7czoyMToiaW1hZ2VfdXBsb2FkX2xvY2F0aW9uIjtzOjE6IjAiO3M6MjoiczMiO2E6NDp7czo0OiJmaWxlIjthOjE6e3M6NjoiYnVja2V0IjtzOjA6IiI7fXM6NToiaW1hZ2UiO2E6MTp7czo2OiJidWNrZXQiO3M6MDoiIjt9czoxNDoiYXdzX2FjY2Vzc19rZXkiO3M6MDoiIjtzOjE0OiJhd3Nfc2VjcmV0X2tleSI7czowOiIiO31zOjY6ImhlaWdodCI7czozOiIyMDAiO3M6OToiZGlyZWN0aW9uIjtzOjM6Imx0ciI7czo3OiJ0b29sYmFyIjtzOjM6InllcyI7czo2OiJzb3VyY2UiO3M6MzoieWVzIjtzOjU6ImZvY3VzIjtzOjI6Im5vIjtzOjEwOiJhdXRvcmVzaXplIjtzOjM6InllcyI7czo1OiJmaXhlZCI7czoyOiJubyI7czoxMjoiY29udmVydGxpbmtzIjtzOjM6InllcyI7czoxMToiY29udmVydGRpdnMiO3M6MzoieWVzIjtzOjc6Im92ZXJsYXkiO3M6MzoieWVzIjtzOjEzOiJvYnNlcnZlaW1hZ2VzIjtzOjM6InllcyI7czozOiJhaXIiO3M6Mjoibm8iO3M6Mzoid3ltIjtzOjM6InllcyI7czoxODoiYWxsb3dlZHRhZ3Nfb3B0aW9uIjtzOjc6ImRlZmF1bHQiO3M6MTE6ImFsbG93ZWR0YWdzIjthOjA6e319')");

		return TRUE;
	}

	// ********************************************************************************* //

	/**
	 * Uninstalls the module
	 *
	 * @access public
	 * @return Boolean FALSE if uninstall failed, TRUE if it was successful
	 **/
	function uninstall()
	{
		// Load dbforge
		$this->EE->load->dbforge();

		$this->EE->dbforge->drop_table('editor_configs');
		$this->EE->dbforge->drop_table('editor_buttons');

		$this->EE->db->where('module_name', ucfirst($this->module_name));
		$this->EE->db->delete('modules');
		$this->EE->db->where('class', ucfirst($this->module_name));
		$this->EE->db->delete('actions');

		return TRUE;
	}

	// ********************************************************************************* //

	/**
	 * Updates the module
	 *
	 * This function is checked on any visit to the module's control panel,
	 * and compares the current version number in the file to
	 * the recorded version in the database.
	 * This allows you to easily make database or
	 * other changes as new versions of the module come out.
	 *
	 * @access public
	 * @return Boolean FALSE if no update is necessary, TRUE if it is.
	 **/
	public function update($current = '')
	{
		// Are they the same?
		if ($current >= $this->version)
		{
			return FALSE;
		}

		$this->EE->load->dbforge();

		$current = str_replace('.', '', $current);

		// Two Digits? (needs to be 3)
		if (strlen($current) == 2) $current .= '0';

		$update_dir = PATH_THIRD.strtolower($this->module_name).'/updates/';

		// Does our folder exist?
		if (@is_dir($update_dir) === TRUE)
		{
			// Loop over all files
			$files = @scandir($update_dir);

			if (is_array($files) == TRUE)
			{
				foreach ($files as $file)
				{
					if ($file == '.' OR $file == '..' OR strtolower($file) == '.ds_store') continue;

					// Get the version number
					$ver = substr($file, 0, -4);

					// We only want greater ones
					if ($current >= $ver) continue;

					require $update_dir . $file;
					$class = 'EditorUpdate_' . $ver;
					$UPD = new $class();
					$UPD->do_update();
				}
			}
		}

		// Upgrade The Module
		$this->EE->db->set('module_version', $this->version);
		$this->EE->db->where('module_name', ucfirst($this->module_name));
		$this->EE->db->update('exp_modules');

		return TRUE;
	}

} // END CLASS

/* End of file upd.editor.php */
/* Location: ./system/expressionengine/third_party/editor/upd.editor.php */
