<?php
/**
* static.class.nxmarket.api.php - WEEBO framework nxmakrket module lib.
*/

class  NXMApi{

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

public static function loadPlugin(){
	
	$plugin = isset($_GET['plugin']) && $_GET['plugin'] != '..' ? $_GET['plugin']: null;
	$pluginContent = null;

	$p = new NXMPlugins;

	if( !is_null($plugin) ){
		$p->plugin = $plugin;
		$pluginContent = $p->run(); 
	}
	
	return $pluginContent;
}


}
?>
