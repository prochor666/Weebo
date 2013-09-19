<?php
/**
* ajax.class.php - WEEBO framework lib.
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
* @package Ajax
* @author Jan Prochazka aka prochor <prochor666@gmail.com>
* @copyright 2011 Jan Prochazka aka prochor <prochor666@gmail.com>
* @license http://opensource.org/licenses/mit-license.php MIT License
* @version 1.0 (2011-04-28)
* @link 
*/


class Ajax{

final public function __construct() { throw new WeeboException("Cannot instantiate static class!"); }
final public function __clone() { throw new WeeboException("Cannot clone static class!"); }

public static function call_required($path){
  $path = self::root().$path;
  if(file_exists($path) && ( is_link($path) || is_file($path) ) ){
    require(System::fs_path($path));
  }else{
    echo '<div class="mwms-error">'.Lng::get('system/mwms_ajax_unknown_request').'</div>';
  }
}

public static function call_addr($path){
  $path = self::path().$path;
  if(file_exists($path) && ( is_link($path) || is_file($path) ) ){
    require(System::fs_path($path));
  }else{
    echo '<div class="mwms-error">'.Lng::get('system/mwms_ajax_unknown_request').'</div>';
  }
}

public static function call_function($function, $params){
  if(function_exists($function)){
    echo call_user_func_array($function, $params);
  }
}

public static function path(){
  return Registry::get('serverdata/site').'/?weeboapi=';
}

public static function root(){
  return Registry::get('serverdata/root');
}

public static function test(){
  return 'HELLO FROM: '.self::root();
}

}
?>
