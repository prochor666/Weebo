<?php
$action = isset($_GET['action']) ? $_GET['action']: 'undefined';
$id_channel = isset($_GET['id_channel']) ? $_GET['id_channel']: 0;
$id_channel = $id_channel == 0 && isset($_POST['id_channel']) && (int)$_POST['id_channel']>0 ? $_POST['id_channel']: $id_channel;

$ass = new ChannelBrowser;

//System::dump($_POST);

if($action == 'del'){

	echo '<div id="links-action-init">'.$ass->channelDelete($id_channel).'</div>';

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
