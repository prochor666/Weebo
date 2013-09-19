<?php
/**
* xhtml.class.php - WEEBO framework lib.
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
* @package Xhtml
* @author Jan Prochazka aka prochor <prochor666@gmail.com>
* @copyright 2011 Jan Prochazka aka prochor <prochor666@gmail.com>
* @license http://opensource.org/licenses/mit-license.php MIT License
* @version 1.0 (2011-04-28)
* @link 
*/

class Xhtml{

public $html, $errors, $autoclose_tags, $exclude_params;

/**
* Constructor
* @access public
* @param array for form init
*/
public function __construct(){
	$this->autoclose_tags = array('area', 'base', 'br' , 'hr', 'img', 'input', 'link', 'meta');
	$this->exclude_params = array();
	$this->errors = null;
	$this->html = null;
}

/**
* Creates DOM elements.
* @access private
* @return string
*/
public function set_element($tagname, $attributes = array(), $close_element = true){

	$html = '<'.$tagname;

	foreach($attributes as $attribute => $value){
		$html .= $attribute != 'default_text' && !in_array($attribute, $this->exclude_params) ? ' '.$this->set_attribute(trim($attribute), trim($value)): null;
	}

	$default_text = array_key_exists('default_text', $attributes) ? trim($attributes['default_text']): null;
	$html .= $this->is_pair($tagname) ? '>'.$default_text: ' /> '.$default_text;
	$html .= $close_element ? $this->close_element($tagname): null;
return $html;
}

/**
* Closes DOM elements if needed.
* @access private
* @return string on success or null, not used
*/
public function close_element($tagname){
return $this->is_pair($tagname) ?  '</'.$tagname.'>': null;
}

/**
* Test if tag is pair or selfclosing.
* @access private
* @return boolean
*/
private function is_pair($tagname){
return in_array($tagname, $this->autoclose_tags) ? false: true;
}

/**
* Sets only valid property.
* @access private
* @return string on success or null, not used
*/
private function set_attribute($attribute, $value){
return !is_null($value) ? $attribute.' = "'.trim($value).'"': null;
}


}
?>
