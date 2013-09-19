<?php
$action = isset($_GET['action']) ? $_GET['action']: 'undefined';
$id_inquiry = isset($_GET['id_inquiry']) ? $_GET['id_inquiry']: 0;
$id_inquiry = $id_inquiry == 0 && isset($_POST['id_inquiry']) && (int)$_POST['id_inquiry']>0 ? $_POST['id_inquiry']: $id_inquiry;

$ass = new InquiryBrowser;

//System::dump($_POST);

if($action == 'del')
{

	echo '<div id="links-action-init">'.$ass->inquiryDelete($id_inquiry).'</div>';

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
