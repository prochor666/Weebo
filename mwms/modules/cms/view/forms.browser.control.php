<?php
$frm = new FormBrowserTemplate;
$inipath = html_entity_decode($frm->ajax_view_url.'forms.browser.inner.php'.$frm->ajax_view_url_suffix);
echo '
<div id="cms_main">
<div id="mwms_load_forms_inner"></div>
</div>
';
?>
<script type="text/javascript">
/* <![CDATA[ */
$(document).ready(function(){
	var contentContainerInner = $('#mwms_load_forms_inner');
	contentContainerInner.load('<?php echo $inipath; ?>');
});
/* ]]> */
</script>
