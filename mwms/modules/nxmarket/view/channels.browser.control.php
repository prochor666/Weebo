<?php
	$cb = new SourceBrowserTemplate;
	echo '<div id="mwms_source_show"></div>';
	$inipath = html_entity_decode($cb->ajax_view_url.'sources.browser.inner.php'.$cb->ajax_view_url_suffix);
?>
<script type="text/javascript">
/* <![CDATA[ */
$(document).ready(function(){
	var contentContainerInner = $('#mwms_source_show');
	contentContainerInner.load('<?php echo $inipath; ?>');
});
/* ]]> */
</script>
