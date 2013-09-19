<?php
/* GOOGLE sitemap */
class CmsXmlSitemap extends CmsOutput{

/* CONSTRUCT */
public function __construct(){
	$this->_parent = 0;
}

/* HUMAN OUTPUT, homepage, navigation level 1 */
public function build_sitemap(){
	
	header('Content-Type: application/xml; charset=UTF-8');
	
	$xml = '<'.'?xml version="1.0" encoding="utf-8"?'.'>
		<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">
	';
	
	$__PRECACHE = $this->getXmlSitemap($this->_parent, _CMS_DOMAIN_);
	
	if(count($__PRECACHE)>0){ 

		foreach($__PRECACHE as $db){
			$priority = '1.0';
			$link = Registry::get('serverdata/site').'/'.$db['textmap']; 
			$lastmod = $db['date_upd'] > 0 && $db['date_upd']>=$db['date_ins'] ? date('Y-m-d', $db['date_upd']): date('Y-m-d', $db['date_ins']);
			
			$xml .= '
			<url>
				<loc>'.$link.'</loc>
				<changefreq>daily</changefreq>
				<priority>'.$priority.'</priority>
				<lastmod>'.$lastmod.'</lastmod>
			</url>
			';
			$xml .= $this->check_sitemap_page_content($db['id_link'],$db['textmap'],$db['default_order']);
			$xml .= $this->check_sitemap_leveling($db['id_link']);
		} 
	}
	 
	$xml .='</urlset>';
return $xml;
} 


/* HUMAN OUTPUT, links, navigation */
private function check_sitemap_leveling($parent){

	$__PRECACHE = $this->getXmlSitemap($parent, _CMS_DOMAIN_);
	
	$xml = null;			
	
	if(count($__PRECACHE)>0){ 
				
		foreach($__PRECACHE as $db){
			$priority = '0.9';
			$link = Registry::get('serverdata/site').'/'.$db['textmap']; 
			$lastmod = $db['date_upd'] > 0 && $db['date_upd']>=$db['date_ins'] ? date('Y-m-d', $db['date_upd']): date('Y-m-d', $db['date_ins']);
			
			$xml .= '
			<url>
				<loc>'.$link.'</loc>
				<changefreq>daily</changefreq>
				<priority>'.$priority.'</priority>
				<lastmod>'.$lastmod.'</lastmod>
			</url>
			';
			$xml .= $this->check_sitemap_page_content($db['id_link'],$db['textmap'],$db['default_order']);
			$xml .= $this->check_sitemap_leveling($db['id_link']);
		} 
	 }
	 
	return $xml;
} 

/* HUMAN OUTPUT, articles, posts */
private function check_sitemap_page_content($parent,$page,$order){

	$q="SELECT * FROM "._SQLPREFIX_."_cms_content WHERE id_link='".(int)$parent."' AND ext_link = '' AND id_public=1 AND id_brief_level IN(1,2) AND date_public <= ".time()." ";
	$q.= " ORDER BY ".$order;
	$__PRECACHE = Db::result($q);
	
	$xml = null;
	
	if(count($__PRECACHE)>0){ 
				
			foreach($__PRECACHE as $db){
				
				$priority = '0.7';
				$link = Registry::get('serverdata/site').'/'.$page.'/'.$db['textmap'].'.html'; 
				
				$lastmod = $db['date_upd'] > 0 && $db['date_upd']>=$db['date_ins'] ? date('Y-m-d', $db['date_upd']): date('Y-m-d', $db['date_ins']);
				
				$xml .= '
				<url>
					<loc>'.$link.'</loc>
					<changefreq>daily</changefreq>
					<priority>'.$priority.'</priority>
					<lastmod>'.$lastmod.'</lastmod>
				</url>
				';
			} 
	 }
	 
	return $xml;
}
 
}
?>
