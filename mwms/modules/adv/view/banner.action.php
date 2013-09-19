<?php
$action = isset($_GET['action']) ? $_GET['action']: 'undefined';
$id_banner = isset($_GET['id_banner']) ? $_GET['id_banner']: 0;
$id_banner = $id_banner == 0 && isset($_POST['id_banner']) && (int)$_POST['id_banner']>0 ? $_POST['id_banner']: $id_banner;

$ass = new BannerBrowser;

//System::dump($_POST);

if($action == 'del')
{

	echo '<div id="links-action-init">'.$ass->bannerDelete($id_banner).'</div>';

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
