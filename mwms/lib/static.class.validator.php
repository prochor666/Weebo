<?php
/**
* validator.class.php - WEEBO framework lib.
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
* @package Validator
* @author Jan Prochazka aka prochor <prochor666@gmail.com>
* @copyright 2011 Jan Prochazka aka prochor <prochor666@gmail.com>
* @license http://opensource.org/licenses/mit-license.php MIT License
* @version 1.0 (2011-04-28)
* @link 
*/

class Validator {
 /*
 // Validate data/formatting
 // ver. 1.0
 */
final public function __construct() { throw new WeeboException("Don't instantiate meeeeeeee! Let me go! I'm static!"); }
final public function __clone() { throw new WeeboException("Don't clone meeeeeeee! God sake, i'm static!"); }

public static function exists($arr, $val){
  return isset($arr) && is_array($arr) && array_key_exists($val, $arr) ? true: false;
}

public static function set_alter($arr1, $arr2, $index, $subindex = '0'){
	if(self::exists($arr1, $index)){
		return is_array($arr1[$index]) ? $arr1[$index][$subindex]: $arr1[$index];
	}

	if(self::exists($arr2, $index)){
		return is_array($arr2[$index]) ? $arr2[$index][$subindex]: $arr2[$index];
	}

return null;
}

public static function set($arr, $val, $default = null){
	return isset($arr) && is_array($arr) && array_key_exists($val, $arr) ? $arr[$val]: $default;
}

public static function fdate($val, $long = true){
	return $long ? date(Lng::get('system/date_time_format'), (int)$val): date(Lng::get('system/date_format'), (int)$val);
}

public static function fdatePrecise($val){
	return date(Lng::get('system/date_time_format_precise'), (int)$val);
}

public static function ftime($val){
	return date(Lng::get('system/time_format'), (int)$val);
}

public static function compare($val1, $val2){
	return $val1 === $val2 ? true: false;
}

public static function selected($val1, $val2){
	return $val1 == $val2 ? ' selected = "selected"': null;
}

public static function checked($val1, $val2){
	return $val1 == $val2 ? ' checked = "checked"': null;
}

public static function checkmail($email){
	return (bool)filter_var($email, FILTER_VALIDATE_EMAIL);
}

public static function checkurl($url){
	return (bool)filter_var($url, FILTER_VALIDATE_URL);
}

public static function checkhttp($url){
	return (bool)preg_match('|^http(s)?://[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i', $url);
}

public static function url($url){
	$str = trim($str);
	$str = htmlspecialchars($str);
	$XFilter = array(
		"---" => "-", "--" => "-",

		"+" => "-","=" => "","\n" => "","\r" => "","\t" => "","%" => "-","/" => "-","#" => "-","$" => "-", "~" => "", "°" => "",

		"^" => "-","&" => "and","(" => "-",")" => "-", ":" => "-", ";" => "_", "?" => "", "!" => "","," => "","." => "-",

		"{" => "","}" => "","\\" => "","<" => "",">" => "","@" => "","*" => "","`" => "","*" => "","ˇ" => "","´" => "","§" => "",

		"'" => "", "\"" => "" ," " => "-", "&#173;" => "", "&#194;" => "", "&amp;" => "and", 

		"А" => "а", "Б" => "б", "В" => "в", "Г" => "г", "Д" => "д", "Е" => "е", "Ж" => "ж", "З" => "з", "И" => "и", "Й" => "и", 
		"К" => "к", "Л" => "л", "М" => "м", "Н" => "н", "О" => "о", "П" => "п", "Р" => "р", "С" => "с", "Т" => "т", "У" => "у", 
		"Ф" => "ф", "Х" => "х", "Ц" => "ц", "Ч" => "ч", "Ш" => "ш", "Щ" => "щ", "Ъ" => "ъ", "Ы" => "ы", "Ь" => "ь", "Э" => "э", 
		"Ю" => "ю", "Я" => "я", "й" => "и",

		"ě" => "e", "š" => "s", "č" => "c","ř" => "r", "ž" => "z","ý" => "y","á" => "a","í" => "i","é" => "e","ů" => "u","ü" => "u",
		"ú" => "u","ó" => "o","ö" => "o","ň" => "n","ń" => "n","ć" => "c", "ë" => "e","ä" => "a","ď" => "d","ľ" => "l", "ť" => "t",

		"Ě" => "E", "Š" => "S", "Č" => "C","Ř" => "R", "Ž" => "Z","Ý" => "Y","Á" => "A","Í" => "I","É" => "E","Ů" => "U","Ü" => "U",
		"Ú" => "U","Ó" => "O","Ö" => "O","Ň" => "N","Ń" => "N","Ć" => "C", "Ë" => "E","Ä" => "A","Ď" => "D","Ľ" => "L", "Ť" => "T"
	) ;

	$str = strtr($str,$XFilter);
	$str = strtr($str,$XFilter);
	$str = strtr($str,$XFilter);
	$str = strtr($str,$XFilter);
	$str = strtr($str,$XFilter);
	$str = strtolower($str);

	return $str;
}

}
?>
