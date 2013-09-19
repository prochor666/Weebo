<?php
/**
* lng.class.php - WEEBO framework lib.
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
* @package Lng
* @author Jan Prochazka aka prochor <prochor666@gmail.com>
* @copyright 2011 Jan Prochazka aka prochor <prochor666@gmail.com>
* @license http://opensource.org/licenses/mit-license.php MIT License
* @version 1.0 (2011-04-28)
* @link 
*/

class Lng{

final public function __construct() { throw new WeeboException("Cannot instantiate static class!"); }
final public function __clone() { throw new WeeboException("Cannot clone static class!"); }

public static function get( $val ){
	return Registry::get('lngdata/'.$val);
}

public static function register( $module_id = 'system', $extfile = null ){
	Registry::merge('lngdata', $module_id, self::add( $module_id, $extfile ));
}

private static function add( $module_id, $extfile ){

	$module_translation = array();

	if(!is_null($extfile) && $module_id != 'system' ){
		$mod = new Translation($extfile);
	}else{
		$mod = new Translation();
	}
	$module_translation = $mod->get_translation();

	return $module_translation;
}

public static function publicContent(){
	$data = Registry::get('lngdata');
	$newScriptList = array();
	$newMethodList = array();
	
	foreach($data as $s => $mod){
		foreach($mod as $key => $val){
			if($key == 'cms_public_views'){
				foreach($val as $script => $name){
					$newScriptList[$script] = $name;
				}
			}
			if($key == 'cms_public_view_methods'){
				foreach($val as $script => $method){
					if(!is_null($method)){
						$newMethodList[$script] = $method;
					}
				}
			}
		}
	}
	
	natsort($newScriptList);
	
	$newScriptList = array_unique($newScriptList);
	Registry::set('output_scripts', $newScriptList);
	Registry::set('output_scripts_count', count($newScriptList));
	
	$newMethodList = array_unique($newMethodList);
	Registry::set('output_methods', $newMethodList);
}


}
?>
