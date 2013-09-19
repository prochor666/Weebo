var cmsLng = {
	resetButton : 'reset',
	contentAnnotationImageLoad : 'imageLoad',
	ajaxUploadEnd: 'uploadFinished'
}

var cms = {
	
	/* Create static TABs */
	initUI : function(){
		$("div.cms_menu a:first").button({
			icons: {
				primary: "ui-icon-folder-collapsed"
			}
		}).next().button({
			icons: {
				primary: "ui-icon-image"
			}
		}).next().button({
			icons: {
				primary: "ui-icon-document"
			}
		});
		
	},
	
	initTabs : function(){
		var tabs = $("#tabs").tabs({
		
			ajaxOptions: {
				error: function(xhr, status, index, anchor) {
					$(anchor.hash).html("Loading tab content...");
				}
			},
			cache: false,
	
		});	
		
		tabs.delegate( "span.ui-icon-close", "click", function() {
			var panel = $( this ).closest( 'li' );
			var panelContent = panel.attr( 'aria-controls' );
			panel.remove();
			$( "#" + panelContent ).remove();
			tabs.tabs( "refresh" );
			tabs.tabs( "enable" )
		});
	},
	
	/* Ad new TAB */
	addTab : function(content_uri, tabID, title, focus){
		
		title = title.length > 0 ? title: tabID;
		
		var tabTemplate = $('<li><a href="#ui-custom-'+tabID+'">'+title+'</a> <span class="ui-icon ui-icon-close" role="presentation">x</span></li>');
		
		if( $('#tabs #ui-custom-' + tabID).length<1 ){
			$('#ui-custom-' + tabID).remove();
			var newTab = $('<div id="ui-custom-' + tabID + '"></div>');
			newTab.load( content_uri );
			$( '#tabs .ui-tabs-nav' ).append( tabTemplate );
			$( '#tabs' ).append( newTab ).tabs( "refresh" );
		}
		
		if(focus === true){
			var index = $( 'a[href="#ui-custom-'+tabID+'"]' ).closest('li').index();
			$('#tabs').tabs('option', 'active', index);
		}
	},
	
	closeTab : function(index){
		var panel = $( '#tabs li:eq('+index+')' );
		var panelContent = panel.attr( 'aria-controls' );
		panel.remove();
		$( "#" + panelContent ).remove();
		$('#tabs').tabs( "refresh" );
	},
	
	/* Activity indicator */
	showPreloader : function(elem, topMargin){
		weebo.showPreloader(elem, topMargin);
	},
	
	toggleToolbar: function( conf ){
		 
		var toolbar = $('<div class="mwms-data-toolbar"></div>');
		$(conf.element+':first').append(toolbar);
		
		var l = conf.buttons.length;
		
		for(i=0;i<l;i++)
		{
			var xbutton = conf.buttons[i];
			
			toolbar.append('<button class="'+xbutton.name+'">'+xbutton.title+'</button>');
			var item = toolbar.find("button."+xbutton.name);
			
			item.click( xbutton.xcall ).button({
				icons : {
					primary: xbutton.icon
				},
				text: xbutton.text
			});
			toolbar.disableSelection();
		}
	},

	removeItem : function(delURI, title){
		var u = delURI + '&action=del';
		weeboMeta.showActionDialog(u, title, 500 , 200);
	},

	chooseScriptMethod : function(id, url){
		var ucache = '&ucache=' + Math.round(Math.random()*10000000000);
		var container = $('#edit_field__cms_content_'+id+'_display_script_param_method_helper');
		
		container.load(weebo.settings.AjaxCall + url + ucache, function(){
			
			var defaultWidget = $('#edit_field__cms_content_'+id+'_display_script_param');
			var selectWidget = $('#param_select_method_data_'+id);
			var container = $('#edit_field__cms_content_'+id+'_display_script_param_method_helper');
			
			defaultWidget.val(selectWidget.val());
			
			selectWidget.change(
				function(){
					defaultWidget.val($(this).val());
				}
			);
			
			selectWidget.blur(
				function(){
					defaultWidget.val($(this).val());
				}
			);
		});
		
	},

	newLink : function(dURI, xtitle){
		
		var ucache = '&ucache=' + Math.round(Math.random()*10000000000);
		
		var buttons_def = {};
		
		var saveButton = cmsLng.resetButton;
		buttons_def[saveButton] = function() {
			$('#weebo-modal-dialog-content').dialog('close');
		}
		
		$('#weebo-modal-dialog-content').html('').load(weebo.settings.AjaxCall + dURI + ucache).dialog({
			autoOpen: false,
			height: 690,
			width: 855,
			title: xtitle,
			modal: true,
			show: 'slide',
			hide: 'blind',
			buttons: buttons_def,
			close: function() {
				$('#weebo-modal-dialog-content').html('');
			}
		});
		
		$('#weebo-modal-dialog-content').dialog('open');
	},

	newDir : function(dURI, xtitle){
		
		var ucache = '&ucache=' + Math.round(Math.random()*10000000000);
		
		var buttons_def = {};
		
		var saveButton = cmsLng.resetButton;
		buttons_def[saveButton] = function() {
			$('#weebo-modal-dialog-content').dialog('close');
		}
		
		$('#weebo-modal-dialog-content').html('').load(weebo.settings.AjaxCall + dURI + ucache).dialog({
			autoOpen: false,
			height: 400,
			width: 855,
			title: xtitle,
			modal: true,
			show: 'slide',
			hide: 'blind',
			buttons: buttons_def,
			close: function() {
				$('#weebo-modal-dialog-content').html('');
			}
		});
		
		$('#weebo-modal-dialog-content').dialog('open');
	},

	editorFiles : function(field_name, url, type, win){
		
		//alert("Field_Name: " + field_name + "nURL: " + url + "nType: " + type + "nWin: " + win); // debug/testing
		
		var ucache = '&ucache=' + Math.round(Math.random()*10000000000);
		
		var aTitle = cmsLng.contentAnnotationImageLoad;
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
	},

	attachEditorFile : function(xFile){
		alert(xFile);
		return false; 
	},

	attachAnnotationFile : function(elem, file, thumb){
		$(elem).val(file);
		$(elem+'_thumb').attr('src', thumb).removeClass('hide');
		return false;
	},

	detachAnnotationFile : function(elem){
		$(elem).val('');
		$(elem+'_thumb').attr('src', '').addClass('hide');
		return false;
	},

	newFile : function(dURI, xtitle){
		
		var ucache = '&ucache=' + Math.round(Math.random()*10000000000);
		
		var buttons_def = {};
		
		var saveButton = cmsLng.resetButton;
		buttons_def[saveButton] = function() {
			$('#weebo-modal-dialog-content').dialog('close');
		}
		
		$('#weebo-modal-dialog-content').html('').load(weebo.settings.AjaxCall + dURI + ucache).dialog({
			autoOpen: false,
			height: 450,
			width: 800,
			title: xtitle,
			modal: true,
			show: 'slide',
			hide: 'blind',
			buttons: buttons_def,
			zIndex: 9999,
			close: function() {
				$('#weebo-modal-dialog-content').html('');
			}
		});
		
		$('#weebo-modal-dialog-content').dialog('open');
		return false;
	},
	
	uploadFiles: function(filter){
		$('#uploader-box-wrapper').html('<div id="uploader-box"></div>');
		
		var UIF = Math.round((new Date()).getTime() / 1000);
		
		$("#uploader-box").plupload({
			// General settings
			runtimes : 'html5,flash,html4',
			url : weebo.settings.AjaxCall + 'require&file=/mwms/modules/media/view/media.upload.recieve.php&dir='+media.readDir()+'&sysadmin='+media.UID+'&uif='+UIF,
			max_file_size : '2048mb',
			chunk_size : '256kb',
			unique_names : false,
			// Resize images on clientside if we can
			//resize : {width : 320, height : 240, quality : 90},

			// Specify what files to browse for
			
			filters : [
				{ title : "Image files", extensions : filter },
			],
			
			// Flash settings
			flash_swf_url : weebo.settings.SiteRoot + '/shared/plupload/js/plupload.flash.swf',
		});
		
		$('#uploader-box-wrapper').dialog({ 
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

	uploadGallery: function(filter, path, id_dir){
		$('#weebo-modal-dialog-content').html('<div id="uploader-box"></div>');
		
		var UIF = Math.round((new Date()).getTime() / 1000);
		
		$("#uploader-box").plupload({
			// General settings
			runtimes : 'html5,flash,html4',
			url : weebo.settings.AjaxCall + 'require&file=/mwms/modules/media/view/media.upload.recieve.php&uif='+UIF+'&dir=content/'+path,
			max_file_size : '2048mb',
			chunk_size : '256kb',
			unique_names : false,
			// Resize images on clientside if we can
			//resize : {width : 320, height : 240, quality : 90},

			// Specify what files to browse for
			
			filters : [
				{ title : "Image files", extensions : filter }
			],
			
			// Flash settings
			flash_swf_url : weebo.settings.SiteRoot + '/shared/plupload/js/plupload.flash.swf',
		});
		
		var buttons_def = {};
		
		var sLabel = cmsLng.ajaxUploadEnd;
		buttons_def[sLabel] = function() {
			var ref = weebo.settings.AjaxCall + "require&file=/mwms/modules/cms/view/media.browser.inner.php&id_dir=" + id_dir;
			var ri = weebo.settings.AjaxCall + 'require&file=/mwms/modules/cms/view/cms.media.dir.reindex.php&dir=content/'+path+'&id_dir='+id_dir;
			$('#uploader-box').load( ri, function(){ /*$(this).html('');*/ });
			cms.showPreloader('#mwms_content_show', 100);
			$('#mwms_content_show').load( ref );
			setTimeout("$('#weebo-modal-dialog-content').dialog('close')", 2000);
		}
		
		$('#weebo-modal-dialog-content').dialog({ 
			title: $('button.mwms_media_new').attr('title') + ' -> '+path,
			autoOpen: false,
			width: 725,
			height: 520,
			modal: true,
			buttons: buttons_def,
			close: function(){
				//cms.reindexDir(id_dir, path);
			} 
		}).dialog( 'open' );
		
		return false;
	},

	reindexDir : function(id_dir, path){
		var ref = weebo.settings.AjaxCall + "require&file=/mwms/modules/cms/view/media.browser.inner.php&id_dir=" + id_dir;
		var ri = weebo.settings.AjaxCall + 'require&file=/mwms/modules/cms/view/cms.media.dir.reindex.php&dir=content/'+path+'&id_dir='+id_dir;
		$('#uploader-box-wrapper').load( ri );
		
		var buttons_def = {};
		
		var sLabel = cmsLng.ajaxUploadEnd;
		buttons_def[sLabel] = function() {
			$('#uploader-box-wrapper').dialog('close');
		}
		
		$('#uploader-box-wrapper').dialog({ 
			title: $('button.mwms_media_new').attr('title') + ' -> '+path,
			autoOpen: false,
			width: 725,
			height: 520,
			modal: true,
			buttons: buttons_def,
			close: function(){
				cms.showPreloader('#mwms_content_show', 100);
				$('#mwms_content_show').load( ref );
			} 
		}).dialog( 'open' );
		
		return false;
	},
	
	setEditor: function(elem, id){
		var myEditor = CodeMirror.fromTextArea(document.getElementById(elem), {
			mode: {
				id : elem,
				name : 'xml',
				alignCDATA : true,
				version: 1,
				singleLineStringErrors: false
			},
			lineNumbers: true,
			matchBrackets: true,
			theme: 'eclipse',
			indentWithTabs: true,
			enterMode: "keep",
			tabMode: "shift",
			smartHome : true,
			undoDepth : 100
		});
		
		return myEditor;
	},
	
	previewItem : function(url){
		window.open(url);
	}

}
