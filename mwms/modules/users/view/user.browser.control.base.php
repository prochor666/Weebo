<?php
$ass = new UserBrowserTemplate;
echo '<div id="mwms_load_content_inner"></div>';
$inipath = html_entity_decode($ass->ajax_view_url.'user.browser.control.php'.$ass->ajax_view_url_suffix);
?>
<script type="text/javascript">
/* <![CDATA[ */
$(document).ready(function(){
	var tome = $('div#mwms_load_content_inner');
	tome.load('<?php echo $inipath; ?>');
});

/* ]]> */
</script>
