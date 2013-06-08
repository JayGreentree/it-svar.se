<?php if (!defined('BASEPATH')) die('No direct script access allowed');

/**
 * Forms Module Tags
 *
 * @package			DevDemon_Forms
 * @author			DevDemon <http://www.devdemon.com> - Lead Developer @ Parscale Media
 * @copyright 		Copyright (c) 2007-2011 Parscale Media <http://www.parscale.com>
 * @license 		http://www.devdemon.com/license/
 * @link			http://www.devdemon.com
 * @see				http://expressionengine.com/user_guide/development/module_tutorial.html#core_module_file
 */
class Forms
{

	/**
	 * Constructor
	 *
	 * @access public
	 *
	 * Calls the parent constructor
	 */
	public function __construct()
	{
		$this->EE =& get_instance();
		if (isset($this->EE->forms) == FALSE) $this->EE->forms = new stdClass();

		$this->EE->load->library('forms_helper');
		$this->EE->load->model('forms_model');
		$this->EE->lang->loadfile('forms');
		$this->site_id = $this->EE->forms_helper->get_current_site_id();
		$this->EE->forms_helper->define_theme_url();
	}

	// ********************************************************************************* //

	public function form($params=array(), $tagdata=NULL)
	{
		$this->EE->load->helper('form');

		$this->SSL = FALSE;
		if (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) != 'off') $this->SSL = TRUE;

		// Some Standard Vars
		$form_errors = (isset($_POST['forms_global_errors']) == TRUE) ? $_POST['forms_global_errors'] : array();
		$field_errors = (isset($_POST['forms_errors']) == TRUE) ? $_POST['forms_errors'] : array();

		// Load the params from an earlier submitted form.
		if (isset($_POST['FDATA']) === TRUE)
		{
			$FDATA = @unserialize($this->EE->forms_helper->decode_string($this->EE->input->post('FDATA')));
			$params = $FDATA['params'];
		}
		else {
			if (empty($params) === TRUE) $params = $this->EE->TMPL->tagparams;
		}

		if ($tagdata === NULL) $tagdata = $this->EE->TMPL->tagdata;

		// Variable prefix
		$prefix = (isset($params['prefix']) === FALSE) ? 'forms:' : $params['prefix'].':';

		// Queue JS
		$this->EE->forms->queue_js = (isset($params['queue_js']) === TRUE && $params['queue_js'] == 'yes') ? TRUE : FALSE;

		// -----------------------------------------
		// Form Name="" or ID?
		// -----------------------------------------
		if (isset($params['form_name']) === TRUE OR isset($params['form_id']) === TRUE)
		{
			if (isset($params['form_name']) === TRUE)
			{
				$this->EE->db->where('form_url_title', $params['form_name']);
			}
			else
			{
				$this->EE->db->where('form_id', $params['form_id']);
			}
		}
		else
		{
			// -----------------------------------------
			// Do we have entry_id ?
			// -----------------------------------------
			$entry_id = FALSE;
			if (isset($params['entry_id']) === TRUE) $entry_id = $params['entry_id'];
			if (isset($params['url_title']) === TRUE)
			{
				$q = $this->EE->db->query('SELECT entry_id FROM exp_channel_titles WHERE url_title = "'.$this->EE->db->escape($params['url_title']).'" ');
				if ($q->num_rows() > 0) $entry_id = $q->row('entry_id');
			}

			if (! $entry_id)
			{
				$this->EE->TMPL->log_item('FORMS: Entry ID could not be resolved (form_name=""/form_id="" either)');
				return $this->EE->forms_helper->custom_no_results_conditional($prefix.'no_form', $tagdata);
			}

			$this->EE->db->where('entry_id', $entry_id);
		}

		// Field ID
		if (isset($params['field_id']) === true && $params['field_id'] > 0)
		{
			$this->EE->db->where('ee_field_id', $params['field_id']);
		}

		// -----------------------------------------
		// Grab the form
		// -----------------------------------------
		$this->EE->db->select('*');
		$this->EE->db->from('exp_forms');
		$this->EE->db->limit(1);
		$query = $this->EE->db->get();

		// Did we find anything?
		if ($query->num_rows() == 0)
		{
			$this->EE->TMPL->log_item('FORMS: No form has been found!');
			return $this->EE->forms_helper->custom_no_results_conditional($prefix.'no_form', $tagdata);
		}

		$form = $query->row_array();
		$form['form_settings'] = unserialize($form['form_settings']);
		$form['params'] = $params;
		$form['ip_address'] = $this->EE->forms_helper->getUserIp();

		// Lets store the params, in some cases it's just not defined? wtf
		$form['params'] = $params;

		// Lets make a reference so we can access it
		unset($this->EE->forms->form_data);
		$this->EE->forms->form_data =& $form;

		// -----------------------------------------
		// Force SSL?
		// -----------------------------------------
		if (isset($form['form_settings']['force_https']) === TRUE && $form['form_settings']['force_https'] == 'yes')
		{
			if ($this->SSL == FALSE)
			{
				// Redirect the user
				$this->EE->load->helper('url');

				$site_url = str_replace('http://', 'https://', $this->EE->config->item('base_url'));
				$this->EE->config->set_item('base_url',$site_url);
				$this->EE->config->set_item('site_url',$site_url);
				redirect(current_url());
			}
		}

		if ($this->SSL == TRUE)
		{
			$site_url = str_replace('http://', 'https://', $this->EE->config->item('base_url'));
			$this->EE->config->set_item('base_url',$site_url);
			$this->EE->config->set_item('site_url',$site_url);
		}

		// -----------------------------------------
		// Form Open?
		// -----------------------------------------
		if (isset($form['form_settings']['form_enabled']) == TRUE && $form['form_settings']['form_enabled'] != 'yes')
		{
			$this->EE->TMPL->log_item('FORMS: The form is closed! (Forms Settings)');
			return $this->EE->forms_helper->custom_no_results_conditional($prefix.'closed', $tagdata);
		}

		// Open FROM
		if (isset($form['form_settings']['open_fromto']['from']) == TRUE && $form['form_settings']['open_fromto']['from'] != FALSE)
		{
			$time = strtotime($form['form_settings']['open_fromto']['from'] . ' 01:01 AM');

			if ($time > $this->EE->localize->now)
			{
				$this->EE->TMPL->log_item('FORMS: The form is closed! (Forms Settings: Open FROM)');
				return $this->EE->forms_helper->custom_no_results_conditional($prefix.'closed', $tagdata);
			}
		}

		// Open TO
		if (isset($form['form_settings']['open_fromto']['to']) == TRUE && $form['form_settings']['open_fromto']['to'] != FALSE)
		{
			$time = strtotime($form['form_settings']['open_fromto']['to'] . ' 11:59 PM');

			if ($time < $this->EE->localize->now)
			{
				$this->EE->TMPL->log_item('FORMS: Form cannot be displayed (Member Group Restriction)');
				return $this->EE->forms_helper->custom_no_results_conditional($prefix.'no_form', $tagdata);
			}
		}

		// -----------------------------------------
		// Form Limit Reached?
		// -----------------------------------------
		if (isset($form['form_settings']['max_entries']) == TRUE && $form['form_settings']['max_entries'] != FALSE)
		{
			if ($form['total_submissions'] >= $form['form_settings']['max_entries'])
			{
				$this->EE->TMPL->log_item('FORMS: Form Closed! Max entries reached!');
				return $this->EE->forms_helper->custom_no_results_conditional($prefix.'closed', $tagdata);
			}
		}

		// -----------------------------------------
		// Member Group Restriction?
		// -----------------------------------------
		if (isset($form['form_settings']['member_groups']) == TRUE && is_array($form['form_settings']['member_groups']) == TRUE && empty($form['form_settings']['member_groups']) == FALSE)
		{
			if ($this->EE->session->userdata('group_id') != 1 && in_array($this->EE->session->userdata('group_id'), $form['form_settings']['member_groups']) == FALSE)
			{
				$this->EE->TMPL->log_item('FORMS: The form is closed! (Forms Settings)');
				return $this->EE->forms_helper->custom_no_results_conditional($prefix.'closed', $tagdata);
			}
		}

		// -----------------------------------------
		// Return URL
		// -----------------------------------------
		$form['return'] = $this->EE->uri->uri_string();

		// Return Param?
		if (isset($params['return']) === TRUE)
		{
			$form['return'] = $params['return'];
		}

		// Form Settings Override?
		if (isset($form['form_settings']['return_url']) == TRUE && $form['form_settings']['return_url'] != FALSE)
		{
			$form['return'] = $form['form_settings']['return_url'];
		}

		// -----------------------------------------
		// Display Error
		// -----------------------------------------
		$form['display_error'] = 'inline';

		if (isset($params['display_error']) === TRUE && $params['display_error'] == 'default')
		{
			$form['display_error'] = 'default';
		}

		// -----------------------------------------
		// Extra Params
		// -----------------------------------------
		$form['ignore_ip'] = 'no';
		if (isset($params['ignore_ip']) === TRUE && $params['ignore_ip'] == 'yes')
		{
			$form['ignore_ip'] = 'yes';
		}

		// -----------------------------------------
		// Grab the all fields
		// -----------------------------------------
		$colfields = array('columns_2', 'columns_3', 'columns_4', 'fieldset');
		$this->EE->db->select('*');
		$this->EE->db->from('exp_forms_fields');
		$this->EE->db->where('form_id', $form['form_id']);
		$this->EE->db->where('parent_id', 0);
		$this->EE->db->order_by('field_order', 'ASC');
		$query = $this->EE->db->get();

		// Did we find anything?
		if ($query->num_rows() == 0)
		{
			$this->EE->TMPL->log_item('FORMS: No form fields has been associated.');
			return $this->EE->forms_helper->custom_no_results_conditional($prefix.'no_form', $tagdata);
		}

		// Store the DB fields
		$dbfields = $query->result_array();

		// -----------------------------------------
		// Find Pagebreaks
		// -----------------------------------------
		$pagebreaks = array();
		$page_count = 2;
		foreach ($dbfields as $key => $field)
		{
			// Is it a pagebreak?
			if ($field['field_type'] == 'pagebreak')
			{
				// Are they any other fields after it?
				if (isset($dbfields[ ($key+1) ]) == TRUE && $dbfields[ ($key+1) ]['field_type'] != 'pagebreak')
				{
					$pagebreaks[$page_count] = $field['field_id'];
					$page_count++;
					continue;
				}
			}
		}

		// -----------------------------------------
		// What Page are we on?
		// -----------------------------------------
		$form['paging'] = FALSE;
		$form['current_page']	= 1;
		$form['total_pages']	= count($pagebreaks)+1;
		$form['fields_shown']	= array();

		if (isset($_POST['page']) == TRUE)
		{
			$form['current_page']	= $_POST['page'];
		}

		$form['pages_left'] = $form['total_pages'] - $form['current_page'];

		// -----------------------------------------
		// Remove all fields that don't belong to this page
		// -----------------------------------------
		if ($form['total_pages'] > 1)
		{
			$form['paging'] = TRUE;
			$this->EE->TMPL->log_item("FORMS: Pagebreaks Found. Total: {$form['total_pages']}");
			$this->EE->TMPL->log_item("FORMS: (Paging) Current Page: {$form['current_page']}");

			// There are two pathways,
			// 1. First page, loop over all fields until you find a pagebreak
			// 2. Delete all fields untill you find your id

			if ($form['current_page'] == 1)
			{
				$pagebreak_found = FALSE;
				$this->EE->TMPL->log_item("FORMS: (Paging) Looping over all fields. (START) | Pathway 1");

				foreach ($dbfields as $key => $field)
				{
					if ($pagebreak_found == TRUE OR $field['field_type'] == 'pagebreak')
					{
						if ($field['field_type'] == 'pagebreak') $this->EE->TMPL->log_item("FORMS: (Paging) Found pagebreak! Removing all future fields!");
						$pagebreak_found = TRUE;
						unset ($dbfields[$key]);
						continue;
					}

					$this->EE->TMPL->log_item("FORMS: (Paging) Adding Field to output (ID: {$field['field_id']}, Name: {$field['title']})");
				}

				$this->EE->TMPL->log_item("FORMS: (Paging) Looping over all fields. (END) | Pathway 1");
			}
			else
			{
				$pagebreak_id = $pagebreaks[$form['current_page']];
				$pagebreak_found = FALSE;
				$this->EE->TMPL->log_item("FORMS: (Paging) Looping over all fields. (START) | Pathway 2");

				foreach ($dbfields as $key => $field)
				{
					// Is this our pagebreak field_id?
					if ($field['field_id'] == $pagebreak_id)
					{
						// Mark it and remove it
						$this->EE->TMPL->log_item("FORMS: (Paging) Pagebreak of current page FOUND!");
						$pagebreak_found = TRUE;
						unset ($dbfields[$key]);
						continue;
					}

					// We didn't find our pagebreak yet, delete it
					if ($pagebreak_found == FALSE)
					{
						$this->EE->TMPL->log_item("FORMS: (Paging) Pagebreak not found, removing field. (ID: {$field['field_id']}, Name: {$field['title']})");
						unset ($dbfields[$key]);
						continue;
					}

					// Did we find our pagebreak? And is this a pagebreak?
					if ($pagebreak_found == TRUE && $field['field_type'] == 'pagebreak')
					{
						// Mark it, and delete all future ones!
						$this->EE->TMPL->log_item("FORMS: (Paging) Found next pagebreak! Removing all future fields!");
						$pagebreak_found = FALSe;
						unset ($dbfields[$key]);
						continue;
					}

					$this->EE->TMPL->log_item("FORMS: (Paging) Adding Field to output (ID: {$field['field_id']}, Name: {$field['title']})");
					$fields_shown[] = $field['field_id'];
				}

				$this->EE->TMPL->log_item("FORMS: (Paging) Looping over all fields. (END) | Pathway 2");
			}
		}
		else
		{
			$this->EE->TMPL->log_item("FORMS: (Paging) Field Added to output (ID: {$field['field_id']}, Name: {$field['title']})");
		}

		// -----------------------------------------
		// Output CSS & JS?
		// -----------------------------------------
		$css_js = '';
		$output_css = (isset($params['output_css']) === TRUE && $params['output_css'] == 'no') ? FALSE : TRUE;
		$output_js = (isset($params['output_js']) === TRUE && $params['output_js'] == 'no') ? FALSE : TRUE;

		if ( $output_css && isset($this->EE->session->cache['forms']['outputted_css']) === FALSE)
		{
			$css_js .= '<link rel="stylesheet" href="' . FORMS_THEME_URL . 'forms_base.css" type="text/css" media="print, projection, screen" />';
			$this->EE->session->cache['forms']['outputted_css'] = TRUE;
		}

		if ( $output_js && isset($this->EE->session->cache['forms']['outputted_js']) === FALSE)
		{
			$css_js .= $this->EE->forms_helper->output_js_buffer('<script type="text/javascript" src="' . FORMS_THEME_URL . 'forms_base.js"></script>');
			$this->EE->session->cache['forms']['outputted_js'] = TRUE;
		}


		// -----------------------------------------
		// Parse Fields
		// -----------------------------------------
		$fields = array();

		foreach ($dbfields as $field)
		{
			$this->EE->TMPL->log_item('FORMS: Start Render: ' . $field['field_type']);

			// Grab our field settings
			$field['settings'] = @unserialize($field['field_settings']);

			// Our Form Name
			$field['form_name'] = 'fields[' . $field['field_id'] . ']';
			$field['form_elem_id'] = 'ddform_' . $field['field_id'];

			// Then Finally our form settings!
			$field['form_settings'] = $form['form_settings'];

			$field['disable_title'] = (isset($this->EE->formsfields[ $field['field_type'] ]->info['disable_title']) == TRUE && $this->EE->formsfields[ $field['field_type'] ]->info['disable_title'] == TRUE) ? TRUE : FALSE;

			// Add to fields shown
			$form['fields_shown'][] = $field['field_id'];

			if (in_array($field['field_type'], $colfields) == TRUE)
			{
				$this->EE->db->select('*');
				$this->EE->db->from('exp_forms_fields');
				$this->EE->db->where('form_id', $form['form_id']);
				$this->EE->db->where('parent_id', $field['field_id']);
				$this->EE->db->order_by('column_number', 'ASC');
				$this->EE->db->order_by('field_order', 'ASC');
				$subquery = $this->EE->db->get();

				foreach ($subquery->result_array() as $subfield)
				{
					$subfield['settings'] = @unserialize($subfield['field_settings']);
					$subfield['form_name'] = 'fields[' . $subfield['field_id'] . ']';
					$subfield['form_elem_id'] = 'ddform_' . $subfield['field_id'];
					$subfield['form_settings'] = $form['form_settings'];
					$subfield['disable_title'] = (isset($this->EE->formsfields[ $subfield['field_type'] ]->info['disable_title']) == TRUE && $this->EE->formsfields[ $subfield['field_type'] ]->info['disable_title'] == TRUE) ? TRUE : FALSE;
					$field['columns'][ $subfield['column_number'] ][] = $this->EE->formsfields[ $subfield['field_type'] ]->display_field($subfield, TRUE);

					$form['fields_shown'][] = $subfield['field_id'];
				}

			}


			$field['html'] = $this->EE->formsfields[ $field['field_type'] ]->display_field($field, TRUE);

			// Add it to the array
			$fields[] = $field;
		}

		// -----------------------------------------
		// Submit Button
		// -----------------------------------------
		if (isset($params['output_submit']) === FALSE OR  $params['output_submit'] != 'no')
		{
			$submit_btn = '';

			// Default Button?
			if ($form['form_settings']['submit_button']['type'] == 'default')
			{
				if ($form['pages_left'] > 0)
				{
					$form['form_settings']['submit_button']['text'] = $form['form_settings']['submit_button']['text_next_page'];
				}

				$submit_btn	.= '<div class="dform_element submit_button"> <div class="dform_container"><div class="dfinput_full">';
				$submit_btn	.= '<input type="submit" class="submit" name="submit_button" value="'.$form['form_settings']['submit_button']['text'].'"/>';
				$submit_btn	.= '</div></div></div>';
			}

			// Image Button (also adds class="submit_button_image")
			else
			{
				if ($form['pages_left'] > 0)
				{
					$form['form_settings']['submit_button']['img_url'] = $form['form_settings']['submit_button']['img_url_next_page'];
				}

				$submit_btn	.= '<div class="dform_element submit_button submit_button_image"> <div class="dform_container"><div class="dfinput_full">';
				$submit_btn	.= '<input type="image" class="submit" name="submit_button" src="'.$form['form_settings']['submit_button']['img_url'].'"/>';
				$submit_btn	.= '</div></div></div>';
			}

			// Add it to the array
			$fields[] = array(
				'field_type' => 'submit_button',
				'field_id' => 0,
				'html' => $submit_btn
			);
		}

		// -----------------------------------------
		// Do we need to show the confirmation message?
		// -----------------------------------------
		if ($this->EE->session->flashdata('forms:show_confirm') == 'yes')
		{
			$css_js .= '<p class="dform_confirmation">' . $form['form_settings']['confirmation']['text'] . '</p>';

			if (isset($form['form_settings']['confirmation']['show_form']) === TRUE && $form['form_settings']['confirmation']['show_form'] == 'no')
			{
				return $css_js;
			}
		}

		// -----------------------------------------
		// Form Action URL
		// -----------------------------------------
		$action_url = current_url();

		// Override CURRENT URL
		if (isset($this->EE->forms->config['action_url_from_server_vars']) === TRUE && $this->EE->forms->config['action_url_from_server_vars'] == 'yes')
		{
			$pageURL = 'http';

			if ($this->SSL == TRUE) {
				$pageURL .= "s";
			}

			$pageURL .= "://";

			if ($_SERVER["SERVER_PORT"] != "80") {
				$pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
			} else {
				$pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
			}

			$action_url = $pageURL;
		}

		// Last Slash?
		if (substr($_SERVER['REQUEST_URI'], -1, 1) == '/') $action_url .= '/';

		// Double check SSL
		if (isset($form['form_settings']['force_https']) === TRUE && $form['form_settings']['force_https'] == 'yes')
		{
			$action_url = str_replace('http://', 'https://', $action_url);
		}

		// Triplecheck SSL
		if ($this->SSL == TRUE || strpos(current_url(), 'https://') === 0)
		{
			$action_url = str_replace('http://', 'https://', $action_url);
		}

		// -----------------------------------------
		// Hidden Fields
		// -----------------------------------------
		$hidden_fields = array();
		$hidden_fields['ACT'] = $this->EE->forms_helper->get_router_url('act_id', 'ACT_form_submission');
		$hidden_fields['XID'] = (isset($_POST['XID']) == TRUE) ? $_POST['XID'] : '';

		// -----------------------------------------
		// Store Paging?
		// -----------------------------------------
		if (isset($form['paging']) && $form['paging'] == TRUE && isset($_POST['fields']))
		{
			foreach ($_POST['fields'] as $field_id => $val)
			{
				$form['fields_shown'][] = $field_id;

				// Is it an array?
				if (is_array($val) === TRUE)
				{
					// Loop
					foreach ($val as $key => $val_sub)
					{
						// store it
						$hidden_fields["fields[{$field_id}][{$key}]"] = $val_sub;
					}

					continue;
				}

				// Simle field, just store it
				$hidden_fields["fields[{$field_id}]"] = $val;
			}
		}

		// -----------------------------------------
		// FieldData
		// -----------------------------------------
		$hidden_fields['FDATA'] = $this->EE->forms_helper->encrypt_string(serialize($form));

		//----------------------------------------
		// <form> Data!
		//----------------------------------------
		$formdata = array();
		$formdata['enctype'] = 'multi';
		$formdata['hidden_fields'] = $hidden_fields;
		$formdata['action']	= $action_url;
		$formdata['name']	= '';
		$formdata['id']		= (isset($params['attr:id']) === TRUE) ? $params['attr:id'] : 'new_submission';
		$formdata['class']	= (isset($params['attr:class']) === TRUE) ? $params['attr:class'] : '';
		$formdata['onsubmit'] = (isset($params['attr:onsubmit']) === TRUE) ? $params['attr:onsubmit'] : '';


		$OUT = '';
		$OUT_FORM_PREPEND = '';

		//----------------------------------------
		// Snaptcha
		//----------------------------------------
		if ( isset($form['form_settings']['snaptcha']) == TRUE && $form['form_settings']['snaptcha'] == 'yes')
		{
			// Does the file exist?
			if (isset($this->EE->extensions->version_numbers['Snaptcha_ext']) == TRUE)
			{
				require_once(PATH_THIRD.'snaptcha/ext.snaptcha.php');
				$SNAP = new Snaptcha_ext();

				// We need to find the settings
				foreach ($this->EE->extensions->extensions['insert_comment_start'] as $priority => $exts)
				{
					// Loop over all extension
					foreach ($exts as $name => $ext)
					{
						if ($name == 'Snaptcha_ext')
						{
							// Store the Snaptcha field settings
							$SNAP->settings = unserialize($ext[1]);
						}
					}
				}

				// Append the field!
				$OUT_FORM_PREPEND .= $SNAP->comment_field($OUT_FORM_PREPEND);
			}
		}

		// We need to UNSET THIS HERE, Just in Case
		unset($this->EE->session->cache['forms']['ee_entry_row']);

		// -----------------------------------------
		// Single Tag Pair?
		// -----------------------------------------
		if ($tagdata == FALSE)
		{
			$fdata = '';
			foreach ($fields as $field) $fdata .= $field['html'];

			// -----------------------------------------
			// Contruct Final Output
			// -----------------------------------------
			$OUT = $css_js;
			$OUT .= $this->EE->functions->form_declaration($formdata);
			$OUT .= $OUT_FORM_PREPEND;
			$OUT .= '<div class="dform">' . $fdata . '</div>';
			$OUT .= '</form>';

			$this->return_data = $OUT;
			return $OUT;
		}

		// -----------------------------------------
		// {forms:fields} tag pair exists?
		// -----------------------------------------
		if (strpos($tagdata, LD.'/'.$prefix.'fields'.RD) === FALSE)
		{
			$this->EE->TMPL->log_item('FORMS: The fields variable pair was not found! ');
			return $this->EE->forms_helper->custom_no_results_conditional($prefix.'no_form', $tagdata);
		}

		$pair_data = $this->EE->forms_helper->fetch_data_between_var_pairs($prefix.'fields', $tagdata);

		// -----------------------------------------
		// Loop over all fields!
		// -----------------------------------------
		$final = '<div class="dform">';

		foreach ($fields as $count => $field)
		{
			$temp = '';
			$vars = array();
			$vars[$prefix.'field'] = $field['html'];
			$vars[$prefix.'field_type'] = $field['field_type'];

			$temp = $this->EE->TMPL->parse_variables_row($pair_data, $vars);
			$final .= $temp;
		}

		$final .= '</div> <!-- /dform -->';

		// Swap it back!
		$tagdata = $this->EE->forms_helper->swap_var_pairs($prefix.'fields', $final, $tagdata);

		// -----------------------------------------
		// Parse {forms:form_errors}
		// -----------------------------------------
		if (strpos($tagdata, LD.'/'.$prefix.'form_errors'.RD) !== FALSE)
		{
			$pair_data = $this->EE->forms_helper->fetch_data_between_var_pairs($prefix.'form_errors', $tagdata);
			$final = '';

			foreach ($form_errors as $count => $error)
			{
				$temp = '';
				$vars = array();
				$vars[$prefix.'error'] = $error['msg'];
				$vars[$prefix.'error_type'] = $error['type'];
				$vars[$prefix.'error_count'] = $count + 1;

				$temp = $this->EE->TMPL->parse_variables_row($pair_data, $vars);
				$final .= $temp;
			}

			// Swap it back!
			$tagdata = $this->EE->forms_helper->swap_var_pairs($prefix.'form_errors', $final, $tagdata);
		}

		// -----------------------------------------
		// Parse Form Variables
		// -----------------------------------------
		$vars = array();
		$vars[$prefix.'form_id']         = $form['form_id'];
		$vars[$prefix.'label']           = $form['form_title'];
		$vars[$prefix.'short_name']      = $form['form_url_title'];
		$vars[$prefix.'entry_id']        = $form['entry_id'];
		$vars[$prefix.'channel_id']      = $form['channel_id'];
		$vars[$prefix.'ee_field_id']     = $form['ee_field_id'];
		$vars[$prefix.'member_id']       = $form['member_id'];
		$vars[$prefix.'date_created']    = $form['date_created'];
		$vars[$prefix.'date_last_entry'] = $form['date_last_entry'];
		$vars[$prefix.'total_entries']   = $form['total_submissions'];
		$vars[$prefix.'current_page']    = $form['current_page'];
		$vars[$prefix.'total_pages']     = $form['total_pages'];

		$vars[$prefix.'paged']		     = ($form['total_pages'] > 1) ? 'yes' : '';
		$vars[$prefix.'total_form_errors'] = count($form_errors);
		$vars[$prefix.'total_field_errors'] = count($field_errors);

		$tagdata = $this->EE->TMPL->parse_variables_row($tagdata, $vars);

		// -----------------------------------------
		// Contruct Final Output
		// -----------------------------------------
		$OUT = $css_js;
		$OUT .= $this->EE->functions->form_declaration($formdata);
		$OUT .= $OUT_FORM_PREPEND;
		$OUT .= $tagdata;
		$OUT .= '</form>';


		return $OUT;
	}

	// ********************************************************************************* //

	public function entries()
	{
		// Variable prefix
		$prefix = $this->EE->TMPL->fetch_param('prefix', 'forms') . ':';

		// -----------------------------------------
		// fentry_id?
		// -----------------------------------------
		if ($this->EE->TMPL->fetch_param('fentry_id') != FALSE)
		{
			$query = $this->EE->db->select('form_id')->from('exp_forms_entries')->where('fentry_id', $this->EE->TMPL->fetch_param('fentry_id'))->get();

			if ($query->num_rows() == 0)
			{
				@$this->EE->db->_reset_select();
				$this->EE->TMPL->log_item('FORMS: Entry ID could not be resolved (form_name=""/form_id="" either)');
				return $this->EE->forms_helper->custom_no_results_conditional($prefix.'no_form', $this->EE->TMPL->tagdata);
			}

			$this->EE->db->where('form_id', $query->row('form_id'));
		}

		// -----------------------------------------
		// fentry_hash?
		// -----------------------------------------
		elseif ($this->EE->TMPL->fetch_param('fentry_hash') != FALSE)
		{
			$query = $this->EE->db->select('form_id')->from('exp_forms_entries')->where('fentry_hash', $this->EE->TMPL->fetch_param('fentry_hash'))->get();

			if ($query->num_rows() == 0)
			{
				@$this->EE->db->_reset_select();
				$this->EE->TMPL->log_item('FORMS: Entry ID could not be resolved (form_name=""/form_id="" either)');
				return $this->EE->forms_helper->custom_no_results_conditional($prefix.'no_form', $this->EE->TMPL->tagdata);
			}

			$this->EE->db->where('form_id', $query->row('form_id'));
		}

		// -----------------------------------------
		// Form Name="" or ID?
		// -----------------------------------------
		elseif ($this->EE->TMPL->fetch_param('form_name') != FALSE OR $this->EE->TMPL->fetch_param('form_id') != FALSE)
		{
			if ($this->EE->TMPL->fetch_param('form_name') != FALSE)
			{
				$this->EE->db->where('form_url_title', $this->EE->TMPL->fetch_param('form_name'));
			}
			else
			{
				$this->EE->db->where('form_id', $this->EE->TMPL->fetch_param('form_id'));
			}
		}
		else
		{
			// -----------------------------------------
			// Do we have entry_id ?
			// -----------------------------------------
			$entry_id = FALSE;
			if ($this->EE->TMPL->fetch_param('entry_id') != FALSE) $entry_id = $this->EE->TMPL->fetch_param('entry_id');
			if ($this->EE->TMPL->fetch_param('url_title') != FALSE)
			{
				$q = $this->EE->db->query("SELECT entry_id FROM exp_channel_titles WHERE url_title = '".$this->EE->db->escape($this->EE->TMPL->fetch_param('url_title'))."' ");
				if ($q->num_rows() > 0) $entry_id = $q->row('entry_id');
			}

			if (! $entry_id)
			{
				@$this->EE->db->_reset_select();
				$this->EE->TMPL->log_item('FORMS: Entry ID could not be resolved (form_name=""/form_id="" either)');
				return $this->EE->forms_helper->custom_no_results_conditional($prefix.'no_form', $this->EE->TMPL->tagdata);
			}

			$this->EE->db->where('entry_id', $entry_id);
		}

		// -----------------------------------------
		// Grab the form
		// -----------------------------------------
		$this->EE->db->select('*');
		$this->EE->db->from('exp_forms');
		$this->EE->db->limit(1);
		$query = $this->EE->db->get();

		// Did we find anything?
		if ($query->num_rows() == 0)
		{
			$this->EE->TMPL->log_item('FORMS: No form has been found!');
			return $this->EE->forms_helper->custom_no_results_conditional($prefix.'no_form', $this->EE->TMPL->tagdata);
		}

		$form = $query->row_array();
		$field['form_settings'] = unserialize($form['form_settings']);

		// -----------------------------------------
		// Grab the all fields
		// -----------------------------------------
		$this->EE->db->select('*');
		$this->EE->db->from('exp_forms_fields');
		$this->EE->db->where('form_id', $form['form_id']);
		$this->EE->db->where_not_in('field_type', array('pagebreak', 'fieldset', 'columns_2', 'columns_3', 'columns_4', 'html') );
		$this->EE->db->order_by('field_order', 'ASC');
		$query = $this->EE->db->get();

		// Did we find anything?
		if ($query->num_rows() == 0)
		{
			$this->EE->TMPL->log_item('FORMS: No form fields has been associated.');
			return $this->EE->forms_helper->custom_no_results_conditional($prefix.'no_form', $this->EE->TMPL->tagdata);
		}

		// Store the DB fields
		$fields = array();
		foreach ($query->result_array() as $row)
		{
			if ($row['field_type'] == 'pagebreak') continue;
			$row['form_settings'] = $form['form_settings'];
			$row['settings'] = @unserialize($row['field_settings']);
			$fields[] = $row;
		}

		// -----------------------------------------
		// Grab the all entries
		// -----------------------------------------
		$this->EE->db->select('fe.*, mb.screen_name, mb.username');
		$this->EE->db->from('exp_forms_entries fe');
		$this->EE->db->join('exp_members mb', ' fe.member_id = mb.member_id ', 'left');
		$this->EE->db->where('fe.form_id', $form['form_id']);


		// -----------------------------------------
		// Member ID
		// -----------------------------------------
		$member_id = $this->EE->TMPL->fetch_param('member_id');
		if ($member_id == 'CURRENT_USER')
		{
			$this->EE->db->where('fe.member_id', $this->EE->session->userdata['member_id']);
		}
		elseif ($member_id != FALSE)
		{
			// Multiple Authors?
			if (strpos($member_id, '|') !== FALSE)
			{
				$cols = explode('|', $member_id);
				$this->EE->db->where_in('fe.member_id', $cols);
			}
			else
			{
				$this->EE->db->where('fe.member_id', $member_id);
			}
		}

		// Sort
		$sort = ($this->EE->TMPL->fetch_param('sort') == 'desc') ? 'DESC': 'ASC';

		// Fentry ID
		if ($this->EE->TMPL->fetch_param('fentry_id') != FALSE) $this->EE->db->where('fe.fentry_id', $this->EE->TMPL->fetch_param('fentry_id'));
		if ($this->EE->TMPL->fetch_param('fentry_hash') != FALSE) $this->EE->db->where('fe.fentry_hash', $this->EE->TMPL->fetch_param('fentry_hash'));

		$this->EE->db->order_by('fe.date', $sort);
		$query = $this->EE->db->get();

		// Did we find anything?
		if ($query->num_rows() == 0)
		{
			$this->EE->TMPL->log_item('FORMS: No Entries found');
			return $this->EE->forms_helper->custom_no_results_conditional($prefix.'no_entries', $this->EE->TMPL->tagdata);
		}

		$pair_data = $this->EE->forms_helper->fetch_data_between_var_pairs($prefix.'fields', $this->EE->TMPL->tagdata);

		$OUT = '';
		$total = $query->num_rows();

		foreach ($query->result() as $count => $entry)
		{
			$temp = $this->EE->TMPL->tagdata;

			$vars = array();
			$vars[$prefix.'member_id'] = $entry->member_id;
			$vars[$prefix.'ip_address'] = long2ip($entry->ip_address);
			$vars[$prefix.'date'] = $entry->date;
			$vars[$prefix.'country_cc'] = $entry->country;
			$vars[$prefix.'screen_name'] = $entry->screen_name;
			$vars[$prefix.'username'] = $entry->username;



			$inner = '';
			foreach ($fields as $field)
			{
				$name = 'fid_'.$field['field_id'];
				$ivars = array();
				$ivars[$prefix.'field'] = $this->EE->formsfields[ $field['field_type'] ]->output_data($field, $entry->$name ,'html');
				$ivars[$prefix.'field_type'] = $field['field_type'];
				$ivars[$prefix.'field_label'] = $field['title'];
				$ivars[$prefix.'field_name'] = $field['url_title'];

				$vars[$prefix.'field:'.$field['url_title']] = $ivars[$prefix.'field'];

				$inner .= $this->EE->TMPL->parse_variables_row($pair_data, $ivars);
			}

			$temp = $this->EE->TMPL->parse_variables_row($temp, $vars);
			$temp = $this->EE->forms_helper->swap_var_pairs($prefix.'fields', $inner, $temp);

			$OUT .= $temp;
		}


		return $OUT;
	}

	// ********************************************************************************* //

	public function output_js()
	{
		$js = '';

		if (isset($this->EE->forms->js_buffer) === TRUE && is_array($this->EE->forms->js_buffer))
		{
			$js = implode('', $this->EE->forms->js_buffer);
		}

		if (strpos($this->EE->TMPL->tagdata, LD.'forms_js'.RD) !== FALSE)
		{
			$this->EE->TMPL->tagdata = str_replace(LD.'forms_js'.RD, $js, $this->EE->TMPL->tagdata);
			return $this->EE->TMPL->tagdata;
		}
		else
		{
			return $js;
		}
	}

	// ********************************************************************************* //

	public function ACT_general_router()
	{
		@header('Access-Control-Allow-Origin: *');
		@header('Access-Control-Allow-Credentials: true');
        @header('Access-Control-Max-Age: 86400');
        @header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
        @header('Access-Control-Allow-Headers: Keep-Alive, Content-Type, User-Agent, Cache-Control, X-Requested-With, X-File-Name, X-File-Size');

		// -----------------------------------------
		// Ajax Request?
		// -----------------------------------------
		if ( $this->EE->input->get_post('ajax_method') != FALSE OR (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') )
		{
			// Load Library
			if (class_exists('forms_AJAX') != TRUE) include 'ajax.forms.php';

			$AJAX = new forms_AJAX();

			// Shoot the requested method
			$method = $this->EE->input->get_post('ajax_method');
			echo $AJAX->$method();
			exit();
		}

		exit('CHANNEL FORMS ACT!');

	}

	// ********************************************************************************* //

	public function ACT_form_submission()
	{
		// Load Library
		if (class_exists('Forms_ACT') != TRUE) include 'act.forms.php';

		$ACT = new Forms_ACT();

		$ACT->form_submission();
	}

	// ********************************************************************************* //


} // END CLASS

/* End of file mod.forms.php */
/* Location: ./system/expressionengine/third_party/forms/mod.forms.php */
