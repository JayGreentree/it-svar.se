<?php

if (! defined('CSM_VERSION'))
{
    // get the version from config.php
    require PATH_THIRD.'system_messages/config.php';
    define('CSM_VERSION', $config['version']);
    define('CSM_NAME', $config['name']);
    define('CSM_DESCRIPTION', $config['description']);
}

/**
 * ExpressionEngine 2.x Custom System Messages Plugin
 *
 * @package     ExpressionEngine
 * @subpackage  Extensions
 * @category    Custom System Messages
 * @author      Brian Litzinger
 * @copyright   Copyright (c) 2010, 2011 - Brian Litzinger
 * @link        http://boldminded.com/add-ons/csm
 * @license 
 *
 * Copyright (c) 2011, 2012. BoldMinded, LLC
 * All rights reserved.
 *
 * This source is commercial software. Use of this software requires a
 * site license for each domain it is used on. Use of this software or any
 * of its source code without express written permission in the form of
 * a purchased commercial or other license is prohibited.
 *
 * THIS CODE AND INFORMATION ARE PROVIDED "AS IS" WITHOUT WARRANTY OF ANY
 * KIND, EITHER EXPRESSED OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND/OR FITNESS FOR A
 * PARTICULAR PURPOSE.
 *
 * As part of the license agreement for this software, all modifications
 * to this source must be submitted to the original author for review and
 * possible inclusion in future releases. No compensation will be provided
 * for patches, although where possible we will attribute each contribution
 * in file revision notes. Submitting such modifications constitutes
 * assignment of copyright to the original author (Brian Litzinger and
 * BoldMinded, LLC) for such modifications. If you do not wish to assign
 * copyright to the original author, your license to  use and modify this
 * source is null and void. Use of this software constitutes your agreement
 * to this clause.
 */

$plugin_info = array(
    'pi_name'       => CSM_NAME,
    'pi_version'    => CSM_VERSION,
    'pi_description'=> CSM_DESCRIPTION,
    'pi_author_url' => 'http://boldminded.com/add-ons/csm/',
    'pi_description'=> 'Adds jQuery to the page for loading CSM messages in jQuery UI Dialog window.',
    'pi_usage'      => System_messages::usage()
);


class System_messages {

    public $return_data;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->EE =& get_instance();
        
        // Create cache
        if (! isset($this->EE->session->cache['csm']))
        {
            $this->EE->session->cache['csm'] = array();
        }
        $this->cache =& $this->EE->session->cache['csm'];
    }
    
    public function form()
    {
        $tagdata = $this->EE->TMPL->tagdata;
        
        $params = array(
            'close_button' => $this->EE->TMPL->fetch_param('close_button', 'Close'),
            'width' => $this->EE->TMPL->fetch_param('width', '400'),
            
            'modal_container' => $this->EE->TMPL->fetch_param('modal_container', 'modal_container'),
            'message_container' => $this->EE->TMPL->fetch_param('message_container', ''),
            
            // Show the message on a successful form submission.
            'message_on_success' => $this->EE->TMPL->fetch_param('message_on_success', 'yes'),
            
            // Show message, then redirect on a successful form submission
            'redirect_on_success' => $this->EE->TMPL->fetch_param('redirect_on_success', 'yes'),
            
            // Custom text displayed after the message used to indicate to the user that a redirect is about to happen
            'redirect_link_text' => $this->EE->TMPL->fetch_param('redirect_link_text', ''),
            
            // Override form's built in redirect method if needed
            'redirect_to' => $this->EE->TMPL->fetch_param('redirect_to', ''),
            
            // Params to change the HTML elements used in the message display
            'message_container_tag' => $this->EE->TMPL->fetch_param('message_container_tag', 'div'),
            'message_container_tag_attributes' => $this->EE->TMPL->fetch_param('message_container_tag_attributes', ''),
            
            'message_header_tag' => $this->EE->TMPL->fetch_param('message_header_tag', 'h3'),
            'message_header_tag_attributes' => $this->EE->TMPL->fetch_param('message_header_tag_attributes', ''),
            'message_header_tag_show' => $this->EE->TMPL->fetch_param('message_header_tag_show', 'yes'),
            
            'message_error_tag' => $this->EE->TMPL->fetch_param('message_error_tag', 'ol'),
            'message_error_tag_attributes' => $this->EE->TMPL->fetch_param('message_error_tag_attributes', ''),
            
            'site_url' => $this->EE->TMPL->fetch_param('site_url', $this->EE->config->slash_item('site_url'))
        );
        
        // Create a unique identifier if multiple forms are on the same page with different params.
        $form_id = uniqid();
        
        $js = '<script type="text/javascript">'.NL.'if (typeof window.CSM_'. $form_id .' == "undefined") window.CSM_'. $form_id .' = {};'.NL;
        
        foreach($params as $param => $value)
        {
            $js .= 'CSM_'. $form_id .'.'. $param .'="'. $value .'";'.NL;
        }
        
        $js .= '</script>';
        
        // add our parameters so the JS below can use them
        $this->return_data = '<div class="system_messages_form" data-csm-form-id="CSM_'. $form_id .'">'. $js . $tagdata .'</div>';

        return $this->return_data;
    }
    
    public function js()
    {
        $include_ui_css = $this->EE->TMPL->fetch_param('include_ui_css', false);
        $jq_version = $this->EE->TMPL->fetch_param('jq_version', '1.5.2');
        $ui_version = $this->EE->TMPL->fetch_param('ui_version', '1.8.12');
        $redirect_timeout = $this->EE->TMPL->fetch_param('redirect_timeout', '4000');
        $load_jquery = $this->EE->TMPL->fetch_param('load_jquery', 'yes');
        
        $this->cache['js_included'] = true;
        
        $this->return_data = '<!-- BEGIN: CSM script -->';
        
        if($include_ui_css == 'yes')
        {
            $this->return_data .= '<link type="text/css" rel="stylesheet" href="'. $this->EE->config->slash_item('theme_folder_url') .'cp_themes/default/css/jquery-ui-1.8.16.custom.css" />';
        }

        $this->return_data .= '
        <script type="text/javascript">

        var csm_jq_loaded = false;
        var csm_ui_loaded = false;
        
        function init_csm() {

            if( typeof(jQuery) == "undefined" || !jQuery.ui) 
            {
                if(!csm_jq_loaded && typeof(jQuery) == "undefined" && "'. $load_jquery .'" != "no") 
                {
                    csm_jq_loaded = true;

                    var s = document.createElement("script");
                    s.setAttribute("src", "http://ajax.googleapis.com/ajax/libs/jquery/'. $jq_version .'/jquery.min.js");
                    s.setAttribute("type", "text/javascript");
                    document.getElementsByTagName("head")[0].appendChild(s);
                }

                if(!csm_ui_loaded && typeof(jQuery) == "function" && !jQuery.ui && "'. $load_jquery .'" != "no") 
                {
                    csm_ui_loaded = true;

                    var s = document.createElement("script");
                    s.setAttribute("src", "https://ajax.googleapis.com/ajax/libs/jqueryui/'. $ui_version .'/jquery-ui.min.js");
                    s.setAttribute("type", "text/javascript");
                    document.getElementsByTagName("head")[0].appendChild(s);
                }
                
                setTimeout("init_csm()", 50);
            } 
            else 
            {
                jQuery(function(){
                    EEAction.init();
                });
            }
        }
        
        init_csm();
        
        var EEAction = {
            wrapper : "",
            form    : "",
            action  : "",
            form_id : "",
            init: function() {

                var self = this;
                
                jQuery("div.system_messages_form").each(function()
                {
                    var form = jQuery(this);
                    form.action = form.find("input[name=\'ACT\']").val();
                    form.form_id = self.get_data(form, "csm-form-id");

                    if (typeof(csm_form_hook) !== "undefined" && typeof(csm_form_hook) === "function") {
                        form = csm_form_hook(form);
                    }
                    
                    if(form.action)
                    {
                        var params = {};
                        for(param in window[form.form_id])
                        {
                            params[param] = window[form.form_id][param];
                        }
                        
                        form.params = params;
                        form.params.redirect_to = form.params.redirect_to ? form.params.redirect_to : form.find("input[name=\'RET\']").val();

                        if (typeof(csm_pre_submit_hook) !== "undefined" && typeof(csm_pre_submit_hook) === "function") {
                            self.params = csm_pre_submit_hook(self.params);
                        }

                        form.bind("submit", function(event){
                            var data = form.find("form").serialize();
                            var success = false;
                            var return_data = "";

                            if (typeof(csm_post_submit_hook) !== "undefined" && typeof(csm_post_submit_hook) === "function") {
                                data = csm_post_submit_hook(data);
                            }
                            
                            jQuery.ajax({
                                type: "POST",
                                url: form.params.site_url +"?ACT="+ form.action,
                                data: data,
                                async: false,
                                error : function(jqXHR, textStatus, errorThrown)
                                {
                                    self.throw_error(self);
                                },
                                success: function(data, textStatus, jqXHR){
                                    
                                    return_data = data;
                                    success = return_data.success;

                                    if(typeof return_data != "object")
                                    {
                                        self.throw_error(form);
                                    }
                                    else
                                    {
                                        if(success && form.params.message_on_success == "no")
                                        {
                                            /* do nothing intenionally, the form will process as usual */
                                        }
                                        else
                                        {
                                            if(typeof(csm_success_hook) !== "undefined" && typeof(csm_success_hook) === "function") 
                                            {
                                                return_data = csm_success_hook(return_data);
                                            }
                                            
                                            if(form.params.redirect_link_text != "" && return_data.success == true)
                                            {
                                                return_data.content += form.params.redirect_link_text;
                                            }
                                            
                                            if(jQuery(form.params.message_container).length > 0)
                                            {
                                                EEMessageHandler.showMessage(form, return_data);
                                            }
                                            else
                                            { 
                                                EEMessageHandler.openModal(form, return_data);
                                            }
                                        }
                                    }
                                }
                            });
                            
                            if(success)
                            {
                                if(form.params.redirect_on_success == "yes")
                                {
                                    event.preventDefault();
                                    
                                    var timeout = (return_data.content == "" || form.params.message_on_success == "no") ? 0 : '. $redirect_timeout .';
                                    
                                    setTimeout({
                                        run: function() {
                                            window.location = form.params.redirect_to;
                                        }
                                    }.run, timeout);
                                }
                            }
                            else
                            {
                                event.preventDefault();
                            }
                        });
                    }
                });
            },
            
            throw_error: function(form) {

                var return_data = {
                    heading: "AJAX Error",
                    content: "The response was not a valid JSON object."
                };
                
                if(jQuery(form.params.message_container).length > 0)
                {
                    EEMessageHandler.showMessage(form, return_data);
                }
                else
                { 
                    EEMessageHandler.openModal(form, return_data);
                }
            },
            
            get_data: function(ele, key, default_value) {
                return ele.data(key) || ele.attr("data-"+ key) || default_value;
            }
        };

        var EEMessageHandler = {
            showMessage: function(form, data) {
                var params = form.params;
                if(jQuery(params.message_container).length > 0)
                {
                    var modalHtml = \'<\'+ params.message_container_tag +\' \'+ params.message_container_tag_attributes +\'>\';
                    
                    if(params.message_header_tag_show == "yes") {
                        modalHtml = modalHtml + \'<\'+ params.message_header_tag +\' \'+ params.message_header_tag_attributes +\'>\'+ data.heading +\'</\'+ params.message_header_tag +\'>\';
                    }
                    
                    modalHtml = modalHtml + this.replaceContent(data.content, params.message_error_tag, params.message_error_tag_attributes);
                    
                    if (typeof(csm_show_message_hook) !== "undefined" && typeof(csm_show_message_hook) === "function") {
                        modalHtml = csm_show_message_hook(modalHtml);
                    }
                                    
                    jQuery(params.message_container).html(modalHtml);
                }
            },
            openModal: function(form, data) {
                var params = form.params;
                var modalHtml = \'<\'+ params.message_container_tag +\' \'+ params.message_container_tag_attributes +\'>\';
                
                if(params.message_header_tag_show == "yes") {
                    modalHtml = modalHtml + \'<\'+ params.message_header_tag +\' \'+ params.message_header_tag_attributes +\'>\'+ data.heading +\'</\'+ params.message_header_tag +\'>\';
                }
                
                modalHtml = modalHtml + this.replaceContent(data.content, params.message_error_tag, params.message_error_tag_attributes);
                modalHtml = modalHtml + \' <a href="#" class="csm-close">\'+ params.close_button +\'</a></\'+ params.message_container_tag +\'>\';
                
                if (typeof(csm_show_modal_hook) !== "undefined" && typeof(csm_show_modal_hook) === "function") {
                    modalHtml = csm_show_modal_hook(modalHtml);
                }

                if (!jQuery("div."+ params.modal_container).length) 
                {
                    jQuery("body").append(\'<div class="\'+ params.modal_container +\'"></div>\');                    
                }      
                
                jQuery("div."+ params.modal_container).dialog({
                    autoOpen: true,
                    width: parseInt(params.width),
                    position: "middle",
                    modal: true,
                    resizable: false,
                    closeOnEscape: true,
                    draggable: true,
                    open: function() {
                        jQuery(this).html(modalHtml);
                    }
                });

                jQuery("a.csm-close").live("click", function(){
                    jQuery("div."+ params.modal_container).remove();
                    $(form)[0].reset();
                    event.preventDefault();
                });
            },
            replaceContent: function(content, tag, attr) {
                content = content.replace(\'<ul>\', \'<ul \'+ attr +\'>\');
                content = content.replace(/\<(ul(.*?))\>/, \'<\'+ tag +\' \'+ attr +\'>\');
                content = content.replace(/\<(\/ul)\>/, \'</\'+ tag +\'>\');
                return content;
            }
        }
        </script>
        <!-- END: CSM script -->';

        return $this->return_data;
        // return preg_replace("/\s+/", " ", $this->return_data);
    }

    /**
     * Plugin Usage
     */
    public static function usage()
    {
        ob_start();
?>

See http://boldminded.com/docs/csm

<?php
    $buffer = ob_get_contents();
    ob_end_clean();
    return $buffer;
    }
}


/*
var params = {
    is_ajax: self.get_data("is-ajax", "no"),
    
    close_button: self.get_data("close-button", "Close"),
    width: self.get_data("width", "600"),
    
    modal_container: self.get_data("modal-container", "modal_container"),
    message_container: self.get_data("message-container", ""),
    message_on_success: self.get_data("message-on-success", "no"),
    
    redirect_on_success: self.get_data("redirect-on-success", "yes"),
    redirect_link_text: self.get_data("redirect-link-text", ""),
    redirect_to: self.get_data("redirect-to", self.wrapper.find("input[name=\'RET\']").val()),
    
    message_container_tag: self.get_data("message-container-tag", "section"),
    message_container_tag_attributes: self.get_data("message-container-tag-attributes", ""),
    
    message_header_tag: self.get_data("message-header-tag", "h3"),
    message_header_tag_attributes: self.get_data("message-header-tag-attributes", ""),
    message_header_tag_show: self.get_data("message-header-tag-show", "yes"),
    
    message_error_tag: self.get_data("message-error-tag", "div"),
    message_error_tag_attributes: self.get_data("message-error-tag-attributes", ""),
    
    site_url: self.get_data("site-url", "/")
};
*/