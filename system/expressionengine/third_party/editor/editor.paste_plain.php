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
class Paste_plain_ebtn extends Editor_button
{
	/**
	 * Button info - Required
	 *
	 * @access public
	 * @var array
	 */
	public $info = array(
		'name' 		=> 'Paste Plain Text',
		'author'	=> 'DevDemon',
		'author_url' => 'http://www.devdemon.com',
		'description'=> 'Paste any content and all markup will be stripped',
		'version'	=> EDITOR_VERSION,
		'callback'	=> 'EditorButtons.PastePlainText.OpenModal',
		'button_css'    => 'background-position:center 5px; background-image: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAwAAAAQCAYAAAAiYZ4HAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAHFJREFUeNpiZICC0LCw+UAqgQE7uADEjqtXrfrACFToAOQoAPF8qGQgEH9AUrweiAWAeAIQbwRp+M9AAmBCs/YADuccwKahEOhGRzTnIIuDAQu6cUBJQWKdRLIfRjVQTQNyxPUD09UHUjQYEGMDQIABACCOG5eeklOGAAAAAElFTkSuQmCC)',
        'button_css_hq' => 'background-position:center 5px; background-image: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABMAAAAZCAYAAADTyxWqAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAKhJREFUeNpiZEACoWFhAkBqPxAbMBAGD4A4cPWqVRdgAoxohq0HUgFQ7gIgfojFEH4gLkAy0BBo4AcQhwXJIAckg0DgIhBfwGKYAJJhCkBcD8SFYJcBDfnPQCXAxEBFQFPDGoGByQjCQPYBPPoEoWockQVZ8GhYiCtJwGIPHeA0DKhhATR5DMMIGDVs1LBRw8gG6MX2AygmFggg12ToRZACFA+8NwECDADb9ih7hMWFDgAAAABJRU5ErkJggg==)',
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
	}

	// ********************************************************************************* //

	public function display($settings=array())
	{
		$theme_url = $this->EE->editor_helper->getThemeUrl();

		// Let's load our CSS/JS
		//$this->css_js('js', 'url', EDITOR_THEME_URL.'editor_buttons.js?v='.EDITOR_VERSION, 'editor_buttons', 'main');
		//$this->css_js('css', 'url', EDITOR_THEME_URL.'editor_buttons.css?v='.EDITOR_VERSION, 'editor_buttons', 'main');
	}

	// ********************************************************************************* //
}

/* End of file editor.link.php */
/* Location: ./system/expressionengine/third_party/editor/editor.link.php */
