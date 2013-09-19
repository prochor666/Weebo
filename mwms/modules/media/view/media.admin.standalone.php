<?php
if(Registry::get('userdata/logged_in') == 1){

$Gui = new Gui; 
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="cs" lang="cs">
<!--
__    ______    _______ _______ _______ _______
\ \   \    /   / /_____|  _____|  __   |  ___  |
 \ \   \  /   / /|_____| |_____| |__| _| |   | |
  \ \  / /\  / /  _____| ______|  __ |_| |   | |
   \ \/ /\ \/ /| |_____| |_____| |__|  | |___| |
    \__/  \__/ |_______|_______|_______|_______|
     easy web tool, media module static template for tinyMCE editor
-->
<head>
<!-- LANG -->
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="Content-Language" content="<?php echo Registry::get('lng'); ?>" />
<!-- /LANG -->

<!-- CACHE -->
<meta http-equiv='Cache-Control' content='must-revalidate, post-check=0, pre-check=0' />
<meta http-equiv='Cache-Control' content='no-cache' />
<meta http-equiv='Pragma' content='no-cache' />
<meta http-equiv='Expires' content='-1' />
<meta name="revisit-after" content="1" />
<!-- /CACHE -->

<!-- TOP -->
<base target="_parent" />
<!-- /TOP -->

<!-- SEO -->
<title><?php echo Lng::get('media/mwms_content_file_load') ?></title>
<meta name="description" content="<?php echo _PRODUCTNAME_.' | '._PRODUCTVERSION_.' | '._PRODUCTCODENAME_; ?>" />
<!-- /SEO -->

<!-- ROBOTS -->
<meta name="robots" content="noindex,nofollow" />
<meta name='googlebot' content='noindex,nofollow' />
<meta name="rating" content="general" />
<!-- /ROBOTS -->

<!-- COPYRIGHT -->
<meta name="copyright" content="Copyright (c) prochor666@gmail.com" />
<meta name="author" content="Jan Prochazka" />
<!-- /COPYRIGHT -->

<!-- CSS -->
<?php
	$cb = new ScriptBundle;
	$____path = null; 
	$cb->scripts = array(
		$____path."/shared/nx.ui/jquery-ui.css" => true,
		$____path."/admin/default/css/weebo-official/main.css" => true,
		$____path."/admin/default/css/weebo-official/dashboard.css" => true,
		$____path."/admin/default/css/weebo-official/interactive.css" => true,
	);
	
	$cb->finalCSSScript = 'weebo.media.module.css.bundle.css';
	
	$cb->keepalive = 86400;
	$cb->apply = true;
	echo $cb->bundleCss();
?>

<?php echo System::load_module_media('css', 'media'); ?>

<!-- /CSS -->

<!-- JAVASCRIPT -->

<?php
	$jb = new ScriptBundle;
	$jb->scripts = array(
		$____path."/shared/jquery.min.js" => false,
		$____path."/shared/nx.ui/jquery-ui.min.js" => false,
		$____path."/admin/default/js/weebo.tools.js" => true,
		$____path."/admin/default/js/weebo.core.js" => true,
		$____path."/admin/default/js/weebo.meta.js" => true,
	);
	
	$jb->finalJsScript = 'weebo.media.module.script.bundle.js';
	
	$jb->keepalive = 86400;
	$jb->apply = true;
	echo $jb->bundleJs();

	echo System::load_module_media('js', 'media'); 
?>

<script src="<?php echo Registry::get('serverdata/site'); ?>/shared/tinymce/tiny_mce_popup.js" type="text/javascript"></script>

<script type="text/javascript">
/* <![CDATA[ */
	weebo.options = {
		TemplateLogin: <?php echo (int)Registry::get('userdata/logged_in'); ?>,
		AjaxCall: '<?php echo Ajax::path(); ?>',
		ActiveModule: '<?php echo Registry::get('active_admin_module'); ?>',
		SiteRoot: '<?php echo Registry::get('serverdata/site'); ?>',
		ClientTimeFormat: '<?php echo Lng::get('system/date_time_format_precise'); ?>',
		WeeboPreloader: "<?php echo Registry::get('serverdata/path'); ?>/admin/default/img/loading.gif",
		AjaxTimeout: <?php echo _WEEBOAJAXREQUESTTIMEOUT_; ?>, //timeout in ms 
		ShellState:  '<?php echo Registry::get('shell_state'); ?>'
	}
	
/* ]]> */
</script>
<!-- /JAVASCRIPT -->
</head>
<body>
<?php
Registry::set('mediatree', array());

$mf = new MediaDisplay;

$mf->rootDir = _GLOBALDATADIR_;
//$mf->rootDir = _GLOBALDATADIR_;

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
	
//System::dump($_GET);

$fileMaskContainer = isset($_GET['type']) ? $_GET['type']: 'file';
$fileFieldName = isset($_GET['field_name']) ? $_GET['field_name']: null;

if($fileFieldName == 'video_poster'){
	$fileMaskContainer = 'image';
}

switch($fileMaskContainer){
	case 'image':
		$fileMask = 'jpg,png,gif';
	break; case 'media':
		$fileMask = 'm4v,webm,mp4,flv,swf,ogg,mp3,ogv';
	break; case 'file':
		$fileMask = '*';
	break; default:
		$fileMask = '*';
}
?>

<script type="text/javascript">
/* <![CDATA[ */
$(document).ready( function()
{
	$('body, html').css({
		'background-color': '#ffffff',
	});
	
	var fileMask = '<?php echo $fileMask; ?>';
	
	var win = tinyMCEPopup.getWindowArg("window");
	var input = tinyMCEPopup.getWindowArg("input");
	var res = tinyMCEPopup.getWindowArg("resizable");
	var inline = tinyMCEPopup.getWindowArg("inline");
	
	var FileBrowserDialogue = {
	init : function () {
		// Here goes your code for setting your custom things onLoad.
	},
	mySubmit : function (xurl) {
			
			var URL = xurl;
			var win = tinyMCEPopup.getWindowArg("window");

			// insert information now
			win.document.getElementById(tinyMCEPopup.getWindowArg("input")).value = URL;

			// are we an image browser
			if (typeof(win.ImageDialog) != "undefined") {
				// we are, so update image dimensions...
				if (win.ImageDialog.getImageData){
					win.ImageDialog.getImageData();
				}
				
				// ... and preview if necessary
				if (win.ImageDialog.showPreviewImage){
					win.ImageDialog.showPreviewImage(URL);
				}
			}

			// close popup window
			tinyMCEPopup.close();

		}
	}

	//alert();

	tinyMCEPopup.onInit.add(FileBrowserDialogue.init, FileBrowserDialogue);
	
	//alert("Input: " + input + " nRES: " + res + " nInline: " + inline + "nWin: " + win); // debug/testing
	
	/* INIT */
	var UID = '<?php echo Registry::get("userdata/id_user"); ?>';
	
	media.UID = UID;
	media.saveDir('<?php echo $mf->rootDir; ?>');
	media.mountView = 'require&file=/mwms/modules/media/view/media.client.mount.view.php&fileMask='+fileMask;
	
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
		FileBrowserDialogue.mySubmit(xFile);
		return false;
	});
	
	
	/* HANDLE FILE DELETE CLICK */
	$('a.remove-file').remove();

	/* HANDLE DIR DELETE CLICK */
	$('a.remove-dir').remove();
	
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
		media.uploadFiles(fileMask);
		return false;
	});
});
/* ]]> */
</script>
	
	<div id="uploader-panel"></div><div id="uploader-box-wrapper"></div>

	<div id="weebo-modal-dialog-content"></div>
	<?php //System::dump($_GET); ?>
</body>
</html>

<?php } ?>
