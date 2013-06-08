<?php if (!defined('BASEPATH')) die('No direct script access allowed');

// include config file
require_once dirname(dirname(__FILE__)).'/editor/config.php';

/**
 * Editor Module Extension File
 *
 * @package			DevDemon_Updater
 * @author			DevDemon <http://www.devdemon.com> - Lead Developer @ Parscale Media
 * @copyright 		Copyright (c) 2007-2012 Parscale Media <http://www.parscale.com>
 * @license 		http://www.devdemon.com/license/
 * @link			http://www.devdemon.com
 * @see				http://expressionengine.com/user_guide/development/extensions.html
 */
class Editor_ext
{
	public $version			= EDITOR_VERSION;
	public $name			= 'Editor Extension';
	public $description		= 'Supports the Editor Module in various functions.';
	public $docs_url		= 'http://www.devdemon.com';
	public $settings_exist	= FALSE;
	public $settings		= array();
	public $hooks			= array('cp_js_end', 'cp_css_end');

	// ********************************************************************************* //

	/**
	 * Constructor
	 *
	 * @access public
	 * @return void
	 */
	public function __construct()
	{
		$this->EE =& get_instance();
		$this->site_id = $this->EE->config->item('site_id');

		if (isset($_GET['C']) === TRUE && $_GET['C'] == 'addons_extensions') return;

		$this->EE->load->add_package_path(PATH_THIRD . 'editor/');
		$this->EE->lang->loadfile('editor');
		$this->EE->load->library('editor_helper');

		$this->EE->config->load('editor_config');
	}

	// ********************************************************************************* //

	/**
	 * cp_menu_array
	 *
	 * @param array $menu
	 * @access public
	 * @see N/A
	 * @return array
	 */
	public function cp_js_end()
	{
		$js = '';

		if ($this->EE->extensions->last_call !== FALSE)
		{
			$js = $this->EE->extensions->last_call;
		}

		/*
		if (isset($_SERVER['HTTP_REFERER']) == FALSE) return $js;
		$url = parse_url($_SERVER['HTTP_REFERER'], PHP_URL_QUERY);
		if (!$url) return $js;
		parse_str($url, $url);

		if (isset($url['C']) !== TRUE || $url['C'] != 'admin_content' || isset($url['M']) != TRUE || $url['M'] != 'category_edit') return $js;
		 */

		// -----------------------------------------
		// Settings
		// -----------------------------------------
		$ext_settings =$this->EE->editor_helper->grab_extension_settings($this->site_id);
		$settings = $this->EE->editor_helper->parse_editor_settings($ext_settings);

		$this->EE->editor_helper->get_editor_css_js($settings, 'cat_description');
		$outjs = $this->EE->editor->css_js_out['js'];

		$theme_url = $this->EE->editor_helper->getThemeUrl();

		// Do we need to load an additional Language?
		$lang_js = '';
		if (isset($settings['language']) === TRUE && $settings['language'] != 'en')
		{
			$lang_js = "document.write('<script type=\"text/javascript\" src=\"".$theme_url."redactor/lang/{$settings['language']}.js?v=".EDITOR_VERSION."\"></script>');";
		}

		//$this->EE->firephp->log($json);

		$js .= "
		function addDevDemonEditor(){
			jQuery('body').append('<link rel=\"stylesheet\" href=\"".$theme_url."redactor/redactor.css?v=".EDITOR_VERSION."\" type=\"text/css\" />');
			jQuery('body').append('<link rel=\"stylesheet\" href=\"".$theme_url."editor.css?v=".EDITOR_VERSION."\" type=\"text/css\" />');

			document.write('<script type=\"text/javascript\" src=\"".$theme_url."editor_buttons.js?v=".EDITOR_VERSION."\"></script>');
			document.write('<script type=\"text/javascript\" src=\"".$theme_url."redactor/redactor.min.js?v=".EDITOR_VERSION."\"></script>');
			document.write('<script type=\"text/javascript\" src=\"".$theme_url."editor_helper.js?v=".EDITOR_VERSION."\"></script>');
			{$lang_js}
		};

		var add_editor = false;
		if (document.getElementById('sub_hold_field_category')) {
			if (typeof(EditorButtons) == 'undefined') addDevDemonEditor();

			$(document).ajaxComplete(function(event,request, settings) {
			  if (/D=cp&C=admin_content&M=category_edit/.test(settings.url)) {
			  	setTimeout(function(){
			  		{$outjs}
			  	}, 500);
			  }
			});
		}

		if (/D=cp&C=admin_content&M=category_edit/.test(document.location.href) && document.getElementById('cat_description')) {

			if (typeof(EditorButtons) == 'undefined') addDevDemonEditor();

			$(document).ready(function(){
				{$outjs}
    		});
		}
	    ";

		return $js;
	}

	// ********************************************************************************* //

	/**
	 * cp_menu_array
	 *
	 * @param array $menu
	 * @access public
	 * @see N/A
	 * @return array
	 */
	public function cp_css_end()
	{
		$css = '';

		if ($this->EE->extensions->last_call !== FALSE)
		{
			$css = $this->EE->extensions->last_call;
		}

		if (isset($this->EE->editor->css_js_out) === TRUE)
		{
			$css .= $this->EE->editor->css_js_out['css'];
		}

		$css .= "
			{$this->EE->editor->buttons_css}

			@media all and (-webkit-min-device-pixel-ratio: 1.5) {
    			{$this->EE->editor->buttons_css_hq}
			}

			#redactor_paste_plaintext_area {border:1px solid #8195A0; border-radius:3px 3px 3px 3px; overflow:scroll; width: 99%; height: 300px;}
			#redactor_paste_plaintext_area p {margin:0 0 10px}

			#EditorPackStyles {}
			#EditorPackStyles a:hover {background-color:#fff;}
		";

		return $css;
	}

	// ********************************************************************************* //

	/**
	 * Called by ExpressionEngine when the user activates the extension.
	 *
	 * @access		public
	 * @return		void
	 **/
	public function activate_extension()
	{
		foreach ($this->hooks as $hook)
		{
			 $data = array(	'class'		=>	__CLASS__,
			 				'method'	=>	$hook,
							'hook'      =>	$hook,
							'settings'	=>	'a:23:{s:15:"editor_settings";s:10:"predefined";s:13:"convert_field";s:4:"none";s:14:"upload_service";s:5:"local";s:20:"file_upload_location";s:1:"0";s:21:"image_upload_location";s:1:"0";s:2:"s3";a:4:{s:4:"file";a:1:{s:6:"bucket";s:0:"";}s:5:"image";a:1:{s:6:"bucket";s:0:"";}s:14:"aws_access_key";s:0:"";s:14:"aws_secret_key";s:0:"";}s:6:"height";s:3:"200";s:9:"direction";s:3:"ltr";s:7:"toolbar";s:3:"yes";s:6:"source";s:3:"yes";s:5:"focus";s:2:"no";s:10:"autoresize";s:3:"yes";s:5:"fixed";s:2:"no";s:12:"convertlinks";s:3:"yes";s:11:"convertdivs";s:3:"yes";s:7:"overlay";s:3:"yes";s:13:"observeimages";s:3:"yes";s:3:"air";s:2:"no";s:3:"wym";s:2:"no";s:18:"allowedtags_option";s:7:"default";s:11:"allowedtags";s:0:"";s:11:"editor_conf";s:1:"1";s:6:"site:1";a:22:{s:15:"editor_settings";s:10:"predefined";s:13:"convert_field";s:4:"none";s:14:"upload_service";s:5:"local";s:20:"file_upload_location";s:1:"0";s:21:"image_upload_location";s:1:"0";s:2:"s3";a:4:{s:4:"file";a:1:{s:6:"bucket";s:0:"";}s:5:"image";a:1:{s:6:"bucket";s:0:"";}s:14:"aws_access_key";s:0:"";s:14:"aws_secret_key";s:0:"";}s:6:"height";s:3:"200";s:9:"direction";s:3:"ltr";s:7:"toolbar";s:3:"yes";s:6:"source";s:3:"yes";s:5:"focus";s:2:"no";s:10:"autoresize";s:3:"yes";s:5:"fixed";s:2:"no";s:12:"convertlinks";s:3:"yes";s:11:"convertdivs";s:3:"yes";s:7:"overlay";s:3:"yes";s:13:"observeimages";s:3:"yes";s:3:"air";s:2:"no";s:3:"wym";s:2:"no";s:18:"allowedtags_option";s:7:"default";s:11:"allowedtags";s:0:"";s:11:"editor_conf";s:1:"2";}}',
							'priority'	=>	100,
							'version'	=>	$this->version,
							'enabled'	=>	'y'
      			);

			// insert in database
			$this->EE->db->insert('exp_extensions', $data);
		}
	}

	// ********************************************************************************* //

	/**
	 * Called by ExpressionEngine when the user disables the extension.
	 *
	 * @access		public
	 * @return		void
	 **/
	public function disable_extension()
	{
		$this->EE->db->where('class', __CLASS__);
		$this->EE->db->delete('exp_extensions');
	}

	// ********************************************************************************* //

	/**
	 * Called by ExpressionEngine updates the extension
	 *
	 * @access public
	 * @return void
	 **/
	public function update_extension($current=FALSE)
	{
		if($current == $this->version) return false;

		// Update the extension
		$this->EE->db
			->where('class', __CLASS__)
			->update('extensions', array('version' => $this->version));

	}

	// ********************************************************************************* //

} // END CLASS

/* End of file ext.editor.php */
/* Location: ./system/expressionengine/third_party/editor/ext.editor.php */
