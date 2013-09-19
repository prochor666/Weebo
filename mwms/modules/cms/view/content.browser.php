<?php
$id_link = isset($_GET['id_link']) ? (int)$_GET['id_link']: 0;

if($id_link>0){

	$ass = new contentBrowserTemplate;
	$ass->id_link = $id_link;
	echo '<div id="mwms_content_show"></div>';
	$inipath = html_entity_decode($ass->ajax_view_url.'content.browser.inner.php'.$ass->ajax_view_url_suffix.'&amp;id_link='.$id_link);
?>
<script type="text/javascript">
/* <![CDATA[ */
$(document).ready(function(){
	var contentContainerInner = $('#mwms_content_show');
	contentContainerInner.load('<?php echo $inipath; ?>');
});
/* ]]> */
</script>
<?php } ?>
