<?php
/**
* static.class.cms.mailer.php - WEEBO framework cms module lib.
*/

class CmsMailer{

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

public static function chooseMailForm($id_content)
{
	$s = null;
	$param = null;
	$cms = new Cms;
	
	if($id_content > 0){
		$d = $cms->getContentData($id_content);
		$param = $d['display_script_param'];
	}
	
	$paramList = $cms->getForms(); 
	
	if(count($paramList)>0){
		$s .= '<select id="param_select_method_data_'.$id_content.'" name="param_select_method_data" class="select meta_live_edit param_select_method_data">';
		foreach($paramList as $form){
			$s .= '<option value="id_form:'.$form['id_form'].'" '.Validator::selected('id_form:'.$form['id_form'], $param).'>'.$form['title'].'</option>';
		}
		$s .= '</select>';
	}
	
	return $s;
}

}
?>
