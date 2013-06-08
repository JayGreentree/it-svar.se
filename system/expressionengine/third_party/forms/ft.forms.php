<?php if (!defined('BASEPATH')) die('No direct script access allowed');

// include config file
if (file_exists(PATH_THIRD.'forms/config.php') === TRUE) include PATH_THIRD.'forms/config.php';
else include dirname(dirname(__FILE__)).'/forms/config.php';

/**
 * Channel Forms Module FieldType
 *
 * @package			DevDemon_Forms
 * @author			DevDemon <http://www.devdemon.com> - Lead Developer @ Parscale Media
 * @copyright 		Copyright (c) 2007-2011 Parscale Media <http://www.parscale.com>
 * @license 		http://www.devdemon.com/license/
 * @link			http://www.devdemon.com/forms/
 * @see				http://expressionengine.com/user_guide/development/fieldtypes.html
 */
class Forms_ft extends EE_Fieldtype
{

	/**
	 * Field info - Required
	 *
	 * @access public
	 * @var array
	 */
	public $info = array(
		'name' 		=> FORMS_NAME,
		'version'	=> FORMS_VERSION
	);

	/**
	 * The field settings array
	 *
	 * @access public
	 * @var array
	 */
	public $settings = array();

	public $has_array_data = TRUE;

	/**
	 * Constructor
	 *
	 * @access public
	 *
	 * Calls the parent constructor
	 */
	public function __construct()
	{
		if (version_compare(APP_VER, '2.1.4', '>')) { parent::__construct(); } else { parent::EE_Fieldtype(); }

		$this->EE->load->add_package_path(PATH_THIRD . 'forms/');
		$this->EE->lang->loadfile('forms');
		$this->EE->load->library('forms_helper');
		$this->EE->forms_helper->define_theme_url();
		$this->EE->config->load('forms_config');

		if ($this->EE->input->get('return') != 'addons_modules') $this->EE->load->model('forms_model');

		$this->site_id = $this->EE->forms_helper->get_current_site_id();
	}

	// ********************************************************************************* //

	function display_field($data)
	{
		// -----------------------------------------
		// Global Vars
		// -----------------------------------------
		$vData = array();
		$vData['missing_settings'] = FALSE;
		$vData['settings'] = array();
		$vData['field_name'] = $this->field_name;
		$vData['field_id'] = $this->field_id;
		$vData['site_id'] = $this->site_id;
		$vData['channel_id'] = ($this->EE->input->get_post('channel_id') != FALSE) ? $this->EE->input->get_post('channel_id') : 0;
		$vData['entry_id'] = ($this->EE->input->get_post('entry_id') != FALSE) ? $this->EE->input->get_post('entry_id') : FALSE;
		$vData['form'] = array();
		$vData['dbfields'] = array();

		// -----------------------------------------
		// Add JS & CSS
		// -----------------------------------------
		$this->EE->forms_helper->mcp_meta_parser('gjs', '', 'Forms');
		$this->EE->forms_helper->mcp_meta_parser('css', FORMS_THEME_URL . 'bootstrap.popovers.css', 'bootstrap.popovers');
		$this->EE->forms_helper->mcp_meta_parser('css', FORMS_THEME_URL . 'chosen/chosen.css', 'jquery.chosen');
		$this->EE->forms_helper->mcp_meta_parser('css', FORMS_THEME_URL . 'forms_pbf.css', 'cfo-pbf');
		$this->EE->forms_helper->mcp_meta_parser('css', FORMS_THEME_URL . 'forms_builder.css', 'cfo-builder');
		$this->EE->forms_helper->mcp_meta_parser('css', FORMS_THEME_URL . 'forms_base.css', 'cfo-base');

		$this->EE->forms_helper->mcp_meta_parser('js', FORMS_THEME_URL . 'bootstrap.popovers.js', 'bootstrap.popovers', 'bootstrap');
		$this->EE->forms_helper->mcp_meta_parser('js', FORMS_THEME_URL . 'bootstrap.modal.js', 'bootstrap.modal', 'bootstrap');
		$this->EE->forms_helper->mcp_meta_parser('js', FORMS_THEME_URL . 'chosen/jquery.chosen.js', 'jquery.chosen', 'jquery');
		$this->EE->forms_helper->mcp_meta_parser('js', FORMS_THEME_URL . 'jquery.base64.js', 'jquery.base64', 'jquery');
		$this->EE->forms_helper->mcp_meta_parser('js', FORMS_THEME_URL . 'jquery.stringtoslug.min.js', 'jquery.stringtoslug', 'jquery');
		//$this->EE->forms_helper->mcp_meta_parser('js', FORMS_THEME_URL . 'hogan.js', 'hogan', 'hogan');
		$this->EE->forms_helper->mcp_meta_parser('js', FORMS_THEME_URL . 'forms_pbf.js', 'cfo-pbf');
		$this->EE->forms_helper->mcp_meta_parser('js', FORMS_THEME_URL . 'forms_builder.js', 'cfo-builder');
		$this->EE->cp->add_js_script(array('ui' => array('sortable', 'draggable')));

		// -----------------------------------------
		// Check settings
		// -----------------------------------------
		if (isset($this->settings['forms']['fields']) == FALSE OR empty($this->settings['forms']['fields']))
		{
			$vData['missing_settings'] = TRUE;
			return $this->EE->load->view('pbf_field', $vData, TRUE);
		}

		// -----------------------------------------
		// Load Settings
		// -----------------------------------------
		$vData['dbfieldjson'] = '{}';
		$vData['form'] = array();
		$vData['form']['form_id'] = 0;
		$vData['form']['admin_template'] = 0;
		$vData['form']['user_template'] = 0;
		$vData['form']['settings'] = $this->EE->config->item('cf_formsettings'); // Default form settings
		$vData['form']['fields'] = $this->settings['forms']['fields'];
		$vData['form']['field_settings'] = $this->settings['forms']['field_settings'];

		// -----------------------------------------
		// Add Config
		// -----------------------------------------
		$vData['config'] = $this->EE->config->item('cf_dropdown_options');

		// -----------------------------------------
		// Grab all Email Templates
		// -----------------------------------------
		$vData['email_templates']['admin'] = array();
		$vData['email_templates']['user'] = array();

		$query = $this->EE->db->select('template_id, template_label, template_type')->from('forms_email_templates')->where('site_id', $this->site_id)->where('form_id', 0)->order_by('template_label')->get();

		foreach($query->result() as $row)
		{
			$vData['email_templates'][$row->template_type][$row->template_id] = $row->template_label;
		}

		// Add Default Template Settings
		$template_fields = $this->EE->db->list_fields('exp_forms_email_templates');

		foreach ($template_fields as $field)
		{
			$vData['form']['templates']['admin'][$field] = '';
			$vData['form']['templates']['user'][$field] = '';
		}

		// -----------------------------------------
		// Grab Member Groups
		// -----------------------------------------
		$mgroups = $this->EE->db->query("SELECT group_id, group_title FROM exp_member_groups WHERE site_id = {$this->site_id} AND group_id != 1");
		foreach($mgroups->result() as $row) $vData['member_groups'][$row->group_id] = $row->group_title;
		$mgroups->free_result();

		// -----------------------------------------
		// Lets grab data!
		// -----------------------------------------
		if ($vData['entry_id'] != FALSE)
		{
			// -----------------------------------------
			// Grab the form!
			// -----------------------------------------
			$this->EE->db->select('*');
			$this->EE->db->from('exp_forms');
			$this->EE->db->where('entry_id', $vData['entry_id']);
			$this->EE->db->where('ee_field_id', $this->field_id);
			$query = $this->EE->db->get();

			if ($query->num_rows() == 1)
			{
				$vData['form'] = $this->EE->forms_helper->array_extend($vData['form'], $query->row_array());
				$vData['form']['settings'] = $this->EE->forms_helper->array_extend($vData['form']['settings'], unserialize($vData['form']['form_settings']));

				// Add template data
				foreach ($template_fields as $field)
				{
					$vData['form']['templates']['admin'][$field] = '';
					$vData['form']['templates']['user'][$field] = '';
				}


				// -----------------------------------------
				// Grab all assigned Templates
				// -----------------------------------------
				foreach(array('admin', 'user') as $type)
				{
					if ($vData['form'][$type.'_template'] == -1)
					{
						$query = $this->EE->db->select('*')->from('exp_forms_email_templates')->where('form_id', $vData['form']['form_id'])->where('template_type', $type)->limit(1)->get();
						if ($query->num_rows > 0)
						{
							$vData['form']['templates'][$type] = $query->row_array();
						}
						else
						{
							$vData['form'][$type.'_template'] = 0;
						}
					}
				}

			}

		}
/*
		// -----------------------------------------
		// Process Fields
		// -----------------------------------------
		if (isset($this->EE->session->cache['Forms']['JSON_defaultfields']) == FALSE)
		{
			// Store
			$fieldjson = array();

			// Loop over all categories
			foreach($vData['form']['fields'] as $catfields)
			{
				// Loop over all categories within this field
				foreach($catfields as $class_name)
				{
					// Lets make our field
					$field = array();
					$field['title'] = $this->EE->formsfields[$class_name]->info['title'];
					$field['url_title'] = $class_name;
					$field['description'] = '';
					$field['field_type'] = $class_name;
					$field['field_type_label'] = $this->EE->formsfields[$class_name]->info['title'];
					$field['settings'] = array();

					// Form input name="" for the field title/url_title/desc
					$field['form_name'] = $vData['field_name'].'[fields][]';

					// Create out JSON. We do it here to keep the JSON as clean as possible
					$fieldjson[$class_name] = $field;

					// Form input name="" for custom field settings
					$field['form_name_settings'] = $vData['field_name'].'[fields]['.mt_rand(0, 999999).'][settings]';

					// Do we have any field settings stored?
					if (isset($vData['form']['field_settings'][$class_name]) == TRUE)
					{
						$field['settings'] = $vData['form']['field_settings'][$class_name];
					}

					// We need to add the form settings
					$field['form_settings'] = $vData['form']['settings'];

					// Continue with out JSON
					$fieldjson[$class_name]['field_content'] = $this->EE->formsfields[$class_name]->display_field($field, FALSE);
					$fieldjson[$class_name]['field_settings'] = $this->EE->formsfields[$class_name]->display_settings($field, FALSE);
					$fieldjson[$class_name]['field_required'] = FALSE;
					$fieldjson[$class_name]['field_id'] = 0;
				}
			}

			$vData['fieldjson'] = $this->EE->forms_helper->generate_json($fieldjson);
		}
*/
		// -----------------------------------------
		// Add Help!
		// -----------------------------------------
		$helpjson = array();
		$alertjson = array();

		foreach ($this->EE->lang->language as $key => $val)
		{
			if (strpos($key, 'form:help:') === 0)
			{
				$helpjson[substr($key, 10)] = $val;
				unset($this->EE->lang->language[$key]);
			}

			if (strpos($key, 'form:alert:') === 0)
			{
				$alertjson[substr($key, 11)] = $val;
				unset($this->EE->lang->language[$key]);
			}

		}

		$helpjson = $this->EE->forms_helper->generate_json($helpjson);
		$alertjson = $this->EE->forms_helper->generate_json($alertjson);

		$js = '
		<script type="text/javascript">
		Forms.JSON = Forms.JSON ? Forms.JSON : {};
		';

		$js .= "
		Forms.JSON.Help = {$helpjson};
		Forms.JSON.Alerts = {$alertjson};
		</script>
		";

		$this->EE->cp->add_to_foot($js);

		return $this->EE->load->view('pbf/field', $vData, TRUE);
	}

	// ********************************************************************************* //

	public function replace_tag($data, $params=array(), $tagdata=FALSE)
	{
		if (class_exists('Forms') == FALSE) include PATH_THIRD.'forms/mod.forms.php';
		$F = new Forms();

		// Lets cache the entire entry row
		$this->EE->session->cache['forms']['ee_entry_row'] = $this->row;

		$params['entry_id'] = $this->row['entry_id'];
		$params['field_id'] = $this->field_id;
		return $F->form($params, $tagdata);
	}

	// ********************************************************************************* //

	function save($data)
	{
		$this->EE->session->cache['Forms']['FieldData'][$this->field_id] = $data;

		if (isset($data['fields']) == FALSE)
		{
			return '';
		}
		else
		{
			return 'Forms';
		}
	}

	// ********************************************************************************* //

	function post_save($data)
	{
		$data = (isset($this->EE->session->cache['Forms'])) ? $this->EE->session->cache['Forms']['FieldData'][$this->field_id] : FALSE;
		$entry_id = $this->settings['entry_id'];
		$channel_id = $this->EE->input->post('channel_id');
		$field_id = $this->field_id;
		$form_id = 0;

		// -----------------------------------------
		// Grab the form!
		// -----------------------------------------
		$this->EE->db->select('*');
		$this->EE->db->from('exp_forms');
		$this->EE->db->where('entry_id', $entry_id);
		$this->EE->db->where('ee_field_id', $this->field_id);
		$query = $this->EE->db->get();

		// -----------------------------------------
		// Empty Form?
		// -----------------------------------------
		if (isset($data['fields']) == FALSE)
		{
			if ($query->num_rows() > 0) $this->EE->forms_model->delete_form($query->row('form_id'));
			return;
		}

		// -----------------------------------------
		// Does it exist?
		// -----------------------------------------
		if ($query->num_rows() == 0)
		{
			// -----------------------------------------
			// Lets create it then!
			// -----------------------------------------
			$fdata = array();
			$fdata['site_id'] = $this->site_id;
			$fdata['entry_id'] = $entry_id;
			$fdata['channel_id'] = $channel_id;
			$fdata['ee_field_id'] = $this->field_id;
			$fdata['member_id'] = $this->EE->session->userdata['member_id'];
			$fdata['form_title'] = $this->EE->input->get_post('title');
			$fdata['form_url_title'] = $this->EE->input->get_post('url_title');
			$fdata['date_created'] = $this->EE->localize->now;
			$fdata['form_type'] = 'entry';
			$fdata['form_settings'] = serialize($data['settings']);
			$form_id = $this->EE->forms_model->create_update_form($fdata);
		}
		else
		{
			$form_id = $query->row('form_id');

			// Update it!
			$fdata = array();
			$fdata['form_settings'] = serialize($data['settings']);
			$this->EE->forms_model->create_update_form($fdata, $form_id);
		}

		// -----------------------------------------
		// Grab all fields
		// -----------------------------------------
		$dbfields = array();
		$this->EE->db->select('*');
		$this->EE->db->from('exp_forms_fields');
		$this->EE->db->where('form_id', $form_id);
		$query = $this->EE->db->get();

		foreach($query->result() as $row)
		{
			$dbfields[$row->field_id] = $row;
		}

		// -----------------------------------------
		// Loop over all fields!
		// -----------------------------------------

		if (isset($data['fields']) == FALSE) $data['fields'] = array();

		$hashmapper = array();

		foreach($data['fields'] as $order => $postfield)
		{
			$field = unserialize(base64_decode($postfield['field']));

			// We need a label!
			if (isset($field['title']) == FALSE OR trim($field['title']) == FALSE) continue;

			// Check if it's empty
			if (isset($field['settings']) == FALSE) $field['settings'] = array();

			if (isset($field['field_type']) === FALSE) continue;
			if ($field['field_type'] == 'pagebreak' && isset($data['fields'][$order+1]['field']) == FALSE) continue;


			// Conditionals
			$conditionals = array();
			if (isset($field['conditionals']['options']) === TRUE)
			{
				$conditionals['options'] = $field['conditionals']['options'];
				foreach ($field['conditionals']['conditionals'] as $cond)
				{
					$cond['value'] = trim($cond['value']);
					if ($cond['value'] === '') continue;
					$conditionals['conditionals'][] = $cond;
				}
			}

			$fdata = array();
			$fdata['entry_id'] = $entry_id;
			$fdata['ee_field_id'] = $this->field_id;
			$fdata['parent_id'] = 0;
			$fdata['column_number'] = 0;
			$fdata['form_id'] = $form_id;
			$fdata['title'] = $field['title'];
			$fdata['url_title'] = $field['url_title'];
			$fdata['description'] = $field['description'];
			$fdata['field_type'] = $field['field_type'];
			$fdata['field_order'] = $order;
			$fdata['conditionals'] = serialize($conditionals);
			$fdata['required'] = (isset($field['required']) === TRUE) ? $field['required'] : 0;
			$fdata['show_label'] = (isset($field['show_label']) === TRUE) ? $field['show_label'] : 1;
			$fdata['label_position'] = (isset($field['label_position']) === TRUE) ? $field['label_position'] : 'auto';
			$fdata['no_dupes'] = (isset($field['no_dupes']) === TRUE && $field['no_dupes'] == 'yes') ? 1 : 0;
			$fdata['field_settings'] = $field['settings'];
			$fdata['field_id'] = (isset($field['field_id']) === TRUE && $field['field_id'] > 0) ? $field['field_id'] : 0;
			$field_id = $this->EE->forms_model->create_update_field($fdata);

			$hashmapper[ $field['field_hash'] ] = $field_id;

			if (isset($dbfields[$field_id]) === TRUE) unset($dbfields[$field_id]);

			if (isset($postfield['columns']) === TRUE)
			{
				foreach ($postfield['columns'] as $col_number => $colfields)
				{
					foreach ($colfields as $colorder => $colfield)
					{
						$subfield = unserialize(base64_decode($colfield));

						// We need a label!
						if (isset($subfield['title']) == FALSE OR trim($subfield['title']) == FALSE) continue;

						// Check if it's empty
						if (isset($subfield['settings']) == FALSE) $subfield['settings'] = array();

						if (isset($subfield['field_type']) === FALSE) continue;

						// Conditionals
						$conditionals = array();
						if (isset($subfield['conditionals']['options']) === TRUE)
						{
							$conditionals['options'] = $subfield['conditionals']['options'];
							foreach ($subfield['conditionals']['conditionals'] as $cond)
							{
								if ($cond['value'] == FALSE) continue;
								$conditionals['conditionals'][] = $cond;
							}
						}

						$fdata = array();
						$fdata['entry_id'] = $entry_id;
						$fdata['ee_field_id'] = $this->field_id;
						$fdata['parent_id'] = $field_id;
						$fdata['column_number'] = $col_number;
						$fdata['form_id'] = $form_id;
						$fdata['title'] = $subfield['title'];
						$fdata['url_title'] = $subfield['url_title'];
						$fdata['description'] = $subfield['description'];
						$fdata['field_type'] = $subfield['field_type'];
						$fdata['field_order'] = ($colorder+1);
						$fdata['conditionals'] = serialize($conditionals);
						$fdata['required'] = (isset($subfield['required']) === TRUE) ? $subfield['required'] : 0;
						$fdata['show_label'] = (isset($subfield['show_label']) === TRUE) ? $subfield['show_label'] : 1;
						$fdata['label_position'] = (isset($subfield['label_position']) === TRUE) ? $subfield['label_position'] : 'auto';
						$fdata['no_dupes'] = (isset($subfield['no_dupes']) === TRUE && $subfield['no_dupes'] == 'yes') ? 1 : 0;
						$fdata['field_settings'] = $subfield['settings'];
						$fdata['field_id'] = (isset($subfield['field_id']) === TRUE && $subfield['field_id'] > 0) ? $subfield['field_id'] : 0;
						$subfield_id = $this->EE->forms_model->create_update_field($fdata);

						$hashmapper[ $subfield['field_hash'] ] = $subfield_id;

						unset($subfield, $fdata);
						if (isset($dbfields[$subfield_id]) === TRUE) unset($dbfields[$subfield_id]);
					}
				}
			}
		}

		// -----------------------------------------
		// Process Hashmaps!
		// -----------------------------------------
		foreach ($hashmapper as $hash => $fid_id)
		{
			// Get the field again.. :(
			$query = $this->EE->db->select('field_id, conditionals')->from('exp_forms_fields')->where('field_id', $fid_id)->get();
			$cond = @unserialize($query->row('conditionals'));
			if (is_array($cond) == FALSE) continue;
			if (isset($cond['conditionals']) == FALSE) continue;

			foreach ($cond['conditionals'] as $num => $c)
			{
				if (isset($hashmapper[ $c['field'] ]) == FALSE) unset($cond['conditionals'][$num]);
				else $cond['conditionals'][$num]['field'] = $hashmapper[ $c['field'] ];
			}

			$this->EE->db->set('conditionals', serialize($cond));
			$this->EE->db->where('field_id', $fid_id);
			$this->EE->db->update('exp_forms_fields');
		}

		// -----------------------------------------
		// Process Templates
		// -----------------------------------------
		foreach($data['templates'] as $type => $template)
		{
			if ($template['which'] == 'predefined')
			{
				$this->EE->forms_model->create_update_form(array($type.'_template' => $template['predefined']), $form_id);
			}
			elseif ($template['which'] == 'custom')
			{
				$fdata = array();
				$fdata['form_id'] = $form_id;
				$fdata['template_label'] = $this->EE->input->get_post('title');
				$fdata['template_name'] = $this->EE->input->get_post('url_title');
				$fdata['template_type'] = $type;
				$fdata['email_type'] 	= $template['custom']['email_type'];
				$fdata['email_wordwrap'] = $template['custom']['email_wordwrap'];
				if (isset($template['custom']['email_to'])) $fdata['email_to'] = $template['custom']['email_to'];
				$fdata['email_from'] 	= $template['custom']['email_from'];
				$fdata['email_from_email'] = $template['custom']['email_from_email'];
				$fdata['email_reply_to'] = $template['custom']['email_reply_to'];
				$fdata['email_reply_to_email'] = $template['custom']['email_reply_to_email'];
				if (isset($template['custom']['reply_to_author'])) $fdata['reply_to_author'] = $template['custom']['reply_to_author'];
				$fdata['email_subject']	= $template['custom']['email_subject'];
				$fdata['email_cc']		= $template['custom']['email_cc'];
				$fdata['email_bcc']		= $template['custom']['email_bcc'];
				$fdata['email_attachments'] = $template['custom']['email_attachments'];
				$fdata['template']		= $template['custom']['template'];
				$this->EE->forms_model->create_update_template($fdata);

				// Update the form too
				$this->EE->forms_model->create_update_form(array($type.'_template' => -1), $form_id);
			}
			else
			{
				$this->EE->forms_model->create_update_form(array($type.'_template' => 0), $form_id);
			}
		}

		// -----------------------------------------
		// Delete all old ones!
		// -----------------------------------------
		if (empty($dbfields) == FALSE)
		{
			$this->EE->forms_model->delete_fields($dbfields);
		}

		return;
	}

	// ********************************************************************************* //

	function delete($ids)
	{



	}

	// ********************************************************************************* //

	/**
	 * Display the settings page. The default ExpressionEngine rows can be created using built in methods.
	 * All of these take the current $data and the fieltype name as parameters:
	 *
	 * @param $data array
	 * @access public
	 * @return void
	 */
	public function display_settings($data)
	{

		$vData = array();

		//----------------------------------------
		// Add JS & CSS
		//----------------------------------------
		$this->EE->forms_helper->mcp_meta_parser('gjs', '', 'Forms');
		$this->EE->forms_helper->mcp_meta_parser('css', FORMS_THEME_URL . 'forms_fts.css', 'forms-fts');
		$this->EE->forms_helper->mcp_meta_parser('css', FORMS_THEME_URL . 'forms_builder.css', 'forms-builder');
		$this->EE->forms_helper->mcp_meta_parser('css', FORMS_THEME_URL . 'bootstrap.popovers.css', 'bootstrap.popovers');
		$this->EE->forms_helper->mcp_meta_parser('js', FORMS_THEME_URL . 'bootstrap.popovers.js', 'bootstrap.popovers', 'bootstrap');
		$this->EE->forms_helper->mcp_meta_parser('js', FORMS_THEME_URL . 'forms_fts.js', 'cfo-fts');
		$this->EE->cp->add_js_script(array('ui' => array('tabs', 'widget')));
		$this->EE->javascript->output('Forms.Init();');

		//----------------------------------------
		// Default Settings
		//----------------------------------------
		$settings = array();
		$settings['fields'] = $this->EE->config->item('cf_formfields');

		//----------------------------------------
		// Merge settings!
		//----------------------------------------
		if (isset($data['forms']['fields']) == TRUE)
		{
			$settings['fields'] = $data['forms']['fields'];
		}

		//----------------------------------------
		// Load all Custom Fields
		//----------------------------------------
		foreach ($this->EE->formsfields as $classname => $field)
		{
			$fd = array();
			$fd['name'] = $classname;
			$fd['label'] = $field->info['title'];

			// Any Settings
			$fdsettings = (isset($data['forms']['field_settings'][$classname]) == TRUE && $data['forms']['field_settings'][$classname] != FALSE) ? $data['forms']['field_settings'][$classname] : array();

			// Parse Settings
			$tempfield = array();
			$tempfield['settings'] = $fdsettings;
			$tempfield['form_name_settings'] = "forms[field_settings][{$classname}]";
			$fd['settings'] = $field->display_settings($tempfield, FALSE);

			// Check before! If the whole section is not checked, the array doesn't exist
			if (isset($settings['fields'][ $field->info['category'] ]) == TRUE)
			{
				// Is it checked?
				$fd['checked'] = in_array($classname, $settings['fields'][ $field->info['category'] ]);
			}
			else
			{
				$fd['checked'] = FALSE;
			}


			$vData['fields'][ $field->info['category'] ][] = $fd;
		}

		//----------------------------------------
		// Reorder Categories, just in case
		//----------------------------------------
		$new_fields = $this->EE->config->item('cf_formfields_cats');
		foreach ($vData['fields'] as $category => $fields)
		{
			$new_fields[$category] = $fields;
		}

		// Put it back
		$vData['fields'] = $new_fields;




		$vData['settings'] = $settings;

		// -----------------------------------------
		// Add Help!
		// -----------------------------------------
		if (isset($this->EE->session->cache['Forms']['JSON_help']) == FALSE)
		{
			$vData['helpjson'] = array();

			foreach ($this->EE->lang->language as $key => $val)
			{
				if (strpos($key, 'form:help:') === 0)
				{
					$vData['helpjson'][substr($key, 10)] = $val;
					unset($this->EE->lang->language[$key]);
				}

			}

			$vData['helpjson'] = $this->EE->forms_helper->generate_json($vData['helpjson']);
		}



		//----------------------------------------
		// Display Row
		//----------------------------------------
		$row = $this->EE->load->view('fts/settings', $vData, TRUE);
		$this->EE->table->add_row(array('data' => $row, 'colspan' => 2));
	}

	// ********************************************************************************* //

	/**
	 * Save the fieldtype settings.
	 *
	 * @param $data array Contains the submitted settings for this field.
	 * @access public
	 * @return array
	 */
	public function save_settings($data)
	{
		$settings = array('forms' => array());

		if (isset($_POST['forms']) == FALSE) return $settings;

		$P = $_POST['forms'];
		$S = array();

		// Fields
		$S['fields'] = (isset($P['fields']) == TRUE) ? $P['fields'] : array();
		$S['field_settings']  = (isset($P['field_settings']) == TRUE) ? $P['field_settings'] : array();

		// -----------------------------------------
		// Loop over all field
		// -----------------------------------------
		foreach ($S['fields'] as $cffields)
		{
			foreach ($cffields as $field_name)
			{
				if (isset($this->EE->formsfields[$field_name]) != TRUE)
				{
					unset($S['fields'][$field_name]);
				}

				$field_settings = (isset($S['field_settings'][$field_name]) == FALSE) ? array() :  $S['field_settings'][$field_name];
				$field_settings = $this->EE->formsfields[$field_name]->save_settings($field_settings);
				$S['field_settings'][$field_name] = $field_settings;
			}
		}

		$settings['forms'] = $S;
		return $settings;
	}

	// ********************************************************************************* //

}

/* End of file ft.forms.php */
/* Location: ./system/expressionengine/third_party/forms/ft.forms.php */
