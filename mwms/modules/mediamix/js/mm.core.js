var MediaMix = {
	
	/* Create static TABs */
	initUI : function(){
	$("div.mm_menu a:first").button({
			icons: {
				primary: "ui-icon-folder-collapsed"
			}
		}).next().button({
			icons: {
				primary: "ui-icon-image"
			}
		}).next().button({
			icons: {
				primary: "ui-icon-arrowthickstop-1-s"
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

	editMeta : function(edURI, title){
		var u = edURI;
		weeboMeta.showActionDialog(u, title, 800 , 600);
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
