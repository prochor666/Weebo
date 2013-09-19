<?php
	$mm = new ItemBrowserTemplate;
	echo $mm->showContent();
	
	$O = urlencode($mm->order_case['items']);
	$D = $mm->order_default_direction['items'];
	$defaultOrderUrl = $mm->ajax_view_url.'items.browser.inner.php'.$mm->ajax_view_url_suffix.'&items_order='.urlencode($mm->default_custom_order).'&items_order_direction=';
?>
<script type="text/javascript">
/* <![CDATA[ */
$(document).ready(function(){
	
	var defaultOrderUrl = '<?php echo $defaultOrderUrl; ?>';
	
	/* Pager */
	var targetContainer = $('#items_show');
	
	$("div.weebo_pager_fixed a").each( function(){
		var pager_uri = $(this).attr("href");
		
		$(this).button().click( function(){
			targetContainer.html('');
			NxMarket.showPreloader(targetContainer, 100);
			targetContainer.load( pager_uri );
			return false;
		});
	});

	/* Order */
	$("div.order_box a").each( function(){
		var order_uri = $(this).attr("href");
		
		$(this).click( function(){
			targetContainer.html('');
			NxMarket.showPreloader(targetContainer, 100);
			targetContainer.load( order_uri );
			return false;
		});
	});
	

	$('button.items_new').button({
		icons: {
			primary: "ui-icon-newwin"
		}
	}).click( function(){
			
			var tabID = '0';
			
			var tabURI = weebo.settings.AjaxCall + "require&file=/mwms/modules/nxmarket/view/items.detail.process.php&id_item=" + tabID;
			NxMarket.addTab( tabURI, tabID, $(this).attr('title'), true);
			
	});

	/* Fulltext */
	$("#items_browser").each( function(){
		
			var search_uri = $(this).find('input#items_search_path').val();
			var search_button = $(this).find('button.items_search_send');
			var reset_button = $(this).find('button.items_search_reset');
			var search_field = $('input#items_search');
			
			reset_button.on('click',
							
				function(){
					$('input#items_search').val('');
					
					targetContainer.html('');
					NxMarket.showPreloader(targetContainer, 100);
					targetContainer.load( search_uri + '&items_search_term=' );
					
					return false;
				}
			).button({
				icons: {
					primary: "ui-icon-circle-close"
				}
			});	
			
			search_button.on('click',
				
				function(){
					var search_term = encodeURIComponent( search_field.val() );
				
					if(search_term.length >= <?php echo $mm->search_term_length_min; ?>){
						
						targetContainer.html('');
						NxMarket.showPreloader(targetContainer, 100);
						targetContainer.load( search_uri + '&items_search_term=' + $.trim(search_term) );
						
						return false;
					}else{
						alert('<?php echo Lng::get('nxmarket/search_term_short'); ?>');
					}
				
				}
			).button({
				icons: {
					primary: "ui-icon-search"
				}
			});	
			
			
			search_field.bind('keypress', function(e) {
					if(e.keyCode==13){
							// si entruj
							var search_term = encodeURIComponent( search_field.val() );
						
							if(search_term.length >= <?php echo $mm->search_term_length_min; ?>){
								
								targetContainer.html('');
								NxMarket.showPreloader(targetContainer, 100);
								targetContainer.load( search_uri + '&items_search_term=' + $.trim(search_term) );
						
							}else{
								alert('<?php echo Lng::get('nxmarket/search_term_short'); ?>');
							}
						
					}
					
			});

	});

	// filter
	$('#id_item').on('change', function(){
		
		var id_item = $(this).val();
		
		var uri = weebo.settings.AjaxCall + 'require&file=/mwms/modules/nxmarket/view/set.items.filter.php&id_item='+id_item;
		
		$.ajax({
			url: uri,
			type: 'get',
			dataType: 'text',
			async: true,
			cache: false,
			success: function(response) {
				
				var filter_uri = $("#items_show").find('input#items_search_path').val();
				
				targetContainer.html('');
				NxMarket.showPreloader(targetContainer, 100);
				targetContainer.load( filter_uri );
			},
			error: function(x, t, m) {
				$(e).html('File error');
			}
		});
		
	});
	
	/* New tab opening */
	$("#items_show input[name='id_item']").each( function(){
			
			$(this).disableSelection();
			var tabID = $(this).val();
			var tabURI = "require&file=/mwms/modules/nxmarket/view/items.detail.process.php&id_item=" + tabID;
			var delURI = "require&file=/mwms/modules/nxmarket/view/items.action.php&action=del&id_item=" + tabID;
			var tabTitle = $('#content_cast_'+tabID+'').attr('title');
			
			var tbutton = new Array();
					
			tbutton[0] = {
				title : '<?php echo Lng::get('nxmarket/items_edit'); ?>',
				name : 'edit',
				icon : 'ui-icon-pencil',
				text : true,
				xcall: function(){
					NxMarket.addTab( weebo.settings.AjaxCall + tabURI, tabID, tabTitle, true );
					$('div.mwms-data-widget').remove();
					return false;
				}
			};
			
			tbutton[1] = {
				title : '<?php echo Lng::get('nxmarket/items_del'); ?>',
				name : 'del',
				icon : 'ui-icon-closethick',
				text : false,
				xcall: function(){
					
					var question = '<?php echo Lng::get('nxmarket/mwms_confirm_del'); ?>: ' + tabTitle +'?';
					var confirmDelete = confirm(question);
					
					if(confirmDelete === true)
					{
						NxMarket.removeItem( delURI, '<?php echo Lng::get('nxmarket/items_del'); ?>' );
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
			 
			NxMarket.toggleToolbar( conf );
			
			$('#content_cast_'+tabID+'').rightClick(
				function(){
					NxMarket.addTab( weebo.settings.AjaxCall + tabURI, tabID, tabTitle, false );
				}
			).click(
				function(){
					//NxMarket.addTab( weebo.settings.AjaxCall + tabURI, tabID, tabTitle, true );
				}
			).css({ 'cursor': 'pointer'  });
	});

});
/* ]]> */
</script>

