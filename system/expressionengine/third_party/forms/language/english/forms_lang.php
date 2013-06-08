<?php if (!defined('BASEPATH')) die('No direct script access allowed');

$lang = array(

// Required for MODULES page
'forms'			=>	'Forms',
'forms_module_name'=>	'Forms',
'forms_module_description'	=>	'Create Forms',

//----------------------------------------
// General
//----------------------------------------
'form'            => 	'Form',
'form:home'       =>	'Channel Forms Home',
'form:docs'       =>	'Documentation',
'form:yes'        =>	'Yes',
'form:no'         =>	'No',
'form:none'       =>	'None',
'form:save'       =>	'Save',
'form:unknown'    =>	'Unknown',
'form:actions'    =>	'Actions',
'form:loading'	  =>	'Loading data, please wait..',
'form:desc'       =>	'Description',
'form:settings'   =>	'Settings',
'form:delete'     =>	'Delete',
'form:print'      =>	'Print',
'form:from'       =>	'From',
'form:to'         =>	'To',
'form:close'	  =>	'Close',
'form:cancel'	  =>	'Cancel',

'form:dropdown'	=>	'Dropdown',
'form:radio'	=>	'Radio Button',
'form:checkbox'	=>	'Check Box',
'form:multiselect' =>  'Multiselect',

'form:order_by' =>  'Order By',
'form:sort' =>  'Sort',
'form:asc'  =>  'ASC',
'form:desc' =>  'DESC',
'form:entry_date'=> 'Entry Date',
'form:show_expired' => 'Show Expired',

//----------------------------------------
// Fieldtype Settings
//----------------------------------------
'form:enabled'              =>	'Enabled',
'form:field'           =>	'Field',
'form:field_name'           =>	'Field Name',

'form:show_settings'        =>	'Default Settings',
'form:hide_settings'        =>	'Hide Settings',
'form:default_settings'     =>	'Default Settings',
'form:default_settings_exp' =>	'Any option choosen here will be applied to all future Forms. (Some options can be overriden on a per Form basis)',

//----------------------------------------
// PBF Specific
//----------------------------------------
'form:missing_settings' =>	'No Field Settings Found!',


//----------------------------------------
// FORM BUILDER
//----------------------------------------
'form:builder'		=>	'Form Builder',
'form:filter_fields'=>	'Filter Fields',
'form:form_settings'=>	'Form Settings',
'form:field_labels'	=>	'Field Labels',
'form:submit_button'=>	'Submit Button',
'form:email_admin'	=>	'Email: Admin',
'form:email_user'	=>	'Email: User',
'form:restrictions'	=>	'Form Restrictions',
'form:submit_flow'	=>	'Submission Flow',
'form:security'		=>	'Security',
'form:back'			=>	'Back..',

// Form Staging
'form:first_drop_exp'   =>	'Drag & Drop form elements here. Or click on any field.',
'form:save_settings'    =>	'Save Settings',
'form:saving_settings'  =>	'Saving, please wait...',
'form:field_settings'   =>	'Field Settings',
'form:field_spec_settings'=>	'Field Specific Settings',
'form:other_settings'   =>	'Other Settings',
'form:field_label'      =>	'Field Label',
'form:field_short_name' =>	'Field Short Name',
'form:field_desc'       =>	'Field Description',
'form:rules'            =>	'Rules',
'form:required'         =>	'Required',
'form:no_duplicates'    =>	'No Duplicates',
'form:placeholder'      =>	'Placeholder Text',
'form:enhanced_ui'      =>	'Enable Enhanced UI',

'form:id_css'           =>	'ID (CSS)',
'form:class_css'        =>	'Class (CSS)',
'form:style_css'        =>	'Style (CSS)',
'form:extra_attr'       =>	'Inline Attributes',
'form:validation_opt'   =>	'Validation',

// Form Builder Templates
'form:tmpl:none'		=>	'None',
'form:tmpl:predefined'	=>	'Predefined Template',
'form:tmpl:custom'		=>	'Custom',
'form:tmpl_predefined'	=>	'Prefined Templates',

// Form Builder Settings
'form:general'			=>	'General',
'form:restrictions'		=>	'Restrictions',
'form:label_placement'	=>	'Label Placement',
'form:desc_placement'	=>	'Description Placement',
'form:return_url'		=>	'Submission Return URL',
'form:place:top'		=>	'Above Inputs',
'form:place:left_align'	=>	'Left Aligned',
'form:place:right_align'=>	'Right Aligned',
'form:place:bottom'		=>	'Below Inputs',
'form:place:none'		=>	'Don\'t Show',
'form:limit_entries'	=>	'Limit number of entries',
'form:limit:total'		=>	'Total Entries',
'form:limit:day'		=>	'per day',
'form:limit:week'		=>	'per week',
'form:limit:month'		=>	'per month',
'form:limit:year'		=>	'per year',
'form:submit_button'	=>	'Submit Button',
'form:button:default'	=>	'Default',
'form:button:image'		=>	'Image',
'form:button:btext'		=>	'Button Text',
'form:button:btext_next'=>	'Next Page',
'form:button:bimg'		=>	'Image URL',
'form:button:bimg_next'	=>	'Next Page',
'form:form_enabled'		=>	'Form Enabled',
'form:max_entries'		=>	'Max Entries',
'form:open_fromto'		=>	'Open From-To',
'form:allow_mgroups'	=>	'Allowed Member Groups',
'form:multiple_entries'	=>	'Allow multiple submissions',

'form:post_submission'         =>	'Post Submission',
'form:success_msg_when'        =>	'Confirm Message Behaviour',
'form:success_msg'             =>	'Confirmation Message',
'form:redir_timeout'           =>   'Redirect Timeout',
'form:seconds'                 =>   'Seconds',
'form:success:before_redirect' =>	'Before Redirecting',
'form:success:after_redirect'  =>	'On the confirmation page',
'form:success:show_only'       =>	'Only Show Message (no redirect)',
'form:success:disabled'        =>	'Just Redirect (no confirmation)',
'form:success_show_form'       =>	'Show Form after submission',

'form:security'	=>	'Security',
'form:snaptcha'	=>	'Enable Snaptcha Support',
'form:force_https'=>	'Force HTTPS',

'form:3rdparty_submission'	=>	'Third Party Submission',
'form:third_party_flow'	=>	'Submission Flow',
'form:disabled'		=>	'Disabled',
'form:post_submit'	=>	'POST Submit',
'form:third_party_url'=>	'Third Party URL',
'form:as_is_field_id'		=>	'As is (Field ID)',
'form:field_identifier'	=>	'Field Identifier',
'form:save_fentry'	=>	'Save Form Submission',

'f:cp_dashboard'    =>  'CP Dashboard',
'f:dashboard:show'  =>  'Show in Dashboard',
'f:dashboard:group'  =>  'Only to certain member groups',

// Choices
'form:choices'    =>	'Choices',
'form:label'      =>	'Label',
'form:value'      =>	'Value',
'form:enable_values'=>	'Enable Values',
'form:remove_all'	=>	'Remove All',
'form:bulkadd'		=>	'Bulk Add / Predefined Choices',
'form:bulkchoice:exp'=>	'Select from one of the predefined lists and customize the choices or paste your own list to bulk add choices.',
'form:insert_choices'=>	'Insert Choices',

'form:show_label'	=>	'Show Field Label',
'form:label:auto'	=>	'Auto (Inherit)',
'form:label:show'	=>	'Always Show',
'form:label:hide'	=>	'Never Show',
'form:label_position'=>	'Label Position',

//----------------------------------------
// Submission Errors
//----------------------------------------
'form:ident'	=>	'Identifier',
'form:system_messages'	=>	'System Messages',
'form:message'	=>	'Messages',
'form:error:missing_data'		=>	'Missing Data.',
'form:error:not_authorized'		=>	'You are not authorized to perform this action',
'form:error:captcha_required'	=>	'You must submit the word that appears in the image',
'form:error:captcha_incorrect'	=>	'You did not submit the word exactly as it appears in the image',
'form:error:required_field'		=>	'You must specify a value for this required field.',
'form:error:invalid_date'       =>  'Invalid date specified.',
'f:err:invalid_phone'   =>  'Invalid Phone Number (numbers only please)',

//----------------------------------------
// MCP Speficic
//----------------------------------------
'f:debug_mode' => 'Enable Debug Mode',

'form:mcp'		=>	'Forms Control Panel',
'form:submissions'	=>	'Submissions',
'form:templates'	=>	'Email Templates',
'form:filter:form'	=>	'Filter by Form',
'form:filter:date'	=>	'Filter by Date',
'form:filter:keywords'	=>	'Filter by Keywords',
'form:filter:country'	=>	'Filter by Country',
'form:filter:members'	=>	'Filter by Members',
'form:date_from'	=>	'Date From',
'form:date_to'		=>	'Date To',
'form:keywords'		=>	'Keywords',
'form:id'			=>	'ID',
'form:fentry_id'	=>	'Submission ID',
'form:date'			=>	'Date',
'form:date_created'	=>	'Date Created',
'form:last_entry'	=>	'Last Submission',
'form:ip'			=>	'IP Address',
'form:member'		=>	'Member',
'form:country'		=>	'Country',
'form:guest'		=>	'Guest',
'form:submissions'	=>	'Submissions',
'form:type'			=>	'Type',
'form:export'		=>	'Export Entries',
'form:view_entry'	=>	'View Entry',
'form:filter_by'	=>	'Filter By',
'form:toggle_fields'=>	'Toggle Fields',
'form:form_new'		=>	'Create New Form',
'form:view_submissions'	=>	'View Submissions',
'form:edit_form'	=>	'Edit Form',
'form:delete_form'	=>	'Delete Form',
'form:dupe_form'	=>	'Duplicate Form',
'form:go_back'		=>	'Go Back..',
'form:no_forms'		=>	'No Forms have yet been created..',
'form:no_templates'	=>	'No Templates have yet been created...',
'form:print_fentry'	=>	'Print Form Entry',

// Export
'form:export:format'		=>	'Export Format',
'form:export:fields'		=>	'Form Fields',
'form:export:current_fields'=>	'Current visible fields',
'form:export:all_fields'	=>	'All available fields',
'form:export:entries'		=>	'Submissions',
'form:export:current_entries'	=>	'Current visible submissions',
'form:export:all_entries'	=>	'All submissions',
'form:export:delimiter'		=>	'Delimiter',
'form:export:comma'			=>	'Commas (,)',
'form:export:tabs'			=>	"Tabs (\t)",
'form:export:scolons'		=>	'Semi-colons (;)',
'form:export:pipes'			=>	'Pipes (|) ',
'form:export:enclosure'		=>	'Enclosure',
'form:export:none'			=>	'None',
'form:export:quote'			=>	'Single Quotes (\')',
'form:export:dblquote'		=>	'Double Quotes (")',
'form:export:include_header'=>	'Include Headers',
'form:export:member_info'	=>	'Member Field Info',
'form:export:screen_name'	=>	'Screenname',
'form:export:username'		=>	'Username',
'form:export:email'			=>	'Email Address',
'form:export:member_id'		=>	'Member ID',
'form:export:loading'		=>	'preparing export, please wait...',

// New Form
'form:form_name'	=>	'Form Label',
'form:form_url_title'=>	'Form Short Name',
'form:gen_info'		=>	'General Information',
'form:entry_linked'	=>	'Entry Linked',
'form:salone'		=>	'Stand Alone Forms',

// Lists
'form:lists'		=>	'Lists',
'form:list_label'	=>	'List Name',
'form:list_new'		=>	'New List',
'form:list_edit'	=>	'Edit List',
'form:list_del'		=>	'Delete List',
'form:no_lists'		=>	'No Lists have yet been created...',
'form:list_gen_info'=>	'General List Info',
'form:list_bulk'	=>	'Bulk Add/Edit/Remove',
'form:list:items'	=>	'List Items',
'form:option_setting_ex'	=>	'
Example 1: Option Label <br />
Example 2: option_value : Option Label
',

// Settings
'form:option'		=>	'Option',
'form:value'		=>	'Value',
'form:service'		=>	'Service',
'form:services_ext'	=>	'External Services',
'form:service_conf'	=>	'Service Configuration',
'form:recaptcha'	=>	'reCAPTCHA Settings',
'form:recaptcha:public'		=>	'reCAPTCHA Public Key',
'form:recaptcha:private'	=>	'reCAPTCHA Private Key',
'form:mailchimp_settings'	=>	'MailChimp Settings',
'form:createsend_settings'	=>	'Campaign Monitor Settings',
'form:api_key'		=>	'API Key',
'form:client_api_key'	=>	'Client ID',
'form:print_pdf'	=>	'PDF Printing',
'form:pdf_template_top'	=>	'PDF Template (Top)',
'form:pdf_template_loop'=>	'PDF Template (Loop)',
'form:paper_size'	=>	'Paper Size',
'form:orientation'	=>	'Paper Orientation',
'form:portrait'		=>	'Portrait',
'form:landscape'	=>	'Landscape',

'f:payment_prov'	=>	'Payment Providers',
'f:stripe'			=>	'Stripe',
'f:secret_key'		=>	'Secret Key',
'f:public_key'		=>	'Public Key',
'f:live'			=>	'Live',
'f:test'			=>	'Test',
'f:aunet'			=>	'Authorize.Net',
'f:cc_number'		=>	'Card Number',
'f:exp_date'		=>	'Expiration Date',
'f:ccv'				=>	'Security Code',
'f:cc_name'			=>	'Cardholder Name',
'f:test_mode'		=>	'Test Mode',
'f:ccs'				=>	'Credit Cards',
'f:currency'        =>  'Currency',
'f:month'			=>	'Month',
'f:year'			=>	'Year',
'f:email_customer'	=>	'Email Costumer',
'f:merchant_default'=>	'Merchant Default',
'f:payflow'			=>	'PayPal Payflow Pro',
'f:ignore_req_if_zero' => 'Only force required field if product/cart total is not zero',

'f:settings:general'=>  'General Settings',
'f:settings:option' =>   'Option',
'f:settings:value'  =>   'Value',
'f:settings:dshbrd_on'  =>  'Enable CP Dashboard',

//----------------------------------------
// Email Templates
//----------------------------------------
'form:tmpl'			=>	'Template',
'form:tmpl_label'	=>	'Template Label',
'form:tmpl_name'	=>	'Template Name',
'form:tmpl_new'		=>	'New Email Template',
'form:tmpl_edit'	=>	'Edit Template',
'form:tmpl_del'		=>	'Delete Template',
'form:tmpl_gen_info'		=>	'General Template Info',
'form:tmpl_email_info'		=>	'Email Template Info',
'form:tmpl:email:type'		=>	'Email Type',
'form:tmpl:email:wordwrap'	=>	'Wordwrap',
'form:tmpl:email:to'		=>	'To (Email Address)',
'form:tmpl:email:from'		=>	'From (Name)',
'form:tmpl:email:from_email'=>	'From (Email Address)',
'form:tmpl:email:reply_to'	=>	'Reply To (Name)',
'form:tmpl:email:reply_to_email'	=>	'Reply To (Email Address)',
'form:tmpl:email:reply_to_author'	=>	'Fill in "Reply To" with submission author info?',
'form:tmpl:email:subject'	=>	'Subject',
'form:tmpl:email:cc'		=>	'CC',
'form:tmpl:email:bcc'		=>	'BCC',
'form:tmpl:email:template'	=>	'Template',
'form:tmpl:email:send_attach'=>	'Send Attachments',
'form:tmpl:email:text'		=>	'Text ',
'form:tmpl:email:html'		=>	'HTML ',
'form:tmpl:user'	=>	'User Notification',
'form:tmpl:admin'	=>	'Admin Notification',

//----------------------------------------
// Fields Specific
//----------------------------------------
'form:legend'	=>	'Legend',
'form:column_width'	=>	'Column Width',

'form:hide_country'	=>	'Hide Country Field',
'form:hide_state'	=>	'Hide State / Province / Region Field',
'form:hide_address2'	=>	'Hide Address Line 2 Field',
'form:hide_zip'		=>	'Hide Zip',
'f:default_country' =>  'Default Country',
'form:street_addr'	=>	'Street Address',
'form:address2'		=>	'Address Line 2',
'form:city'			=>	'City',
'form:state_region'	=>	'State / Province / Region',
'form:zip_postal'	=>	'Zip / Postal Code',
'form:country'		=>	'Country',
'f:master_for'      =>  'Use field for',
'f:billing'          =>  'Billing',
'f:shipping'        =>  'Shipping',
'f:mailinglist'     =>  'Mailinglist',
'form:allowed_ext'	=>	'Allowed Extensions',
'form:max_filesize'	=>	'Max Filesize in MB',
'form:upload_destination'	=>	'Upload Destination',
'form:upload_error'	=>	'Upload Error: No file was uploaded or the upload itself failed!',
'form:filesize_exceed'=> 'The uploaded file likely exceeded the maximum file size allowed (Max: {size}MB) ',
'form:ext_not_allowed'=> 'The uploaded filetype is not allowed (Allowed: {ext})',
'form:default_options'	=>	'Default Options',
'form:option_setting_ex'	=>	'Example 1: Option Label <br />Example 2: option_value : Option Label',
'form:placeholder'		=>	'Placeholder Text',
'form:use_user_email'	=>	'Use Member Email',
'form:hide_if_member_email'=>'Hide this field if the member email can be used?',
'form:master_email'		=>	'Master Email Field?',
'form:add_email_to'     =>  'Add email to',
'form:user_template_cc' =>  'User Email Template: CC',
'form:user_template_bcc' =>  'User Email Template: BCC',
'form:admin_template_cc' =>  'Admin Email Template: CC',
'form:admin_template_bcc' =>  'Admin Email Template: BCC',
'form:not_email'		=>	'A valid E-Mail Address is required in this field.',
'form:max_chars'	=>	'Maximum Characters',
'form:default_val'	=>	'Default Value',
'form:password_field'	=>	'Enable Password Input',
'form:rows'			=>	'Rows',
'form:cols'			=>	'Cols',
'form:disabled'		=>	'Disabled',
'form:default_text'=>	'Default Text',
'form:captcha_loggedin'	=>	'Require CAPTCHA with logged-in members?',
'form:captcha_type'		=>	'Captcha Type',
'form:recaptcha'		=>	'reCAPTCHA',
'form:nucaptcha'		=>	'NuCaptcha',
'form:standard_capt'	=>	'Standard Captcha',
'form:math_chal'		=>	'Math Challenge',
'form:recaptcha_theme'	=>	'reCAPTCHA Theme',
'form:recaptcha_lang'	=>	'reCAPTCHA Language',
'form:recaptcha_error'	=>	'You must correctly submit both words that appear in the image',
'form:recaptch_simple_instr' => 'Please enter the word you see in the image',
'form:day'   =>	'Day',
'form:month' =>	'Month',
'form:year'  =>	'Year',
'form:date_input_type'	=>	'Date Input Type',
'form:datefield'		=>	'Date Field',
'form:datepicker'		=>	'Date Picker',
'form:dateselect'		=>	'Date Drop Down',
'form:date_format'		=>	'Date Format',
'form:datepicker_format'=>	'Datepicker Date Format',
'form:european'		=>	'European (DD/MM/YYYY)',
'form:american'		=>	'American (MM/DD/YYYY)',
'form:not_email'	=>	'A valid E-Mail Address is required in this field.',
'form:mailinglist_type'			=>	'Mailinglist Service',
'form:mailinglist:ee'			=>	'EE Mailinglist',
'form:mailinglist:mailchimp'	=>	'MailChimp',
'form:mailinglist:createsend'	=>	'Campaign Monitor',
'form:mailinglist:list'		=>	'Mailing List',
'f:checked_default'	=>	'Checked by default',
'form:subrtext'	=> 'Subscribe to our newsletter',
'form:subscribe_text'=>'Subscribe Text',
'form:first_name'	=>	'First Name',
'form:last_name'	=>	'Last Name',
'form:prefix'		=>	'Prefix',
'form:show_prefix'	=>	'Show Prefix',
'form:prefix_select'=>	'Show Prefix Dropdown',
'form:suffix'		=>	'Suffix',
'form:show_suffix'	=>	'Show Suffix',
'form:range'	=>	'Range',
'form:min'	=>	'Min',
'form:max'	=>	'Max',
'form:number_format'=>	'Number Format',
'form:decimals'		=> 'Decimals',
'form:thousands_sep'=> 'Thousands Separator',
'form:dec_point'	=> 'Decimal Point Separator',
'form:enforce'		=> 'Enforce',
'form:placeholder'	=> 'Placeholder Text',
'form:not_number'	=> 'Only numbers are allowed in this field',
'form:range_min_error'=> 'The minimum amount set for this field is: ',
'form:range_max_error'=> 'The maximum amount set for this field is: ',
'form:phone_format'	=>	'Phone Number Format',
'form:tel_usa'	=>	'USA/Canada (###) ###-####',
'form:tel_int'	=>	'International Format',
'form:phone_ext'=>	'Show Extension',
'form:phone_cc'=>	'Show Country Code',
'form:phone_area'=>	'Show Area Code',
'form:cc_code'		=>	'Country Code',
'form:area_code'	=>	'Area Code',
'form:phonenumber'	=>	'Phone Number',
'form:extension'	=>	'Extension',
'form:time_format'    =>	'Time Format',
'form:12h'            =>	'12-Hour',
'form:24h'            =>	'24-Hour',
'form:hour'           =>	'Hour',
'form:minute'         =>	'Minute',
'form:inv_time_format' => 'Invalid Time Format',
'form:select_cat_group'	=>	'Select a Category Group',
'form:limit_cat_groups'	=>	'Limit categories by Category Group',
'form:group_by_cat_group' =>	'Group categories by Category Group',
'form:what2store'		=>	'What value should be stored',
'form:form_element'		=>	'Form Element',
'form:cat_name'		=>	'Category Name',
'form:cat_id'		=>	'Category ID',
'form:limit_channel'	=>	'Limit entries by Channel',
'form:group_by_channel' =>	'Group entries by Channel',
'form:entry_title'	=>	'Entry Title',
'form:entry_urltitle'=>	'Entry URL Title',
'form:entry_id'		=>	'Entry ID',
'form:fboard'		=>	'Forum Board',
'form:limit_category'	=>	'Limit forums by Category',
'form:group_by_category' =>	'Group forums by Category',
'form:forum_id'		=>	'Forum ID',
'form:forum_name'	=>	'Forum Name',
'form:field_label'		=>	'Field Label',
'form:field_id'			=>	'Field ID',
'form:group_name'		=>	'Group Name',
'form:group_id'			=>	'Group ID',
'form:limit_mgroups'	=>	'Limit members by Member Group',
'form:group_by_mem_group' =>	'Group members by Member Group',
'form:screen_name'	=>	'Screen Name',
'form:username'		=>	'Username',
'form:email'		=>	'Email Address',
'form:member_id'	=>	'Member ID',
'form:html'			=>	'HTML Content',
'form:products'		=>	'Products',
'form:return_entryids'=>	'Just return entry id\'s ',

// Cart Tools
'f:products'	=>	'Products',
'f:product_options'=> 'Product Options',
'f:shipping_options'=> 'Shipping Options',
'f:price'		=>	'Price',
'f:fieldtype'	=>	'Field Type',
'f:single_product'	=>	'Single Product',
'f:single_method'	=>	'Single Method',
'f:dropdown'	=>	'Drop Down',
'f:radio_button'=>	'Radio Buttons',
'f:checkbox'	=>	'Check Boxes',
'f:user_price'	=>	'User Defined Price',
'f:entry_product'=> 'Entry Defined',
'f:hidden'		=>	'Hidden',
'f:input_field'	=>	'Text Box',
'f:display_tmpl'=>	'Display Template',
'f:product_label'=>	'Product Label',
'f:product_price'=>	'Product Price',
'f:shipping_label'=>'Shipping Label',
'f:shipping_price'=>'Shipping Price',
'f:show_qty'	=>	'Show Quantity Field',
'f:default_val'	=>	'Default Value',
'f:min_val'	=>	'Min Value',
'f:max_val'	=>	'Max Value',
'f:default_qty' =>  'Default Quantity',
'f:product_price_field'=>   'Product Price Field',
'f:product_label_field'=>   'Product Label Field',
'f:invalid_qty'=>	'Invalid Quantity',
'f:error:max_qty'	=>	'Max Quantity: ',
'f:error:min_qty'	=>	'Minimum Quantity: ',
'f:error:no_products'=>	'No products have been selected',
'f:error:no_stripe_token'=>	'Missing Stripe Token!!',

// Conditionals
'form:enable_cond'	=>	'Enable Conditional Logic',
'form:cond:this_field_id'=>	'this field if',
'form:conf:following_match'=> ' of the following match: ',
'form:condition'	=>	'Condition',

'form:help:enable_cond'		=>	'Create rules to dynamically display or hide this field based on values from another field.',
'form:help:phone_format' => 'Select the format you would like to use for display.',
'form:help:date_input_type' => 'Select the type of inputs you would like to use for the date field. Date Picker will let users select a date from a calendar. Date Field will let users free type the date.',
'form:help:max_chars'		=>	'Enter the maximum number of characters that this field is allowed to have',
'form:help:use_user_email'	=>	'Use the email address associated with the member account. (if logged in)',
'form:help:master_email'	=>	'Use the supplied email address as the email address for: sending email notification, signing up for mailing list etc.',
'form:help:allowed_ext'	=>	'Separated with commas (i.e. jpg, gif, png, pdf)',

//----------------------------------------
// Alert
//----------------------------------------
'form:alert:delete_form'	=>	"Are you sure you want to delete this Form? <br><br>All Fields & Form Submissions associated with this Form will also be deleted!",
'form:alert:delete_total'	=>	"Are you sure you want to delete? A total of <strong>{TOTAL}</strong> entries will be deleted.",
'form:alert:deleting'		=>	'deleting..',

//----------------------------------------
// HELP
//----------------------------------------
'form:help:debug_mode'    =>  'When enabled various debug data will be stored in a special debug field which can be displayed on the form entries page',
/*'form:help:form_tools'		=>	'Standard Fields provide basic form functionality.',
'form:help:power_tools'		=>	'Advanced Fields are for specific uses. They enable advanced functionality not normally found in other fields.',
'form:help:list_tools'		=>	'List Fields provide predefined dropdowns.',*/
'form:help:field_label'		=>	'Enter the label of the form field. This is the field title the user will see when filling out the form.',
'form:help:field_short_name'=>	'Enter the short name of the form field. This is never displayed to the user, but used in Email Templates.',
'form:help:field_desc'		=>	'Enter the description for the form field. This will be displayed to the user and provide some direction on how the field should be filled out or selected.',
'form:help:required_field'	=>	'Select this option to make the form field required. A required field will prevent the form from being submitted if it is not filled out or selected.',
'form:help:show_field_label'=>	'<strong>Auto</strong> = Respects the form field label settings<br><strong>Always Show</strong> = Overrides the field label settings and always displays the field label<br><strong>Never Show</strong> = Same as "Always Show" but this time hides the label',
'form:help:no_dupes'		=>	'Select this option to limit user input to unique values only. This will require that a value entered in a field does not currently exist in the entry database for that field.',
'form:help:visibility'		=>	'Select the visibility for this field.<br>Field visibility set to Everyone will be visible by the user submitting the form. Form field visibility set to Admin Only will only be visible within the administration tool.<br><br>Setting a field to Admin Only is useful for creating fields that can be used to set a status or priority level on submitted entries.',
'form:help:enhanced_ui'		=>	'By selecting this option, the <a href="http://harvesthq.github.com/chosen/" target="_blank">Chosen</a> jQuery script will be applied to this field, enabling search capabilities to Drop Down fields and a more user-friendly interface for Multi Select fields.',
'form:help:thousands_sep'	=>	'Specify which character to use to separate groups of thousands. For example, a value of , would parse 10000 as 10,000. Default is , (comma).',
'form:help:dec_point'		=>	'Specify which character to use to separate decimals. For example, a value of . would parse 100,00 as 100.00 Default is . (dot).',
'form:help:decimals'		=>	'Sets the number of decimal points. For example, a value of 2 would parse 100.124 as 100.12 Default is 2.',
'form:help:enforce'			=>	'Force the user to enter a correct format. If left unchecked we will accept any format and try to parse it.',
'form:help:enable_other_choice'=>'Check this option to add a text input as the final choice of your radio button field. This allows the user to specify a value that is not a predefined choice.',
'form:help:label_placement'	=>	'Select the label placement. Labels can be top aligned above a field, left aligned to the left of a field,right aligned to the left of a field, or at the bottom of the fields.',
'form:help:desc_placement'	=>	'Select the description placement. Descriptions can be placed above the field inputs or below the field inputs.',
'form:help:return_url'		=>	'Return url will override your default return url',
'form:help:success_msg'		=>	'Enter the text you would like the user to see on the confirmation page of this form.',
'form:help:choices'		=>	'Add Choices to this field. You can mark each choice as checked by default by using the radio/checkbox fields on the left.',
'form:help:enable_values'=>	'Check this option to specify a value for each choice. Choice values are not displayed to the user viewing the form, but are accessible to administrators when viewing the entry.',
'form:help:snaptcha'	=>	'<a href="http://devot-ee.com/add-ons/snaptcha">Snaptcha</a> (by: PutYourLightsOn - Ben Croker) <p style="font-style:italic; font-size:11px; margin:6px 0;">QUOTE:<br>Snaptcha (Simple Non-obtrusive Automated Public Turing test to tell Computers and Humans Apart) is an invisible captcha and will be the last time you will ever have to think about protecting your forms from spam bots.</p> Buy it at <a href="http://devot-ee.com/add-ons/snaptcha">Devot-EE</a>',

'form:help:default_val'		=>	'If you would like to pre-populate the value of a field, enter it here.
<br><br>
You can also use these variables:
<ul>
<li>{user:*} - Any user session variable is available here: See <a href="http://expressionengine.com/user_guide/development/usage/session.html">EE Dev Docs</a> for the complete list</li>
<li>{user:referrer} - The User\'s HTTP URL Referrer</li>
<li>{date:usa} - Current Date mm/dd/yyyy</li>
<li>{date:eu} - Current Date dd/mm/yyyy</li>
<li>{datetime:usa} - Current Date mm/dd/yyyy hh:mm am/pm</li>
<li>{datetime:eu} - Current Date dd/mm/yyyy HH:mm</li>
<li>{segment_*} - URL Segments</li>
<li>{last_segment} - Last URL Segments</li>
</ul>
',

'form:email_template_exp' =>'
<pre>
Variables
{form:label} - The Form Label
{form:short_name} - The Form short name
{form:id} - The Form ID
{user:*} - Any user session variable is available here: See <a href="http://expressionengine.com/user_guide/development/usage/session.html">EE Dev Docs</a> for the complete list
{user:referrer} - The User\'s HTTP URL Referrer
{date:usa} - Current Date mm/dd/yyyy
{date:eu} - Current Date dd/mm/yyyy
{datetime:usa} - Current Date mm/dd/yyyy hh:mm am/pm
{datetime:eu} - Current Date dd/mm/yyyy HH:mm

Form Fields Variables

{field:FIELD_NAME} - Replace FIELD_NAME with a fields short name
{form:fields} {/form:fields} - This variable pair loops over all fields in your form

Variables Available in side the pair

	{field:label} - The field label
	{field:short_name} - The field short name
	{field:value} - The submitted data for this field
	{field:count} - Sequence number


</pre>
',

















// Form Validations
'form:val:alpha'	=>	'Alphabetic Characters',
'form:val:alphanum'	=>	'Alphabetic & Numeric Characters',
'form:val:numbers'	=>	'Whole Numbers',
'form:val:float'	=>	'Number (decimals accepted)',
'form:val:email'	=>	'Email Address',
'form:val:url'		=>	'URL',



// Form Settings
'form:entry_submission'	=>	'Save Submissions As Entries',

//----------------------------------------
// Tools
//----------------------------------------
'form:page_tools'	=>	'Page Tools',
'form:form_tools'	=>	'Form Tools',
'form:power_tools'	=>	'Power Tools',
'form:list_tools'	=>	'List Tools',
'form:cart_tools'	=>	'Cart Tools',


// END
''=>''
);

/* End of file forms_lang.php */
/* Location: ./system/expressionengine/third_party/forms/forms_lang.php */
