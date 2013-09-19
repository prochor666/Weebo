var nettvLng = {
	resetButton : 'reset'
}

var NettvAdmin = {
	
	/* Create static TABs */
	initUI : function(){
		$("div.nettv_menu a:first").button({
			icons: {
				primary: "ui-icon-info"
			}
		}).next().button({
			icons: {
				primary: "ui-icon-video"
			}
		}).next().button({
			icons: {
				primary: "ui-icon-folder-collapsed"
			}
		}).next().button({
			icons: {
				primary: "ui-icon-script"
			}
		}).next().button({
			icons: {
				primary: "ui-icon-person"
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
	
	videoPlay : function(){
		
		jwplayer("vpr").setup({
			"skin": weebo.settings.SiteRoot + "/shared/jwplayer/skins/lulu.zip",
			"stretching": "exactfit", //uniform,fill,exactfit,bestfit,none
			"flashplayer": weebo.settings.SiteRoot + "/shared/jwplayer/player.swf",
			"autostart": false,
			"provider": "http"
		});
	},
	
	getEncoderStatus : function(){
		
		var uri = weebo.settings.AjaxCall + 'require&file=/mwms/modules/nettv/view/encoder.status.php';
		
		$.ajax({
			url: uri,
			type: 'get',
			dataType: 'json',
			async: true,
			cache: false,
			success: function(response) {
				$('#file').html(response.file);
				
				percent = parseInt(response.progress);
				
				if(percent < 100){
					$('#enc-activity-title').show();
					$('#enc-no-activity-title').hide();
				}else{
					$('#enc-no-activity-title').show();
					$('#enc-activity-title').hide();
				}
				
				$('.ui-progressbar-value').html('<span id="status-int">'+percent+'%</span>');
				$('#id_import').html(response.id_import);
				$('#enc-timecode-duration').html(response.duration);
				$('#enc-timecode-current').html(response.current);
				
				$('#status').progressbar({
					value: percent
				});	
				
				setTimeout('NettvAdmin.getEncoderStatus()', 500);
			},
			error: function(x, t, m) {
				$('#status').progressbar({
					value: 100
				});
				setTimeout('NettvAdmin.getEncoderStatus()', 2000);
			}
		});
	},
	
	recheckImportItem : function(uri, id_import){
		
		$.ajax({
			url: uri,
			type: 'get',
			dataType: 'text',
			async: true,
			cache: false,
			success: function(response) {
				$('#status-'+id_import).html(response);
			},
			error: function(x, t, m) {
				$('#status-'+id_import).html('Request error');
			}
		});
		
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
	
	newFile : function(dURI, xtitle){
		
		var ucache = '&ucache=' + Math.round(Math.random()*10000000000);
		
		var buttons_def = {};
		
		var saveButton = nettvLng.resetButton;
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
	
	attachFile : function(elem, file){
		$(elem).val(file);
		return false;
	},
	
	uploadFiles: function(filter, UIF){
		$('#uploader-box-wrapper').html('<div id="uploader-box"></div>');
		
		var UIF = Math.round((new Date()).getTime() / 1000);
		
		$("#uploader-box").plupload({
			// General settings
			runtimes : 'flash, html5',
			url : weebo.settings.AjaxCall + 'require&file=/mwms/modules/media/view/media.upload.recieve.php&dir='+media.readDir()+'&sysadmin='+media.UID+'&uif='+UIF,
			max_file_size : '8192mb',
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

	removeItem : function(delURI, title){
		var u = delURI + '&action=del';
		weeboMeta.showActionDialog(u, title, 500 , 200);
	},

	setEditor: function(elem, id){
		
		var myWrap = $('<div/>').addClass('editorCell editorCell_'+id);
		
		var myEditor = CodeMirror(myWrap.get(0), {
				lineNumbers: true,
				matchBrackets: true,
				mode: "application/xml",
				indentUnit: 4,
				indentWithTabs: true,
				enterMode: "keep",
				tabMode: "shift",
				smartHome : true,
				undoDepth : 100
			});
			
		$(elem+' textarea[name="meta_value_config"]').parent('div').prepend(myWrap);
		myEditor.setValue($(elem+' textarea[name="meta_value_config"]').val());
		$(elem+' textarea[name="meta_value_config"]').hide();
		return myEditor;
	}

}
