<?php
/**
* static.class.mediamix.embed.custom.php - WEEBO framework cms module lib.
*/

class MediaMixEmbedCustom{

final public function __construct() { throw new WeeboException("Can not instantiate static class!"); }
final public function __clone() { throw new WeeboException("Can not clone static class!"); }

public static function getCfg($var)
{
	$out = new MediaMix;
	return $out->config[$var];
}

public static function getLng($var)
{
	$out = new MediaMix;
	return $out->lng[$var];
}

public static function getarticleItem1($token){
	
	$m = new MediaMixChannelTemplates;
	
	$a = $m->articleToken($token);
	
	if($a !== false){
		
		
		
		
		
	}
}

}

?>
