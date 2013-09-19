<?php
$action = isset($_GET['action']) ? $_GET['action']: 'undefined';
$id_form = isset($_GET['id_form']) ? $_GET['id_form']: 0;

$ass = new FormBrowser;

//System::dump($_POST);

	if($action == 'del'){

		echo '<div id="links-action-init">'.$ass->formDelete($id_form).'</div>';

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
