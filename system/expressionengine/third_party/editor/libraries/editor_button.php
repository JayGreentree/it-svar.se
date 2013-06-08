<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Editor Button File
 *
 * @package			DevDemon_Editor
 * @author			DevDemon <http://www.devdemon.com> - Lead Developer @ Parscale Media
 * @copyright 		Copyright (c) 2007-2010 Parscale Media <http://www.parscale.com>
 * @license 		http://www.devdemon.com/license/
 * @link			http://www.devdemon.com
 */
class Editor_button
{
    /**
     * Button info - Required
     *
     * @access public
     * @var array
     */
    public $info = array(
        'name'          => 'Button Label',
        'version'       => 'Version Number',
        'author'        => 'Author',
        'author_url'    => 'Author URL',
        'description'   => 'Button Description',
        'settings'      => FALSE,
        'callback'      => 'JS Callback',
        'button_css'    => '',
        'button_css_hq' => '',
    );

    /**
     * Overrides Native Button
     *
     * @access public
     * @var bool
     */
    public $overrides_native = FALSE;

    /**
     * Button Settings
     *
     * @access public
     * @var array
     */
    public $settings = array();

    /**
     * Button Dropdown
     *
     * @access public
     * @var array
     */
    public $dropdown = array();

    /**
     * Constructor
     *
     * @param array $settings Button Settings
     */
	public function __construct($settings=array())
	{
		// Creat EE Instance
		$this->EE =& get_instance();
        $this->settings = $settings;
	}

	// ********************************************************************************* //

    /**
     * Display Button HTML (if any)
     *
     * @param  array  $settings Button Settings
     * @return string
     */
    public function display($settings=array())
    {
        return '';
    }

    // ********************************************************************************* //

    /**
     * Display Button Settings
     *
     * @param  array  $settings Button Settings
     * @return string
     */
    public function display_settings($settings=array())
    {
        return '';
    }

    // ********************************************************************************* //

    /**
     * Save Button Settings
     *
     * @param  array  $settings Button settings
     * @return array
     */
    public function save_settings($settings=array())
    {
        return array();
    }

    // ********************************************************************************* //

    /**
     * CSS JS Loader, avoids duplicate assets being loaded
     *
     * Example use jquery plugin: $this->css_js('js', 'url', URL_TO_PLUGIN, 'jquery', 'colorpicker');
     * Example use inline css: $this->css_js('css', 'inline', $inline_css, 'mybutton', 'main');
     *
     * @param  string $asset   Asset type: js/css
     * @param  string $type    Type: url/inline
     * @param  string $value   Be it URL or the inline css/js
     * @param  string $package Package name
     * @param  string $name    Name of the asset
     * @return void
     */
    protected function css_js($asset='', $type='', $value='', $package='', $name='')
    {
        // CSS
        if ($asset == 'css') {
            if (isset($this->EE->session->cache['css'][$package][$name]) === false) {

                if ($type == 'url') {
                    $this->EE->editor->css['url'][] = $value;
                } else {
                    $this->EE->editor->css['inline'][] = $value;
                }

                $this->EE->session->cache['css'][$package][$name] = true;
            }
        }

        // JS
        if ($asset == 'js' && $type == 'url') {
            if (isset($this->EE->session->cache['javascript'][$package][$name]) === false) {

                if ($type == 'url') {
                    $this->EE->editor->js['url'][] = $value;
                } else {
                    $this->EE->editor->js['inline'][] = $value;
                }

                $this->EE->session->cache['javascript'][$package][$name] = true;
            }
        }
    }

    // ********************************************************************************* //

} // END CLASS

/* End of file editor_button.php  */
/* Location: ./system/expressionengine/third_party/forms/libraries/editor_button.php */
