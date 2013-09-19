<?php
	$cb = new InquiryBrowserTemplate;
	echo '<div id="mwms_inquiry_show"></div>';
	$inipath = html_entity_decode($cb->ajax_view_url.'inquiry.browser.inner.php'.$cb->ajax_view_url_suffix);
?>
<script type="text/javascript">
/* <![CDATA[ */
$(document).ready(function(){
	var contentContainerInner = $('#mwms_inquiry_show');
	contentContainerInner.load('<?php echo $inipath; ?>');
});
/* ]]> */
</script>
