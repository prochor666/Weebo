<?php
$action = isset($_GET['action']) ? $_GET['action']: 'undefined';
$id_cat = isset($_GET['id_cat']) ? $_GET['id_cat']: 0;
$id_cat = $id_cat == 0 && isset($_POST['id_cat']) && (int)$_POST['id_cat']>0 ? $_POST['id_cat']: $id_cat;

$ass = new CatBrowser;

//System::dump($_POST);

if($action == 'del'){

	echo '<div id="links-action-init">'.$ass->catDelete($id_cat).'</div>';

	echo '
		<script type="text/javascript">
		/* <![CDATA[ */
		$(document).ready(function(){
			$("#tabs").tabs("load", 0);
			$("#tabs").tabs("select", 0);
			setTimeout(\'$("#weebo-modal-dialog-content").dialog("close");\', 600);
		});
		/* ]]> */
		</script>
		';
}
?>
