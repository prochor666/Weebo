<?php $Gui = new Gui; 

$_mid = Registry::get('active_admin_module');

$module_name = $_mid == 'mwms' ? null: Lng::get($_mid.'/mwms_module_name').' | ';
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
     easy web tool
-->
<head>
<!-- LANG -->
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="Content-Language" content="<?php echo Registry::get('lng'); ?>" />
<!-- /LANG -->

<!-- CACHE -->
<meta http-equiv='Cache-Control' content='private, must-revalidate, post-check=0, pre-check=0' />
<meta http-equiv='Cache-Control' content='no-cache' />
<meta http-equiv='Pragma' content='no-cache' />
<meta http-equiv='Expires' content='-1' />
<meta name="revisit-after" content="1" />
<!-- /CACHE -->

<!-- TOP -->
<base target="_parent" />
<!-- /TOP -->

<!-- SEO -->
<title><?php echo $module_name._PRODUCTNAME_.' | '._PRODUCTVERSION_; ?></title>
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

<!-- FAVICON -->
<link rel="icon" href="<?php echo Registry::get('serverdata/path'); ?>/admin/default/img/favicon.ico" type="image/x-icon" />
<link rel="shortcut icon" href="<?php echo Registry::get('serverdata/path'); ?>/admin/default/img/favicon.ico" type="image/x-icon" />
<!-- /FAVICON -->

<!-- CSS -->
<?php
	$cb = new ScriptBundle;
	$____path = null; 
	$cb->scripts = array(
		$____path."/shared/nx.ui/jquery-ui.css" => true,
		//$____path."/shared/jquery.ui.selectmenu/jquery.ui.selectmenu.css" => true,
		$____path."/shared/jquery.ui.datetime/jquery-ui-timepicker-addon.css" => true,
		$____path."/admin/default/css/weebo-official/main.css" => true,
		$____path."/admin/default/css/weebo-official/dashboard.css" => true,
		$____path."/admin/default/css/weebo-official/interactive.css" => true,
		$____path."/admin/default/css/weebo-official/pager.css" => true,
	);
	
	$cb->finalCSSScript = 'weebo.gui.css.bundle.css';
	
	$cb->keepalive = 86400;
	$cb->apply = true;
	echo $cb->bundleCss();
?>

<?php if(Registry::get('userdata/logged_in') == 1 && Registry::get('userdata/admin') == 1){ echo System::load_module_media('css'); } ?>

<!-- /CSS -->

<!-- JAVASCRIPT -->

<?php
	$jb = new ScriptBundle;
	$jb->scripts = array(
		$____path."/shared/jquery.min.js" => false,
		//$____path."/shared/jquery-migrate-1.1.1.js" => false,
		$____path."/shared/nx.ui/jquery-ui.min.js" => false,
		$____path."/shared/jquery.ui.touch-punch.min.js" => false,
		$____path."/shared/jquery.ui.datetime/jquery-ui-timepicker-addon.js" => true,
		$____path."/shared/jquery.ui.slideaccess/jquery-ui-sliderAccess.js" => true,
		//$____path."/shared/jquery.ui.selectmenu/jquery.ui.selectmenu.js" => true,
		$____path."/shared/jquery.cookie/jquery.cookie.js" => true,
		$____path."/shared/jquery.rightclick/jquery.rightclick.js" => true,
		$____path."/admin/default/js/weebo.tools.js" => true,
		$____path."/admin/default/js/weebo.core.js" => true,
		$____path."/admin/default/js/weebo.meta.js" => true,
		$____path."/admin/default/js/weebo.init.js" => true,
	);
	
	$jb->finalJsScript = 'weebo.gui.script.bundle.js';
	
	$jb->keepalive = 86400;
	$jb->apply = true;
	echo $jb->bundleJs();
?>

<?php if(Registry::get('userdata/logged_in') == 1 && Registry::get('userdata/admin') == 1){ echo System::load_module_media('js'); } ?>

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
		ShellState:  '<?php echo Registry::get('shell_state'); ?>',
		systemUser: '<?php echo Registry::get('userdata/username'); ?>',
		shellReuse: '<?php echo Lng::get('system/shell_use_command_again'); ?>',
		systemMachine: '<?php echo Registry::get('serverdata/site'); ?>',
		systemSaveButton : '<?php echo Lng::get('system/mwms_save'); ?>',
		systemFieldValidationError : '<?php echo Lng::get('system/mwms_field_set_error'); ?>',
	}
	
	weebo.init();
/* ]]> */
</script>

<!-- /JAVASCRIPT -->
</head>
<body>

<?php $wcss = Registry::get('userdata/logged_in') == 1 && Registry::get('userdata/admin') == 1 ? 'logged-in': 'not-logged'; ?>
<div id="mwms_main" class="<?php echo $wcss; ?>">

<?php if(Registry::get('userdata/logged_in') == 1 && Registry::get('userdata/admin') == 1){ ?>
	<div id="mwms_header">
		
		<div class="mwms_logged_box">
			<div id="mwms_logo"></div>
			<?php echo $Gui->load_header(); ?>
		</div>
		<div id="mwms_drop_indicator"></div>
		
		<div id="mwms_dashboard">
			<?php echo $Gui->load_dashboard(); ?>
		</div>

		<div class="cleaner"></div>
	
	</div>
<?php } ?>
	<div id="mwms_inner">
		<?php echo $Gui->load_workspace(); ?>
	</div>
	<div id="mwms_inner_load" class="js-false"><div class="dialog_loader"></div></div>

</div>
<div id="mwms_footer">
	
</div>

<?php if(Registry::get('userdata/logged_in') === 1 && Registry::get('userdata/admin') == 1 && Registry::get('userdata/root') == 1){ echo Shell::show(); } ?>

<div id="weebo-modal-dialog-content"></div>
<div id="weebo-kli"></div>

<?php /*System::dump(Registry::readall());*/ ?>
<?php /*System::dump(html_entity_decode(Registry::get('active_admin_module')));*/ ?>
</body>
</html>
