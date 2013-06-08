// ********************************************************************************* //
if (typeof(Forms) == 'undefined') var Forms = {};
Forms.new_fields = {}; Forms.colfields =['columns_4', 'columns_3', 'columns_2', 'fieldset'];
// ********************************************************************************* //

// Add :Contains (case-insensitive)
$.expr[':'].Contains = function(a,i,m){
    return (a.textContent || a.innerText || "").toUpperCase().indexOf(m[3].toUpperCase())>=0;
};

jQuery(document).ready(function(){

	$('div.Forms').each(function(i, e){
		var DOM = Forms.GetSectionsDom(null, e);

		DOM.LeftBar.find('input.filter_fields').bind('keyup', DOM, Forms.FilterFields);
		DOM.LeftBar.delegate('span.label', 'click', DOM, Forms.AddFieldByClick);

		DOM.RightBar.find('div.settings_toggler').delegate('span.label', 'click', DOM, Forms.OpenSettingsSection);
		DOM.RightBar.find('div.settings_toggler').delegate('a.backr', 'click', DOM, Forms.CloseSettingsSection);
		DOM.RightBar.find('.datepicker').datepicker({dateFormat:'yy-mm-dd', changeMonth:true,changeYear:true});
		DOM.RightBar.find('.email_admin, .email_user').delegate('.template_toggler input', 'click', DOM, Forms.ToggleTemplateWhich);
		DOM.RightBar.find('.email_admin, .email_user').find('.template_toggler input:checked').trigger('click');

		DOM.Composer.delegate('a.toggle_settings', 'click', DOM, Forms.OpenFieldSettings);
		DOM.Composer.delegate('div.FormElem', 'dblclick', DOM, Forms.OpenFieldSettings);
		DOM.Composer.delegate('a.move', 'click', DOM, function(){return;});
		DOM.Composer.delegate('a.remove', 'click', DOM, Forms.DeleteFormElem);
		DOM.Composer.delegate('.save_field', 'click', DOM, Forms.SaveField);

		Forms.PrefetchFields(DOM);
		Forms.PrefetchFieldsDB(DOM);
		Forms.ActivateSortable(DOM);
		Forms.ActivateDraggable(DOM);

		Forms.ActivatePopOver(DOM);
		Forms.ActivateShowHides(DOM);
		Forms.ActivateChoicesEnableVal(DOM);

		DOM.Composer.delegate('a.AddChoice', 'click', DOM, Forms.ChoicesAddOption);
		DOM.Composer.delegate('a.RemoveChoice', 'click', DOM, Forms.ChoicesRemoveOption);
		DOM.Composer.delegate('a.BulkAddChoices', 'click', DOM, Forms.ChoicesBulkAdd);
		DOM.Composer.delegate('a.BulkRemoveAll', 'click', DOM, Forms.ChoicesBulkRemove);
	});



	jQuery("#form_title").stringToSlug({
		setEvents: 'keyup keydown blur',
		getPut: '#form_url_title',
		space: '_'
	});

	$('div.Forms').closest('form').bind('submit', function(){
		if ($('div.Forms').find('.field_settings').length > 0) {
			alert('You have not saved your Form field. Please click the "Save Settings" button.');
			return false;
		}
	});

});

//********************************************************************************* //

Forms.GetSectionsDom = function(elem, wrapper){
	var DOM = {}
	DOM.Wrapper = wrapper ? $(wrapper) : $(elem).closest('div.Forms');
	DOM.LeftBar = DOM.Wrapper.find('div.col2');
	DOM.RightBar = DOM.Wrapper.find('div.col3');
	DOM.Composer = DOM.Wrapper.find('div.col1');

	return DOM;
};

//********************************************************************************* //

Forms.FilterFields = function(e){

	if (!e.target.value){
		e.data.LeftBar.find('span.label').show();
	} else {
		e.data.LeftBar.find('span.label:not(:Contains(' + e.target.value + '))').slideUp();
		e.data.LeftBar.find('span.label:Contains(' + e.target.value + ')').slideDown();
	}
};

//********************************************************************************* //

Forms.OpenSettingsSection = function(e){
	if (e.data.LeftBar.hasClass('transparent') === true) return;
	var Section = e.target.getAttribute('data-section');

	var DIV = e.data.RightBar.find('div.'+Section);
	e.target.className = e.target.className.replace( /(?:^|\s)label-info(?!\S)/ , ' label-important');
	e.data.LeftBar.addClass('transparent');
	e.data.Composer.addClass('transparent');

	e.data.RightBar.find('div.form_settings').css('left', '100%');

	if (DIV.hasClass('email_admin') === false && DIV.hasClass('email_user') === false) DIV.css('top', e.target.offsetTop+'px');
	else {
		DIV.css({position: 'relative'});
		DIV.css({top:'-'+DIV[0].offsetTop+'px'});
	}

	DIV.animate({left: '-'+(DIV[0].clientWidth+10)+'px'}, 300, function() {
		// Animation complete.
	});
};

//********************************************************************************* //

Forms.CloseSettingsSection = function(e){
	e.preventDefault();

	$(e.target).closest('.form_settings').animate({left: '100%'}, 300, function() {
		$(e.target).closest('.form_settings').css({position:'absolute', top:0});
	});

	e.data.LeftBar.removeClass('transparent');
	e.data.Composer.removeClass('transparent');
	e.data.RightBar.find('span.label-important').removeClass('label-important').addClass('label-info');

	if (e.target.parentNode.getAttribute('data-refreshfields') == 'yes'){
		Forms.RefreshFields(e.data);
	}
};

//********************************************************************************* //

Forms.PrefetchFields = function(DOM){
	var Params = DOM.RightBar.find(':input').serializeArray();
	Params.push({name:'ajax_method', value:'prefetch_fields'});

	DOM.LeftBar.find('span.label').each(function(i, e){
		var Field = e.getAttribute('data-field');

		if (typeof(Forms.new_fields[Field]) != 'undefined') return;

		Forms.new_fields[Field] = null;
		Params.push({name:'fields[]', value:Field});
	});

	$.ajax({url:Forms.AJAX_URL,
		data:Params, type: 'POST', dataType:'json',
		error: function(){
			alert('Failed to load Default Forms Fields.. Refreshing the page might help.');
		},
		success:function(rData){
			for (var name in rData.fields){
				Forms.new_fields[name] = rData.fields[name];
			}
		}
	});

};

//********************************************************************************* //

Forms.PrefetchFieldsDB = function(DOM){

	var Params = DOM.RightBar.find(':input').serializeArray();
	Params.push({name:'form_id', value:DOM.Wrapper.data('formid')});
	Params.push({name:'ajax_method', value:'fetch_db_fields'});

	$.ajax({url:Forms.AJAX_URL,
		data:Params, type: 'POST', dataType:'json',
		error: function(){
			alert('Failed to load DB Fields.. Refreshing the page might help. DO NOT SAVE THE FORM!');
		},
		success:function(rData){
			if (rData.fields.length === 0) return;
			DOM.Wrapper.find('.FirstDrop').hide();

			var Fields = [];
			for (var i = 0; i < rData.fields.length; i++) {
				Fields.push(rData.fields[i]);
			}

			DOM.Wrapper.find('.col1').append(Fields.join(''));

			Forms.ActivateSortable(DOM);
			Forms.SyncOrderNumbers(DOM);
		}
	});

};

//********************************************************************************* //

Forms.RefreshFields = function(DOM, CustomParent){

	var FormSettings = DOM.RightBar.find(':input').serializeArray();
	var Elements = CustomParent ? CustomParent.find('div.FormElem') : DOM.Composer.find('div.FormElem');

	if (Elements.length == 0) return;

	// Loop over all fields and you thing
	Elements.each(function(i, e){
		var Parent = $(e);
		var Settings = $( $.base64Decode(Parent.children('.settingshtml').html()) );

		Parent.addClass('transparent');

		// Find Columns
		var Columns = {};
		if (Parent.find('div.FormElem').length > 0){
			Parent.find('.column').each(function(i, e){
				var Elems = $(e).find('div.FormElem').clone();
				Columns['COL_'+e.getAttribute('data-number')] = (Elems);
			});
		}

		var Params = Settings.find(':input').serializeArray();
		Params = Params.concat(FormSettings);
		Params.push({name:'ajax_method', value:'save_field'});
		Params.push({name:'settings[field_hash]', value:Parent[0].getAttribute('data-fieldhash')});

		setTimeout(function(){
			$.post(Forms.AJAX_URL, Params, function(rData){
				var NewParent = $(rData);
				Parent.replaceWith(NewParent);

				for (var Col in Columns){
					NewParent.find('.column[data-number='+Col.substr(4)+']').html(Columns[Col]);
				}

				setTimeout(function(){
					Forms.RefreshFields(DOM, NewParent);
				}, (i*300));

				Forms.SyncOrderNumbers(DOM);
				Forms.ActivateSortable(DOM);
			});
		}, (i*200));

	});
};

//********************************************************************************* //

Forms.AddField = function(FieldName, DOM){
	if (typeof(Forms.new_fields[FieldName]) == 'undefined') return;
	DOM.Composer.find('div.FirstDrop').hide();

	var Field = $(Forms.new_fields[FieldName]);
	Field.attr('data-fieldhash', (Math.random()*10));
	DOM.Composer.append(Field);

	if ($.inArray(FieldName, Forms.colfields) >= 0) Forms.ActivateSortable(DOM);

	// Sync Order Numbers
	Forms.SyncOrderNumbers(DOM);
	Forms.ActivatePopOver(DOM);

	// Trigger ShowHides!
	setTimeout(function(){
		DOM.Composer.find('input.ShowHideSubmitBtn').filter(':checked').click();
	}, 300);

};


//********************************************************************************* //

Forms.ActivateDraggable = function(DOM){

	DOM.LeftBar.find('.sort').each(function(index, Field){

		var FF = $(Field);
		FF.data('original_state', FF.html());

		jQuery(Field).sortable({
			//handle: '.move',
			placeholder : 'sortable_placeholder',
			connectWith:'.col1wrap .sortable',
			helper: function(Event){
				return $( "<div class='dragging_field' style='width:175px;height:20px;background:#ECF1F4;margin:0'>"+jQuery(Event.target).html()+"</div>" );
			},
			stop: function(Event, UI){

				// Is it a draggable?
				if (UI.item.hasClass('draggable') === true){

					// Hide forst drop!
					jQuery('.col1wrap').find('div.FirstDrop').hide();

					var FieldType = $(UI.item).data('field');
					var Field = $(Forms.new_fields[FieldType]);
					Field.attr('data-fieldhash', (Math.random()*10));

					// Replace with the new item
					UI.item.replaceWith(Field);

					if ($.inArray(FieldType, Forms.colfields) >= 0) Forms.ActivateSortable(DOM);
				}

				FF.html(FF.data('original_state'));

				// Wait a bit before syncing numbers
				setTimeout(function(){
					Forms.SyncOrderNumbers(DOM);
					DOM.Composer.find('input.ShowHideSubmitBtn').filter(':checked').click();
				}, 300);

			}
		});
	});
};

//********************************************************************************* //

Forms.ActivateSortable = function(DOM){

	DOM.Wrapper.find('.col1wrap .sortable').each(function(index, Field){

		jQuery(Field).sortable({
			handle: '.move',
			placeholder : 'sortable_placeholder',
			connectWith:'.sortable',
			items: '> .FormElem',
			helper: function(Event){
				var Fieldtype = $(Event.target).closest('div.FormElem').data('fieldtype');
				var HTML = "<div class='dragging_field' style='width:175px;height:20px;background:#ECF1F4;margin:0'>"+DOM.LeftBar.find('.field_'+ Fieldtype).html()+"</div>";
				return $( HTML );
			},
			cursorAt: {right:85, top:10},
			stop: function(){
				setTimeout(function(){
					Forms.SyncOrderNumbers(DOM);
				}, 300);
			}
		});

	});

};

//********************************************************************************* //


Forms.AddFieldByClick = function(e){

	// Add the new Field
	Forms.AddField(e.target.getAttribute('data-field'), e.data);

	// Scroll the page to the newly created page..slowlyyyy
	jQuery('html,body').animate({scrollTop: e.data.Composer.find('div.FormElem:last').offset().top - 40}, 900);

	// Wait and animate the background
	setTimeout(function(){
		e.data.Composer.find('div.FormElem:last').stop().css('background-color', '#FFF6A9').animate({ backgroundColor: 'transparent'}, 2000, null, function(){
			jQuery(this).css({'background-color' : ''});
		});
	}, 300);
};

//********************************************************************************* //

Forms.OpenFieldSettings = function(e){
	e.preventDefault();
	if (! e.data) e.data = Forms.GetSectionsDom(e.target);

	e.data.LeftBar.addClass('transparent');
	e.data.RightBar.addClass('transparent');
	e.data.Composer.children('div.FormElem').addClass('transparent');

	var Parent = $(e.target).closest('div.FormElem');
	var Settings = $( $.base64Decode(Parent.children('.settingshtml').html()) );

	if (e.data.Composer.children('div.field_settings').length == 1) return;

	Settings.data('field_element', Parent);

	e.data.Composer.append(Settings);

	// Find IT!
	Settings = e.data.Composer.children('div.field_settings');

	var TopBefore = Settings[0].offsetTop+Parent[0].offsetTop+Settings.height();
	var TopAfter = Settings[0].offsetTop-Parent[0].offsetTop;
	Settings.css('top', '-'+TopBefore+'px').data('offsetbefore', (TopBefore));
	Settings.animate({top: '-'+TopAfter+'px'}, 300, function() {
		if (! e.data) e.data = Forms.GetSectionsDom(e.target); // somehow, event.data get's lost
		Forms.ActivatePopOver(e.data);
		Forms.ActivateToggleShide(e.data);
		Forms.ChoicesSyncOrderNumbers(Settings.find('.FormsChoicesTable'));
		Settings.find('input.ShowHideSubmitBtn').filter(':checked').click();
		Settings.find('input.ChoicesEnableVal').filter(':checked').click();

		jQuery("#fieldsettings_field_title").stringToSlug({
			setEvents: 'keyup keydown blur',
			getPut: '#fieldsettings_field_url_title',
			space: '_'
		});

		Settings.find('select.multiselect').chosen();
		Settings.find('.field_hash').attr('value', Parent[0].getAttribute('data-fieldhash'));

		var AllFields = Forms.GetFieldsByHash(e.data);
		Settings.find('.ConditionalTable tbody tr').each(function(i, e){
			var Elem = $(e).find('td:first');
			Elem.find('select').html(AllFields).find("option[value='"+Elem.find('select').data('selected')+"']").attr('selected',true);
		});
	});

};

//********************************************************************************* //

Forms.SaveField = function(e){
	e.preventDefault();

	var Action = e.target.getAttribute('data-action');
	var Settings = e.data.Composer.children('div.field_settings');

	if (Action == 'cancel') {
		Settings.find('.saving_settings').hide();
		Settings.animate({top: '-'+Settings.data('offsetbefore')+'px'}, 300, function() {
			e.data.LeftBar.removeClass('transparent');
			e.data.RightBar.removeClass('transparent');
			e.data.Composer.children('div.FormElem').removeClass('transparent');
			e.data.Composer.children('div.field_settings').remove();
		});
		return false;
	}

	Settings.find('.saving_settings').show();

	var Params = Settings.find(':input').serializeArray();
	Params = Params.concat( e.data.RightBar.find(':input').serializeArray() );
	Params.push({name:'ajax_method', value:'save_field'});

	var Parent = Settings.data('field_element');

	// Find Columns
	var Columns = {};
	if (Parent.find('div.FormElem').length > 0){
		Parent.find('.column').each(function(i, e){
			var Elems = $(e).find('div.FormElem').clone();
			Columns['COL_'+e.getAttribute('data-number')] = (Elems);
		});
	}

	$.post(Forms.AJAX_URL, Params, function(rData){
		var NewParent = $(rData);
		Parent.replaceWith(NewParent);

		// Sometimes e.data is empty, so lets try again shall we?
		if (!e.data) e.data = Forms.GetSectionsDom(NewParent);

		for (var Col in Columns){
			NewParent.find('.column[data-number='+Col.substr(4)+']').html(Columns[Col]);
		}

		Settings.find('.saving_settings').hide();
		Settings.animate({top: '-'+Settings.data('offsetbefore')+'px'}, 300, function() {
			e.data.LeftBar.removeClass('transparent');
			e.data.RightBar.removeClass('transparent');
			e.data.Composer.children('div.FormElem').removeClass('transparent');
			e.data.Composer.children('div.field_settings').remove();
		});

		Forms.SyncOrderNumbers(e.data);
		Forms.ActivateSortable(e.data);
	});

};

//********************************************************************************* //

Forms.ActivatePopOver = function(DOM){

	DOM.Wrapper.find('span.pophelp').each(function(i, e){
		jQuery(this).popover({
			content: Forms.JSON.Help[e.getAttribute('data-key')],
			animation: true
		});
	});

};

//********************************************************************************* //

Forms.SyncOrderNumbers = function(DOM){
	DOM.Composer.each(function(){
		jQuery(this).children('.FormElem').each(function(index,elem){
			var Settings = jQuery(elem).find('textarea.settings');
			var attr;

			if (Settings.length == 1) {
				attr = Settings.attr('name').replace(/\[fields\]\[.*/, '[fields][' + (index+1) + '][field]');
				Settings.attr('name', attr);
			} else {
				var SettingsChildren = Settings.slice(0, -1);
				var LastSettings = Settings.filter(':last');

				SettingsChildren.each(function(ii, ee){
					var Elem = $(ee);
					var ColNum = Elem.closest('.column').data('number');
					var attrr = Elem.attr('name').replace(/\[fields\]\[.*/, '[fields][' + (index+1) + '][columns]['+ColNum+'][]');
					Elem.attr('name', attrr);
				});

				attr = LastSettings.attr('name').replace(/\[fields\]\[.*/, '[fields][' + (index+1) + '][field]');
				LastSettings.attr('name', attr);
			}
		});

	});
};

//********************************************************************************* //

Forms.DeleteFormElem = function(e){
	e.preventDefault();

	jQuery(e.target).closest('div.FormElem').slideUp('800', function(){

		if ( jQuery(e.target).closest('.col1').find('div.FormElem').length == 1) {
			jQuery(e.target).closest('.col1').find('.FirstDrop').show();
		}

		jQuery(this).remove();
	});
};


//********************************************************************************* //

Forms.ActivateShowHides = function(DOM){

	DOM.Composer.delegate('input.ShowHideSubmitBtn', 'click', function(){
		var Parent = jQuery(this).parent();
		Parent.find('p').hide(); Parent.find('.showhide').hide();
		Parent.find('.btn_'+jQuery(this).attr('rel')).show();
	});

	setTimeout(function(){
		DOM.Composer.find('input.ShowHideSubmitBtn').filter(':checked').click();
	}, 300);
};


//********************************************************************************* //

Forms.ActivateChoicesEnableVal = function(DOM){

	DOM.Composer.delegate('input.ChoicesEnableVal', 'click', function(){
		var Parent = jQuery(this).closest('tbody');
		Parent.find('.choices_values').hide();

		if (jQuery(this).val() == 'yes') Parent.find('.choices_values').show();
	});

};

//********************************************************************************* //

Forms.ChoicesAddOption = function(e){
	e.preventDefault();

	// Make the clone and clear all fields
	var Clone = jQuery(e.target).closest('tr').clone();
	Clone.find('input[type=text]').val('');
	Clone.find('input[type=radio]').removeAttr('checked');

	// Add it
	jQuery(e.target).closest('tr').after(Clone);
	Forms.ChoicesSyncOrderNumbers(jQuery(e.target).closest('table.FormsChoicesTable'));
};

//********************************************************************************* //

Forms.ChoicesRemoveOption = function(e){
	e.preventDefault();
	var Parent = jQuery(e.target).closest('table');

	// Last field? We can't delete it!
	if (Parent.find('tbody tr').length == 1) return false;

	// Kill, with animation
	jQuery(e.target).closest('tr').fadeOut('fast', function(){
		jQuery(this).remove();
	});
};

//********************************************************************************* //

Forms.ChoicesBulkAdd = function(e){
	e.preventDefault();

	var ModalWrapper = $('div.ModalWrapper:first');

	ModalWrapper.load(Forms.AJAX_URL + '&ajax_method=choices_ajax_ui', function(){
		ModalWrapper.modal();

		// Fill in the Textarea
		ModalWrapper.find('.left a').click(function(E){
			E.preventDefault();
			jQuery('#FormsChoicesText').html( jQuery(E.target).find('span').html() );
		});

		ModalWrapper.find('.btn-primary').click(function(E){
			E.preventDefault();

			var Lines = jQuery('#FormsChoicesText').val().split("\n");
			var Choices = {};

			if (Lines.length === 0) {
				return false;
			}

			for (var i in Lines) {
				Lines[i] = Lines[i].split(' : ');
				if (typeof(Lines[i][1]) != 'undefined') {
					Choices[ Lines[i][0] ] = Lines[i][1];
				} else Choices[ Lines[i][0] ] = Lines[i][0];
			}

			var Tbody = jQuery(e.target).closest('table').find('tbody');


			for (var Val in Choices){
				Tbody.find('tr:first').clone()
				.find('input[type=radio]').removeAttr('checked').closest('tr')
				.find('td:eq(2)').find('input').val(Val).closest('tr')
				.find('td:eq(1)').find('input').val(Choices[Val]).closest('tr')
				.appendTo(Tbody);
			}

			ModalWrapper.modal('hide');

			setTimeout(function(){
				Forms.ChoicesSyncOrderNumbers(Tbody.parent());
			}, 500);

		});
	});
};

//********************************************************************************* //

Forms.ChoicesBulkRemove = function(e){
	$(e.target).closest('table').find('a.RemoveChoice').not(':first').click();
	return false;
};

//********************************************************************************* //

Forms.ChoicesSyncOrderNumbers = function(Wrapper){

	Wrapper.each(function(i, e){
		jQuery(e).find('tbody tr').each(function(index, TR){

			jQuery(TR).find('input, textarea, select').each(function(i, elem){
				var attr = jQuery(elem).attr('name');
				attr = attr.replace(/\[choices\]\[.*?\]/, '[choices][' + index + ']');
				attr = attr.replace(/\[conditionals\]\[.*?\]/, '[conditionals][' + index + ']');
				attr = attr.replace(/\[values\]\[.*?\]/, '[values][' + index + ']');
				attr = attr.replace(/\[labels\]\[.*?\]/, '[labels][' + index + ']');
				jQuery(elem).attr('name', attr);
			});

			jQuery(TR).find('tr:first').find('input').attr('value', index);

		});
	});
};

//********************************************************************************* //

Forms.GetFieldsByHash = function(DOM){
	var Fields = [];
	var AcceptedFields =['text_input', 'radio', 'select', 'textarea', 'email', 'numbers', 'email_route', 'cart_product'];

	DOM.Composer.find('div.FormElem').each(function(i, e){
		if ($.inArray(e.getAttribute('data-fieldtype'), AcceptedFields) == -1) return;

		var Prefix = (e.parentElement.className != 'col1 sortable ui-sortable') ? '&#160;&#160;&#160;' : '';
		Fields.push('<option value="'+e.getAttribute('data-fieldhash')+'">'+Prefix + e.getAttribute('data-fieldlabel')+'</option>');
	});

	return Fields.join('');
};

//********************************************************************************* //

Forms.ToggleTemplateWhich = function(Event){
	var Parent = jQuery(Event.target).closest('.FormTable');
	Parent.find('tbody.toggle').hide().filter('.'+Event.target.value).delay(50).show();
};

//********************************************************************************* //

Forms.ActivateToggleShide = function(){
	$('.shide_triggers').find('input').bind('click', Forms.ToggleShide).filter(':checked').trigger('click');
};

//********************************************************************************* //

Forms.ToggleShide = function(Event){
	var Target = $(Event.target);
	var Val = Target.val();
	var Parent = Target.closest('table');
	Parent.find('.shide').hide();

	Parent.find('.s-' + Val).show();
};

//********************************************************************************* //
