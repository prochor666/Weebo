<?php
$action = isset($_GET['action']) ? $_GET['action']: 'undefined';
$id_team = isset($_GET['id_team']) ? $_GET['id_team']: 0;
$id_team = $id_team == 0 && isset($_POST['id_team']) && (int)$_POST['id_team']>0 ? $_POST['id_team']: $id_team;

$ass = new TeamBrowser;

//System::dump($_POST);

if($action == 'del')
{

	echo '<div id="links-action-init">'.$ass->teamDelete($id_team).'</div>';

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
