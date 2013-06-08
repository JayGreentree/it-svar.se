$(document).ready(function()
{
	//force superuser to be publisher
	$('#bwf_role_1_editor').attr('disabled','disabled');
	$('#bwf_role_1_publisher').attr('checked','checked');
	
	var me;
	var v;
	var c_id;
	var p;

	var form_html;
	var tdd;
	var ndd;
	
	$(".bwf_check_existing_channel_entries").click(function(){
	
		me = $(this);
		v = me.val();		
		c_id = me.attr('id').split('_')[1];
		p = me.parent();
		
		form_html = "";
		tdd = $("#channel_" + c_id + "_tdd");
		ndd = $("#channel_" + c_id + "_ndd");

		var ajax_url = $('input[name="bwf_ajax_url"]').val() + '&ajax_check_existing_channel_entries&channel_id=' + c_id;
		var checking_message = $('<p class="bwf_checking">Checking existing entries</p>');


		build_status_mapping_form = function(data)
		{
			form_html += "<div class=\"bwf_status_mapping_form\">";
			form_html += "<h4 class=\"bwf_alert\">Status mapping required</h4>";
			form_html += "<p>Oops! You have existing entries in this channel with custom statuses. If you wish to use <strong>Better Workflow</strong> on this channel you will have to <strong>permanently</strong> change the status of these entries. Please select your desired status below, or alternatively, disable <strong>Better Workflow</strong> on this channel.</p>"; 
			form_html += "<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\">";
			form_html += "<tr>";
			form_html += "<th>Your status</th>";
			form_html += "<th>BWF status</th>";
			form_html += "</tr>";
			for(s=0; s<data.statuses.length; s++)
			{
				form_html += "<tr>";
				form_html += "<td>" + data.statuses[s].status + "</td>";
				form_html += "<td>";
				form_html += "<input type=\"hidden\" name=\"channels[id_" + c_id + "][existing_statuses]["+s+"][old_status]\" value=\"" + data.statuses[s].status + "\"/>";
				form_html += "<select class=\"bwf_map_existing_channel_entries\" name=\"channels[id_" + c_id + "][existing_statuses]["+s+"][new_status]\">";
				form_html += "<option value=\"\">Please select</option>";
				form_html += "<option value=\"closed\">Closed</option>";
				form_html += "<option value=\"draft\">Draft</option>";
				form_html += "<option value=\"submitted\">Submitted for approval</option>";
				form_html += "<option value=\"open\">Open</option>";
				form_html += "</select>";
				form_html += "</td>";
				form_html += "</tr>";
			}
			form_html += "</table>";
			form_html += "</div>";

			p.find('p.bwf_checking').remove();
			p.append(form_html);
			
			$(".bwf_map_existing_channel_entries").change(function()
			{	
				all_mapped = true;
				$(this).parent().parent().parent().find('select.bwf_map_existing_channel_entries').each(function(index, item){
					if($(item).val() == '') all_mapped = false;
				});
				if(all_mapped)
				{
					show_dropdowns();
				}
				else
				{
					hide_dropdowns();
				}
			});
		}



		hide_dropdowns = function()
		{
			tdd.attr('disabled',true).addClass('bwf_hide');
			ndd.attr('disabled',true).addClass('bwf_hide');
		}

		show_dropdowns = function()
		{
			tdd.attr('disabled',false).removeClass('bwf_hide');
			ndd.attr('disabled',false).removeClass('bwf_hide');
		}

		
		// Start the status checking process
		if(v == 'yes')
		{
			$.ajax({
				url: ajax_url,
				type: 'post',
				dataType: 'JSON',
				beforeSend: function() {
					p.append(checking_message);
				},
				success: function(data)
				{
					if(data.response == 'ok')
					{
						p.find('p.bwf_checking').remove();
						show_dropdowns();
					}
					else
					{
						build_status_mapping_form(data);
					}
				}
			});
		}
		else
		{
			hide_dropdowns();
			p.find('p.bwf_checking').remove();
			p.find('div.bwf_status_mapping_form').remove();
		}	
	});
	
	// Add validation to submit button
	$('input.submit').click(function() {
		
		var all_good = true;
		var message = "Please check the templates you have selected for the following channel(s):\n\n"
		var self;
	
		$('.bwf_check_existing_channel_entries').each(function() {
		
			self = $(this);
			if(($(this).val() == 'yes') && ($(this).is(":checked")))
			{
				theChannelName = $(this).parents('tr').children('td:first').html();
				theAttr = $(this).attr('id');
				theId = theAttr.substring(0, theAttr.length - 4);
				theTemplateID = theId + '_tdd';
				theTemplateSelect = $('#' + theTemplateID);
				//theRow = self.parents('tr');
				
				if (theTemplateSelect.val() == '0') 
				{
					message += theChannelName + "\n";
					all_good = false;
				}
			}
			
		});
	
		if(!all_good)
		{
			alert(message);
			return false;
		}
	
	});
	
	// View log
	$('a[href="ajax_fetch_log_file"]').click(function(event)
	{
		var logWindow = $("<div id='bwf_log_dialog'></div>");
		var ajax_url = $('input[name="bwf_ajax_url"]').val() + '&ajax_fetch_log_file';
	
		$(logWindow).insertAfter('#footer');
	
		$("#bwf_log_dialog").dialog({
			resizable: false,
			modal: true,
			title: "Better Workflow Log",
			height: $(window).height() - 100,
			width: $(window).width() - 100,
			close: function() {
          		$('#bwf_log_dialog').remove();
        	}
		}).html('<div id="epBwf_preview_spinner">&nbsp;</div>');
		
		$.ajax({
			url: ajax_url,
			context: $('#bwf_log_dialog'),
			success: function(data)
			{
				$(this).html('<div id="bwf_log_dialog_inner">' + data + '</div>');
			}
		});

		event.preventDefault();
	});
	
	// Clear log
	$('a[href="ajax_delete_log_file"]').click(function(event)
	{
		var ajax_url = $('input[name="bwf_ajax_url"]').val() + '&ajax_delete_log_file';
		
		var confirm_delete = confirm("Are you sure you want to clear the log file?");
		    if (confirm_delete){
			$.ajax({
				url: ajax_url,
				dataType: 'JSON',
				success: function(data) 
				{
					if(data.response == 'ok')
					{
						$.ee_notice("Better Workflow log file cleared", {type:'success'});
					} 
					else 
					{
						$.ee_notice("Whoops, there was a problem clearing Better Workflow's log file", {type:'error'});
					}
				}
			});
		    }
		event.preventDefault();
	});
})