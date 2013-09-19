<?php
$action = isset($_GET['action']) ? $_GET['action']: 'undefined';
$id_asset = isset($_GET['id_asset']) ? $_GET['id_asset']: 0;
$id_asset = $id_asset == 0 && isset($_POST['id_asset']) && (int)$_POST['id_asset']>0 ? $_POST['id_asset']: $id_asset;

$ass = new AssetBrowser;

//System::dump($_POST);

if($action == 'del')
{

	echo '<div id="links-action-init">'.$ass->assetDelete($id_asset).'</div>';

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
