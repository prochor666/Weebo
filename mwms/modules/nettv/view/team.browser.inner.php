<?php
	$mm = new TeamBrowserTemplate;
	echo $mm->teamContent();
	
	$O = urlencode($mm->order_case['team']);
	$D = $mm->order_default_direction['team'];
	$defaultOrderUrl = $mm->ajax_view_url.'team.browser.inner.php'.$mm->ajax_view_url_suffix.'&team_order='.urlencode($mm->default_custom_order).'&team_order_direction=';
?>
<script type="text/javascript">
/* <![CDATA[ */
$(document).ready(function(){
	
	var defaultOrderUrl = '<?php echo $defaultOrderUrl; ?>';
	
	/* Pager */
	var targetContainer = $('#mwms_team_team');
	
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
	

	$('button.tv_team_new').button({
		icons: {
			primary: "ui-icon-newwin"
		}
	}).click( function(){
			
			var tabID = '0';
			
			var tabURI = weebo.settings.AjaxCall + "require&file=/mwms/modules/nettv/view/team.detail.process.php&id_team=" + tabID;
			NettvAdmin.addTab( tabURI, tabID, $(this).attr('title'), true);
			
	});

	
	/* Filter button */
	$('button.team_search_send').button({
		icons: {
			primary: "ui-icon-search"
		}
	});
	
	$('button.team_search_reset').button({
		icons: {
			primary: "ui-icon-circle-close"
		}
	});
	
	/* Fulltext */
	$("#team_browser").each( function(){
		
			var search_uri = $(this).find('input#team_search_path').val();
			var search_button = $(this).find('button.team_search_send');
			var reset_button = $(this).find('button.team_search_reset');
			var search_field = $('input#team_search');
			
			reset_button.click(
				
				function(){
					$('input#team_search').val('');
					
					targetContainer.html('');
					NettvAdmin.showPreloader(targetContainer, 100);
					targetContainer.load( search_uri + '&team_search_term=' );
					
					return false;
				}
			);	
			
			search_button.click(
				
				function(){
					var search_term = encodeURIComponent( search_field.val() );
				
					if(search_term.length >= <?php echo $mm->search_term_length_min; ?>){
						
						targetContainer.html('');
						NettvAdmin.showPreloader(targetContainer, 100);
						targetContainer.load( search_uri + '&team_search_term=' + $.trim(search_term) );
						
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
								targetContainer.load( search_uri + '&team_search_term=' + $.trim(search_term) );
						
							}else{
								alert('<?php echo Lng::get('nettv/search_term_short'); ?>');
							}
						
					}
					
			});

	});

	/* New tab opening */
	$("#mwms_team_team input[name='id_team']").each( function(){
			
			$(this).disableSelection();
			var tabID = $(this).val();
			var tabURI = "require&file=/mwms/modules/nettv/view/team.detail.process.php&id_team=" + tabID;
			var delURI = "require&file=/mwms/modules/nettv/view/team.action.php&id_team=" + tabID;
			var tabTitle = $('#content_cast_'+tabID+'').attr('title');
			
			var tbutton = new Array();
					
			tbutton[0] = {
				title : '<?php echo Lng::get('nettv/tv_team_edit'); ?>',
				name : 'edit',
				icon : 'ui-icon-pencil',
				text : false,
				xcall: function(){
					NettvAdmin.addTab( weebo.settings.AjaxCall + tabURI, tabID, tabTitle, true );
					$('div.mwms-data-widget').remove();
					return false;
				}
			};	
				
			tbutton[1] = {
				title : '<?php echo Lng::get('nettv/tv_team_del'); ?>',
				name : 'del',
				icon : 'ui-icon-closethick',
				text : false,
				xcall: function(){
					
					var question = '<?php echo Lng::get('nettv/tv_confirm_del'); ?>: ' + tabTitle +'?';
					var confirmDelete = confirm(question);
					
					if(confirmDelete === true)
					{
						NettvAdmin.removeItem( delURI, '<?php echo Lng::get('nettv/tv_team_del'); ?>' );
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

});
/* ]]> */
</script>

