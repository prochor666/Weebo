<?php
/**
* static.class.storage.php - WEEBO framework lib.
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
* @package Storage
* @author Jan Prochazka aka prochor <prochor666@gmail.com>
* @copyright 2011 Jan Prochazka aka prochor <prochor666@gmail.com>
* @license http://opensource.org/licenses/mit-license.php MIT License
* @version 1.0 (2011-04-28)
* @link 
*/

class Storage{

final public function __construct() { throw new WeeboException("Cannot instantiate static class!"); }
final public function __clone() { throw new WeeboException("Cannot clone static class!"); }

public static function cache($file, $data, $keepalive = 86400){
	$c = new Cache;
	$c->sourceFilename = $file;
	$c->data = $data;
	$c->keepalive = (int)$keepalive;
	return $c->compare();
}

public static function isExpired($file, $keepalive = 86400){
	$c = new Cache;
	$c->sourceFilename = $file;
	$c->keepalive = (int)$keepalive;
	return $c->isExpired();
}

public static function cacheRewrite($file, $data){
	$c = new Cache;
	$c->sourceFilename = $file;
	$c->data = $data;
	$c->keepalive = 0;
	return $c->compare();
}

public static function cacheRead($file){
	$c = new Cache;
	$c->cacheFilename = $file;
	return $c->cacheDirectRead();
}

/* operations */
public static function copyFile($from, $to){
	$r = System::root();
	$from = $r.'/'.$from;
	$to = $r.'/'.$to;
	if(file_exists($from) && is_file($from)){
		if(function_exists('copy')){
			copy($from, $to);
		}else{
			$emz = file_get_contents($from);
			file_put_contents($to, $emz);
		}
		
		umask(0000);
		chmod($to, 0777);
	}
}

public static function moveFile($from, $to){
	$r = System::root();
	$from = $r.'/'.$from;
	$to = $r.'/'.$to;
	if(file_exists($from) && is_file($from)){
		rename($from, $to);
	}
}

public static function deleteFile($from){
	$r = System::root();
	$from = $r.'/'.$from;
	if(file_exists($from) && is_file($from)){
		unlink($from);
	}
}

public static function copyDir($from, $to){
	$r = System::root();
	$from = $r.'/'.$from;
	$to = $r.'/'.$to;
	
	if(file_exists($from) && is_dir($from)){
		$dir = opendir($from);
		@mkdir($to);
		while(false !== ( $file = readdir($dir)) ) {
			if (( $file != '.' ) && ( $file != '..' )) {
				if ( is_dir($from . '/' . $file) ) {
					self::copyDir($from . '/' . $file, $to . '/' . $file);
				}else{
					self::copyFile($from . '/' . $file, $to . '/' . $file);
				}
			}
		}
		closedir($dir); 
	}
}

public static function isEmptyDir($dir){
	$r = System::root();
	$dir = $r.'/'.$dir;
	return ( file_exists($dir) && is_dir($dir) && ($files = @scandir($dir)) && count($files) <= 2);
} 

public static function isLink($dir){
	$r = System::root();
	$dir = $r.'/'.$dir;
	return file_exists($dir) && is_link($dir) ? true: false;
} 

public static function info($res){
	$r = System::root();
	$f = $r.'/'.$res;
	return file_exists($f) ? lstat($f): false;
} 

public static function permission($res){
	$r = System::root();
	$f = $r.'/'.$res;
	return file_exists($f) ? substr(sprintf('%o', fileperms($f)), -4): false;
}

public static function permissionChange($res, $perm = 0777){
	$r = System::root();
	$f = $r.'/'.$res;
	if( file_exists($f) ){
		umask(0000);
		chmod($f, $perm);
	}
}

public static function makeDir($dir){
	$r = System::root();
	
	$path = explode('/', $dir);
	$_dir = $r;
	foreach($path as $dir){
		$_dir = $_dir.'/'.$dir;
		if(!file_exists($_dir) && !is_dir($_dir)){
			umask(0000);
			mkdir($_dir, 0777);
		}
	}
}

public static function moveDir($from, $to){
	$r = System::root();
	$from = $r.'/'.$from;
	$to = $r.'/'.$to;
	if(file_exists($from) && is_dir($from) && file_exists($to) && is_dir($to)){
		rename($from, $to);
	}
}

public static function deleteDir($from){
	$r = System::root();
	$ffrom = $r.'/'.$from;
	if(file_exists($ffrom) && is_dir($ffrom) && self::isEmptyDir($from)){
		rmdir($ffrom);
	}
}


}
