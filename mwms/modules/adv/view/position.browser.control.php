<?php
	$cb = new PositionBrowserTemplate;
	echo '<div id="mwms_position_show"></div>';
	$inipath = html_entity_decode($cb->ajax_view_url.'position.browser.inner.php'.$cb->ajax_view_url_suffix);
?>
<script type="text/javascript">
/* <![CDATA[ */
$(document).ready(function(){
	var contentContainerInner = $('#mwms_position_show');
	contentContainerInner.load('<?php echo $inipath; ?>');
});
/* ]]> */
</script>
