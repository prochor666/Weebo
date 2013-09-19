var usersLng = {
	buttonSave : 'save'
}

var users = {
	
	/* Create static TABs */
	initUI : function(){
		$("div.users_user_menu a:first").button({
			icons: {
				primary: "ui-icon-person"
			}
		}).next().button({
			icons: {
				primary: "ui-icon-plus"
			}
		}).next().button({
			icons: {
				primary: "ui-icon-flag"
				
			}
		});
		
	},
	
	initUIUser : function(){
		$("div.users_user_menu a:first").button({
			icons: {
				primary: "ui-icon-person"
			}
		}).next().button({
			icons: {
				primary: "ui-icon-plus"
			},
			disabled: true
		}).attr('href', '').click(
			function(e){
				e.preventDefault();
				return false;
			}
		).next().button({
			icons: {
				primary: "ui-icon-flag"
			},
			disabled: true
		}).attr('href', '').click(
			function(e){
				e.preventDefault();
				return false;
			}
		);
		
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
	
	toggleWidget: function( conf ){
		
		var widgetTitle = '<div class="widget-title">'+conf.title+'</div>';
		var widget = $('<div class="mwms-data-widget"></div>');
		$('body').append(widget);
		widget.hide();
		
		widget.html(widgetTitle).css({ "position": "absolute", "left": parseInt(conf.x-5)+"px", "top": parseInt(conf.y-5)+"px" }).show(150);
		
		var l = conf.buttons.length;
		
		for(i=0;i<l;i++)
		{
			var xbutton = conf.buttons[i];
			var xname = conf.buttons[i]['name']; 
			widget.append('<a href="#" class="'+xname+'">'+xbutton.title+'</a>');
			var item = widget.find("a."+xname);
			item.click( xbutton.xcall );
			widget.disableSelection();
			
		}
	},
	
	toggleToolbar: function( conf ){
		 
		var toolbar = $('<div class="mwms-data-toolbar"></div>');
		$(conf.element).append(toolbar);
		
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
	
	processItems : function(frm, contentURI, title){
		
		$('.dropbox-data').serialize();
		
		var u = contentURI;
		users.showDropBoxDialog(frm, contentURI + '&action=del', title, 600 , 400);
	},
	
	/* UI dialog */
	showDropBoxDialog : function(frm, contentUri, xtitle, w ,h){
		
		var ucache = '&ucache=' + Math.round(Math.random()*10000000000);
		var buttons_def = {};
		var saveButton = usersLng.buttonSave;
		var saveUri = weebo.settings.AjaxCall + 'require&file=/mwms/modules/users/view/user.action.php&action=';
		
		buttons_def[saveButton] = function() {
			
			var saveAction = $('input[name$="user-action"]:checked').val();
			$('#action-set').remove();
			var myData = $(frm).serialize();
			users.showPreloader('#user-action-result', 150);
			
			$.ajax({
				url: saveUri+saveAction,
				type: 'post',
				data: myData,
				dataType: 'text',
				async: false,
				cache: false,
				error: function(jqXHR, textStatus, errorThrown){
					$(res).html('ERROR: '+errorThrown);
				},
				success: function(response) {
					
					// reload content?
					$('#user-action-result').html(response);
				}
			});
		}
		
		$('#weebo-modal-dialog-content').load(weebo.settings.AjaxCall + contentUri + ucache).dialog({
			autoOpen: false,
			height: h,
			width: w,
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


}
