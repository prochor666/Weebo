var mediaLng = {
	mediaDirUpload : 'upload'
}

var media = {
	
	UID: 0,
	mountBox: '#mwms_media_mount_view_inner',
	fileBox: '#mwms_media_file_view_inner',
	headerBox: '#mount_header_path',
	dirMem: '#dir',
	mountView: 'require&file=/mwms/modules/media/view/media.mount.view.php', 
	
	openDir: function(xDir){
		
		var xDir = xDir;
		$(media.mountBox).html('');
		media.showPreloader('#mwms_media_mount_view_inner', 100);
		
		var my_data = ({
			dir : xDir
		});
		
		var uri = weebo.settings.AjaxCall + media.mountView + '&sysadmin=' + media.UID;
		
		$.ajax({
			url: uri,
			type: 'post',
			data: my_data,
			dataType: 'text',
			async: false,
			timeout : weebo.settings.AjaxTimeout,
			cache: false,
			success: function(data) {
				$(media.mountBox).html( data );
				media.saveDir(xDir); 
				$(media.headerBox).html(xDir);
				
				//weebo.setMessage('Status: ok');
				
				$('a.remove-file, a.remove-dir').button({
					icons: {
						primary: 'ui-icon-circle-close' 
					},
					text: false
				});

				$('#pl-controls').remove();
				$('#mount_header_path').after('<div style="display: inline-block;" id="pl-controls"></div>');
				$('#pl-controls').load(weebo.settings.AjaxCall + 'require&file=/mwms/modules/media/view/media.play.dir.controls.php&sysadmin='+media.UID, function()
				{

					$('#pl-controls button').each(
						function()
						{
							$(this).button({
									icons : {
										primary: 'ui-icon-play'
									}
								}).click(
								function()
								{
									media.playDir($(this).attr('id'));
								}
							);
							
						}
					);

				});

			},
			error: function(x, t, m) {
				media.handleError(x, t, m);
			}

		});
		return false;
	},
	
	playDir : function(group){
		
		var uri = weebo.settings.AjaxCall + 'require&file=/mwms/modules/media/view/media.play.dir.php&sysadmin='+media.UID;
		$(media.fileBox).html('');
		
		var my_data = ({
			group : group
		});
		
		$.ajax({
			url: uri,
			type: 'post',
			data: my_data,
			dataType: 'text',
			async: false,
			cache: false,
			timeout : weebo.settings.AjaxTimeout,
			success: function(data) {
				$(media.fileBox).html(data);
			},
			error: function(x, t, m) {
				media.handleError(x, t, m);
			}
		});
		
		$(media.fileBox).dialog({ 
			title: group, 
			autoOpen: true,
			width: 725,
			height: 520,
			modal: true,
			buttons: {
				"Cancel": function(){
					$(this).dialog( "close" );
				}
			},
			close: function(){ 
				$(media.fileBox).html('');
			},  
		});
		
		return false;
	},
	
	makeDir: function(){
		var xDir = media.readDir();
		var newDir =  prompt("Dir: ", "");
		
		var my_data = ({
			dir : xDir,
			newDir: newDir
		});
		
		if(newDir && newDir.length>0)
		{
			
			var uri = weebo.settings.AjaxCall + 'require&file=/mwms/modules/media/view/media.dir.make.php&sysadmin='+media.UID;
			
			$.ajax({
				url: uri,
				type: 'post',
				data: my_data,
				dataType: 'text',
				async: false,
				cache: false,
				timeout : weebo.settings.AjaxTimeout,
				success: function(data) {
					media.openDir(xDir);
					$('#mstat').html( data );
				},
				error: function(x, t, m) {
					media.handleError(x, t, m);
				}
			});
		}
		return false;
	},
	
	openFile: function(xFile){

		$(media.fileBox).html('');
		media.showPreloader('#mwms_media_file_view_inner', 100);
		
		var my_data = ({
			file : xFile
		});
		
		var uri = weebo.settings.AjaxCall + 'require&file=/mwms/modules/media/view/media.file.view.php&sysadmin='+media.UID;
		
		$.ajax({
			url: uri,
			type: 'post',
			data: my_data,
			dataType: 'text',
			async: false,
			cache: false,
			timeout : weebo.settings.AjaxTimeout,
			success: function(data) {
				$(media.fileBox).html( data );
				return false;
			},
			error: function(x, t, m) {
				media.handleError(x, t, m);
				return false;
			}
		});
		
		$(media.fileBox).dialog({ 
			title: xFile, 
			autoOpen: false,
			width: 725,
			height: 520,
			modal: true,
			buttons: {
				"Cancel": function(){
					$(this).dialog( "close" );
				}
			},
			close: function(){ 
				$(media.fileBox).html('');
			},  
		}).dialog( 'open' );
		
		return false;
	},
	
	deleteFiles: function(xFile){
		/* File operations, remove */
		var xDir = media.readDir();
		var my_data = ({
			file : xFile,
			dir : xDir
		});
		
		var uri = weebo.settings.AjaxCall + 'require&file=/mwms/modules/media/view/media.file.delete.php&sysadmin='+media.UID;
		
		$.ajax({
			url: uri,
			type: 'post',
			data: my_data,
			dataType: 'text',
			async: false,
			cache: false,
			timeout : weebo.settings.AjaxTimeout,
			success: function(data) {
				$('#mstat').html( data );
				/*
				var xDir = media.readDir();
				media.openDir(xDir);
				*/ 
			},
			error: function(x, t, m) {
				media.handleError(x, t, m);
			}
		});
		return false;
	},
	
	deleteDirs: function(xRm){
		/* File operations, remove */
		var xDir = media.readDir();
		var my_data = ({
			rdir : xRm,
			dir : xDir
		});
		
		var uri = weebo.settings.AjaxCall + 'require&file=/mwms/modules/media/view/media.dir.delete.php&sysadmin='+media.UID;
		
		$.ajax({
			url: uri,
			type: 'post',
			data: my_data,
			dataType: 'text',
			async: false,
			cache: false,
			timeout : weebo.settings.AjaxTimeout,
			success: function(data) {
				$('#mstat').html( data );
				/*
				var xDir = media.readDir();
				media.openDir(xDir);
				*/ 
			},
			error: function(x, t, m) {
				media.handleError(x, t, m);
			}
		});
		return false;
	},
	
	uploadFiles: function(filter){
		
		$(media.fileBox).html('<div id="uploader-box"></div>');
		
		var UIF = Math.round((new Date()).getTime() / 1000);
		
		$("#uploader-box").plupload({
			// General settings
			runtimes : 'html5,flash,html4',
			url : weebo.settings.AjaxCall + 'require&file=/mwms/modules/media/view/media.upload.recieve.php&dir='+media.readDir()+'&sysadmin='+media.UID+'&uif='+UIF,
			max_file_size : '4096mb',
			chunk_size : '1mb',
			unique_names : false,
			// Resize images on clientside if we can
			//resize : {width : 320, height : 240, quality : 90},

			// Specify what files to browse for
			filters : [
				{ title : "My files", extensions : filter },
			],
			
			// Flash settings
			flash_swf_url : weebo.settings.SiteRoot + '/shared/plupload/js/plupload.flash.swf'
		});
		
		$(media.fileBox).dialog({ 
			title: $('#uploader').attr('title') + ' -> '+media.readDir(),
			autoOpen: false,
			width: 725,
			height: 520,
			modal: true,
			buttons: {
				"Cancel": function(){
					$(this).dialog( "close" );
				}
			},
			close: function(){
				var xDir = media.readDir();
				media.openDir(xDir);
				$(media.fileBox).html('');
			} 
		}).dialog( 'open' );
		
		return false;
	},
	
	handleError: function(o,e,m){
		var indicator = $('#mwms_drop_indicator');
		msg = 'timeout '+weebo.settings.AjaxTimeout+'ms expired, '+' STATUS: '+e;
		weebo.handleServerError(msg);
		return false;
	},
	
	showPreloader: function(elem, load_default){
		$(elem).html('<div class="dialog_loader"></div>');
		$('div.dialog_loader').css({'margin-top': load_default + 'px' });
	},
	
	readDir: function(){
		return $(media.dirMem).val();
	},
	
	saveDir: function(dir){
		$(media.dirMem).val(dir);
	},
	
	editorFiles : function(field_name, url, type, win){
		
		//alert("Field_Name: " + field_name + "nURL: " + url + "nType: " + type + "nWin: " + win); // debug/testing
		
		var ucache = '&ucache=' + Math.round(Math.random()*10000000000);
		
		var aTitle =mediaLng.mediaDirUpload;
		var cmsURL = weebo.settings.AjaxCall + 'require&file=/mwms/modules/media/view/media.admin.standalone.php&type=' + type +'&field_name=' + field_name + '&url=' + url + ucache;
		
		tinyMCE.activeEditor.windowManager.open({
			file : cmsURL,
			title : aTitle,
			width : 900,  // Your dimensions may differ - toy around with them!
			height : 600,
			resizable : "yes",
			inline : "yes",  // This parameter only has an effect if you use the inlinepopups plugin!
			close_previous : "no"
		}, {
			window : win,
			input : field_name
		});
		
		return false;
	}
}

/***
 * Pacth for dialog-fix ckeditor problem [ by ticket #4727 ]
 * 	http://dev.jqueryui.com/ticket/4727
 * also fixes plupload problem
 */

$.extend($.ui.dialog.overlay, { create: function(dialog){
	if (this.instances.length === 0) {
		// prevent use of anchors and inputs
		// we use a setTimeout in case the overlay is created from an
		// event that we're going to be cancelling (see #2804)
		setTimeout(function() {
			// handle $(el).dialog().dialog('close') (see #4065)
			if ($.ui.dialog.overlay.instances.length) {
				$(document).bind($.ui.dialog.overlay.events, function(event) {
					var parentDialog = $(event.target).parents('.ui-dialog');
					if (parentDialog.length > 0) {
						var parentDialogZIndex = parentDialog.css('zIndex') || 0;
						return parentDialogZIndex > $.ui.dialog.overlay.maxZ;
					}
					
					var aboveOverlay = false;
					$(event.target).parents().each(function() {
						var currentZ = $(this).css('zIndex') || 0;
						if (currentZ > $.ui.dialog.overlay.maxZ) {
							aboveOverlay = true;
							return;
						}
					});
					
					return aboveOverlay;
				});
			}
		}, 1);
		
		// allow closing by pressing the escape key
		$(document).bind('keydown.dialog-overlay', function(event) {
			(dialog.options.closeOnEscape && event.keyCode
					&& event.keyCode == $.ui.keyCode.ESCAPE && dialog.close(event));
		});
			
		// handle window resize
		$(window).bind('resize.dialog-overlay', $.ui.dialog.overlay.resize);
	}
	
	var $el = $('<div></div>').appendTo(document.body)
		.addClass('ui-widget-overlay').css({
		width: this.width(),
		height: this.height()
	});
	
	(dialog.options.stackfix && $.fn.stackfix && $el.stackfix());
	
	this.instances.push($el);
	return $el;
}});
