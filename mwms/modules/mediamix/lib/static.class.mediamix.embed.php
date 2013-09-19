<?php
/**
* static.class.mediamix.embed.php - WEEBO framework cms module lib.
*/

class MediaMixEmbed{

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

public static function chooseChannel($id_content)
{
	$param = null;
	$cms = new Cms;
	$rss = new MediaMix;
	$s = null;
	
	if($id_content > 0){
		$d = $cms->getContentData($id_content);
		$param = $d['display_script_param'];
	}
	
	$paramList = $rss->getActiveSources(); 
	
	if(count($paramList)>0){
		$s = '<select id="param_select_method_data_'.$id_content.'" name="param_select_method_data" class="select meta_live_edit param_select_method_data">';
		foreach($paramList as $channels){
			$s .= '<option value="id_source:'.$channels['id_source'].'" '.Validator::selected('id_source:'.$channels['id_source'], $param).'>'.$channels['title'].'</option>';
		}
		$s .= '</select>';
	}
	
	return $s;
}

public static function rssDbSaveAll()
{
	$rl = New MediaMixChannelTemplates;
	$rl->dumpAllChannels();
}

public static function apiLastItem($template = null)
{
	$result = array('Status' => 'Error', 'Message' => 'Invalid template, no template set');
	
	if(!is_null($template) && mb_strlen($template)>0)
	{
		$rl = New MediaMixChannelTemplates;
		$result = $rl->exportChannelByTemplate($template);
	}
	
	return json_encode($result);
}

public static function api($id_source = 0, $format = 'plain')
{
	$result = $format == 'json' ? json_encode( array('Status' => 'Error', 'Message' => 'Invalid source, zero error') ): 'Status: Error<br />Message: Invalid source, zero error';
	
	if((int)$id_source > 0)
	{
		$rl = New MediaMixChannelTemplates;
		$result = $rl->exportChannelBy('id_source', $id_source);
	}
	
	switch($format){
		case 'json':
			$result = json_encode($result);
		break; case 'xml':
			$result = is_array($result) ? self::array2xml($result): '<?xml version="1.0" encoding="UTF-8" ?><root>'.$result.'</root>';
		break; default:
			
	}
	
	return $result;
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

/* XML OUTPUT */
public function outputRSS(){
	
	$q = "
		SELECT *, 
			"._SQLPREFIX_."_mm_articles.title AS feedLineTitle, 
			"._SQLPREFIX_."_mm_articles.date_public AS feedLineDate, 
			"._SQLPREFIX_."_mm_articles.data AS feedLineText,
			 "._SQLPREFIX_."_mm_articles.link AS feedLineLink 
		FROM "._SQLPREFIX_."_mm_articles
		LEFT JOIN "._SQLPREFIX_."_mm_sources 
		ON "._SQLPREFIX_."_mm_sources.id_source = "._SQLPREFIX_."_mm_articles.id_source
		WHERE ("._SQLPREFIX_."_mm_sources.id_public = 1 AND "._SQLPREFIX_."_mm_articles.id_public = 1) OR ("._SQLPREFIX_."_mm_articles.id_source = 0 AND "._SQLPREFIX_."_mm_articles.id_public = 1)
		ORDER BY date_public DESC
	";
	$qq = Db::result($q);

	header('Content-Type: application/xml; charset=UTF-8');
	
	$xml = '
			<'.'?xml version="1.0" encoding="utf-8"?'.'>
			<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
				<channel>
					<title>'.self::getLng('feedTitle').'</title>
					<link>'.self::getLng('feedLink').'</link>
					<atom:link href="'.self::getLng('feedLink').'" rel="self" type="application/rss+xml" />
					<description>'.self::getLng('feedDescription').'</description>
					<language>'.self::getLng('feedLng').'</language>
					<generator>'._PRODUCTNAME_.' '._PRODUCTVERSION_.'</generator>
					<ttl>20</ttl>
		';
	
	if(count($qq)>0){ 
				
			foreach($qq as $d)
			{
				$_title = htmlspecialchars($d['feedLineTitle']);
				$_text = $d['feedLineText'];
				$_link = htmlspecialchars($d['feedLineLink']);
				
				$xml .= mb_strlen($_title)>0 ? '
					<item>
						<title>'.$_title.'</title>
						<link>'.$_link.'</link>
						<description><![CDATA[ '.$_text.' ]]></description>
						<pubDate>'.date('r', $d['feedLineDate']).'</pubDate>
						<guid isPermaLink="true">'.$_link.'</guid>
					</item>
				': null;
			 } 
	 }
	 
	$xml .='
				</channel>
			</rss>
			';
	return trim($xml);
	
} 


}
?>
