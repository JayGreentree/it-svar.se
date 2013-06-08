var Editor = Editor ? Editor : {};
Editor.Server = {};
// ********************************************************************************* //

Editor.Init = function() {

	Editor.EditorSettings = $('.EditorField');
	Editor.EditorSettings.find('.dmenu').delegate('a', 'click', Editor.ToggleSettingsSection);
	Editor.EditorSettings.find('.editor_settings_toggler').delegate('input[type=radio]', 'click', Editor.ToggleEditorSettingsWrapper);

	Editor.EditorSettings.each(function(i, el){
		$(el).find('.editor_settings_toggler').find('input:checked').trigger('click');
	});

	Editor.EditorSettings.find('.tbuttons').each(function(i, e){
		var Holder = $(e);

		Holder.find('.redactor_toolbar').sortable({
			connectWith: '.redactor_toolbar',
			containment: Holder,
			appendTo: 'body',
			helper: function(Event){
				var HTML = '';

				if ( jQuery(Event.target).hasClass('redactor_separator') === true ) {
					HTML = jQuery(Event.target).clone().wrap('<p>').parent().html();
					return $( "<ul class='redactor_toolbar' style='width:10px;height:25px;margin:0'>"+HTML+"</ul>" );
				}
				else {
					HTML = jQuery(Event.target).parent().clone().wrap('<p>').parent().html();
					return $( "<ul class='redactor_toolbar' style='width:28px;height:25px;margin:0'>"+HTML+"</ul>" );
				}
			},
			stop: function(Event, UI){

				if ( jQuery(Event.target).hasClass('buttons_avail') === true ) {

					UI.item.find('input').removeAttr('disabled');

					if (UI.item.hasClass('redactor_separator') === true) {
						jQuery(Event.target).append(UI.item.clone());
					}

				} else {

					UI.item.find('input').attr('disabled', 'disabled');

					if (UI.item.hasClass('redactor_separator') === true) {
						UI.item.remove();
					}
				}

				// Just in case
				Holder.find('.redactor_toolbar.buttons_current').find('input').removeAttr('disabled');
				Holder.find('.redactor_toolbar.buttons_avail').find('input').attr('disabled', 'disabled');
			}
		}).disableSelection();
	});

	Editor.EditorSettings.find('.upload_service').delegate('input', 'click', Editor.ToggleUploadService).find('input:checked').trigger('click');

	if (document.getElementById('field_type') !== null) {
		$('#field_type').change(Editor.FieldtypeSelect);
		Editor.PreviousFieldtype = $('#field_type').val();
	}
};

//********************************************************************************* //

Editor.ToggleSettingsSection = function(Event){
	Event.preventDefault();
	Editor.EditorSettings.find('.dmenu').find('li').removeClass('current');
	$(Event.target).parent().addClass('current');

	Editor.EditorSettings.find('.tabholder:visible').hide();
	Editor.EditorSettings.find('.tabholder.' + Event.target.getAttribute('data-section')).show();
};

//********************************************************************************* //

Editor.ToggleEditorSettingsWrapper = function(Event){
	var Field = $(Event.target).closest('.EditorField');
	Field.find('.editor_settings_wrapper').hide();
	Field.find('.editor_settings_' + Event.target.value).show();
};

//********************************************************************************* //

Editor.ToggleUploadService = function(Event){
	var Parent = $(Event.target).closest('table');

	Parent.find('.upload_wrapper').hide();
	Parent.find('.upload_' + Event.target.value).show();
};

//********************************************************************************* //

Editor.FieldtypeSelect = function(Event){
	var Target = $(Event.target);
	if (window.location.href.indexOf('field_id=') == -1) return;
	if (Editor.PreviousFieldtype == 'textarea' || Editor.PreviousFieldtype == 'text' || Editor.PreviousFieldtype == 'rte') {
		Editor.EditorSettings.find('.editor_fieldtype_toggler').show().find('input:first').trigger('click');
	}
};

//********************************************************************************* //
