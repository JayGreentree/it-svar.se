<?php if (!defined('BASEPATH')) die('No direct script access allowed');

// include config file
if (file_exists(PATH_THIRD.'forms/config.php') === TRUE) include PATH_THIRD.'forms/config.php';
else include dirname(dirname(__FILE__)).'/forms/config.php';

/**
 * Forms Module Extension File
 *
 * @package         DevDemon_Forms
 * @author          DevDemon <http://www.devdemon.com> - Lead Developer @ Parscale Media
 * @copyright       Copyright (c) 2007-2012 Parscale Media <http://www.parscale.com>
 * @license         http://www.devdemon.com/license/
 * @link            http://www.devdemon.com
 * @see             http://expressionengine.com/user_guide/development/extensions.html
 */
class Forms_ext
{
    public $version         = FORMS_VERSION;
    public $name            = 'Forms Extension';
    public $description     = 'Supports the Forms Module in various functions.';
    public $docs_url        = 'http://www.devdemon.com';
    public $settings_exist  = FALSE;
    public $settings        = array();
    public $hooks           = array('cp_js_end');

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
        error_reporting(E_ALL);
        @ini_set('display_errors', 1);

        if (version_compare(APP_VER, 2.6, '>=')) {
            $this->EE->load->helper('compat');
            $this->EE->load->library(array('localize', 'remember', 'session'));
        }

        $this->EE->lang->loadfile('forms');
        $this->EE->load->library('forms_helper');
        $this->EE->load->library('firephp');
        $this->EE->config->load('forms_config');

        $js = '';

        if ($this->EE->extensions->last_call !== FALSE)
        {
            $js = $this->EE->extensions->last_call;
        }

        // Get Settings
        $settings = $this->EE->forms_helper->get_module_settings($this->EE->config->item('site_id'));
        //$this->EE->firephp->log($settings);

        // Enabled?
        if (isset($settings['cp_dashboard']['enabled']) === FALSE || $settings['cp_dashboard']['enabled'] != 'yes')
        {
            return $js;
        }

        // Are we on the right page?
        if (isset($_SERVER['HTTP_REFERER']) == FALSE) return $js;
        $url = parse_url($_SERVER['HTTP_REFERER']);
        if (!$url) return $js;

        parse_str($url['query'], $url_query);
        //$this->EE->firephp->log($url);

        if (isset($url_query['C']) === TRUE && $url_query['C'] != 'homepage') {
            return $js;
        }

        // Any Forms?
        $forms = array();
        $items = array();

        // Get all Stand Alone Forms
        $this->EE->db->select('f.*, mb.screen_name');
        $this->EE->db->from('exp_forms f');
        $this->EE->db->join('exp_members mb', 'mb.member_id = f.member_id', 'left');
        $this->EE->db->order_by('f.form_title', 'ASC');
        $this->EE->db->where('f.site_id', $this->site_id);
        $this->EE->db->where('f.entry_id', 0);
        $query = $this->EE->db->get();

        $items = $query->result();

        // Get all Stand Alone Forms
        $this->EE->db->select('f.*, mb.screen_name');
        $this->EE->db->from('exp_forms f');
        $this->EE->db->join('exp_channel_titles ct', 'ct.entry_id = f.entry_id', 'left');
        $this->EE->db->join('exp_members mb', 'mb.member_id = f.member_id', 'left');
        $this->EE->db->where('f.site_id', $this->site_id);
        $this->EE->db->where('f.entry_id >', 0);
        $this->EE->db->where('(ct.expiration_date = 0 OR ct.expiration_date > '.$this->EE->localize->now.')', null, false);
        $this->EE->db->order_by('f.form_title', 'ASC');
        $query = $this->EE->db->get();

        foreach ($query->result() as $row)
        {
            $items[] = $row;
        }

        $default_settings = $this->EE->config->item('cf_formsettings');
        //$this->EE->firephp->log($default_settings);

        foreach ($items as $row)
        {
            $row->form_settings = @unserialize($row->form_settings);
            if (!$row->form_settings) continue;

            $row->form_settings = $this->EE->forms_helper->array_extend($this->EE->config->item('cf_formsettings'), $row->form_settings);

            if (isset($row->form_settings['cp_dashboard']['show']) === TRUE && $row->form_settings['cp_dashboard']['show'] == 'yes'
                && isset($row->form_settings['cp_dashboard']['member_groups']) === TRUE && in_array($this->EE->session->userdata('group_id'), $row->form_settings['cp_dashboard']['member_groups']) === TRUE
                ) {
                $forms[] = $row;
            }

        }

        // -----------------------------------------
        // Add Help!
        // -----------------------------------------
        $helpjson = array();
        $alertjson = array();

        foreach ($this->EE->lang->language as $key => $val)
        {
            if (strpos($key, 'form:help:') === 0)
            {
                $this->vData['helpjson'][substr($key, 10)] = $val;
                unset($this->EE->lang->language[$key]);
            }

            if (strpos($key, 'form:alert:') === 0)
            {
                $this->vData['alertjson'][substr($key, 11)] = $val;
                unset($this->EE->lang->language[$key]);
            }

        }

        $helpjson = $this->EE->forms_helper->generate_json($this->vData['helpjson']);
        $alertjson = $this->EE->forms_helper->generate_json($this->vData['alertjson']);

        //$this->EE->firephp->log($forms);

        // Show SHOW
        if (empty($forms) === TRUE) return $js;

        $v = FORMS_VERSION;
        $theme_url = $this->EE->forms_helper->define_theme_url();

        $ul = '<table class="holder" cellspacing="0" cellpadding="0" border="0">';
        $ul .= '<thead><tr>';
        $ul .= '<th>'.lang('form').'</th>';
        $ul .= '<th>'.lang('form:submissions').'</th>';
        $ul .= '<th>'.lang('form:date_created').'</th>';
        $ul .= '<th>'.lang('form:last_entry').'</th>';
        $ul .= '</tr></thead>';

        foreach ($forms as $form)
        {
            $ul .= '<tr>';
            $ul .= '<td><a href="#" class="form_toggle" data-id="'.$form->form_id.'">'.$form->form_title.'</a></td>';
            $ul .= '<td>'.$form->total_submissions.'</td>';
            $ul .= '<td>'.$this->EE->forms_helper->formatDate('%d-%M-%Y %g:%i %A', $form->date_created).'</td>';
            $ul .= '<td>'.(($form->date_last_entry != FALSE) ? $this->EE->forms_helper->formatDate('%d-%M-%Y %g:%i %A', $form->date_last_entry) : '').'</td>';
            $ul .= '</tr>';
        }

        $ul .= '</table>';
        $ul = str_replace("'", "\\'", $ul);

        $AJAX_url = $this->EE->forms_helper->get_router_url();

        $cp_js_url = $_SERVER['HTTP_REFERER'];
        $cp_js_url = preg_replace('/((?!S=.*))&(.*)/', '', $cp_js_url);
        $go_back = lang('form:go_back');

        $js .= <<<EOT

        function getUrlVars() {
            var map = {};
            var parts = window.location.search.replace(/[?&]+([^=&]+)(=[^&]*)?/gi, function(m,key,value) {
                map[key] = (value === undefined) ? true : value.substring(1);
            });
            return map;
        };

        var URLVars = getUrlVars();

        if (typeof(Forms) == 'undefined' && (URLVars.C == 'homepage' || !URLVars.C) ) {
            jQuery('body').append('<link rel="stylesheet" href="{$theme_url}chosen/chosen.css?v={$v}" type="text/css"/>');
            jQuery('body').append('<link rel="stylesheet" href="{$theme_url}bootstrap.popovers.css?v={$v}" type="text/css"/>');
            jQuery('body').append('<link rel="stylesheet" href="{$theme_url}forms_mcp.css?v={$v}" type="text/css"/>');
            jQuery('body').append('<link rel="stylesheet" href="{$theme_url}forms_cp_dashboard.css?v={$v}" type="text/css"/>');

            document.write('<script type="text/javascript" src="{$cp_js_url}&D=cp&C=javascript&M=combo_load&ui=datepicker"></script>');
            document.write('<script type="text/javascript" src="{$theme_url}chosen/jquery.chosen.js?v={$v}"></script>');
            document.write('<script type="text/javascript" src="{$theme_url}jquery.dataTables.js?v={$v}"></script>');
            document.write('<script type="text/javascript" src="{$theme_url}bootstrap.popovers.js?v={$v}"></script>');
            document.write('<script type="text/javascript" src="{$theme_url}bootstrap.modal.js?v={$v}"></script>');
            document.write('<script type="text/javascript" src="{$theme_url}forms_mcp.js?v={$v}"></script>');
            document.write('<script type="text/javascript" src="{$theme_url}forms_cp_dashboard.js?v={$v}"></script>');

            $('#mainContent').append('<div id="formsdash" class="contentMenu"><div class="heading"><h2>Forms Dashboard <a class="abtn back resetdashboard"><span>{$go_back}</span></a></h2></div><div id="fbody" class="innerh">{$ul}</div></div>');

            var Forms = {};
            Forms.AJAX_URL = "{$AJAX_url}";
            Forms.JSON = {};

            Forms.JSON.Help = {$helpjson};
            Forms.JSON.Alerts = {$alertjson};
        }

EOT;

        return $js;
    }

    // ********************************************************************************* //

    /**
     * Called by ExpressionEngine when the user activates the extension.
     *
     * @access      public
     * @return      void
     **/
    public function activate_extension()
    {
        foreach ($this->hooks as $hook)
        {
             $data = array( 'class'     =>  __CLASS__,
                            'method'    =>  $hook,
                            'hook'      =>  $hook,
                            'settings'  =>  serialize($this->settings),
                            'priority'  =>  100,
                            'version'   =>  $this->version,
                            'enabled'   =>  'y'
                );

            // insert in database
            $this->EE->db->insert('exp_extensions', $data);
        }
    }

    // ********************************************************************************* //

    /**
     * Called by ExpressionEngine when the user disables the extension.
     *
     * @access      public
     * @return      void
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

/* End of file ext.forms.php */
/* Location: ./system/expressionengine/third_party/forms/ext.forms.php */
