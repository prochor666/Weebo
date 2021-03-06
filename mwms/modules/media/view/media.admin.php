<?php
Registry::set('mediatree', array());

$mf = new MediaDisplay;

$mf->rootDir = _GLOBALDATADIR_;

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
mediaLng.mediaDirUpload = '<?php echo Lng::get('media/media_dir_upload'); ?>';
	
/* <![CDATA[ */
$(document).ready( function()
{
	/* INIT */
	var UID = '<?php echo Registry::get("userdata/id_user"); ?>';
	var extensionFilter = '<?php echo implode(",", $mf->getAllFiles()); ?>';
	
	media.UID = UID;
	media.saveDir('<?php echo $mf->rootDir; ?>');

	/* LOAD VIRGIN ROOT -> FIRST TIME :-) */
	var xDir = media.readDir();
	media.openDir(xDir);

	/* UNHANDLE ALL PREVIOUS */
	$(document).off('click', 'a.dir');
	$(document).off('click', 'a.file');
	$(document).off('click', 'a.remove-file');
	$(document).off('click', 'a.remove-dir');
	$(document).off('click', '#new-dir');
	$(document).off('click', '#refresh');
	$(document).off('click', '#uploader');

	/* HANDLE DIR CLICK */
	$(document).on('click', 'a.dir', function()
	{
		var xDir = $(this).attr('href');
		media.openDir(xDir);
		return false;
	});
	
	/* HANDLE FILE CLICK */
	$(document).on('click', 'a.file', function()
	{
		var xFile = $(this).attr('href');
		media.openFile(xFile);
		return false;
	});

	/* HANDLE FILE DELETE CLICK */
	$('a.remove-file').button({
		icons: {
			primary: 'ui-icon-circle-close'
		},
		text: false
	})
	
	$(document).on('click', 'a.remove-file', function()
	{
		var xTitle = $(this).parent().find('a.file').text();
		var c = confirm('<?php echo $mf->lng['media_file_delete_confirm']; ?> ' + xTitle + '?');
		if(c){
			var xFile = $(this).attr('href');
			media.deleteFiles(xFile);
			$(this).parent().remove();
		}
		return false;
	});

	/* HANDLE DIR DELETE CLICK */
	$('a.remove-dir').button({
		icons: {
			primary: 'ui-icon-circle-close'
		},
		text: false
	})
	
	$(document).on('click', 'a.remove-dir', function()
	{
		var xTitle = $(this).parent().find('a.dir').text();
		var c = confirm('<?php echo $mf->lng['media_dir_delete_confirm']; ?> ' + xTitle + '?');
		if(c){
			var xFile = $(this).attr('href');
			media.deleteDirs(xFile);
			$(this).parent().remove();
		}
		return false;
	});
	
	/* HANDLE DIR MAKE */
	$('#new-dir').button({
		icons: {
			primary: 'ui-icon-folder-open'
		}
	
	});
	
	$(document).on('click', '#new-dir', function()
	{
		media.makeDir();
		return false;
	});
	
	/* HANDLE DIR REFRESH */
	$('#refresh').button({
		icons: {
			primary: 'ui-icon-refresh'
		},
		text: false
	})
	
	$(document).on('click', '#refresh', function()
	{
		var xDir = media.readDir();
		media.openDir(xDir);
		return false;
	});
	
	/* HANDLE UPLOAD BUTTON */
	$('#uploader').button({
		icons: {
			 primary: 'ui-icon-arrowthickstop-1-s'
		} 
	})
	
	$(document).on('click', '#uploader', function()
	{
		setTimeout('media.uploadFiles("'+extensionFilter+'")', 70);
		return false;
	});
});
/* ]]> */
</script>
