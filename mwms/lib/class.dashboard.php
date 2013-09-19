<?php
/**
* dashboard.class.php - WEEBO framework lib.
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
* @package Dashboard
* @author Jan Prochazka aka prochor <prochor666@gmail.com>
* @copyright 2011 Jan Prochazka aka prochor <prochor666@gmail.com>
* @license http://opensource.org/licenses/mit-license.php MIT License
* @version 1.0 (2011-04-28)
* @link 
*/

class Dashboard{

private $path, $module_path, $html;

public function __construct(){
	$this->path = Registry::get('serverdata/path');
	$this->html = null;
	$this->module_path = Registry::get('active_admin_module');
	$this->load_base();
}

public function show(){
  return $this->html;
}

private function load_base(){
	$this->html = '
		<a href="?module=mwms" class="mwms_dashboard_home_drop">'.Lng::get('system/mwms_workspace').'</a>
	';
}

public function add(){
	$this->html .= '
			&raquo; <a href="?module='.$this->module_path.'">'.Lng::get($this->module_path.'/mwms_module_name').'</a>
		';
}

}
?>
