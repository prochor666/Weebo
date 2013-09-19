<?php
$action = isset($_GET['action']) ? $_GET['action']: 'undefined';
$id_campaign = isset($_GET['id_campaign']) ? $_GET['id_campaign']: 0;
$id_campaign = $id_campaign == 0 && isset($_POST['id_campaign']) && (int)$_POST['id_campaign']>0 ? $_POST['id_campaign']: $id_campaign;

$ass = new CampaignBrowser;

//System::dump($_POST);

if($action == 'del')
{

	echo '<div id="links-action-init">'.$ass->campaignDelete($id_campaign).'</div>';

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
