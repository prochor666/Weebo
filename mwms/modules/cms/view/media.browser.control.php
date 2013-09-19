<?php
$id_dir = isset($_GET['id_dir']) ? (int)$_GET['id_dir']: 0;

if($id_dir>0){
	Registry::set('cms_active_gallery', $id_dir);
	$ass = new MediaBrowserTemplate;
	$ass->id_dir = $id_dir;
	echo $ass->showBrowserMenu();
} 
?>
<script type="text/javascript">
/* <![CDATA[ */
$(document).ready(function(){
	cms.initTabs();
	$("#tabs").tabs('option', 'active', 1);
});
/* ]]> */
</script>
