<?php if (!defined('BASEPATH')) die('No direct script access allowed');

// include config file
require_once dirname(dirname(__FILE__)).'/editor/config.php';

/**
 * Editor Module FieldType
 *
 * @package			DevDemon_editor
 * @author			DevDemon <http://www.devdemon.com> - Lead Developer @ Parscale Media
 * @copyright 		Copyright (c) 2007-2011 Parscale Media <http://www.parscale.com>
 * @license 		http://www.devdemon.com/license/
 * @link			http://www.devdemon.com
 * @see				http://expressionengine.com/user_guide/development/fieldtypes.html
 */
class Editor_ft extends EE_Fieldtype
{

	/**
	 * Field info - Required
	 *
	 * @access public
	 * @var array
	 */
	public $info = array(
		'name' 		=> EDITOR_NAME,
		'version'	=> EDITOR_VERSION,
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

		if ($this->EE->input->cookie('cp_last_site_id')) $this->site_id = $this->EE->input->cookie('cp_last_site_id');
		else if ($this->EE->input->get_post('site_id')) $this->site_id = $this->EE->input->get_post('site_id');
		else $this->site_id = $this->EE->config->item('site_id');

		$this->EE->load->add_package_path(PATH_THIRD . 'editor/');
		$this->EE->lang->loadfile('editor');

		$this->EE->load->library('editor_helper');

		$this->EE->config->load('editor_config');
		$this->cache = array();
	}

	// ********************************************************************************* //

	/**
	 * Display the field in the publish form
	 *
	 * @access public
	 * @param $data String Contains the current field data. Blank for new entries.
	 * @return String The custom field HTML
	 *
	 */
	public function display_field($data)
	{
		// -----------------------------------------
		// Settings
		// -----------------------------------------
		if (isset($this->custom_settings) == TRUE)
		{
			$settings = $this->custom_settings;
		}
		else
		{
			$settings = $this->EE->editor_helper->parse_editor_settings($this->settings['editor']);
		}

		//----------------------------------------
		// Add Global JS & CSS & JS Scripts
		//----------------------------------------
        $this->EE->editor_helper->addMcpAssets('gjs');
        $this->EE->editor_helper->addMcpAssets('css', 'redactor/redactor.css?v='.EDITOR_VERSION, 'redactor', 'main');
        $this->EE->editor_helper->addMcpAssets('css', 'editor.css?v='.EDITOR_VERSION, 'editor', 'main');
        $this->EE->editor_helper->addMcpAssets('css', 'editor_buttons.css?v='.EDITOR_VERSION, 'editor', 'buttons');
        $this->EE->editor_helper->addMcpAssets('js', 'redactor/redactor.min.js?v='.EDITOR_VERSION, 'redactor', 'main');
        $this->EE->editor_helper->addMcpAssets('js', 'editor_helper.js?v='.EDITOR_VERSION, 'editor', 'helper');
        $this->EE->editor_helper->addMcpAssets('js', 'editor_buttons.js?v='.EDITOR_VERSION, 'editor', 'buttons');

		// Do we need to load an additional Language?
		if (isset($settings['language']) === TRUE && $settings['language'] != 'en')
		{
			$this->EE->editor_helper->addMcpAssets('js', 'redactor/lang/'.$settings['language'].'.js?v='.EDITOR_VERSION, 'redactor', 'lang');
		}

		$textarea_id = $this->field_name;
		$textarea_id = str_replace(array('[', ']'), array('_', ''), $textarea_id);

		if (REQ == 'PAGE')
		{
			$data = form_prep($data, $this->field_name);
		}

		$data = $this->_parse_variables($data, TRUE);

		$html = $this->EE->editor_helper->get_editor_button_html();
		$css_js = $this->EE->editor_helper->get_editor_css_js($settings, $textarea_id);
		$this->EE->cp->add_to_foot($css_js.$html);

		return "
		<div class='editor'>
			<textarea id='{$textarea_id}' name='{$this->field_name}' class='redactor_editor'>{$data}</textarea>
		</div>
		";
	}

	// ********************************************************************************* //

	/**
	 * Validates the field input
	 *
	 * @param $data Contains the submitted field data.
	 * @return mixed Must return TRUE or an error message
	 */
	public function validate($data)
	{
		// Is this a required field?
		if ($this->settings['field_required'] == 'y' && ! $data)
		{
			return lang('required');
		}

		return TRUE;
	}

	// ********************************************************************************* //

	public function validate_cell($data)
	{
		// is this a required cell?
		if ($this->settings['col_required'] == 'y' && ! $data)
		{
			return lang('col_required');
		}

		return TRUE;
	}

	// ********************************************************************************* //

	/**
	 * Preps the data for saving
	 *
	 * @param $data Contains the submitted field data.
	 * @return string Data to be saved
	 */
	public function save($data)
	{
		$data = trim($data);

		// Clear out if just whitespace
		if (! $data || preg_match('/^\s*(<\w+>\s*(&nbsp;)*\s*<\/\w+>|<br \/>)?\s*$/s', $data))
		{
			return '';
		}

		// Entitize curly braces within codeblocks
		$data = preg_replace_callback('/<code>(.*?)<\/code>/s',
			create_function('$matches',
				'return str_replace(array("{","}"), array("&#123;","&#125;"), $matches[0]);'
			),
			$data
		);

		// Remove empty at the end
		for ($i=0; $i < 20; $i++) {
			$data = preg_replace('/<p><br><\/p><p><br><\/p>$/s', '', $data);
		}

		// Just in case, lets remove the last one
		$data = preg_replace('/<p><br><\/p>$/s', '', $data);

		// Remove Firebug 1.5.2+ div
		$data = preg_replace('/<div firebugversion=(.|\t|\n|\s)*<\\/div>/', '', $data);

		// Cursor Resize!
		$data = preg_replace('/cursor\:.*nw-resize\;/', '', $data);

		$data = $this->_parse_variables($data, FALSE);

		// Does it contain emptyness?
		if ($data == '<p><br></p>') $data = '';

		return $data;
	}

	// ********************************************************************************* //

	public function pre_process($data)
	{
		$this->EE->load->library('typography');

		$tmp_encode_email = $this->EE->typography->encode_email;
		$this->EE->typography->encode_email = FALSE;

		$tmp_convert_curly = $this->EE->typography->convert_curly;
		$this->EE->typography->convert_curly = FALSE;

		$data = $this->EE->typography->parse_type($data, array(
			'text_format'   => 'none',
			'html_format'   => 'all',
			'auto_links'    => (isset($this->row['channel_auto_link_urls']) ? $this->row['channel_auto_link_urls'] : 'n'),
			'allow_img_url' => (isset($this->row['channel_allow_img_urls']) ? $this->row['channel_allow_img_urls'] : 'y')
		));

		$this->EE->typography->encode_email = $tmp_encode_email;
		$this->EE->typography->convert_curly = $tmp_convert_curly;

		// use normal quotes
		$data = str_replace('&quot;', '"', $data);

		$data = $this->_parse_variables($data, TRUE);

		return $data;
	}

	// ********************************************************************************* //

	private function _display_settings($settings=array())
	{
		// -----------------------------------------
		// Settings
		// -----------------------------------------
		if (isset($settings['editor']) === TRUE)
		{
			$settings = $settings['editor'];
		}

		$data = $this->EE->editor_helper->parse_editor_settings($settings);

		// -----------------------------------------
		// Add JS & CSS
		// -----------------------------------------
        $this->EE->editor_helper->addMcpAssets('gjs');
        $this->EE->editor_helper->addMcpAssets('css', 'redactor/redactor.css?v='.EDITOR_VERSION, 'redactor', 'main');
        $this->EE->editor_helper->addMcpAssets('css', 'global.css?v='.EDITOR_VERSION, 'editor', 'global');
        $this->EE->editor_helper->addMcpAssets('js', 'editor_settings.js?v='.EDITOR_VERSION, 'editor', 'settings');

		$this->EE->cp->add_js_script(array('ui' => array('sortable')));

		$this->EE->load->library('javascript');
		$this->EE->javascript->output('Editor.Init();');

		$this->EE->cp->add_to_head("<style type='text/css'>
			{$this->EE->editor->buttons_css}

			@media all and (-webkit-min-device-pixel-ratio: 1.5) {
    			{$this->EE->editor->buttons_css_hq}
			}
		</style>
		");

		// Just to be sure
		if (isset($data['allowedtags']) === FALSE || is_array($data['allowedtags']) === FALSE) $data['allowedtags'] = array();

		$data['field_name'] = 'editor';

		// Get all buttons
		$data['all_buttons'] = $this->EE->editor_helper->get_editor_buttons();

		$data['editors_confs'] = array();
		$query = $this->EE->db->select('config_id, config_label')->from('exp_editor_configs')->where('site_id', $this->site_id)->order_by('config_label', 'ASC')->get();
		foreach($query->result() as $row)
		{
			$data['editors_confs'][$row->config_id] = $row->config_label;
		}

		$data['locations'] = array(0 => $this->EE->lang->line('ed:disabled'));
		$locs = $this->EE->editor_helper->get_upload_preferences();
		foreach ($locs as $loc) $data['locations'][ $loc['id'] ] = $loc['name'];

		// URL
		$data['act_url'] = $this->EE->editor_helper->getRouterUrl('url', 'ACT_file_upload');

		// Are there any config overrides
		$data['config_override'] = $this->EE->config->item('editor');

		// -----------------------------------------
		// Display Row
		// -----------------------------------------
		$row = $this->EE->load->view('editor_settings', $data, TRUE);

		return $row;
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
	public function display_settings($settings=array())
	{
		$row = $this->_display_settings($settings);
		$this->EE->table->add_row(array('data' => $row, 'colspan' => 2));
	}

	// ********************************************************************************* //

	public function display_var_settings($settings=array())
	{
		$row = $this->_display_settings($settings);
		return array( array($this->EE->lang->line('ed:editor_settings'), $row) );
	}

	// ********************************************************************************* //

	public function display_cell_settings($settings=array())
	{
		$row = $this->_display_settings($settings);
		return array( array('', $row) );
	}

	// ********************************************************************************* //

	private function _save_settings($settings=array(), $matrix=FALSE)
	{
		if (isset($_POST['editor']) === TRUE && $matrix == FALSE)
		{
			$settings['editor'] = $_POST['editor'];
		}

		// -----------------------------------------
		// Parse allowed_tags
		// -----------------------------------------
		$allowedtags = array();
		foreach (explode(',', $settings['editor']['allowedtags']) as $cat)
		{
			$cat = trim($cat);
			if ($cat != FALSE) $allowedtags[] = $cat;
		}

		$settings['editor']['allowedtags'] = $allowedtags;



		if (isset($settings['editor']['convert_field']) === TRUE && $settings['editor']['convert_field'] != 'none')
		{
			$field_id = $this->EE->input->post('field_id');

			if ($field_id && $settings['editor']['convert_field'])
			{
				$this->EE->db->select('entry_id, field_id_'.$field_id.' data, field_ft_'.$field_id.' format');
				$query = $this->EE->db->get_where('channel_data', 'field_id_'.$field_id.' != ""');

				if ($query->num_rows())
				{
					// prepare Typography
					$this->EE->load->library('typography');
					$this->EE->typography->initialize();

					foreach ($query->result_array() as $row)
					{
						$data = $row['data'];

						$convert = FALSE;

						// Auto <br /> and XHTML
						switch ($row['format'])
						{
							case 'br':
								$convert = TRUE;
								$data = $this->EE->typography->nl2br_except_pre($data);
								break;
							case 'xhtml':
								$convert = TRUE;
								$data = $this->EE->typography->auto_typography($data);
								break;
						}

						// Save the new field data
						if ($convert)
						{
							$this->EE->db->query($this->EE->db->update_string('exp_channel_data',
								array(
									'field_id_'.$field_id => $data,
									'field_ft_'.$field_id => 'none'
								),
								'entry_id = '.$row['entry_id']
							));
						}
					}
				}
			}
		}

		unset($settings['editor']['convert_field']);

		//exit(print_r($settings));

		return $settings;
	}

	// ********************************************************************************* //

	/**
	 * Save the fieldtype settings.
	 *
	 * @param $data array Contains the submitted settings for this field.
	 * @access public
	 * @return array
	 */
	public function save_settings($settings=array())
	{
		return $this->_save_settings($settings);
	}

	// ********************************************************************************* //

	public function save_var_settings($settings=array())
	{
		return $this->_save_settings($settings);
	}

	// ********************************************************************************* //

	public function save_cell_settings($settings=array())
	{
		return $this->_save_settings($settings, TRUE);
	}

	// ********************************************************************************* //

	/**
	 * Replace Tag - Replace the field tag on the frontend.
	 *
	 * @param  mixed   $data    contains the field data (or prepped data, if using pre_process)
	 * @param  array   $params  contains field parameters (if any)
	 * @param  boolean $tagdata contains data between tag (for tag pairs)
	 * @return string           template data
	 */
	public function replace_tag($data, $params=array(), $tagdata = FALSE)
	{

		return $data;
	}

	// ********************************************************************************* //

	/**
	 * Display Variable Tag
	 */
	public function display_var_tag($data)
	{
		return $this->replace_tag($this->pre_process($data));
	}

	// ********************************************************************************* //

	public function display_var_field($data)
	{
		// -----------------------------------------
		// Settings
		// -----------------------------------------
		if (isset($this->settings['editor']) === FALSE) $this->settings = array('editor' => array());
		$settings = $this->EE->editor_helper->parse_editor_settings($this->settings['editor']);
		$this->custom_settings = $settings;

		/*
		if (isset($this->settings['editor']) === FALSE) $this->settings = array('editor' => array());
		$settings = $this->EE->editor_helper->array_extend($settings, $this->settings['editor']);
		 */

		return $this->display_field($data);
	}

	// ********************************************************************************* //

	public function display_cell($data)
	{
		// -----------------------------------------
		// Settings
		// -----------------------------------------
		if (isset($this->settings['editor']) === FALSE) $this->settings = array('editor' => array());
		$settings = $this->EE->editor_helper->parse_editor_settings($this->settings['editor']);

		//----------------------------------------
		// Add Global JS & CSS & JS Scripts
		//----------------------------------------
		$this->EE->editor_helper->addMcpAssets('gjs');
        $this->EE->editor_helper->addMcpAssets('css', 'redactor/redactor.css?v='.EDITOR_VERSION, 'redactor', 'main');
        $this->EE->editor_helper->addMcpAssets('css', 'editor.css?v='.EDITOR_VERSION, 'editor', 'main');
        $this->EE->editor_helper->addMcpAssets('css', 'editor_buttons.css?v='.EDITOR_VERSION, 'editor', 'buttons');
        $this->EE->editor_helper->addMcpAssets('js', 'redactor/redactor.min.js?v='.EDITOR_VERSION, 'redactor', 'main');
        $this->EE->editor_helper->addMcpAssets('js', 'editor_helper.js?v='.EDITOR_VERSION, 'editor', 'helper');
        $this->EE->editor_helper->addMcpAssets('js', 'editor_buttons.js?v='.EDITOR_VERSION, 'editor', 'buttons');

		// Do we need to load an additional Language?
		if (isset($settings['language']) === TRUE && $settings['language'] != 'en')
		{
			$this->EE->editor_helper->addMcpAssets('js', 'redactor/lang/'.$settings['language'].'.js?v='.EDITOR_VERSION, 'redactor', 'lang');
		}

		$data = $this->_parse_variables($data, TRUE);

		$css_js = $this->EE->editor_helper->get_editor_css_js($settings, $this->cell_name);
		$json = $this->EE->editor_helper->get_editor_json($settings);
		$this->EE->cp->add_to_foot($css_js);
		$this->EE->cp->add_to_foot("<script type='text/javascript'>Editor.matrixColConfigs.col_id_{$this->col_id} = {$json};</script>");

		return "
		<div class='editor'>
			<textarea name='{$this->cell_name}' class='redactor_editor'>{$data}</textarea>
		</div>
		";
	}

	// ********************************************************************************* //

	private function _parse_variables($data='', $var_to_val=TRUE)
	{
		if ($var_to_val)
		{
			$data = $this->_parse_file_variables($data);
			$data = $this->_parse_page_variables($data);
		}
		else
		{
			$data = $this->_parse_file_urls($data);
			$data = $this->_parse_page_urls($data);
		}

		return $data;
	}

	// ********************************************************************************* //

	private function _parse_file_variables($data='')
	{
		if (strpos($data, LD.'filedir_') !== FALSE)
		{
			$vars = $this->_fetch_file_variables();

			foreach ($vars as $variable => $url)
			{
				$data = str_replace($variable, $url, $data);
			}
		}

		return $data;
	}

	// ********************************************************************************* //

	private function _parse_file_urls($data='')
	{
		$vars = $this->_fetch_file_variables();

		foreach ($vars as $variable => $url)
		{
			$data = str_replace($url, $variable, $data);
		}

		return $data;
	}

	// ********************************************************************************* //

	private function _fetch_file_variables($sort=FALSE)
	{
		if (! isset($this->cache['file_variables']))
		{
			$this->cache['file_variables'] = array();
			$file_paths = $this->EE->functions->fetch_file_paths();

			foreach ($file_paths as $id => $url)
			{
				// ignore "/" URLs
				if ($url == '/') continue;

				$this->cache['file_variables'][LD.'filedir_'.$id.RD] = $url;
			}
		}

		return $this->cache['file_variables'];
	}

	// ********************************************************************************* //

	private function _parse_page_variables($data='')
	{
		if (strpos($data, LD.'page_') !== FALSE)
		{
			$this->EE->editor_helper->pages_get();

			foreach ($this->EE->cache['editor']['pages_urls'] as $entry_id => $url)
			{
				$data = str_replace(LD.'page_'.$entry_id.RD, $url, $data);
			}
		}

		return $data;
	}

	// ********************************************************************************* //

	private function _parse_page_urls($data='')
	{
		$this->EE->editor_helper->pages_get();
		arsort($this->EE->cache['editor']['pages_urls']);

		foreach ($this->EE->cache['editor']['pages_urls'] as $entry_id => $url)
		{
			$data = str_replace($url, LD.'page_'.$entry_id.RD, $data);
		}

		return $data;
	}

	// ********************************************************************************* //

}

/* End of file ft.editor.php */
/* Location: ./system/expressionengine/third_party/editor/ft.editor.php */
