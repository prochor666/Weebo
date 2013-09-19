<?php
	$cb = new CampaignBrowserTemplate;
	echo '<div id="mwms_campaign_show"></div>';
	$inipath = html_entity_decode($cb->ajax_view_url.'campaign.browser.inner.php'.$cb->ajax_view_url_suffix);
?>
<script type="text/javascript">
/* <![CDATA[ */
$(document).ready(function(){
	var contentContainerInner = $('#mwms_campaign_show');
	contentContainerInner.load('<?php echo $inipath; ?>');
});
/* ]]> */
</script>
