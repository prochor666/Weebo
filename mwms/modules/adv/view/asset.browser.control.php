<?php
	$cb = new AssetBrowserTemplate;
	echo '<div id="mwms_asset_show"></div>';
	$inipath = html_entity_decode($cb->ajax_view_url.'asset.browser.inner.php'.$cb->ajax_view_url_suffix);
?>
<script type="text/javascript">
/* <![CDATA[ */
$(document).ready(function(){
	var contentContainerInner = $('#mwms_asset_show');
	contentContainerInner.load('<?php echo $inipath; ?>');
});
/* ]]> */
</script>
