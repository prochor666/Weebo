<?php
	$cb = new BannerBrowserTemplate;
	echo '<div id="mwms_banner_show"></div>';
	$inipath = html_entity_decode($cb->ajax_view_url.'banner.browser.inner.php'.$cb->ajax_view_url_suffix);
?>
<script type="text/javascript">
/* <![CDATA[ */
$(document).ready(function(){
	var contentContainerInner = $('#mwms_banner_show');
	contentContainerInner.load('<?php echo $inipath; ?>');
});
/* ]]> */
</script>
