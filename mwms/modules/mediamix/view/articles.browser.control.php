<?php
	$cb = new ArticleBrowserTemplate;
	echo '<div id="mwms_article_show"></div>';
	$inipath = html_entity_decode($cb->ajax_view_url.'articles.browser.inner.php'.$cb->ajax_view_url_suffix);
?>
<script type="text/javascript">
/* <![CDATA[ */
$(document).ready(function(){
	var contentContainerInner = $('#mwms_article_show');
	contentContainerInner.load('<?php echo $inipath; ?>');
});
/* ]]> */
</script>
