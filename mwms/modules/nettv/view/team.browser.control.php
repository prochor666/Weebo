<?php
	$cb = new TeamBrowserTemplate;
	echo '<div id="mwms_team_team"></div>';
	$inipath = html_entity_decode($cb->ajax_view_url.'team.browser.inner.php'.$cb->ajax_view_url_suffix);
?>
<script type="text/javascript">
/* <![CDATA[ */
$(document).ready(function(){
	var contentContainerInner = $('#mwms_team_team');
	contentContainerInner.load('<?php echo $inipath; ?>');
});
/* ]]> */
</script>
