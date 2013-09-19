<?php
/**
* static.class.nxmarket.embed.php - WEEBO framework nxmakrket module lib.
*/

class NxMarketEmbed{

final public function __construct() { throw new WeeboException("Can not instantiate static class!"); }
final public function __clone() { throw new WeeboException("Can not clone static class!"); }

public static function getCfg($var)
{
	$out = new NxMarket;
	return $out->config[$var];
}

public static function getLng($var)
{
	$out = new NxMarket;
	return $out->lng[$var];
}

public static function chooseChannel($id_content)
{
	$param = null;
	$cms = new Cms;
	$rss = new NxMarket;
	$s = null;
	
	if($id_content > 0){
		$d = $cms->getContentData($id_content);
		$param = $d['display_script_param'];
	}
	
	$paramList = $rss->getCats(); 
	
	if(count($paramList)>0){
		$s = '<select id="param_select_method_data_'.$id_content.'" name="param_select_method_data" class="select meta_live_edit param_select_method_data">';
		foreach($paramList as $channels){
			$s .= '<option value="id_cat:'.$channels['id_cat'].'" '.Validator::selected('id_cat:'.$channels['id_cat'], $param).'>'.$channels['title'].'</option>';
		}
		$s .= '</select>';
	}
	
	return $s;
}

public static function array2xml($arr, $header = true){
	
	$xml = $header === true ? '<?xml version="1.0" encoding="UTF-8" ?><root>': null;
	
	foreach($arr as $k => $v){
		if(is_array($v)){
			$xml .= self::array2xml($v, false);
		}else{
			$xml .= '<'.$k.'>'.$v.'</'.$k.'>';
		}
	}
	
	return $xml.'</root>';
}




}
?>
