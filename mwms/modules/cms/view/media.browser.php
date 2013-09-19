<?php
$ass = new LinksBrowserTemplate;

echo '
<div id="cms_main">
<div id="mwms_load_dirs_inner" class="ui-widget ui-widget-content ui-corner-all"></div>
<div id="mwms_load_dir_list_inner"></div>
<div class="cleaner"></div>
</div>
';

$dirs_inipath = html_entity_decode($ass->ajax_view_url.'media.dir.browser.control.php'.$ass->ajax_view_url_suffix);

$firstDir = html_entity_decode($ass->ajax_view_url.'media.browser.control.php'.$ass->ajax_view_url_suffix).'&id_dir='.Registry::get('cms_active_gallery');
?>
<script type="text/javascript">
/* <![CDATA[ */
$(document).ready(function(){
	
	var firstDirReg = '<?php echo $firstDir; ?>';
	var firstDirIDStr = firstDirReg.replace('&amp;', '&');
	var firstDirIDA = firstDirIDStr.split('&');
	var firstDirIDAIndex = firstDirIDA.length - 1;
	var firstDirID = firstDirIDA[firstDirIDAIndex];
	
	var dirsContainer = $('div#mwms_load_dirs_inner');
	
	dirsContainer.load('<?php echo $dirs_inipath; ?>', function(){

		var dirListContainer = $('div#mwms_load_dir_list_inner');
		
		if( $('a.dir_target').length>0 ){
		
			var firstDirLink = firstDirID == 'id_dir=0' || firstDirID == 'id_dir=' ? $('a.dir_target:first').attr('href').replace('&amp;', '&'): firstDirReg.replace('&amp;', '&');
			dirListContainer.load(firstDirLink);
			$("ul.dir_list a").each( function(){
					
					$(this).on('click', function(e){
						e.preventDefault();
						dirListContainer.html('');
						$("li.nav_link .ui-icon").removeClass('ui-icon-folder-open').addClass('ui-icon-document');
						$("li.nav_link a").removeClass('highlight').addClass('nh');
						$(this).find('span.ui-icon').removeClass('ui-icon-document').addClass('ui-icon-folder-open');
						$(this).removeClass('nh').addClass('highlight');
						dirListContainer.load( $(this).attr('href') );
					});
			});
		}

		$('.cms-dir_type').change(
		function(){
				
				var dUri = weebo.settings.AjaxCall + 'require&file=/mwms/modules/cms/view/cms.dir.set.php';
				var myData = {
					active_domain_dir_type : $(this).val()
				}

				$.ajax({
					url: dUri,
					type: 'post',
					data: myData,
					dataType: 'text',
					async: false,
					cache: false,
					error: function(jqXHR, textStatus, errorThrown){
						$(res).html('ERROR: '+errorThrown);
					},
					success: function(response) {
						document.location.href = weebo.settings.SiteRoot + '?module=cms&sub=media.browser';
					}
				});
			}
		);
		
		
		
	});
	
});
/* ]]> */
</script>
