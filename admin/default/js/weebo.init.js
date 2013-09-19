$(document).ready(function(){
	
	weebo.topFix();
	
	$('button#mwms_login_bt').button({
			icons: {
				primary: "ui-icon-key"
			}
	});

	$('button#mwms_logout_bt').button({
			icons: {
				primary: "ui-icon-power"
			}
	});

	/* weebo shell */
	weebo.shellInit();
	
	setTimeout("weebo.keepLogged()", 10000);
	
	$("#mwms_header #mwms_logo").addClass("ui-state-hover").append('<span class="ui-icon ui-icon-triangle-1-s"></span>');
	
	/* dashboard configurator */
	/*if(weebo.settings.ActiveModule == 'mwms'){*/
		var dashw = $("div.mwms_logged_box").width();
		var dashh = $("div.mwms_logged_box").height();
		$("div#module_widget").css({'width': (dashw - 11) + 'px', 'top': dashh+ 'px' }).hide();
		
		$('#mwms_header #mwms_logo').css({ 'cursor': 'pointer' }).on('click', 
			function(){
				$("div#module_widget").toggle(300, function()
				{
					$(this).animate();
				});
				
				if($("#mwms_logo span.ui-icon-triangle-1-s").length == 1){
					$("#mwms_logo span.ui-icon-triangle-1-s").remove();
					$("#mwms_header #mwms_logo").append('<span class="ui-icon ui-icon-triangle-1-n"></span>');
				}else{
					$("#mwms_logo span.ui-icon-triangle-1-n").remove();
					$("#mwms_header #mwms_logo").append('<span class="ui-icon ui-icon-triangle-1-s"></span>');
				}
			}
		);
	/*}*/
	
	$("#mwms_inner_load").removeClass('js-false').addClass('js-true').hide();
	$("#mwms_inner").show();
	
	// Stop chrome's autocomplete from making your input fields that nasty yellow. Yuck.
	// http://www.benjaminmiles.com/2010/11/22/fixing-google-chromes-yellow-autocomplete-styles-with-jquery/
	if (navigator.userAgent.toLowerCase().indexOf("chrome") >= 0) {
		$(window).on('load', function(){
			$('input:-webkit-autofill').each(function(){
				var text = $(this).val();
				var name = $(this).attr('name');
				$(this).after(this.outerHTML).remove();
				$('input[name=' + name + ']').val(text);
			});
		});
	}
});

