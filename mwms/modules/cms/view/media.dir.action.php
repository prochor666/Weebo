<?php
$id_dir = isset($_GET['id_dir']) ? $_GET['id_dir']: 0;
$action = isset($_GET['action']) ? $_GET['action']: null;

if($action == 'del' && $id_dir > 0){
	$ass = new MediaBrowser;
	echo '<div id="dir-action-init">'.$ass->dirDelete($id_dir).'</div>';
	
echo '
<script type="text/javascript">
/* <![CDATA[ */
$(document).ready(function(){
	setTimeout(\'$("#weebo-modal-dialog-content").dialog("close");location.reload(true);\', 1000);
});
/* ]]> */
</script>
';
}
?>
