// ********************************************************************************* //
if (typeof(Forms) == 'undefined') var Forms = {};
// ********************************************************************************* //

jQuery(document).ready(function(){

    $('#formsdash').delegate('a.form_toggle', 'click', Forms.Dash_OpenForm);
    $('#formsdash').delegate('a.resetdashboard', 'click', Forms.Dash_Reset);
});

//********************************************************************************* //

Forms.Dash_OpenForm = function(e){
    var Elem = $(e.target);
    Elem.addClass('loading');

    var FormsDash = $('#formsdash');

    var Params = {};
    Params.form_id = e.target.getAttribute('data-id');
    Params.XID = EE.XID;
    Params.ajax_method = 'getDashboardForm';

    $.post(Forms.AJAX_URL, Params, function(rData){

        FormsDash.find('.holder').hide();
        FormsDash.find('.heading h2 .abtn').css('display', 'inline-block');

        FormsDash.find('.innerh').append('<div id="dsform"></div>').find('#dsform').append(rData);
        Elem.removeClass('loading');
        Forms.mcp_init();
    });

    return false;
}

//********************************************************************************* //

Forms.Dash_Reset = function(e){
    var FormsDash = $('#formsdash');
    FormsDash.find('#dsform').remove();
    FormsDash.find('.holder').show();
    FormsDash.find('.heading h2 .abtn').hide();
    return false;
};

//********************************************************************************* //
