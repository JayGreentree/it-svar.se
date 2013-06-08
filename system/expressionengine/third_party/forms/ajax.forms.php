<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Forms AJAX File
 *
 * @package			DevDemon_Forms
 * @author			DevDemon <http://www.devdemon.com> - Lead Developer @ Parscale Media
 * @copyright 		Copyright (c) 2007-2010 Parscale Media <http://www.parscale.com>
 * @license 		http://www.devdemon.com/license/
 * @link			http://www.devdemon.com/forms/
 */
class Forms_AJAX
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

		$this->EE->load->library('forms_helper');
		$this->EE->load->library('firephp');
		$this->EE->lang->loadfile('forms');
		$this->EE->load->model('forms_model');
		$this->EE->load->helper('form');
		$this->EE->config->load('forms_config');

		if ($this->EE->input->get_post('site_id')) $this->site_id = $this->EE->input->get_post('site_id');
		else if ($this->EE->input->cookie('cp_last_site_id')) $this->site_id = $this->EE->input->cookie('cp_last_site_id');
		else $this->site_id = $this->EE->config->item('site_id');
	}

	// ********************************************************************************* //

	public function prefetch_fields()
	{
		$out = array('success' => 'yes', 'fields' => array());

		// Sometimes this might be empty!
		if (isset($_POST['fields']) === FALSE || empty($_POST['fields']) === TRUE)
		{
			exit($this->EE->forms_helper->generate_json($out));
		}

		$fields = $_POST['fields'];
		unset($_POST['fields']);

		// Are we in the PBF? Lets Fake it then!
		if (isset($_POST['form']) === FALSE)
		{
			$temp = '';
			foreach ($_POST as $key => $value)
			{
				if ( strpos($key, 'field_id_') !== FALSE ) $temp = $key;
			}
			if ($temp == FALSE) exit('{}');
			else $_POST['form'] = $_POST[$temp];
		}


		// Loop over all categories within this field
		foreach($this->EE->formsfields as $class_name => &$cfield)
		{
			// Do we need to grab them?
			if (in_array($class_name, $fields) === FALSE) continue;

			// Lets make our field
			$field = array();
			$field['title'] = $cfield->info['title'];
			$field['url_title'] = $class_name;
			$field['description'] = '';
			$field['field_type'] = $class_name;
			$field['field_type_label'] = $cfield->info['title'];
			$field['field_id'] = 0;
			$field['field_hash'] = '';
			$field['required'] = 0;
			$field['show_label'] = 1;
			$field['label_position'] = 'auto';
			$field['conditionals'] = array('options' => array(), 'conditionals' => array());
			$field['disable_title'] = (isset($cfield->info['disable_title']) == TRUE && $cfield->info['disable_title'] == TRUE) ? TRUE : FALSE;
			$field['settings'] = array();
			$field['form_name'] = $_POST['form']['field_name'];

			// Do we have any field settings stored?
			if (isset($vData['form']['field_settings'][$class_name]) == TRUE)
			{
				$field['settings'] = $vData['form']['field_settings'][$class_name];
			}

			// We need to add the form settings
			$field['form_settings'] = $_POST['form']['settings'];

			// Continue with out JSON
			$out['fields'][$class_name] = $this->display_composer_field($field);
		}

		exit($this->EE->forms_helper->generate_json($out));
	}

	// ********************************************************************************* //

	public function fetch_db_fields()
	{
		$out = array('fields' => array());
		$colfields = array('columns_2', 'columns_3', 'columns_4', 'fieldset', 'html', 'pagebreak', 'html');

		$form_id = $this->EE->input->post('form_id');

		if ($form_id == FALSE) exit($this->EE->forms_helper->generate_json($out));

		// Are we in the PBF? Lets Fake it then!
		if (isset($_POST['form']) === FALSE)
		{
			$temp = '';
			foreach ($_POST as $key => $value)
			{
				if ( strpos($key, 'field_id_') !== FALSE ) $temp = $key;
			}
			if ($temp == FALSE) exit('{}');
			else $_POST['form'] = $_POST[$temp];
		}

		$this->EE->db->select('*');
		$this->EE->db->from('exp_forms_fields');
		$this->EE->db->where('form_id', $form_id);
		$this->EE->db->where('parent_id', 0);
		$this->EE->db->order_by('field_order', 'ASC');
		$query = $this->EE->db->get();

		foreach ($query->result() as $row)
		{
			// Conditionals
			$row->conditionals = ($row->conditionals != FALSE) ? unserialize($row->conditionals) : array() ;
			$conditionals = array('options' => array(), 'conditionals' => array());
			if (isset($row->conditionals['options']) === TRUE) $conditionals['options'] = $row->conditionals['options'];
			if (isset($row->conditionals['conditionals']) === TRUE) $conditionals['conditionals'] = $row->conditionals['conditionals'];

			// Lets make our field
			$field = array();
			$field['title'] = $row->title;
			$field['url_title'] = $row->url_title;
			$field['description'] = $row->description;
			$field['field_type'] = $row->field_type;
			$field['field_type_label'] = $this->EE->formsfields[$row->field_type]->info['title'];
			$field['field_id'] = $row->field_id;
			$field['field_hash'] = $row->field_id;
			$field['required'] = $row->required;
			$field['show_label'] = $row->show_label;
			$field['label_position'] = $row->label_position;
			$field['conditionals'] = $conditionals;
			$field['disable_title'] = (isset($this->EE->formsfields[$row->field_type]->info['disable_title']) == TRUE && $this->EE->formsfields[$row->field_type]->info['disable_title'] == TRUE) ? TRUE : FALSE;
			$field['settings'] = unserialize($row->field_settings);
			$field['form_name'] = $_POST['form']['field_name'];

			// We need to add the form settings
			$field['form_settings'] = $_POST['form']['settings'];

			if (in_array($field['field_type'], $colfields) == TRUE)
			{
				$this->EE->db->select('*');
				$this->EE->db->from('exp_forms_fields');
				$this->EE->db->where('form_id', $form_id);
				$this->EE->db->where('parent_id', $row->field_id);
				$this->EE->db->order_by('column_number', 'ASC');
				$this->EE->db->order_by('field_order', 'ASC');
				$subquery = $this->EE->db->get();

				foreach ($subquery->result() as $subrow)
				{
					// Conditionals
					$subrow->conditionals = ($subrow->conditionals != FALSE) ? unserialize($subrow->conditionals) : array() ;
					$conditionals = array('options' => array(), 'conditionals' => array());
					if (isset($subrow->conditionals['options']) === TRUE) $conditionals['options'] = $subrow->conditionals['options'];
					if (isset($subrow->conditionals['conditionals']) === TRUE) $conditionals['conditionals'] = $subrow->conditionals['conditionals'];

					// Lets make our field
					$subfield = array();
					$subfield['title'] = $subrow->title;
					$subfield['url_title'] = $subrow->url_title;
					$subfield['description'] = $subrow->description;
					$subfield['field_type'] = $subrow->field_type;
					$subfield['field_type_label'] = $this->EE->formsfields[$subrow->field_type]->info['title'];
					$subfield['field_id'] = $subrow->field_id;
					$subfield['field_hash'] = $subrow->field_id;
					$subfield['required'] = $subrow->required;
					$subfield['show_label'] = $subrow->show_label;
					$subfield['label_position'] = $subrow->label_position;
					$subfield['enable_conditionals'] = (empty($conditionals['conditionals']) != FALSE) ? 1 : 0 ;
					$subfield['conditionals'] = $conditionals;
					$subfield['disable_title'] = (isset($this->EE->formsfields[$subrow->field_type]->info['disable_title']) == TRUE && $this->EE->formsfields[$subrow->field_type]->info['disable_title'] == TRUE) ? TRUE : FALSE;
					$subfield['settings'] = unserialize($subrow->field_settings);
					$subfield['form_name'] = $_POST['form']['field_name'];

					// We need to add the form settings
					$subfield['form_settings'] = $_POST['form']['settings'];

					$html = $this->display_composer_field($subfield);

					$field['columns'][$subrow->column_number][] = $html;
				}

			}

			// Continue with out JSON
			$out['fields'][] = $this->display_composer_field($field);
		}

		exit($this->EE->forms_helper->generate_json($out));
	}

	// ********************************************************************************* //

	private function display_composer_field($field)
	{
		$final = '';
		$field['field_body'] = $this->EE->formsfields[$field['field_type']]->display_field($field, FALSE);

		$this->EE->load->add_package_path(PATH_THIRD . 'forms/');

		$temp_field = $field;
		unset($field['columns']);
		unset($temp_field['field_body']);
		unset($temp_field['form_settings']);
		unset($temp_field['columns']);

		$field['temp_field'] = serialize($temp_field);

		$field['form_name_settings'] = 'field_settings';
		$field['field_body_settings'] = $this->EE->formsfields[$field['field_type']]->display_settings($field, FALSE);

		$this->EE->load->add_package_path(PATH_THIRD . 'forms/');
		$field['settings_body'] = $this->EE->load->view('form_builder/composer_field_settings.php', $field, TRUE);

		$final = $this->EE->load->view('form_builder/composer_field.php', $field, TRUE);

		return $final;
	}

	// ********************************************************************************* //

	public function save_field()
	{
		$out = array();
		$settings = $this->EE->input->get_post('settings');
		if (is_array($settings) == FALSE) $settings = array();

		// Conditionals
		$conditionals = array('options' => array(), 'conditionals' => array());
		if (isset($settings['conditionals_options']) === TRUE)
		{
			$conditionals['options'] = $settings['conditionals_options'];
			foreach ($settings['conditionals'] as $cond)
			{
				$cond['value'] = trim($cond['value']);
				if ($cond['value'] === '') continue;
				$conditionals['conditionals'][] = $cond;
			}
		}

		// Are we in the PBF? Lets Fake it then!
		if (isset($_POST['form']) === FALSE)
		{
			$temp = '';
			foreach ($_POST as $key => $value)
			{
				if ( strpos($key, 'field_id_') !== FALSE ) $temp = $key;
			}
			if ($temp == FALSE) exit('{}');
			else $_POST['form'] = $_POST[$temp];
		}

		// Lets make our field
		$field = array();
		$field['title'] = (isset($settings['title']) === TRUE) ? $settings['title'] : 'Untitled';
		$field['url_title'] = (isset($settings['url_title']) === TRUE) ? $settings['url_title'] : 'untitled';
		$field['description'] = (isset($settings['description']) === TRUE) ? $settings['description'] : '';
		$field['field_type'] = $settings['field_type'];
		$field['field_type_label'] = $this->EE->formsfields[ $settings['field_type'] ]->info['title'];
		$field['field_id'] = $settings['field_id'];
		$field['field_hash'] = $settings['field_hash'];
		$field['required'] = ($settings['required'] == 'yes') ? TRUE : FALSE;
		$field['show_label'] = $settings['show_label'];
		$field['label_position'] = $settings['label_position'];
		$field['conditionals'] = $conditionals;
		$field['disable_title'] = (isset($this->EE->formsfields[ $settings['field_type'] ]->info['disable_title']) == TRUE && $this->EE->formsfields[ $settings['field_type'] ]->info['disable_title'] == TRUE) ? TRUE : FALSE;
		$field['settings'] = $this->EE->input->get_post('field_settings');
		$field['form_name'] = $_POST['form']['field_name'];

		// We need to add the form settings
		$field['form_settings'] = $_POST['form']['settings'];

		// Continue with out JSON
		$out = $this->display_composer_field($field);

		exit($out);
	}

	// ********************************************************************************* //




















	public function datatable_submissions()
	{
		$this->EE->load->helper('form');

		//----------------------------------------
		// Prepare Data Array
		//----------------------------------------
		$data = array();
		$data['aaData'] = array();
		$data['iTotalDisplayRecords'] = 0; // Total records, after filtering (i.e. the total number of records after filtering has been applied - not just the number of records being returned in this result set)
		$data['sEcho'] = $this->EE->input->get_post('sEcho');

		// Total records, before filtering (i.e. the total number of records in the database)
		$data['iTotalRecords'] = $this->EE->db->count_all('exp_forms_entries');

		//----------------------------------------
		// Date Ranges?
		//----------------------------------------
		$date_from = FALSE;
		if (isset($_POST['filter']['date']['from']) != FALSE && $_POST['filter']['date']['from'] != FALSE)
		{
			$date_from = strtotime($_POST['filter']['date']['from'] . ' 01:00 AM');
		}

		$date_to = FALSE;
		if (isset($_POST['filter']['date']['to']) != FALSE && $_POST['filter']['date']['to'] != FALSE)
		{
			$date_to = strtotime($_POST['filter']['date']['to'] . ' 11:59 PM');
		}

		//----------------------------------------
		// Forms Filter
		//----------------------------------------
		$filter_forms = FALSE;
		if (isset($_POST['filter']['forms']) != FALSE && empty($_POST['filter']['forms']) == FALSE)
		{
			$filter_forms = $_POST['filter']['forms'];
		}

		//----------------------------------------
		// Country Filter
		//----------------------------------------
		$filter_country = FALSE;
		if (isset($_POST['filter']['country']) != FALSE && empty($_POST['filter']['country']) == FALSE)
		{
			$filter_country = $_POST['filter']['country'];
		}

		//----------------------------------------
		// Member Filter
		//----------------------------------------
		$filter_members = FALSE;
		if (isset($_POST['filter']['members']) != FALSE && empty($_POST['filter']['members']) == FALSE)
		{
			$filter_members = $_POST['filter']['members'];
		}

		$this->EE->db->save_queries = TRUE;

		//----------------------------------------
		// Total after filter
		//----------------------------------------
		$this->EE->db->select('COUNT(*) as total_records', FALSE);
		$this->EE->db->from('exp_forms_entries fe');
		$this->EE->db->join('exp_members mb', 'mb.member_id = fe.member_id', 'left');
		$this->EE->db->join('exp_forms f', 'f.form_id = fe.form_id', 'left');
		if ($date_from) $this->EE->db->where('fe.date >', $date_from);
		if ($date_to) $this->EE->db->where('fe.date <', $date_to);
		if ($filter_forms) $this->EE->db->where_in('fe.form_id', $filter_forms);
		if ($filter_country) $this->EE->db->where_in('fe.country', $filter_country);
		$this->EE->db->where('fe.site_id', $this->site_id);
		$query = $this->EE->db->get();
		$data['iTotalDisplayRecords'] = $query->row('total_records');
		$query->free_result();

		//----------------------------------------
		// Real Query
		//----------------------------------------
		$this->EE->db->select('fe.*, mb.screen_name, f.form_title');
		$this->EE->db->from('exp_forms_entries fe');
		$this->EE->db->join('exp_members mb', 'mb.member_id = fe.member_id', 'left');
		$this->EE->db->join('exp_forms f', 'f.form_id = fe.form_id', 'left');

		//----------------------------------------
		// Sort By
		//----------------------------------------
		$sort_cols = $this->EE->input->get_post('iSortingCols');

		for ($i = 0; $i < $sort_cols; $i++)
		{
			$col = $this->EE->input->get_post('iSortCol_'.$i);
			$sort =  $this->EE->input->get_post('sSortDir_'.$i);

			switch ($col)
			{
				case 0: // ID
					$this->EE->db->order_by('fe.fentry_id', $sort);
					break;
				case 1: // Member
					$this->EE->db->order_by('mb.screen_name', $sort);
					break;
				case 2: // Date
					$this->EE->db->order_by('fe.date', $sort);
					break;
				case 3: // Country
					$this->EE->db->order_by('fe.country', $sort);
					break;
				case 4: // IP
					$this->EE->db->order_by('fe.ip_address', $sort);
					break;
				case 5: // Form
					$this->EE->db->order_by('f.form_title', $sort);
					break;
			}
		}

		if ($sort_cols == 0) $this->EE->db->order_by('fe.date', 'DESC');

		//----------------------------------------
		// Limit
		//----------------------------------------
		$limit = 10;
		if ($this->EE->input->get_post('iDisplayLength') !== FALSE)
		{
			$limit = $this->EE->input->get_post('iDisplayLength');
			if ($limit < 1) $limit = 999999;
		}

		//----------------------------------------
		// WHERE/LIKE
		//----------------------------------------
		$this->EE->db->where('fe.site_id', $this->site_id);
		if ($date_from) $this->EE->db->where('fe.date >', $date_from);
		if ($date_to) $this->EE->db->where('fe.date <', $date_to);
		if ($filter_forms) $this->EE->db->where_in('fe.form_id', $filter_forms);
		if ($filter_country) $this->EE->db->where_in('fe.country', $filter_country);
		if ($filter_members) $this->EE->db->where_in('fe.member_id', $filter_members);

		//----------------------------------------
		// OFFSET & LIMIT & EXECUTE!
		//----------------------------------------
		$offset = 0;
		if ($this->EE->input->get_post('iDisplayStart') !== FALSE)
		{
			$offset = $this->EE->input->get_post('iDisplayStart');
		}

		$this->EE->db->limit($limit, $offset);
		$query = $this->EE->db->get();


		//----------------------------------------
		// Loop Over all
		//----------------------------------------
		foreach ($query->result() as $row)
		{
			// View Form
			$view_link = '<a href="' . $this->EE->input->post('mcp_base').AMP.'method=view_form'.AMP.'form_id='.$row->form_id.'" class="gForm">'.$row->form_title.'</a>';

			// Member Name
			if ($row->member_id == 0) $row->screen_name = $this->EE->lang->line('form:guest');

			//----------------------------------------
			// Create TR row
			//----------------------------------------
			$trow = array();
			$trow['DT_RowId']  = $row->fentry_id;
			$trow['fentry_id'] = $row->fentry_id;
			$trow['member']    = $row->screen_name;
			$trow['date']      = $this->EE->forms_helper->formatDate('%d-%M-%Y %g:%i %A', $row->date);
			$trow['country']   = strtoupper($row->country);
			$trow['ip']        = long2ip($row->ip_address);
			$trow['form']      = $view_link;
			$data['aaData'][] = $trow;
		}

		exit($this->EE->forms_helper->generate_json($data));
	}

	// ********************************************************************************* //

	public function datatable_forms_entries()
	{
		$this->EE->load->helper('form');

		$this->EE->db->save_queries = TRUE;
		$form_id = $this->EE->input->get_post('form_id');

		//----------------------------------------
		// Columns
		//----------------------------------------
		$cols = explode(',', $this->EE->input->get_post('sColumns'));
		$cols_inv = array_flip($cols);

		// Visible Cols
		$visible_cols = array();
		if (isset($_POST['visible_cols']) === TRUE)
		{
			foreach ($_POST['visible_cols'] as $colname)
			{
				// Only for real fields! And fill with dummy data
				if (strpos($colname, 'field_id_') !== FALSE)
				{
					$visible_cols[$colname] = substr($colname, 9);
				}
			}
		}

		//----------------------------------------
		// Prepare Data Array
		//----------------------------------------
		$data = array();
		$data['aaData'] = array();
		$data['iTotalDisplayRecords'] = 0; // Total records, after filtering (i.e. the total number of records after filtering has been applied - not just the number of records being returned in this result set)
		$data['sEcho'] = $this->EE->input->get_post('sEcho');

		// Total records, before filtering (i.e. the total number of records in the database)
		$query = $this->EE->db->select('COUNT(*) as total_records', FALSE)->from('exp_forms_entries')->where('form_id', $form_id)->get();
		$data['iTotalRecords'] = $query->row('total_records');

		//----------------------------------------
		// Date Ranges?
		//----------------------------------------
		$date_from = FALSE;
		if (isset($_POST['filter']['date']['from']) != FALSE && $_POST['filter']['date']['from'] != FALSE)
		{
			$date_from = strtotime($_POST['filter']['date']['from'] . ' 01:00 AM');
		}

		$date_to = FALSE;
		if (isset($_POST['filter']['date']['to']) != FALSE && $_POST['filter']['date']['to'] != FALSE)
		{
			$date_to = strtotime($_POST['filter']['date']['to'] . ' 11:59 PM');
		}

		//----------------------------------------
		// Country Filter
		//----------------------------------------
		$filter_country = FALSE;
		if (isset($_POST['filter']['country']) != FALSE && empty($_POST['filter']['country']) == FALSE)
		{
			$filter_country = $_POST['filter']['country'];
		}

		//----------------------------------------
		// Member Filter
		//----------------------------------------
		$filter_members = FALSE;
		if (isset($_POST['filter']['members']) != FALSE && empty($_POST['filter']['members']) == FALSE)
		{
			$filter_members = $_POST['filter']['members'];
		}

		$this->EE->db->save_queries = TRUE;

		//----------------------------------------
		// Total after filter
		//----------------------------------------
		$this->EE->db->select('COUNT(*) as total_records', FALSE);
		$this->EE->db->from('exp_forms_entries fe');
		$this->EE->db->join('exp_members mb', 'mb.member_id = fe.member_id', 'left');
		$this->EE->db->join('exp_forms f', 'f.form_id = fe.form_id', 'left');
		$this->EE->db->where('fe.form_id', $form_id);
		if ($date_from) $this->EE->db->where('fe.date >', $date_from);
		if ($date_to) $this->EE->db->where('fe.date <', $date_to);
		if ($filter_country) $this->EE->db->where_in('fe.country', $filter_country);
		if ($filter_members) $this->EE->db->where_in('fe.member_id', $filter_members);
		$query = $this->EE->db->get();
		$data['iTotalDisplayRecords'] = $query->row('total_records');
		$query->free_result();

		//----------------------------------------
		// Real Query
		//----------------------------------------
		$this->EE->db->select('fe.*, mb.screen_name, f.form_title');
		$this->EE->db->from('exp_forms_entries fe');
		$this->EE->db->join('exp_members mb', 'mb.member_id = fe.member_id', 'left');
		$this->EE->db->join('exp_forms f', 'f.form_id = fe.form_id', 'left');
		foreach ($visible_cols as $field_name => $field_id)
		{
			$this->EE->db->select("fe.fid_{$field_id} AS {$field_name}");
		}

		//----------------------------------------
		// Sort By
		//----------------------------------------
		$sort_cols = $this->EE->input->get_post('iSortingCols');

		for ($i = 0; $i < $sort_cols; $i++)
		{
			$col = $this->EE->input->get_post('iSortCol_'.$i);
			$sort =  $this->EE->input->get_post('sSortDir_'.$i);

			// Translate to column name
			$col = $cols[$col];

			switch ($col)
			{
				case 'fentry_id': // ID
					$this->EE->db->order_by('fe.fentry_id', $sort);
					break;
				case 'member': // Member
					$this->EE->db->order_by('mb.screen_name', $sort);
					break;
				case 'date': // Date
					$this->EE->db->order_by('fe.date', $sort);
					break;
				case 'country': // Country
					$this->EE->db->order_by('fe.country', $sort);
					break;
				case 'ip': // IP
					$this->EE->db->order_by('fe.ip_address', $sort);
					break;
				case (strpos($col, 'field_id_') !== FALSE): // FIELD ID
					if (isset($visible_cols[$col]) == FALSE) break; // Check if it's visible FIRST
					$this->EE->db->order_by($col, $sort);
					break;
				default:
					$this->EE->db->order_by('fe.date', 'DESC');
					break;
			}
		}

		if ($sort_cols == 0) $this->EE->db->order_by('fe.date', 'DESC');

		//----------------------------------------
		// Limit
		//----------------------------------------
		$limit = 10;
		if ($this->EE->input->get_post('iDisplayLength') !== FALSE)
		{
			$limit = $this->EE->input->get_post('iDisplayLength');
			if ($limit < 1) $limit = 999999;
		}

		//----------------------------------------
		// WHERE/LIKE
		//----------------------------------------
		$this->EE->db->where('fe.form_id', $form_id);
		if ($date_from) $this->EE->db->where('fe.date >', $date_from);
		if ($date_to) $this->EE->db->where('fe.date <', $date_to);
		if ($filter_country) $this->EE->db->where_in('fe.country', $filter_country);
		if ($filter_members) $this->EE->db->where_in('fe.member_id', $filter_members);

		//----------------------------------------
		// OFFSET & LIMIT & EXECUTE!
		//----------------------------------------
		$offset = 0;
		if ($this->EE->input->get_post('iDisplayStart') !== FALSE)
		{
			$offset = $this->EE->input->get_post('iDisplayStart');
		}

		$this->EE->db->limit($limit, $offset);
		$query = $this->EE->db->get();

		//----------------------------------------
		// Grab all fields!
		//----------------------------------------
		$fields = array();
		$q2 = $this->EE->db->select('*')->from('exp_forms_fields')->where('form_id', $form_id)->get();

		foreach ($q2->result_array() as $f)
		{
			$f['settings'] = @unserialize($f['field_settings']);
			$fields[ $f['field_id'] ] = $f;
		}

		//----------------------------------------
		// Loop Over all
		//----------------------------------------
		foreach ($query->result() as $row)
		{
			$trow = array();

			// Member Name
			if ($row->member_id == 0) $row->screen_name = $this->EE->lang->line('form:guest');

			$trow['DT_RowId'] = $row->fentry_id;
			$trow['id'] = $row->fentry_id;
			$trow['fentry_id'] = $row->fentry_id;
			$trow['member'] = $row->screen_name;
			$trow['date'] = $this->EE->forms_helper->formatDate('%d-%M-%Y %g:%i%A', $row->date);
			$trow['country'] = strtoupper($row->country);
			$trow['ip'] = long2ip($row->ip_address);

			/*
			// Loop over all fields!
			foreach ($cols as $field)
			{
				// Only for real fields! And fill with dummy data
				if (strpos($field, 'field_id_') !== FALSE)
				{
					$trow[$field] = '';
				}
			}
			*/

			/*
			// All visible
			foreach ($visible_cols as $field_name => $field_id)
			{
				$trow[$field_name] = $this->EE->formsfields[ $fields[$field_id]['field_type'] ]->output_data($fields[$field_id], $row->{$field_name}, 'line');
			}
			*/

			foreach ($cols as $field)
			{
				if (strpos($field, 'field_id_') !== FALSE)
				{
					$field_id = substr($field, 9);
					$field_name = 'fid_' . $field_id;
					$trow[$field] = $this->EE->formsfields[ $fields[$field_id]['field_type'] ]->output_data($fields[$field_id], $row->{$field_name}, 'line');
				}
			}

			// Add to data
			$data['aaData'][] = $trow;
		}

		//print_r($this->EE->db->queries);

		exit($this->EE->forms_helper->generate_json($data));
	}

	// ********************************************************************************* //

	public function export_entries()
	{
		@set_time_limit(0);
		@ini_set('memory_limit', '64M');
		@ini_set('memory_limit', '96M');
		@ini_set('memory_limit', '128M');
		@ini_set('memory_limit', '160M');
		@ini_set('memory_limit', '192M');
		@ini_set('memory_limit', '256M');
		@ini_set('memory_limit', '320M');
		@ini_set('memory_limit', '512M');

		//----------------------------------------
		// Vars
		//----------------------------------------
		$member_field = (isset($_POST['export']['member_info']) != FALSE) ? $_POST['export']['member_info'] : 'screen_name';

		//----------------------------------------
		// Get All Fields
		//----------------------------------------
		$dbfields = array();
		$this->EE->db->select('*');
		$this->EE->db->from('exp_forms_fields');
		$this->EE->db->where('form_id', $_POST['export']['form_id']);
		$this->EE->db->where_not_in('field_type', array('pagebreak', 'fieldset', 'columns_2', 'columns_3', 'columns_4', 'html') );
		$this->EE->db->order_by('field_order');
		$query = $this->EE->db->get();

		foreach ($query->result_array() as $row)
		{
			$row['settings'] = @unserialize($row['field_settings']);
			$dbfields[ $row['field_id'] ] = $row;
		}

		$query->free_result();

		//----------------------------------------
		// What Fields?
		//----------------------------------------
		$fields2export = array();
		if (isset($_POST['export']['fields']) == TRUE && $_POST['export']['fields'] == 'current')
		{
			$fields2export = $_POST['export']['visible_cols'];
		}
		else
		{
			$fields2export[] = 'fentry_id';
			$fields2export[] = 'member';
			$fields2export[] = 'date';
			$fields2export[] = 'country';
			$fields2export[] = 'ip';

			foreach($dbfields as $row)
			{
				$fields2export[] = 'field_id_' . $row['field_id'];
			}
		}

		//----------------------------------------
		// Real Query
		//----------------------------------------
		$this->EE->db->select("fe.*, mb.{$member_field}, f.form_title");
		$this->EE->db->from('exp_forms_entries fe');
		$this->EE->db->join('exp_members mb', 'mb.member_id = fe.member_id', 'left');
		$this->EE->db->join('exp_forms f', 'f.form_id = fe.form_id', 'left');
		$this->EE->db->where('f.form_id', $_POST['export']['form_id']);

		foreach ($fields2export as $key => $field)
		{
			if (strpos($field, 'field_id_') !== FALSE)
			{
				$field_id = substr($field, 9);
				$this->EE->db->select("fe.fid_{$field_id} AS {$field}");
			}
		}

		//----------------------------------------
		// Current Entries
		//----------------------------------------
		if ($_POST['export']['entries'] == 'current')
		{
			$this->EE->db->where_in('fe.fentry_id', $_POST['export']['current_entries']);
		}

		$query = $this->EE->db->get();

		//----------------------------------------
		// Columns!
		//----------------------------------------
		$columns = array();
		foreach ($fields2export as $efield)
		{
			if (strpos($efield, 'field_id_') !== FALSE)
			{
				$field_id = substr($efield, 9);
				$columns[] = $dbfields[$field_id]['title'];
			}
			else
			{
				$columns[] = $this->EE->lang->line('form:'.$efield);
			}

		}

		//----------------------------------------
		// Create Data Arrays
		//----------------------------------------
		$data = array();

		// Include Headers?
		if (isset($_POST['export']['include_header']) == TRUE && $_POST['export']['include_header'] == 'yes')
		{
			$data = array($columns);
		}

		foreach ($query->result() as $row)
		{
			$entry = array();

			// Loop over all visible rows
			foreach ($fields2export as $efield)
			{
				switch ($efield) {
					case 'fentry_id':
						$entry[] = $row->fentry_id;
						break;
					case 'member':
						if ($row->{$member_field} == FALSE)
						{
							switch ($member_field) {
								case 'member_id':
									$row->{$member_field} = 0;
									break;
								case 'username':
									$row->{$member_field} = strtoupper($this->EE->lang->line('form:guest'));
									break;
								case 'screen_name':
									$row->{$member_field} = strtoupper($this->EE->lang->line('form:guest'));
									break;

							}
						}
						$entry[] = $row->{$member_field};
						break;
					case 'date':
						$entry[] = $this->EE->forms_helper->formatDate('%Y-%m-%d %g:%i %A', $row->date);
						break;
					case 'country':
						$entry[] = $row->country;
						break;
					case 'ip':
						$entry[] = long2ip($row->ip_address);
						break;
					case (strpos($efield, 'field_id_') !== FALSE):
						$ff_id = substr($efield, 9);
						$entry[] = $this->EE->formsfields[ $dbfields[$ff_id]['field_type'] ]->output_data($dbfields[$ff_id], $row->$efield, 'text');
						break;
				}
			}

			$data[] = $entry;
		}

		//$query->free_result(); unset($query);



		//print_r($query->result());
		//print_r($_POST);
		//print_r($data);
		//print_r($columns);

		// -----------------------------------------
		// Temp Dir to run Actions
		// -----------------------------------------
		$temp_dir = APPPATH.'cache/devdemon_forms/';

		if (@is_dir($temp_dir) === FALSE)
   		{
   			@mkdir($temp_dir, 0777, true);
   			@chmod($temp_dir, 0777);
   		}

		// Last check, does the target dir exist, and is writable
		if (is_really_writable($temp_dir) !== TRUE)
		{
			exit($this->EE->output->show_user_error('general', 'TEMP PATH IS NOT WRITABLE! (EE_CACHE_DIR/devdemon_forms/)'));
		}

		// Temp File
		$filename = 'export_' . date('Ymd-Hi');

		//----------------------------------------
		// CSV ?
		//----------------------------------------
		if (isset($_POST['export']['format']) == FALSE OR $_POST['export']['format'] == 'csv')
		{
			$filename .= '.csv';
			$fp = fopen($temp_dir.$filename, 'w');

			//----------------------------------------
			// What delimiter
			//----------------------------------------
			$delimiter = ',';
			switch ($_POST['export']['delimiter']) {
				case 'comma':
					$delimiter = ',';
					break;
				case 'tab':
					$delimiter = "\t";
					break;
				case 'semicolon':
					$delimiter = ';';
					break;
				case 'pipe':
					$delimiter = '|';
					break;;
			}

			//----------------------------------------
			// What enclosure
			//----------------------------------------
			$enclosure = '"';
			switch ($_POST['export']['enclosure']) {
				case 'quote':
					$enclosure = '\'';
					break;
				case 'double_quote':
					$enclosure = '"';
					break;
			}

			foreach ($data as $entry)
			{
				fputcsv($fp, $entry, $delimiter, $enclosure);
			}

			fclose($fp);

			// Server
			$this->server_file_to_browser($temp_dir.$filename, $filename, 'text/csv');
		}

		//----------------------------------------
		// XLS?
		//----------------------------------------
		elseif ($_POST['export']['format'] == 'xls')
		{
			$filename .= '.xlsx';

			include PATH_THIRD .'forms/libraries/PHPExcel.php';
			include PATH_THIRD .'forms/libraries/PHPExcel/Writer/Excel2007.php';

			$objPHPExcel = new PHPExcel();
			//$objPHPExcel->getProperties()->setCreator("Maarten Balliauw");
			//$objPHPExcel->getProperties()->setLastModifiedBy("Maarten Balliauw");
			//$objPHPExcel->getProperties()->setTitle("Office 2007 XLSX Test Document");
			//$objPHPExcel->getProperties()->setSubject("Office 2007 XLSX Test Document");
			//$objPHPExcel->getProperties()->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.");

			$objPHPExcel->setActiveSheetIndex(0);


			foreach ($data as $row => $entry)
			{
				foreach ($entry as $col => $val)
				{
					$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $row+1, $val);
				}
			}

			$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
			$objWriter->save($temp_dir.$filename);

			// Server
			$this->server_file_to_browser($temp_dir.$filename, $filename, 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		}


	}

	// ********************************************************************************* //

	public function show_form_entry()
	{
		//----------------------------------------
		// Data
		//----------------------------------------
		$fentry_id = $this->EE->input->get_post('fentry_id');
		$vData = array();

		//----------------------------------------
		// Grab fentry
		//----------------------------------------
		$query = $this->EE->db->select('fe.*, mb.screen_name, mb.email')->from('exp_forms_entries fe')->join('exp_members mb', 'mb.member_id = fe.member_id', 'left')->where('fentry_id', $fentry_id)->get();

		if ($query->num_rows() == 0)
		{
			exit('FORM ENTRY NOT FOUND!');
		}

		$vData['fentry'] = $query->row_array();

		// Guest?
		if ($vData['fentry']['member_id'] == 0)
		{
			$vData['fentry']['screen_name'] = strtoupper($this->EE->lang->line('form:guest'));
			$vData['fentry']['email'] = '';
		}

		//----------------------------------------
		// Grab all Fields
		//----------------------------------------
		$this->EE->db->select('*');
		$this->EE->db->from('exp_forms_fields');
		$this->EE->db->where('form_id', $vData['fentry']['form_id']);
		$this->EE->db->where_not_in('field_type', array('pagebreak', 'fieldset', 'columns_2', 'columns_3', 'columns_4', 'html') );
		$this->EE->db->order_by('field_order', 'ASC');
		$query = $this->EE->db->get();
		foreach ($query->result_array() as $row)
		{
			$row['settings'] = @unserialize($row['field_settings']);
			$vData['dbfields'][] = $row;
		}

		$vData['dbfields'] = $this->EE->forms_helper->array_split($vData['dbfields'], 2);
		$vData['debug'] = @unserialize($vData['fentry']['debug_info']);
		if (is_array($vData['debug']) === false) $vData['debug'] = array();


		return $this->EE->load->view('mcp/view_form_entry', $vData, TRUE);

	}

	// ********************************************************************************* //

	public function choices_ajax_ui()
	{
		$vData = array();

		// Grab all lists
		$query = $this->EE->db->select('*')->from('exp_forms_lists')->order_by('list_label', 'ASC')->get();

		// Loop over lists
		foreach($query->result() as $row)
		{
			$vData['lists'][$row->list_label] = '';

			$row->list_data = unserialize($row->list_data);

			foreach ($row->list_data as $key => $val)
			{
				$vData['lists'][$row->list_label] .= ($key == $val) ? "{$val}\n": "{$key} : {$val}\n";
			}

			$vData['lists'][$row->list_label] = trim($vData['lists'][$row->list_label]);
		}


		exit($this->EE->load->view('form_builder/choices_ajax_ui', $vData, TRUE));
	}

	// ********************************************************************************* //

	public function delete_fentry()
	{
		foreach (explode('|', $_POST['entries']) as $fentry_id)
		{
			// Get Fentry
			$query = $this->EE->db->select('*')->from('exp_forms_entries')->where('fentry_id', $fentry_id)->get();

			if ($query->num_rows() > 0 )
			{
				$this->EE->db->where('fentry_id', $fentry_id);
				$this->EE->db->delete('exp_forms_entries');

				$this->EE->db->set('total_submissions', ' (total_submissions-1) ', FALSE);
				$this->EE->db->where('form_id', $query->row('form_id'));
				$this->EE->db->update('exp_forms');
			}
		}
	}

	// ********************************************************************************* //

	public function print_pdf_fentry()
	{
		// Module Settings
		$settings = $this->EE->forms_helper->grab_settings($this->site_id);
		$settings = $settings['print_pdf'];

		// get the entry ids
		$entries = explode('|', $this->EE->input->get('ids'));
		$first_entry = reset($entries);

		// Template TOP
		$html = $settings['template_top'];

		// PDF Printing
		require_once(PATH_THIRD."forms/libraries/dompdf/dompdf_config.inc.php");

		// Grab the FORM ID from the fentry
		$q = $this->EE->db->select('form_id')->from('exp_forms_entries')->where('fentry_id', $first_entry)->get();
		if ($q->num_rows() == 0)
		{
			exit('NO ENTRY FOUND!');
		}

		// Now lets grab the form
		$query = $this->EE->db->select('*')->from('exp_forms')->where('form_id', $q->row('form_id'))->get();
		if ($query->num_rows() == 0)
		{
			exit('NO FORM FOUND!');
		}

		$form = $query->row();

		$vars = array();
		$vars['{form:label}'] = $form->form_title;
		$vars['{date:usa}'] = $this->EE->forms_helper->formatDate('%m/%d/%Y', $this->EE->localize->now);
		$vars['{date:eu}'] = $this->EE->forms_helper->formatDate('%d/%m/%Y', $this->EE->localize->now);
		$vars['{datetime:usa}'] = $this->EE->forms_helper->formatDate('%m/%d/%Y %h:%i %A', $this->EE->localize->now);
		$vars['{datetime:eu}'] =  $this->EE->forms_helper->formatDate('%d/%m/%Y %H:%i', $this->EE->localize->now);
		$html = str_replace(array_keys($vars), array_values($vars), $html);

		// Get all fields!
		$fields = array();
		$query = $this->EE->db->select('*')->from('exp_forms_fields')->where('form_id', $form->form_id)->where_not_in('field_type', array('pagebreak', 'fieldset', 'columns_2', 'columns_3', 'columns_4', 'html') )->order_by('field_order', 'ASC')->get();
		foreach ($query->result_array() as $field)
		{
			$field['settings'] = @unserialize($field['field_settings']);
			$fields[ $field['field_id'] ] = $field;
		}

		// Grab all entries!
		$query = $this->EE->db->select('fe.*, mb.screen_name, mb.email')->from('exp_forms_entries fe')->join('exp_members mb', 'mb.member_id = fe.member_id', 'left')->where_in('fe.fentry_id', $entries)->order_by('date', 'ASC')->get();

		foreach ($query->result_array() as $entry)
		{
			$loop = $settings['template_loop'];

			// Guest?
			if ($entry['member_id'] == 0)
			{
				$entry['screen_name'] = strtoupper($this->EE->lang->line('form:guest'));
				$entry['email'] = '';
			}

			$vars = array();
			$vars['{form:label}'] = $form->form_title;
			$vars['{entry:datetime:usa}'] = $this->EE->forms_helper->formatDate('%m/%d/%Y %h:%i %A', $entry['date']);
			$vars['{entry:datetime:eu}'] = $this->EE->forms_helper->formatDate('%d/%m/%Y %H:%i', $entry['date']);
			$vars['{entry:country}'] =  strtoupper($entry['country']);
			$vars['{entry:member}'] =  $entry['screen_name'];
			$vars['{entry:ip_address}'] =  long2ip($entry['ip_address']);
			$loop = str_replace(array_keys($vars), array_values($vars), $loop);

			if (strpos($loop, '{form:fields}') !== FALSE)
			{
				// Grab the data between the pairs
				$tagdata = $this->EE->forms_helper->fetch_data_between_var_pairs('form:fields', $loop);

				$final = '';
				$count = 0;

				// Loop over all fields
				foreach ($fields as $field)
				{
					$row = '';
					$count++;

					// Create the VARS
					$vars = array();
					$vars['{field:label}'] = $field['title'];
					$vars['{field:short_name}'] = $field['url_title'];
					$vars['{field:value}'] = $this->EE->formsfields[ $field['field_type'] ]->output_data($field, $entry['fid_'.$field['field_id'] ], 'html');
					$vars['{field:count}'] = $count;

					// Parse them
					$row = str_replace(array_keys($vars), array_values($vars), $tagdata);

					$final .= $row;
				}

				// Replace the var pair!
				$loop = $this->EE->forms_helper->swap_var_pairs('form:fields', $final, $loop);
			}

			$loop .= '<hr/>';
			$html .= $loop;
		}

		$html .= "</body></html>";

		$dompdf = new DOMPDF();
		$dompdf->load_html($html);
		$dompdf->set_paper($settings['paper_size'], $settings['paper_orientation']);
		$dompdf->render();
		$dompdf->stream('Form Entry Print', array('Attachment' => false));
		exit();
	}

	// ********************************************************************************* //

	public function getDashboardForm()
	{
		// Page Title & BreadCumbs
		$this->vData['dashboard'] = TRUE;

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

		$this->vData['js_added'] = false;


		return $this->EE->load->view('mcp/view_form', $this->vData, TRUE);
	}

	// ********************************************************************************* //

	private function server_file_to_browser($path, $filename, $mime)
	{
		$filesize = @filesize($path);

		header('Pragma: public');
		header('Expires: 0');
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Cache-Control: public', FALSE);
		header('Content-Description: File Transfer');
		header('Content-Type: ' . $mime);
		header('Accept-Ranges: bytes');
		header('Content-Disposition: attachment; filename="' . $filename . '";');
		header('Content-Transfer-Encoding: binary');
		if ($filesize != FALSE) header('Content-Length: ' . $filesize);

		if (! $fh = fopen($path, 'rb'))
		{
			exit('COULD NOT OPEN FILE.');
		}

		while (!feof($fh))
		{
			@set_time_limit(0);
			print(fread($fh, 8192));
			flush();
		}
		fclose($fh);

		@unlink($path);

		exit();
	}

	// ********************************************************************************* //


} // END CLASS

/* End of file ajax.forms.php  */
/* Location: ./system/expressionengine/third_party/forms/ajax.forms.php */
