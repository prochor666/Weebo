<?php 
/**
* registry.class.php - WEEBO framework lib.
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
* @package Registry
* @author Jan Prochazka aka prochor <prochor666@gmail.com>
* @copyright 2011 Jan Prochazka aka prochor <prochor666@gmail.com>
* @license http://opensource.org/licenses/mit-license.php MIT License
* @version 1.0 (2011-04-28)
* @link 
*/

class Registry{
 /*
 // Store system variables into session
 // ver. 1.0
 */
final public function __construct() { throw new WeeboException("Cannot instantiate static class!"); }
final public function __clone() { throw new WeeboException("Cannot clone static class!"); }

 public static function init(){
  /* Set registry value */	
  if(!isset($_SESSION['weebo_registry'][session_id()]) || !is_array($_SESSION['weebo_registry'][session_id()])){
   $_SESSION['weebo_registry'][session_id()] = array();
  }  
 }

 public static function set($index, $default = null, $data = null){
  /* Set registry value */
  if(isset($data) && !is_null($data)){
     $_SESSION['weebo_registry'][session_id()][$index] =  $data;
  }else{
     $_SESSION['weebo_registry'][session_id()][$index] = $default;
  }

 }

 public static function merge($parent, $index, $data = null){
  /* merge registry value */
  if(isset($data)){
    $_SESSION['weebo_registry'][session_id()][$parent][$index] = $data;
  }
 }

 public static function readall(){
    return $_SESSION['weebo_registry'][session_id()];
 }

/* get value from array or alternate from registry */
public static function alternate($data, $index_tree){

 $new = null;
 
 if(is_array($data)){
  $new = $data;
  $tree = explode('/',$index_tree);
  foreach($tree as $index){
  	echo $new[$index];
  	$new = isset($new[$index]) ? $new[$index]: null;
  }
 }

 return !is_null($new) ? $new: self::get($index_tree);
}


 public static function get_one($index, $default = false){
  /* get registry value */
  return isset($_SESSION['weebo_registry'][session_id()][$index]) ?  $_SESSION['weebo_registry'][session_id()][$index]: $default;	
 }

 public static function get($index_tree, $default = false){
  /* get registry value from tree/array */
  $rvar = $_SESSION['weebo_registry'][session_id()];
  $tree = explode('/',$index_tree);
  foreach($tree as $index){
  	$rvar = isset($rvar[$index]) ? $rvar[$index]: $default;
  }
  //echo $rvar;
  return isset($rvar) ? $rvar: $default;	
 }

 public static function del($index){
  /* delete registry value */
  if(isset($_SESSION['weebo_registry'][session_id()][$index])){ unset($_SESSION['weebo_registry'][session_id()][$index]); }	
 }

 public static function reset(){
  /* reset registry */
  if(isset($_SESSION['weebo_registry'][session_id()])){ unset($_SESSION['weebo_registry']); self::init(); }	
 }

}
?>
