<?php
/**
* static.class.filter.php - WEEBO framework lib.
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
* @package Filter
* @author Jan Prochazka aka prochor <prochor666@gmail.com>
* @copyright 2011 Jan Prochazka aka prochor <prochor666@gmail.com>
* @license http://opensource.org/licenses/mit-license.php MIT License
* @version 1.0 (2011-07-28)
* @link 
*/


class Filter{

final public function __construct() { throw new WeeboException("Can not instantiate static class!"); }
final public function __clone() { throw new WeeboException("Can not clone static class!"); }

public static function isId($var)
{
  return  is_int($var) && $var>0 ? true: false;
}

public static function makeInt($var)
{
  return  (int)$var;
}

public static function bytesReadable($size){
	return System::fsFileSize($size);
}

public static function makeSafeString($str){

	$str = strip_tags($str);

	/*
	if($str !== mb_convert_encoding( mb_convert_encoding($str, 'UTF-32', 'UTF-8'), 'UTF-8', 'UTF-32') ){
		$str = mb_convert_encoding($str, 'UTF-8');
	}
	$str = htmlentities($str, ENT_COMPAT, 'UTF-8');
	$str = preg_replace('`&([a-z]{1,2})(acute|uml|circ|grave|ring|cedil|slash|tilde|caron|lig);`i', '\\1', $str);
	$str = html_entity_decode($str, ENT_COMPAT, 'UTF-8');
	*/
	return trim($str);
}

public static function makeUrlString($str){

	$str = strip_tags($str);

	$XFilter = array(
		"А" => "а", "Б" => "б", "В" => "в", "Г" => "г", "Д" => "д", "Е" => "е", "Ж" => "ж", "З" => "з", "И" => "и", "Й" => "и", 
		"К" => "к", "Л" => "л", "М" => "м", "Н" => "н", "О" => "о", "П" => "п", "Р" => "р", "С" => "с", "Т" => "т", "У" => "у", 
		"Ф" => "ф", "Х" => "х", "Ц" => "ц", "Ч" => "ч", "Ш" => "ш", "Щ" => "щ", "Ъ" => "ъ", "Ы" => "ы", "Ь" => "ь", "Э" => "э", 
		"Ю" => "ю", "Я" => "я", "й" => "и",

		"ě" => "e", "š" => "s", "č" => "c","ř" => "r", "ž" => "z","ý" => "y","á" => "a","í" => "i","é" => "e","ů" => "u","ü" => "u",
		"ú" => "u","ó" => "o","ö" => "oe","ň" => "n","ń" => "n","ć" => "c", "ë" => "ea","ä" => "ae","ď" => "d","ľ" => "l", "ť" => "t", "ç" => "c", "ß" => "ss",

		"Ě" => "E", "Š" => "S", "Č" => "C","Ř" => "R", "Ž" => "Z","Ý" => "Y","Á" => "A","Í" => "I","É" => "E","Ů" => "U","Ü" => "U",
		"Ú" => "U","Ó" => "O","Ö" => "Oe","Ň" => "N","Ń" => "N","Ć" => "C", "Ë" => "Ea","Ä" => "Ae","Ď" => "D","Ľ" => "L", "Ť" => "T", "Ç" => "C"
		) ;

	$str = strtr($str,$XFilter);
	
	if($str !== mb_convert_encoding( mb_convert_encoding($str, 'UTF-32', 'UTF-8'), 'UTF-8', 'UTF-32') ){
		$str = mb_convert_encoding($str, 'UTF-8');
	}
	$str = htmlentities($str, ENT_NOQUOTES, 'UTF-8');
	$str = preg_replace('`&([a-z]{1,2})(acute|uml|circ|grave|ring|cedil|slash|tilde|caron|lig);`i', '\\1', $str);
	$str = html_entity_decode($str, ENT_NOQUOTES, 'UTF-8');
	$str = preg_replace(array('`[^a-z0-9]`i','`[-]+`'), '-', $str);
	$str = mb_strtolower( trim($str, '-') );
	
	return rawurlencode($str);
}

}
?>
