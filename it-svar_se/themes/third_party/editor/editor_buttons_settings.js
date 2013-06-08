if (typeof EditorButtons === 'undefined') var EditorButtons = {};
// ********************************************************************************* //

jQuery(document).ready(function(){

    if (document.getElementById('btn_styles')){
        EditorButtons.Styles_Wrap = $('#btn_styles');
        EditorButtons.Styles_Wrap.find('.add_style').bind('click', EditorButtons.Styles_AddRow);
        EditorButtons.Styles_Wrap.find('.typesel select').bind('change', EditorButtons.CheckTypeDropdown).trigger('change');
        EditorButtons.Styles_Wrap.delegate('a.delete', 'click', EditorButtons.Styles_DelRow);
        EditorButtons.Styles_SyncRows();
    }

    if (document.getElementById('btn_templates')){
        EditorButtons.Templates_Wrap = $('#btn_templates');
        EditorButtons.Templates_Wrap.find('.add_template').bind('click', EditorButtons.Templates_AddRow);
        EditorButtons.Templates_Wrap.delegate('a.delete', 'click', EditorButtons.Templates_DelRow);
        EditorButtons.Templates_SyncRows();
    }
});

// ********************************************************************************* //

EditorButtons.Styles_AddRow = function(e){
    e.preventDefault();
    var Cloned = EditorButtons.Styles_Wrap.find('tbody.styles_rows').find('.dummy').clone();
    Cloned.show().removeClass('dummy');

    Cloned.appendTo(EditorButtons.Styles_Wrap.find('tbody.styles_rows'));
    EditorButtons.Styles_SyncRows();
};

// ********************************************************************************* //

EditorButtons.Styles_DelRow = function(e){
    e.preventDefault();

    $(e.target).closest('tr').fadeOut('slow', function(){
        $(e.target).closest('tr').remove();
        EditorButtons.Styles_SyncRows();
    });
};

// ********************************************************************************* //

EditorButtons.CheckTypeDropdown = function(e){
    var Value = $(e.target).val();
    var Parent = $(e.target).closest('td');

    if (Value == 'custom') {
        Parent.find('input').show();
    } else {
        Parent.find('input').hide();
    }
}

// ********************************************************************************* //

EditorButtons.Styles_SyncRows = function(){
    EditorButtons.Styles_Wrap.find('tbody.styles_rows').find('tr').each(function(index, elem){
        // Find all form inputs
        $(elem).find(':input').each(function(findex, felem){
            var Input = $(felem);
            if (!felem.getAttribute('name')) return false;

            // Get it's attribute and change it
            var attr = Input.attr('name').replace(/settings\[styles\]\[.*?\]/, 'settings[styles][' + (index) + ']');
            Input.attr('name', attr);
        });
    });
};

// ********************************************************************************* //

// ********************************************************************************* //

EditorButtons.Templates_AddRow = function(e){
    e.preventDefault();
    var Cloned = EditorButtons.Templates_Wrap.find('tbody.templates_rows').find('.dummy').clone();
    Cloned.show().removeClass('dummy');

    Cloned.appendTo(EditorButtons.Templates_Wrap.find('tbody.templates_rows'));
    EditorButtons.Templates_SyncRows();
};

// ********************************************************************************* //

EditorButtons.Templates_DelRow = function(e){
    e.preventDefault();

    $(e.target).closest('tr').fadeOut('slow', function(){
        $(e.target).closest('tr').remove();
        EditorButtons.Templates_SyncRows();
    });
};

// ********************************************************************************* //

EditorButtons.Templates_SyncRows = function(){
    EditorButtons.Templates_Wrap.find('tbody.templates_rows').find('tr').each(function(index, elem){
        // Find all form inputs
        $(elem).find(':input').each(function(findex, felem){
            var Input = $(felem);
            if (!felem.getAttribute('name')) return false;

            // Get it's attribute and change it
            var attr = Input.attr('name').replace(/settings\[templates\]\[.*?\]/, 'settings[templates][' + (index) + ']');
            Input.attr('name', attr);
        });
    });
};

// ********************************************************************************* //
