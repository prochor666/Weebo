<?php
$id_media = isset($_GET['id_media']) ? (int)$_GET['id_media']: 0;
$id_dir = isset($_GET['id_dir']) ? (int)$_GET['id_dir']: 0;
$reset_pager = isset($_GET['reset_pager']) ? (int)$_GET['reset_pager']: 0;
$action = isset($_GET['action']) ? $_GET['action']: null;

if($action == 'del' && $id_media > 0){
	$ass = new MediaBrowser;
	echo '<div id="file-action-init">'.$ass->fileDelete($id_media).'</div>';
	
echo '
<script type="text/javascript">
/* <![CDATA[ */
var reset_pager = parseInt("'.$reset_pager.'");

$(document).ready(function(){
	
	if(reset_pager == 1){
		
	}
	
	$("#tabs").tabs("load", 1);
	$("#tabs").tabs("option", "active", 1);
	
	setTimeout(\'$("#weebo-modal-dialog-content").dialog("close");\', 1000);
});
/* ]]> */
</script>
';
}
?>
