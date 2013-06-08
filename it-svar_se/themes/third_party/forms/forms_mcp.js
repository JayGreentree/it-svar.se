// ********************************************************************************* //
if (typeof(Forms) == 'undefined') var Forms = {};
Forms.DataTables = {}; Forms.DatatablesCols = {}; Forms.Timeout = 0; Forms.IDS = [];
// ********************************************************************************* //
var testinput = document.createElement('input');
$.extend($.support, { placeholder: !!('placeholder' in testinput) });
//********************************************************************************* //

// In the MCP Header now!
jQuery(document).ready(function(){
	//Forms.mcp_init();
});

//********************************************************************************* //

Forms.mcp_init = function(){
	Forms.fBody = jQuery('#fbody');
	Forms.PlaceHolderFix();
	Forms.fBody.find("select.chzn-select").chosen();
	Forms.fBody.find('.tooltips').tooltip();

	Forms.fBody.delegate('table.selectable tbody tr', 'click', Forms.SelectTableTR);
	Forms.fBody.delegate('.btn-action', 'click', Forms.TriggerBtnAction);
	Forms.fBody.find('input.CheckAll').click(Forms.CheckAllTR);

	Forms.LeftBar = jQuery('#leftbar');
	Forms.LeftBarFilters = jQuery('#leftbar-filters');
	Forms.LeftBarColumns = jQuery('#leftbar-columns');
	Forms.LeftBarColumns.delegate('span', 'click', Forms.DTColumnToggler);

	Forms.LeftBarFilters.find('.datepicker').datepicker({dateFormat: 'yy-mm-dd', changeMonth: true, changeYear: true,
		onSelect: function(){
			$(this).trigger('keyup');
		}
	});

	// Initialize Datatables
	Forms.DatatablesInit();
	Forms.ActivateSidebarFilters();

	//----------------------------------------
	// Forms Home Page
	//----------------------------------------
	if (document.getElementById('FormsHome') != null){
		$('#actionbar').delegate('.DeleteForm', 'click', Forms.DeleteForm);
	}

	//----------------------------------------
	// Submissions Page
	//----------------------------------------
	if (document.getElementById('SubmissionsDT') != null){
		$('#actionbar').delegate('.ViewEntry', 'click', Forms.ViewEntry);
		$('#actionbar').delegate('.DelEntry', 'click', Forms.FentryDelete);
		$('#FormEntryWrapper').delegate('.FormEntrySlideBack', 'click', Forms.ViewEntryBack);
	}

	//----------------------------------------
	// New Forms Page
	//----------------------------------------
	if (document.getElementById('NewForm') != null){
		Forms.Fields = jQuery('.Forms');
		Forms.Fields.tabs();
	}


	//----------------------------------------
	// Templates Page
	//----------------------------------------
	if (document.getElementById('TemplatesDT') != null){
		Forms.TemplatesDT();
	}

	if (document.getElementById('TemplatesForm') != null){

		var TTSelect = jQuery('#TemplatesForm').find('.template_type select');
		TTSelect.change(function(){
			var val = TTSelect.val();
			jQuery('#TemplatesForm').find('.admin_only, .user_only').hide('fast', function(){
				jQuery('#TemplatesForm').find('.'+val+'_only').show();
			});
		});
		TTSelect.trigger('change');
	}

	//----------------------------------------
	// Form Entries
	//----------------------------------------
	if (document.getElementById('EntriesDT') != null){
		$('#actionbar').delegate('.ViewEntry', 'click', Forms.ViewEntry);
		$('#actionbar').delegate('.DelEntry', 'click', Forms.FentryDelete);
		$('#actionbar').delegate('.PrintEntry', 'click', Forms.FentryPrint);
		$('#actionbar').delegate('.ExportForm', 'click', Forms.FormsExportDialog);
		$('#FormEntryWrapper').delegate('.FormEntrySlideBack', 'click', Forms.ViewEntryBack);
	}
};

//********************************************************************************* //

Forms.PlaceHolderFix = function(){

	if(!jQuery.support.placeholder) {
		Forms.fBody.find('input[placeholder]').each(function() {
	        var placeholder = $(this).attr("placeholder");

	        $(this).val(placeholder).focus(function() {
	            if($(this).val() == placeholder) {
	                $(this).val("")
	            }
	        }).blur(function() {
	            if($(this).val() == "") {
	                $(this).val(placeholder)
	            }
	        });
	    });
	}
};

//********************************************************************************* //

Forms.SelectTableTR = function(Event){
	var TR = $(this);
	var Table = TR.closest('table');
	var MultiCheck = Table[0].getAttribute('data-multicheck');

	// Is it Checked?
	if ( TR.hasClass('checked') == false ){
		if (MultiCheck == 'no') Table.find('tbody tr.checked').removeClass('checked');
		TR.addClass('checked');
	}
	else {
		TR.removeClass('checked');
	}

	var Length = Table.find('tbody tr.checked').length;
	if ( Length > 0){
		$('#actionbar').find('a.btn-action').each(function(i, e){
			//$(e).removeClass('enabled');
			if ( Length > 1 && e.getAttribute('data-multiple') == 'yes' ) {
				e.className += ' enabled'; return;
			} else e.className = e.className.replace( /(?:^|\s)enabled(?!\S)/ , '');

			if ( Length == 1 ) e.className += ' enabled';
		});
	} else {
		$('#actionbar').find('a.btn-action').removeClass('enabled');
	}
};

//********************************************************************************* //

Forms.CheckAllTR = function(Event){
	Event.cancelBubble = true;
	Event.stopPropagation();

	// Grab all TR's
	var TRS = jQuery(Event.target).closest('table').find('tbody tr');

	// Is it Checked?
	if (Event.target.checked == true){

		TRS.each(function(i, elem){
			var TR = $(elem);

			// Is it NOT Checked?
			if (TR.hasClass('checked') == false) TR.click();

		});
	}
	else {

		TRS.each(function(i, elem){
			var TR = $(elem);

			// Is it NOT Checked?
			if (TR.hasClass('checked') == true) TR.click();

		});
	}

	delete TR;
	delete TempCheckBox;
};

//********************************************************************************* //

Forms.TriggerBtnAction = function(Event){
	var BTN = $(Event.target).parent();

	if (BTN.hasClass('enabled') == false) return false;

	if (BTN.data('multiple') == 'no'){
		if (BTN.data('followurl') == 'yes') {
			window.location.href=BTN.attr('href') + $('#fbody').find('table.DFTable').find('tbody tr.checked:first').attr('id');
		}
	}

	return false;
};

//********************************************************************************* //

Forms.DatatablesInit = function(){

	$('table.datatable').each(function(index, elem){

		// Store, for quick access
		var DTE = $(elem);

		if (DTE.data('disabled') == 'yes') {
			return;
		}

		// Initialize the datatable
		Forms.DataTables[DTE.data('name')] = DTE.dataTable({
			sPaginationType: 'full_numbers',
			sDom: 'R<"toptable"rl>t<"bottomtable" ip>',
			sAjaxSource: Forms.AJAX_URL+'&'+DTE.data('url'),
			aoColumns:Forms.DatatablesCols[DTE.data('name')],
			bAutoWidth: false,
			iDisplayLength: 15,
			bProcessing: true,
			fnServerData: function ( sSource, aoData, fnCallback ) {

				var DT = Forms.DataTables[ $(this).data('name') ];

				aoData.push( {name: 'mcp_base', value: Forms.MCP_BASE} );

				// Add all filters to the POST
				var Filters = Forms.LeftBarFilters.find(':input').serializeArray();
				for (var attrname in Filters) {
					aoData.push( {name: Filters[attrname]['name'], value:Filters[attrname]['value'] } );
				}

				/*
				// Loop over all rows to check the already checked ones
				jQuery.each(Forms.LeftBarColumns.find('span.label-success'), function(index, elem){
					aoData.push( {name: 'visible_cols[]', value: elem.getAttribute('data-field')} );
				});*/

				// Send the AJAX request
				$.ajax({dataType:'json', type:'POST', url:sSource, data:aoData, success:function(rData){

					// Give it back
					fnCallback(rData);

					// Recalculate column sizes, if it's not the first time
					if (DT) DT.fnAdjustColumnSizing(false);

					// If it's the first time, lets do some magic
					else setTimeout(function(){

						// Find all datatables
						$('table.datatable').each(function(i, e){

							if ($(e).data('disabled') == 'yes') return;

							// And Resize their columns!
							Forms.DataTables[ $(e).data('name') ].fnAdjustColumnSizing(false);
						});
					}, 200);

				}});

			},
			fnDrawCallback: function(){

			},
			fnInitComplete: function(oSettings, json) {

				// Remove all column classes
				Forms.LeftBarColumns.find('span').removeClass('label-success');

				// Loop over all rows to check the already checked ones
				if ( typeof(oSettings.aoData[0]) != 'undefined' ){

					// Coumns 2 Index! (only if we have data!)
					Forms.DatatableCols = {};

					var Cols = oSettings.aoColumns;

					// Loop over all columns
					for(col in Cols){

						// Since are already looping over all cols, lets store the mapping here!
						Forms.DatatableCols[ Cols[col].mDataProp ] = col;

						// Is it visible?
						if (Cols[col].bVisible == true) Forms.LeftBarColumns.find('span[data-field='+Cols[col].mDataProp+']').addClass('label-success');
					}
				}
			},
			bServerSide: true,
			oLanguage: {
				sLengthMenu: 'Display <select>'+
					'<option value="15">15</option>'+
					'<option value="25">25</option>'+
					'<option value="50">50</option>'+
					'<option value="100">100</option>'+
					'<option value="-1">All</option>'+
					'</select> records'
			},
			oColReorder: {iFixedColumns:1},

			bStateSave: ((DTE.data('savestate') == 'no') ? false : true),
			fnStateSave: function (oSettings, oData) {
				if (localStorage) localStorage.setItem( 'DataTables_'+DTE.data('url'), JSON.stringify(oData) );
	        },
	        fnStateLoad: function (oSettings) {
				if (localStorage) return JSON.parse( localStorage.getItem('DataTables_'+DTE.data('url')) );
	        },
	        aaSorting: []
		});

	/*
		// Global Filter?
		if (DTE.find('.global_filter input').length > 0){
			DTE.find('.global_filter input').keyup(function(EV){
				clearTimeout(CRM.Timeout);
				CRM.Timeout = setTimeout(function(){
					CRM.Datatables[ DTE.data('name') ].fnFilter($(EV.target).val());
				}, 300);
			});
		}
	*/

	});
};

//********************************************************************************* //

Forms.DTColumnToggler = function(Event){
	var Target = $(Event.target);
	var ToggledColumn = Target.data('field');

	// Lets grab the first Datatable
	for (D in Forms.DataTables) {
		var DT = Forms.DataTables[D];
		break;
	}

	if ( typeof(DT.fnSettings().aoData[0]) != 'undefined' ){

		// Create local var
		var Cols = DT.fnSettings().aoColumns;

		// Loop over all cols
		for(col in Cols){

			if (ToggledColumn == Cols[col].mDataProp){

				// Is the column already visible?
				if (Target.hasClass('label-success') == true) {

					// Remove the class of course
					Target.removeClass('label-success');

					// Make it hidden
					DT.fnSetColumnVis(col, false, false);

					// Re-Calculate the column sizes (and don't fetch new data)
					DT.fnAdjustColumnSizing(false);
				}

				// The column was hidden
				else {
					// Add the class of course
					Target.addClass('label-success');

					// Mark it visible!
					DT.fnSetColumnVis(col, true);

					// Re-Calculate the column sizes (and don't fetch new data)
					DT.fnAdjustColumnSizing(false);
				}

			}
		}

	}
};

//********************************************************************************* //

Forms.ActivateSidebarFilters = function(){

	// Normal Text Inputs
	var TextInput = 0;
	Forms.LeftBarFilters.find('input[type=text]').keyup(function(Event){
		if (Event.target.name == false) return;

		// Clear the timeout
		clearTimeout(TextInput);

		// Trigger a new drawing
		TextInput = setTimeout(function(){
			for (DT in Forms.DataTables) {
				Forms.DataTables[DT].fnDraw();
			}
		}, 300);
	});

	// Dropdowns
	Forms.LeftBarFilters.find('select').change(function(Event){
		for (DT in Forms.DataTables) {
			Forms.DataTables[DT].fnDraw();
		}
	});

};

//********************************************************************************* //

Forms.DeleteForm = function(Event){
	if ( $(Event.target).parent().hasClass('enabled') == false) return;

	var Modal = $('#del_form').modal();
	var HTML = Forms.JSON.Alerts.delete_form;
	var PrimatyBTN = Modal.find('.btn-primary');
	Modal.find('.modal-body p').html(HTML);

	if ( typeof(PrimatyBTN.data('click_binded')) != 'undefined') return;
	PrimatyBTN.data('click_binded', true);

	Modal.find('.btn-primary').click(function(){
		window.location.href=$(Event.target).parent().attr('href') + $('#fbody').find('table.DFTable').find('tbody tr.checked:first').attr('id');
	});
};

//********************************************************************************* //

Forms.FentryDelete = function(Event){
	if ( $(Event.target).parent().hasClass('enabled') == false) return;

	var Modal = $('#del_fentry').modal();
	var HTML = Forms.JSON.Alerts.delete_total.replace('{TOTAL}', Forms.fBody.find('table.datatable').find('tbody tr.checked').length);
	var PrimatyBTN = Modal.find('.btn-primary');
	var BtnTxt = PrimatyBTN.text();
	Modal.find('.modal-body p').html(HTML);

	if ( typeof(PrimatyBTN.data('click_binded')) != 'undefined') return;
	PrimatyBTN.data('click_binded', true);

	PrimatyBTN.bind('click', function(){
		Modal.find('.btn-primary').addClass('disabled').html(Forms.JSON.Alerts.deleting);

		var IDS = [];
		Forms.fBody.find('table.datatable').find('tbody tr.checked').each(function(i, e){ IDS.push( e.id ); });

		$.post(Forms.AJAX_URL, {entries: IDS.join('|'), ajax_method: 'delete_fentry'}, function(){
			Modal.find('.btn-primary').removeClass('disabled').html(BtnTxt);

			for (DT in Forms.DataTables) Forms.DataTables[DT].fnDraw();
			if ( $('#EntriesDT').css('position') == 'absolute') Forms.ViewEntryBack();
			Modal.modal('hide');
		});
	});

};

//********************************************************************************* //

Forms.FentryPrint = function(Event){
	Event.preventDefault();
	if ( $(Event.target).parent().hasClass('enabled') == false) return;

	var IDS = [];
	Forms.fBody.find('table.datatable').find('tbody tr.checked').each(function(i, e){
		IDS.push(e.id);
	});

	var URL = Forms.AJAX_URL+'&ajax_method=print_pdf_fentry&ids='+IDS.join('|');

	// Mac ?
	if (navigator.platform == 'MacIntel' || navigator.userAgent.match(/Macintosh/)){
		window.open(URL, '_newtab');
		return false;
	}

	var Modal = $('#print_fentry').modal();
	var HTML = '<iframe width="100%" height="400px" src="'+URL+'" frameborder="0"</iframe>';

	Modal.find('.modal-body').html(HTML);

};

//********************************************************************************* //

Forms.ViewEntry = function(Event){
	var ActionsBar = $('#actionbar');
	Event.preventDefault();
	if ( $(Event.target).parent().hasClass('enabled') == false) return;
	ActionsBar.find('.loadingblock').css('display', 'inline-block');
	ActionsBar.find('.ExportForm').css('opacity', 0.3);
	ActionsBar.find('.ViewEntry').removeClass('enabled');
	$('#PageWrapper').css('min-height', $('#PageWrapper').height()).find('.dataTables_length').hide();
	$('#EntriesDT').css({position:'absolute'}).animate({"left":"-150%"}, "slow");

	Forms.LeftBarFilters.find('input[type=text]').attr('disabled','disabled');
	Forms.LeftBarFilters.find('select').attr('disabled','disabled').trigger("liszt:updated");
	Forms.LeftBarColumns.find('.label').css('opacity', 0.5);

	var ID = Forms.fBody.find('table.datatable').find('tbody tr.checked:first').attr('id');

	$.post(Forms.AJAX_URL, {fentry_id:ID, ajax_method: 'show_form_entry'}, function(rData){
		$('#fentry').html(rData);
		$('#FormEntryWrapper').css({left:function(){ return $(this).offset().left; }}).animate({"left":"0"}, "slow", function(){
			$('#actionbar').find('.loadingblock').hide();
		});
	});
};

//********************************************************************************* //

Forms.ViewEntryBack = function(Event){
	if (typeof(Event) != 'undefined') Event.preventDefault();
	var ActionsBar = $('#actionbar');
	$('#EntriesDT').css({left: function(){ return $(this).offset().left; }}).animate({"left":"0"}, "slow")
	.delay(600).css('position', 'relative').find('.dataTables_length').show();
	$('#FormEntryWrapper').css('left', function(){ return $(this).offset().left; }).animate({"left":"100%"}, "slow");
	$('#fentry').empty();
	ActionsBar.find('.ViewEntry').addClass('enabled');
	ActionsBar.find('.ExportForm').css('opacity', 1);
	Forms.LeftBarFilters.find('input[type=text]').removeAttr('disabled','disabled');
	Forms.LeftBarFilters.find('select').removeAttr('disabled','disabled').trigger("liszt:updated");
	Forms.LeftBarColumns.find('.label').css('opacity', 1);

};

//********************************************************************************* //

Forms.FormsExportDialog = function(Event){
	Event.preventDefault();

	var Modal = $('#FormsExportDialog').modal();
	var CheckedTRS = Forms.fBody.find('table.datatable').find('tbody tr.checked');
	var PrimatyBTN = Modal.find('.btn-primary');
	PrimatyBTN.html(PrimatyBTN.data('normal')).removeClass('disabled');

	var IDS = [];
	CheckedTRS.each(function(i, e){ IDS.push( e.id ); });

	if ( typeof(PrimatyBTN.data('click_binded')) != 'undefined') return;

	PrimatyBTN.data('click_binded', true);
	PrimatyBTN.bind('click', function(E){
		E.preventDefault();
		PrimatyBTN.html(PrimatyBTN.data('loading')).addClass('disabled');

		var FormDIV = Modal.find('form');
		FormDIV.find('.hidden_fields').empty();

		// Visible Cols
		var VisCols = '';
		jQuery.each(Forms.DataTables.form_entries.fnSettings().aoColumns, function(index, elem){
			if (elem.bVisible == true) VisCols += '<input name="export[visible_cols][]" type="hidden" value="'+elem.sName+'"/>';
		});

		// Current Entries
		var CurrentEntries = '';
		jQuery.each(Forms.DataTables.form_entries.fnSettings().aoData, function(index, elem){
			CurrentEntries += '<input name="export[current_entries][]" type="hidden" value="'+elem._aData.id+'"/>';
		});

		// Add hidden fields
		FormDIV.find('.hidden_fields').html(VisCols + CurrentEntries);

		FormDIV.attr('action', Forms.AJAX_URL + '&ajax_method=export_entries');
		FormDIV.submit();
		setTimeout(function(){ PrimatyBTN.html(PrimatyBTN.data('normal')).removeClass('disabled'); }, 2000);
	});

};

//********************************************************************************* //
