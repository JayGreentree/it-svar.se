<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Forms Entry File
 *
 * @package    DevDemon_Forms
 * @author     DevDemon <http://www.devdemon.com> - Lead Developer @ Parscale Media
 * @copyright  Copyright (c) 2007-2010 Parscale Media <http://www.parscale.com>
 * @license    http://www.devdemon.com/license/
 * @link       http://www.devdemon.com/forms/
 */
class FormsEntry
{
    private $EE;

    public $send_email = true;
    public $fields = array();
    public $fieldsdata = array();

    private $fentry_id = false;
    private $fentry_hash = false;
    private $form_id = false;
    private $country = false;
    private $site_id = false;
    private $member_id = false;
    private $ip = false;
    private $date = false;


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

        if (class_exists('FormsEmail') === false) require_once PATH_THIRD.'forms/libraries/formsemail.php';
    }

    // ----------------------------------------------------------------------

    public function save($send_emails=false)
    {
        if ($this->date === false) {
            $this->date = $this->EE->localize->now;
        }

        if ($this->country === false) {
            $this->country = $this->EE->forms->getUserCountry();
        }

        if ($this->fentry_hash === false) {

            // Check if it already exists
            for ($i=0; $i < 50; $i++) {
                $this->fentry_hash = $this->EE->forms_helper->uuid(false);
                $query = $this->EE->db->query("SELECT fentry_id FROM exp_forms_entries WHERE fentry_hash = '{$this->fentry_hash}'");
                if ($query->num_rows() == 0) break;
            }
        }

        $this->ip = sprintf("%u", ip2long($this->EE->forms->ip_address));

        $this->EE->db->set('fentry_hash', $this->fentry_hash);
        $this->EE->db->set('form_id', $this->form_id);
        $this->EE->db->set('site_id', $this->site_id);
        $this->EE->db->set('member_id', $this->member_id);
        $this->EE->db->set('ip_address', $this->ip);
        $this->EE->db->set('date', $this->date);
        $this->EE->db->set('country', $this->country);

        foreach ($this->fieldsdata as $field_id => $data)
        {
            $this->EE->db->set('fid_'.$field_id, $data);
        }

        $this->EE->db->insert('exp_forms_entries');
        $this->fentry_id = $this->EE->db->insert_id();

        //----------------------------------------
        // Update Form Data
        //----------------------------------------
        $this->EE->db->set('total_submissions', '(total_submissions+1)', FALSE);
        $this->EE->db->set('date_last_entry', $this->EE->localize->now);
        $this->EE->db->where('form_id', $this->form_id);
        $this->EE->db->update('exp_forms');

        if ($send_emails === true) {
            $this->sendEmails();
        }
    }

    // ----------------------------------------------------------------------

    public function sendEmails()
    {
        $this->emailAdmin();
        $this->emailUser();
    }

    // ----------------------------------------------------------------------

    public function emailAdmin()
    {
        if (isset($this->EE->forms->data['admin_template']) === false) {
            return;
        }

        if ((int)$this->EE->forms->data['admin_template'] == 0) {
            return;
        }

        $email = new FormsEmail();
        $email->template_id = $this->EE->forms->data['admin_template'];
        $email->send();
        $this->EE->forms->debug['emails']['admin'] = $email->debug;
    }

    // ----------------------------------------------------------------------

    public function emailUser()
    {
        if (isset($this->EE->forms->data['user_template']) === false) {
            return;
        }

        if ((int)$this->EE->forms->data['user_template'] == 0) {
            return;
        }

        $email = new FormsEmail();
        $email->template_id = $this->EE->forms->data['user_template'];
        $email->send();

        $this->EE->forms->debug['emails']['user'] = $email->debug;
    }

    // ----------------------------------------------------------------------

    private function parseFormVars($string, $format='text')
    {
        $vars = array();
        $vars['{form:label}'] = $this->EE->forms->data['form_title'];
        $vars['{form:short_name}'] = $this->EE->forms->data['form_url_title'];
        $vars['{form:id}'] = $this->EE->forms->data['form_id'];
        $vars['{user:referrer}'] = (isset($_SERVER['HTTP_REFERER']) == TRUE) ? $_SERVER['HTTP_REFERER'] : '';
        $vars['{date:usa}'] = $this->EE->forms_helper->formatDate('%m/%d/%Y', $this->EE->localize->now, true);
        $vars['{date:eu}'] = $this->EE->forms_helper->formatDate('%d/%m/%Y', $this->EE->localize->now, true);
        $vars['{datetime:usa}'] = $this->EE->forms_helper->formatDate('%m/%d/%Y %h:%i %A', $this->EE->localize->now, true);
        $vars['{datetime:eu}'] =  $this->EE->forms_helper->formatDate('%d/%m/%Y %H:%i', $this->EE->localize->now, true);

        if (isset($this->fentry_id) === TRUE)
        {
            $vars['{fentry_id}'] = $this->fentry_id;
        }

        if (isset($this->fentry_hash) === TRUE)
        {
            $vars['{fentry_hash}'] = $this->fentry_hash;
        }

        // Parse it!
        $string = str_replace(array_keys($vars), array_values($vars), $string);

        // Parse all user session data too
        foreach($this->EE->session->userdata as $var => $val)
        {
            // Val has arrays? Ignore them!
            if (is_array($val) == TRUE) continue;

            $string = str_replace('{user:'.$var.'}', $val, $string);
        }

        foreach($this->EE->forms->data as $var => $val)
        {
            // Val has arrays? Ignore them!
            if (is_array($val) == TRUE) continue;

            $string = str_replace('{form:'.$var.'}', $val, $string);
        }

        foreach ($this->forms->entries->fields as $field)
        {
            if (in_array($field['field_type'], $this->EE->forms->ignored_fields) === TRUE) continue;

            $string = str_replace('{field:'.$field['url_title'].'}', $this->EE->forms->fieldtypes[ $field['field_type'] ]->output_data($field, $this->fieldsdata[ $field['field_id'] ], $format), $string);
        }

        return $string;
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

/* End of file formsentry.php  */
/* Location: ./system/expressionengine/third_party/forms/libraries/formsentry.php */
