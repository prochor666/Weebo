<?php
	$ass = new FormBrowserTemplate;
	echo $ass->showContent();
?>
<script type="text/javascript">
/* <![CDATA[ */
$(document).ready(function(){
	
	/* Pager */
	var targetContainer = $('div#mwms_load_forms_inner');
	
	$("div.weebo_pager_fixed a").each( function(){
		var pager_uri = $(this).attr("href");
		
		$(this).button().click( function(){
			targetContainer.html('');
			cms.showPreloader(targetContainer, 100);
			targetContainer.load( pager_uri );
			return false;
		});
	});

	/* Order */
	$("div.order_box a").each( function(){
		var order_uri = $(this).attr("href");
		
		$(this).click( function(){
			targetContainer.html('');
			cms.showPreloader(targetContainer, 100);
			targetContainer.load( order_uri );
			return false;
		});
	});
	
	$('button.mwms_form_new').button({
		icons: {
			primary: "ui-icon-newwin"
		}
	}).click( function(){
			
			var tabID = '0';
			
			var tabURI = weebo.settings.AjaxCall + "require&file=/mwms/modules/cms/view/form.detail.process.php&id_form=" + tabID;
			cms.addTab( tabURI, tabID, $(this).attr('title'), true);
			
	});

	
	/* Filter button */
	$('button.form_search_send').button({
		icons: {
			primary: "ui-icon-search"
		}
	});
	
	$('button.form_search_reset').button({
		icons: {
			primary: "ui-icon-circle-close"
		}
	});
	
	/* Fulltext */
	$("#form_browser").each( function(){
		
			var search_uri = $(this).find('input#form_search_path').val();
			var search_button = $(this).find('button.form_search_send');
			var reset_button = $(this).find('button.form_search_reset');
			var search_field = $('input#form_search');
			
			reset_button.click(
				
				function(){
					$('input#form_search').val('');
					
					targetContainer.html('');
					cms.showPreloader(targetContainer, 100);
					targetContainer.load( search_uri + '&form_search_term=' );
					
					return false;
				}
			);	
			
			search_button.click(
				
				function(){
					var search_term = encodeURIComponent( search_field.val() );
				
					if(search_term.length >= <?php echo $ass->search_term_length_min; ?>){
						
						targetContainer.html('');
						cms.showPreloader(targetContainer, 100);
						targetContainer.load( search_uri + '&form_search_term=' + $.trim(search_term) );
						
						return false;
					}else{
						alert('<?php echo Lng::get('cms/search_term_short'); ?>');
					}
				
				}
			);	
			
			
			search_field.bind('keypress', function(e) {
					if(e.keyCode==13){
							// si entruj
							var search_term = encodeURIComponent( search_field.val() );
						
							if(search_term.length >= <?php echo $ass->search_term_length_min; ?>){
								
								targetContainer.html('');
								cms.showPreloader(targetContainer, 100);
								targetContainer.load( search_uri + '&form_search_term=' + $.trim(search_term) );
						
							}else{
								alert('<?php echo Lng::get('cms/search_term_short'); ?>');
							}
						
					}
					
			});

	});

	/* New tab opening */
	$(".form_cast").each( function(){
			
			$(this).disableSelection();
			
			var tabIDstr = $(this).attr('id').split('_');
			var tabID = parseInt(tabIDstr[2]);
			var tabURI = "require&file=/mwms/modules/cms/view/form.detail.process.php&id_form=" + tabID;
			var delURI = "require&file=/mwms/modules/cms/view/form.action.php&id_form=" + tabID+"&action=del";
			var tabTitle = $(this).attr('title');
			
			var tbutton = new Array();
					
			tbutton[0] = {
				title : '<?php echo Lng::get('cms/mwms_form_edit'); ?>',
				name : 'edit',
				icon : 'ui-icon-pencil',
				text : false,
				xcall: function(){
					cms.addTab( weebo.settings.AjaxCall + tabURI, tabID, tabTitle, true );
					$('div.mwms-data-widget').remove();
					return false;
				}
			};	
				
			tbutton[1] = {
				title : '<?php echo Lng::get('cms/mwms_form_del'); ?>',
				name : 'del',
				icon : 'ui-icon-closethick',
				text : false,
				xcall: function(){
					
					var question = '<?php echo Lng::get('cms/mwms_confirm_del'); ?>: ' + tabTitle +'?';
					var confirmDelete = confirm(question);
					
					if(confirmDelete === true)
					{
						cms.removeItem( delURI, '<?php echo Lng::get('cms/mwms_form_del'); ?>' );
						$('div.mwms-data-widget').remove();
						return false;
					}
				}
			};
			
			var conf = {
				title : tabTitle,
				id : tabID,
				element : '#'+$(this).attr('id')+' .toolbar',
				buttons : tbutton
				
			}
			 
			cms.toggleToolbar( conf );
			
			
			$(this).rightClick(
				function(){
					
					cms.addTab( weebo.settings.AjaxCall + tabURI, tabID, tabTitle, true );
				}
			).click(
				function(){
					
					//cms.addTab( weebo.settings.AjaxCall + tabURI, tabID, tabTitle, true );

				}
			).css({ 'cursor': 'pointer'  });
	});

});
/* ]]> */
</script>
