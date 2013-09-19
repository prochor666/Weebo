<?php
/**
* static.class.render.php - WEEBO framework cms module lib.
*/

class WebRender{

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

public static function chef($a, $k, $d = false)
{
	return is_array($a) && array_key_exists($k, $a) ? $a[$k]: $d;
}

public static function links($config = array())
{
	$id_position = self::chef($config, 'id_position', 0);
	$id_link = self::chef($config, 'id_link', 0);
	$domain = self::chef($config, 'domain', 'www');
	$level = self::chef($config, 'level', 1);
	$cssClass = self::chef($config, 'cssClass', null);
	$lockOnLevel = self::chef($config, 'lockOnLevel', false);
	
	$html = null;
	$out = new CmsOutput;
	$qq = $out->getLinks($id_link, $id_position, $domain);
	
	if(count($qq)>0){
	
		$uc = $level > 1 ? 'dropdown-menu': 'nav';
		$uc = !is_null($cssClass) ? $uc.' '.$cssClass : $uc;
		
		$html .= '
		<ul class="'.$uc.'">';
		foreach($qq as $d){
			
			$title = mb_strlen($d['link_title'])>0 ? $d['link_title']: $d['title'];
			$url = mb_strlen($d['ext_link'])>0 ? $d['ext_link']: Registry::get('serverdata/site').'/'.$d['textmap'].'/';
			$target = $d['id_blank'] == 1 ? ' target="_blank"': null;
			
			$subHtml = $lockOnLevel === false ? self::links(array(
				'id_position' => $id_position,
				'id_link' => $d['id_link'],
				'domain' => $domain,
				'level' => $level + 1,
				'cssClass' => null,
				'lockOnLevel' => $lockOnLevel
			)): null;
			
			$toggler = null;
			$cToggler = null;
			
			if(mb_strlen($subHtml)>0){
				$toggler = $level == 1 && $d['id_sub'] == 0 ? ' data-toggle="dropdown"': null;
				$cToggler = $level == 1 && $d['id_sub'] == 0 ? ' dropdown-toggle': null;
			}
			
			$actualClass = $d['textmap'] == __CMS_MAP__ ? ' class="dropdown menu-item menu-item-'.$d['id_link'].' menu-item-actual active"': ' class="dropdown menu-item level-'.$level.' menu-item-'.$d['id_link'].'"'; 
			$actualLinkClass = $d['textmap'] == __CMS_MAP__ ? ' class="menu-item-link menu-item-link-'.$d['id_link'].' menu-item-actual-link '.$cToggler.'"': ' class="menu-item-link menu-item-link-'.$d['id_link'].' '.$cToggler.'"'; 
			
			$html .= '
			<li'.$actualClass.'><a href="'.$url.'" '.$target.' title="'.$title.'"'.$actualLinkClass.$toggler.'>'.$d['title'].'</a>
				'.$subHtml.'
			</li>';

		}
		$html .= '</ul>';
	}
	
	return $html;
}


}
?>
