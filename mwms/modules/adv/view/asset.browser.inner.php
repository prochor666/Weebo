<?php
	$mm = new AssetBrowserTemplate;
	echo $mm->showContent();
	
	$O = urlencode($mm->order_case['asset']);
	$D = $mm->order_default_direction['asset'];
	$defaultOrderUrl = $mm->ajax_view_url.'asset.browser.inner.php'.$mm->ajax_view_url_suffix.'&asset_order='.urlencode($mm->default_custom_order).'&asset_order_direction=';
?>
<script type="text/javascript">
/* <![CDATA[ */
$(document).ready(function(){
	
	var defaultOrderUrl = '<?php echo $defaultOrderUrl; ?>';
	
	/* Pager */
	var targetContainer = $('#mwms_asset_show');
	
	$("div.weebo_pager_fixed a").each( function(){
		var pager_uri = $(this).attr("href");
		
		$(this).button().on('click', function(){
			targetContainer.html('');
			AdvAdmin.showPreloader(targetContainer, 100);
			targetContainer.load( pager_uri );
			return false;
		});
	});

	/* Order */
	$("div.order_box a").each( function(){
		var order_uri = $(this).attr("href");
		
		$(this).on('click', function(){
			targetContainer.html('');
			AdvAdmin.showPreloader(targetContainer, 100);
			targetContainer.load( order_uri );
			return false;
		});
	});
	
	$('.urltest').button({
		icons : {
			primary : 'ui-icon-newwin'
		}
	});

	$('button.adv_asset_new').button({
		icons: {
			primary: "ui-icon-newwin"
		}
	}).click( function(){
			
			var tabID = '0';
			
			var tabURI = weebo.settings.AjaxCall + "require&file=/mwms/modules/adv/view/asset.detail.process.php&id_asset=" + tabID;
			AdvAdmin.addTab( tabURI, tabID, $(this).attr('title'), true);
			
	});
	
	/* STATS */
	$('.asset-stats').button({
		icons: {
			primary: "ui-icon-image"
		}
	});
	
	/* Filter button */
	$('button.asset_search_send').button({
		icons: {
			primary: "ui-icon-search"
		}
	});
	
	$('button.asset_search_reset').button({
		icons: {
			primary: "ui-icon-circle-close"
		}
	});
	
	/* Fulltext */
	$("#asset_browser").each( function(){
		
			var search_uri = $(this).find('input#asset_search_path').val();
			var search_button = $(this).find('button.asset_search_send');
			var reset_button = $(this).find('button.asset_search_reset');
			var search_field = $('input#asset_search');
			
			reset_button.click(
				
				function(){
					$('input#asset_search').val('');
					
					targetContainer.html('');
					AdvAdmin.showPreloader(targetContainer, 100);
					targetContainer.load( search_uri + '&asset_search_term=' );
					
					return false;
				}
			);	
			
			search_button.click(
				
				function(){
					var search_term = encodeURIComponent( search_field.val() );
				
					if(search_term.length >= <?php echo $mm->search_term_length_min; ?>){
						
						targetContainer.html('');
						AdvAdmin.showPreloader(targetContainer, 100);
						targetContainer.load( search_uri + '&asset_search_term=' + $.trim(search_term) );
						
						return false;
					}else{
						alert('<?php echo Lng::get('adv/search_term_short'); ?>');
					}
				
				}
			);	
			
			
			search_field.bind('keypress', function(e) {
					if(e.keyCode==13){
							// si entruj
							var search_term = encodeURIComponent( search_field.val() );
						
							if(search_term.length >= <?php echo $mm->search_term_length_min; ?>){
								
								targetContainer.html('');
								AdvAdmin.showPreloader(targetContainer, 100);
								targetContainer.load( search_uri + '&asset_search_term=' + $.trim(search_term) );
						
							}else{
								alert('<?php echo Lng::get('adv/search_term_short'); ?>');
							}
						
					}
					
			});

	});

	/* New tab opening */
	$("#mwms_asset_show input[name='id_asset']").each( function(){
			
			$(this).disableSelection();
			var tabID = $(this).val();
			var tabURI = "require&file=/mwms/modules/adv/view/asset.detail.process.php&id_asset=" + tabID;
			var delURI = "require&file=/mwms/modules/adv/view/asset.action.php&id_asset=" + tabID;
			var tabTitle = $('#content_cast_'+tabID+'').attr('title');
			
			var tbutton = new Array();
					
			tbutton[0] = {
				title : '<?php echo Lng::get('adv/adv_asset_edit'); ?>',
				name : 'edit',
				icon : 'ui-icon-pencil',
				text : false,
				xcall: function(){
					AdvAdmin.addTab( weebo.settings.AjaxCall + tabURI, tabID, tabTitle, true );
					$('div.mwms-data-widget').remove();
					return false;
				}
			};	
				
			tbutton[1] = {
				title : '<?php echo Lng::get('adv/adv_asset_del'); ?>',
				name : 'del',
				icon : 'ui-icon-closethick',
				text : false,
				xcall: function(){
					
					var question = '<?php echo Lng::get('adv/adv_confirm_del'); ?>: ' + tabTitle +'?';
					var confirmDelete = confirm(question);
					
					if(confirmDelete === true)
					{
						AdvAdmin.removeItem( delURI, '<?php echo Lng::get('adv/adv_asset_del'); ?>' );
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
			 
			AdvAdmin.toggleToolbar( conf );
			
			$('#content_cast_'+tabID+'').rightClick(
				function(){
					
					AdvAdmin.addTab( weebo.settings.AjaxCall + tabURI, tabID, tabTitle, false );
				}
			).click(
				function(){
					//AdvAdmin.addTab( weebo.settings.AjaxCall + tabURI, tabID, tabTitle, true );
				}
			).css({ 'cursor': 'pointer'  });
	});

});
/* ]]> */
</script>

