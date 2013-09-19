<?php
define('_SYSADMINMODE_', isset($_GET['weeboadmin']) || ( isset($_GET['sysadmin']) && $_GET['sysadmin'] == Registry::get('userdata/id') ) ? true: false );

Registry::set('api_whitelist', array(
	'Gui::load_workspace', 'Login::save_dashboard_config', 'Gui::save_system_lng'
));

Lng::register();
System::modules_init();
Lng::publicContent();
ob_start();

$___mAllow = Registry::get('api_whitelist');

header("Expires: " . gmdate("D, d M Y H:i:s", (time() - 3600) ) . " GMT");
header("Cache-Control: no-cache, must-revalidate"); 
header("Content-Type: text/html; charset=UTF-8");

if(Registry::get('userdata/logged_in') == 1){
	
	$___request_method = isset($_GET['weeboapi']) ? $_GET['weeboapi']: null;

	switch($___request_method){
		case 'require':
			$___request_addr = isset($_GET['file']) ? $_GET['file']: null;
			Ajax::call_required($___request_addr);

		break; case 'translation':
			$___request_part = isset($_GET['part']) ? $_GET['part']: null;
			echo Lng::get($___request_part);
			
		break; case 'registry':
			$___request_part = isset($_GET['part']) ? $_GET['part']: null;
			echo Registry::get($___request_part);

		/* Whitelisted methods and classes */
		break; case 'method':
			
			if(isset($_GET['fn']) && in_array($_GET['fn'], $___mAllow) ){

				$___request_method = $_GET['fn'];
				$___request_params = isset($_GET['qs']) ? explode("|", $_GET['qs']): array();
				$___request_method = explode("::", $___request_method);
				$___class = new $___request_method[0];
				echo call_user_func_array(array($___class, $___request_method[1]), $___request_params);

			}else{
				echo '<div class="mwms-error">LOCAL CONTEXT: '.Lng::get('system/mwms_ajax_unknown_request').'</div>';
			}

		/* Whitelisted static methods and classes */
		break; case 'static-method': case 'alias':

			if(isset($_GET['fn']) && in_array($_GET['fn'], $___mAllow) ){

				$___request_method = $_GET['fn'];
				$___request_params = isset($_GET['qs']) ? explode("|", $_GET['qs']): array();
				$___request_method = explode("::", $___request_method);
				echo call_user_func_array(array($___request_method[0] ,$___request_method[1]), $___request_params);

			}else{
				echo '<div class="mwms-error">LOCAL CONTEXT: '.Lng::get('system/mwms_ajax_unknown_request').'</div>';
			}

		break; case 'shell':

			$___shell_command = isset($_POST['___command']) ? $_POST['___command']: null;
			$___shell_params = isset($_POST['___params']) ? $_POST['___params']: null;
			echo Shell::com($___shell_command, $___shell_params);

		break; case 'status':
			echo System::rnd(128);

		break; default:
			echo '<div class="mwms-error">>LOCAL CONTEXT: '.Lng::get('system/mwms_ajax_unknown_request').'</div>';

	}

}else{
	/* WORLD API */
	$___request_method = isset($_GET['weeboapi']) ? $_GET['weeboapi']: null;
	
	//System::dump($___mAllow);
	
	switch($___request_method){
		case 'require': case 'registry': case 'function': case 'shell':
			echo '<div class="mwms-error">WORLD CONTEXT: '.Lng::get('system/mwms_ajax_unknown_request').'</div>';

		break; case 'translation':
			$___request_part = isset($_GET['part']) ? $_GET['part']: null;
			echo Lng::get($___request_part);

		/* Whitelisted methods and classes */
		break; case 'method':
			
			if(isset($_GET['fn']) && in_array($_GET['fn'], $___mAllow) ){

				$___request_method = $_GET['fn'];
				$___request_params = isset($_GET['qs']) ? explode("|", $_GET['qs']): array();
				$___request_method = explode("::", $___request_method);
				$___class = new $___request_method[0];
				echo call_user_func_array(array($___class, $___request_method[1]), $___request_params);

			}else{
				echo '<div class="mwms-error">WORLD CONTEXT: '.Lng::get('system/mwms_ajax_unknown_request').'</div>';
			}

		/* Whitelisted static methods and classes */
		break; case 'static-method': case 'alias':

			if(isset($_GET['fn']) && in_array($_GET['fn'], $___mAllow) ){

				$___request_method = $_GET['fn'];
				$___request_params = isset($_GET['qs']) ? explode("|", $_GET['qs']): array();
				$___request_method = explode("::", $___request_method);
				echo call_user_func_array(array($___request_method[0] ,$___request_method[1]), $___request_params);

			}else{
				echo '<div class="mwms-error">WORLD CONTEXT: '.Lng::get('system/mwms_ajax_unknown_request').'</div>';
			}
		
		break; case 'status':
			echo '<script type="text/javascript">if(weebo.settings.TemplateLogin == 1){ document.location = "?module=mwms"; }</script>';

		break; default:
			echo '<div class="mwms-error">WORLD CONTEXT: '.Lng::get('system/mwms_ajax_unknown_request').' '.$_GET['file'].'</div>';
	}
	
}

$weebo_html = ob_get_contents();

define('__MEMTOP__', memory_get_usage(true));
define('__MEMPEAKTOP__', memory_get_peak_usage(true));

ob_end_clean();
echo $weebo_html;
?>
