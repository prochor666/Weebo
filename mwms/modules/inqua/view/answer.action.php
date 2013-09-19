<?php
$action = isset($_GET['action']) ? $_GET['action']: 'undefined';
$id_answer = isset($_GET['id_answer']) ? $_GET['id_answer']: 0;
$id_answer = $id_answer == 0 && isset($_POST['id_answer']) && (int)$_POST['id_answer']>0 ? $_POST['id_answer']: $id_answer;

$ass = new AnswerBrowser;

//System::dump($_POST);

if($action == 'del')
{

	echo '<div id="links-action-init">'.$ass->answerDelete($id_answer).'</div>';

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
