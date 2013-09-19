<?php
/**
* static.class.user.render.php - WEEBO framework users module lib.
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
* @package UserRender
* @author Jan Prochazka aka prochor <prochor666@gmail.com>
* @copyright 2012 Jan Prochazka aka prochor <prochor666@gmail.com>
* @license http://opensource.org/licenses/mit-license.php MIT License
* @version 1.0 (2012-10-16)
* @link 
*/

class UserRender{

final public function __construct() { throw new WeeboException("Can not instantiate static class!"); }
final public function __clone() { throw new WeeboException("Can not clone static class!"); }

public static function getCfg($var)
{
	$out = new Users;
	return $out->config[$var];
}

public static function getLng($var)
{
	$out = new Users;
	return $out->lng[$var];
}






}
?>
