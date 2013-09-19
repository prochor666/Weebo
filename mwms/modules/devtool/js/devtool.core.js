/**
 * jquery.dump.js
 * @author Torkild Dyvik Olsen
 * @version 1.0
 * 
 * A simple debug function to gather information about an object.
 * Returns a nested tree with information.
 * 
 */
(function($) {

function ltrim(str) {
   return str.replace(new RegExp("^[\\s]+", "g"), "");
}

function rtrim(str) {
   return str.replace(new RegExp("[\\s]+$", "g"), "");
}

})(jQuery);


var devtool = {
	
	/* set defaults */
	settings: {

	},
	
	/* external settings */
	options: {},
	
	/* init settings */
	init: function() { 
		$.extend(this.settings, this.options);
		
		$("#tabs").tabs({
				   
			ajaxOptions: {
				error: function(xhr, status, index, anchor) {
					$(anchor.hash).html("MODULE ERROR - no tab content");
				}
			},
			cache: false,
		
		});	
	},
	
	uiInit: function(){
		
		$('div#mwms_devtool button.data_send, div#mwms_devtool button.data_resend').button({
			icons: {
				primary: "ui-icon-play"
			}
		});
		
	},
	
	dateUtil : function(options){
		
		$('.devtool_date').each(  function()
		{
			var obj = $(this);
			
			if(obj.attr('id') == 'devtool_source'){
				
				ValidatorDataTypes.datetime(obj, options);
				
				$('.ui-datepicker-trigger').button({
					icons : {
						'primary' : 'ui-icon-calendar'
					},
					text : false
				});
				//obj.prop('readonly', false);
			}
		});
		
	},
	
	showPreloader: function (elem, load_default){
		$(elem).html('<div class="dialog_loader"></div>');
		$('div.dialog_loader').css({'margin-top': load_default + 'px' });
	},

	selectData: function(tool){
		$('div#'+tool+' #devtool_result').click( 
			function(){
				$(this).select();
			}
		);  
	},
	
	setEditor: function(){
		
		var myWrap = $('<div/>').addClass('editorCell');
		
		//var myEditor = CodeMirror.fromTextArea(document.getElementById("devtool_source"),
		var myEditor = CodeMirror(myWrap.get(0), {
				lineNumbers: true,
				matchBrackets: true,
				mode: "application/x-httpd-php",
				indentUnit: 4,
				indentWithTabs: true,
				enterMode: "keep",
				tabMode: "shift",
				smartHome : true,
				undoDepth : 100
			});
			
		$("#php_run .devtool_source_wrap").prepend(myWrap);
		$("#php_run .devtool_source_wrap #devtool_source").hide();
		
		return myEditor;
	},

	copyEditorValue: function(){
		//alert($(this).dump());
	},

	removeEditor: function(editor){
		var tor = editor.getWrapperElement();
		tor.remove();
	},
	
	infoEditor: function(editor){
		var tor = editor.getWrapperElement();
		alert(tor.className);
	},
	
	createToolbar: function(){
		var toolbar = '<div class="devtoolbar"></div>';
	},
	
	bindAction: function(tool){
		
		/* Result width fix */
		var rw1 = parseInt($('div#'+tool+' div.devtool_source_wrap').css('width'));
		var rw2 = parseInt($('div#'+tool+' div.devtool_separator_wrap').css('width'));
		var rw3 = parseInt($('div#'+tool).css('width'));
		
		var rh1 = parseInt($('div#'+tool).css('height')) - 20;
		
		var rw4 = rw3 - (rw1 + rw2 + 40);
		
		$('div#'+tool).append('<div class="cleaner"></div>');
		
		$('div#'+tool+' div.devtool_result_wrap').animate(
		{
			width: rw4 + 'px',
			height: rh1 + 'px'
    
		}, 400); //.css({ 'border': '1px dashed #000' });
		
		
		/* Init editor */
		if(tool == 'php_run'){
			
			var myEditor = devtool.setEditor();
			myEditor.setValue('<?php ?>');
			/* File operations, load */
			$('div.php_sources a.file').each( function()
			{
				$(this).click( function()
				{
					var uri = weebo.settings.AjaxCall + 'method&fn=DevTool::get_php_source&qs='+$(this).attr('href');
					var my_data = ({});
					
					$.ajax({
						url: uri,
						type: 'post',
						data: my_data,
						dataType: 'text',
						async: false,
						cache: false,
						error: function(){},
						success: function(data) {
							var resultData = data;
							$('div#'+tool+' #devtool_source').val( resultData );
							myEditor.setValue( resultData );
						}
					});
					
					return false;
				});
			});
			
			/* File operations, remove */
			$('div.php_sources a.remove').each( function()
			{
				$(this).click( function()
				{
					var my_data = ({});
					var elem = $(this);
					
					$.ajax({
						url: weebo.settings.AjaxCall + 'method&fn=DevTool::delete_php_source&qs='+$(this).attr('href'),
						type: 'post',
						data: my_data,
						dataType: 'text',
						async: false,
						cache: false,
						error: function(){},
						success: function(data) {
							elem.parent().remove();
						}
					});
					
					return false;
				});
				
			}).button({
				icons: {
					primary: "ui-icon-circle-close",
					text: false
				}
			});
		
		}
		
		$('div#'+tool+' button.data_send').click( 
		 function(){
			
			if(tool == 'php_run'){
				$("#php_run .devtool_source_wrap #devtool_source").val( myEditor.getValue() );
			}
			
			var uri = weebo.settings.AjaxCall + 'method&fn=DevTool::' + $('div#'+tool+' #data_callback').val();
			
			var my_data = ({
				devtool_source: $('div#'+tool+' #devtool_source').val(),
				data_format: $('div#'+tool+' #data_format').val()
			});
			
			$.ajax({
				url: uri,
				type: 'post',
				data: my_data,
				dataType: 'text',
				async: false,
				cache: false,
				error: function(){},
				success: function(data) {
					if( $('#'+tool+' #devtool_result')[0].tagName == 'TEXTAREA' || $('#'+tool+' #devtool_result')[0].tagName == 'INPUT')
					{
						$('div#'+tool+' #devtool_result').val( data );
					}else{
						$('div#'+tool+' iframe#devtool_result').attr( 'src', data );
					}
				}
			});
		}); 
	}
}

 
