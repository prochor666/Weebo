/**
 * weebo horizontal menu plugin for jQuery
 * v1.0
 * Converts child ul li structure into rollover menu.
 *
 * By Jan Prochazka aka prochor666, prochor666@gmail.com
 *
 * As featured on multizone.cz
 * Please use as you wish at your own risk.
 */

(function($) {  
	$.fn.weeboMenu = function(options) { 
		
		var options = $.extend( { opened: '.menu-item-actual' }, options);  
		
		//$('.full_naviset_actual') parent as this
		
		// Create structure
		var menuWrapper = this;
		
		menuWrapper.addClass('weebo-menu-wrapper').hide();
		
		var mainMenuItems = $('.weebo-menu-wrapper>ul>li');
		var subMenuItems = $('.weebo-menu-wrapper>ul>li>ul>li');
		var menuWidth = parseInt(menuWrapper.outerWidth(true));
		
		mainMenuItems.addClass('level-1');
		subMenuItems.addClass('level-2');
		
		$(options.opened).each( function()
		{
			if( $(this).hasClass('level-1') )
			{
				var submenu = $(this).find('ul');
				
				if(submenu.length == 1)
				{
					var posLeft = 0;
					submenu.find('li a').each( function()
					{
						posLeft = posLeft + parseInt($(this).outerWidth(true));
					});
					
					$(this).addClass('active-menu');
					submenu.css({
						'width': posLeft*10+'px',
						'display': 'block'
					});
				}
			
			}else if( $(this).hasClass('level-2') ){
				
				var submenu = $(this).parent('ul');
				
				if(submenu.length == 1)
				{
					var posLeft = 0;
					submenu.find('li a').each( function()
					{
						posLeft = posLeft + parseInt($(this).outerWidth(true));
					});
					$(this).parent('ul').parent('li').addClass('active-menu');
					submenu.css({
						'width': posLeft*10+'px',
						'display': 'block'
					});
				}
			
			}
			
		});

		$('.level-1').each( function()
		{
			var hd = $(this).hasClass('active-menu');
			if( hd == false )
			{
			
				$(this).mouseover( function()
				{
					var submenu = $(this).find('ul');
					var posLeft = $(this).find('ul').outerWidth(true);
					
					if(submenu.length == 1)
					{
						$('.active-menu ul').hide();
						var posLeft = 0;
						submenu.find('li a').each( function()
						{
							posLeft = posLeft + parseInt($(this).outerWidth(true));
						});
						
						//log('Show submenu, active menu set hidden state<br />');
						submenu.css({
							'width': posLeft*10+'px',
							'display': 'block'
						});
					}
					
				}).mouseout( function()
				{
					var submenu = $(this).find('ul');
					$('.active-menu ul').show();
					//log('Hide submenu, active menu turn back from hidden state<br />');
					if(submenu.length == 1)
					{
						submenu.css({
							'display': 'none'
						});
					}
				});
			
			}else{
				$(this).mouseover( function()
				{
					
				})
				
			}
		});
		
		menuWrapper.show();
	};  
})(jQuery); 
