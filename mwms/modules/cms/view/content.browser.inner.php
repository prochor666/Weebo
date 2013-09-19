<?php
$id_link = isset($_GET['id_link']) ? (int)$_GET['id_link']: 0;

if($id_link>0){
	Registry::set('cms_active_link', $id_link);
}

if($id_link>0){
	$ass = new contentBrowserTemplate;
	$ass->id_link = $id_link;
	
	echo $ass->showContent();
	
	$O = urlencode($ass->order_case['content']);
	$D = $ass->order_default_direction['content'];
	$defaultOrderUrl = $ass->ajax_view_url.'content.browser.inner.php'.$ass->ajax_view_url_suffix.'&content_order='.urlencode($ass->default_custom_order).'';
?>
<script type="text/javascript">
/* <![CDATA[ */
$(document).ready(function(){
	
	var idLink = '<?php echo $id_link ?>';
	var defaultOrderUrl = '<?php echo html_entity_decode($defaultOrderUrl); ?>&id_link='+idLink+'&content_order_direction=';
	
	/* Pager */
	var targetContainer = $('div#mwms_content_show');
	
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
		var order_uri = $(this).attr("href") + '&id_link='+idLink;
		
		$(this).click( function(){
			targetContainer.html('');
			cms.showPreloader(targetContainer, 100);
			targetContainer.load( order_uri );
			return false;
		});
	});
	
	
	$('#mwms_content_default_order').on('click', function(e){
		e.preventDefault();
		targetContainer.html('');
		cms.showPreloader(targetContainer, 100);
		targetContainer.load( defaultOrderUrl );
	}).button({
		icons : {
			primary : 'ui-icon-arrowreturnthick-1-w'
		},
		text : false
	});

	$('button.mwms_content_new').button({
		icons: {
			primary: "ui-icon-newwin"
		}
	}).click( function(){
			
			var tabID = '0';
			
			var tabURI = weebo.settings.AjaxCall + "require&file=/mwms/modules/cms/view/content.detail.process.php&id_content=" + tabID + "&id_link=" + idLink;
			cms.addTab( tabURI, tabID, $(this).attr('title'), true);
			
	});

	
	/* Filter button */
	$('button.content_search_send').button({
		icons: {
			primary: "ui-icon-search"
		}
	});
	
	$('button.content_search_reset').button({
		icons: {
			primary: "ui-icon-circle-close"
		}
	});
	
	/* Fulltext */
	$("#content_browser").each( function(){
		
			var search_uri = $(this).find('input#content_search_path').val();
			var search_button = $(this).find('button.content_search_send');
			var reset_button = $(this).find('button.content_search_reset');
			var search_field = $('input#content_search');
			
			reset_button.click(
				
				function(){
					$('input#content_search').val('');
					
					targetContainer.html('');
					cms.showPreloader(targetContainer, 100);
					targetContainer.load( search_uri + '&content_search_term=' );
					
					return false;
				}
			);	
			
			search_button.click(
				
				function(){
					var search_term = encodeURIComponent( search_field.val() );
				
					if(search_term.length >= <?php echo $ass->search_term_length_min; ?>){
						
						targetContainer.html('');
						cms.showPreloader(targetContainer, 100);
						targetContainer.load( search_uri + '&content_search_term=' + $.trim(search_term) );
						
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
								targetContainer.load( search_uri + '&content_search_term=' + $.trim(search_term) );
						
							}else{
								alert('<?php echo Lng::get('cms/search_term_short'); ?>');
							}
						
					}
					
			});

	});

	/* New tab opening */
	$("#mwms_content_show input[name='id_content']").each( function(){
			
			$(this).disableSelection();
			var tabID = $(this).val();
			var tabURI = "require&file=/mwms/modules/cms/view/content.detail.process.php&id_content=" + tabID + "&id_link=" + idLink;
			var delURI = "require&file=/mwms/modules/cms/view/content.action.php&id_content=" + tabID;
			var tabTitle = $('#content_cast_'+tabID+'').attr('title');
			var textMap =  $('#content_cast_'+tabID+' input[name="textmap"]').val();
			var textBriefLevel =  $('#content_cast_'+tabID+' input[name="id_brief_level"]').val();
			
			var tbutton = new Array();
					
			tbutton[0] = {
				title : '<?php echo Lng::get('cms/mwms_content_preview'); ?>',
				name : 'preview',
				icon : 'ui-icon-zoomin',
				text : false,
				xcall: function(){
					var myPrev = textBriefLevel > 0 ? weebo.settings.SiteRoot+'/<?php echo $ass->linkMap; ?>/'+textMap+'.html?weebo_preview=1': weebo.settings.SiteRoot+'/<?php echo $ass->linkMap; ?>/?weebo_preview=1';
					cms.previewItem( myPrev );
					//$('div.mwms-data-widget').remove();
					return false;
				}
			};	
			
			tbutton[1] = {
				title : '<?php echo Lng::get('cms/mwms_content_edit'); ?>',
				name : 'edit',
				icon : 'ui-icon-pencil',
				text : false,
				xcall: function(){
					cms.addTab( weebo.settings.AjaxCall + tabURI, tabID, tabTitle, true );
					//$('div.mwms-data-widget').remove();
					return false;
				}
			};	
			
			tbutton[2] = {
				title : '<?php echo Lng::get('cms/mwms_content_del'); ?>',
				name : 'del',
				icon : 'ui-icon-closethick',
				text : false,
				xcall: function(){
					
					var question = '<?php echo Lng::get('cms/mwms_confirm_del'); ?>: ' + tabTitle +'?';
					var confirmDelete = confirm(question);
					
					if(confirmDelete === true)
					{
						cms.removeItem( delURI, '<?php echo Lng::get('cms/mwms_content_del'); ?>' );
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
			 
			cms.toggleToolbar( conf );
			
			$('#content_cast_'+tabID+'').rightClick(
				function(){
					cms.addTab( weebo.settings.AjaxCall + tabURI, tabID, tabTitle, false );
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
<?php } ?>
