(function ($, undefined) {
'use strict';
  
  
  /**
   * The preview box for previewing entries directly from within the EE entry edit form
   * transition - an instance of Bwf.EditorTransition or Bwf.PublisherTransition
   */
  function PreviewBox (transition) {
    this.transition = transition;
    this.height;
    this.width;
    this.iframeHeight;
    this.iframeWidth;
    this.thirdpartyFields = [];
    this.thirdpartyFieldTypes = [];
    this.activeButton;
    this.previewWindowOpen = false;
  }
  

  PreviewBox.prototype = {

   /*
    * Start with render Function
    */
    render: function () {
      var self = this;
      this._setPreviewWindowDimensions();
      this._appendPreviewDialog();
      this._appendButton();
      $(window).resize(function() { 
      	if(!self.previewWindowOpen) {
      	    self._setPreviewWindowDimensions();
      	    $("#EpBwf_preview_dialog").dialog('option', 'height', self.height);
      	    $("#EpBwf_preview_dialog").dialog('option', 'width', self.width);
      	}
      });
    },


     _appendPreviewDialog: function () {
      var previewWindow = $("<div id='EpBwf_preview_dialog'></div>"),
          self = this;
  
      jQuery(previewWindow).insertAfter('#footer');
  
      //Prepare the preview DOM element for preview
      $("#EpBwf_preview_dialog").dialog({        
        autoOpen: false,
        resizable: false,
        modal: true,
        position: "center",
        title: "Better Workflow Preview",
        top: 100,
        height: self.height,
        width: self.width,
        close: function() {
          self._previewDialogClose();
        }
      });
    },


    _setPreviewWindowDimensions: function() {
      this.height = $(window).height() - 100;
      this.width = $(window).width() - 100;
      this.iframeHeight = (this.transition.externalPreviews) ? this.height - 80 : this.height - 60;
      this.iframeWidth = this.width - 30;
    },


    // Serialize the Form data appending the correct status action
    // Al we need to know is are we creating an entry / draft or updating an entry / draft
    _serializeForm: function () {
      var formData = $('#publishForm').serialize(),
          inputName,
          inputValue;
          
		// Is this a brand new entry - if yes we need to create a new entry record
		if(!this.transition.entryExists)
		{
          	// this.transition.defaultStatus
          	inputName = 'epBwfEntry_create_' + this.transition.defaultStatus;
          	inputValue = this.transition.defaultStatus;
        }
        // Do we have an unpublished entry - if yes we need to update the entry record
    	else if (this.transition.entryExists && this.transition.entryStatus != 'open')
        {
          	inputName = 'epBwfEntry_update_' + this.transition.entryStatus;
          	inputValue = this.transition.entryStatus;
        }
        // Do we have a published entry and no draft - if yes we need to create a new draft record
    	else if (this.transition.entryExists && this.transition.entryStatus == 'open' && !this.transition.draftExists)
        {
          	inputName = 'epBwfDraft_create_' + this.transition.defaultStatus;
          	inputValue = this.transition.defaultStatus + '|create';
        }
        // Otherwise we need to update the draft record
        else
        {
          	inputName = 'epBwfDraft_update_' + this.transition.draftStatus;
          	inputValue = this.transition.draftStatus + '|update';
        }
          
        // Append the selected action and return the form data
      	return formData + '&'+inputName+'='+ inputValue;
    },


    /*
    * Issues an ajax request to save the entry data when previewing an unsaved entry.
    *
    * isNewEntry - whether or not this is a new entry.
    * callback   - A callback function to be executed after.
    * returns nothing.
    */
    _saveEntryForPreview: function (isNewEntry, callback) {

      var self = this,
      transition = this.transition,
      errorText,
      isNewEntry = isNewEntry || false;
      
      Bwf.debug('Better Workflow: isNewEntry: \'' + isNewEntry + '\'');
      
      // Loop through the ajaxSave callbacks
      for(var field in Bwf.callbacks.ajaxSave)
      {
        if(typeof Bwf.callbacks['ajaxSave'][field] == 'function')
        {          
          Bwf.debug('Better Workflow: ajaxSave callback method called on: \'' + field + '\'');
          Bwf.callbacks['ajaxSave'][field].call();
        }
      }
  
      // Serialise the form data
      var formAction = $('#publishForm').attr('action'),
          formData = this._serializeForm(),
          newEntryId,
          newURLTitle;
  
      if (isNewEntry) {
        formData += '&bwf_ajax_new_entry=t';
      }
  
      // Fire off the AJAX to save the entry
      $.ajax({
        url: formAction,
        type: 'post',
        data: formData,
        context: $('#EpBwf_preview_dialog'),
        beforeSend: function() {
          $(this).append('<div id="epBwf_preview_spinner">&nbsp;</div>');
        },
  
        success: function(data) {
          // Check we have received valid JSON
          // If an error occured while trying to save the entry we're get a whole bunch of HTML
          if(self._isValidJSON(data))
          {
            $('#epBwf_preview_spinner').remove();
            data = data.length > 0 ? $.parseJSON(data) : null;
            newEntryId = data ? data.new_entry_id : null;
            newURLTitle = data ? data.new_url_title : null;
  
            if (newEntryId) {
              // append the entry_id to the form action url
              $('input[name="entry_id"]').val(newEntryId);
            }
  
            callback(newEntryId, newURLTitle);
          }
          else
          {
            // We have an error in the entry we're trying to save
            // We need to unbind the close behaviour so we do not update the publish page's DOM
            // We then need to rebind the event so they can preview properly once the problem has been resolved
            // Close the preview window and report a problem
            $("#EpBwf_preview_dialog").empty();
            $("#EpBwf_preview_dialog").dialog('option', 'close', null)
            $("#EpBwf_preview_dialog").dialog("close");
            $("#EpBwf_preview_dialog").dialog('option', 'close', function() { self._previewDialogClose(); })
            
            // Process response from AJAX and display message
            self._processError(data, transition.showErrors);
          }
        }
      });
    },


    /*
    * Replaces the default preview dialog's iframe with another one containing
    * the page to be previewed.
    *
    * entryId     - The id of entry to be previewed.
    * buttonValue - A reference to the preview button value.
    * returns nothing.
    */
    _appendIframe: function (newEntryId, newURLTitle, structurePreviewUrl, buttonValue) {
	var self = this,
	isStructureURL = false,
	shareDiv, 
	shareButton, 
	shareHidden,
	lastSegment = newEntryId; // Set a lastSegment variable to the value of the newEntryId
	
	if(this.transition.previewLastSegment == 'URL Title') lastSegment = newURLTitle;

	// We can't just find and replace on undefined - need both entryID and urlTitle depending on settings
	if (newEntryId) {
        buttonValue = buttonValue.replace(/undef_id/g, newEntryId);
        buttonValue = buttonValue.replace(/undefined/g, lastSegment);
		this.transition.previewTemplate = this.transition.previewTemplate.replace(/undefined/g, lastSegment);
		this.transition.entryId = newEntryId;
	}
	
	Bwf.debug('Better Workflow: Parsed preview template: \'' + this.transition.previewTemplate + '\'');
	Bwf.debug('Better Workflow: Parsed buttonValue: \'' + buttonValue + '\'');

	if (structurePreviewUrl && !this.transition.ignorePageUrl)
	{
		// Preserve the get vars by regex-ing the current button value
		// for everything after the question mark
		var getVars = buttonValue.match(/\?.*/);
		buttonValue = structurePreviewUrl + getVars;
		isStructureURL = true;
	}

	buttonValue = this.transition.baseURL + buttonValue;

	$('#EpBwf_preview_iframe').remove();
  
	// Now set an auth token, append it to the URL and launch the preview
	this._setAuthToken(buttonValue, isStructureURL, this.iframeHeight, this.iframeWidth, function (previewHeight, previewWidth, previewURL) {
		$("#EpBwf_preview_dialog").append('<iframe id="EpBwf_preview_iframe" height="' + previewHeight + '" width="' + previewWidth + '" frameborder="0" src="' + previewURL + '"></iframe>');
		if (self.transition.externalPreviews) {
			shareDiv = $('<div>').addClass('bwf_share');
			shareButton = $('<a>').addClass('bwf_share_button').text('Share draft').click(function(event) {
				$('.bwf_share_hide').css('display','block');
				$('#bwf_external_preview_url').focus();
				$('#bwf_external_preview_url').select();
				event.preventDefault();
			});
			shareHidden = $('<div>').addClass('bwf_share_hide').append('<input type="text" value="' + previewURL + '" id="bwf_external_preview_url" /><p>Paste this link in <strong>email</strong> or <strong>IM</strong>');
			shareDiv.append(shareButton).append(shareHidden);
			$("#EpBwf_preview_dialog").append(shareDiv);
			
			Bwf.debug('Better Workflow: append external preview URL controls to preview modal');
		}
	});
    },


   /* -------------------------------------------------------------------------------------------------
    * Preview process starts here
    *
    * Opens the preview window.
    * buttonValue - The value of the preview button.
    * returns nothing.
    * ------------------------------------------------------------------------------------------------- */
    _openPreviewWindow: function (buttonValue) {
      var self = this,
          isNewEntry;
      
      // Remove any messages that BWF might have added to the publish page on previous clicks of the submit button
      self._removeAllmessages();
  
      //check that there is an entry title, don't do anything without one
      if($("input#title").val() == "")
      {
        alert("Please enter a title before you preview");
      }
  
      else
      {
        // Set the flag so we know the window is open
        self.previewWindowOpen = true;
        
        //Remove any BWF message windows
        $('.bwf_message_dialog').remove();
        
        //open preview dialog
        $("#EpBwf_preview_dialog").dialog("open");
  
        // check whether we need to preview this entry using Structure or using the normal method.
        this._cmpStructure( function (structurePreviewUrl) {
          
          // Does the current preview button value end with 'undef_id'
          isNewEntry = /undef_id$/.test(buttonValue);
          
          self._saveEntryForPreview(isNewEntry, function (newEntryId, newURLTitle) {
            self._appendIframe(newEntryId, newURLTitle, structurePreviewUrl, buttonValue);
          });
        });
      }
    },


   /*
    * Sets an auth token in the database.
    * returns ID of auth token.
    * Add a timestamp to the AJAX URL so we don't get a cached result
    */
    _setAuthToken: function(buttonValue, isStructureURL, previewHeight, previewWidth, launchPreviewFunc) {
      var self = this,
      transition = this.transition,
      ajaxUrl = $('#publishForm').attr('action') + '&ajax_set_auth_token=y&rand=' + new Date().getTime(),
      humanReadableUrl,
      previewURL = '';
       
      $.getJSON(ajaxUrl, function(data)
      {
        // Define the preview URL
        previewURL = buttonValue + "&bwf_token_id=" + data.token_id + "&bwf_token=" + data.token;

        // Take the preview URL and add it to the preview window
        // Either full url with link or simplified human readable version
        if(transition.showFullUrl) {
            humanReadableUrl = previewURL;
        } else { 
            humanReadableUrl = previewURL.split('?')[0];
        }
        $("#EpBwf_preview_dialog").dialog('option', 'title', 'Better Workflow Preview [URL: ' +humanReadableUrl  + ']');
         
        launchPreviewFunc(previewHeight, previewWidth, previewURL);
      });
    },


   /*
    * Perform UI updates when preview is closed
    * Clear the HTML from the preview holder
    * Update DOM elements in the publish form so previewing again doesn't break things
    * returns nothing
    */
    _previewDialogClose: function ()
    {
	// Is the a brand new entry - if yes we need to create a new entry record
	if(!this.transition.entryExists)
	{
		this.transition.entryExists = true;
		this.transition.entryStatus = this.transition.defaultStatus;
	}
	// Do we have a published entry and no draft - if yes we need to create a new draft record
	else if(this.transition.entryExists && this.transition.entryStatus == 'open' && !this.transition.draftExists)
	{
		this.transition.draftExists = true;
		this.transition.draftStatus = this.transition.defaultStatus;
	}

	this.transition.previousState = this.transition.currentState;
	this.transition.currentState = this.transition.entryStatus + '|' + this.transition.draftStatus; 

	this._attachOverlay();
	this._writeMessageBox('epBwf_DOM_updating_message', 'Please wait a second or two, we\'re just updating the publish page');
	$("#EpBwf_preview_dialog").empty();
	this._getUpdatedPublishHTML();
    },


    /*
    * Performs an AJAX call on the publish page with the current/new entry ID to get the updated HTML
    * returns a huge string of HTML.
    */
    _getUpdatedPublishHTML: function() {
      var url = document.location.href,
          self = this;
  
      if (! /&entry_id/.test(url)) {
        url = url + "&entry_id=" + $('input[name="entry_id"]').val();
      }

      jQuery.ajax({
        type: "GET",
        url: url,
        timeout: 20000,
        contentType: "application/x-www-form-urlencoded;charset=ISO-8859-15",
        dataType: 'html',
        success: function(html){
          self._updatePublishView(html);
        }
      });
    },


    /*
    * Makes the necessary changes to the publish form DOM so that
    * that the form will reflect the state of the updated entry record in the DB.
    * returns nothing.
    */
    _updatePublishView: function (html) {
	var i,
	  matrixReInit = true,
	  self = this,
	  url = document.location.href,
	  callbackFunc;

	// Add some debugging information to the console
	Bwf.debug('Better Workflow: _updatePublishView() method called in preview.js');
	Bwf.debug('Better Workflow: BWF Draft exists for this entry: \'' + this.transition.draftExists + '\'');

	// Remove any errors
	$('.bwf_field_error').removeClass('bwf_field_error');

	// If the current state is different from the old state we need to update the controls
	if(this.transition.currentState != this.transition.previousState)
	{
		this.transition.uiButtons.render();
		this.transition.uiPreview.render();
	}
	
	// Loop through the callbacks
	for(var field in Bwf.callbacks.previewClose)
	{
		this.thirdpartyFields.push(field);
	}

	// Reload the field type html into the DOM
	for(i=0; i<this.thirdpartyFields.length; i++)
	{
		this._reloadThirdPartyField(this.thirdpartyFields[i], html);
	}

	// Fire the previewClose callback function
	for(i=0; i<this.thirdpartyFields.length; i++)
	{
	  if(typeof Bwf.callbacks['previewClose'][this.thirdpartyFields[i]] == 'function')
	  {
	    Bwf.callbacks['previewClose'][this.thirdpartyFields[i]].call();

	    // Add some debugging information to the console
	    Bwf.debug('Better Workflow: previewClose callback method called on: \'' + this.thirdpartyFields[i] + '\'');
	  }
	}

	// Now kill the updating message and overlay
	$('#epBwf_DOM_updating_message').remove();
	$('#bwf_overlay').remove();

	// Reset the self.previewWindowOpen flag so we can re-enable resizing
	this.previewWindowOpen = false;

	// Empty the thirdpartyFields array ready for the next preview
	this.thirdpartyFields = [];
	this.thirdpartyFieldTypes = [];
    },


    /*
    * Find the relevant block of html in the updated publish page and injects them into the holding elements on the old page.
    * returns nothing.
    */
    _reloadThirdPartyField: function(fieldName, html) {
      var self = this,
          field_id,
          div_id,
          input_id,
          replacement,
          fieldType;
          
      fieldType = fieldName.split('_');
      fieldType = fieldType.slice(0, fieldType.length - 1).join('_');
      
      if ($.inArray(fieldType, self.thirdpartyFieldTypes) == -1)
      {
	      $(html).find('.publish_' + fieldType).each(function(index, field) {
	        field_id = $(field).attr('id');
	        div_id = '#sub_'+ field_id;
	        input_id = 'field_id_' + field_id.replace(/hold_field_/,'');
	        replacement = $(field).find(div_id).html();
	        
	        $(div_id).html(replacement);
	        // Check to see if BWF has previously added an empty input
	        // If we find one get rid of it
	        if($('#'+input_id+'.bwf_temp_blank_matrix_input').length > 0) {
	          $('#'+input_id+'.bwf_temp_blank_matrix_input').remove();
	        }
	      });
	     self.thirdpartyFieldTypes.push(fieldType);
	   }
    },


    /*
    * Appends the preview button to the Workflow widget container
    * returns nothing.
    */
    _appendButton: function () {
      var transition = this.transition,
          workflowStatus = transition.draftStatus || transition.entryStatus || transition.defaultStatus,
          previewEntryId = (transition.entryId != null) ?  transition.entryId : 'undef_id' ,
          previewTemplate = transition.previewTemplate,
          self = this;
  
      // TODO: Do not hard code this - needs to be governed by role data
      // We want the preview button in almost all cases, however if this is an editor and the draft is submitted, don't display it.
      if (workflowStatus === 'submitted' && transition.userRole === 'editor') {
        return; //this exits from the function
      }
  
      // Draft template should either end with a numeric id or with undefined
      //if (!/(\d+)$/.test(transition.previewTemplate) && !/undefined$/.test(transition.previewTemplate)) {
      //  transition.previewTemplate += 'undefined';
      //}
  
      // Do we have an entry ID - if not make sure the disablePreview is set to Never
      // If we do have an entry ID - make sure the the disablePreview is not set to Always 
      if(
      	($('input[name="entry_id"]').val() != 0 && transition.disablePreview != 'Always') || 
      	($('input[name="entry_id"]').val() == 0 && transition.disablePreview == 'Never')
      	)
      {
        $('.bwf_control_unit ul').append('<li><button class="bwf_grey_button bwf_save_button" value="' + transition.previewTemplate + '?bwf_dp=t&bwf_entry_id=' + previewEntryId + '">Save and preview</button>&nbsp;&nbsp;|</li>');
      }
  
      // This is where we append the click event to the preview button **Super Important**
      $('.bwf_grey_button').click(function (event) {
        self._openPreviewWindow($(this).val(), self.previewIframeHeight, self.previewIframeWidth);
        event.preventDefault();
      });
     },


    /*
     * Compatibility method for Structure.
     * Checks if an entry has a structure url and and if so passes that to the
     * continueCallback function.
     */
     _cmpStructure: function (continueCallback) {
         var channelId = $('input[name="channel_id"]').val(),
             entryId = $('input[name="entry_id"]').val(),
             structurePreviewUrl;
          
         if (!entryId) {
           continueCallback();
         }
         else {
           structurePreviewUrl = $('#publishForm').attr('action') + '&ajax_structure_get_entry_url&channel_id='+channelId+'&entry_id='+entryId;
            
           $.getJSON(structurePreviewUrl, function(data) {
             continueCallback(data.structure_url);
           });
         }
     },


     _isValidJSON: function(value) {
       try {
         jQuery.parseJSON(value);
         return true;
       } catch(e) {
         return false;
       }
     },


     _writeMessageBox: function(id, message) {
       var messageBox = $("<div class='bwf_message_dialog' id='" + id + "'>" + message + "</div>");
       $(messageBox).insertAfter('#footer').css("position", "absolute").css("top", (($(window).height() - messageBox.outerHeight()) / 2) + $(window).scrollTop() + "px").css("left", (($(window).width() - messageBox.outerWidth()) / 2) + $(window).scrollLeft() + "px");      
       $(messageBox).find('.close').first().click(function(event){
         event.preventDefault();
         $(messageBox).remove();
       });
     },


     //_updateMessageBox: function(id, message) {
     //  var obj = $("#" + id);
     //  if (obj.length > 0) {
     //    obj.css("background","#ECF1F4").css("padding","20px").html(message);
     //  }
     //  else {
     //   this._writeMessageBox(id, message);
     //  }
     //},


     _attachOverlay: function() {
       var overlay = $("<div id='bwf_overlay'></div>");
       $(overlay).insertAfter('#footer').css("height", $("body").height()).css("width", $(window).width());
     },


     _processError: function(data, showErrors) {
       var parts,
       actualErrorMessage,
       parentField,
       parentObj,
       hasError = false,
       message = '';
       
       // Do we have the publish view HTML? If so we do not want to display it
       if (/\<\!DOCTYPE/.test(data)) {
         // Split the string on the doctype
         parts = data.split('<!DOCTYPE');
         if(parts[0].length > 0) {
           hasError = true;
           message = parts[0];
         } else {
           jQuery(data).find('.notice').each(function() {
             actualErrorMessage = $(this).text();
             parentField = $(this).parents('.publish_field');
             parentObj = $('#'+ parentField.attr('id'));
             parentObj.addClass('bwf_field_error');
             parentObj.append('<div class="bwf_field_error_notice">' + actualErrorMessage + '</div>');

             // Add the actual error message to the alert box
             message += '<li>' + actualErrorMessage + '</li>';
           });
           message = '<p>Please check the following:</p><ul class="req-fields">' + message + '</ul>';
         }
       } else {
         hasError = true;
         message = data;
       }
       // If we have show errors set to true
       if (showErrors) {
         message = '<a class="close">[x]</a><h2>Whoops! It looks like there\'s a problem generating your preview</h2><p>The following error occured</p><div class=\"error\">' + message + '</div>';
       } else {
         if(hasError) {
           message = '<a class="close">[x]</a><h2>Whoops! It looks like there\'s a problem generating your preview</h2><p>This might be caused by required fields being empty or it may be a compatibility issue with one of the third-party add-ons you have installed.</p><p>Please check the page for required fields and the <a href="http://betterworkflow.electricputty.co.uk/documentation.html" target="_blank">Better Workflow documentation</a> for a list of supported add-ons</p>';
         } else {
           message = '<a class="close">[x]</a><h2>Whoops! It looks like there\'s a problem generating your preview</h2><div class=\"error\">' + message + '</div>';
         }
       }
       this._writeMessageBox('epBwf_DOM_warning_message', message);
     },


     _removeAllmessages: function() {
       $('div.bwf_field_error').each(function() {
         $(this).removeClass('bwf_field_error');
         $(this).find('div.bwf_field_error_notice').each(function() {
           $(this).remove();
         });
       });
     }
  
  };


  this.PreviewBox = PreviewBox;
  
}).call(Bwf, jQuery, undefined);
