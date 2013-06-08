<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Forms Email File
 *
 * @package    DevDemon_Forms
 * @author     DevDemon <http://www.devdemon.com> - Lead Developer @ Parscale Media
 * @copyright  Copyright (c) 2007-2010 Parscale Media <http://www.parscale.com>
 * @license    http://www.devdemon.com/license/
 * @link       http://www.devdemon.com/forms/
 */
class FormsEmail
{
    private $EE;

    public $template_id = false;
    public $wordwrap = false;
    public $mailtype = false;
    public $from = false;
    public $reply_to = false;
    public $to = false;
    public $subject = false;
    public $cc = false;
    public $bcc = false;
    public $message = false;
    public $newline = "\n";
    public $crlf = "\n";

    public $debug = array();

    // ----------------------------------------------------------------------

    /**
     * Constructor
     *
     * @access public
     */
    public function __construct()
    {
        // Creat EE Instance
        $this->EE =& get_instance();
        $this->site_id = $this->EE->config->item('site_id');
        $this->EE->load->library('email');

        //----------------------------------------
        // CRLF & Newline
        //----------------------------------------
        if ($this->EE->config->item('email_newline') != false) {
            $this->newline = $this->EE->config->item('email_newline');
        }

        if ($this->EE->config->item('email_crlf') != false) {
            $this->crlf = $this->EE->config->item('email_crlf');
        }

        // Forms Specific one?
        $conf = $this->EE->config->item('forms');
        if (is_array($conf) === true) {

            if (isset($conf['email']['newline']) === true) {
                $this->newline = $conf['email']['newline'];
            }

            if (isset($conf['email']['crlf']) === true) {
                $this->crlf = $conf['email']['crlf'];
            }
        }
    }

    // ----------------------------------------------------------------------

    public function send()
    {
        if ($this->template_id == false) {
            return false;
        }

        //----------------------------------------
        // Grab our Template
        //----------------------------------------
        $this->EE->db->select('*');
        $this->EE->db->from('exp_forms_email_templates');

        if (isset($this->EE->forms->entry->form_id) === true && $this->template_id == false) {
            $this->EE->db->where('template_id', $this->template_id);
        } else {
            $this->EE->db->where('form_id', $this->EE->forms->data['form_id']);
        }

        $query = $this->EE->db->get();

        if ($query->num_rows() == 0) {
            return false;
        }

        $tmpl = $query->row();
        $type = $tmpl->template_type;

        //----------------------------------------
        // Override Templates
        //----------------------------------------
        if (isset($this->EE->forms->params) === true && empty($this->EE->forms->params) === false) {
            $params = $this->EE->forms->params;
            if (isset($params["notify_{$type}_email"]) === TRUE) $tmpl->email_to = $params["notify_{$type}_email"];
            if (isset($params["notify_{$type}_from_name"]) === TRUE) $tmpl->email_from = $params["notify_{$type}_from_name"];
            if (isset($params["notify_{$type}_from_email"]) === TRUE) $tmpl->email_from_email = $params["notify_{$type}_from_email"];
            if (isset($params["notify_{$type}_cc"]) === TRUE) $tmpl->email_cc = $params["notify_{$type}_cc"];
            if (isset($params["notify_{$type}_subject"]) === TRUE) $tmpl->email_subject = $params["notify_{$type}_subject"];
            if (isset($params["notify_{$type}_bcc"]) === TRUE) $tmpl->email_bcc = $params["notify_{$type}_bcc"];
            if (isset($params["notify_{$type}_replyto_name"]) === TRUE) $tmpl->email_reply_to = $params["notify_{$type}_replyto_name"];
            if (isset($params["notify_{$type}_replyto_email"]) === TRUE) $tmpl->email_reply_to_email = $params["notify_{$type}_replyto_email"];
            if (isset($params["notify_{$type}_replyto_author"]) === TRUE) $tmpl->reply_to_author = $params["notify_{$type}_replyto_author"];
        }

        $this->EE->email->EE_initialize();

        $email->email_tp = explode(',', $email->email_to);

        $email->email_cc = explode(',', $email->email_cc);
        foreach($email->email_cc as &$val) { $this->EE->forms->entry->parseFormVars(trim($val)); }
        if (isset($this->EE->forms->master_info["{$type}_email_cc"]) === TRUE && is_array($this->EE->forms->master_info["{$type}_email_cc"]) === TRUE)
        {
            foreach ($this->EE->forms->master_info["{$type}_email_cc"] as $email_val) $email->email_cc[] = $email_val;
        }

        $email->email_bcc = explode(',', $email->email_bcc);
        foreach($email->email_bcc as &$val) { $this->EE->forms->entry->parseFormVars(trim($val)); }
        if (isset($this->EE->forms->master_info["{$type}_email_bcc"]) === TRUE && is_array($this->EE->forms->master_info["{$type}_email_bcc"]) === TRUE)
        {
            foreach ($this->EE->forms->master_info["{$type}_email_bcc"] as $email_val) $email->email_bcc[] = $email_val;
        }

        //----------------------------------------
        // Admin Specific
        //----------------------------------------
        if ($type == 'admin') {

            if (isset($this->EE->session->cache['Forms']['EmailAdminOverride']) == TRUE) {
                $tmpl->email_to = $this->EE->session->cache['Forms']['EmailAdminOverride'];
            }
        }

        //----------------------------------------
        // User Specific
        //----------------------------------------
        if ($type == 'user') {
            $tmpl->email_to = $this->EE->session->userdata['email'];
        }

        //----------------------------------------
        // Custom Reply To?
        //----------------------------------------
        if ($email->reply_to_author == 'yes')
        {
            $this->EE->email->reply_to($this->EE->session->userdata['email']);
        }
        else
        {
            $this->EE->email->reply_to( $this->EE->forms->entry->parseFormVars($email->email_reply_to_email), $this->EE->forms->entry->parseFormVars($email->email_reply_to) );
        }


        $this->EE->email->wordwrap = ($email->email_wordwrap == 'yes') ? TRUE : FALSE;
        $this->EE->email->mailtype = $email->email_type;
        $this->EE->email->from( $this->EE->forms->entry->parseFormVars($email->email_from_email), $this->EE->forms->entry->parseFormVars($email->email_from));
        $this->EE->email->to( $tmpl->email_to );
        $this->EE->email->subject( $this->EE->forms->entry->parseFormVars($email->email_subject) );
        $this->EE->email->cc( $this->EE->forms->entry->parseFormVars($email->email_cc) );
        $this->EE->email->bcc( $this->EE->forms->entry->parseFormVars($email->email_bcc) );
        $this->EE->email->message( $this->parse_email_template($email) );
        $this->EE->email->set_newline($newline);
        $this->EE->email->set_crlf($crlf);

        if ($email->email_type == 'html') $this->EE->email->set_alt_message( $this->parse_email_template($email, TRUE) );

        // Handle Attachtments!
        if ($email->email_attachments == 'yes')
        {
            if (isset($this->EE->session->cache['Forms']['UploadedFiles']) == TRUE && is_array($this->EE->session->cache['Forms']['UploadedFiles']) == TRUE)
            {
                foreach($this->EE->session->cache['Forms']['UploadedFiles'] as $file)
                {
                    $this->EE->email->attach($file);
                }
            }
        }

        $this->EE->email->send();
        $this->debug = $this->EE->email->print_debugger();
        $this->EE->email->clear(true);
    }

    // ----------------------------------------------------------------------

    private function parse_email_template($email_template, $alt_body=FALSE)
    {
        $out = '';

        // What Email Type? (for form fields display method)
        $email_type = $email_template->email_type;
        if ($alt_body == TRUE) $email_type = 'text';

        //----------------------------------------
        // Get the template body
        //----------------------------------------
        if ($alt_body == TRUE)
        {
            $out = $email_template->alt_template;
        }
        elseif ($email_template->ee_template_id > 0)
        {
            $query = $this->EE->db->select('template_data')->from('exp_templates')->where('template_id', $email_template->ee_template_id)->get();
            $out = $query->row('template_data');
        }
        else
        {
            $out = $email_template->template;
        }

        // Empty? Nothing to do then!
        if ($out == FALSE) return '';

        //----------------------------------------
        // Parse available variables!
        //----------------------------------------
        $out = $this->EE->forms->entry->parseFormVars($out, $email_type);

        //----------------------------------------
        // Loop over all fields?
        //----------------------------------------
        if (strpos($out, '{form:fields}') !== FALSE)
        {
            // Grab the data between the pairs
            $tagdata = $this->EE->forms_helper->fetch_data_between_var_pairs('form:fields', $out);

            $final = '';
            $count = 0;

            // Loop over all fields
            foreach ($this->forms->entries->fields as $field)
            {
                if (in_array($field['field_type'], $this->EE->forms->ignored_fields) === TRUE) continue;

                $row = '';
                $count++;

                // Create the VARS
                $vars = array();
                $vars['{field:label}'] = $field['title'];
                $vars['{field:short_name}'] = $field['url_title'];
                $vars['{field:value}'] = $this->EE->forms->fieldtypes[ $field['field_type'] ]->output_data($field, $this->EE->forms->entry->fieldsdata[ $field['field_id'] ], $email_type);
                $vars['{field:count}'] = $count;

                // Convert back to html entities
                $vars['{field:value}'] = html_entity_decode($vars['{field:value}'], ENT_QUOTES, 'UTF-8');

                // Parse them
                $row = str_replace(array_keys($vars), array_values($vars), $tagdata);

                $final .= $row;
            }

            // Replace the var pair!
            $out = $this->EE->forms_helper->swap_var_pairs('form:fields', $final, $out);
        }

        //----------------------------------------
        // Allows template parsing!
        //----------------------------------------
        if (class_exists('EE_Template') == FALSE) require_once APPPATH.'libraries/Template.php';
        $this->EE->TMPL = new EE_Template();
        $this->EE->TMPL->parse($out, FALSE, $this->site_id);
        $out = $this->EE->TMPL->final_template;

        return $out;
    }

    // ----------------------------------------------------------------------



    public function __set($var, $val)
    {
        $method = 'set' . ucfirst(strtolower($var));

        if (method_exists($this, $method) === true){
            $this->{$method}($val);
            return;
        }

        $this->{$var} = $val;
    }

    // ----------------------------------------------------------------------

    public function __get($var)
    {
        $method = 'get' . ucfirst(strtolower($var));

        if (method_exists($this, $method) === true){
            return $this->{$method}();
        }

        if (property_exists($this, $var) === true){
            return $this->{$var};
        }

        throw new Exception('Class property does not exists: ' . $var );
    }

    // ----------------------------------------------------------------------

} // END CLASS

/* End of file formsemail.php  */
/* Location: ./system/expressionengine/third_party/forms/libraries/formsemail.php */
