/**
 * Low Variables JavaScript file
 *
 * @package        low_variables
 * @author         Lodewijk Schutte <hi@gotolow.com>
 * @link           http://gotolow.com/addons/low-variables
 * @copyright      Copyright (c) 2009-2013, Low
 */

(function($){

/**
 *  Drag and drop lists object
 */
$.fn.lowDragLists = function(){

	this.each(function(){

		// Set input names
		var input_on  = 'var['+ this.id.replace('low-drag-lists-', '') +'][]',
			input_off = '';

		// Get lists
		var $list_on  = $('.low-on', this),
			$list_off = $('.low-off', this);

		// Quick function to see if list item is On or not
		var isOn = function(li) {
			return $(li).parent().hasClass('low-on');
		};

		// Define callback function
		var switched = function(event, obj) {
			$('input', obj.item).attr('name', (isOn(obj.item) ? input_on : input_off));
		};

		// Initiate sortables
		$list_on.sortable({connectWith: $list_off, receive: switched});
		$list_off.sortable({connectWith: $list_on, receive: switched});

		// Add doubleclick event to lis in element
		$(this).delegate('li', 'dblclick', function(event){
			$(this).appendTo((isOn(this) ? $list_off : $list_on));
			switched(event, {item: this});
		});

	});

};

/**
 *  File Upload
 */
$.fn.lowFileUpload = function(){

	this.each(function(){

		// Determine vars
		var var_id  = this.id.replace(/^.*\-(\d+)$/, '$1'),
			$el     = $(this),
			$toggle = $('a', this),
			speed   = 200;

		// Create file input field
		var $upload = $('<input/>').attr({
			'type': 'file',
			'name': 'newfile['+var_id+']'
		}).css('display', 'none').change(function(){
			$el.addClass('has-file');
		});

		// Add events to toggle link to show/hide input field
		$toggle.toggle(function(event){
			event.preventDefault();
			$toggle.after($upload);
			$upload.fadeIn(speed);
		}, function(event){
			event.preventDefault();
			$el.removeClass('has-file');
			$upload.fadeOut(speed, function(){
				$upload.detach();
			});
		});

	});

};

/**
 *  Low Table variable type
 */
$.fn.lowTable = function(){

	this.each(function(){

		// Get elements we need
		var var_id = this.id.replace(/^.*\-(\d+)$/, '$1'),
			$el    = $(this),
			$tbody = $('tbody', this),
			$add   = $('tfoot a', this),
			cols   = $('thead th', this).length,
			rows   = $('tbody tr', this).length;

		// Add class to cell to get rid of padding in css
		$el.parent().addClass('low-table-cell');

		// Define addRow function
		$add.click(function(event) {

			// don't go anywhere
			event.preventDefault();

			// Create new row and append it to the table
			var $tr = $('<tr/>');
			$tbody.append($tr);

			// Loop thru cols and add <td><input /></td> for each one
			for (var i = 0; i < cols; i++) {

				var $td = $('<td/>'),
					$input = $('<input/>');

				$input.attr({
					'name': 'var['+ var_id +']['+ rows +']['+ i +']',
					'type': 'text'
				});

				$tr.append($td.append($input));
			}

			// Increase row count
			rows++;
		});

		// Add / remove class to table cell on focus / blur
		$el.delegate('input', 'focus', function(){
			$(this).parent().addClass('low-focus');
		});

		$el.delegate('input', 'blur', function(){
			$(this).parent().removeClass('low-focus');
		});

	});

};

/**
 *  Manage Variables list-table
 */
$.fn.lowManageList = function(){

	// Reference to table element
	var el;

	if ( ! (el = $(this).get(0))) return;

	// On/Off toggles
	$(this).find('a.onoff').click(function(event){
		event.preventDefault();
		var $link = $(this),
			type = this.href.split('#')[1],
			url = location.href.replace('method=manage', 'method=ajax_update'),
			id = $link.parents('tr').find('td:first-child').text();

		if (type) {
			$.post(url, {
				XID: EE.XID,
				var_id: id,
				type: type,
				status: ($link.hasClass('on') ? 'n' : 'y')
			}, function(){
				$link.toggleClass('on');
			});
		}
	});

	// Make it not selectable
	el.onselectstart = function() { return false; };

	// Select already checked rows (looking at you, firefox!)
	$(':checked', el).closest('tr').addClass('selected');

	// Get all checkboxes in this table
	var $boxes = $('tbody input[type=checkbox]', el);

	// (de)selects a single row
	$boxes.change(function(event) {

		var box  = this,
			$box = $(box);

		// Get parent TR
		var $tr = $box.closest('tr');

		// (de)select row depending on box status
		$tr[(box.checked ? 'addClass' : 'removeClass')]('selected');

	});

	// Catches click on a row
	$('tbody tr', el).click(function(event) {

		// Prevent text selection
		if (document.selection && document.selection.empty) {
			document.selection.empty();
		} else if (window.getSelection) {
			window.getSelection().removeAllRanges();
		}

		// Get clicked element name
		var clicked = event.target.tagName.toLowerCase(),
			$target = $(event.target);

		// Bail out if it's a link
		if (clicked == 'a') return;

		// Get checkbox in this row
		var $box = $('input[type=checkbox]', this),
			box  = $box.get(0);

		// Trigger click if not clicked itself
		if (clicked != 'input') $box.click();

		// Select range of boxes with shift-click
		if (event.shiftKey && box.checked) {

			// Get current box index and init other index
			var boxIndex   = $boxes.index(box);
			var otherIndex = -1;

			// Get nearest checked box index above it
			for (var i = boxIndex - 1; i >= 0; i--) {
				if ($boxes[i].checked) {
					otherIndex = i;
					break;
				}
			}

			// If there is one, check all boxes in between 'em
			if (otherIndex >= 0) {

				while (--boxIndex > otherIndex) {
					$boxes[boxIndex].click();
				}

			// If there isn't...
			} else {

				// ...look down for the first checked box index
				for (var i = boxIndex + 1; i < $boxes.length; i++) {
					if ($boxes[i].checked) {
						otherIndex = i;
						break;
					}
				}

				// And check each of those in between
				while (++boxIndex < otherIndex) {
					$boxes[boxIndex].click();
				}
			}

		} // End shift-range click

	});

	// Show variable code on alt-click
	$('.low-var-name', el).click(function(event) {
		if (event.altKey) {
			prompt('Code:', '{'+ $.trim($(this).text()) +'}');
			event.preventDefault();
		}
	});

	// Toggle all checkboxes
	$('#low-toggle-all').change(function(){
		var all = this;

		$boxes.each(function(){
			console.log(all);
			if (all.checked != this.checked) {
				this.checked = all.checked;
				var method = this.checked ? 'addClass' : 'removeClass';
				$(this).parents('tr')[method]('selected');
			}
		});
	});

};

/**
 *  Stuff to execute onDomReady
 */
$(function(){

	// Create new drag-lists for each one found
	$('.low-drag-lists').lowDragLists();

	// File-upload fields
	$('.low-new-file').lowFileUpload();

	// Create new drag-lists for each one found
	$('.low-table').lowTable();

	// Manage List vars selections
	$('#low-list-vars').lowManageList();

	// Edit group - sort variables in group
	$('#low-variables-list').sortable({axis:'y', opacity:0.75});

	// Sortable groups in manage list
	$('.low-manager #low-sortable-groups').sortable({
		axis: 'y',
		opacity: 0.75,
		handle: '.low-handle',
		update: function(e, ui) {
			var new_order = [];

			$('.low-grouplink').each(function(){
				new_order.push($(this).attr('data-groupid'));
			});

			$.ajax({
				url: location.href + '&method=save_group_order',
				data: 'groups=' + new_order.join('|'),
				type: 'GET' // POST fucks it right up somehow...
			});
		}
	});

	// Toggle all types
	$('#low-select-all-types').click(function(){
		$('input[name*=enabled_types]').not(':disabled').attr('checked', this.checked);
	});

	// Toggle variable type tables
	$('#low-select-type').change(function(){
		$('table.low-var-type').hide();
		$('#' + $(this).val()).show();
	});

	// Toggle allow-multiple settings
	$('table.low-var-type').each(function(){
		var toggle = function() {
			var set = $(this).parents('tr').nextAll();
			this.checked ? set.show() : set.hide();
		};
		$('input[class=low-allow-multiple]', this).each(toggle).click(toggle);
	});

	// Variable group stuff
	var $saveAsNew = $(':checkbox[name=save_as_new_group]');
	var toggleNewGroupOptions = function() {
		var $target = $('#new-group-options'),
			speed = 200;
		if ($saveAsNew.is(':checked')) {
			$target.slideDown(speed);
		} else {
			$target.slideUp(speed);
		}
	};

	// On error, show the options again
	toggleNewGroupOptions();
	$saveAsNew.change(toggleNewGroupOptions);


	$('#first-group').each(function(){
		var $this = $(this),
			$link = $('<a href="#" id="low-collapse"/>'),
			$groups = $('#low-grouplist'),
			$vars = $('#low-varlist'),
			$that = $groups.find('th'),
			opened = '&lsaquo;]',
			closed = '[&rsaquo;',
			margin = $vars.css('marginLeft')
			speed = 200;

		$('#mainContent .pageContents').css('min-height', $groups.css('height'));

		$link.html(opened);
		$link.appendTo($that);

		$link.toggle(
			function(event){
				event.preventDefault();
				$groups.animate({width:'hide'}, speed);
				$vars.animate({marginLeft: 0}, speed);
				$link.html(closed);
				$link.prependTo($this);
			},
			function(event){
				event.preventDefault();
				$groups.animate({width:'show'}, speed);
				$vars.animate({marginLeft:margin}, speed);
				$link.html(opened);
				$link.prependTo($that);
			}
		);
	});

});
})(jQuery);