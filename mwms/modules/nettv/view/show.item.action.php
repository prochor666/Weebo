<?php
$action = isset($_GET['action']) ? $_GET['action']: 'undefined';
$id_item = isset($_GET['id_item']) ? $_GET['id_item']: 0;
//$id_item = $id_item == 0 && isset($_POST['id_item']) && (int)$_POST['id_item']>0 ? $_POST['id_item']: $id_item;
$id_import = isset($_GET['id_import']) ? $_GET['id_import']: 0;

$ass = new ShowItemBrowser;

//System::dump($_POST);

if($action == 'del' && $id_item>0)
{

	echo '<div id="links-action-init">'.$ass->itemDelete($id_item, $id_import).'</div>';

	echo '
		<script type="text/javascript">
		/* <![CDATA[ */
		$(document).ready(function(){
			$("#tabs").tabs("option", "active", 0);
			$("#tabs").tabs("load", 0);
			setTimeout(\'$("#weebo-modal-dialog-content").dialog("close");\', 600);
		});
		/* ]]> */
		</script>
	';
}
?>
