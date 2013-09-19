<?php
	$mm = new AnswerBrowserTemplate;
	echo $mm->showContent();
	
	$O = urlencode($mm->order_case['answer']);
	$D = $mm->order_default_direction['answer'];
	$defaultOrderUrl = $mm->ajax_view_url.'answer.browser.inner.php'.$mm->ajax_view_url_suffix.'&answer_order='.urlencode($mm->default_custom_order).'&answer_order_direction=';
?>
<script type="text/javascript">
/* <![CDATA[ */
$(document).ready(function(){
	
	var defaultOrderUrl = '<?php echo $defaultOrderUrl; ?>';
	
	/* Pager */
	var targetContainer = $('#mwms_answer_show');
	
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
	

	$('button.inqua_answer_new').button({
		icons: {
			primary: "ui-icon-newwin"
		}
	}).click( function(){
			
			var tabID = '0';
			
			var tabURI = weebo.settings.AjaxCall + "require&file=/mwms/modules/inqua/view/answer.detail.process.php&id_answer=" + tabID;
			InquaAdmin.addTab( tabURI, tabID, $(this).attr('title'), true);
			
	});

	
	/* Filter button */
	$('button.answer_search_send').button({
		icons: {
			primary: "ui-icon-search"
		}
	});
	
	$('button.answer_search_reset').button({
		icons: {
			primary: "ui-icon-circle-close"
		}
	});
	
	/* Fulltext */
	$("#answer_browser").each( function(){
		
			var search_uri = $(this).find('input#answer_search_path').val();
			var search_button = $(this).find('button.answer_search_send');
			var reset_button = $(this).find('button.answer_search_reset');
			var search_field = $('input#answer_search');
			
			reset_button.click(
				
				function(){
					$('input#answer_search').val('');
					
					targetContainer.html('');
					InquaAdmin.showPreloader(targetContainer, 100);
					targetContainer.load( search_uri + '&answer_search_term=' );
					
					return false;
				}
			);	
			
			search_button.click(
				
				function(){
					var search_term = encodeURIComponent( search_field.val() );
				
					if(search_term.length >= <?php echo $mm->search_term_length_min; ?>){
						
						targetContainer.html('');
						InquaAdmin.showPreloader(targetContainer, 100);
						targetContainer.load( search_uri + '&answer_search_term=' + $.trim(search_term) );
						
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
								targetContainer.load( search_uri + '&answer_search_term=' + $.trim(search_term) );
						
							}else{
								alert('<?php echo Lng::get('inqua/search_term_short'); ?>');
							}
						
					}
					
			});

	});

	// filter
	$('#id_inquiry').on('change', function(){
		
		var id_inquiry = $(this).val();
		
		var uri = weebo.settings.AjaxCall + 'require&file=/mwms/modules/inqua/view/set.answer.filter.php&id_inquiry='+id_inquiry;
		
		$.ajax({
			url: uri,
			type: 'get',
			dataType: 'text',
			async: true,
			cache: false,
			success: function(response) {
				
				var filter_uri = $("#mwms_answer_show").find('input#answer_search_path').val();
				
				targetContainer.html('');
				InquaAdmin.showPreloader(targetContainer, 100);
				targetContainer.load( filter_uri );
			},
			error: function(x, t, m) {
				$(e).html('File error');
			}
		});
		
	});
	
	
	/* New tab opening */
	$("#mwms_answer_show input[name='id_answer']").each( function(){
			
			$(this).disableSelection();
			var tabID = $(this).val();
			var tabURI = "require&file=/mwms/modules/inqua/view/answer.detail.process.php&id_answer=" + tabID;
			var delURI = "require&file=/mwms/modules/inqua/view/answer.action.php&id_answer=" + tabID;
			var tabTitle = $('#content_cast_'+tabID+'').attr('title');
			
			var tbutton = new Array();
					
			tbutton[0] = {
				title : '<?php echo Lng::get('inqua/inqua_answer_edit'); ?>',
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
				title : '<?php echo Lng::get('inqua/inqua_answer_del'); ?>',
				name : 'del',
				icon : 'ui-icon-closethick',
				text : false,
				xcall: function(){
					
					var question = '<?php echo Lng::get('inqua/inqua_confirm_del'); ?>: ' + tabTitle +'?';
					var confirmDelete = confirm(question);
					
					if(confirmDelete === true)
					{
						InquaAdmin.removeItem( delURI, '<?php echo Lng::get('inqua/inqua_answer_del'); ?>' );
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

