<?php if (!defined('BASEPATH')) die('No direct script access allowed');

// include config file
require_once dirname(dirname(__FILE__)).'/editor/config.php';

/**
 * Editor Module Control Panel Class
 *
 * @package			DevDemon_Editor
 * @author			DevDemon <http://www.devdemon.com> - Lead Developer @ Parscale Media
 * @copyright 		Copyright (c) 2007-2012 Parscale Media <http://www.parscale.com>
 * @license 		http://www.devdemon.com/license/
 * @link			http://www.devdemon.com/editor/
 * @see				http://expressionengine.com/user_guide/development/module_tutorial.html#control_panel_file
 */
class Editor_mcp
{

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
		$this->site_id = $this->EE->config->item('site_id');
		$this->EE->load->config('editor_config');
		$this->EE->load->library('editor_helper');
		$this->EE->load->helper('form');

		$this->settings = $this->EE->editor_helper->grab_settings();

		// Some Globals
		$this->base = BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=editor';
		$this->base_short = 'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=editor';

		$this->EE->cp->set_breadcrumb($this->base, $this->EE->lang->line('editor'));

		// Add JS & CSS
        $this->EE->editor_helper->addMcpAssets('gjs');
        $this->EE->editor_helper->addMcpAssets('css', 'redactor/redactor.css?v='.EDITOR_VERSION, 'redactor', 'main');
        $this->EE->editor_helper->addMcpAssets('css', 'global.css?v='.EDITOR_VERSION, 'editor', 'global');
        $this->EE->editor_helper->addMcpAssets('js', 'editor_settings.js?v='.EDITOR_VERSION, 'editor', 'settings');


		$this->EE->cp->add_js_script(array('ui' => array('sortable')));

		$this->EE->load->library('javascript');
		$this->EE->javascript->output('Editor.Init();');

		if (function_exists('ee')) {
            ee()->view->cp_page_title = $this->EE->lang->line('ed:editor_mcp');
        } else {
            $this->EE->cp->set_variable('cp_page_title', $this->EE->lang->line('ed:editor_mcp'));
        }
	}

	// ********************************************************************************* //

	public function index()
	{
		$data = $this->_global_vars();
		$data['section'] = 'editors';

		$data['editors'] = array();

		$query = $this->EE->db->select('config_id, config_label')->from('exp_editor_configs')->where('site_id', $this->site_id)->order_by('config_label', 'ASC')->get();
		$data['editors'] = $query->result();

		return $this->EE->load->view('mcp/editors', $data, TRUE);
	}

	// ********************************************************************************* //

	public function new_configuration()
	{
		$data = $this->_global_vars();
		$data['section'] = 'editors';

		$fields = $this->EE->db->list_fields('exp_editor_configs');

		foreach ($fields as $name) $data[$name] = '';
		$data['field_name'] = 'editor';

		$settings = array();

		if ($this->EE->input->get('config_id') > 0)
		{
			$query = $this->EE->db->select('*')->from('exp_editor_configs')->where('config_id', $this->EE->input->get('config_id'))->get();
			if ($query->num_rows() > 0)
			{
				$data['config_id'] = $query->row('config_id');
				$data['config_label'] = $query->row('config_label');
				$settings = unserialize(base64_decode($query->row('config_settings')));
			}
		}

		// -----------------------------------------
		// Settings
		// -----------------------------------------
		$settings = $this->EE->editor_helper->parse_editor_settings($settings);
		$data = array_merge($data, $settings);

		// Are there any config overrides
		$data['config_override'] = $this->EE->config->item('editor');

		// Just to be sure
		if (isset($data['allowedtags']) === FALSE || is_array($data['allowedtags']) === FALSE) $data['allowedtags'] = array();

		// Get all buttons
		$data['all_buttons'] = $this->EE->editor_helper->get_editor_buttons();

		$data['locations'] = array(0 => $this->EE->lang->line('ed:disabled'));
		$locs = $this->EE->editor_helper->get_upload_preferences();
		foreach ($locs as $loc) $data['locations'][ $loc['id'] ] = $loc['name'];

		// URL
		$data['act_url'] = $this->EE->editor_helper->getRouterUrl('url', 'ACT_file_upload');

		$this->EE->cp->add_to_head("<style type='text/css'>
			{$this->EE->editor->buttons_css}

			@media all and (-webkit-min-device-pixel-ratio: 1.5) {
    			{$this->EE->editor->buttons_css_hq}
			}
		</style>
		");

		return $this->EE->load->view('mcp/create_config', $data, TRUE);
	}

	// ********************************************************************************* //

	public function update_configuration()
	{
		//----------------------------------------
		// Create/Updating?
		//----------------------------------------
		if ($this->EE->input->get('delete') != 'yes')
		{
			// -----------------------------------------
			// Parse allowed_tags
			// -----------------------------------------
			$allowedtags = array();
			foreach (explode(',', $_POST['editor']['allowedtags']) as $cat)
			{
				$cat = trim($cat);
				if ($cat != FALSE) $allowedtags[] = $cat;
			}

			$_POST['editor']['allowedtags'] = $allowedtags;

			$this->EE->db->set('config_label', $this->EE->input->post('config_label'));
			$this->EE->db->set('site_id', $this->site_id);
			$this->EE->db->set('config_settings', base64_encode( serialize($_POST['editor']) ) );

			// -----------------------------------------
			// Update Or Insert
			// -----------------------------------------
			if ($this->EE->input->post('config_id') > 0)
			{
				$this->EE->db->where('config_id', $this->EE->input->post('config_id'));
				$this->EE->db->update('exp_editor_configs');
			}
			else
			{
				$this->EE->db->insert('exp_editor_configs');
			}

		}

		//----------------------------------------
		// Delete
		//----------------------------------------
		else
		{
			$this->EE->db->where('config_id', $this->EE->input->get('config_id'));
			$this->EE->db->delete('exp_editor_configs');
		}

		$this->EE->functions->redirect($this->base . '&method=index');
	}

	// ********************************************************************************* //

	public function clone_configuration()
	{
		$query = $this->EE->db->select('*')->from('exp_editor_configs')->where('config_id', $this->EE->input->get('config_id'))->get();
		if ($query->num_rows() > 0)
		{
			$this->EE->db->set('config_label', $query->row('config_label') . ' DUPE');
			$this->EE->db->set('config_settings', $query->row('config_settings'));
			$this->EE->db->insert('exp_editor_configs');
		}

		$this->EE->functions->redirect($this->base . '&method=index');
	}

	// ********************************************************************************* //

	public function categories()
	{
		$data = $this->_global_vars();
		$data['section'] = 'categories';


		// -----------------------------------------
		// Settings
		// -----------------------------------------
		$settings = $this->EE->editor_helper->grab_extension_settings($this->site_id);
		$settings = $this->EE->editor_helper->parse_editor_settings($settings);
		$data = array_merge($data, $settings);

		$data['editors_confs'] = array();
		$query = $this->EE->db->select('config_id, config_label')->from('exp_editor_configs')->where('site_id', $this->site_id)->order_by('config_label', 'ASC')->get();
		foreach($query->result() as $row)
		{
			$data['editors_confs'][$row->config_id] = $row->config_label;
		}

		// Are there any config overrides
		$data['config_override'] = $this->EE->config->item('editor');

		// Just to be sure
		if (isset($data['allowedtags']) === FALSE || is_array($data['allowedtags']) === FALSE) $data['allowedtags'] = array();

		// Get all buttons
		$data['all_buttons'] = $this->EE->editor_helper->get_editor_buttons();

		$data['locations'] = array(0 => $this->EE->lang->line('ed:disabled'));
		$locs = $this->EE->editor_helper->get_upload_preferences();
		foreach ($locs as $loc) $data['locations'][ $loc['id'] ] = $loc['name'];

		// URL
		$data['act_url'] = $this->EE->editor_helper->getRouterUrl('url', 'ACT_file_upload');

		$data['field_name'] = 'editor';

		$this->EE->cp->add_to_head("<style type='text/css'>
			{$this->EE->editor->buttons_css}

			@media all and (-webkit-min-device-pixel-ratio: 1.5) {
    			{$this->EE->editor->buttons_css_hq}
			}
		</style>
		");

		return $this->EE->load->view('mcp/categories', $data, TRUE);
	}

	// ********************************************************************************* //

	public function update_categories()
	{
		// Grab Settings
		$settings =$this->EE->editor_helper->grab_extension_settings($this->site_id);
		$settings['site:'.$this->site_id] = $this->EE->input->post('editor');

		// Put it Back
		$this->EE->db->set('settings', serialize($settings));
		$this->EE->db->where('class', 'Editor_ext');
		$this->EE->db->update('exp_extensions');

		$this->EE->functions->redirect($this->base . '&method=index');
	}

	// ********************************************************************************* //

	public function buttons()
	{
		$data = $this->_global_vars();
		$data['section'] = 'buttons';

		return $this->EE->load->view('mcp/buttons', $data, TRUE);
	}

	// ********************************************************************************* //

	public function buttons_settings()
	{
		$data = $this->_global_vars();
		$data['section'] = 'buttons';
		$data['btn'] = FALSE;
		$data['btn_class'] = FALSE;

		$class = $this->EE->input->get('class');

		$this->EE->db->select('button_settings');
		$this->EE->db->from('exp_editor_buttons');
		$this->EE->db->where('button_class', $class);
		$this->EE->db->where('config_id', 0);
		$this->EE->db->where('lowvar_id', 0);
		$this->EE->db->where('matrixcol_id', 0);
		$query = $this->EE->db->get();

		$settings = array();
		if ($query->num_rows() > 0)
		{
			$settings = @unserialize($query->row('button_settings'));
			if (is_array($settings) === FALSE) $settings = array();
		}

		if (isset($this->EE->editor->buttons[$class]) != FALSE)
		{
			$this->EE->editor->buttons[$class]->settings = $settings;
			$data['btn'] = $this->EE->editor->buttons[$class];
			$data['btn_class'] = $class;
		}

		return $this->EE->load->view('mcp/buttons_settings', $data, TRUE);
	}

	// ********************************************************************************* //

	public function buttons_save()
	{
		$class = $this->EE->input->get_post('class');
		if (isset($this->EE->editor->buttons[$class]) != FALSE)
		{
			$settings = $this->EE->input->get_post('settings');
			if (empty($settings) == TRUE) $settings = array();

			$settings = $this->EE->editor->buttons[$class]->save_settings($settings);

			$id = 0;
			$this->EE->db->select('id');
			$this->EE->db->from('exp_editor_buttons');
			$this->EE->db->where('button_class', $class);
			$this->EE->db->where('config_id', 0);
			$this->EE->db->where('lowvar_id', 0);
			$this->EE->db->where('matrixcol_id', 0);
			$query = $this->EE->db->get();

			if ($query->num_rows() > 0)
			{
				$id = $query->row('id');
			}

			$this->EE->db->set('site_id', $this->site_id);
			$this->EE->db->set('button_class', $class);
			$this->EE->db->set('config_id', 0);
			$this->EE->db->set('lowvar_id', 0);
			$this->EE->db->set('matrixcol_id', 0);
			$this->EE->db->set('button_settings', serialize($settings));

			//exit(print_r($settings));

			if ($id > 0)
			{
				$this->EE->db->where('id', $id);
				$this->EE->db->update('exp_editor_buttons');
			}
			else
			{
				$this->EE->db->insert('exp_editor_buttons');
			}
		}

		$this->EE->functions->redirect($this->base . '&method=buttons');
	}

	// ********************************************************************************* //

	private function _global_vars($data=array())
	{
		$data['base_url'] = $this->base;
		$data['base_url_short'] = $this->base_short;

		return $data;
	}

	// ********************************************************************************* //


} // END CLASS

/* End of file mcp.updater.php */
/* Location: ./system/expressionengine/third_party/updater/mcp.updater.php */
