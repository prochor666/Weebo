<?php
define('_SYSADMINMODE_', false );

Registry::set('api_whitelist', array(

));

if(isset($_GET['weebo_preview']) && $_GET['weebo_preview'] == 1){
	define('_WEEBO_PREVIEW_', true);
}else{
	define('_WEEBO_PREVIEW_', false);
}

Lng::register();
System::modules_init();
//Lng::publicContent();
ob_start();

System::module_auto_script('cms');

define('__MEMTOP__', memory_get_usage(true));
define('__MEMPEAKTOP__', memory_get_peak_usage(true));

/* NO ADMIN GUI OUTPUT */
System::lib_include('html_build.php');

$weebo_html = ob_get_contents();

ob_end_clean();

echo $weebo_html;
?>
