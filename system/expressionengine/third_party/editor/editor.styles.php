<?php if (!defined('BASEPATH')) die('No direct script access allowed');

// include config file
require_once dirname(dirname(__FILE__)).'/editor/config.php';

/**
 * Link Button for Editor
 *
 * @package			DevDemon_Editor
 * @author			DevDemon <http://www.devdemon.com> - Lead Developer @ Parscale Media
 * @copyright 		Copyright (c) 2007-2011 Parscale Media <http://www.parscale.com>
 * @license 		http://www.devdemon.com/license/
 * @link			http://www.devdemon.com/editor/
 */
class Styles_ebtn extends Editor_button
{
	/**
	 * Button info - Required
	 *
	 * @access public
	 * @var array
	 */
	public $info = array(
		'name' 		=> 'Styles',
		'author'	=> 'DevDemon',
		'author_url' => 'http://www.devdemon.com',
		'description'=> 'Styles dropdown',
		'version'	=> EDITOR_VERSION,
		'settings'	=> TRUE,
		'callback'	=> 'EditorButtons.Styles.OpenDropDown',
		'button_css'    => 'background-position:3px 7px; background-image: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAABmJLR0QA/wD/AP+gvaeTAAABW0lEQVR42qVTu47CMBCcrN1QgAR/kIaCghJBvoMvQNTX0dFRUvJJSFGKwF9ESBBCBElQeGiuIbmEi0DcjTSSbXlnx7tr4AcjAPyQoyx4AICfAgCNhwAzpc1mgyIMw8gpIqV9s9mEPO5ZAGDbNlzXzYNFpMRMJCMAVDp4lbV4Vq/XfztYrVZ5hsVigW63i3a7DcuysFwuS4JF5JXdbrf0fZ/7/Z7r9Zqe5zEMQ87nc5qmySiKGMcxkyQhAOqCAztz0Ov1ICJI0xTj8Riu60Iphd1u995BEAQ8HA70PI+tVouz2Yy+79NxHIoIz+cz0zTl5XIhAFZ2QUQQhiGCIMBwOEStVoPjOHlnskJWOjgejzydToyiiJPJhI1Gg6ZpcjqdstPp8Hq98na78X6/lwZpkNUgSRL0+/2X7SvxeQ7iOH47A8UprayB1hpaayiloJTK18/vfsZffuMX/otvyYkGOKTnByIAAAAASUVORK5CYII=);',
        'button_css_hq' => '',
	);


	/**
	 * Constructor
	 *
	 * @access public
	 *
	 * Calls the parent constructor
	 */
	public function __construct()
	{
		parent::__construct();
		$this->EE->load->add_package_path(PATH_THIRD . 'editor/');
	}

	// ********************************************************************************* //

	public function display($settings=array())
	{
		// Let's load our CSS/JS
		// $this->css_js('js', 'url', EDITOR_THEME_URL.'editor_buttons.js?v='.EDITOR_VERSION, 'editor_buttons', 'main');
		// $this->css_js('css', 'url', EDITOR_THEME_URL.'editor_buttons.css?v='.EDITOR_VERSION, 'editor_buttons', 'main');
	}

	// ********************************************************************************* //

	public function display_settings($settings=array())
	{
		$data = $settings;
		if (isset($data['styles']) === FALSE) $data['styles'] = array();

		// We added custom_type later on
		foreach ($data['styles'] as &$style)
		{
			if (isset($style['custom_type']) === FALSE) $style['custom_type'] = 'div';
		}

		// Load our css/js
        $this->EE->editor_helper->addMcpAssets('js', 'editor_buttons_settings.js?v='.EDITOR_VERSION, 'editor_buttons', 'settings');

		return $this->EE->load->view('btn/settings_styles', $data, TRUE);
	}

	// ********************************************************************************* //

	public function save_settings($settings=array())
	{
		if (isset($settings['styles']) === FALSE || is_array($settings['styles']) === FALSE) return array();

		$data = array();

		foreach ($settings['styles'] as $style)
		{
			if (isset($style['title']) === FALSE || $style['title'] == FALSE) continue;
			if (isset($style['type']) === FALSE || $style['type'] == FALSE) continue;

			$data['styles'][] = $style;
		}

		return $data;
	}

	// ********************************************************************************* //
}

/* End of file editor.styles.php */
/* Location: ./system/expressionengine/third_party/editor/editor.styles.php */
