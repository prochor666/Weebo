<?php
/**
* static.class.adv.api.php - WEEBO framework adv module lib.
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
* @package AdvApi
* @author Jan Prochazka aka prochor <prochor666@gmail.com>
* @copyright 2011 Jan Prochazka aka prochor <prochor666@gmail.com>
* @license http://opensource.org/licenses/mit-license.php MIT License
* @version 1.0 (2011-07-28)
* @link 
*/

class AdvApi{

final public function __construct() { throw new WeeboException("Can not instantiate static class!"); }
final public function __clone() { throw new WeeboException("Can not clone static class!"); }

public static function getPos(){
	
	$id_position = self::param('id');
	$action = (int)self::param('action');
	$length = (int)self::param('length');
	$format = (string)self::param('format');
	
	$a = new AdvEmbed;
	$a->positionsGet = $id_position;
	$a->format = $format;
	$a->action = $action;
	
	if($id_position > 0)
	{
		switch($action){
			case 0:
				return $a->release();
			break; case 1:
				
				return $a->release();
			break; case 2:
				
				return $a->release();
			break; case 3:
				
				return $a->release();
			break; default:
				
				return null;
		}
	}
}

public static function route(){
	
	$id = (int)self::param('plan');
	$length = (int)self::param('length');
	$ref = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER']: null;
	
	if(!is_null($ref) && $id>0)
	{
		$a = new AdvEmbed;
		
		$x = $a->getBannerLink($id);
		
		if(count($x)>0 && array_key_exists('url', $x) === true && Validator::checkhttp($x['url']) === true)
		{
			$a->writeAction($id, 1, $length, $ref);
			header("Location:".trim($x['url']));
			header("Connection: close"); 
		}else{
			echo 'NO ROUTE DB';
		}
	}else{
		echo 'NO ROUTE REF';
	}
}

public static function param($var){
	return array_key_exists($var, $_GET) === true ? trim($_GET[$var]): null;
}

}
?>
