// ********************************************************************************* //
if (typeof(Forms) == 'undefined') var Forms = {};
Forms.prototype = {};
// ********************************************************************************* //

Forms.Init = function(){
	Forms.CField = jQuery('.Forms');
	Forms.CField.tabs();

	Forms.CField.find('.toggler').click(function(Event){
		if (Event.target.checked == true) jQuery(this).closest('tr').addClass('checked');
		else jQuery(this).closest('tr').removeClass('checked');
	});

	Forms.CField.find('.SettingsToggler').click(Forms.FieldSettingsToggler);

	Forms.ActivateShowHides();
	Forms.ActivateChoicesEnableVal();
	Forms.ActivatePopOver();
	jQuery('#FormBuilder').delegate('a.AddChoice', 'click', Forms.ChoicesAddOption);
	jQuery('#FormBuilder').delegate('a.RemoveChoice', 'click', Forms.ChoicesRemoveOption);
	jQuery('#FormBuilder').delegate('a.BulkAddChoices', 'click', Forms.ChoicesBulkAdd);

	setTimeout( function(){
		Forms.CField.find('input.ShowHideSubmitBtn').filter(':checked').click();
		Forms.CField.find('input.ChoicesEnableVal').filter(':checked').click();
		Forms.CField.find('table.FormsChoicesTable').each(function(){
			Forms.ChoicesSyncOrderNumbers(jQuery(this));
		});
	}, 500);
};

//********************************************************************************* //

Forms.FieldSettingsToggler = function(Event){
	var Target = $(Event.target);
	var Rel = Target.text();
	var HTML = Target.attr('rel');

	if (Target.hasClass('sHidden')){
		Target.removeClass('sHidden');
		Target.parent().find('.fsettings').show();
	}
	else {
		Target.addClass('sHidden');
		Target.parent().find('.fsettings').hide();
	}

	Target.attr('rel', Rel);
	Target.text(HTML);

	return false;
};

//********************************************************************************* //


Forms.ActivatePopOver = function(){

	Forms.CField.find('span.ToolTip').each(function(){
		jQuery(this).popover({
			content: Forms.JSON.Help[jQuery(this).attr('rel')],
			animation: true
		});
	});

};

//********************************************************************************* //









//********************************************************************************* //

Forms.ActivateShowHides = function(){

	jQuery('#FormBuilder').delegate('input.ShowHideSubmitBtn', 'click', function(){
		var Parent = jQuery(this).parent();
		Parent.find('p').hide();
		Parent.find('p.btn_'+jQuery(this).attr('rel')).show();
	});

	setTimeout(function(){
		jQuery('#FormBuilder').find('input.ShowHideSubmitBtn').filter(':checked').click();
	}, 300);
};

//********************************************************************************* //

Forms.ActivateChoicesEnableVal = function(){

	jQuery('#FormBuilder').delegate('input.ChoicesEnableVal', 'click', function(){
		var Parent = jQuery(this).closest('tbody');
		Parent.find('.choices_values').hide();

		if (jQuery(this).val() == 'yes') Parent.find('.choices_values').show();
	});

};

//********************************************************************************* //

Forms.ChoicesAddOption = function(Event){
	Event.preventDefault();

	// Make the clone and clear all fields
	var Clone = jQuery(Event.target).closest('tr').clone();
	Clone.find('input[type=text]').val('');
	Clone.find('input[type=radio]').removeAttr('checked');

	// Add it
	jQuery(Event.target).closest('tr').after(Clone);
	Forms.ChoicesSyncOrderNumbers(jQuery(Event.target).closest('table.FormsChoicesTable'));
};

//********************************************************************************* //

Forms.ChoicesRemoveOption = function(Event){
	Event.preventDefault();
	var Parent = jQuery(Event.target).closest('table');

	// Last field? We can't delete it!
	if (Parent.find('tbody tr').length == 1) return false;

	// Kill, with animation
	jQuery(Event.target).closest('tr').fadeOut('fast', function(){
		jQuery(this).remove();
	});
};

//********************************************************************************* //

Forms.ChoicesBulkAdd = function(Event){
	Event.preventDefault();

	jQuery.colorbox({
		href:Forms.AJAX_URL + '&ajax_method=choices_ajax_ui',
		onComplete: function(){

			var Wrapper = jQuery('#FormsChoices');

			// Fill in the Textarea
			Wrapper.find('.left a').click(function(E){
				E.preventDefault();
				jQuery('#FormsChoicesText').html( jQuery(E.target).find('span').html() );
			});

			// Insert Event
			Wrapper.find('.FormsBtn').click(function(E){
				E.preventDefault();

				var Lines = jQuery('#FormsChoicesText').html().split("\n");
				var Choices = {};

				if (Lines.length == 0) {
					return false;
				}

				for (i in Lines) {
					Lines[i] = Lines[i].split(' : ');
					if (typeof(Lines[i][1]) != 'undefined') {
						Choices[ Lines[i][0] ] = Lines[i][1];
					} else Choices[ Lines[i][0] ] = Lines[i][0];
				}

				var Tbody = jQuery(Event.target).closest('table').find('tbody');


				for (Val in Choices){
					Tbody.find('tr:first').clone()
					.find('input[type=radio]').removeAttr('checked').closest('tr')
					.find('td:eq(2)').find('input').val(Val).closest('tr')
					.find('td:eq(1)').find('input').val(Choices[Val]).closest('tr')
					.appendTo(Tbody);
				}

				jQuery.colorbox.close();

				setTimeout(function(){
					Forms.ChoicesSyncOrderNumbers(Tbody.parent());
				}, 500);

			});
		}
	});
};

//********************************************************************************* //

Forms.ChoicesSyncOrderNumbers = function(Wrapper){

	Wrapper.find('tbody tr').each(function(index, TR){

		jQuery(TR).find('input, textarea, select').each(function(i, elem){
			attr = jQuery(elem).attr('name').replace(/\[choices\]\[.*?\]/, '[choices][' + index + ']');
			jQuery(elem).attr('name', attr);
		});

		jQuery(TR).find('tr:first').find('input').attr('value', index);

	});
};

//********************************************************************************* //
