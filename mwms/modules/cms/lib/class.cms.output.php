<?php
/**
* class.cms.output.php - WEEBO framework cms module lib.
*/

class CmsOutput{

public $lng, $config, $cmsDomainKey, $updateInfoReleaseTime;

public function __construct(){
	$this->lng = Lng::get('cms') + Lng::get('system');
	$this->config = Registry::get('moduledata/cms');
	$this->cmsDomainKey = $this->getDomainKey(_CMS_DOMAIN_);
	$this->config['updateInfoReleaseTime'] = 86400;
}

public function releaseTemplate(){
	
	$data = array(
		'template' => Registry::get('serverdata/root').'/public/templates/'.__CMS_PAGE_TEMPLATE__.'/index.php',
		'collection' => $this
	);
	
	return $data;
}

public function parseRwUrl($url){
	return parse_url($url);
}

public function getDomainPrefix(){
	
}

public function getDomainKey($dom){
	foreach($this->lng['cms_public_domains'] as $domainKey => $data){
		if($data['name'] == $dom){
			return $domainKey;
		}
	}
	return -1;
}

public function getContentByView($display_script){
	$q = "SELECT * FROM "._SQLPREFIX_."_cms_content 
		LEFT JOIN "._SQLPREFIX_."_cms_links 
		ON "._SQLPREFIX_."_cms_content.id_link = "._SQLPREFIX_."_cms_links.id_link 
		WHERE "._SQLPREFIX_."_cms_content.display_script LIKE '".Db::escapeField(trim($display_script))."' 
		AND domain LIKE '"._CMS_DOMAIN_."' 
		AND lng LIKE '".__CMS_PAGE_LNG__."' 
		ORDER BY id_sticky, "._SQLPREFIX_."_cms_content.id_link, "._SQLPREFIX_."_cms_content.public_order 
		LIMIT 1 
		";
	//$qq = Db::result($q);
	$qq = Db::memAuto($q, 10);
	return count($qq)>0 ? $qq: array();
}

public function getHome($lng){
	if(array_key_exists($this->cmsDomainKey, $this->lng['cms_public_domains']) && is_array($this->lng['cms_public_domains'][$this->cmsDomainKey]))
	{
		$q = "SELECT * FROM "._SQLPREFIX_."_cms_links WHERE lng LIKE '".Db::escapeField(trim($lng))."' AND LENGTH(ext_link)<1 AND id_sub = 0 AND domain LIKE '"._CMS_DOMAIN_."' AND id_position = '".key($this->lng['cms_public_domains'][$this->cmsDomainKey]['cms_public_positions'])."' ORDER BY public_order, id_link LIMIT 1";
		//$qq = Db::result($q);
		$qq = Db::memAuto($q, 20);
		return count($qq)>0 ? $qq: array();
	}else{
		return array();
	}
}

public function getXmlSitemap($id_link, $domain){
	$q = "SELECT * FROM "._SQLPREFIX_."_cms_links WHERE id_public = 1 AND id_sub = '".(int)$id_link."' AND LENGTH(ext_link)<1 AND domain LIKE '".Db::escapeField($domain)."' AND id_sitemap = 1 ORDER BY lng, id_position, public_order, id_link";
	//$qq = Db::result($q);
	$qq = Db::memAuto($q, 10);
	return count($qq)>0 ? $qq: array();
}

public function getSitemap($id_link, $domain, $lng){
	$q = "SELECT * FROM "._SQLPREFIX_."_cms_links WHERE id_public = 1 AND id_sub = '".(int)$id_link."' AND domain LIKE '".Db::escapeField($domain)."' AND id_sitemap = 1 AND lng LIKE '".Db::escapeField(trim($lng))."' ORDER BY id_position, public_order, id_link";
	//$qq = Db::result($q);
	$qq = Db::memAuto($q, 20);
	return count($qq)>0 ? $qq: array();
}

public function getLinkID($textmap){
	$q = "SELECT * FROM "._SQLPREFIX_."_cms_links WHERE textmap LIKE '".Db::escapeField(trim($textmap))."' AND domain LIKE '"._CMS_DOMAIN_."' AND id_public = 1 LIMIT 1";
	//$qq = Db::result($q);
	$qq = Db::memAuto($q, 10);
	Registry::set('cms_page_exists', 1);
	
	if(count($qq)<1 || is_null($textmap)){
		Registry::set('cms_page_exists', 0);
	}
	
	return count($qq)==1 ? $qq[0]: null;
}

public function getLinks($id_sub = 0, $id_position = 1, $domain = _CMS_DOMAIN_){
	$q = "SELECT * FROM "._SQLPREFIX_."_cms_links WHERE id_sub = '".(int)$id_sub."' AND id_public = '1' AND id_menu = '1' AND id_position = '".(int)$id_position."' AND domain LIKE '".Db::escapeField($domain)."' ORDER BY public_order, id_link ";
	//$qq = Db::result($q);
	$qq = Db::memAuto($q, 10);
	return count($qq)>0 ? $qq: array();
}

public function getLinkData($id_link){
	$q = "SELECT * FROM "._SQLPREFIX_."_cms_links WHERE id_link = '".(int)$id_link."' ";
	//$qq = Db::result($q);
	$qq = Db::memAuto($q, 10);
	return count($qq)>0 ? $qq[0]: array();
}

public function getLinkContentData($id_link){
	$q = "SELECT * FROM "._SQLPREFIX_."_cms_content WHERE id_link = '".(int)$id_link."' ";
	//$qq = Db::result($q);
	$qq = Db::memAuto($q, 10);
	return count($qq)>0 ? $qq: array();
}

public function getContentData($id_content){
	$q = "SELECT * FROM "._SQLPREFIX_."_cms_content WHERE id_content = '".(int)$id_content."' ";
	//$qq = Db::result($q);
	$qq = Db::memAuto($q, 10);
	return count($qq)>0 ? $qq[0]: array();
}

public function getContentDataByTextmap($id_link, $textmap){
	
	$hashDocument = explode("-", $textmap);
	$hdl = count($hashDocument) - 1;
	$hash = $hashDocument[$hdl];
	
	$q = "SELECT * FROM "._SQLPREFIX_."_cms_content WHERE id_link = '".(int)$id_link."' AND textmap LIKE '%-".Db::escapeField($hash)."' ";
	//$qq = Db::result($q);
	$qq = Db::memAuto($q, 10);
	return count($qq)>0 ? $qq[0]: array();
}

public function getContentDataByTextmapPublished($id_link, $textmap, $preview = false){
	
	$hashDocument = explode("-", $textmap);
	$hdl = count($hashDocument) - 1;
	$hash = $hashDocument[$hdl];
	$sqlif = $preview === true ? null: " AND id_public = 1 AND date_public <= ".time()." ";
	$q = "SELECT * FROM "._SQLPREFIX_."_cms_content WHERE id_link = '".(int)$id_link."' ".$sqlif." AND textmap LIKE '%-".Db::escapeField($hash)."' ";
	//$qq = Db::result($q);
	$qq = Db::memAuto($q, 10);
	return count($qq)>0 ? $qq[0]: array();
}

}
?>
