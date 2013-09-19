<?php
header("Expires: " . gmdate("D, d M Y H:i:s", (time() + 1800) ) . " GMT");
header("Cache-Control: private"); 

define('_SYSADMINMODE_', true );

Registry::set('api_whitelist', array(
	'Gui::load_workspace', 'Login::save_dashboard_config', 'Gui::save_system_lng'
));

Registry::set('mwms_module_path', array() );

Lng::register();
System::modules_init();

Lng::publicContent();

ob_start();

/* RUN ADMIN HERE */
Registry::set('active_admin_module', $_GET['module']);

$____i = new WeeboInstall;
$____i->onInit = false;
$____i->run();

if( $____i->installed === true )
{
	System::module_auto_script();
}else{
	echo '<!DOCTYPE html"><html>
<meta http-equiv="Cache-Control" content="private, must-revalidate, post-check=0, pre-check=0" />
<meta http-equiv="Cache-Control" content="no-cache" />
<meta http-equiv="Pragma" content="no-cache" />
<meta http-equiv="Expires" content="-1" />
<meta name="revisit-after" content="1" />
<title>'._PRODUCTNAME_.' | '._PRODUCTVERSION_.'</title>
</head>
<body>
<h1>MODULE INSTALL ERRORS</h1>'.$____i->displayErrors().'</body></html>';
}

define('__MEMTOP__', memory_get_usage(true));
define('__MEMPEAKTOP__', memory_get_peak_usage(true));

System::lib_include('admin/default/index.php');

if( ( isset($_GET['autorun']) && $_GET['autorun'] == 1 ) ){
	$_module = Registry::get('userdata/autorun');
	System::redirect(Registry::get('serverdata/path').'/?module='.$_module);
}
/* END ADMIN */

$weebo_html = ob_get_contents();

ob_end_clean();

echo $weebo_html;
?>
