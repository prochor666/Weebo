<?php
	$cb = new ItemBrowserTemplate;
	echo '<div id="items_show"></div>';
	$inipath = html_entity_decode($cb->ajax_view_url.'items.browser.inner.php'.$cb->ajax_view_url_suffix);
?>
<script type="text/javascript">
/* <![CDATA[ */
$(document).ready(function(){
	var contentContainerInner = $('#items_show');
	contentContainerInner.load('<?php echo $inipath; ?>');
});
/* ]]> */
</script>
