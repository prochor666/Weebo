var dropBox = {
	
	setup : {
		dataContainer : 'mwms-data-dropbox',
		dataTitle : 'dropbox-title',
		dataForm : 'dropbox-data',
		doButton : 'dropbox-do-button',
		resetButton : 'dropbox-reset-button',
		initURI : 'require&file=/mwms/modules/users/view/drop.box.php',
		
		element : '#browser_filter',
		title : 'title',
		buttonDoTitle : 'Do',
		buttonResetTitle : 'Reset',
		reg : "none",
		docall : function(){
			alert('Default');
		},
		resetcall : function(){
			this.reset();
		}, 
		
		testVar : function(){ alert('test'); }
	},
	
	settings : {},
	
	/* init settings */
	init: function() { 
		$.extend(this.setup, this.settings);
	},
	
	create : function(){
		var dropboxhtml = $('<div class="'+this.setup.dataContainer+'"><div class="'+this.setup.dataTitle+'">'+this.setup.title+' <span>0</span></div><form action="" method="post" class="'+this.setup.dataForm+'"></form> <button class="'+this.setup.doButton+'">'+this.setup.buttonDoTitle+'</button> <button class="'+this.setup.resetButton+'">'+this.setup.buttonResetTitle+'</button></div>');
		$(this.setup.element).append(dropboxhtml);
		$("."+this.setup.doButton).click( this.setup.docall ).button();
		$("."+this.setup.resetButton).click( this.setup.resetcall ).button();
		this.run();
		this.update();
	},
	
	run : function(param){
		$("."+this.setup.dataContainer+" ."+this.setup.dataForm).load( weebo.settings.AjaxCall + this.setup.initURI + '&reg=' + this.setup.reg + '&' + param );
	},
	
	add : function(dropBoxId){
		this.run(this.setup.reg+'_dropbox='+dropBoxId);
		$("."+this.setup.doButton).button({ disabled: false });
		var test = parseInt($("."+this.setup.dataContainer+" ."+this.setup.dataTitle+" span").text());
		$("."+this.setup.dataContainer+" ."+this.setup.dataTitle+" span").html(test + 1);
	},
	
	remove : function(dropBoxId){
		this.run(this.setup.reg+'_dropbox='+dropBoxId+'&regaction=remove');
		var test = parseInt($("."+this.setup.dataContainer+" ."+this.setup.dataTitle+" span").text());
		$("."+this.setup.dataContainer+" ."+this.setup.dataTitle+" span").html(test - 1);
		if(parseInt(test) == 1){
			$("."+this.setup.doButton).button({ disabled: true });
		}
	},
	
	update : function(){
		var result = '';
		var that = this;
		$.ajax({
			url: weebo.settings.AjaxCall + that.setup.initURI + '&reg=' + that.setup.reg + '&count',
			type: 'get',
			dataType: 'text',
			async: false,
			cache: false,
			error: function(){},
			success: function(data) {
				result = data;
				
				$("."+that.setup.dataContainer+" ."+that.setup.dataTitle+" span").html( result );
				if(parseInt(result) == 0){
					$("."+that.setup.doButton).button({ disabled: true });
				}else{
					$("."+that.setup.doButton).button({ disabled: false });
				}
			}
		});
	},
	
	reset : function(){
		this.run( 'regaction=reset' );
		$("."+this.setup.dataContainer+" ."+this.setup.dataTitle+" span").html( 0 );
		$("."+this.setup.doButton).button({ disabled: true });
	},
	
	assignItem : function(elem){
		$("."+this.setup.dataContainer).effect("pulsate", { times: 2 }, 50);
		$(elem).addClass("highlight").effect("transfer", { to: $("."+this.setup.dataContainer+" ."+this.setup.dataTitle+" span") }, 300);
	},
	
	unassignAll : function(elem){
		$("."+this.setup.dataContainer).effect("pulsate", { times: 2 }, 50);
		$(elem).removeClass("highlight");
	},
	
	unassignItem : function(elem){
		$("."+this.setup.dataContainer).effect("pulsate", { times: 2 }, 50);
		$(elem).removeClass("highlight");
		$("."+this.setup.dataContainer+" ."+this.setup.dataTitle+" span").effect("transfer", { to: $(elem) }, 300);
	}
	
}
