<?php
/* RSS 2.0 creator */
class RSSManager extends CmsOutput{
	
	private $_source_page;
	
	/* CONSTRUCT */
	public function __construct(){
		parent::__construct();
		Render::definePage();
		$this->_source_page = __CMS_PAGE_ID__;
	}
	
	/* HUMAN OUTPUT */
	public function outputRSS(){
		
		if(__CMS_PAGE_RSS__ == 1)
		{
		
			$q = "SELECT * FROM "._SQLPREFIX_."_cms_content WHERE id_link = '".(int)$this->_source_page."' AND id_public = 1 AND id_rss = 1 AND id_brief_level IN(0,1,2) AND date_public <= ".__CMS_LTIME__." ";
			//$q.= Registry::get('userdata/logged_in') === 1 ? " AND secure IN(0,1) ": " AND secure IN(0,2) ";
			$q.=" AND CASE id_date_restrict WHEN 1 THEN publish_date_from <= ".__CMS_LTIME__." WHEN 0 THEN publish_date_from>-1 END";
			$q.=" AND CASE id_date_restrict WHEN 1 THEN publish_date_to >= ".__CMS_LTIME__." WHEN 0 THEN publish_date_to>-1 END";
			$q.=  " ORDER BY ".__CMS_PAGE_DEFAULT_ORDER__." LIMIT 0, ".Db::escapeField($this->config['rss_default']);

			$qq = Db::result($q);
			
			$key = $this->lng['cms_public_domains'][$this->getDomainKey(_CMS_DOMAIN_)]['title'];
			
			header('Content-Type: application/xml; charset=UTF-8');
			
			$xml = '
					<'.'?xml version="1.0" encoding="utf-8"?'.'>
					<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
						<channel>
							<title>'.__CMS_PAGE_TITLE__.' / '.$key.'</title>
							<link>'.Registry::get('serverdata/site').'/'.__CMS_MAP__.'/</link>
							<atom:link href="'.Registry::get('serverdata/site').'/'.__CMS_MAP__.'/rss.xml" rel="self" type="application/rss+xml" />
							<description>'.__CMS_PAGE_DESCRIPTION__.'</description>
							<language>'.__CMS_PAGE_LNG__.'</language>
							<generator>'._PRODUCTNAME_.' '._PRODUCTVERSION_.'</generator>
							<ttl>20</ttl>
				';
			
			if(count($qq)>0){ 
						
					foreach($qq as $d){
					
						$ob_title = strlen($d['title'])>0 ? $d['title']: null;
						$ob_anote = mb_strlen($d['annotation_text'])>0 ? $d['annotation_text'] : $d['content'];
						$ob_link = mb_strlen($d['ext_link'])>0 ? $d['ext_link']: Registry::get('serverdata/site').'/'.__CMS_MAP__.'/'.$d['textmap'].'.html';
						
						$xml .= strlen($ob_title)>0 ? '
							<item>
								<title>'.$ob_title.'</title>
								<link>'.$ob_link.'</link>
								<description><![CDATA[ '.mb_substr(strip_tags($ob_anote), 0, $this->config['brief_size']).' ]]></description>
								<pubDate>'.date('r', $d['date_public']).'</pubDate>
								<guid isPermaLink="true">'.$ob_link.'</guid>
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
		
		return 'RSS disabled';
	} 
	   
	
	private function rss_webmaster(){
		return '<webmaster>'.$this->lng['mwms_cms_site_author'].'</webmaster>';
	}   
	

}
?>
