<?php
$aq = new Cms;

if(isset($_GET['sub'])){
	$activeSubmodule = trim($_GET['sub']);
}else{
	$activeSubmodule = $aq->initial_sub;
}

$activeDomainKey = mb_strlen(Registry::get('active_domain'))>0 ? Registry::get('active_domain'): 0;

$activeDomainLng = mb_strlen(Registry::get('active_domain_lng'))>0 ? Registry::get('active_domain_lng'): $aq->config['default_lng'];
//$activeDomain = $aq->lng['cms_public_domains'][$activeDomainKey]['name'];
Registry::set('active_domain', $activeDomainKey);
Registry::set('active_domain_name', $aq->lng['cms_public_domains'][$activeDomainKey]['name']);
Registry::set('active_domain_lng', $activeDomainLng);

$aq->checkDomainDataDir($aq->lng['cms_public_domains'][$activeDomainKey]['name']);

echo '<div id="cms_load_dashboard">'.$aq->showStaticDashboard().'</div>';
?>
<script type="text/javascript">
/* <![CDATA[ */
var cmsDateTimePrecise = '<?php echo Lng::get('system/date_time_format_precise'); ?>';

cmsLng.resetButton = '<?php echo Lng::get('cms/reset_button'); ?>';
cmsLng.contentAnnotationImageLoad = '<?php echo Lng::get('cms/mwms_content_annotation_image_load'); ?>';
cmsLng.ajaxUploadEnd = '<?php echo Lng::get('system/mwms_ajax_upload_end'); ?>';

$(document).ready(function(){
	
	cms.initUI();

	//$('#set_domain, #set_domain_lng').selectmenu();

	$("a[href$='<?php echo $activeSubmodule; ?>']").addClass('ui-state-active').mouseover(
		function(){
			$(this).addClass('ui-state-active'); 
			return false; 
	}).mouseout( 
		function(){
			$(this).addClass('ui-state-active'); 
			return false; 
	});
	
	$('#set_domain').change(
		function(){
			
			var dUri = weebo.settings.AjaxCall + 'require&file=/mwms/modules/cms/view/cms.domain.set.php';
			var myData = {
				active_domain : $(this).val()
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
					document.location.href = weebo.settings.SiteRoot + '?module=cms';
				}
			});
		}
	);
	
	$('#set_domain_lng').change(
		function(){
			
			var dUri = weebo.settings.AjaxCall + 'require&file=/mwms/modules/cms/view/cms.domain.lng.set.php';
			var myData = {
				active_domain_lng : $(this).val()
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
					document.location.href = weebo.settings.SiteRoot + '?module=cms';
				}
			});
		}
	);

});  
/* ]]> */
</script>
<?php 
require_once($activeSubmodule.'.php');
?>

