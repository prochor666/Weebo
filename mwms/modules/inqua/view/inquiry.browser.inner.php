<?php
	$mm = new InquiryBrowserTemplate;
	echo $mm->showContent();
	
	$O = urlencode($mm->order_case['inquiry']);
	$D = $mm->order_default_direction['inquiry'];
	$defaultOrderUrl = $mm->ajax_view_url.'inquiry.browser.inner.php'.$mm->ajax_view_url_suffix.'&inquiry_order='.urlencode($mm->default_custom_order).'&inquiry_order_direction=';
?>
<script type="text/javascript">
/* <![CDATA[ */
$(document).ready(function(){
	
	var defaultOrderUrl = '<?php echo $defaultOrderUrl; ?>';
	
	/* Pager */
	var targetContainer = $('#mwms_inquiry_show');
	
	$("div.weebo_pager_fixed a").each( function(){
		var pager_uri = $(this).attr("href");
		
		$(this).button().click( function(){
			targetContainer.html('');
			InquaAdmin.showPreloader(targetContainer, 100);
			targetContainer.load( pager_uri );
			return false;
		});
	});

	/* Order */
	$("div.order_box a").each( function(){
		var order_uri = $(this).attr("href");
		
		$(this).click( function(){
			targetContainer.html('');
			InquaAdmin.showPreloader(targetContainer, 100);
			targetContainer.load( order_uri );
			return false;
		});
	});
	

	$('button.inqua_inquiry_new').button({
		icons: {
			primary: "ui-icon-newwin"
		}
	}).click( function(){
			
			var tabID = '0';
			
			var tabURI = weebo.settings.AjaxCall + "require&file=/mwms/modules/inqua/view/inquiry.detail.process.php&id_inquiry=" + tabID;
			InquaAdmin.addTab( tabURI, tabID, $(this).attr('title'), true);
			
	});

	
	/* Filter button */
	$('button.inquiry_search_send').button({
		icons: {
			primary: "ui-icon-search"
		}
	});
	
	$('button.inquiry_search_reset').button({
		icons: {
			primary: "ui-icon-circle-close"
		}
	});
	
	/* Fulltext */
	$("#inquiry_browser").each( function(){
		
			var search_uri = $(this).find('input#inquiry_search_path').val();
			var search_button = $(this).find('button.inquiry_search_send');
			var reset_button = $(this).find('button.inquiry_search_reset');
			var search_field = $('input#inquiry_search');
			
			reset_button.click(
				
				function(){
					$('input#inquiry_search').val('');
					
					targetContainer.html('');
					InquaAdmin.showPreloader(targetContainer, 100);
					targetContainer.load( search_uri + '&inquiry_search_term=' );
					
					return false;
				}
			);	
			
			search_button.click(
				
				function(){
					var search_term = encodeURIComponent( search_field.val() );
				
					if(search_term.length >= <?php echo $mm->search_term_length_min; ?>){
						
						targetContainer.html('');
						InquaAdmin.showPreloader(targetContainer, 100);
						targetContainer.load( search_uri + '&inquiry_search_term=' + $.trim(search_term) );
						
						return false;
					}else{
						alert('<?php echo Lng::get('inqua/search_term_short'); ?>');
					}
				
				}
			);	
			
			
			search_field.bind('keypress', function(e) {
					if(e.keyCode==13){
							// si entruj
							var search_term = encodeURIComponent( search_field.val() );
						
							if(search_term.length >= <?php echo $mm->search_term_length_min; ?>){
								
								targetContainer.html('');
								InquaAdmin.showPreloader(targetContainer, 100);
								targetContainer.load( search_uri + '&inquiry_search_term=' + $.trim(search_term) );
						
							}else{
								alert('<?php echo Lng::get('inqua/search_term_short'); ?>');
							}
						
					}
					
			});

	});
	
	/* New tab opening */
	$("#mwms_inquiry_show input[name='id_inquiry']").each( function(){
			
			$(this).disableSelection();
			var tabID = $(this).val();
			var tabURI = "require&file=/mwms/modules/inqua/view/inquiry.detail.process.php&id_inquiry=" + tabID;
			var delURI = "require&file=/mwms/modules/inqua/view/inquiry.action.php&id_inquiry=" + tabID;
			var tabTitle = $('#content_cast_'+tabID+'').attr('title');
			
			var tbutton = new Array();
					
			tbutton[0] = {
				title : '<?php echo Lng::get('inqua/inqua_inquiry_edit'); ?>',
				name : 'edit',
				icon : 'ui-icon-pencil',
				text : false,
				xcall: function(){
					InquaAdmin.addTab( weebo.settings.AjaxCall + tabURI, tabID, tabTitle, true );
					$('div.mwms-data-widget').remove();
					return false;
				}
			};	
				
			tbutton[1] = {
				title : '<?php echo Lng::get('inqua/inqua_inquiry_del'); ?>',
				name : 'del',
				icon : 'ui-icon-closethick',
				text : false,
				xcall: function(){
					
					var question = '<?php echo Lng::get('inqua/inqua_confirm_del'); ?>: ' + tabTitle +'?';
					var confirmDelete = confirm(question);
					
					if(confirmDelete === true)
					{
						InquaAdmin.removeItem( delURI, '<?php echo Lng::get('inqua/inqua_inquiry_del'); ?>' );
						$('div.mwms-data-widget').remove();
						return false;
					}
				}
			};
			
			var conf = {
				title : tabTitle,
				id : tabID,
				element : '#content_cast_'+tabID+' .toolbar',
				buttons : tbutton
			}
			 
			InquaAdmin.toggleToolbar( conf );
			
			$('#content_cast_'+tabID+'').rightClick(
				function(){
					
					InquaAdmin.addTab( weebo.settings.AjaxCall + tabURI, tabID, tabTitle, false );
				}
			).click(
				function(){
					//InquaAdmin.addTab( weebo.settings.AjaxCall + tabURI, tabID, tabTitle, true );
				}
			).css({ 'cursor': 'pointer'  });
	});

});
/* ]]> */
</script>

