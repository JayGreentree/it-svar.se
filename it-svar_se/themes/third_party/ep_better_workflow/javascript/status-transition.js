(function () {
'use strict';

	// State object constructor
	function BwfState(name, args, buttons)
	{
		this.name = name;
		this.label = (typeof(args['label']) === 'undefined') ? '' : args['label'];
		this.canEdit = (typeof(args['can_edit']) === 'undefined') ? true : args['can_edit'];
		this.canPreview = (typeof(args['can_preview']) === 'undefined') ? true : args['can_preview'];
		this.buttons = buttons;
	}

	// Button object constructor
	function BwfButton(newState, entity, method, label, notify)
	{
		this.newState = newState;
		this.entity = entity;
		this.method = method;	
		this.label = label;
		this.notify = notify;
	};


	// StatusTransition constructor
	function StatusTransition (args)
	{
		// Set the log evets flag at Bwf level
		Bwf.logEvents			= (typeof(args.logEvents) === 'undefined' ) ? false : args.logEvents; 

		this.userRole			= args.userRole;
		this.entryId			= args.entryId;
		this.urlTitle			= args.urlTitle
		this.entryExists		= (typeof(args.entryExists) === 'undefined' ) ? false : args.entryExists;
		this.draftExists		= (typeof(args.draftExists) === 'undefined' ) ? false : args.draftExists;
		this.entryStatus		= (typeof(args.entryStatus) === 'undefined' ) ? null : args.entryStatus;
		this.draftStatus		= (typeof(args.draftStatus) === 'undefined' ) ? null : args.draftStatus;
		this.baseURL			= (typeof(args.baseURL) === 'undefined' ) ? null : args.baseURL;
		this.previewTemplate	= (typeof(args.previewTemplate) === 'undefined' ) ? null : args.previewTemplate;
		this.previewLastSegment = (typeof(args.previewLastSegment) === 'undefined' ) ? null : args.previewLastSegment;
		this.showFullUrl		= (typeof(args.showFullUrl) === 'undefined' ) ? null : args.showFullUrl;
		this.showPreviewOnNew	= (typeof(args.showPreviewOnNew) === 'undefined' ) ? null : args.showPreviewOnNew;
		this.disablePreview		= (typeof(args.disablePreview) === 'undefined' ) ? null : args.disablePreview;
		this.showErrors			= (typeof(args.showErrors) === 'undefined' ) ? null : args.showErrors;
		this.EEVersion			= (typeof(args.EEVersion) === 'undefined' ) ? null : args.EEVersion;
		this.ignorePageUrl		= (typeof(args.ignorePageUrl) === 'undefined' ) ? null : args.ignorePageUrl;
		this.defaultStatus		= (typeof(args.defaultStatus) === 'undefined' ) ? 'draft' : args.defaultStatus;
		this.externalPreviews	= (typeof(args.externalPreviews) === 'undefined' ) ? false : args.externalPreviews;

		this.entryOptions		= [];
		this.draftOptions		= [];
		this.formIsEditable		= true;

		this.roleOptions;
		this.states = [];

		this.currentState;
		this.previousState;
	}

	
	// StatusTransition prototype (extends StatusTransition object)
	StatusTransition.prototype ={

		userRole: null,
		entryStatus: null,
		draftExists: null,
		draftStatus: null,

		processUserOptions: function() {
			var state,
			stateObj,
			button,
			buttonArr,
			buttonObj,
			self = this;

			for (state in this.roleOptions) 
			{
				stateObj = new BwfState(state, self.roleOptions[state], new Array());
				for(button in self.roleOptions[state]['buttons'])
				{
					buttonArr = self.roleOptions[state]['buttons'][button];
					stateObj.buttons.push(new BwfButton(buttonArr[0], buttonArr[1], buttonArr[2], buttonArr[3], buttonArr[4]));
				}
				self.states.push(stateObj);
			}
		},

		render: function() {
			var uiButtons, uiPreview;

			this.processUserOptions();
			this.currentState = this.entryStatus + '|' + this.draftStatus;
			this.previousState = '';

			// Add some debugging information to the console
			Bwf.debug('Better Workflow: Current user role: \'' + this.userRole + '\'');
			Bwf.debug('Better Workflow: Current entry state: \'' + this.currentState + '\'');

			uiButtons = new Bwf.ButtonsUI(this);
			uiButtons.prep();
			uiButtons.render();

			uiPreview = new Bwf.PreviewBox(this);
			uiPreview.render();

			// Assign the uiButtons object to the StatusTransition object
			// e.g. the 'this' here refers to StatusTransition
			this.uiButtons = uiButtons;
			this.uiPreview = uiPreview;
		}
	};


	// Assign the StatusTransition object to the Bwf object
	// e.g. 'this' here refers to Bwf
	this.StatusTransition = StatusTransition;

}).call(Bwf);
