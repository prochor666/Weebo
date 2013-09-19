<?php
$action = isset($_GET['action']) ? $_GET['action']: 'undefined';
$id_article = isset($_GET['id_article']) ? $_GET['id_article']: 0;
$id_article = $id_article == 0 && isset($_POST['id_article']) && (int)$_POST['id_article']>0 ? $_POST['id_article']: $id_article;

$ass = new ArticleBrowser;

//System::dump($_POST);

if($action == 'del'){

	echo '<div id="links-action-init">'.$ass->articleDelete($id_article).'</div>';

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
