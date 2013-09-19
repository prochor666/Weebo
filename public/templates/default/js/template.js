$(document).ready(function(){
/* DOC READY */
$('.jform').weeboJForm();

$('button').addClass('btn');

$('.dropdown-toggle').append(' <span class="caret"></span>');

weeboMedia.resize( $('#main iframe, #main video, #main object'), $('#main .span12:first').width() - 10 );

$('video').attr('controls', 'cotrols');

/* Video player */
if(weeboPublic.deviceType == 'computer'){
	weeboMedia.autoLoader();
}

$(window).on('resize', function(){
	weeboMedia.resize( $('#main iframe, #main video, #main object'), $('#main .span12:first').width() - 10 );
});

if(typeof ie !== 'undefined'){
	$('html').addClass(ie);
}

$('object, iframe').attr('wmode', 'opaque');

if( typeof ie == false || ie > 7 )
{
	$("a[href$='.jpg'], a[href$='.png'], a[href$='.gif'], a[href$='.JPG'], a[href$='.PNG'], a[href$='.GIF']").fancybox({
		openEffect : 'elastic',
		closeEffect : 'fadeout',
		beforeLoad : function(){
			$('object, iframe').hide();
		},
		afterClose : function(){
			$('object, iframe').show();
		},
		helpers : {
			title : { type : 'inside' },
			buttons : {}
		}
	});

	$("a[href$='.embed']").each( function(index)
	{
	 
		var newContainer = $('<div id="embed-content-'+index+'" style="display: none;"></div>')
		newContainer.insertAfter($(this));
		newContainer.load($(this).attr('href'));
		$(this).attr('href', '#embed-content-'+index);
		$(this).attr('id', 'embed-content-source-'+index);
	
		$(this).fancybox({
			autoSize	: true,
			beforeLoad : function(){
				$('object, iframe').hide();
			},
			afterClose : function(){
				$('object, iframe').show();
			},
			helpers : {
				title : { type : 'inside' },
				buttons : {}
			}
		});
	});
	
}else{
	
	$("a:has(img)").fancybox({
		openEffect	: 'elastic',
		closeEffect	: 'fadeout',
		beforeLoad : function(){
			$('object, iframe').hide();
		},
		afterClose : function(){
			$('object, iframe').show();
		}
	});
}

$(".weebo_gallery_item_link, .mediamix-thumb").fancybox({
	openEffect	: 'fadein',
	closeEffect	: 'fadeout',
	closeBtn: false,
	arrows: false,
	scrollOutside : false,
	loop: false,
	beforeLoad : function(){
		$('object, iframe').hide();
	},
	afterClose : function(){
		$('object, iframe').show();
	},
	
	helpers : {
		title : { type : 'over' },
		buttons : {},
		overlay : { showEarly : true }
	}
});

/* DOC READY */
});

