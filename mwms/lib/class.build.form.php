<?php
/**
* build.form.class.php - WEEBO framework lib.
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
* @package BuildForm
* @author Jan Prochazka aka prochor <prochor666@gmail.com>
* @copyright 2011 Jan Prochazka aka prochor <prochor666@gmail.com>
* @license http://opensource.org/licenses/mit-license.php MIT License
* @version 1.0 (2011-04-28)
* @link 
*/

class BuildForm{

private $properties, $page;
public $html, $idhash, $errors, $validation, $autoclose_tags, $exclude_params;

/**
* Constructor
* @access public
* @param array for form init
*/
public function __construct($properties = array()){

	$default_properties = array(
		'method' => 'post',
		'action' => '',
		'in_table' => false,
		'id' => '',
		'class' => '',
		'idhash' => System::rnd(), // TODO: make some hash?
		'enctype'=> 'application/x-www-form-urlencoded',
	);

	$this->properties = $this->set_properties_by_default($properties, $default_properties);
	$this->autoclose_tags = array('input');
	$this->exclude_params = array('idhash','in_table');
	$this->validation = array();
	$this->page = 1;
	$this->idhash = $this->properties['idhash'];
	$this->errors = null;
}

/**
* Sets Form active.
* @access public
* @return void
*/
public function init(){
  $this->html .= $this->dom_create('form', $this->properties, false);
  $this->add_page();
}


/**
* Sets Form active.
* @access public
* @return mixed string
*/
public function display(){
   $this->close_page();
   $this->html .= $this->dom_close('form');
   return $this->html;
}

/**
* Adds next page as TABLE or DIV.
* @access public
* @return void
*/
public function add_page(){

	if($this->page>1){ $this->close_page(); }

	if($this->properties['in_table']){
		$this->html .= $this->dom_create('table', array('class' => $this->properties['class'].' '.$this->properties['class'].'_page_'.$this->page), false);
		$this->html .= $this->dom_create('tbody', array(), false);
	}else{
		$this->html .= $this->dom_create('div', array('class' => $this->properties['class'].' '.$this->properties['class'].'_page_'.$this->page), false);
	}
	$this->page++;
	
}

/**
* Closes previous page.
* @access private
* @return void
*/
private function close_page(){
	if($this->properties['in_table']){
		$this->html .= $this->dom_close('tbody');
		$this->html .= $this->dom_close('table');
	}else{
		$this->html .= $this->dom_close('div');
	}
}

/**
* Adds select item.
* @access public
* @return void
*/
public function add_select($properties = array()){

	$default_properties = array(
		'title' => '',
		'name' => '',
		'class' => '',
		'size' => '',
		'multiple' => '',
		'selected' => array(),
		'options' => array(),
		'main_title' => '',
	);

	$properties = $this->set_properties_by_default($properties, $default_properties);

	$item_label = $properties['main_title'];

	$properties_input['id'] = $properties['name'].'_'.$this->idhash;
	$properties_input['class'] = $properties['class'];
	$properties_input['title'] = $properties['title'];
	$properties_input['size'] = $properties['size'];
	$properties_input['name'] = $properties['name'];

	if($properties['multiple'] === 'multiple'){
		$properties_input['multiple'] = 'multiple';
	}

	$item_input = $this->dom_create('select', $properties_input, false);

	foreach($properties['options'] as $key => $label){

	$properties_option['value'] = $key;
	$properties_option['default_text'] = $label;

	if(in_array($key, $properties['selected'])){

		$properties_option['selected'] = 'selected';

	}else{

		if(array_key_exists('selected', $properties_option)){
			unset($properties_option['selected']);
		}

	}

	$item_input .= $this->dom_create('option', $properties_option);

	}
	$item_input .= $this->dom_close('select');

$this->add_form_item($item_label, $item_input);
   
}

/**
* Adds set of radio buttons.
* @access public
* @return void
*/
public function add_radio_set($properties = array()){

	$default_properties = array(
		'labels' => array(),
		'titles' => array(),
		'name' => '',
		'class' => '',
		'values' => array(),
		'checked' => '',
		'main_title' => '',
	);

	$properties = $this->set_properties_by_default($properties, $default_properties);

	$item_label = $properties['main_title'];
	$item_input = null;

	foreach($properties['labels'] as $key => $label){

		$properties_label['for'] = $properties['name'].'_'.$this->idhash.'_'.$key;
		$properties_label['class'] = 'mwms_label_as_button';
		//$properties_label['default_text'] = $label;

		$properties_input['id'] = $properties['name'].'_'.$this->idhash.'_'.$key;
		$properties_input['type'] = 'radio';
		$properties_input['class'] = $properties['class'];
		$properties_input['title'] = $properties['titles'][$key];
		$properties_input['name'] = $properties['name'];
		$properties_input['default_text'] = $label;

		if($properties['checked'] === $properties['values'][$key]){

			$properties_input['checked'] = 'checked';

		}else{
			if(array_key_exists('checked', $properties_input)){
				unset($properties_input['checked']);
			}

		}

		$item_input .= $this->dom_create('label', $properties_label, false);
		$item_input .= $this->dom_create('input', $properties_input);
		$item_input .= $this->dom_close('label');

	}

$this->add_form_item($item_label, $item_input);
}

/**
* Adds set of checkboxes.
* @access public
* @return void
*/
public function add_checkbox_set($properties = array()){

	$default_properties = array(
		'labels' => array(),
		'titles' => array(),
		'name' => '',
		'class' => '',
		'values' => array(),
		'checked' => array(),
		'main_title' => '',
	);

	$properties = $this->set_properties_by_default($properties, $default_properties);

	$item_label = $properties['main_title'];
	$item_input = null;

	foreach($properties['labels'] as $key => $label){

		$properties_label['for'] = $properties['name'].'_'.$this->idhash.'_'.$key;
		$properties_label['class'] = 'mwms_label_as_button';
		//$properties_label['default_text'] = $label;

		$properties_input['id'] = $properties['name'].'_'.$this->idhash.'_'.$key;
		$properties_input['type'] = 'checkbox';
		$properties_input['class'] = $properties['class'];
		$properties_input['title'] = $properties['titles'][$key];
		$properties_input['name'] = $properties['name'].'[]';
		$properties_input['default_text'] = $label;

		if(in_array($properties['values'][$key], $properties['checked'])){

			$properties_input['checked'] = 'checked';

		}else{
			if(array_key_exists('checked', $properties_input)){
				unset($properties_input['checked']);
			}

		}

		$item_input .= $this->dom_create('label', $properties_label, false);
		$item_input .= $this->dom_create('input', $properties_input);
		$item_input .= $this->dom_close('label');

	}

$this->add_form_item($item_label, $item_input);
}

/**
* Adds text input item.
* @access public
* @return void
*/
public function add_text_input($properties = array()){

	 $default_properties = array(
	  'title' => '',
	  'name' => '',
	  'type' => '',
	  'class' => '',
	  'size' => '',
	  'maxlength' => '',
	  'value' => ''
	);
	$properties = $this->set_properties_by_default($properties, $default_properties);

	$properties['id'] = $properties['name'].'_'.$this->idhash;

	$item_label = $this->dom_create('label', array('for' => $properties['id'], 'default_text' => $properties['title']));
	$item_input = $this->dom_create('input', $properties);

	$this->add_form_item($item_label, $item_input);
}
/**
* Adds hidden input item.
* @access public
* @return void
*/
public function add_hidden_input($properties = array()){

	$default_properties = array(
	  'name' => '',
	  'value' => ''
	);

	$properties = $this->set_properties_by_default($properties, $default_properties);

	$properties['id'] = $properties['name'].'_'.$this->idhash;
	$properties['type'] = 'hidden';

	$item_input = $this->dom_create('input', $properties);

	$this->html .= $item_input;
}

/**
* Adds textarea item.
* @access public
* @return void
*/
public function add_textarea($properties = array()){

	$default_properties = array(
		'title' => '',
		'name' => '',
		'rows' => '',
		'cols' => '',
		'class' => '',
		'default_text' => '',
	);
	$properties = $this->set_properties_by_default($properties, $default_properties);
	$properties['id'] = $properties['name'].'_'.$this->idhash;

	$item_label = $this->dom_create('label', array('for' => $properties['id'], 'default_text' => $properties['title']));
	$item_input = $this->dom_create('textarea', $properties);

	$this->add_form_item($item_label, $item_input);
}

/**
* Adds text input item.
* @access public
* @return void
*/
public function add_button($properties = array()){

	 $default_properties = array(
	  'title' => '',
	  'name' => '',
	  'type' => '',
	  'class' => '',
	  'value' => ''
	);
	 
	$properties = $this->set_properties_by_default($properties, $default_properties);
	$properties['id'] = $properties['name'].'_'.$this->idhash;
	$item_input = $this->dom_create('input', $properties);

	$this->html .= $item_input;
}

/**
* Adds external class/method row to global html.
* @access public
* @return void
*/
public function add_external_method($item_label, $static, $class, $func, $params){
   if(!$static){ $class = new $class; }
   $item_input = call_user_func_array(array($class ,$func), $params);
   $this->add_form_item($item_label, $item_input);
}

/**
* Adds blank row to global html.
* @access public
* @return void
*/
public function add_blank_row($default_text = null){
	if($this->properties['in_table']){

		$this->html .= $this->dom_create('tr', array(), false);
		$this->html .= $this->dom_create('td', array('class' => 'mwms_spanned_row', 'colspan' => '2'), false);

	}else{

		$this->html .= $this->dom_create('div', array('class' => 'mwms_spanned_row'), false);
	}

$this->html .= $default_text;
}

/**
* Closes blank row to global html.
* @access public
* @return void
*/
public function close_blank_row(){
	if($this->properties['in_table']){

		$this->html .= $this->dom_close('td');
		$this->html .= $this->dom_close('tr');
	}else{

		$this->html .= $this->dom_close('div');
	}
}

/**
* Adds form item to global html.
* @access private
* @return void
*/
private function add_form_item($item_label, $item_input){
	if($this->properties['in_table']){

		$this->html .= $this->dom_create('tr', array(), false);
		$this->html .= $this->dom_create('td', array('class' => 'mwms_form_label'), false);
		$this->html .= $item_label;
		$this->html .= $this->dom_close('td');

		$this->html .= $this->dom_create('td', array('class' => 'mwms_form_active'), false);
		$this->html .= $item_input;
		$this->html .= $this->dom_close('td');
		$this->html .= $this->dom_close('tr');

	}else{

		$this->html .= $this->dom_create('div', array('class' => 'mwms_form_label'), false);
		$this->html .= $item_label;
		$this->html .= $this->dom_close('div');
		$this->html .= $this->dom_create('div', array('class' => 'mwms_form_active'), false);
		$this->html .= $item_input;
		$this->html .= $this->dom_close('div');

	}
}


/**
* Creates DOM elements.
* @access private
* @return string
*/
private function dom_create($tagname, $properties, $closetag = true){
$html = new Xhtml;
$html->exclude_params = $this->exclude_params;
return $html->set_element($tagname, $properties, $closetag);
}

/**
* Closes DOM elements if needed.
* @access private
* @return string
*/
private function dom_close($tagname){
$html = new Xhtml;
return $html->close_element($tagname);
}


/**
* Force value from defaults.
* @access private
* @return data index or default index value, proper data type
*/
private function set_properties_by_default($data, $defaults){
 
	$new = array();
	foreach($defaults as $property => $value){
		if(array_key_exists($property, $data)){
			$datatype_test = $this->datatype_required($property, $data, $defaults);
			if(!$datatype_test){
				$new[$property] = $data[$property];
			}else{
				$new[$property] = $defaults[$property];
			}
		}
	}
	return $new;
}

/**
* Validate property datatype.
* @access private
* @return boolean
*/
private function validate_property($property, $data, $defaults){
	if(array_key_exists($property, $data) && array_key_exists($property, $defaults)){
		$datatype_test = $this->datatype_required($property, $data, $defaults);
		if(!$datatype_test){
			return true;
		}else{	
			$this->errors .= Lng::get('system/mwms_debug_datatype_required').' '.$datatype_test;
			return false;
		}
	}else{
		$this->errors .= Lng::get('system/mwms_debug_key_not_enabled').' '.$property;
		return false;
	}

 return false;
}

/**
* Test datatype of data / defaults.
* @access private
* @return boolean false on success, string with datatype on fail
*/
private function datatype_required($property, $data, $defaults){
	$original = gettype($data[$property]);
	$default  = gettype($defaults[$property]);
	return $original === $default ? false: $default;
}

}
?>
