<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Channel Forms Module Control Panel Class
 *
 * @package			DevDemon_Forms
 * @author			DevDemon <http://www.devdemon.com> - Lead Developer @ Parscale Media
 * @copyright 		Copyright (c) 2007-2010 Parscale Media <http://www.parscale.com>
 * @license 		http://www.devdemon.com/license/
 * @link			http://www.devdemon.com/forms/
 * @see				http://expressionengine.com/user_guide/development/module_tutorial.html#control_panel_file
 */
class Forms_mcp
{
	/**
	 * Views Data
	 * @var array
	 * @access private
	 */
	private $vData = array();

	/**
	 * Constructor
	 *
	 * @access public
	 * @return void
	 */
	public function __construct()
	{
		// Creat EE Instance
		$this->EE =& get_instance();

		// Load Models & Libraries & Helpers
		$this->EE->load->library('forms_helper');
		$this->EE->load->model('forms_model');
		$this->site_id = $this->EE->config->item('site_id');

		// Some Globals
		$this->base = BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=forms';
		$this->base_short = 'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=forms';

		// Global Views Data
		$this->vData['base_url'] = $this->base;
		$this->vData['base_url_short'] = $this->base_short;
		$this->vData['method'] = $this->EE->input->get('method');

		$this->EE->forms_helper->define_theme_url();

		$this->mcp_globals();

		// Add Right Top Menu
		$this->EE->cp->set_right_nav(array(
			'form:docs' 				=> $this->EE->cp->masked_url('http://www.devdemon.com/forms/docs/'),
		));

		if (function_exists('ee')) {
			ee()->view->cp_page_title = $this->EE->lang->line('form:mcp');
		} else {
			$this->EE->cp->set_variable('cp_page_title', $this->EE->lang->line('form:mcp'));
		}

		$this->EE->config->load('forms_config');



		// Debug
		//$this->EE->db->save_queries = TRUE;
		//$this->EE->output->enable_profiler(TRUE);
	}

	// ********************************************************************************* //

	public function index()
	{
		return $this->forms();
		/*
		// Page Title & BreadCumbs
		$this->vData['PageHeader'] = 'home';

		return $this->EE->load->view('mcp/index', $this->vData, TRUE);*/
	}

	// ********************************************************************************* //

	public function forms()
	{
		// Page Title & BreadCumbs
		$this->vData['PageHeader'] = 'forms';

		// Get all Stand Alone Forms
		$this->EE->db->select('f.*, mb.screen_name');
		$this->EE->db->from('exp_forms f');
		$this->EE->db->join('exp_members mb', 'mb.member_id = f.member_id', 'left');
		$this->EE->db->order_by('f.form_title', 'ASC');
		$this->EE->db->where('f.site_id', $this->site_id);
		$this->EE->db->where('f.entry_id', 0);
		$query = $this->EE->db->get();

		$this->vData['forms'] = $query->result();

		// Get all Stand Alone Forms
		$this->EE->db->select('f.*, mb.screen_name');
		$this->EE->db->from('exp_forms f');
		$this->EE->db->join('exp_channel_titles ct', 'ct.entry_id = f.entry_id', 'left');
		$this->EE->db->join('exp_members mb', 'mb.member_id = f.member_id', 'left');
		$this->EE->db->where('f.site_id', $this->site_id);
		$this->EE->db->where('f.entry_id >', 0);
		$this->EE->db->where('(ct.expiration_date = 0 OR ct.expiration_date > '.$this->EE->localize->now.')', null, false);
		$this->EE->db->order_by('f.form_title', 'ASC');
		$query = $this->EE->db->get();

		foreach ($query->result() as $row)
		{
			$this->vData['forms'][] = $row;
		}

		return $this->EE->load->view('mcp/forms', $this->vData, TRUE);
	}

	// ********************************************************************************* //

	public function view_form()
	{
		// Page Title & BreadCumbs
		$this->vData['PageHeader'] = 'forms';

		//----------------------------------------
		// Grab Form
		//----------------------------------------
		$this->EE->db->select('f.*, mb.screen_name');
		$this->EE->db->from('exp_forms f');
		$this->EE->db->join('exp_members mb', 'mb.member_id = f.member_id', 'left');
		$this->EE->db->where('f.form_id', $this->EE->input->get_post('form_id'));
		$query = $this->EE->db->get();

		if ($query->num_rows() != 1)
		{
			return show_error('Missing Form Info..');
		}

		$this->vData['form'] = $query->row();

		//----------------------------------------
		// Standard Fields
		//----------------------------------------
		$this->vData['standard_fields'] = array();
		$this->vData['standard_fields']['member'] = $this->EE->lang->line('form:member');
		$this->vData['standard_fields']['date'] = $this->EE->lang->line('form:date');
		$this->vData['standard_fields']['country'] = $this->EE->lang->line('form:country');
		$this->vData['standard_fields']['ip'] = $this->EE->lang->line('form:ip');
		$this->vData['dbfields'] = array();

		// -----------------------------------------
		// Grab all DB fields
		// -----------------------------------------
		$this->EE->db->select('*');
		$this->EE->db->from('exp_forms_fields');
		$this->EE->db->where('form_id', $this->EE->input->get_post('form_id'));
		$this->EE->db->where_not_in('field_type', array('pagebreak', 'fieldset', 'columns_2', 'columns_3', 'columns_4', 'html') );
		$this->EE->db->order_by('field_order');
		$query = $this->EE->db->get();

		foreach($query->result() as $row)
		{
			$row->field_settings = @unserialize($row->field_settings);
			$this->vData['dbfields'][] = $row;
		}

		//----------------------------------------
		// Grab all members
		//----------------------------------------
		$this->vData['members'] = array();
		$this->EE->db->select('mb.screen_name, fe.member_id');
		$this->EE->db->from('exp_forms_entries fe');
		$this->EE->db->join('exp_members mb', 'mb.member_id = fe.member_id', 'left');
		$this->EE->db->group_by('fe.member_id');
		$this->EE->db->order_by('mb.screen_name');
		$query = $this->EE->db->get();

		foreach ($query->result() as $row)
		{
			if ($row->member_id == 0) $row->screen_name = $this->EE->lang->line('form:guest');
			$this->vData['members'][$row->member_id] = $row->screen_name;
		}

		//----------------------------------------
		// Add JS
		//----------------------------------------
		$js = '<script type="text/javascript">';
		$js .= "
		Forms.DatatablesCols['form_entries'] = [];
		Forms.DatatablesCols['form_entries'].push({mDataProp:'fentry_id', sName:'fentry_id', bSortable: false, sWidth:'50px'});
		";

		foreach ($this->vData['standard_fields'] as $field_name => $field_title){
			$js .= "Forms.DatatablesCols['form_entries'].push({mDataProp:'{$field_name}', sName:'{$field_name}'});";
		}

		foreach ($this->vData['dbfields'] as $field){
			$js .= "Forms.DatatablesCols['form_entries'].push({mDataProp:'field_id_{$field->field_id}', sName:'field_id_{$field->field_id}', bVisible:false});";
		}

		$js .= '</script>';

		$this->EE->cp->add_to_foot($js);

		$this->vData['js_added'] = true;

		return $this->EE->load->view('mcp/view_form', $this->vData, TRUE);
	}

	// ********************************************************************************* //

	public function create_form()
	{
		$vData = $this->vData;

		// For the Builder
		$this->EE->forms_helper->mcp_meta_parser('css', FORMS_THEME_URL . 'forms_builder.css', 'cfo-builder');
		$this->EE->forms_helper->mcp_meta_parser('css', FORMS_THEME_URL . 'forms_base.css', 'cfo-base');
		$this->EE->forms_helper->mcp_meta_parser('js', FORMS_THEME_URL . 'jquery.stringtoslug.min.js', 'jquery.stringtoslug', 'jquery');
		//$this->EE->forms_helper->mcp_meta_parser('js', FORMS_THEME_URL . 'hogan.js', 'hogan', 'hogan');
		$this->EE->forms_helper->mcp_meta_parser('js', FORMS_THEME_URL . 'forms_builder.js', 'cfo-builder');
		$this->EE->cp->add_js_script(array('ui' => array('sortable', 'draggable')));

		// Page Title & BreadCumbs
		$vData['PageHeader'] = 'forms';
		$vData['field_id'] = 0;
		$vData['form'] = array();
		$vData['form']['admin_template'] = 0;
		$vData['form']['user_template'] = 0;
		$vData['dbfieldjson'] = '{}';
		$vData['field_name'] = 'form';

		$form_id = $this->EE->input->get('form_id') ? $this->EE->input->get('form_id') : 0;

		// -----------------------------------------
		// Add Blank
		// -----------------------------------------
		$fields = $this->EE->db->list_fields('exp_forms');

		foreach ($fields as $name)
		{
			$vData['form'][$name] = '';
		}

		// -----------------------------------------
		// Add Fields!
		// -----------------------------------------
		foreach ($this->EE->formsfields as $classname => $field)
		{
			$vData['form']['fields'][$field->info['category']][] = $classname;
		}

		//----------------------------------------
		// Reorder Categories, just in case
		//----------------------------------------
		$new_fields = $this->EE->config->item('cf_formfields_cats');
		foreach ($vData['form']['fields'] as $category => $fields)
		{
			$new_fields[$category] = $fields;
		}

		// Put it back
		$vData['form']['fields'] = $new_fields;

		// -----------------------------------------
		// Load Settings
		// -----------------------------------------
		$vData['form']['settings'] = $this->EE->config->item('cf_formsettings'); // Default form settings

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
		if ($form_id > 0)
		{
			// -----------------------------------------
			// Grab the form!
			// -----------------------------------------
			$this->EE->db->select('*');
			$this->EE->db->from('exp_forms');
			$this->EE->db->where('form_id', $form_id);
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

		unset($mgroups);

		return $this->EE->load->view('mcp/forms_create', $vData, TRUE);
	}

	// ********************************************************************************* //

	public function update_form()
	{
		$data = (isset($_POST['form']) == TRUE) ? $_POST['form'] : array();
		$form_id = $this->EE->input->post('form_id') ? $this->EE->input->post('form_id') : 0;

		// -----------------------------------------
		// Grab the form!
		// -----------------------------------------
		$this->EE->db->select('*');
		$this->EE->db->from('exp_forms');
		$this->EE->db->where('form_id', $form_id);
		$query = $this->EE->db->get();

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
			$fdata['member_id'] = $this->EE->session->userdata['member_id'];
			$fdata['form_title'] = $data['settings']['form_title'];
			$fdata['form_url_title'] = $data['settings']['form_url_title'];
			$fdata['date_created'] = $this->EE->localize->now;
			$fdata['form_settings'] = serialize($data['settings']);
			$fdata['form_type'] = 'normal';
			$form_id = $this->EE->forms_model->create_update_form($fdata);
		}
		else
		{
			$form_id = $query->row('form_id');

			// Update it!
			$fdata = array();
			$fdata['form_settings'] = serialize($data['settings']);
			$fdata['form_title'] = $data['settings']['form_title'];
			$fdata['form_url_title'] = $data['settings']['form_url_title'];
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

		$field_order = 0;

		foreach($data['fields'] as $key => $postfield)
		{
			$field = unserialize(base64_decode($postfield['field']));

			// We need a label!
			if (isset($field['title']) == FALSE OR trim($field['title']) == FALSE) continue;

			// Check if it's empty
			if (isset($field['settings']) == FALSE) $field['settings'] = array();

			if (isset($field['field_type']) === FALSE) continue;

			/*
			if ($field['field_type'] == 'pagebreak' && isset($data['fields'][$field_order+1]['field']) == FALSE)
			{
				continue;
			}
			*/

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
			$fdata['parent_id'] = 0;
			$fdata['column_number'] = 0;
			$fdata['form_id'] = $form_id;
			$fdata['title'] = $field['title'];
			$fdata['url_title'] = $field['url_title'];
			$fdata['description'] = $field['description'];
			$fdata['field_type'] = $field['field_type'];
			$fdata['field_order'] = $field_order;
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

			// This needs to be at the bottom because of the pagebreak conditional
			$field_order++;

			if (isset($postfield['columns']) === TRUE)
			{
				foreach ($postfield['columns'] as $col_number => $colfields)
				{
					foreach ($colfields as $colorder => $colfield)
					{
						$field_order++;
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
						$fdata['parent_id'] = $field_id;
						$fdata['column_number'] = $col_number;
						$fdata['form_id'] = $form_id;
						$fdata['title'] = $subfield['title'];
						$fdata['url_title'] = $subfield['url_title'];
						$fdata['description'] = $subfield['description'];
						$fdata['field_type'] = $subfield['field_type'];
						$fdata['field_order'] = $field_order;
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

		$this->EE->functions->redirect($this->base . '&method=forms');
	}

	// ********************************************************************************* //

	public function delete_form()
	{
		$this->EE->forms_model->delete_form( $this->EE->input->get('form_id') );
		$this->EE->functions->redirect($this->base . '&method=forms');
	}

	// ********************************************************************************* //

	public function duplicate_form()
	{
		$old_form_id = $this->EE->input->get('form_id');

		// Get the Form
		$query = $this->EE->db->select('*')->from('exp_forms')->where('form_id', $this->EE->input->get('form_id') )->get();

		// Create the Form
		$fdata = $query->row_array();
		$fdata['date_created'] = $this->EE->localize->now;
		$fdata['form_title'] .= ' DUPLICATE';
		$fdata['form_url_title'] .= '_dupe';
		unset($fdata['form_id']);
		unset($fdata['entry_id']);
		$new_form_id = $this->EE->forms_model->create_update_form($fdata);

		// Grab all fields
		$field_maps = array();

		$query = $this->EE->db->select('*')->from('exp_forms_fields')->where('form_id', $old_form_id)->get();
		foreach ($query->result_array() as $row)
		{
			$fdata = $row;
			$fdata['form_id'] = $new_form_id;
			$fdata['field_settings'] = unserialize($fdata['field_settings']);

			$old_field_id = $fdata['field_id'];
			unset($fdata['field_id']);

			$new_field_id = $this->EE->forms_model->create_update_field($fdata);
			$field_maps[$old_field_id] = $new_field_id;
		}

		foreach ($field_maps as $old_field => $new_field)
		{
			$this->EE->db->set('parent_id', $new_field);
			$this->EE->db->where('parent_id', $old_field);
			$this->EE->db->where('form_id', $new_form_id);
			$this->EE->db->update('exp_forms_fields');
		}

		$this->EE->functions->redirect($this->base . '&method=forms');
	}

	// ********************************************************************************* //

	public function entries()
	{
		// Page Title & BreadCumbs
		$this->vData['PageHeader'] = 'entries';

		// Get all forms
		$e = $this->EE->lang->line('form:entry_linked');
		$s = $this->EE->lang->line('form:salone');

		$this->vData['forms'] = array();

		$query = $this->EE->db->select('form_title, form_id')->from('exp_forms')->where('entry_id >', 0)->where('site_id', $this->site_id)->order_by('form_title')->get();
		foreach ($query->result() as $row)
		{
			$this->vData['forms'][$e][$row->form_id] = $row->form_title;
		}

		$query = $this->EE->db->select('form_title, form_id')->from('exp_forms')->where('entry_id', 0)->where('site_id', $this->site_id)->order_by('form_title')->get();
		foreach ($query->result() as $row)
		{
			$this->vData['forms'][$s][$row->form_id] = $row->form_title;
		}

		//----------------------------------------
		// Standard Fields
		//----------------------------------------
		$this->vData['standard_fields'] = array();
		$this->vData['standard_fields']['member'] = $this->EE->lang->line('form:member');
		$this->vData['standard_fields']['date'] = $this->EE->lang->line('form:date');
		$this->vData['standard_fields']['country'] = $this->EE->lang->line('form:country');
		$this->vData['standard_fields']['ip'] = $this->EE->lang->line('form:ip');
		$this->vData['standard_fields']['form'] = $this->EE->lang->line('form');

		//----------------------------------------
		// Grab all members
		//----------------------------------------
		$this->vData['members'] = array('0' => $this->EE->lang->line('form:guest'));
		$this->EE->db->select('mb.screen_name, fe.member_id');
		$this->EE->db->from('exp_forms_entries fe');
		$this->EE->db->join('exp_members mb', 'mb.member_id = fe.member_id', 'left');
		$this->EE->db->group_by('fe.member_id');
		$this->EE->db->order_by('mb.screen_name');
		$query = $this->EE->db->get();

		foreach ($query->result() as $row)
		{
			if ($row->member_id == 0) continue;
			$this->vData['members'][$row->member_id] = $row->screen_name;
		}

		//----------------------------------------
		// Add JS
		//----------------------------------------
		$js = '<script type="text/javascript">';
		$js .= "
		Forms.DatatablesCols['submissions'] = [];
		Forms.DatatablesCols['submissions'].push({mDataProp:'fentry_id', sName:'fentry_id', bSortable: false, sWidth:'50px'});
		";

		foreach ($this->vData['standard_fields'] as $field_name => $field_title){
			$js .= "Forms.DatatablesCols['submissions'].push({mDataProp:'{$field_name}', sName:'{$field_name}'});";
		}

		$js .= '</script>';

		$this->EE->cp->add_to_foot($js);

		return $this->EE->load->view('mcp/entries', $this->vData, TRUE);
	}

	// ********************************************************************************* //

	public function templates()
	{
		// Page Title & BreadCumbs
		$this->vData['PageHeader'] = 'templates';

		$this->EE->db->select('f.*');
		$this->EE->db->from('exp_forms_email_templates f');
		$this->EE->db->where('f.site_id', $this->site_id);
		$this->EE->db->order_by('f.template_label', 'ASC');
		$query = $this->EE->db->get();

		$this->vData['templates'] = $query->result();

		return $this->EE->load->view('mcp/templates', $this->vData, TRUE);
	}

	// ********************************************************************************* //

	public function create_template()
	{
		// Page Title & BreadCumbs
		$this->vData['PageHeader'] = 'templates';

		$fields = $this->EE->db->list_fields('exp_forms_email_templates');

		foreach ($fields as $name) $this->vData[$name] = '';

		// Edit?
		if ($this->EE->input->get('template_id') != FALSE)
		{
			$query = $this->EE->db->select('*')->from('exp_forms_email_templates')->where('template_id', $this->EE->input->get('template_id'))->get();
			$this->vData = array_merge($this->vData, $query->row_array());
		}

		return $this->EE->load->view('mcp/templates_create', $this->vData, TRUE);
	}

	// ********************************************************************************* //

	public function update_template()
	{
		//----------------------------------------
		// Create/Updating?
		//----------------------------------------
		if ($this->EE->input->get('delete') != 'yes')
		{
			$data = array();
			$data['template_label'] = $this->EE->input->post('template_label');
			$data['template_name'] = $this->EE->input->post('template_name');
			$data['template_type'] = $this->EE->input->post('template_type');
			$data['template_desc'] = $this->EE->input->post('template_desc');
			$data['email_type'] 	= $this->EE->input->post('email_type');
			$data['email_wordwrap'] = $this->EE->input->post('email_wordwrap');
			$data['email_to'] 		= $this->EE->input->post('email_to');
			$data['email_from'] 	= $this->EE->input->post('email_from');
			$data['email_from_email'] = $this->EE->input->post('email_from_email');
			$data['email_reply_to'] = $this->EE->input->post('email_reply_to');
			$data['email_reply_to_email'] = $this->EE->input->post('email_reply_to_email');
			$data['reply_to_author'] = $this->EE->input->post('reply_to_author');
			$data['email_subject']	= $this->EE->input->post('email_subject');
			$data['email_cc']		= $this->EE->input->post('email_cc');
			$data['email_bcc']		= $this->EE->input->post('email_bcc');
			$data['email_attachments'] = $this->EE->input->post('email_attachments');
			$data['template']		= $this->EE->input->post('template');

			$this->EE->forms_model->create_update_template($data, $this->EE->input->post('template_id'));

		}

		//----------------------------------------
		// Delete
		//----------------------------------------
		else
		{
			$this->EE->forms_model->delete_template($this->EE->input->get('template_id'));
		}

		$this->EE->functions->redirect($this->base . '&method=templates');
	}

	// ********************************************************************************* //

	public function lists()
	{
		// Page Title & BreadCumbs
		$this->vData['PageHeader'] = 'lists';

		$query = $this->EE->db->select('*')->from('exp_forms_lists')->order_by('list_label', 'ASC')->get();

		$this->vData['lists'] = $query->result();


		return $this->EE->load->view('mcp/lists', $this->vData, TRUE);
	}

	// ********************************************************************************* //

	public function create_list()
	{
		// Page Title & BreadCumbs
		$this->vData['PageHeader'] = 'lists';
		$this->vData['items'] = '';

		$fields = $this->EE->db->list_fields('exp_forms_lists');

		foreach ($fields as $name) $this->vData[$name] = '';

		// Edit?
		if ($this->EE->input->get('list_id') != FALSE)
		{
			$query = $this->EE->db->select('*')->from('exp_forms_lists')->where('list_id', $this->EE->input->get('list_id'))->get();
			$this->vData = array_merge($this->vData, $query->row_array());

			$this->vData['list_data'] = unserialize($this->vData['list_data']);

			foreach ($this->vData['list_data'] as $key => $val)
			{
				$this->vData['items'] .= ($key == $val) ? "{$val}\n": "{$key} : {$val}\n";
			}

			$this->vData['items'] = trim($this->vData['items']);
		}


		return $this->EE->load->view('mcp/lists_create', $this->vData, TRUE);
	}

	// ********************************************************************************* //

	public function update_list()
	{
		//----------------------------------------
		// Create/Updating?
		//----------------------------------------
		if ($this->EE->input->get('delete') != 'yes')
		{
			$data = array();
			$data['list_label'] = $this->EE->input->post('list_label');

			//----------------------------------------
			// Format Items
			//----------------------------------------
			if ($this->EE->input->post('items') == FALSE) show_error('Missing Items!');

			$data['list_data'] = array();
			$items = explode("\n", $this->EE->input->post('items'));

			foreach ($items as $line)
			{
				if (strpos($line, ' : ') !== FALSE)
				{
					$line = explode(' : ', $line);
					$data['list_data'][$line[0]] = $line[1];
				}
				else
				{
					$data['list_data'][$line] = $line;
				}
			}

			$data['list_data'] = serialize($data['list_data']);


			$this->EE->forms_model->create_update_list($data, $this->EE->input->post('list_id'));

		}

		//----------------------------------------
		// Delete
		//----------------------------------------
		else
		{
			$this->EE->forms_model->delete_list($this->EE->input->get('list_id'));
		}

		$this->EE->functions->redirect($this->base . '&method=lists');
	}

	// ********************************************************************************* //
	public function settings()
	{
		// Page Title & BreadCumbs
		$this->vData['PageHeader'] = 'settings';
		$conf = $this->EE->config->item('forms_defaults');

		// Grab Settings
		$query = $this->EE->db->query("SELECT settings FROM exp_modules WHERE module_name = 'Forms'");
		if ($query->row('settings') != FALSE)
		{
			$settings = @unserialize($query->row('settings'));
			if ($settings != FALSE && isset($settings['site:'.$this->site_id]))
			{
				$conf = $this->EE->forms_helper->array_extend($conf, $settings['site:'.$this->site_id]);
			}
		}

		$this->vData['config'] = $conf;


		// PDF Printing
		require_once(PATH_THIRD."forms/libraries/dompdf/dompdf_config.inc.php");
		$this->vData['pdf_paper_sizes'] = array();
		foreach ( array_keys(CPDF_Adapter::$PAPER_SIZES) as $size ) $this->vData['pdf_paper_sizes'][$size] = $size;

		return $this->EE->load->view('mcp/settings', $this->vData, TRUE);
	}

	// ********************************************************************************* //

	public function update_settings()
	{
		// Grab Settings
		$query = $this->EE->db->query("SELECT settings FROM exp_modules WHERE module_name = 'Forms'");
		if ($query->row('settings') != FALSE)
		{
			$settings = @unserialize($query->row('settings'));

			if (isset($settings['site:'.$this->site_id]) == FALSE)
			{
				$settings['site:'.$this->site_id] = array();
			}
		}

		$settings['site:'.$this->site_id] = $this->EE->input->post('settings');

		// Put it Back
		$this->EE->db->set('settings', serialize($settings));
		$this->EE->db->where('module_name', 'Forms');
		$this->EE->db->update('exp_modules');


		$this->EE->functions->redirect($this->base . '&method=index');
	}

	// ********************************************************************************* //

	public function mcp_globals()
	{
		$this->EE->cp->set_breadcrumb($this->base, $this->EE->lang->line('forms'));
		$this->EE->cp->add_js_script(array('ui' => array('tabs', 'datepicker', 'dialog')));

		// Add Global JS & CSS & JS Scripts
		$this->EE->forms_helper->mcp_meta_parser('gjs', '', 'Forms');
		$this->EE->forms_helper->mcp_meta_parser('css', FORMS_THEME_URL . 'forms_mcp.css', 'forms-pbf');
		$this->EE->forms_helper->mcp_meta_parser('css', FORMS_THEME_URL . 'chosen/chosen.css', 'jquery.chosen');
		$this->EE->forms_helper->mcp_meta_parser('css', FORMS_THEME_URL . 'bootstrap.popovers.css', 'bootstrap.popovers');
		$this->EE->forms_helper->mcp_meta_parser('js',  FORMS_THEME_URL . 'jquery.dataTables.js', 'jquery.dataTables', 'jquery');
		$this->EE->forms_helper->mcp_meta_parser('js',  FORMS_THEME_URL . 'jquery.dataTables.ColReorder.js', 'jquery.dataTables.ColReorder', 'jquery');
		$this->EE->forms_helper->mcp_meta_parser('js',  FORMS_THEME_URL . 'jquery.base64.js', 'jquery.base64', 'jquery');
		$this->EE->forms_helper->mcp_meta_parser('js',  FORMS_THEME_URL . 'chosen/jquery.chosen.js', 'jquery.chosen', 'jquery');
		$this->EE->forms_helper->mcp_meta_parser('js',  FORMS_THEME_URL . 'bootstrap.popovers.js', 'bootstrap.popovers', 'bootstrap');
		$this->EE->forms_helper->mcp_meta_parser('js',  FORMS_THEME_URL . 'bootstrap.modal.js', 'bootstrap.modal', 'bootstrap');
		$this->EE->forms_helper->mcp_meta_parser('js',  FORMS_THEME_URL . 'forms_mcp.js', 'forms-pbf');

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

	}

	// ********************************************************************************* //

} // END CLASS

/* End of file mcp.forms.php */
/* Location: ./system/expressionengine/third_party/tagger/mcp.forms.php */
