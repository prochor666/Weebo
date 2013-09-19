<?php
define('__INITMEMTOP__', memory_get_usage(true));
define('__INITMEMPEAKTOP__', memory_get_peak_usage(true));

$____time = microtime();
$____time = explode(" ", $____time);
define('__MWMS_LOAD_BEGIN__', $____time[1] + $____time[0]);
define('__MWMS_APP_ROOT__', dirname(__FILE__));

error_reporting(E_ALL);

// MAIN CONFIGURATION
if(file_exists("./mwms/init/config.php")){ require_once "./mwms/init/config.php"; } // system variables
if(file_exists("./mwms/init/mail.config.php")){ require_once "./mwms/init/mail.config.php"; } // system variables

if(function_exists('session_save_path') && file_exists('userdata') && is_dir('userdata')){ session_save_path('userdata'); }

if(function_exists("ini_set")){
	ini_set('display_errors',1); 
	ini_set('session.gc_maxlifetime',_AUTOLOGOUT_); 
	ini_set('session.cache_expire',(_AUTOLOGOUT_/60));	
	ini_set('session.cookie_lifetime',_AUTOLOGOUT_);
	ini_set('session.gc_probability',30);
	ini_set('session.gc_divisor',1);

}elseif(function_exists("ini_alter") && !function_exists("ini_set")){
	ini_alter('display_errors',1); 
	ini_alter('session.gc_maxlifetime',_AUTOLOGOUT_); 
	ini_alter('session.cache_expire',(_AUTOLOGOUT_/60));
	ini_alter('session.cookie_lifetime',_AUTOLOGOUT_);
	ini_alter('session.gc_probability',30);
	ini_alter('session.gc_divisor',1);

}else{
	die('PHP configuration error');
}

mb_internal_encoding('UTF-8');

if( isset($_GET['weeboapi']) && file_exists("mwms/live/live.php") ){
	define('_APILEVEL_', 0);
}elseif( isset($_GET['module']) && file_exists("mwms/live/main.php") ){
	define('_APILEVEL_', 1);
}elseif( file_exists("mwms/live/public.php") ){
	define('_APILEVEL_', 2);
}else{
	define('_APILEVEL_', -1);
}

// RESTRICTIVE INCLUDE CORE FUNCTIONS & CLASSES
if(file_exists("./mwms/lib/static.class.system.php")){ require_once "./mwms/lib/static.class.system.php"; } // system core
if(file_exists("./mwms/lib/static.class.storage.php")){ require_once "./mwms/lib/static.class.storage.php"; } // system core
if(file_exists("./mwms/lib/static.class.db.php")){ require_once "./mwms/lib/static.class.db.php"; } // system db wrapper
if(file_exists("./mwms/lib/class.cache.php")){ require_once "./mwms/lib/class.cache.php"; } // system cache ops
if(file_exists("./mwms/lib/class.mysql.php")){ require_once "./mwms/lib/class.mysql.php"; } // mysql class
if(file_exists("./mwms/lib/class.postgresql.php")){ require_once "./mwms/lib/class.postgresql.php"; } // postgresql class
if(file_exists("./mwms/lib/class.weebo.install.php")){ require_once "./mwms/lib/class.weebo.install.php"; } // installer

$____i = new WeeboInstall;
$____i->run();

define('_WEEBOISOK_', $____i->coreInstalled);

if(_WEEBOISOK_ === true)
{
	if(file_exists("./mwms/init/systeminit.php")){ require_once "./mwms/init/systeminit.php"; } // system init

	if(!defined('_CMS_DOMAIN_')){
		define('_CMS_DOMAIN_', System::detectDomain());
	}

	if( isset($_GET['weeboapi']) && file_exists("mwms/live/live.php")){
		System::lib_include("mwms/live/live.php");
	}elseif( isset($_GET['module']) && file_exists("mwms/live/main.php")){
		System::lib_include("mwms/live/main.php");
	}elseif( file_exists("mwms/live/public.php")){
		System::lib_include("mwms/live/public.php");
	}else{
		echo 'CORE INIT ERROR, /LIVE SCRIPT MISSING';
		Registry::reset();
		session_unset();
	}
	
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
<h1>CORE INSTALL ERRORS</h1>'.$____i->displayErrors().'</body></html>';
	session_unset();
}
?>
