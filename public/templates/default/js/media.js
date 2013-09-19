var weeboMedia = {
	
	/* set defaults */
	conf : {
		elementID : 'video',
		file : '',
		width : 768,
		height : 432,
		autostart : false,
		repeat : true,
		image : '',
		player : "/shared/smp.osmf/StrobeMediaPlayback.swf",
		live : false
	},
	
	/* external settings */
	options: {},
	
	/* init settings */
	init: function() { 
		$.extend(this.conf, this.options);
	},
	
	create : function(){
		
		var newW = $('#main .span12:first').width();
		var w = weeboMedia.conf.width;
		var h = weeboMedia.conf.height;
		var a = w/h;
		weeboMedia.conf.width = newW;
		weeboMedia.conf.height = (newW/a);
		
		
		var token = Math.ceil(30000*Math.random());	
		
		if(weeboMedia.conf.live === true)
		{
			var options={
				swf : weeboPublic.siteUrl+weeboMedia.conf.player,
				src: weeboMedia.conf.file,
				width: weeboMedia.conf.width,
				height: weeboMedia.conf.height,
				streamType: 'live', 
				autoPlay: weeboMedia.conf.autostart, 
				scaleMode: 'stretch', 
				controlBarMode: 'floating', 
				controlBarAutoHideTimeout: .7,
				volume : .7
			};
			
			// Now we are ready to generate the video tags
			$(weeboMedia.conf.elementID).strobemediaplayback(options);
		}else{
			
			
			var options={
				swf : weeboPublic.siteUrl+weeboMedia.conf.player,
				src: weeboMedia.conf.file,
				width: weeboMedia.conf.width,
				height: weeboMedia.conf.height,
				streamType: 'recorded', 
				autoPlay: weeboMedia.conf.autostart, 
				scaleMode: 'stretch', 
				poster: weeboMedia.conf.image,
				controlBarMode: 'floating', 
				controlBarAutoHideTimeout: .7,
				volume : .7
			};
			
			// Now we are ready to generate the video tags
			var player = $('#'+weeboMedia.conf.elementID).strobemediaplayback(options);
		}
	},
	
	resize : function(c, newW){
		
		c.each( function(){
			if(newW <= 1000)
			{
				var e = $(this);
				var w = e.prop("tagName") == 'video' || e.prop("tagName") == 'object' || e.prop("tagName") == 'embed' || e.prop("tagName") == 'audio' ? parseInt(e.attr('width')): parseInt(e.width());
				var h = e.prop("tagName") == 'video' || e.prop("tagName") == 'object' || e.prop("tagName") == 'embed' ? parseInt(e.attr('height')): parseInt(e.height());
				
				var a = w/h;
				newH = e.prop("tagName") == 'audio' ? parseInt(e.attr('height')): parseInt(newW/a);
				
				if( e.prop("tagName") == 'video' || e.prop("tagName") == 'object' || e.prop("tagName") == 'embed' || e.prop("tagName") == 'audio'){
					e.attr('width', newW);
					e.attr('height', newH);
				}else{
					e.width(newW);
					e.height(newH);
				}
				
				//console.log('Element: ' +  e.prop("tagName") + ' -> New dimensions: ' + newW+'x'+newH);
			}
		});
	},
	
	autoLoader : function(){
	if($('video').length > 0)
	{
		$('video').each( function(index)
		{
			var videoParent = $(this).parent();
			var videoSource = $(this).attr('src');
			var videoWidth = $(this).attr('width');
			var videoHeight = $(this).attr('height');
			var videoPoster = $(this).attr('poster');
			var idVideoContainer = 'video-element-' + index;
			
			aspectR = videoHeight / videoWidth;
			
			// resample to screen
			newWidth = 700;
			newHeight = parseInt(aspectR * 700);
			
			if(videoSource)
			{
				if(videoSource.match("^http")!='http')
				{
					videoSource = videoSource.match("^/")=='/' ? weeboPublic.siteUrl+videoSource: weeboPublic.siteUrl+'/'+videoSource;
				}
				
				if(videoPoster && videoPoster.match("^http")!='http')
				{
					videoPoster = videoPoster.match("^/")=='/' ? weeboPublic.siteUrl+videoPoster: weeboPublic.siteUrl+'/'+videoPoster;
				}
				
				var newContainer = $('<div id="'+idVideoContainer+'"></div>');
				newContainer.insertAfter(videoParent);
				videoParent.remove();
				
				var opts = {
					elementID : idVideoContainer,
					file : videoSource,
					width : newWidth,
					height : newHeight,
					autostart : false,
					repeat : false
				}
				if(videoPoster)
				{
					opts.image = videoPoster;
				}
				
				weeboMedia.options = opts;
				
				weeboMedia.init();
				
				weeboMedia.create();
			}
		});
	}
}
	
}
