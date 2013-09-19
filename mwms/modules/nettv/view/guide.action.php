<?php
$action = isset($_GET['action']) ? $_GET['action']: 'undefined';
$id_guide = isset($_GET['id_guide']) ? $_GET['id_guide']: 0;
$id_guide = $id_guide == 0 && isset($_POST['id_guide']) && (int)$_POST['id_guide']>0 ? $_POST['id_guide']: $id_guide;

$ass = new GuideBrowser;

//System::dump($_POST);

if($action == 'del')
{

	echo '<div id="links-action-init">'.$ass->guideDelete($id_guide).'</div>';

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
