<?php
if(function_exists("ini_set")){
	ini_set('session.cookie_domain',System::cookie_domain());

}elseif(function_exists("ini_alter") && !function_exists("ini_set")){
	ini_alter('session.cookie_domain',System::cookie_domain());
}

session_start();

// INCLUDE REMAINING CORE FUNCTIONS & CLASSES, EXCLUDE STARTUP LIBS
System::lib_autoload(array(
	"static.class.system.php",
	"static.class.db.php",
	"class.cache.php",
	"class.mysql.php",
	"class.postgresql.php",
	"class.weebo.install.php",
));

Registry::init();

Registry::set('lng', System::set_lng());

Registry::set( 'serverdata',
		array(
			'path' => System::path(),
			'site' => System::app_root(),
			'root' => System::root(),
			'rel' => System::rel()
		)
);

if(defined('_MEMCACHEENABLED_') && _MEMCACHEENABLED_ === true){
	$___m = new Mem;
	$___m->memTest();
	define('_MEMCACHESERVEROK_', $___m->connection);
}else{
	define('_MEMCACHESERVEROK_', false);
}

setlocale(LC_ALL, Lng::get('system_locale'));

Registry::set( 'moduledata', array() );
Registry::set( 'output_scripts', array() );

$___auth = new Auth;

if((isset($_POST['logout']) && $_POST['logout']==1) || (isset($_GET['logout']) && $_GET['logout']==1)){

	$___auth->logout();
	System::redirect( System::app_root().'/' );
	exit();
	
}else{
	
	$___authState = $___auth->first_login();
	
	if($___authState === true)
	{
		if(array_key_exists('module', $_GET)){
			System::redirect( System::app_root().'/?module=mwms&autorun=1');
			//exit();
		}
		
		if(array_key_exists('weebo_redirect', $_GET)){
			System::redirect( $_GET['weebo_redirect'] );
			//exit();
		}
	}
	
	if(Registry::get('userdata/logged_in') !== 1){
		
		if(_APILEVEL_ == 2){
			Registry::set('lng', _WEEBODEFAULTLNG_);
			setlocale(LC_ALL, Lng::get('system_locale'));
		}else{
			Registry::set('lng', _WEEBODEFAULTADMINLNG_);
			setlocale(LC_ALL, Lng::get('system_locale'));
		}
		
	}else{
		
		if(_APILEVEL_ == 2){
			Registry::set('lng', _WEEBODEFAULTLNG_);
			setlocale(LC_ALL, Lng::get('system_locale'));
		}else{
			Registry::set('lng', Registry::get('userdata/lng'));
			setlocale(LC_ALL, Lng::get('system_locale'));
		}
		
	}

}
?>
