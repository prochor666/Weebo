<?php
	$mm = new ArticleBrowserTemplate;
	echo $mm->showContent();
	
	$O = urlencode($mm->order_case['sources']);
	$D = $mm->order_default_direction['sources'];
	$defaultOrderUrl = $mm->ajax_view_url.'articles.browser.inner.php'.$mm->ajax_view_url_suffix.'&articles_order='.urlencode($mm->default_custom_order).'&articles_order_direction=';
?>
<script type="text/javascript">
/* <![CDATA[ */
$(document).ready(function(){
	
	var defaultOrderUrl = '<?php echo $defaultOrderUrl; ?>';
	
	/* Pager */
	var targetContainer = $('#mwms_article_show');
	
	$("div.weebo_pager_fixed a").each( function(){
		var pager_uri = $(this).attr("href");
		
		$(this).button().click( function(){
			targetContainer.html('');
			MediaMix.showPreloader(targetContainer, 100);
			targetContainer.load( pager_uri );
			return false;
		});
	});

	/* Order */
	$("div.order_box a").each( function(){
		var order_uri = $(this).attr("href");
		
		$(this).click( function(){
			targetContainer.html('');
			MediaMix.showPreloader(targetContainer, 100);
			targetContainer.load( order_uri );
			return false;
		});
	});
	

	$('button.mwms_article_new').button({
		icons: {
			primary: "ui-icon-newwin"
		}
	}).click( function(){
			
			var tabID = '0';
			
			var tabURI = weebo.settings.AjaxCall + "require&file=/mwms/modules/mediamix/view/articles.detail.process.php&id_article=" + tabID;
			MediaMix.addTab( tabURI, tabID, $(this).attr('title'), true);
			
	});

	
	/* Filter button */
	$('button.articles_search_send').button({
		icons: {
			primary: "ui-icon-search"
		}
	});
	
	$('button.articles_search_reset').button({
		icons: {
			primary: "ui-icon-circle-close"
		}
	});
	
	/* Fulltext */
	$("#articles_browser").each( function(){
		
			var search_uri = $(this).find('input#articles_search_path').val();
			var search_button = $(this).find('button.articles_search_send');
			var reset_button = $(this).find('button.articles_search_reset');
			var search_field = $('input#articles_search');
			
			reset_button.click(
				
				function(){
					$('input#articles_search').val('');
					
					targetContainer.html('');
					MediaMix.showPreloader(targetContainer, 100);
					targetContainer.load( search_uri + '&articles_search_term=' );
					
					return false;
				}
			);	
			
			search_button.click(
				
				function(){
					var search_term = encodeURIComponent( search_field.val() );
				
					if(search_term.length >= <?php echo $mm->search_term_length_min; ?>){
						
						targetContainer.html('');
						MediaMix.showPreloader(targetContainer, 100);
						targetContainer.load( search_uri + '&articles_search_term=' + $.trim(search_term) );
						
						return false;
					}else{
						alert('<?php echo Lng::get('mediamix/search_term_short'); ?>');
					}
				
				}
			);	
			
			
			search_field.bind('keypress', function(e) {
					if(e.keyCode==13){
							// si entruj
							var search_term = encodeURIComponent( search_field.val() );
						
							if(search_term.length >= <?php echo $mm->search_term_length_min; ?>){
								
								targetContainer.html('');
								MediaMix.showPreloader(targetContainer, 100);
								targetContainer.load( search_uri + '&articles_search_term=' + $.trim(search_term) );
						
							}else{
								alert('<?php echo Lng::get('mediamix/search_term_short'); ?>');
							}
						
					}
					
			});

	});

	// filter
	$('#id_source').on('change', function(){
		
		var id_source = $(this).val();
		
		var uri = weebo.settings.AjaxCall + 'require&file=/mwms/modules/mediamix/view/set.article.filter.php&id_source='+id_source;
		
		$.ajax({
			url: uri,
			type: 'get',
			dataType: 'text',
			async: true,
			cache: false,
			success: function(response) {
				
				var filter_uri = $("#mwms_article_show").find('input#articles_search_path').val();
				
				targetContainer.html('');
				MediaMix.showPreloader(targetContainer, 100);
				targetContainer.load( filter_uri );
			},
			error: function(x, t, m) {
				$(e).html('File error');
			}
		});
		
	});
	
	/* New tab opening */
	$("#mwms_article_show input[name='id_article']").each( function(){
			
			$(this).disableSelection();
			var tabID = $(this).val();
			var tabURI = "require&file=/mwms/modules/mediamix/view/articles.detail.process.php&id_article=" + tabID;
			var delURI = "require&file=/mwms/modules/mediamix/view/articles.action.php&action=del&id_article=" + tabID;
			var edURI = "require&file=/mwms/modules/mediamix/view/articles.meta.edit.php&id_article=" + tabID;
			var tabTitle = $('#content_cast_'+tabID+'').attr('title');
			
			var tbutton = new Array();
					
			tbutton[0] = {
				title : '<?php echo Lng::get('mediamix/mwms_article_edit'); ?>',
				name : 'edit',
				icon : 'ui-icon-pencil',
				text : true,
				xcall: function(){
					MediaMix.addTab( weebo.settings.AjaxCall + tabURI, tabID, tabTitle, true );
					$('div.mwms-data-widget').remove();
					return false;
				}
			};
			
			tbutton[1] = {
				title : '<?php echo Lng::get('mediamix/mwms_source_edit_meta'); ?>',
				name : 'editMeta',
				icon : 'ui-icon-pencil',
				text : true,
				xcall: function(){
					MediaMix.editMeta( edURI, '<?php echo Lng::get('mediamix/mwms_article_del'); ?>' );
					$('div.mwms-data-widget').remove();
					return false;
				}
			};
			
			tbutton[2] = {
				title : '<?php echo Lng::get('mediamix/mwms_article_del'); ?>',
				name : 'del',
				icon : 'ui-icon-closethick',
				text : false,
				xcall: function(){
					
					var question = '<?php echo Lng::get('mediamix/mwms_confirm_del'); ?>: ' + tabTitle +'?';
					var confirmDelete = confirm(question);
					
					if(confirmDelete === true)
					{
						MediaMix.removeItem( delURI, '<?php echo Lng::get('mediamix/mwms_article_del'); ?>' );
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
			 
			MediaMix.toggleToolbar( conf );
			
			$('#content_cast_'+tabID+'').rightClick(
				function(){
					
					MediaMix.addTab( weebo.settings.AjaxCall + tabURI, tabID, tabTitle, false );
				}
			).click(
				function(){
					//MediaMix.addTab( weebo.settings.AjaxCall + tabURI, tabID, tabTitle, true );
				}
			).css({ 'cursor': 'pointer'  });
	});

});
/* ]]> */
</script>

