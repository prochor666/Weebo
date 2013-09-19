<?php
	$cb = new AnswerBrowserTemplate;
	echo '<div id="mwms_answer_show"></div>';
	$inipath = html_entity_decode($cb->ajax_view_url.'answer.browser.inner.php'.$cb->ajax_view_url_suffix);
?>
<script type="text/javascript">
/* <![CDATA[ */
$(document).ready(function(){
	var contentContainerInner = $('#mwms_answer_show');
	contentContainerInner.load('<?php echo $inipath; ?>');
});
/* ]]> */
</script>
