<?php
$action = isset($_GET['action']) ? $_GET['action']: 'undefined';
$id_position = isset($_GET['id_position']) ? $_GET['id_position']: 0;
$id_position = $id_position == 0 && isset($_POST['id_position']) && (int)$_POST['id_position']>0 ? $_POST['id_position']: $id_position;

$ass = new PositionBrowser;

//System::dump($_POST);

if($action == 'del')
{

	echo '<div id="links-action-init">'.$ass->positionDelete($id_position).'</div>';

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
