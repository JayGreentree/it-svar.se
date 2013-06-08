<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// include config file
if (file_exists(PATH_THIRD.'forms/config.php') === TRUE) include PATH_THIRD.'forms/config.php';
else include dirname(dirname(__FILE__)).'/forms/config.php';

/**
 * Install / Uninstall and updates the modules
 *
 * @package			DevDemon_Forms
 * @version			2.0
 * @author			DevDemon <http://www.devdemon.com> - Lead Developer @ Parscale Media
 * @copyright 		Copyright (c) 2007-2010 Parscale Media <http://www.parscale.com>
 * @license 		http://www.devdemon.com/license/
 * @link			http://www.devdemon.com/forms/
 * @see				http://expressionengine.com/user_guide/development/module_tutorial.html#update_file
 */
class Forms_upd
{
	/**
	 * Module version
	 *
	 * @var string
	 * @access public
	 */
	public $version		=	FORMS_VERSION;

	/**
	 * Module Short Name
	 *
	 * @var string
	 * @access private
	 */
	private $module_name	=	FORMS_CLASS_NAME;

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
		// EXP_FORMS
		//----------------------------------------
		$ci = array(
			'form_id'		=> array('type' => 'INT',		'unsigned' => TRUE,	'auto_increment' => TRUE),
			'site_id'		=> array('type' => 'TINYINT',	'unsigned' => TRUE,	'default' => 1),
			'entry_id'		=> array('type' => 'INT',		'unsigned' => TRUE, 'default' => 0),
			'channel_id'	=> array('type' => 'TINYINT',	'unsigned' => TRUE, 'default' => 0),
			'ee_field_id'	=> array('type' => 'MEDIUMINT',	'unsigned' => TRUE, 'default' => 0),
			'member_id'		=> array('type' => 'INT',		'unsigned' => TRUE, 'default' => 0),
			'form_type'		=> array('type' => 'VARCHAR',	'constraint' => 250, 'default' => ''),
			'form_title'	=> array('type' => 'VARCHAR',	'constraint' => 250, 'default' => ''),
			'form_url_title'	=> array('type' => 'VARCHAR',	'constraint' => 250, 'default' => ''),
			'custom_title'		=> array('type' => 'TINYINT',	'unsigned' => TRUE, 'default' => 0),
			'date_created'		=> array('type' => 'INT',		'unsigned' => TRUE, 'default' => 0),
			'date_last_entry'	=> array('type' => 'INT',		'unsigned' => TRUE, 'default' => 0),
			'total_submissions'	=> array('type' => 'INT',		'unsigned' => TRUE, 'default' => 0),
			'admin_template'	=> array('type' => 'MEDIUMINT',	'default' => 0), // -1= Custom, 0=None, +1=TemplateID
			'user_template'		=> array('type' => 'MEDIUMINT',	'default' => 0), // -1= Custom, 0=None, +1=TemplateID
			'form_settings'		=> array('type' => 'TEXT'),
		);

		$this->EE->dbforge->add_field($ci);
		$this->EE->dbforge->add_key('form_id', TRUE);
		$this->EE->dbforge->add_key('entry_id');
		$this->EE->dbforge->create_table('forms', TRUE);

		//----------------------------------------
		// EXP_FORMS_FIELDS
		//----------------------------------------
		$ci = array(
			'field_id'	=> array('type' => 'INT',		'unsigned' => TRUE,	'auto_increment' => TRUE),
			'form_id'	=> array('type' => 'INT',		'unsigned' => TRUE, 'default' => 0),
			'entry_id'	=> array('type' => 'INT',		'unsigned' => TRUE, 'default' => 0),
			'ee_field_id'	=> array('type' => 'INT',		'unsigned' => TRUE, 'default' => 0),
			'parent_id'		=> array('type' => 'INT',		'unsigned' => TRUE, 'default' => 0),
			'column_number'	=> array('type' => 'SMALLINT',	'unsigned' => TRUE, 'default' => 0),
			'title'		=> array('type' => 'VARCHAR',	'constraint' => 255, 'default' => ''),
			'url_title'	=> array('type' => 'VARCHAR',	'constraint' => 255, 'default' => ''),
			'description'	=> array('type' => 'TEXT'),
			'field_type'	=> array('type' => 'VARCHAR',	'constraint' => 255, 'default' => 'text'),
			'field_order'	=> array('type' => 'MEDIUMINT',	'unsigned' => TRUE,	'default' => 0),
			'required'		=> array('type' => 'TINYINT',	'unsigned' => TRUE, 'default' => 0),
			'show_label'	=> array('type' => 'TINYINT',	'unsigned' => TRUE, 'default' => 1), // 1=Auto, 0=Never, 2=Always
			'label_position'=> array('type' => 'VARCHAR',	'constraint' => 100, 'default' => 'auto'),
			'no_dupes'		=> array('type' => 'TINYINT',	'unsigned' => TRUE, 'default' => 0),
			'conditionals'	=> array('type' => 'TEXT'),
			'field_settings'	=> array('type' => 'TEXT'),
		);

		$this->EE->dbforge->add_field($ci);
		$this->EE->dbforge->add_key('field_id', TRUE);
		$this->EE->dbforge->add_key('form_id');
		$this->EE->dbforge->create_table('forms_fields', TRUE);

		//----------------------------------------
		// EXP_FORMS_ENTRIES
		//----------------------------------------
		$ci = array(
			'fentry_id'	=> array('type' => 'INT',		'unsigned' => TRUE,	'auto_increment' => TRUE),
			'fentry_hash'=> array('type' => 'VARCHAR',	'constraint' => 35, 'default' => ''),
			'site_id'	=> array('type' => 'SMALLINT',	'unsigned' => TRUE, 'default' => 1),
			'form_id'	=> array('type' => 'INT',		'unsigned' => TRUE, 'default' => 0),
			'member_id'	=> array('type' => 'INT',		'unsigned' => TRUE, 'default' => 0),
			'ip_address'	=> array('type' => 'INT',		'unsigned' => TRUE, 'default' => 0),
			'date'		=> array('type' => 'INT',		'unsigned' => TRUE, 'default' => 0),
			'country'	=> array('type' => 'VARCHAR',	'constraint' => 20, 'default' => ''),
			'email'		=> array('type' => 'VARCHAR',	'constraint' => 250, 'default' => ''),
			'debug_info'=> array('type' => 'TEXT'),
		);

		$this->EE->dbforge->add_field($ci);
		$this->EE->dbforge->add_key('fentry_id', TRUE);
		$this->EE->dbforge->add_key('form_id');
		$this->EE->dbforge->add_key('member_id');
		$this->EE->dbforge->create_table('forms_entries', TRUE);

		//----------------------------------------
		// EXP_FORMS_EMAIL_TEMPLATES
		//----------------------------------------
		$ci = array(
			'template_id'	=> array('type' => 'INT',		'unsigned' => TRUE,	'auto_increment' => TRUE),
			'form_id'		=> array('type' => 'INT',		'unsigned' => TRUE, 'default' => 0),
			'site_id'		=> array('type' => 'SMALLINT',	'unsigned' => TRUE, 'default' => 1),
			'template_label'=> array('type' => 'VARCHAR',	'constraint' => 250, 'default' => ''),
			'template_name'	=> array('type' => 'VARCHAR',	'constraint' => 250, 'default' => ''),
			'template_type'	=> array('type' => 'VARCHAR',	'constraint' => 10, 'default' => 'user'),
			'template_desc'	=> array('type' => 'VARCHAR',	'constraint' => 250, 'default' => ''),
			'ee_template_id'=> array('type' => 'INT',		'unsigned' => TRUE, 'default' => 0),
			'email_type'	=> array('type' => 'VARCHAR',	'constraint' => 50, 'default' => 'text'),
			'email_wordwrap'=> array('type' => 'VARCHAR',	'constraint' => 10, 'default' => 'no'),
			'email_to'		=> array('type' => 'VARCHAR',	'constraint' => 250, 'default' => ''),
			'email_from'	=> array('type' => 'VARCHAR',	'constraint' => 250, 'default' => ''),
			'email_from_email'=> array('type' => 'VARCHAR',	'constraint' => 250, 'default' => ''),
			'email_reply_to'=> array('type' => 'VARCHAR',	'constraint' => 250, 'default' => ''),
			'email_reply_to_email'=> array('type' => 'VARCHAR',	'constraint' => 250, 'default' => ''),
			'email_subject'	=> array('type' => 'VARCHAR',	'constraint' => 250, 'default' => ''),
			'email_cc'		=> array('type' => 'VARCHAR',	'constraint' => 250, 'default' => ''),
			'email_bcc'		=> array('type' => 'VARCHAR',	'constraint' => 250, 'default' => ''),
			'email_attachments'=> array('type' => 'VARCHAR',	'constraint' => 10, 'default' => 'no'),
			'reply_to_author'=> array('type' => 'VARCHAR',	'constraint' => 10, 'default' => 'yes'),
			'template'		=> array('type' => 'TEXT'),
			'alt_template'	=> array('type' => 'TEXT'),
		);

		$this->EE->dbforge->add_field($ci);
		$this->EE->dbforge->add_key('template_id', TRUE);
		$this->EE->dbforge->add_key('form_id');
		$this->EE->dbforge->create_table('forms_email_templates', TRUE);

		//----------------------------------------
		// EXP_FORMS_LISTS
		//----------------------------------------
		$ci = array(
			'list_id'	=> array('type' => 'INT',		'unsigned' => TRUE,	'auto_increment' => TRUE),
			'list_label'=> array('type' => 'VARCHAR',	'constraint' => 250, 'default' => ''),
			'list_data'	=> array('type' => 'TEXT'),
		);

		$this->EE->dbforge->add_field($ci);
		$this->EE->dbforge->add_key('list_id', TRUE);
		$this->EE->dbforge->create_table('forms_lists', TRUE);

		//----------------------------------------
		// EXP_ACTIONS
		//----------------------------------------
		$module = array( 'class' => ucfirst($this->module_name), 'method' => 'ACT_general_router');
		$this->EE->db->insert('actions', $module);
		$module = array( 'class' => ucfirst($this->module_name), 'method' => 'ACT_form_submission');
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
		// Insert Standard Data
		//----------------------------------------
		$this->EE->db->set('form_id', 0);
		$this->EE->db->set('template_label', 'Default Admin Email Notification Template');
		$this->EE->db->set('template_name', 'default_admin');
		$this->EE->db->set('template_type', 'admin');
		$this->EE->db->set('template_desc', '');
		$this->EE->db->set('email_type', 'text');
		$this->EE->db->set('email_wordwrap', 0);
		$this->EE->db->set('email_to', $this->EE->config->item('webmaster_email'));
		$this->EE->db->set('email_from', $this->EE->config->item('site_label'));
		$this->EE->db->set('email_from_email', $this->EE->config->item('webmaster_email'));
		$this->EE->db->set('email_reply_to', $this->EE->config->item('site_label'));
		$this->EE->db->set('email_reply_to_email', $this->EE->config->item('webmaster_email'));
		$this->EE->db->set('reply_to_author', 'yes');
		$this->EE->db->set('email_subject', 'New Form Submission: {form:label}');
		$this->EE->db->set('template', "
Someone submitted the {form:form_title} form:

Date: {datetime:usa}

{form:fields}
{field:count}. {field:label} : {field:value}
{/form:fields}
		");
		$this->EE->db->insert('exp_forms_email_templates');

		$this->EE->db->set('form_id', 0);
		$this->EE->db->set('template_label', 'Default User Email Notification Template');
		$this->EE->db->set('template_name', 'default_user');
		$this->EE->db->set('template_type', 'user');
		$this->EE->db->set('template_desc', '');
		$this->EE->db->set('email_type', 'text');
		$this->EE->db->set('email_wordwrap', 0);
		$this->EE->db->set('email_from', $this->EE->config->item('site_label'));
		$this->EE->db->set('email_from_email', $this->EE->config->item('webmaster_email'));
		$this->EE->db->set('email_reply_to', $this->EE->config->item('site_label'));
		$this->EE->db->set('email_reply_to_email', $this->EE->config->item('webmaster_email'));
		$this->EE->db->set('email_subject', 'Thank you for your submission!');
		$this->EE->db->set('template', "
Thank you for filling out the {form:form_title} form:

Date: {datetime:usa}

{form:fields}
{field:count}. {field:label} : {field:value}
{/form:fields}
		");
		$this->EE->db->insert('exp_forms_email_templates');

		// Form Lists
		include(PATH_THIRD . 'forms/config/forms_list_data.php');

		if (isset($cf_lists) == TRUE && empty($cf_lists) == FALSE)
		{
			foreach ($cf_lists as $list)
			{
				$this->EE->db->set('list_label', $list['name']);
				$this->EE->db->set('list_data', serialize($list['list']));
				$this->EE->db->insert('exp_forms_lists');
			}
		}

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

		// Remove
		$this->EE->dbforge->drop_table('forms');
		$this->EE->dbforge->drop_table('forms_fields');
		$this->EE->dbforge->drop_table('forms_entries');
		$this->EE->dbforge->drop_table('forms_lists');
		$this->EE->dbforge->drop_table('forms_email_templates');

		$this->EE->db->where('module_name', ucfirst($this->module_name));
		$this->EE->db->delete('modules');
		$this->EE->db->where('class', ucfirst($this->module_name));
		$this->EE->db->delete('actions');

		// $this->EE->cp->delete_layout_tabs($this->tabs(), 'points');

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
					$class = 'FormsUpdate_' . $ver;
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

/* End of file upd.forms.php */
/* Location: ./system/expressionengine/third_party/forms/upd.forms.php */
