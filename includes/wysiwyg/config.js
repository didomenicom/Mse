/* Custom Config of the CKEditor */

CKEDITOR.editorConfig = function(config){
	config.resize_dir = 'vertical';
	config.toolbarCanCollapse = false;
	config.scayt_autoStartup = true;
	
	config.toolbar = 'Mouseware';
 
	config.toolbar_Mouseware =
	[
		{ name: 'document', items : [ 'Source','-', 'Preview', 'Maximize' ] },
		{ name: 'clipboard', items : [ 'Cut','Copy','Paste','PasteText','PasteFromWord','-','Undo','Redo' ] },
		{ name: 'editing', items : [ 'Find','Replace','-','SelectAll','-','Scayt' ] },
		{ name: 'insert', items : [ 'Image','Table','HorizontalRule','Smiley' ] },
		{ name: 'styles', items : [ 'Styles','Format' ] },
		{ name: 'basicstyles', items : [ 'Bold','Italic','Strike','-','RemoveFormat' ] },
		{ name: 'paragraph', items : [ 'NumberedList','BulletedList','-','Outdent','Indent','-','Blockquote' ] },
		{ name: 'links', items : [ 'Link','Unlink','Anchor' ] },
	];	
};

