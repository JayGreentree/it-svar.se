/*
    Instead of providing a nifty {exp:system_messages:js}
    method and imposing the code and markup upon you, I'm providing
    the example that you can use directly, or tweak to your hearts content.
    The idea is simple, if an EE form is wrapped in a container with 
    data-is-ajax="yes" as an attribute, it will post the form via ajax,
    thus returning the custom error messages in a modal window. Note that
    this will _not_ use your custom EE templates! It returns a 'data' object
    that contains all the same variables as a normal template.

    Any form that contains an input named "ACT", basically any EE form
    will be processed via Ajax, and the result sent to a modal window.
    
    The form parent container must have the class .form, and can contain
    2 optional data parameters:
    
        <div class="form" data-is-ajax="yes" data-modal-on-success="yes">
            <form>
                <div class="hiddenFields"><input type="hidden" name="ACT" value="1" /></div>
            </form>
        </div>
        
    The first, is-ajax, indicates that we want the form to be processed
    as an Ajax request, if set to "no", then the form will submit and process
    like a normal EE form. By default, successful form actions will process
    like a normal EE valid form submission, but if you want to show the 
    modal window containing the data set data-modal-on-success="yes". Note
    this will still only work if content is sent back by EE.
*/

$(function(){
    EEAction.init();
}); 

var EEAction = {
    init: function() {
        $("form").each(function(){
            
            var form = $(this);
            var action = form.find('input[name="ACT"]').val();
            
            if(action){
                
                var parent = form.closest('div');
                var is_ajax = parent.data('is-ajax') || parent.attr('data-is-ajax') || 'no';
                var modal_on_success = parent.data('modal-on-success') || parent.attr('data-modal-on-success') || 'no';

                if(is_ajax == 'yes') {
                    form.bind('submit', function(event){
                        var data = form.serialize();
                        var success = true;

                        $.ajax({
                            type: 'POST',
                            url: site_url + "?ACT=" + action,
                            data: data,
                            async: false,
                            dataType: 'json',
                            success: function(data, textStatus, jqXHR){
                                if( data.success == false || (modal_on_success == 'yes' && data.content != '') ) {
                                    success = false;
                                    EEMessageDialog.openModal(data);
                                }
                            }
                        });

                        if(success == false){
                            event.preventDefault();
                        }
                    });
                }
            }
        });
    }
};

var EEMessageDialog = {
    openModal: function(data) {
        var self = this;
        console.log(data);
        var modalHtml = '<div class="ajax_container"> \
                            <section class="message_modal"> \
                                <h1>'+ data.heading +'</h1> \
                                <p>'+ data.content +'</p> \
                                <a href="#" class="ui-dialog-titlebar-close">'+ lang.close +'</a> \
                            </section> \
                        </div>';
        
        if (!$('div.modal_container').length) {
            $('body').append('<div class="modal_container"></div>');                    
        }      
                  
        $('div.modal_container').dialog({
            autoOpen: true,
            width: 780,
            position: 'top',
            modal: true,
            resizable: false,
            closeOnEscape: true,
            draggable: false,
            open: function() {
                $(this).html(modalHtml);
                locationMap();
            },
            close: function() {
            }
        });

        $('a.ui-dialog-titlebar-close').click(self.closeModal);
    },
    closeModal: function(event) {
        $('.modal_container').remove();
        event.preventDefault();
    }
}