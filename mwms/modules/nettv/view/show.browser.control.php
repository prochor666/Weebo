<?php
	$cb = new ShowBrowserTemplate;
	echo '<div id="mwms_show_show"></div>';
	$inipath = html_entity_decode($cb->ajax_view_url.'show.browser.inner.php'.$cb->ajax_view_url_suffix);
?>
<script type="text/javascript">
/* <![CDATA[ */
$(document).ready(function(){
	var contentContainerInner = $('#mwms_show_show');
	contentContainerInner.load('<?php echo $inipath; ?>');
});
/* ]]> */
</script>
