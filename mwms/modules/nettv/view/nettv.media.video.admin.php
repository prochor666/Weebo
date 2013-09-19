<?php
Registry::set('mediatree', array());

$mf = new MediaDisplay;

$mf->rootDir = _GLOBALDATADIR_.'/nettv/archive-local';

echo '
		<div id="mwms_media">
			<div id="mount_header">
				<input type="hidden" id="dir" value="" />
				<code id="mount_header_path"></code>
				<button id="uploader" title="'.$mf->lng['media_dir_upload'].'">'.$mf->lng['media_dir_upload'].'</button>
				<button id="new-dir" title="'.$mf->lng['media_make_dir'].'">'.$mf->lng['media_make_dir'].'</button>
				<button id="refresh" title="'.$mf->lng['media_dir_refresh'].'">'.$mf->lng['media_dir_refresh'].'</button>
			</div>
			
			<div id="mwms_media_mount_view" title="">
				<div id="mwms_media_mount_view_inner"></div>
				<div class="cleaner"></div>
			</div>
			<div id="mstat"></div>
			<div id="mwms_media_file_view" title="">
				<div id="mwms_media_file_view_inner"></div>
			</div>
		</div>
	';
?>

<script type="text/javascript">
/* <![CDATA[ */
$(document).ready( function()
{
	/* INIT */
	var UID = '<?php echo Registry::get("userdata/id_user"); ?>';
	
	media.UID = UID;
	media.saveDir('<?php echo $mf->rootDir; ?>');
	media.mountView = 'require&file=/mwms/modules/nettv/view/nettv.media.mount.video.view.php';
	
	/* LOAD VIRGIN ROOT -> FIRST TIME :-) */
	var xDir = media.readDir();
	media.openDir(xDir);
});
/* ]]> */
</script>
