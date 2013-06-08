// ********************************************************************************* //
var ChannelImages = ChannelImages ? ChannelImages : {};
//********************************************************************************* //

$(document).ready(function() {

	$('#regensizes .sidebar a').click(ChannelImages.GrabImagesTotal);
	$('#start_regen').bind('click', ChannelImages.getBatch);
	$('#regen_images').delegate('.show_ajax_error', 'click', ChannelImages.ShowAjaxError);

	$('.ImportMatrixImages .submit').click(ChannelImages.ImportMatrixImages);
});

//********************************************************************************* //

ChannelImages.GrabImagesTotal = function(Event){

	var block = $('#regensizes .fieldcontent');
	block.find('.batches').empty();

	$.post(ChannelImages.AJAX_URL, {ajax_method:'grab_image_totals', field_id:Event.target.getAttribute('data-id')}, function(rData){
		block.find('.noselect').hide();
		for (var i = 0; i < rData.batches.length; i++) {
			block.find('.batches').append('<span class="label label-waiting" data-field="'+Event.target.getAttribute('data-id')+'" data-offset="'+rData.batches[i]+'">&nbsp;'+rData.batches[i]+'&nbsp;</span>&nbsp;&nbsp;')
		};
	}, 'json');

	return false;
};

//********************************************************************************* //

ChannelImages.getBatch = function(e) {
	var label = $('#regensizes .fieldcontent .batches .label-waiting:first');

	if (label.length == 0) return;
	var fieldid = label.data('field');
	var offset = label.data('offset');

	label.removeClass('label-waiting').addClass('label-info');

	$.post(ChannelImages.AJAX_URL, {ajax_method:'grab_image_ids', field_id:fieldid, offset:offset}, function(rData){

		var HTML = '';

		$('.regenblock table tbody').find('.nofiles').hide();

		for (var i = 0; i < rData.images.length; i++) {
			HTML += '<tr data-id="'+rData.images[i].image_id+'">';
			HTML += '<td>'+ rData.images[i].image_id +'</td>';
			HTML += '<td>'+ rData.images[i].filename +'</td>';
			HTML += '<td>'+ rData.images[i].title +'</td>';
			HTML += '<td><a href="'+ChannelImages.EntryFormURL+'&channel_id='+rData.images[i].channel_id+'&entry_id='+rData.images[i].entry_id+'" target="_blank">'+ rData.images[i].entry_id +'</a></td>';
			HTML += '<td><span class="label label-waiting">Waiting</span></td>';
			HTML += '</tr>';
		}

		$('.regenblock table tbody').append(HTML);

		setTimeout(function(){
			ChannelImages.StartRegen();
		}, 50);
	}, 'json');

	return false;

};

//********************************************************************************* //

ChannelImages.StartRegen = function(Event){

	// Get the first in queue
	var Current = $('.regenblock table').find('.label-waiting:first').closest('tr');

	if (Current.length == 0) {
		ChannelImages.getBatch();
	}

	Params = {};
	Params.XID = EE.XID;
	Params.ajax_method = 'regenerate_image_size';
	Params.image_id = Current.attr('data-id');

	Current.find('.label-waiting').removeClass('label-waiting').addClass('label-info').html('Processing');

	$.ajax({
		type: "POST",
		url: ChannelImages.AJAX_URL,
		data: Params,
		success: function(rData){
			if (rData.success == 'yes')	{
				Current.find('.label-info').removeClass('label-info').addClass('label-success').html('Done');
				Current.fadeOut('fast', function(){
					Current.remove();
				});
				ChannelImages.StartRegen(); // Shoot the next one!
			}
			else{
				Current.find('.label-info').removeClass('label-info').addClass('label-important').html('Failed');
			}
		},
		dataType: 'json',
		error: function(xhr){
			Current.find('.label-info').removeClass('label-info').addClass('label-important').html('Failed');
			Current.find('.label-important').after('&nbsp;&nbsp;<a href="#" class="label label-inverse show_ajax_error" style="color:#fff">Show Error</a>');
			Current.find('.show_ajax_error').data('ajax_error', xhr.responseText);
		}
	});


	return false;
};

//********************************************************************************* //

ChannelImages.ShowAjaxError = function(Event){
	Event.preventDefault();
	$('#error_log').find('.body').html( $(Event.target).data('ajax_error') );

	$('#error_log').show();

	$('html, body').stop().animate({
		scrollTop: $('#error_log').offset().top
	}, 1000);
	return false;
};

//********************************************************************************* //

ChannelImages.ImportMatrixImages = function(Event){

	var Current = jQuery(Event.target).closest('table').find('.CI_IMAGES').find('.Queued:first');
	var Params = jQuery(Event.target).closest('form').find(':input').serializeArray();

	if (Current.length === 0) return false;

	Params.push({name: 'ajax_method', value:'import_matrix_images'});
	Params.push({name: 'entry_id', value:Current.attr('rel')});
	Params.image_id = Current.attr('rel');

	Current.removeClass('Queued').addClass('label-info');

	$.ajax({
		type: "POST",
		url: ChannelImages.AJAX_URL,
		data: Params,
		success: function(rData){
			if (rData.success == 'yes')	{
				ChannelImages.ImportMatrixImages(Event);
				Current.removeClass('label-info').addClass('label-success');
			}
			else{
				Current.removeClass('label-info').addClass('label-warning');
			}
		},
		dataType: 'json',
		error: function(XMLHttpRequest, textStatus, errorThrown){
			Current.removeClass('label-info').addClass('label-warning');
		}
	});

	return false;
};

//********************************************************************************* //

ChannelImages.Debug = function(msg){
	try {
		console.log(msg);
	}
	catch (e) {	}
};

//********************************************************************************* //
