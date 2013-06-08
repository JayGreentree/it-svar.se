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
 * @link			http://www.devdemon.com/editor_pack/
 */
class Templates_ebtn extends Editor_button
{
	/**
	 * Button info - Required
	 *
	 * @access public
	 * @var array
	 */
	public $info = array(
		'name' 		=> 'Templates',
		'author'	=> 'DevDemon',
		'author_url' => 'http://www.devdemon.com',
		'description'=> 'Create HTML Templates',
		'version'	=> EDITOR_VERSION,
		'settings'	=> TRUE,
		'button_css'    => 'background-position:4px 6px; background-image: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAIAAACQkWg2AAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyJpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoV2luZG93cykiIHhtcE1NOkluc3RhbmNlSUQ9InhtcC5paWQ6MjIyM0RBNkIyODZEMTFFMjg4OENDMDUwOTRENzE2NUIiIHhtcE1NOkRvY3VtZW50SUQ9InhtcC5kaWQ6MjIyM0RBNkMyODZEMTFFMjg4OENDMDUwOTRENzE2NUIiPiA8eG1wTU06RGVyaXZlZEZyb20gc3RSZWY6aW5zdGFuY2VJRD0ieG1wLmlpZDoyMjIzREE2OTI4NkQxMUUyODg4Q0MwNTA5NEQ3MTY1QiIgc3RSZWY6ZG9jdW1lbnRJRD0ieG1wLmRpZDoyMjIzREE2QTI4NkQxMUUyODg4Q0MwNTA5NEQ3MTY1QiIvPiA8L3JkZjpEZXNjcmlwdGlvbj4gPC9yZGY6UkRGPiA8L3g6eG1wbWV0YT4gPD94cGFja2V0IGVuZD0iciI/Pkf4iPQAAAGCSURBVHjadJJLisJAEIZNLMUHKqIGXbnxBi4EQVx4I8/jSURvImYRokYFH6D4TObTYjIZJ1OQTtej//rr7zYmk8nlcnFdd7lcsjFNM/HbfN/P5XLlcjmdTuPKfr/vdrv9fj8IAnKJOAOF7G63A13W63WpVMJZrVYiEnvg8Xg0Go1qtep5nmw2m+fzyW6xWBQKBZA+qg3DOB6PrBDbbrcmvP9joqYQHKDsfD4LPzrU63VCyWQS93a7RUfnQDabrVQqlJGS0+kExWKxmEqlSNM3n8/HtoIzoMJH9Wg0mk6nRHu93nA4nM1m4QA6dK1WU0lEmxJqNpt0ZD6tow/E9ADUfyQmRAJisNcZFIIg+/v9rvegN4hCEnwb8CEHXf23hSopkET1/pAfGaigrSJq/NWBHPDz+RyfW9cp4R2Ke71eQ+lEATqdjvrtdpvVsiwF0iDVAB0Oh9cBbsRxHB7fYDDApxV6/32z3tsymYy0Wq3xeGzbNu9PUVlRIzqYDkA1t/ElwACnkQcyo7ujvAAAAABJRU5ErkJggg==);',
        'button_css_hq' => '',
	);

	/**
	 * Constructor
	 *
	 * @access public
	 *
	 * Calls the parent constructor
	 */
	public function __construct($settings=array())
	{
		parent::__construct($settings);

		$this->EE->load->add_package_path(PATH_THIRD . 'editor_pack/');

		// If we have no settings? Lets alert!
		if (isset($settings['templates']) === FALSE || empty($settings['templates']) === TRUE)
		{
			$this->info['callback'] = 'EditorButtons.NoSettingsAlert';
			return;
		}

		foreach ($settings['templates'] as $key => $tmpl)
		{
			$this->dropdown[$key]['title'] = $tmpl['title'];
			$this->dropdown[$key]['callback'] = 'EditorButtons.Templates.InsertTemplate';
		}
	}

	// ********************************************************************************* //

	public function display($settings=array())
	{
		// Let's load our CSS/JS
		//$this->css_js('js', 'url', EDITOR_THEME_URL.'editor_buttons.js?v='.EDITOR_VERSION, 'editor_buttons', 'main');
		//$this->css_js('css', 'url', EDITOR_THEME_URL.'editor_buttons.css?v='.EDITOR_VERSION, 'editor_buttons', 'main');
	}

	// ********************************************************************************* //

	public function display_settings($settings=array())
	{
		$data = $settings;
		if (isset($data['templates']) === FALSE) $data['templates'] = array();

		// Load our css/js
        $this->EE->editor_helper->addMcpAssets('js', 'editor_buttons_settings.js?v='.EDITOR_VERSION, 'editor_buttons', 'settings');

		return $this->EE->load->view('btn/settings_templates', $data, TRUE);
	}

	// ********************************************************************************* //

	public function save_settings($settings=array())
	{
		if (isset($settings['templates']) === FALSE || is_array($settings['templates']) === FALSE) return array();

		$data = array();

		foreach ($settings['templates'] as $template)
		{
			if (isset($template['title']) === FALSE || $template['title'] == FALSE) continue;

			$data['templates'][] = $template;
		}

		return $data;
	}

	// ********************************************************************************* //
}

/* End of file editor.template.php */
/* Location: ./system/expressionengine/third_party/editor_pack/editor.template.php */
