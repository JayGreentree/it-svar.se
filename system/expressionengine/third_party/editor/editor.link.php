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
class Link_ebtn extends Editor_button
{
	/**
	 * Button info - Required
	 *
	 * @access public
	 * @var array
	 */
	public $info = array(
		'name' 		=> 'Link',
		'author'	=> 'DevDemon',
		'author_url' => 'http://www.devdemon.com',
		'description'=> 'Link Button (with added features)',
		'version'	=> EDITOR_VERSION,
		'button_css'    => '',
        'button_css_hq' => '',
	);

	public $overrides_native = TRUE;
	public $dropdown = array();

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

		$this->dropdown['link']['title'] = 'Insert Link ...';
		$this->dropdown['link']['callback'] = 'EditorButtons.Link.OpenModal';
		$this->dropdown['unlink']['title'] = 'Unlink';
		$this->dropdown['unlink']['exec'] = 'unlink';
	}

	// ********************************************************************************* //

	public function display($settings=array())
	{
		// Let's load our CSS/JS
		// $this->css_js('js', 'url', EDITOR_THEME_URL.'editor_buttons.js?v='.EDITOR_VERSION, 'editor_buttons', 'main');
		// $this->css_js('css', 'url', EDITOR_THEME_URL.'editor_buttons.css?v='.EDITOR_VERSION, 'editor_buttons', 'main');
	}

	// ********************************************************************************* //
}

/* End of file editor.link.php */
/* Location: ./system/expressionengine/third_party/editor/editor.link.php */
