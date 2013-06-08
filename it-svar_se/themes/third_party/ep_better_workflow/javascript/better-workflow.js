(function () {
'use strict';

  this.Bwf = {};
  
  Bwf.logEvents;
  
  Bwf.callbacks = {
    ajaxSave: {},
    previewClose: {}
  };
  
  Bwf.typeIndex = 0;

  /**
  * Bind
  */
  Bwf.bind = function(fieldtype, event, callback) {
    if (typeof Bwf.callbacks[event] == 'undefined') return;
    Bwf.callbacks[event][fieldtype + '_' + this.typeIndex] = callback;
    this.typeIndex++;
  };

  /**
  * Unbind
  */
  Bwf.unbind = function(fieldtype, event) {
    var patt = new RegExp(fieldtype+'_\\d+');    
    if (typeof Bwf.callbacks[event] == 'undefined') return;
    for(var field in Bwf.callbacks[event]) {
      if (patt.test(field)) {
        delete Bwf.callbacks[event][field];
    	Bwf.debug("Better Workflow: " + field + " unbound from " + event + " event");	
      }
    }
  };

  /**
  * Console.log which won't break older browsers
  */
  Bwf.debug = function(msg){
    if(this.logEvents){
      try {
        console.log(msg);
      }
      catch (e) {}
    }     
  };

}).call(window);
