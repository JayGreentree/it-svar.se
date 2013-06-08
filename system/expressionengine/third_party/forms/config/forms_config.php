<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if (isset($this->EE) == FALSE) $this->EE =& get_instance(); // For EE 2.2.0+

$config['cf_formfields_cats'] = array('page_tools'=>array(), 'form_tools' => array(), 'power_tools' => array(), 'list_tools' => array(), 'cart_tools' => array());
$config['cf_formfields']['page_tools']	= array('columns_2', 'html', 'pagebreak');
$config['cf_formfields']['form_tools']	= array('text_input', 'textarea', 'select', 'radio', 'checkbox');
$config['cf_formfields']['power_tools']	= array('captcha');
$config['cf_formfields']['list_tools']	= array('entries_list');

$config['cf_formsettings']['label_placement']          = 'top';
$config['cf_formsettings']['desc_placement']           = 'bottom';
$config['cf_formsettings']['form_enabled']             = 'yes';
$config['cf_formsettings']['open_fromto']['from']      = '';
$config['cf_formsettings']['open_fromto']['to']        = '';
$config['cf_formsettings']['max_entries']             = '';
$config['cf_formsettings']['member_groups']            = array();
$config['cf_formsettings']['multiple_entries']         = 'yes';
$config['cf_formsettings']['submit_button']['type']    = 'default';
$config['cf_formsettings']['submit_button']['text']    = 'Save';
$config['cf_formsettings']['submit_button']['text_next_page'] = 'Next Page';
$config['cf_formsettings']['submit_button']['img_url'] = '';
$config['cf_formsettings']['submit_button']['img_url_next_page'] = '';
$config['cf_formsettings']['limit_entries']['number']  ='';
$config['cf_formsettings']['limit_entries']['type']    ='';
$config['cf_formsettings']['return_url']				= '';
$config['cf_formsettings']['confirmation']['when']      = 'before_redirect';
$config['cf_formsettings']['confirmation']['text']      = '';
$config['cf_formsettings']['confirmation']['redirect_timeout'] = 2;
$config['cf_formsettings']['confirmation']['show_form']	= 'no';
$config['cf_formsettings']['snaptcha']                 = 'no';
$config['cf_formsettings']['force_https']              = 'no';
$config['cf_formsettings']['third_party']['flow']	= 'disabled';
$config['cf_formsettings']['third_party']['url'] = '';
$config['cf_formsettings']['third_party']['field_identifier'] = 'field_name';
$config['cf_formsettings']['save_fentry'] = 'yes';
$config['cf_formsettings']['cp_dashboard']['show'] = 'yes';
$config['cf_formsettings']['cp_dashboard']['member_groups'] = array(1);
$config['cf_formsettings']['debug_mode'] = 'no';

$config['forms_defaults']['cp_dashboard']['enabled'] = 'yes';
$config['forms_defaults']['recaptcha']['public']          = '';
$config['forms_defaults']['recaptcha']['private']         = '';
$config['forms_defaults']['mailchimp']['api_key']         = '';
$config['forms_defaults']['createsend']['api_key']        = '';
$config['forms_defaults']['createsend']['client_api_key'] = '';
$config['forms_defaults']['messages']['required_field'] = $this->EE->lang->line('form:error:required_field');
$config['forms_defaults']['messages']['form_submit_success_heading'] = $this->EE->lang->line('thank_you');
$config['forms_defaults']['stripe']['test']['secret']   = '';
$config['forms_defaults']['stripe']['test']['public']   = '';
$config['forms_defaults']['stripe']['live']['secret']   = '';
$config['forms_defaults']['stripe']['live']['public']   = '';
$config['forms_defaults']['aunet']['api_login_id'] = '';
$config['forms_defaults']['aunet']['transaction_key'] = '';
$config['forms_defaults']['payflow_pro']['vendor']   = '';
$config['forms_defaults']['payflow_pro']['partner']   = '';
$config['forms_defaults']['payflow_pro']['username']   = '';
$config['forms_defaults']['payflow_pro']['password']   = '';


$config['cf_validation_options'] = array();
$config['cf_validation_options']['none']     = $this->EE->lang->line('form:none');
$config['cf_validation_options']['alpha']    = $this->EE->lang->line('form:val:alpha');
$config['cf_validation_options']['alphanum'] = $this->EE->lang->line('form:val:alphanum');
$config['cf_validation_options']['numbers']  = $this->EE->lang->line('form:val:numbers');
$config['cf_validation_options']['float']    = $this->EE->lang->line('form:val:float');
$config['cf_validation_options']['email']    = $this->EE->lang->line('form:val:email');
$config['cf_validation_options']['url']      = $this->EE->lang->line('form:val:url');

$config['cf_dropdown_options']['yes_no']['no']               = $this->EE->lang->line('form:no');
$config['cf_dropdown_options']['yes_no']['yes']              = $this->EE->lang->line('form:yes');
$config['cf_dropdown_options']['email_types']['text']        = $this->EE->lang->line('form:tmpl:email:text');
$config['cf_dropdown_options']['email_types']['html']        = $this->EE->lang->line('form:tmpl:email:html');
$config['cf_dropdown_options']['template_types']['user']     = $this->EE->lang->line('form:tmpl:user');
$config['cf_dropdown_options']['template_types']['admin']    = $this->EE->lang->line('form:tmpl:admin');
$config['cf_dropdown_options']['limit_types']['total']       = $this->EE->lang->line('form:limit:total');
$config['cf_dropdown_options']['limit_types']['day']         = $this->EE->lang->line('form:limit:day');
$config['cf_dropdown_options']['limit_types']['week']        = $this->EE->lang->line('form:limit:week');
$config['cf_dropdown_options']['limit_types']['month']       = $this->EE->lang->line('form:limit:month');
$config['cf_dropdown_options']['limit_types']['year']        = $this->EE->lang->line('form:limit:year');
$config['cf_dropdown_options']['form_types']['entry_linked'] = $this->EE->lang->line('form:entry_linked');
$config['cf_dropdown_options']['form_types']['normal']       = $this->EE->lang->line('form:salone');



$config['forms_defaults']['print_pdf']['paper_size']      = 'letter';
$config['forms_defaults']['print_pdf']['paper_orientation'] = 'portrait';
$config['forms_defaults']['print_pdf']['template_top'] = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xml:lang="en" xmlns="http://www.w3.org/1999/xhtml" lang="en">
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8" />

<title>Form Entry</title>

<style type="text/css">
@page {margin:2cm;}
body {font-family:sans-serif; margin:0.5cm 0; text-align:justify;}
#header, #footer {position:fixed; left:0; right 0;color:#aaa;font-size: 0.9em;}
#header {top:0; border-bottom:0.1pt solid #aaa;}
#footer {bottom:0; border-top:0.1pt solid #aaa;}
#header table, #footer table {width:100%; border-collapse:collapse; border:none;}
#header td, #footer td {padding:0; width:50%;}
.page-number {text-align:center;}
.page-number:before {content:"Page " counter(page);}
hr {page-break-after:always; border:0;}
</style>
</head>

<body>

<div id="header">
    <strong>FORM:</strong> {form:label}
</div>

<div id="footer">
  <table>
    <tr>
      <td style="text-align:left;">For internal use only</td>
      <td class="page-number"></td>
      <td style="text-align:right;"><strong>PRINTED ON:</strong> {date:usa}</td>
    </tr>
  </table>
</div>';

$config['forms_defaults']['print_pdf']['template_loop'] = '<table style="width:100%; border-collapse:collapse; border:0">
<tr>
    <td><strong>Date: </strong> {entry:datetime:usa}</td>
    <td><strong>Country: </strong> {entry:country}</td>
</tr>
<tr>
    <td><strong>Member: </strong> {entry:member}</td>
    <td><strong>IP Address: </strong> {entry:ip_address}</td>
</tr>
</table>

<div class="fields">
{form:fields}
<p><strong>{field:count}. {field:label}:</strong> {field:value}</p>
{/form:fields}
</div>';
