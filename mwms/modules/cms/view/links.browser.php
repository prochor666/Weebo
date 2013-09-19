<?php
$ass = new LinksBrowserTemplate;
echo '
<div id="cms_main">
<div id="mwms_load_links_inner" class="ui-widget ui-widget-content ui-corner-all"></div>
<div id="mwms_load_content_inner"></div>
<div class="cleaner"></div>
</div>
';

$links_inipath = html_entity_decode($ass->ajax_view_url.'links.browser.control.php'.$ass->ajax_view_url_suffix);

$firstLink = html_entity_decode($ass->ajax_view_url.'content.browser.control.php'.$ass->ajax_view_url_suffix).'&id_link='.Registry::get('cms_active_link');
?>
<script type="text/javascript">
/* <![CDATA[ */
$(document).ready(function(){
	var firstLinkReg = '<?php echo $firstLink; ?>';
	var firstLinkIDStr = firstLinkReg.replace('&amp;', '&');
	var firstLinkIDA = firstLinkIDStr.split('&');
	var firstLinkIDAIndex = firstLinkIDA.length - 1;
	var firstLinkID = firstLinkIDA[firstLinkIDAIndex];
	
	var linksContainer = $('div#mwms_load_links_inner');
	
	linksContainer.load('<?php echo $links_inipath; ?>', function(){
		
		if(linksContainer.find('ul li').length>0){
			var contentContainer = $('div#mwms_load_content_inner');
			var firstContentLink = firstLinkID == 'id_link=0' || firstLinkID == 'id_link=' ? $('a.link_target:first').attr('href').replace('&amp;', '&'): firstLinkReg.replace('&amp;', '&');
			contentContainer.load(firstContentLink);
			
			$("ul.links_list a").each( function(){
					
					$(this).on('click', function(e){
						e.preventDefault();
						contentContainer.html('');
						$("li.nav_link a span.ui-icon").removeClass('ui-icon-folder-open').addClass('ui-icon-document');
						$("li.nav_link a").removeClass('highlight').addClass('nh');
						$(this).find('span.ui-icon').removeClass('ui-icon-document').addClass('ui-icon-folder-open');
						$(this).removeClass('nh').addClass('highlight');
						//cms.showPreloader(contentContainer, 100);
						contentContainer.load( $(this).attr('href') );
					});
			});
		}
	});

});
/* ]]> */
</script>
