<?php
class DevTool{

private $config, $blankUrl, $toolUrl, $dataDir;

public function __construct(){
	$this->config = Lng::get('devtool');
	$this->toolUrl = Ajax::path().'require&amp;file=/mwms/modules/devtool/view/devtool.call.php&amp;sysadmin='.Registry::get('userdata/id').'&amp;tool=';
	$this->blankUrl = System::path().'/mwms/modules/devtool/view/devtool.blank.php?f=';
	$this->dataDir = '/devtool';
	
	if(Registry::get('userdata/root') != 1 || Registry::get('userdata/admin') != 1){
		die();
	}
}

public function list_controls(){

	$html = '
	<div class="mwms_tabs" id="tabs">
		<ul>
			<li><a href="'.$this->toolUrl.'date_time_control">'.$this->config['date_time_control'].'<span>&nbsp;</span></a></li>
			<li><a href="'.$this->toolUrl.'url_control">'.$this->config['url_control'].'<span>&nbsp;</span></a></li>
			<li><a href="'.$this->toolUrl.'js_pack_control">'.$this->config['js_pack_control'].'<span>&nbsp;</span></a></li>
			<li><a href="'.$this->toolUrl.'hash_control">'.$this->config['hash_control'].'<span>&nbsp;</span></a></li>
			<li><a href="'.$this->toolUrl.'php_run">'.$this->config['php_run'].'<span>&nbsp;</span></a></li>
			<li><a href="'.$this->toolUrl.'crypt_performance">'.$this->config['crypt_performance_test'].'<span>&nbsp;</span></a></li>
			<li><a href="'.$this->toolUrl.'ui_icons">'.$this->config['ui_icons'].'<span>&nbsp;</span></a></li>
		</ul>
		<div id="tabs-1" class="mwms_devtool_control" title=""></div>
	</div>
	';

	return $html;
}

private function list_hash_algorithms(){
	$html = null;
	$algos = hash_algos();
	sort($algos);
	foreach ($algos as $v) {
		$html .= '<option vlaue="'.$v.'">'.$v.'</option>';
	}
	return $html;
}

/* tools */
public function hash_control(){

	$html = '
	 <div id="hash_control">
		<div id="devtool_source_wrap">
			<input type="hidden" name="data_callback" id="data_callback" value="to_hash" />
			<div class="devtool_source_wrap"> 
			<textarea cols="10" rows="10" id="devtool_source"></textarea>
		</div> 
		
		<div class="devtool_separator_wrap">
			<select name="data_format" class="data_format" id="data_format">
			  '.$this->list_hash_algorithms().'
			</select><br />
			<input type="hidden" id="data_format" value="0" />
			<button class="data_send button" title="'.$this->config['send_data'].'">'.$this->config['send_data'].'</button>
		</div>

		<div class="devtool_result_wrap"> 
			<textarea cols="10" rows="10" id="devtool_result"></textarea>
		</div> 
	</div>
	';

	return $html;
} 

public function date_time_control(){
  
	$html = '
	<div id="date_time_control">
		<div class="devtool_source_wrap"> 
			<input type="text" size="10" class="in devtool_date" id="devtool_source" value="" />
		</div> 
		<div class="devtool_separator_wrap">
			<select name="data_callback" class="data_callback" id="data_callback">
				<option value="date_to_int">'.$this->config['date_to_int'].'</option>
				<option value="int_to_date">'.$this->config['int_to_date'].'</option>
			</select> <br />
			<input type="text" size="10" class="in devtool_date" id="data_format" value="'.$this->config['default_date_format'].'" /> <br />
			<button class="data_send button" title="'.$this->config['send_data'].'">'.$this->config['send_data'].'</button>
		</div>
		<div class="devtool_result_wrap"> 
			<input type="text" size="10" class="in devtool_date" id="devtool_result" value="" />
		</div> 
	</div>
	';

	return $html;
} 

public function url_control(){
  
	$html = '
	<div id="url_control">
		<div class="devtool_source_wrap"> 
			<textarea cols="10" rows="10" id="devtool_source"></textarea>
		</div> 
		<div class="devtool_separator_wrap">
			<select name="data_format" class="data_format" id="data_format">
				<option value="urlencode">'.$this->config['url_encode'].'</option>
				<option value="urldecode">'.$this->config['url_decode'].'</option>
			</select> <br />
			<input type="hidden" id="data_callback" value="process_url" />
			<button class="data_send button" title="'.$this->config['send_data'].'">'.$this->config['send_data'].'</button>
		</div>
		<div class="devtool_result_wrap"> 
			<textarea cols="10" rows="10" id="devtool_result"></textarea>
		</div> 
	</div>
	';
  
	return $html;
} 


public function js_pack_control(){
	$html = '
	<div id="js_pack_control">
		<div class="devtool_source_wrap"> 
			<textarea cols="10" rows="10" id="devtool_source"></textarea>
		</div> 
		<div class="devtool_separator_wrap">
			<select name="data_format" class="data_format" id="data_format">
				<option value="10">Numeric</option>
				<option value="62">Normal ASCII</option>
				<option value="95">High ASCII</option>
			</select> <br />
			<input type="hidden" id="data_callback" value="js_packer" />
			<button class="data_send button" title="'.$this->config['send_data'].'">'.$this->config['send_data'].'</button>
		</div>
		<div class="devtool_result_wrap"> 
			<textarea cols="10" rows="10" id="devtool_result"></textarea>
		</div> 
	</div>
	';

	return $html;
} 

public function crypt_performance(){

	$html = '
	<div id="crypt_performance">
		<div class="devtool_source_wrap"> 
			<input type="hidden" id="devtool_source" value="0" />
		</div> 
		<div class="devtool_separator_wrap">
			<input type="hidden" id="data_format" value="0" />
			<input type="hidden" id="data_callback" value="crypt_performance_test" />
			<p>'.$this->config['crypt_performance_test'].'</pp>
			<button class="data_send button" title="'.$this->config['run'].'">'.$this->config['run'].'</button>
		</div>
		<div class="devtool_result_wrap"> 
			<textarea cols="10" rows="10" id="devtool_result"></textarea>
		</div> 
	</div>
	';

	return $html;
} 

public function php_run(){
	
	$html = '
	<div id="php_run">
		<div class="devtool_source_wrap"> 
			<textarea cols="10" rows="10" id="devtool_source" class="php"></textarea>
			'.$this->devtool_php_dir_list().'
		</div> 
		<div class="devtool_separator_wrap">
			<input type="hidden" id="data_format" value="0" />
			<input type="hidden" id="data_callback" value="php_run_test" />
			<button class="data_send button" title="'.$this->config['run'].'">'.$this->config['run'].'</button>
		</div>
		<div class="devtool_result_wrap"> 
			<iframe id="devtool_result" src=""></iframe>
		</div> 
	</div>
	';

	return $html;
}

public function uploadFile(){
	
	
	
}

public function ui_icons(){

	$html = '
<div id="icons" class="ui-widget ui-helper-clearfix">
			
<span class="ui-icon ui-icon-blank"></span>
<span class="ui-icon ui-icon-carat-1-n"></span>
<span class="ui-icon ui-icon-carat-1-ne"></span>
<span class="ui-icon ui-icon-carat-1-e"></span>
<span class="ui-icon ui-icon-carat-1-se"></span>
<span class="ui-icon ui-icon-carat-1-s"></span>
<span class="ui-icon ui-icon-carat-1-sw"></span>
<span class="ui-icon ui-icon-carat-1-w"></span>
<span class="ui-icon ui-icon-carat-1-nw"></span>
<span class="ui-icon ui-icon-carat-2-n-s"></span>
<span class="ui-icon ui-icon-carat-2-e-w"></span>
<span class="ui-icon ui-icon-triangle-1-n"></span>
<span class="ui-icon ui-icon-triangle-1-ne"></span>
<span class="ui-icon ui-icon-triangle-1-e"></span>
<span class="ui-icon ui-icon-triangle-1-se"></span>
<span class="ui-icon ui-icon-triangle-1-s"></span>
<span class="ui-icon ui-icon-triangle-1-sw"></span>
<span class="ui-icon ui-icon-triangle-1-w"></span>
<span class="ui-icon ui-icon-triangle-1-nw"></span>
<span class="ui-icon ui-icon-triangle-2-n-s"></span>
<span class="ui-icon ui-icon-triangle-2-e-w"></span>
<span class="ui-icon ui-icon-arrow-1-n"></span>
<span class="ui-icon ui-icon-arrow-1-ne"></span>
<span class="ui-icon ui-icon-arrow-1-e"></span>
<span class="ui-icon ui-icon-arrow-1-se"></span>
<span class="ui-icon ui-icon-arrow-1-s"></span>
<span class="ui-icon ui-icon-arrow-1-sw"></span>
<span class="ui-icon ui-icon-arrow-1-w"></span>
<span class="ui-icon ui-icon-arrow-1-nw"></span>
<span class="ui-icon ui-icon-arrow-2-n-s"></span>
<span class="ui-icon ui-icon-arrow-2-ne-sw"></span>
<span class="ui-icon ui-icon-arrow-2-e-w"></span>
<span class="ui-icon ui-icon-arrow-2-se-nw"></span>
<span class="ui-icon ui-icon-arrowstop-1-n"></span>
<span class="ui-icon ui-icon-arrowstop-1-e"></span>
<span class="ui-icon ui-icon-arrowstop-1-s"></span>
<span class="ui-icon ui-icon-arrowstop-1-w"></span>
<span class="ui-icon ui-icon-arrowthick-1-n"></span>
<span class="ui-icon ui-icon-arrowthick-1-ne"></span>
<span class="ui-icon ui-icon-arrowthick-1-e"></span>
<span class="ui-icon ui-icon-arrowthick-1-se"></span>
<span class="ui-icon ui-icon-arrowthick-1-s"></span>
<span class="ui-icon ui-icon-arrowthick-1-sw"></span>
<span class="ui-icon ui-icon-arrowthick-1-w"></span>
<span class="ui-icon ui-icon-arrowthick-1-nw"></span>
<span class="ui-icon ui-icon-arrowthick-2-n-s"></span>
<span class="ui-icon ui-icon-arrowthick-2-ne-sw"></span>
<span class="ui-icon ui-icon-arrowthick-2-e-w"></span>
<span class="ui-icon ui-icon-arrowthick-2-se-nw"></span>
<span class="ui-icon ui-icon-arrowthickstop-1-n"></span>
<span class="ui-icon ui-icon-arrowthickstop-1-e"></span>
<span class="ui-icon ui-icon-arrowthickstop-1-s"></span>
<span class="ui-icon ui-icon-arrowthickstop-1-w"></span>
<span class="ui-icon ui-icon-arrowreturnthick-1-w"></span>
<span class="ui-icon ui-icon-arrowreturnthick-1-n"></span>
<span class="ui-icon ui-icon-arrowreturnthick-1-e"></span>
<span class="ui-icon ui-icon-arrowreturnthick-1-s"></span>
<span class="ui-icon ui-icon-arrowreturn-1-w"></span>
<span class="ui-icon ui-icon-arrowreturn-1-n"></span>
<span class="ui-icon ui-icon-arrowreturn-1-e"></span>
<span class="ui-icon ui-icon-arrowreturn-1-s"></span>
<span class="ui-icon ui-icon-arrowrefresh-1-w"></span>
<span class="ui-icon ui-icon-arrowrefresh-1-n"></span>
<span class="ui-icon ui-icon-arrowrefresh-1-e"></span>
<span class="ui-icon ui-icon-arrowrefresh-1-s"></span>
<span class="ui-icon ui-icon-arrow-4"></span>
<span class="ui-icon ui-icon-arrow-4-diag"></span>
<span class="ui-icon ui-icon-newwin"></span>
<span class="ui-icon ui-icon-refresh"></span>
<span class="ui-icon ui-icon-shuffle"></span>
<span class="ui-icon ui-icon-transfer-e-w"></span>
<span class="ui-icon ui-icon-transferthick-e-w"></span>
<span class="ui-icon ui-icon-folder-collapsed"></span>
<span class="ui-icon ui-icon-folder-open"></span>
<span class="ui-icon ui-icon-document"></span>
<span class="ui-icon ui-icon-document-b"></span>
<span class="ui-icon ui-icon-note"></span>
<span class="ui-icon ui-icon-mail-closed"></span>
<span class="ui-icon ui-icon-mail-open"></span>
<span class="ui-icon ui-icon-suitcase"></span>
<span class="ui-icon ui-icon-comment"></span>
<span class="ui-icon ui-icon-person"></span>
<span class="ui-icon ui-icon-print"></span>
<span class="ui-icon ui-icon-trash"></span>
<span class="ui-icon ui-icon-locked"></span>
<span class="ui-icon ui-icon-unlocked"></span>
<span class="ui-icon ui-icon-bookmark"></span>
<span class="ui-icon ui-icon-tag"></span>
<span class="ui-icon ui-icon-home"></span>
<span class="ui-icon ui-icon-flag"></span>
<span class="ui-icon ui-icon-calculator"></span>
<span class="ui-icon ui-icon-cart"></span>
<span class="ui-icon ui-icon-pencil"></span>
<span class="ui-icon ui-icon-clock"></span>
<span class="ui-icon ui-icon-disk"></span>
<span class="ui-icon ui-icon-calendar"></span>
<span class="ui-icon ui-icon-zoomin"></span>
<span class="ui-icon ui-icon-zoomout"></span>
<span class="ui-icon ui-icon-search"></span>
<span class="ui-icon ui-icon-wrench"></span>
<span class="ui-icon ui-icon-gear"></span>
<span class="ui-icon ui-icon-heart"></span>
<span class="ui-icon ui-icon-star"></span>
<span class="ui-icon ui-icon-cancel"></span>
<span class="ui-icon ui-icon-plus"></span>
<span class="ui-icon ui-icon-plusthick"></span>
<span class="ui-icon ui-icon-minus"></span>
<span class="ui-icon ui-icon-minusthick"></span>
<span class="ui-icon ui-icon-close"></span>
<span class="ui-icon ui-icon-closethick"></span>
<span class="ui-icon ui-icon-key"></span>
<span class="ui-icon ui-icon-scissors"></span>
<span class="ui-icon ui-icon-copy"></span>
<span class="ui-icon ui-icon-contact"></span>
<span class="ui-icon ui-icon-image"></span>
<span class="ui-icon ui-icon-video"></span>
<span class="ui-icon ui-icon-script"></span>
<span class="ui-icon ui-icon-alert"></span>
<span class="ui-icon ui-icon-info"></span>
<span class="ui-icon ui-icon-notice"></span>
<span class="ui-icon ui-icon-help"></span>
<span class="ui-icon ui-icon-check"></span>
<span class="ui-icon ui-icon-bullet"></span>
<span class="ui-icon ui-icon-radio-off"></span>
<span class="ui-icon ui-icon-radio-on"></span>
<span class="ui-icon ui-icon-pin-w"></span>
<span class="ui-icon ui-icon-pin-s"></span>
<span class="ui-icon ui-icon-play"></span>
<span class="ui-icon ui-icon-pause"></span>
<span class="ui-icon ui-icon-seek-next"></span>
<span class="ui-icon ui-icon-seek-prev"></span>
<span class="ui-icon ui-icon-seek-end"></span>
<span class="ui-icon ui-icon-seek-first"></span>
<span class="ui-icon ui-icon-stop"></span>
<span class="ui-icon ui-icon-eject"></span>
<span class="ui-icon ui-icon-volume-off"></span>
<span class="ui-icon ui-icon-volume-on"></span>
<span class="ui-icon ui-icon-power"></span>
<span class="ui-icon ui-icon-signal-diag"></span>
<span class="ui-icon ui-icon-signal"></span>
<span class="ui-icon ui-icon-battery-0"></span>
<span class="ui-icon ui-icon-battery-1"></span>
<span class="ui-icon ui-icon-battery-2"></span>
<span class="ui-icon ui-icon-battery-3"></span>
<span class="ui-icon ui-icon-circle-plus"></span>
<span class="ui-icon ui-icon-circle-minus"></span>
<span class="ui-icon ui-icon-circle-close"></span>
<span class="ui-icon ui-icon-circle-triangle-e"></span>
<span class="ui-icon ui-icon-circle-triangle-s"></span>
<span class="ui-icon ui-icon-circle-triangle-w"></span>
<span class="ui-icon ui-icon-circle-triangle-n"></span>
<span class="ui-icon ui-icon-circle-arrow-e"></span>
<span class="ui-icon ui-icon-circle-arrow-s"></span>
<span class="ui-icon ui-icon-circle-arrow-w"></span>
<span class="ui-icon ui-icon-circle-arrow-n"></span>
<span class="ui-icon ui-icon-circle-zoomin"></span>
<span class="ui-icon ui-icon-circle-zoomout"></span>
<span class="ui-icon ui-icon-circle-check"></span>
<span class="ui-icon ui-icon-circlesmall-plus"></span>
<span class="ui-icon ui-icon-circlesmall-minus"></span>
<span class="ui-icon ui-icon-circlesmall-close"></span>
<span class="ui-icon ui-icon-squaresmall-plus"></span>
<span class="ui-icon ui-icon-squaresmall-minus"></span>
<span class="ui-icon ui-icon-squaresmall-close"></span>
<span class="ui-icon ui-icon-grip-dotted-vertical"></span>
<span class="ui-icon ui-icon-grip-dotted-horizontal"></span>
<span class="ui-icon ui-icon-gripsmall-diagonal-se"></span>
<span class="ui-icon ui-icon-grip-diagonal-se"></span>
</div>
<script type="text/javascript">
/* <![CDATA[ */
$(document).ready(function(){
	$(\'div#icons span.ui-icon\').each(
		function(){
			var t = $(this).attr(\'class\').split(\' \');
			$(this).before(\'<span class="ui-desc" style="float:left;width:200px;">\'+t[1]+\'</span>\').after(\'<span class="ui-helper-clearfix" style="float:none;"></span>\');
		}
	);
});  
/* ]]> */
</script>	
	';
	
	return $html;
}


/* Output */
public function js_packer(){

	$data = isset($_POST['devtool_source']) ? $_POST: null;
	
	if(!is_null($data)){
		$myjs = $data['devtool_source'];
		$encoding = $data['data_format'];
		$fast_decode = true;
		$special_char = false;

		$packer = new JavaScriptPacker($myjs, $encoding, $fast_decode, $special_char);
		$output = $packer->pack();
		
		return $output;
	}else{
		return $this->config['undefined_data'];
	}	
}


public function php_run_test(){

	$data = isset($_POST['devtool_source']) ? $_POST: null;
	
	if(!is_null($data) && mb_strlen($data['devtool_source'])>0){
		$myphp = $data['devtool_source'];
		$filename = System::hash($myphp).'.php';
		$full = $this->dataDir.'/phptests/'.$filename;
		System::fsFile($full, $myphp);
		
		//$output = file_get_contents($this->blankUrl.System::dataDir().$full);
		$output = $this->blankUrl.System::dataDir().$full;
		
		return $output;
	}else{
		return null;
	}
}

public function to_hash(){
	$data = isset($_POST['devtool_source']) ? $_POST: null;
	if(!is_null($data)){
		$value = $data['devtool_source']; 
		$hash = $data['data_format'];
		return hash($hash, $value);
	}
	return $this->config['undefined_data']; 	
}

public function date_to_int(){
	$data = isset($_POST['devtool_source']) ? $_POST: null;
	if(!is_null($data)){
		$date = (string)$data['devtool_source']; 
		return strtotime($date);
	}
	return $this->config['undefined_data']; 	
}

public function int_to_date(){
	$data = isset($_POST['devtool_source']) ? $_POST: null;
	if(!is_null($data)){
		$format = is_null($data['data_format']) || mb_strlen($data['data_format'])<1 ? $this->config['default_date_format']: $data['data_format'];
		$num = (int)$data['devtool_source']; 
		return date($format, (int)$num);
	}
	
	return $this->config['undefined_data']; 	
}

public function process_url(){
	$data = isset($_POST['devtool_source']) ? $_POST: null;
	if(!is_null($data)){
		$f = $data['data_format'];
		$url = $data['devtool_source']; 
		return function_exists($f) ? call_user_func($f, $url): $this->config['undefined_function'];
	}
 
  return $this->config['undefined_data']; 
}

public function crypt_performance_test(){
	
	@ob_flush();
	flush();
		$data = '';
	for ($i = 0; $i < 64000; $i++){
		$data .= hash('md5', rand(), true);
	}
	
	$html =  strlen($data) . ' bytes [ RND ] '.PHP_EOL.'Hash algorithms ...'.PHP_EOL.PHP_EOL.'';
	@ob_flush();
	flush();

	$results = array();
	$algos = hash_algos();
	sort($algos);
	
	foreach ($algos as $v) {
		$html .= $v . PHP_EOL;
		@ob_flush();flush();
		$time = microtime(true);
		hash($v, $data, false);
		$time = microtime(true) - $time;
		$results[$time * 1000000000][] = $v." (hex)";
		$time = microtime(true);
		hash($v, $data, true);
		$time = microtime(true) - $time;
		$results[$time * 1000000000][] = $v." (raw)";
	}

	ksort($results);

	$html .=  PHP_EOL . 'Results: ' . PHP_EOL;

	$i = 1;
	foreach ($results as $k => $v){
		foreach ($v as $k1 => $v1){
			$html .= '' . str_pad($i++ . '.', 4, ' ', STR_PAD_LEFT) . '  ' . str_pad($v1, 30, ' ') . ($k / 1000) . ' μs ' . PHP_EOL;
		}	
	}	
	
	return $html;
}

/* SYS */
public function devtool_php_dir_list(){
	
	$files = array();
	
	$md = opendir(System::fsDir($this->dataDir.'/phptests'));
	while(false!==($f = readdir($md))){
		
		if($f != '.' && $f != '..' && is_file(System::getFsFile($this->dataDir.'/phptests/'.$f)) ){
			$i = stat(System::getFsFile($this->dataDir.'/phptests/'.$f));
			
			$files[$i['mtime']] = '<div>
					<a class="file" href="'.$this->dataDir.'/phptests/'.$f.'" title="'.$f.'">'.date('Y-m-d-H-i-s', $i['mtime']).'.src.php  
						<span class="time">'.date(Lng::get('system/date_time_format_precise'), $i['mtime']).'</span>  
						<span class="size">'.System::fsFileSize($i['size']).'</span>
					</a>
					<a class="remove" href="'.$this->dataDir.'/phptests/'.$f.'">
					</a>
				</div>';
			
		}
	}
	krsort($files);
	return '<div class="php_sources">'.implode('', $files).'</div>';

}

public function get_php_source($path){
	return System::getFileData($path);
}

public function delete_php_source($path){
	return System::deleteFile($path);
}


}
?>
