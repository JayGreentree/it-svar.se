var Editor = Editor ? Editor : {};
Editor.matrixColConfigs = {};
// ********************************************************************************* //

jQuery(document).ready(function(){

	if ('undefined' !== typeof window['Matrix']) Editor.MatrixInit();
});

// ********************************************************************************* //

Editor.UploadCallback = function(obj, json) {

};

// ********************************************************************************* //

Editor.MatrixInit = function(){

	Matrix.bind('editor', 'display', function(cell){
		var $textarea = $('textarea', cell.dom.$td);
		var config = Editor.matrixColConfigs[cell.col.id];
		var id = cell.field.id+'_'+cell.row.id+'_'+cell.col.id+'_'+Math.floor(Math.random()*100000000);

		id = id.replace(/\[/, '_').replace(/\]/, '');

		$textarea.attr('id', id);

		jQuery('#'+id).redactor(config);
		cell.dom.$td.addClass('editor-matrix');
	});
};

// ********************************************************************************* //

Editor.TriggerRedactorCleanup = function(redactor) {

	for (var i = 0; i < 5; i++) {
		setTimeout(function(){
			redactor.syncCode();
			redactor.opts.visual = false;
			redactor.toggle();
		}, (i*100));
	};
};

// ********************************************************************************* //
