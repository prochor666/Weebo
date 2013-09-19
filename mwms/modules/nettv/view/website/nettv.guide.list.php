<?php
$g = new WeeboNettvRender;

echo $g->renderGuideList();
?>
<script type="text/javascript">
// <![CDATA[
var bindGuideTabs = function(newHash, oldHash){
	
	var days = $('.program-tabs .program-day');
	var dayBlocks = $('.program-wrap');
	
	var identifier = location.hash; 
	
	if (identifier.length > 0 && $(identifier).length > 0 ){
		dayBlocks.removeClass('wrap-selected');
		days.removeClass('selected');
		
		$(identifier+'-link').addClass('selected');
		$(identifier).addClass('wrap-selected');
		$('html, body').animate({ scrollTop: $('#container').offset().top - 60}, 'fast');
	}
}

$(document).ready(function(){
	
	//$('.program-item .program-name').hide();
	$('.item-star .program-name').show();
	
	$('.program-item').each( function(){
		var title = $(this).find('.program-title');
		var desc = $(this).find('.program-name');
		
		if(desc.length == 1){
			
			if($(this).hasClass('item-star')){
				title.removeClass('plus').addClass('minus');
			}else{
				title.removeClass('minus').addClass('plus');
			}
			
			title.on('click', function(){
				desc.toggle( function(){
					if($(this).is(':visible')){
						title.removeClass('plus').addClass('minus');
					}else{
						title.removeClass('minus').addClass('plus');
					}
				});
			});
		}
	});
	
	
	// Trigger on load
	$(window).hashChange();
	
	// Bind the event.
	$(window).hashChange( bindGuideTabs );

});
// ]]>
</script>
