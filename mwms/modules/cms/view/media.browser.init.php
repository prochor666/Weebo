<?php
$id_dir = isset($_GET['id_dir']) ? (int)$_GET['id_dir']: 0;

if($id_dir>0){

	$ass = new MediaBrowserTemplate;
	$ass->id_dir = $id_dir;
	echo '<div id="mwms_content_show"></div>';
	$inipath = html_entity_decode($ass->ajax_view_url.'media.browser.inner.php'.$ass->ajax_view_url_suffix.'&amp;id_dir='.$id_dir);
?>
<script type="text/javascript">
/* <![CDATA[ */
$(document).ready(function(){
	var dirContainerInner = $('div#mwms_content_show');
	dirContainerInner.load('<?php echo $inipath; ?>');
});
/* ]]> */
</script>
<?php } ?>
