<?php
class NXMPlugins extends NxMarket {

public $plugin, $pluginLib;

public function __construct(){
	parent::__construct();
	$this->plugin = null;
	$this->pluginLib = null;
}

public function showPlugins(){
	$directory = $this->config['pluginDir'];
	$md = opendir(System::root().'/'.$directory);
	$html = '<select name="plugin" id="plugin" class="select"><option value="..">SELECT PLUGIN</option>';

	while(false!==($lib_id = readdir($md))){
		if($lib_id != "." && $lib_id != ".." && is_dir(System::root().'/'.$directory.'/'.$lib_id) && file_exists(System::root().'/'.$directory.'/'.$lib_id.'/index.php') && is_file(System::root().'/'.$directory.'/'.$lib_id.'/index.php')){
			$html .= '<option value="'.$lib_id.'"'.Validator::selected($lib_id, $this->plugin).'>'.$lib_id.'</option>';
		}
	}
	closedir($md);
	
	$html .= '</select>';
	
	return $html;
}

public function run(){
	$pluginInit = Registry::get('serverdata/root').'/'.$this->config['pluginDir'].'/'.$this->plugin;
	
	$content = 'NO PLUGIN LOADED';
	
	if( !is_null($this->plugin) && file_exists( $pluginInit ) && is_dir( $pluginInit ) && file_exists( $pluginInit.'/index.php' ) ){
		ob_start();
		require_once($pluginInit.'/index.php');
		$content = ob_get_contents();
		ob_end_clean();
	}
	
	return $content;
}


}
?>
