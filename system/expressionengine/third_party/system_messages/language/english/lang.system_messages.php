<?php

/*
=====================================================
System Messages Extension for ExpressionEngine
-----------------------------------------------------
http://www.brianlitzinger.com
=====================================================
*/

$lang = array(

'extension_missing' =>
'The System Messages extension file cannot be found.',

'system_messages_settings' =>
'System Messages Settings',

'enable_ajax' =>
'Enable ajax responses?',

'enable_ajax_detail' =>
'Only disable if you implement your own message handling and don\'t want CSM to interfere. If you do not use the {exp:system_messages:form} plugin tag, then you can ignore this setting. <b>No longer officially supported. Use at your own risk.</b>',

'select_template_title' =>
'Override the User Message Template?',

'general_detail' =>
'Select a default template you would like to use for <b>general</b> errors. This will be used unless a Custom Action template is selected below.',

'submission_detail' =>
'Select a default template you would like to use for <b>submission</b> errors. This will be used unless a Custom Action template is selected below.',

'accessibility_detail' =>
'This will let you re-write the {link} variable for more flexibility and accessibility.',

'offline_detail' =>
'Select a template to serve as the <b>System Offline</b> template. All template tags will be processed as normal.',

'accessibility_label' =>
'Accessibility',

'general_label' =>
'General Error',

'submission_label' =>
'Submission Error',

'offline_label' =>
'Offline Message',

'bypass_success_label' =>
'Bypass the message template entirely on successful actions?',

'redirect_to_template_label' =>
'Redirect to the defined error template?',

'redirect_to_template_detail' =>
'This will redirect the user to the URI of the defined template. Normal behavior is to render all messages at the root of a site, e.g. yoursite.com/. 
This option lets you show the error messages at a custom URI, e.g. yoursite.com/pages/error, where <b>pages</b> is your template group, and <b>error</b> is your template. If you use an index template within a template group, <b>index</b> will not appear in the URI.',

'bypass_success_detail' =>
'This requires that the form have a valid return or redirect parameter. If so it will issue a redirect to that page instead of displaying any success page.',

'error_no_template' =>
'Please define a template in the Custom System Messages extension settings to use as the error template.',

'error_not_found' => 
'No templates defined for the Custom System Messages extension settings, or the defined templates can not be found.',

'accessibility_option_default' =>
'No Change (use JavaScript back link)', 

'accessibility_option_referrer' =>
'Better Accessibility (use HTTP Referrer)',

'accessibility_option_both' =>
'Show both links (shows default, plus referrer in &lt;noscript&gt; tag)',

'action' =>
'Action',

'template' =>
'Template',

'custom_actions_header' =>
'Custom Actions',

'custom_actions_detail' =>
'For even more control of your messages, you can select specific templates to be rendered for the system actions.
<i>(On Error)</i> indicates which template will be used when there is an error in the form.',

'no_settings' => 
'Custom System Messages has not been configured. Visit your Extensions page in the control panel and select an error template.',


// -- LG Addon Updater setting lines
'check_for_extension_updates_title' =>
'Check for System Messages updates?',

'check_for_extension_updates_info' =>
'System Messages can call home and check to see if the extension has been updated.',

'check_for_extension_updates_label' =>
'Would you like this extension to check for updates and display them on your CP homepage?',

'check_for_extension_updates_nolgau' =>
'This setting requires <strong><a href="http://leevigraham.com/cms-customisation/expressionengine/lg-addon-updater/">LG Addon Updater</a></strong>.',

// END
''=>''
);
?>