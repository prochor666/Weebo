<?php
$id_link = isset($_GET['id_link']) ? (int)$_GET['id_link']: 0;
$_GET['content_page'] = 1;
if($id_link>0){
	$ass = new contentBrowserTemplate;
	$ass->id_link = $id_link;
	
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
