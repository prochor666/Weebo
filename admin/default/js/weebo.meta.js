weeboMeta = {
	
	/* UI dialog */
	showDialog : function(id, frm, contentUri, saveUri, xtitle, w ,h){
		var mydata = $(frm).serialize();
		
		var ucache = '&ucache=' + Math.round(Math.random()*10000000000);
		
		var buttons_def = {};
		
		var saveButton = weebo.settings.systemSaveButton;
		
		buttons_def[saveButton] = function() {
			weeboMeta.applyCallback(id, saveUri);
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

	/* UI dialog */
	showActionDialog : function(contentUri, xtitle, w ,h){
		
		var ucache = '&ucache=' + Math.round(Math.random()*10000000000);
		
		$('#weebo-modal-dialog-content').load(weebo.settings.AjaxCall + contentUri + ucache).dialog({
			autoOpen: false,
			height: h,
			width: w,
			title: xtitle,
			modal: true,
			show: 'slide',
			hide: 'blind',
			close: function() {
				$('#weebo-modal-dialog-content').html('');
			}
		});
		
		$('#weebo-modal-dialog-content').dialog('open');
	},

	
	/* Dialog callback */
	applyCallback : function(id, url){
		myData = $('#form_call_'+id).serializeArray();
		var rQ = weebo.settings.AjaxCall + url;

		var res = '#result_'+id;

		$.ajax({
			url: rQ,
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
				$(res).html(response);
			}
		});

		return false;
	},
	
	/* apply xml conversion */
	htmlToXml : function(){
		
		var html = $("ul.predefined_widget_view").find("li");
		var xml = '';
		
		html.each(function()
		{
			if($(this).length>0){
				xml += '<item><value>'+$(this).text()+'</value></item>';
			}
		});
		
		$("#edit_field_default_value").val('<predefined_data>'+xml+'</predefined_data>');
	},
	
	/* apply xml conversion */
	xmlToHtml : function(){
		
		var mxml = weeboMeta.parseXml($("#edit_field_default_value").val(), 'item value');
		var html = '';
		
		mxml.each(function()
		{
			if($(this).length>0){
				html += '<li>'+$(this).text()+'</li>';
			}
		});
		
		$("ul.predefined_widget_view").append(html).sortable({
			placeholder: "ui-state-highlight",
			update: function(event, ui) { 
				weeboMeta.htmlToXml();
			}
		}).disableSelection().find("li").addClass("ui-state-default")
		.prepend('<span class="ui-icon ui-icon-arrowthick-2-n-s"></span>')
		.append('<span class="ui-icon ui-icon-close item-remove"></span>');
		weeboMeta.widgetItemRemove(); 
	},
	
	/* Convert values */
	convertPredefinedItems: function(action){
		var item = $(".multiple_widget_add").val();

		if(action == "delete"){
			alert("NOT IMPLEMENTED YET");
		}else{
			if(item.length>0){
				
				$("ul.predefined_widget_view").append('<li>'+item+'</li>').sortable({
					placeholder: "ui-state-highlight",
					update: function(event, ui) { 
						weeboMeta.htmlToXml();
					}
				}).disableSelection().find("li:last").addClass("ui-state-default")
				.prepend('<span class="ui-icon ui-icon-arrowthick-2-n-s"></span>')
				.append('<span class="ui-icon ui-icon-close item-remove"></span>');
				
			}else{
				alert( weebo.settings.systemFieldValidationError );
			}
		}
		
		weeboMeta.htmlToXml();
		weeboMeta.widgetItemRemove();
	},
	
	addMetaWidget: function(buttonTitle){
		var widget = '<div class="multiple_widget"><input type="text" class="text multiple_widget_add" name="multiple_widget_add" /><button class="mwms_meta_new button" title="'+buttonTitle+'">'+buttonTitle+'</button>';
		
		var display = '<ul class="predefined_widget_view"></ul>';
		$("ul.predefined_widget_view").remove();
		$("#edit_field_default_value").hide();
		
		$("td.default_cell").append( display ).prepend( widget );
		
		$("button.mwms_meta_new").button({
			icons: {
				primary: "ui-icon-circle-plus"
			}
		}).click(function()
		{
			weeboMeta.convertPredefinedItems("update");
			return false;
		});
		
		weeboMeta.xmlToHtml();
	},
	
	widgetItemRemove: function(){
		$("span.item-remove").click(function()
		{
			$(this).parent("li").remove();
			weeboMeta.htmlToXml();
		});
	},
	
	removeMetaWidget: function(){
		$("ul.predefined_widget_view").remove();
		$(".multiple_widget").remove();
	},

	parseXml : function(data, field){
		rT = $(data).find(field);
		return rT;
	}
	
	
}
