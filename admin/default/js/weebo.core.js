var weebo = {
	
	/* set defaults */
	settings: {
		AjaxCall: '?weeboapi=',
		ActiveModule: 'mwms',
		SiteRoot: '',
		ClientTimeFormat: 'j.n.Y H:i:s',
		WeeboPreloader: "admin/default/img/loading.gif",
		AjaxTimeout: 3000,
		ShellState: 0 
	},
	
	/* external settings */
	options: {},
	
	/* init settings */
	init: function() { 
		 $.extend(this.settings, this.options);
	},
	
	keepLogged : function(){
		var bg = $("body").css("background-color");
		$("#weebo-kli").load(this.settings.AjaxCall + "status").css({ "color": bg });
		setTimeout("weebo.keepLogged()", 10000);
	},
	
	/* object test */
	test: function() { alert(this.settings.AjaxCall); },
	
	/* object test */
	testFunc: function() { this.test(); },
	
	/* Detect & remove frames */
	topFix : function(){
		if( window.location != top.location ){
			top.location.replace(window.location);
		}
	},
	
	/* Shell command line */
	shellInit : function() {
		
		var output_h = parseInt($("#weebo-shell").height()) - ( parseInt($("#weebo-shell-header").height()) + parseInt($("#weebo-shell-commandline").height()) );
		var dx = $(document).width();
		var sx = $("#weebo-shell").width();
		var dy = $(document).height();
		var sy= $("#weebo-shell").height();
		var w_output_x = parseInt( dx/2  - sx/2 );
		var w_output_y = parseInt( dy/2  - sy/2 );
		
		$("#weebo-shell").hide().css({ "left": w_output_x + "px", "bottom": w_output_y + "px" })
		$("#weebo-shell-output").html('').height(output_h);
		
		var disp = weebo.settings.ShellState;
		
		$("#weebo-shell-toggle").button({
				icons: {
					primary: "ui-icon-gear"
				},
				text: false
		}).on('click', function()
		{
			$("#weebo-shell").toggle(300, 

				function(){
					$(this).animate();
				});

				var opts = {
					___command : "entry_text",
					___params : ""
				}
				
				var entry = null; //weebo.weeboRequest( weebo.settings.AjaxCall + "shell", opts );
				
				$.ajax({
					url: weebo.settings.AjaxCall + "shell",
					type: 'post',
					data: opts,
					dataType: 'text',
					async: false,
					cache: false,
					error: function(){},
					success: function(data) {
						entry = data;
						$("#weebo-shell-output").html('').height(output_h).append( '<div class="cmd-output">' + entry + '</div>' );
						$("#weebo-shell-commandline-input").focus();
					}
				});
		});

		$("#weebo-shell-close").button({
			icons: {
				primary: "ui-icon-close"
			},
			text: false
		}).on('click', function(){
			$("#weebo-shell").hide(300, 
				function(){
					$(this).animate();
				});
		});

		$("#weebo-shell-commandline-input").on("keypress", function(e) {
			
			if(e.keyCode==13)
			{
				e.preventDefault();
				
				// si entruj
				var command = $(this).val();
				
				if(command.length >= 1)
				{
					if(command == 'clr' || command == 'clear'){
						$("#weebo-shell-output").html('');
						$("#weebo-shell-commandline-input").val("").focus();
					}else{
						var user = weebo.settings.systemUser;
						var title =  weebo.settings.shellReuse;
						var machine = weebo.settings.systemMachine;
						
						// shell command
						var command_source = $("#weebo-shell-commandline-input").val().trim().split(" ");
						$("#weebo-shell-commandline-input").val("");

						var commandStd = command_source[0];
						param_list = command_source.reverse();
						param_list.pop();
						var param_list = param_list.reverse().toString();

						var opts = {
							___command : commandStd,
							___params : param_list
						}

						$.ajax({
							url: weebo.settings.AjaxCall + "shell",
							type: 'post',
							data: opts,
							dataType: 'text',
							async: false,
							cache: false,
							error: function(){},
							success: function(data) {
								$("#weebo-shell-output")
								.append( '<div class="cmd-input">' + machine + "@" + user + ' &rarr; <span title="' + title + '">' + command + '</span></div><div class="cmd-output">' + data + '</div>' )
								.animate({ scrollTop: $("#weebo-shell-output").prop("scrollHeight") - $("#weebo-shell-output").height() }, 150);
							}
						});
					}
				}
			}
		});
		
		$("#weebo-shell").delegate("div.cmd-input span, td.cmd-input-history, code", "click", function(){
			$("#weebo-shell-commandline-input").val( $(this).text().trim() ).focus();
		});
	},

	/* save modules on user workspace */
	saveDashboardConfig: function(position){
		
		var token =  Math.round(Math.random()*10000000000);
		var indicator = $('#mwms_drop_indicator');
		var newurl = this.settings.AjaxCall + "static-method&fn=Login::save_dashboard_config&qs=" + position + '&ucache=' + token;

		$(indicator).html('');
		$(indicator).load(newurl);

		return false;
	},

	/* save modules on user workspace -> wrapper */
	saveDashboardConfigFromMain: function(position){

		var token = Math.round(Math.random()*10000000000);

		this.saveDashboardConfig(position);
		if(weebo.settings.ActiveModule == 'mwms'){
			$('#mwms_inner').html('').load( this.settings.AjaxCall + "method&fn=Gui::load_workspace&ucache=" + token );
		}
		return false;
	},
	
	/* save user lang set */
	saveSystemLng: function(lng){
		var token = Math.round(Math.random()*10000000000);
		var indicator = $('#mwms_drop_indicator');
		newurl = this.settings.AjaxCall + "method&fn=Gui::save_system_lng&qs=" + lng + '&ucache=' + token;
		$(indicator).load(newurl);
		document.location.href = window.location;
	},

	/* Activity indicator */
	showPreloader : function(elem, marginTop){
		$(elem).html('<div class="dialog_loader"></div>');
		$('div.dialog_loader').css({'margin-top': parseInt(marginTop) + 'px' });
	},
	
	setMessage: function(e){
		//clearTimeout();
		var indicator = $('#mwms_drop_indicator');
		msg = e;
		str = '<div class="mwms_dashboard_info ui-widget"><div class="ui-state-highlight ui-corner-all"><span class="ui-icon ui-icon-alert"></span>'+msg+'</div></div>';
		$(indicator).html( str );
		window.setTimeout("$('#mwms_drop_indicator').html('')", 5000);
	},
	
	clearMessage : function(){
		$('#mwms_drop_indicator').html('');
	},
	
	handleServerError: function(e){
		var indicator = $('#mwms_drop_indicator');
		msg = 'Network rror';
		str = '<div class="mwms_dashboard_warning ui-widget"><div class="ui-state-error ui-corner-all"><span class="ui-icon ui-icon-alert"></span>'+msg+'</div></div>';
		$(indicator).html( str );
	},
	
	/* Native dialog */
	systemDialog: function(frm, url, xtitle, w, h){
		
		/* replacement of __show_weebo_dialog */
		var button_title = weebo.settings.systemSaveButton;
		var newurl = this.settings.AjaxCall + url + '&action=save' + '&ucache=';
		var buttons_def = {};

		buttons_def[button_title] = function(){

		var mydata = $(frm).serialize();

		$(frm).submit(function() {
			var token =  Math.round(Math.random()*10000000000);
			$('#weebo-modal-dialog-content').html('');

			$.post(
			  newurl + token,
			  //data: dataString,
			  mydata ,
			  function(data){
						$('#weebo-modal-dialog-content').html(data);
			  }
			);
			return false;
		});

		$(frm).submit();

			 //$(this).dialog('close');
		}

		this.dialog('weebo-modal-dialog-content', url, xtitle, buttons_def, w, h);
	},

	dialog: function(winid ,url, xtitle, buttons_def, w, h){

		/* replacement of __show_dialog */
		$.fx.speeds._default = 300;

		var ucache = '&ucache=' + Math.round(Math.random()*10000000000);

		// load remote content
		$("#" + winid).remove();

		$("#mwms_main").after('<div id="' + winid + '"></div>');

		$("#"+winid).load(
				this.settings.AjaxCall + url + ucache,
				{}

		).dialog({
			title: xtitle,
			width: w,
			height: h,
			modal: true,
			show: 'slide',
			hide: 'blind',
			buttons: buttons_def
		});
		//prevent the browser to follow the link
		return false;
	}
}
