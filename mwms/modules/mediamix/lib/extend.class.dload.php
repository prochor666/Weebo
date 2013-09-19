<?php
class Dload extends MediaMix{

public $template, $link, $tpl, $suffix, $referer, $input;

private $items, $html;

public function __construct(){
	parent::__construct();
	$this->link = null;
	$this->suffix = null;
	$this->referer = null;
	$this->input = null;
	$this->html = null;
	$this->items = array();
	$this->tpl = $this->config['dload_templates'];
}

public function showTemplates(){

	$html = '<div id="tpl-list">';

	foreach($this->tpl as $key => $tpl) 
	{
		$html .= '
			<label for="'.$key.'">'.$tpl['name'].'</label> <input type="radio" id="'.$key.'" name="tpl" value="'.$key.'" /> 
		';
	}
	
	$html .= '</div>';
	
	return $html;
}

public function showTemplateInput(){
	
	$source = array_key_exists('source', $this->template) ? htmlspecialchars($this->template['source']): null;
	$suffix = array_key_exists('suffix', $this->template) ? htmlspecialchars($this->template['suffix']): null;
	$referer = array_key_exists('referer', $this->template) ? htmlspecialchars($this->template['referer']): null;
	$name = array_key_exists('name', $this->template) ? htmlspecialchars($this->template['name']): null;
	
	$html = '
	<div id="tpl-input-wrap">
		<input type="hidden" name="tpl-key" id="tpl-key" value="'.$this->template['key'].'" />
		<input type="hidden" name="tpl-name" id="tpl-name" value="'.$name.'" />
		<div class="tpl-cnf"><h3>'.$name.'</h3>
			<label for="tpl-uri">'.$this->lng['dload_link'].'</label>
			<textarea rows="5" cols="100" name="tpl-uri" id="tpl-uri" class="tpl-in"></textarea></div>
		<div class="tpl-cnf"><label for="tpl-input">'.$this->lng['dload_input'].'</label><input type="text" name="tpl-input" id="tpl-input" class="tpl-in" value="'.$source.'" /></div>
		<div class="tpl-cnf"><label for="tpl-suffix">'.$this->lng['dload_suffix'].'</label><input type="text" name="tpl-suffix" id="tpl-suffix" class="tpl-in" value="'.$suffix.'" /></div>
		<div class="tpl-cnf"><label for="tpl-referer">'.$this->lng['dload_referer'].'</label><input type="text" name="tpl-referer" id="tpl-referer" class="tpl-in" value="'.$referer.'" /></div>
		<div class="tpl-cnf"><button id="tpl-run">'.$this->lng['dload_run'].'</button></div>
	</div>
	';
	
	return $html;
}


}
?>
