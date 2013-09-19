<?php
/**
* navigator.class.php - WEEBO framework lib.
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
* @package Navigator
* @author Jan Prochazka aka prochor <prochor666@gmail.com>
* @copyright 2011 Jan Prochazka aka prochor <prochor666@gmail.com>
* @license http://opensource.org/licenses/mit-license.php MIT License
* @version 1.0 (2011-04-28)
* @link 
*/

class Navigator{

final public function __construct() { throw new WeeboException("Cannot instantiate static class!"); }
final public function __clone() { throw new WeeboException("Cannot clone static class!"); }

/* Modules */
public static function get_module($map = null){
  $m = new Module();
  $m->get_module($map);
}

/* Pager */
public static function pager($total, $default = null, $index = 'page'){
  $pg = new Pager($total, $default, $index);
  return $pg->show_pager();
}

public static function pager_limited($total, $default = null, $index = 'page'){
  $pg = new Pager($total, $default, $index);
  return $pg->show_pager_limited();
}

public static function pager_custom($total, $default = null, $custom_uri = array(), $page_limiter = 3){
  $pg = new Pager($total, $default);
  $pg->page_limiter = $page_limiter;
  return $pg->show_custom_pager($custom_uri);
}

public static function pager_ajax($total, $default = null, $custom_uri = array(), $force = 0, $default_name = 'page', $page_limiter = 3){
  $pg = new Pager($total, $default);
  $pg->actual_force = $force;
  $pg->page_limiter = $page_limiter;
  $pg->page_name_default = $default_name;
  return $pg->show_ajax_pager($custom_uri);
}

public static function pager_ajax_rewrite($total, $default = null, $custom_uri = array(), $force = 0, $default_name = 'page', $page_limiter = 3){
  $pg = new Pager($total, $default);
  $pg->actual_force = $force;
  $pg->page_limiter = $page_limiter;
  $pg->page_name_default = $default_name;
  return $pg->show_ajax_pager_rewrite($custom_uri);
}

}
?>
