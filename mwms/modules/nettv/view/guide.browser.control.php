<?php
	$cb = new GuideBrowserTemplate;
	echo '<div id="mwms_guide_show"></div>';
	$inipath = html_entity_decode($cb->ajax_view_url.'guide.browser.inner.php'.$cb->ajax_view_url_suffix);
?>
<script type="text/javascript">
/* <![CDATA[ */
$(document).ready(function(){
	var contentContainerInner = $('#mwms_guide_show');
	contentContainerInner.load('<?php echo $inipath; ?>');
});
/* ]]> */
</script>
