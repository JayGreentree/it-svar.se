(function () {
'use strict';

	var $ = jQuery;

	/*
	* Public:
	*
	* Constructors a set of buttons from a Workflow stateTransition
	* transition - an instance of EditorTransition or PublisherTransition
	* returns nothing.
	*/

	var ButtonsUI = function (transition) {
		this.transition = transition;
	};

	ButtonsUI.prototype = {


	/*
	* Public:
	* Renders the buttons into the the page dom.
	* returns nothing.
	*/
	prep: function () {
		this._prepareDom();
	},


	/*
	 * Public:
	 * Appends the buttons to the Better Workflow button containers
	 * returns nothing.
	 */
	 render: function ()
	 {
		var transition = this.transition,
		self = this,
		state;

		//console.log('Current state: ' + transition.currentState);
		//console.log('Previous state: ' + transition.previousState);

		// Empty the buttons (if there are any)
		$('.bwf_control_unit ul').html('');

		$.each(transition.states, function(i, state)
		{
			if(state.name == transition.currentState)
			{
				// Add some debugging information	
				Bwf.debug('Better Workflow: Button group found for current entry state: \'' + transition.currentState + '\'');
				
				$.each(state.buttons, function(optionIndex, option){
					var btnValue = self._buttonValue(option),
					btnClass = self._buttonClass(btnValue, option.method), // Need to look at class creation method
					btnName =  self._buttonName(option),
					btnLabel = option.label;

					$(".bwf_control_unit ul").append(
						"<li><button type='submit' class='" + btnClass + "' name='" + btnName + "' value='" + btnValue +"'>" + btnLabel + "</button></li>" 
					);

					//bind click event to the buttons outside the form
					$('button[name="' + btnName + '"]').eq(0).click( function() {
						$('button[name="' + $(this).attr('name') + '"]').get(1).click();
					});

					//bind confirm dialog to delete buttons
					if (option.method == 'delete' ) {
						$("#publishForm button[name='" + btnName + "']").click( function(event) {
							self.confirmDelete(event);
						});
					}
					
					// Add some debugging information
					Bwf.debug('Better Workflow: \'' + option.label + '\' button added to control bar');
				});

				// Now we have a current state update the container
				self._setControlClass();
				$('.bwf_control_unit h3').html(state.label.toUpperCase());

				// If this is a read only state -apply the overlay
				if (!state.canEdit) {
					$('#holder').prepend("<div id='bwf_locked_publish'>&nbsp;</div>");
				}
				
				// break the each loop
				return false;
			}
		});
	},


	/*
	* Prepares the EE entry edit DOM for injecting Better Workflow UI elements.
	* returns nothing.
	*/
	_prepareDom: function () {
		this._hideStatusSelectInput();
		this._removeDefaultSubmit();
		this._insertButtonContainer();
	},



	/*
	* Hides EE's default select input for changing an entry state.
	* returns nothing.
	*/
	_hideStatusSelectInput: function () {
		$('select[name=status]').attr('disabled','disabled');
		$('#hold_field_status').hide();
	},


	/*
	* Removes the default submit button from the entry editing form.
	* returns nothing
	*/
	_removeDefaultSubmit: function () {
		$("#publish_submit_buttons").remove();
	},


	/*
	* Adds to the DOM the two containers elements wherein the Workflow buttons will be appended.
	* returns nothing.
	*/
	_insertButtonContainer: function () {
		var container = $("<div class='bwf_control_unit'><ul></ul><h3></h3></div>");
		$($(container).clone()).insertAfter('.heading');
		$(container).insertAfter('#holder');

	},


	/*
	 * Gets a button's value on the basis of the transition data.
	 * Params - the button object
	 * returns - A string to be used as the button value.
	 */
	_buttonValue: function (option)
	{
		var btnValue = option.newState;
		if (option.entity == 'epBwfDraft' && typeof option.method !== 'undefined') btnValue += '|' + option.method;
		return btnValue;
	},


	/*
	* Gets the button name on the basis of the transition data.
	* returns a string
	*/
	_buttonName: function(option)
	{
		var btnName = option.entity + '_' + option.method + '_' + option.newState;
		if(option.notify) btnName += '_epBwfNotify';
		return btnName;
	},


	/*
	* Sets the correct class to the control bar based on status
	* returns nothing
	*/
	_setControlClass: function()
	{
		$('.bwf_control_unit').attr('class', 'bwf_control_unit ' + this._statusClassName());
	},


	/*
	* Gets a classname for this entry's status.
	* returns a string.
	*/
	_statusClassName: function () {
		return this.transition.draftStatus || this.transition.entryStatus || this.transition.defaultStatus;
	},


	/*
	* Gets a button classname.
	* btnValue    - The value attribute of the button.
	* dbOperation - The value of this.transition.options[{draft|entry}].dbOperation.
	*/
	_buttonClass: function (btnValue, dbOperation) {
		var btnClass = dbOperation ?  'epBwfDraft' : 'epBwfEntry';

		if( btnValue == 'draft|create' || btnValue == 'draft' || btnValue == 'draft|update' ) {
			btnClass = 'bwf_red_button bwf_save_button';
		}
		else if (btnValue == 'submitted' || btnValue == 'submitted|create' || btnValue == 'submitted|update' || btnValue == 'open|replace' || btnValue == 'open' ) {
			btnClass = 'submit bwf_save_button';
		}
		else if (btnValue == 'null|delete' || btnValue === 'closed') { 
			btnClass = 'bwf_blue_button';
		}

		return btnClass;
	},


	/*
	* Event handler for adding a confirm dialog to delete buttons.
	* event - a blur event
	*/
	confirmDelete: function (event) {
		var go_ahead = confirm("Are you sure you want to delete this draft? This action cannot be undone.");
		if (go_ahead) {
			return;
		} else {
			event.preventDefault();
		}
	}
};


this.ButtonsUI = ButtonsUI;


}).call(Bwf);
