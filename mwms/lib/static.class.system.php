<?php
/**
* system.class.php - WEEBO framework lib.
*
* THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
* IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
* FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
* AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
* LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
* OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
* SOFTWARE.
* --
*
* @package System
* @author Jan Prochazka aka prochor <prochor666@gmail.com>
* @copyright 2011 Jan Prochazka aka prochor <prochor666@gmail.com>
* @license http://opensource.org/licenses/mit-license.php MIT License
* @version 1.0 (2011-04-28)
* @link 
*/

class System{

final public function __construct() { throw new WeeboException("Cannot instantiate static class!"); }
final public function __clone() { throw new WeeboException("Cannot clone static class!"); }

public static function log($s, $m){
	Db::query("INSERT INTO "._SQLPREFIX_."_log (tstamp, ident, message) VALUES (".time().", '".Db::escapeField($s)."', '".Db::escapeField($m)."')");
}

public static function rnd($length = 5, $numOnly = false){
	$args = $numOnly === true ? '0123456789': 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
	$str = null;
	while(strlen($str) < $length){
		$str .= mb_substr($args, mt_rand(0, strlen($args) - 1), 1);
	}
	return (string)$str;
}

public static function rndStat($length = 5){
	$args = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
	$str = null;
	while(strlen($str) < $length){
		$str .= mb_substr($args, mt_rand(0, strlen($args) - 1), 1);
	}
	return (string)$str.' '.date(Lng::get('system/date_time_format_precise'), Registry::get('userdata/lasttime'));
}

public static function hash($str){
	return md5($str);
}

/* Include files into system, multidomain support */
public static function lib_include($param, $return_config = null){
	$param = self::root().'/'.$param;
	if(file_exists($param) && ( is_link($param) || is_file($param) ) ){
		require_once(self::fs_path($param));
		return !is_null($return_config) && isset($$return_config) ? $$return_config: false;
	}
}

public static function lib_call($param, $return_config = null){
	$param = self::root().'/'.$param;
	if(file_exists($param) && ( is_link($param) || is_file($param) ) ){
		require(self::fs_path($param));
		return !is_null($return_config) && isset($$return_config) ? $$return_config: false;
	}
}

/* data DUMPer */
public static function dump($var=null){
	echo '<pre>';
	var_dump($var);
	echo '</pre>';
}


public static function chef($a, $k, $d = false)
{
	return is_array($a) && array_key_exists($k, $a) ? $a[$k]: $d;
}

/*
 * FILESYSTEM root path
 * */
 public static function root(){
	$system_root = __MWMS_APP_ROOT__;
	$system_root = mb_substr($system_root, -1, 1, 'UTF-8') === '/' ? mb_substr($system_root, 0, -1, 'UTF-8'): $system_root;
	
	return $system_root;
}

/*
 * FILESYSTEM content path 
 * */
public static function dataDir(){
	return self::root().'/'._GLOBALDATADIR_;
}

/*
 * FILESYSTEM file create
 * */
public static function fsFile($file = null, $content = null){
	$md = self::dataDir().$file;
	
	$rds = explode(_FILESYSTEMSLASH_, $file);
	array_pop($rds);
	$rds = implode(_FILESYSTEMSLASH_, $rds);
	self::fsDir($rds);
	
	$res = file_put_contents($md, $content);
	umask(0000);
	chmod($md, 0777);
	
	return $res;
}

/*
 * FILESYSTEM path 
 * */
public static function getFsFile($file = null){
	$md = self::dataDir().$file;
	return file_exists($md) && !is_dir($md) ? $md: false;
}

/*
 * HTTP path 
 * */
public static function getFileUrl($file = null){
	$md = mb_substr($file, 0, 1) == '/' ? self::dataDir().$file: self::dataDir().'/'.$file;
	$hd = mb_substr($file, 0, 1) == '/' ? self::app_root().'/'._GLOBALDATADIR_.$file: self::app_root().'/'._GLOBALDATADIR_.'/'.$file;
	return file_exists($md) && !is_dir($md) ? $hd: false;
}

/*
 * FILESYSTEM path content
 * */
public static function getFileData($file = null){
	$md = self::dataDir().$file;
	return file_exists($md) && !is_dir($md) ? file_get_contents($md): false;
}

/*
 * FILESYSTEM delete file
 * */
public static function deleteFile($file = null){
	$md = self::dataDir().$file;
	return file_exists($md) && !is_dir($md) ? unlink($md): false;
}

/*
 * FILESYSTEM directory check/create
 * */
public static function fsDir($dir=null){
	$root = self::dataDir();
	
	$dirs = explode(_FILESYSTEMSLASH_, $dir);
	$md = $root;
	foreach($dirs as $d){
		
		$md .= mb_strlen($d)>0 ? '/'.$d: null;
		
		if(!file_exists($md) || !is_dir($md)){
			umask(0000);
			mkdir($md, 0777);
		}
	}
	return $md;
}

/*
 * STAT human readable filesize
 * */
public static function fsFileSize($size) {
 
	$mod = 1024;

	$units = explode(' ','B KB MB GB TB PB');
	for ($i = 0; $size > $mod; $i++) {
		$size /= $mod;
	}

	return round($size, 2) . ' ' . $units[$i];
}

/*
 * HTTP root path 
 * */
public static function app_root(){
	
	$path =  explode(_FILESYSTEMSLASH_, $_SERVER['REQUEST_URI']);
	$protocol = isset($_SERVER['HTTPS']) && !empty($_SERVER['HTTPS']) ? 'https://': 'http://';
	$port = isset($_SERVER["SERVER_PORT"]) && $_SERVER["SERVER_PORT"] != '80' && $_SERVER["SERVER_PORT"] != '443' ? ':'.$_SERVER["SERVER_PORT"]: null;
	$system_root = $protocol.$_SERVER['SERVER_NAME'].$port.dirname($_SERVER['SCRIPT_NAME']);
	$system_root = mb_substr($system_root, -1, 1, 'UTF-8') === '\\' ? mb_substr($system_root, 0, -1, 'UTF-8'): $system_root;
	
	$system_root = mb_substr($system_root, -1, 1, 'UTF-8') === '/' ? mb_substr($system_root, 0, -1, 'UTF-8'): $system_root;
	
	return $system_root;
}

public static function rel(){
	
	$system_root = dirname($_SERVER['SCRIPT_NAME']);
	$system_root = mb_substr($system_root, -1, 1, 'UTF-8') === '\\' ? mb_substr($system_root, 0, -1, 'UTF-8'): $system_root;
	$system_root = mb_substr($system_root, -1, 1, 'UTF-8') === '/' ? mb_substr($system_root, 0, -1, 'UTF-8'): $system_root;
	
	return $system_root;
}

public static function detectDomain(){
	
	$domain = 'www';
	
	if(array_key_exists('SERVER_NAME', $_SERVER)){
		$strs = explode('.', $_SERVER['SERVER_NAME']);
		
		if(count($strs) == 3){
			$domain = $strs[0];
		}
	}
	
	if($domain == 'adm' || $domain == 'dev' || $domain == 'beta' ){
		$domain = 'www';
	}
	
	return $domain;
}

/*
 * Path to array 
 * */
public static function fs_path($path = null){
   return implode(_FILESYSTEMSLASH_, explode('/', $path));
}

/*
 * HTTP root path alias
 * */
public static function path(){
/*
	$path =  explode(_FILESYSTEMSLASH_, $_SERVER['REQUEST_URI']);
	$protocol = isset($_SERVER['HTTPS']) && !empty($_SERVER['HTTPS']) ? 'https://': 'http://';
	$i = array_pop($path);
	$system_root = $protocol.$_SERVER['SERVER_NAME'].implode(_FILESYSTEMSLASH_, $path);
	return $system_root;
*/
return self::app_root();	
}

public static function get_url(){
	$s = empty($_SERVER["HTTPS"]) ? null : ($_SERVER["HTTPS"] == 'on') ? 's' : null;
	$protocol = isset($_SERVER['HTTPS']) && !empty($_SERVER['HTTPS']) ? 'https://': 'http://';
	$port = isset($_SERVER["SERVER_PORT"]) && $_SERVER["SERVER_PORT"] != '80' && $_SERVER["SERVER_PORT"] != '443' ? ':'.$_SERVER["SERVER_PORT"]: null;
	return $protocol.$_SERVER['SERVER_NAME'].$port.$_SERVER['REQUEST_URI'];
}


/* Reedirect, use to prevent resending (forget) POST/GET data */
public static function redirect($path = null){
	$path = !is_null($path) && strlen($path)>0 ? $path: self::get_url();
	header("Location:".$path);
	header("Connection: close"); 
}

/* URL builder */
public static function serial_uri($data=array(), $force_rewrite_off = false){
	$path = null;

	$rewrite = !_WEEBO_REWRITE_ || (defined('_SYSADMINMODE_') && _SYSADMINMODE_) ? false: true;
	$rewrite = $force_rewrite_off ? false: $rewrite;

	if(!$rewrite){
	  $path = '?'.htmlspecialchars(http_build_query($data));
	}else{
	  $path = implode('/', $data)._WEEBO_REWRITE_EXTENSION_;
	}
	return Registry::get('serverdata/site').'/'.$path;
}

/*
 * FILESYSTEM upload file to content directory
 * */
public static function autoUTF($str){
	// detect UTF-8
	if (preg_match('#[\x80-\x{1FF}\x{2000}-\x{3FFF}]#u', $str))
	{
		return $str;
	}elseif(preg_match('#[\x7F-\x9F\xBC]#', $str))
	{
		// detect WINDOWS-1250
		return iconv('WINDOWS-1250', 'UTF-8', $str);
	}
	// assume ISO-8859-2
	return iconv('ISO-8859-2', 'UTF-8', $str);
}


public static function set_lng(){
  return Registry::get( 'lng', _WEEBODEFAULTLNG_ );
}

public static function cookie_domain(){
  if (isset($_SERVER['HTTP_HOST'])) {
	  if(strpos($_SERVER['HTTP_HOST'], ':') != -1){
		  $domain = substr($_SERVER['HTTP_HOST'], 0, strpos($_SERVER['HTTP_HOST'], ':'));
	  }
	  else{
		  $domain = $_SERVER['HTTP_HOST'];
	  }
	  $domain = preg_replace('`^www.`', '', $domain);
	// Per RFC 2109, cookie domains must contain at least one dot other than the
	// first. For hosts such as 'localhost', we don't set a cookie domain.
	if (count(explode('.', $domain)) > 2) {
		ini_set('session.cookie_domain', $domain);
	}
  }
}

public static function extension($filename){
	$f = explode('.', $filename);
	return array_pop($f);
}

public static function fileNameOnly($filename){
	$f = explode('.', $filename);
	$u = array();
	$ext = array_pop($f);
	
	foreach($f as $s){
		$ns = Filter::makeUrlString($s);
		array_push($u, $ns);
	}
	return implode('.', $u);
}

public static function sanitizeFileName($filename){
	$f = explode('.', $filename);
	$ext = array_pop($f);
	$u = array();
	
	foreach($f as $s){
		$ns = Filter::makeUrlString($s);
		array_push($u, $ns);
	}
	return implode('.', $u).'.'.mb_strtolower($ext);
}

public static function load_time(){
	$____time = microtime();
	$____time = explode(" ", $____time);

	return round( ( ($____time[1] + $____time[0]) - __MWMS_LOAD_BEGIN__), 5 );
}

public static function remove_unsafe_tags($input, $validTags = ''){
	$regex = '#\s*<(/?\w+)\s+(?:on\w+\s*=\s*(["\'\s])?.+?\(\1?.+?\1?\);?\1?|style=["\'].+?["\'])\s*>#is';
	return preg_replace($regex, '<${1}>',strip_tags($input, $validTags));
}

public static function module_auto_script($module_id = null){
  
  $module_id = is_null($module_id) ? Registry::get('active_admin_module'): $module_id;
  $module_config = Registry::get('moduledata/'.$module_id);
  
  if(is_array($module_config) && array_key_exists('auto_script', $module_config) && count($module_config['auto_script'])>0){
	foreach($module_config['auto_script'] as $a_script){
		self::lib_call($module_config['module_path'].'/'.$module_id.'/'.$a_script);
	}
  }
}

/* wrapper mbStrings alternative to unserialize */
public static function mb_unserialize($str){
   $res = preg_replace('!s:(\d+):"(.*?)";!se', "'s:'.strlen('$2').':\"$2\";'", $str );
   return unserialize($res);
}

/* load modules info into registry */
public static function modules_init($exclude = array()){

	$directories = array(
		'mwms/modules',
	);

	$lib_config = array();

	foreach($directories as $xdir){

		 $directory =  self::fs_path($xdir);

		 $md = opendir(self::root().'/'.$directory);
		 $html = null;

		  while(false!==($lib_id = readdir($md))){

			  if($lib_id != "." && $lib_id != ".." && is_dir(self::root().'/'.$directory.'/'.$lib_id) && file_exists(self::root().'/'.$directory.'/'.$lib_id.'/module.init.php') && is_file(self::root().'/'.$directory.'/'.$lib_id.'/module.init.php')){
					if(!in_array($lib_id, $exclude)){

						$module_config = self::lib_include($directory.'/'.$lib_id.'/module.init.php', 'mwms_module_init');
						$module_config['module_path'] = $xdir;
						
						$moduleWhiteList = array_key_exists('api_whitelist', $module_config) && is_array($module_config['api_whitelist']) ? $module_config['api_whitelist']: array();
						
						if(array_key_exists('lng_dir', $module_config) && isset($module_config['lng_dir'][0])){
							if(file_exists($directory.'/'.$lib_id.'/'.$module_config['lng_dir'][0].'/'.Registry::get('lng').'.php')){
								Lng::register($lib_id, $directory.'/'.$lib_id.'/'.$module_config['lng_dir'][0].'/'.Registry::get('lng').'.php');
							}else{
								Lng::register($lib_id, $directory.'/'.$lib_id.'/'.$module_config['lng_dir'][0].'/'._WEEBODEFAULTADMINLNG_.'.php');
							}
						}

						self::module_lib_autoload($directory.'/'.$lib_id, $module_config);
						$lib_config[$lib_id] = $module_config;
						
						$whiteList = Registry::get('api_whitelist');
						$newWhiteList = array_merge($whiteList, $moduleWhiteList);
						$newWhiteList = array_unique($newWhiteList);
						Registry::set('api_whitelist', $newWhiteList);
					}

			  }
			 
		  }
		  closedir($md);

	}

	ksort($lib_config);
	Registry::set('moduledata', $lib_config);
	unset($lib_config);

}



/* load module media files into template */
public static function load_module_media($type = 'css', $module_id = null){
  
  $module_id = is_null($module_id) ? Registry::get('active_admin_module'): $module_id;
  $module_config = Registry::get('moduledata/'.$module_id);
  $media = null;
  
	switch($type){
	
	case "css":
	  if(is_array($module_config) && array_key_exists('css', $module_config) && count($module_config['css'])>0){
		foreach($module_config['css'] as $css_script){
			$media .= '<link rel="stylesheet" href="'.Registry::get('serverdata/path').'/'.$module_config['module_path'].'/'.$module_id.'/'.$css_script.'" media="all" type="text/css" />';
		}
	  }
	
	break; case "js": 
	  if(is_array($module_config) && array_key_exists('js', $module_config) && count($module_config['js'])>0){
		foreach($module_config['js'] as $js_script){
			$media .= '<script type="text/javascript" src="'.Registry::get('serverdata/path').'/'.$module_config['module_path'].'/'.$module_id.'/'.$js_script.'"></script>';
		}
	  }
	
	break; default:
	
	
	}
			
	return $media; 
}

/* System libs loader */
public static function lib_autoload($exclude = array()){

	$directories = array(
	'mwms/lib'
	);

	foreach($directories as $xdir){
		
		$dirs = self::lib_find($xdir);
		
		foreach($dirs as $dir){
			self::lib_include($dir);
		}	
				  
	}
}

/* load module libs */
public static function module_lib_autoload($module_path, $config = array()){

	$directories = array_key_exists('lib_dir', $config) && is_array($config['lib_dir']) ? $config['lib_dir']: array();
	foreach($directories as $xdir){

	$dirs = self::lib_find($module_path.'/'.$xdir);

	foreach($dirs as $dir){
		self::lib_call($dir);
	}

  }
 
}

public static function lib_find($xdir, $exclude = array()){

	$directory =  self::fs_path($xdir);
	
	$md = opendir(self::root().'/'.$directory);
	$fl = array();
	
	while(false!==($lib_id = readdir($md))){
			
		if($lib_id != "." && $lib_id != ".." && !is_dir(self::root().'/'.$directory.'/'.$lib_id) && is_file(self::root().'/'.$directory.'/'.$lib_id)){
			
			if( ( self::extension($lib_id)=='php' || self::extension($lib_id)=='inc' ) && !in_array($lib_id, $exclude)){
				array_push($fl, $directory.'/'.$lib_id);
			}
		}

	}
	
	closedir($md);
	
	sort($fl);
	
	return $fl;
}

/* CLIENT IP beta */
public static function getClientIp() {
	
	$clientVars = array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR');

	foreach($clientVars as $key) {
		if(array_key_exists($key, $_SERVER) === true)
		{
			foreach(explode(',', $_SERVER[$key]) as $ip)
			{
				if (filter_var($ip, FILTER_VALIDATE_IP) !== false)
				{
					return $ip;
				}
			}
		}
	}
}

/* BROWSER DETECTION */

/**
 * Get browser
 *
 * @return array of browserData
 */
public static function getBrowser($asArray = true) {
	return get_browser(null, $asArray);
}


}
?>
