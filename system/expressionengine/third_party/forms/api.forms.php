<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

//require_once PATH_THIRD.'credits/libraries/creditsaward.php';

/**
 * Credits API File
 *
 * @package         DevDemon_Credits
 * @author          DevDemon <http://www.devdemon.com> - Lead Developer @ Parscale Media
 * @copyright       Copyright (c) Parscale Media <http://www.parscale.com>
 * @license         http://www.devdemon.com/license/
 * @link            http://www.devdemon.com/credits/
 */
class Forms_API
{
    private $EE;

    public $debug = array();
    public $colfields = array('columns_2', 'columns_3', 'columns_4', 'fieldset', 'html', 'pagebreak', 'html');
    public $config = array();
    public $form_data = array();

    private $fieldtypes = false;



    // ----------------------------------------------------------------------

    public function __construct()
    {
        $this->EE =& get_instance();
        $this->EE->load->add_package_path(PATH_THIRD . 'forms/');

        //----------------------------------------
        // Forms config (config.php)
        //----------------------------------------
        $this->config = $this->EE->config->item('forms');
        if (is_array($this->config) === FALSE) $this->config = array();

        $this->EE->config->load('forms_config');

        $this->EE->load->model('forms_model');
    }

    // ----------------------------------------------------------------------

    public function getFieldtypes()
    {
        if ($this->fieldtypes === false) {
            $this->loadFieldtypes();
        }

        return $this->fieldtypes;
    }

    // ----------------------------------------------------------------------

    public function loadFieldtypes()
    {
        if (class_exists('FormsField') === false) require_once PATH_THIRD.'forms/fields/formsfield.php';

        // Lets start clean..
        $this->EE->forms->fieldtypes = array();

        // Get the files & sort
        $files = scandir(PATH_THIRD.'forms/fields/');
        sort($files);

        if (is_array($files) === FALSE || count($files) == 0) return;

        // Loop over all fields
        foreach ($files as $file)
        {
            // The file must start with: field.
            if (strpos($file, 'field.') === 0) {

                // Get the class name
                $name = substr($file, 6); // removes field. (be aware of this: field.hidden_field.php)
                $name = substr($name, 0, -4); // removes the .php
                //$name = str_replace(array('.php'), '', $file);
                $class = 'FormsField_'.$name;

                // Load the file
                $path = PATH_THIRD.'forms/fields/'.$file;
                require_once $path;

                // Does the class exists now?
                if (class_exists($class) === FALSE) continue;

                $this->EE->forms->fieldtypes[$name] = new $class();

                // Final check
                if (isset($this->EE->forms->fieldtypes[$name]->info) == FALSE) unset($this->EE->forms->fieldtypes[$name]);
                if (isset($this->EE->forms->fieldtypes[$name]->info['disabled']) == TRUE && $this->EE->forms->fieldtypes[$name]->info['disabled'] == TRUE) unset($this->EE->formsfields[$name]);
            }
        }
    }

    // ----------------------------------------------------------------------

    public function getUserCountry()
    {
        $country = '';
        if ($this->EE->config->item('ip2nation') == 'y')
        {
            if ( version_compare(APP_VER, '2.5.2', '>=') )
            {
                $addr = $this->ip_address;

                // all IPv4 go to IPv6 mapped
                if (strpos($addr, ':') === FALSE && strpos($addr, '.') !== FALSE)
                {
                    $addr = '::'.$addr;
                }
                $addr = inet_pton($addr);

                $query = $this->EE->db
                ->select('country')
                ->where("ip_range_low <= '".$addr."'", '', FALSE)
                ->where("ip_range_high >= '".$addr."'", '', FALSE)
                ->order_by('ip_range_low', 'desc')
                ->limit(1, 0)
                ->get('exp_ip2nation');

                if ($query->num_rows() > 0) $country = $query->row('country');
            }
            else
            {
                $query = $this->EE->db->query("SELECT country FROM exp_ip2nation WHERE ip < INET_ATON('".$this->EE->db->escape_str($this->ip_address)."') ORDER BY ip DESC LIMIT 0,1");
                $country = $query->row('country');
            }

        }
        else
        {
            if (function_exists('dns_get_record') == TRUE)
            {
                $reverse_ip = implode('.',array_reverse(explode('.',$this->ip_address)));
                $DNS_resolver = '.lookup.ip2.cc';
                $lookup = @dns_get_record($reverse_ip.$DNS_resolver, DNS_TXT);
                $country = isset($lookup[0]['txt']) ? strtolower($lookup[0]['txt']) : FALSE;

                if ($country == FALSE)
                {
                    $content = $this->EE->forms_helper->fetch_url_file('http://www.geoplugin.net/php.gp?ip='.$this->ip_address);
                    $geoip = @unserialize($content);
                    $country = strtolower($geoip['geoplugin_countryCode']);
                }
            }
            else
            {
                $content = $this->EE->forms_helper->fetch_url_file('http://www.geoplugin.net/php.gp?ip='.$this->ip_address);
                $geoip = @unserialize($content);
                $country = strtolower($geoip['geoplugin_countryCode']);
            }
        }

        if ($country == FALSE) $country = 'xx';

        return $country;
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

/* End of file api.forms.php  */
/* Location: ./system/expressionengine/third_party/forms/api.forms.php */
