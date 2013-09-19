<?php
$action = isset($_GET['action']) ? $_GET['action']: 'undefined';
$id_show = isset($_GET['id_show']) ? $_GET['id_show']: 0;
$id_show = $id_show == 0 && isset($_POST['id_show']) && (int)$_POST['id_show']>0 ? $_POST['id_show']: $id_show;

$ass = new ShowBrowser;

//System::dump($_POST);

if($action == 'del')
{

	echo '<div id="links-action-init">'.$ass->showDelete($id_show).'</div>';

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
