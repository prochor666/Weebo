<?php
$action = isset($_GET['action']) ? $_GET['action']: 'undefined';
$id_article = isset($_GET['id_article']) ? $_GET['id_article']: 0;

echo 'Juchhuuuu';
///$ass = new SourceBrowser;

//System::dump($_POST);

if($action == 'edit'){

	echo '
		<script type="text/javascript">
		/* <![CDATA[ */
		$(document).ready(function(){
			$("#tabs").tabs("load", 0);
			$("#tabs").tabs("select", 0);
			//setTimeout(\'$("#weebo-modal-dialog-content").dialog("close");\', 600);
		});
		/* ]]> */
		</script>
		';
}
?>
