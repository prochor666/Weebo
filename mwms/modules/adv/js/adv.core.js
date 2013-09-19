var advLng = {
	resetButton : 'Reset'
}

var AdvAdmin = {
	
	/* Create static TABs */
	initUI : function(){
		$("div.adv_menu a:first").button({
			icons: {
				primary: "ui-icon-calculator"
			}
		}).next().button({
			icons: {
				primary: "ui-icon-flag"
			}
		}).next().button({
			icons: {
				primary: "ui-icon-folder-collapsed"
			}
		}).next().button({
			icons: {
				primary: "ui-icon-arrow-4-diag"
			}
		}).next().button({
			icons: {
				primary: "ui-icon-image"
			},
			disabled: true
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
		
		var saveButton = advLng.resetButton;
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
	
	statImage : function(image, xtitle){
		
		var buttons_def = {};
		
		var saveButton = advLng.resetButton;
		buttons_def[saveButton] = function() {
			$('#weebo-modal-dialog-content').dialog('close');
		}
		
		$('#weebo-modal-dialog-content').append('<img src="'+image+'" alt="~" />').dialog({
			autoOpen: false,
			height: 500,
			width: '95%',
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
	
	showBannerMedia : function(e, id_banner){
		
		var uri = weebo.settings.AjaxCall + 'require&file=/mwms/modules/adv/view/adv.show.banner.media.php&id_banner='+id_banner;
		
		$.ajax({
			url: uri,
			type: 'get',
			dataType: 'json',
			async: true,
			cache: false,
			success: function(response) {
				var fUrl = weebo.settings.SiteRoot + '/' + response.file;
				var ext = response.extension;
				var advUrl = response.url;
				var format = response.format.split('x');
				var x = format[0];
				var y = format[1];
				var parentID = $(e).attr('id');
				var subID = parentID + '_inner';
				
				//$(e).css({ 'width' : x+'px', 'height' : y+'px' });
				weebo.setMessage('advURL: '+advUrl);
				
				$(e).html('<div class="banner_prewiev" id="'+subID+'"></div>');
				
				if(ext == 'swf'){
					
					var flashvars = {};
					
					flashvars.clickTag = advUrl;
					flashvars.ClickTag = advUrl;
					flashvars.clicktag = advUrl;
					flashvars.Clicktag = advUrl;
					
					flashvars.clickThru = advUrl;
					flashvars.ClickThru = advUrl;
					flashvars.clickthru = advUrl;
					flashvars.Clickthru = advUrl;
					
					var params = {};
					var attributes = {};
					
					swfobject.embedSWF(fUrl, subID, x, y, "9.0.0", false, flashvars, params, attributes);
				}else{
					var iid = subID + '_banner';
					$('#'+subID).html('<img src="'+fUrl+'" id="'+iid+'" alt="prewiev" style="width:'+x+';height:'+y+';" />');
					$('#'+iid).on('click', function(){ window.open(advUrl); });
				}
			},
			error: function(x, t, m) {
				$(e).html('File error');
			}
		});
		
	},
	
	attachFile : function(elem, file){
		$(elem).val(file);
		return false;
	},
	
	uploadFiles: function(filter){
		$('#uploader-box-wrapper').html('<div id="uploader-box"></div>');
		
		var UIF = Math.round((new Date()).getTime() / 1000);
		
		$("#uploader-box").plupload({
			// General settings
			runtimes : 'flash, html5',
			url : weebo.settings.AjaxCall + 'require&file=/mwms/modules/media/view/media.upload.recieve.php&dir='+media.readDir()+'&sysadmin='+media.UID+'&uif='+UIF,
			max_file_size : '128mb',
			chunk_size : '100kb',
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
