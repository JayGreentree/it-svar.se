(function () {
'use strict';


// Bind the internal ajaxSave callback compatibility functions 
if (typeof(Bwf) != 'undefined')
{  
  Bwf.bind('bwf_epEditor', 'ajaxSave', function(){
    Bwf._cmpEpEditor();
  });
  
  Bwf.bind('bwf_wygwam', 'ajaxSave', function(){
    Bwf._cmpwygWam();
  });
  
  Bwf.bind('bwf_matrix', 'ajaxSave', function(){
    Bwf._cmpMatrix();
  });
  
  Bwf.bind('bwf_eeRTR', 'ajaxSave', function(){
    Bwf._cmpeeRTE();
  });
}


Bwf._cmpEpEditor = function()
{
  $('.epEditorContent').each(function(i,item) {
    var editorId = jQuery(item).attr('id'),
    fieldId,
    fieldValue;

    // Check for the different version of the epEditor - older ones don't use the underscore
    if (editorId.indexOf("_epEditorIFrame") != -1) {
      fieldId = editorId.replace(/_epEditorIFrame/,'');
    } else {
      fieldId = editorId.replace(/epEditorIFrame/,'');
    }
	
    fieldValue = jQuery(item).get(0).contentWindow.document.body.innerHTML;
    $('input[name="'+fieldId+'"]').val(fieldValue);
    
    Bwf.debug('Better Workflow: EpEditor field '+fieldId+' processed pre ajax save');
  });
};


Bwf._cmpwygWam = function()
{
  var self = this;
  // Get all regular, full Wygwam fields
  $('.cke_editor iframe, .cke_editor textarea').each(function(i,item) {
    var fieldId = $(item).parent().attr('id').replace(/cke_contents_/,'');
    var fieldValue = self._getEditorContents($(item));
    self._setEditorContents($('#'+fieldId),fieldValue);
    
    Bwf.debug('Better Workflow: CKEditor based field '+fieldId+' processed pre ajax save');
  });
  
  // Get all wygwam fields placed inside a Matrix row
  $('td.matrix td[id^="cke_contents_"] iframe, td.matrix td[id^="cke_contents_"] textarea').each(function(i,item) {
    var matrixParentCell = $(this).parents('td.matrix');
    var fieldId = matrixParentCell.children('textarea').attr('id').replace(/cke_contents_/,'');
    var fieldValue = self._getEditorContents($(item));
    self._setEditorContents($('#'+fieldId), fieldValue);

    Bwf.debug('Better Workflow: CKEditor based field '+fieldId+' within a Matrix row processed pre ajax save');
  });
};


Bwf._cmpeeRTE = function()
{
  var self = this;
  // Get all EE RTE fields
  $('.WysiHat-editor').each(function(i,item) {
    var fieldId = $(this).next('textarea').attr('id');
    var fieldValue = $(item).html();
    self._setEditorContents($('#'+fieldId), fieldValue);
    
    Bwf.debug('Better Workflow: EE RTE based field '+fieldId+' processed pre ajax save');
  });
};


Bwf._getEditorContents = function(obj)
{
  if(obj.get(0).nodeName.toLowerCase() == 'iframe') {
    return obj.get(0).contentWindow.document.body.innerHTML;
  }
  if(obj.get(0).nodeName.toLowerCase() == 'textarea') {
    return obj.val();
  }
};


Bwf._setEditorContents = function(obj, value)
{
  // If this is just a [br] tag remove it
  if(value == '<br>') value = '';
         
  // As ever, IE does its own thing here
  if($.browser.msie) {
    obj.val(value);
  } else {
    if(obj.get(0).nodeName.toLowerCase() == 'textarea') {
      obj.html(value);
    } else {
      obj.val(value);
    }
  }
};


Bwf._cmpMatrix = function ()
{  
  $('.publish_matrix').each(function(i,item) {
    // Count the inputs, if we don't have any add a blank input input, so we trigger our matrix model
    // If we don't and have deleted rows, these chnages won't get processed
    var mtxInputs = jQuery(item).find(":input");
    if (mtxInputs.length == 0) {
      Bwf.debug('Better Workflow: No matrix fields found');
      var fieldId = jQuery(item).find("div.matrix").attr("id");
      jQuery(item).append('<input type="hidden" class="bwf_temp_blank_matrix_input" name="'+ fieldId + '" id="' + fieldId + '" value="" />');
      
      Bwf.debug('Better Workflow: Hidden field added to Matrix field '+fieldId+' on pre ajax save');
    }
  });
};


}).call(window);