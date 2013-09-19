<?php
$action = isset($_GET['action']) ? $_GET['action']: 'undefined';
$id_source = isset($_GET['id_source']) ? $_GET['id_source']: 0;
$id_source = $id_source == 0 && isset($_POST['id_source']) && (int)$_POST['id_source']>0 ? $_POST['id_source']: $id_source;

$ass = new SourceBrowser;

//System::dump($_POST);

if($action == 'del'){

	echo '<div id="links-action-init">'.$ass->sourceDelete($id_source).'</div>';

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
