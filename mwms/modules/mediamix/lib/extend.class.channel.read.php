<?php
class MediaMixChannel extends MediaMix{

public $e, $timeout;
protected $f;

public function __construct(){
	parent::__construct();
	$this->lng = Lng::get('mediamix');
	$this->config = Registry::get('moduledata/mediamix');
	$this->cacheDir = _GLOBALCACHEDIR_;
	$this->timeout = 3600;
}

public function isDetail(){
	$url = $_SERVER['REQUEST_URI'];
	$urlset = explode('/', $url);
	$detailID = end($urlset);
	return $detailID == __CMS_MAP__ || mb_strlen($detailID)<1 ? false: $detailID;
}

public function listAllChannels(){
	
	$q = "SELECT * FROM "._SQLPREFIX_."_mm_sources WHERE id_public = 1 ORDER BY public_order, title";
	$qq = Db::result($q);
	$html = null;
	$ids = array();
	
	foreach($qq as $d)
	{
		$lifeTime = time() - $this->config['dumpLifetime'];
		if($lifeTime > $d['last_update'])
		{
			$i = $this->saveChannel($d);
		}
		$html .= $this->listChannel($d);
	}

	return $html;
}

public function dumpAllChannels(){
	
	$q = "SELECT * FROM "._SQLPREFIX_."_mm_sources WHERE id_public = 1 ORDER BY public_order, title";
	$qq = Db::result($q);
	
	$html = null;
	foreach($qq as $d)
	{
		$html .= $this->saveChannel($d);
	}
	
	return $html;
}

public function loadChannelBy($name = 'id_source', $data = 0, $operator = '='){
	$q = "SELECT * FROM "._SQLPREFIX_."_mm_sources WHERE ".$name." ".$operator." ".$data." AND id_public = 1 LIMIT 1";
	$qq = Db::result($q);
	$html = null;
	
	if(count($qq) == 1)
	{
		$d = $qq[0];
		$i = $this->saveChannel($d);
		$html .= $this->listChannel($d);
	}
	
	return $html;
}

public function loadChannelArticle($token, $name = 'id_source', $data = 0, $operator = '='){
	$q = "SELECT * FROM "._SQLPREFIX_."_mm_sources WHERE ".$name." ".$operator." ".$data." AND id_public = 1 LIMIT 1";
	$qq = Db::result($q);
	$html = null;
	
	if(count($qq) == 1)
	{
		$id_article = end(explode('-', $token));
		
		$d = $qq[0];
		$html .= $this->articleDetail($d, (int)$id_article);
	}
	
	return $html;
}

public function exportChannelBy($name = 'id_source', $data = 0, $operator = '='){
	$q = "SELECT * FROM "._SQLPREFIX_."_mm_sources WHERE ".$name." ".$operator." '".$data."' AND id_public = 1 LIMIT 1";
	$qq = Db::result($q);
	$e = array('Status' => 'Error', 'Message' => 'Invalid source, SQL error');
	
	if(count($qq) == 1)
	{
		$d = $qq[0];
		$e = $this->exportChannel($d);
	}
	
	return $e;
}

public function exportChannelByTemplate($template){
	$q = "SELECT * FROM "._SQLPREFIX_."_mm_sources WHERE template IN('".implode(",'", explode(',',$template) )."') AND id_public = 1 ORDER BY last_played, public_order DESC, id_source LIMIT 1";
	$qq = Db::result($q);
	$e = array('Status' => 'Error', 'Message' => 'Invalid template, not found');
	
	if(count($qq) == 1)
	{
		$d = $qq[0];
		
		$timeSpec = time() - $d['last_played'];
		
		if($timeSpec > $this->timeout  && $d['last_played'] > 0)
		{
			Db::query("UPDATE "._SQLPREFIX_."_mm_sources SET last_played = 0 ");
			$q = "SELECT * FROM "._SQLPREFIX_."_mm_sources WHERE template IN('".implode(",'", explode(',',$template) )."') AND id_public = 1 ORDER BY last_played, public_order DESC, id_source LIMIT 1";
			$qq = Db::result($q);
			$d = $qq[0];
		}
		
		$e = $this->exportChannelLast($d);
	}
	
	return $e;
}

/*
 * 
 * API
 * 
 * */
public function exportChannelLast($d){
	
	$out = array('Status' => 'Empty', 'Message' => 'No public data');
	
	$q = "SELECT * FROM "._SQLPREFIX_."_mm_articles WHERE id_public = 1 ORDER BY last_played, public_order DESC, date_public, id_article LIMIT 1";
	$qq = Db::result($q);
	
	if(count($qq) > 0)
	{ 
		$a = $qq[0];
		
		$timeSpec = time() - $a['last_played'];
		
		if($timeSpec > $this->timeout && $a['last_played'] > 0)
		{
			Db::query("UPDATE "._SQLPREFIX_."_mm_articles SET last_played = 0 ");
			Db::query("UPDATE "._SQLPREFIX_."_mm_sources SET last_played = 0 ");
			
			$q = "SELECT * FROM "._SQLPREFIX_."_mm_articles WHERE id_public = 1 ORDER BY last_played, public_order DESC, date_public, id_article LIMIT 1";
			$qq = Db::result($q);
			$a = $qq[0];
		}
		
		$method = $this->lng['mwms_source_templates'][$d['template']]['methodExport'];
		$out = call_user_func_array( array('MediaMixChannelTemplates', $method), array($qq) );
		
		Db::query("UPDATE "._SQLPREFIX_."_mm_articles SET last_played = ".time()." WHERE id_article = ".$a['id_article'] );
		Db::query("UPDATE "._SQLPREFIX_."_mm_sources SET last_played = ".time()." WHERE id_source = ".$d['id_source'] );
	}

	return $out;
}

public function exportChannel($d){
	
	$out = array('Status' => 'Empty', 'Message' => 'No data in source');
	
	$q = "SELECT * FROM "._SQLPREFIX_."_mm_articles WHERE id_source = ".$d['id_source']." AND id_public = 1 ORDER BY date_public DESC";
	$qq = Db::result($q);
	
	if(count($qq) > 0)
	{ 
		$method = $this->lng['mwms_source_templates'][$d['template']]['methodExport'];
		$out = call_user_func_array( array('MediaMixChannelTemplates', $method), array($qq) );
	}

	return $out;
}


public function listChannel($d){
	
	$html = null;
	
	$q = "SELECT "._SQLPREFIX_."_mm_articles.*, "._SQLPREFIX_."_mm_sources.template, "._SQLPREFIX_."_mm_sources.id_type, "._SQLPREFIX_."_mm_sources.title AS sourceTitle FROM "._SQLPREFIX_."_mm_articles INNER JOIN "._SQLPREFIX_."_mm_sources ON "._SQLPREFIX_."_mm_articles.id_source = "._SQLPREFIX_."_mm_sources.id_source WHERE "._SQLPREFIX_."_mm_sources.id_public = 1 AND "._SQLPREFIX_."_mm_articles.id_source = ".$d['id_source']." AND "._SQLPREFIX_."_mm_articles.id_public = 1 ORDER BY date_public DESC";
	//$q = "SELECT * FROM "._SQLPREFIX_."_mm_articles WHERE id_source = ".$d['id_source']." AND id_public = 1 ORDER BY date_public DESC";
	$qq = Db::result($q);
	
	if(count($qq)>0)
	{ 
		
		$method = $this->lng['mwms_source_templates'][$d['template']]['methodRead'];
		$html .= call_user_func_array( array('MediaMixChannelTemplates', $method), array($d, $qq) );
	}

	return $html;
}

public function articleDetail($d, $id_article){

	$html = null;
	
	$q = "SELECT "._SQLPREFIX_."_mm_articles.*, "._SQLPREFIX_."_mm_sources.template, "._SQLPREFIX_."_mm_sources.id_type, "._SQLPREFIX_."_mm_sources.title AS sourceTitle FROM "._SQLPREFIX_."_mm_articles INNER JOIN "._SQLPREFIX_."_mm_sources ON "._SQLPREFIX_."_mm_articles.id_source = "._SQLPREFIX_."_mm_sources.id_source WHERE "._SQLPREFIX_."_mm_sources.id_public = 1 AND "._SQLPREFIX_."_mm_articles.id_source = ".$d['id_source']." AND "._SQLPREFIX_."_mm_articles.id_public = 1 AND id_article = '".(int)$id_article."' ORDER BY date_public DESC";
	//$q = "SELECT * FROM "._SQLPREFIX_."_mm_articles WHERE id_source = ".$d['id_source']." AND id_public = 1 ORDER BY date_public DESC";
	$qq = Db::result($q);
	
	if(count($qq)>0)
	{ 
		
		$method = $this->lng['mwms_source_templates'][$d['template']]['methodDetailRead'];
		$html .= call_user_func_array( array('MediaMixChannelTemplates', $method), array($d, $qq) );
	}

	return $html;
}

public function saveChannel($d){
	
	$lifeTime = time() - $this->config['dumpLifetime'];
	
	if($lifeTime < $d['last_update'])
	{
		return 0;
	}else{
		if($d['id_archive'] == 0){
			Db::query("DELETE FROM "._SQLPREFIX_."_mm_articles WHERE id_source = '".(int)$d['id_source']."' ");
		}
	}
	
	$xml = $this->loadChannelData($d['data']);
	
	if(!is_null($xml) && $xml !== false && is_object($xml)){
		$method = $this->lng['mwms_source_templates'][$d['template']]['methodSave'];
		call_user_func_array( array('MediaMixChannelTemplates', $method), array($d, $xml) );
	}
}

protected function saveResourceLink($d, $url){
	
	$localFile = null;
	
	$r = @file_get_contents( $url );
	
	if($r !== false){
		
		$aDir = $this->config['rssData'].'/'.$d['id_source'].'/'.$d['article_dir'];
		
		Storage::makeDir($aDir);
		
		$localFile = $aDir.'/'.basename($url);
		file_put_contents( Registry::get('serverdata/root').'/'.$aDir.'/'.basename($url), $r );
	}
	
	return $localFile;
}

public function articleToken($token){
	$q = "SELECT * FROM "._SQLPREFIX_."_mm_articles WHERE token LIKE '".$token."' LIMIT 1";
	$qq = Db::result($q);
	return count($qq) == 1 ? $qq[0]: false;
}

protected function articleExists($token){
	$q = "SELECT * FROM "._SQLPREFIX_."_mm_articles WHERE token LIKE '".$token."' LIMIT 1";
	$qq = Db::result($q);
	return count($qq) == 1 ? true: false;
}

protected function valid($o){
	return is_object($o) && (mb_strlen((string)$o)>0 || count($o)>0) ? true: false;
}

protected function loadRssLink($link){
	return @file_get_contents($link);
}

protected function loadChannelData($link){
	$rssFile = System::hash($link).'.xml';
	$xml = simplexml_load_string(@Storage::cacheRead($rssFile));
	
	if(Storage::isExpired($rssFile, 43200) === true || is_null($xml) || !is_object($xml))
	{
		$rssData = $this->loadRssLink($link);
		if($rssData !== false && mb_strlen($rssData)>100)
		{
			$rssData = Storage::cacheRewrite($rssFile, $rssData);
			$xml = @simplexml_load_string($rssData);
		}
	}
	
	return $xml; 
}


}
