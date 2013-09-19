var inquaLng = {
	resetButton: 'reset'
}

var InquaAdmin = {
	
	/* Create static TABs */
	initUI : function(){
		$("div.inqua_menu a:first").button({
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

	statImage : function(image, xtitle){
		
		var buttons_def = {};
		
		var saveButton = inquaLng.resetButton;
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
	
	removeItem : function(delURI, title){
		var u = delURI + '&action=del';
		weeboMeta.showActionDialog(u, title, 500 , 200);
	}
}
