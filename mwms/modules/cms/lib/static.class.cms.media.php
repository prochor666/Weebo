<?php
/**
* static.class..cms.media.php - WEEBO framework cms module lib.
*/

class CmsMedia{

final public function __construct() { throw new WeeboException("Can not instantiate static class!"); }
final public function __clone() { throw new WeeboException("Can not clone static class!"); }

public static function getCfg($var)
{
	$out = new CmsOutput;
	return $out->config[$var];
}

public static function getLng($var)
{
	$out = new CmsOutput;
	return $out->lng[$var];
}

public static function chooseGallery($id_content)
{
	$param = null;
	$cms = new Cms;
	$s = null;
	
	if($id_content > 0){
		$d = $cms->getContentData($id_content);
		$param = $d['display_script_param'];
	}
	
	$paramList = $cms->getDirList($type="images"); 
	
	if(count($paramList)>0){
		$s .= '<select id="param_select_method_data_'.$id_content.'" name="param_select_method_data" class="select meta_live_edit param_select_method_data">';
		foreach($paramList as $dirs){
			$c = count($cms->getDirMedia($dirs['id_dir']));
			$s .= '<option value="id_dir:'.$dirs['id_dir'].'" '.Validator::selected('id_dir:'.$dirs['id_dir'], $param).'>'.$dirs['title'].' ('.$c.')</option>';
		}
		$s .= '</select>';
	}
	
	return $s;
}

public static function chooseVideo($id_content)
{
	return 'Video';
}


}
?>
