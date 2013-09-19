<?php
	$mm = new ShowItemBrowserTemplate;
	echo $mm->showContent();
	
	$O = urlencode($mm->order_case['show_items']);
	$D = $mm->order_default_direction['show_items'];
	$defaultOrderUrl = $mm->ajax_view_url.'show.item.browser.inner.php'.$mm->ajax_view_url_suffix.'&show_items_order='.urlencode($mm->default_custom_order).'&show_items_order_direction=';
?>
<script type="text/javascript">
/* <![CDATA[ */
$(document).ready(function(){
	
	var defaultOrderUrl = '<?php echo $defaultOrderUrl; ?>';
	
	/* Pager */
	var targetContainer = $('#mwms_show_items_show');
	
	$("div.weebo_pager_fixed a").each( function(){
		var pager_uri = $(this).attr("href");
		
		$(this).button().click( function(){
			targetContainer.html('');
			NettvAdmin.showPreloader(targetContainer, 100);
			targetContainer.load( pager_uri );
			return false;
		});
	});

	/* Order */
	$("div.order_box a").each( function(){
		var order_uri = $(this).attr("href");
		
		$(this).click( function(){
			targetContainer.html('');
			NettvAdmin.showPreloader(targetContainer, 100);
			targetContainer.load( order_uri );
			return false;
		});
	});
	

	$('button.tv_show_items_new').button({
		icons: {
			primary: "ui-icon-newwin"
		}
	}).click( function(){
			var tabID = '0';
			var tabURI = weebo.settings.AjaxCall + "require&file=/mwms/modules/nettv/view/show.item.detail.process.php&id_item=" + tabID;
			NettvAdmin.addTab( tabURI, tabID, $(this).attr('title'), true);
	});

	
	/* Filter button */
	$('button.show_items_search_send').button({
		icons: {
			primary: "ui-icon-search"
		}
	});
	
	$('button.show_items_search_reset').button({
		icons: {
			primary: "ui-icon-circle-close"
		}
	});
	
	$('a.vpr').on('click', function(){
		var f = $(this).attr('href');
		
		var ucache = '&ucache=' + Math.round(Math.random()*10000000000);
		
		var buttons_def = {};
		
		var saveButton = nettvLng.resetButton;
		buttons_def[saveButton] = function() {
			$('#weebo-modal-dialog-content').dialog('close');
		}
		var url = weebo.settings.AjaxCall + "require&file=/mwms/modules/nettv/view/nettv.video.preview.php&pfile=" + f;
		
		$('#weebo-modal-dialog-content').html('').load(url).dialog({
			autoOpen: false,
			height: 500,
			width: 720,
			title: $(this).attr('title'),
			modal: true,
			show: 'slide',
			hide: 'blind',
			buttons: buttons_def,
			zIndex: 9999,
			close: function() {
				$('#weebo-modal-dialog-content').html('');
			}
		});
		
		$('#weebo-modal-dialog-content').dialog('open');
		return false;
	}).button({
		icons: {
			primary: 'ui-icon-play'
			}
	});
	
	/* Fulltext */
	$("#show_items_browser").each( function(){
		
			var search_uri = $(this).find('input#show_items_search_path').val();
			var search_button = $(this).find('button.show_items_search_send');
			var reset_button = $(this).find('button.show_items_search_reset');
			var search_field = $('input#show_items_search');
			
			reset_button.click(
				
				function(){
					$('input#show_items_search').val('');
					
					targetContainer.html('');
					NettvAdmin.showPreloader(targetContainer, 100);
					targetContainer.load( search_uri + '&show_items_search_term=' );
					
					return false;
				}
			);	
			
			search_button.click(
				
				function(){
					var search_term = encodeURIComponent( search_field.val() );
				
					if(search_term.length >= <?php echo $mm->search_term_length_min; ?>){
						
						targetContainer.html('');
						NettvAdmin.showPreloader(targetContainer, 100);
						targetContainer.load( search_uri + '&show_items_search_term=' + $.trim(search_term) );
						
						return false;
					}else{
						alert('<?php echo Lng::get('nettv/search_term_short'); ?>');
					}
				
				}
			);	
			
			
			search_field.bind('keypress', function(e) {
					if(e.keyCode==13){
							// si entruj
							var search_term = encodeURIComponent( search_field.val() );
						
							if(search_term.length >= <?php echo $mm->search_term_length_min; ?>){
								
								targetContainer.html('');
								NettvAdmin.showPreloader(targetContainer, 100);
								targetContainer.load( search_uri + '&show_items_search_term=' + $.trim(search_term) );
						
							}else{
								alert('<?php echo Lng::get('nettv/search_term_short'); ?>');
							}
						
					}
					
			});

	});

	// filter
	$('#id_show').on('change', function(){
		
		var id_show = $(this).val();
		var id_public = $('#hide_unpublished').prop('checked') === true ? 1: 0;
		
		var uri = weebo.settings.AjaxCall + 'require&file=/mwms/modules/nettv/view/nettv.archive.filter.php&id_show='+id_show+'&id_public='+id_public;
		
		$.ajax({
			url: uri,
			type: 'get',
			dataType: 'text',
			async: true,
			cache: false,
			success: function(response) {
				
				var filter_uri = $("#show_items_browser").find('input#show_items_search_path').val();
				
				targetContainer.html('');
				NettvAdmin.showPreloader(targetContainer, 100);
				targetContainer.load( filter_uri );
			},
			error: function(x, t, m) {
				$(e).html('File error');
			}
		});
		
	});
	
	$('#nettv_state_view').on('change', function(){
		
		var id_show = $('#id_show').val();
		var id_public = $(this).prop('checked') === true ? 1: 0;
		
		var uri = weebo.settings.AjaxCall + 'require&file=/mwms/modules/nettv/view/nettv.archive.filter.php&id_show='+id_show+'&id_public='+id_public;
		
		$.ajax({
			url: uri,
			type: 'get',
			dataType: 'text',
			async: true,
			cache: false,
			success: function(response) {
				
				var filter_uri = $("#show_items_browser").find('input#show_items_search_path').val();
				
				targetContainer.html('');
				NettvAdmin.showPreloader(targetContainer, 100);
				targetContainer.load( filter_uri );
			},
			error: function(x, t, m) {
				$(e).html('File error');
			}
		});
		
	});
	
	/* New tab opening */
	$("#mwms_show_items_show input[name='id_item']").each( function(){
			
			$(this).disableSelection();
			var tabID = $(this).val();
			
			var id_import = $('#content_cast_'+tabID+' input[name="id_import"]').val();
			var job_done = $('#content_cast_'+tabID+' input[name="job_done"]').val();
			var tabTitle = $('#content_cast_'+tabID+'').attr('title');
			
			var tabURI = "require&file=/mwms/modules/nettv/view/show.item.detail.process.php&id_item=" + tabID;
			var delURI = "require&file=/mwms/modules/nettv/view/show.item.action.php&id_item=" + tabID + "&id_import=" + id_import;
			var checkURI = "require&file=/mwms/modules/nettv/view/show.item.import.recheck.php&id_import=" + id_import;
			
			var tbutton = new Array();
			
			tbutton[0] = {
				title : '<?php echo Lng::get('nettv/tv_show_items_edit'); ?>',
				name : 'edit',
				icon : 'ui-icon-pencil',
				text : false,
				xcall: function(){
					NettvAdmin.addTab( weebo.settings.AjaxCall + tabURI, tabID, tabTitle, true );
					$('div.mwms-data-widget').remove();
					return false;
				}
			};
			
			if(job_done == 1 || job_done == -3)
			{
				tbutton[1] = {
					title : '<?php echo Lng::get('nettv/tv_job_restart'); ?>',
					name : 'restart',
					icon : 'ui-icon-refresh',
					text : false,
					xcall: function(){
						
						var question = '<?php echo Lng::get('nettv/tv_confirm_restart'); ?>: ' + tabTitle +'?';
						var confirmRestart = confirm(question);
						
						if(confirmRestart === true)
						{
							NettvAdmin.recheckImportItem( weebo.settings.AjaxCall + checkURI + '&job_done=0', id_import );
							return false;
						}
					}
				};
			}
			
			if(job_done == 1)
			{	
				tbutton[2] = {
					title : '<?php echo Lng::get('nettv/tv_job_remake_thumbs'); ?>',
					name : 'rethumb',
					icon : 'ui-icon-image',
					text : false,
					xcall: function(){
						
						var question = '<?php echo Lng::get('nettv/tv_confirm_rethumb'); ?>: ' + tabTitle +'?';
						var confirmRestart = confirm(question);
						
						if(confirmRestart === true)
						{
							NettvAdmin.recheckImportItem( weebo.settings.AjaxCall + checkURI + '&job_done=-1', id_import );
							return false;
						}
					}
				};
			}
			
			indx = job_done == -3 ? 2: 3;
			indx = job_done != 1 && job_done != -3 ? 1: indx;
			
			tbutton[indx] = {
				title : '<?php echo Lng::get('nettv/tv_show_items_del'); ?>',
				name : 'del',
				icon : 'ui-icon-closethick',
				text : false,
				xcall: function(){
					
					var question = '<?php echo Lng::get('nettv/tv_confirm_del'); ?>: ' + tabTitle +'?';
					var confirmDelete = confirm(question);
					
					if(confirmDelete === true)
					{
						NettvAdmin.removeItem( delURI, '<?php echo Lng::get('nettv/tv_show_items_del'); ?>' );
						//$('div.mwms-data-widget').remove();
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
			 
			NettvAdmin.toggleToolbar( conf );
			
			$('#content_cast_'+tabID+'').rightClick(
				function(){
					
					NettvAdmin.addTab( weebo.settings.AjaxCall + tabURI, tabID, tabTitle, false );
				}
			).click(
				function(){
					//NettvAdmin.addTab( weebo.settings.AjaxCall + tabURI, tabID, tabTitle, true );
				}
			).css({ 'cursor': 'pointer'  });
	});
	
	// Image tooltip
	$( 'img.nettv-prev' ).tooltip({
		show: {
			effect: "slideDown",
			duration: 170
		},
		hide: {
			effect: "slideUp",
			duration: 80
		},
		position: { my: "left top", at: "right+15 top-15", collision: "flipfit" },
		content: function() {
			return '<img src="' + $(this).attr('src') + '" alt="~" style="width: 300px;" />';
		}
	});
});
/* ]]> */
</script>

