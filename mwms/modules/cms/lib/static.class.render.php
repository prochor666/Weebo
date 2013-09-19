<?php
/**
* static.class.render.php - WEEBO framework cms module lib.
*/

class Render{

final public function __construct() { throw new WeeboException("Can not instantiate static class!"); }
final public function __clone() { throw new WeeboException("Can not clone static class!"); }

public static function getCfg($var){
	$out = new CmsOutput;
	return $out->config[$var];
}

public static function getLng($var){
	$out = new CmsOutput;
	return $out->lng[$var];
}

public static function urlInfo(){
	$newLink = array();
	
	$homeCount = mb_strlen(Registry::get('serverdata/site').'/');
	
	$xprotocol = isset($_SERVER['HTTPS']) && !empty($_SERVER['HTTPS']) ? 'https://': 'http://';
	$xport = isset($_SERVER["SERVER_PORT"]) && $_SERVER["SERVER_PORT"] != '80' && $_SERVER["SERVER_PORT"] != '443' ? ':'.$_SERVER["SERVER_PORT"]: null;
	$xsystem_root = $xprotocol.$_SERVER['SERVER_NAME'].$xport;
	$xsystem_root = mb_substr($xsystem_root, -1, 1, 'UTF-8') === '/' ? mb_substr($xsystem_root, 0, -1, 'UTF-8'): $xsystem_root;
	
	$url = $xsystem_root.$_SERVER['REQUEST_URI'];
	
	$RU = mb_substr($url, (int)$homeCount, mb_strlen($url));
	
	$urlset = parse_url($RU);
	
	$x = explode('/', $urlset['path']);
	
	if(count($x)==0 || (count($x)>0 && mb_strlen($x[0])<1) ){
		array_push($newLink, null);
	}else{
		foreach($x as $value){
			if(!is_null($value) && mb_strlen($value)>0){
				array_push($newLink, $value);
			}
		}
	}
	
	$data = array(
		'link' => $newLink,
		'get' => $_GET,
	);
	
	return $data;
}

public static function getHomeMap($index = 'textmap')
{
	$out = new CmsOutput;
	$d = $out->getHome(Registry::get('lng'));
	return count($d) == 1 ? $d[0][$index]: false;
}

public static function relocate($map, $header = null)
{
	if(!is_null($header) && $header == 404)
	{
		header('HTTP/1.1 404 Not Found');
		require_once(Registry::get('serverdata/root').$map);
		exit();
	}else{
		header('HTTP/1.1 301 Moved Permanently');
		header('Location: '.$map);
	}
}

public static function definePage()
{
	$homePage = self::getHomeMap();
	$data = self::urlInfo();
	$out = new CmsOutput;
	$d = $out->getLinkID($data['link'][0]);
	$documentTitle = null;
	$mainTitle = null;
	$docScript = null;
	
	if(mb_strlen($data['link'][0])>0 && count($d) == 0 && Registry::get('cms_page_exists') == 0 ){
		self::relocate('/404.html', 404);
	}elseif( ( is_null($data['link'][0]) || mb_strlen($data['link'][0])==0 ) && $homePage !== false){
		self::relocate(Registry::get('serverdata/site').'/'.$homePage.'/');
	}elseif(is_null($data['link'][0])){
		self::relocate('/404.html', 404);
	}
	
	$title = mb_strlen($d['link_title'])>0 ? $d['link_title']: $d['title'];
	$actual_page = isset($_GET['page']) && is_numeric($_GET['page']) && (int)$_GET['page']>0 ? (int)$_GET['page']: 1; 
	
	$mainTitle = $title;
	$pageDescription = $d['description'];
	
	$i = (count($data['link']) - 1);
	
	$document = mb_strlen(System::extension($data['link'][$i]))>0 && ( System::extension($data['link'][$i]) == 'html' ) ? System::fileNameOnly($data['link'][$i]): null;
	
	if(!is_null($document)){
		$preview = defined('_WEEBO_PREVIEW_') ? _WEEBO_PREVIEW_: false; 
		$cData = $out->getContentDataByTextmapPublished($d['id_link'], $document, $preview);
		
		if(count($cData)<2 || (isset($cData['id_public']) && $cData['id_public'] == 0) ){
			self::relocate('/404.html', 404);
		}else{
			$documentTitle = array_key_exists('title', $cData) ? $cData['title']: null;
			$mainTitle = !is_null($documentTitle) ? $documentTitle.' - '.$title: $title;
			$docScript = array_key_exists('display_script', $cData) ? $cData['display_script']: null;
			$pubAnnotation = mb_strlen($cData['annotation_text'])>0 ? $cData['annotation_text'] : $cData['content'];
			$pageDescriptionDetail = mb_substr(strip_tags($pubAnnotation), 0, Registry::get('moduledata/cms/brief_size'));
			$pageDescription = mb_strlen($pageDescriptionDetail)>0 ? $pageDescriptionDetail: $pageDescription;
		}
	}
	
	$mainTitle = mb_strlen(Registry::get('cms_custom_title'))>0 ? Registry::get('cms_custom_title'): $mainTitle;
	
	$myGet = null;
	
	if(count($_GET)>0)
	{
		$myGet .= '?';
		$x = 0;
		foreach($data['get'] as $k => $v){
			$myGet .= $x == 0 ? $k.'='.$v: '&'.$k.'='.$v;
		}
	}
	
	$pagerDefault = (int)$d['pager_default']>0 ? (int)$d['pager_default']: 10;
	
	define('__CMS_PAGE_ID__', (int)$d['id_link']);
	define('__CMS_PARENT_PAGE_ID__', (int)$d['id_sub']);
	define('__CMS_MAP__', $d['textmap']);
	define('__CMS_SCRIPT__', $docScript);
	define('__CMS_DOCUMENT__', $document);
	define('__CMS_DOMAIN__', $d['domain']);
	define('__CMS_PAGE_NAME__', $d['title']);
	define('__CMS_PAGE_TITLE__', $title);
	define('__CMS_DOCUMENT_TITLE__', $documentTitle);
	define('__CMS_MAIN_TITLE__', $mainTitle);
	define('__CMS_PAGE_ID_PAGER__', (int)$d['id_pager']);
	define('__CMS_PAGE_PAGE_DEFAULT__', $pagerDefault);
	define('__CMS_PAGE_DEFAULT_ORDER__', $d['default_order']);
	define('__CMS_PAGE_DOMAIN__', $d['domain']);
	define('__CMS_PAGE_LNG__', $d['lng']);
	define('__CMS_PAGE_HOMEPAGE__', $homePage);
	define('__CMS_PAGE_RSS__', (int)$d['id_rss']);
	define('__CMS_PAGE_ACTUAL_PAGE__',  $actual_page);
	define('__CMS_PAGE_KEYWORDS__', $d['keywords']);
	define('__CMS_PAGE_TEMPLATE__', $d['template']);
	define('__CMS_PAGE_DESCRIPTION__', $pageDescription);
	define('__CMS_LTIME__', time());
	define('__CMS_FULL_PATH__', implode('/', $data['link']).$myGet);
}

public static function moduleContent(){
	if(!is_null(__CMS_DOCUMENT__)){
		return self::contentDetail();
	}
	
	return self::content();
}

public static function pageContent(){
	
	$html = 'WARNING - MAIN VIEW MISSING!';
	
	if(!is_null(__CMS_DOCUMENT__)){
		
		$actual_page = __CMS_PAGE_ACTUAL_PAGE__; 
		$myformat = Lng::get('cms/date_format');
		
		$hashDocument = explode("-", __CMS_DOCUMENT__);
		$hdl = count($hashDocument) - 1;
		$hash = $hashDocument[$hdl];
		
		$q = "SELECT * FROM "._SQLPREFIX_."_cms_content WHERE id_link = '".__CMS_PAGE_ID__."' ";
		//$q.= Registry::get('userdata/logged_in') === 1 ? " AND secure IN(0,1) ": " AND secure IN(0,2) ";
		$q.= defined('_WEEBO_PREVIEW_') && _WEEBO_PREVIEW_ === false ? " AND id_public = 1 AND textmap LIKE '%-".$hash."' ": " AND textmap LIKE '".__CMS_DOCUMENT__."' ";
		$q.= defined('_WEEBO_PREVIEW_') && _WEEBO_PREVIEW_ === false ? " AND date_public <= ".__CMS_LTIME__." ": null;
		$q.=" AND CASE id_date_restrict WHEN 1 THEN publish_date_from <= ".__CMS_LTIME__." WHEN 0 THEN publish_date_from>-1 END";
		$q.=" AND CASE id_date_restrict WHEN 1 THEN publish_date_to >= ".__CMS_LTIME__." WHEN 0 THEN publish_date_to>-1 END";
		$q.=  " LIMIT 1";
		
		//$qq = Db::result($q);
		$qq = Db::memAuto($q, 10);
		$result_count = count($qq); 
		
		if($result_count==1)
		{
			$d = $qq[0];
			self::logImpression($d['id_content'], $d['impress'] + 1);
			
			$mainViewScript = Registry::get('serverdata/root').'/mwms/modules/cms/view/website/template.cms.content.detail.php';
			
			if(file_exists($mainViewScript)){
				ob_start();
				require($mainViewScript);
				$html = ob_get_contents();
				ob_end_clean();
			}
			
		}
		
	}else{
		
		$actual_page = __CMS_PAGE_ACTUAL_PAGE__; 
		$query_limit = ($actual_page-1)*__CMS_PAGE_PAGE_DEFAULT__; 

		$myformat = Lng::get('cms/date_format');
		$myorder = __CMS_PAGE_DEFAULT_ORDER__; 

		$q = "SELECT * FROM "._SQLPREFIX_."_cms_content WHERE id_link = '".__CMS_PAGE_ID__."' ";
		//$q.= Registry::get('userdata/logged_in') === 1 ? " AND secure IN(0,1) ": " AND secure IN(0,2) ";
		$q.= defined('_WEEBO_PREVIEW_') && _WEEBO_PREVIEW_ === false ? " AND id_public = 1 ": null;
		$q.= defined('_WEEBO_PREVIEW_') && _WEEBO_PREVIEW_ === false ? " AND date_public <= ".__CMS_LTIME__." ": null;
		$q.=" AND CASE id_date_restrict WHEN 1 THEN publish_date_from <= ".__CMS_LTIME__." WHEN 0 THEN publish_date_from>-1 END";
		$q.=" AND CASE id_date_restrict WHEN 1 THEN publish_date_to >= ".__CMS_LTIME__." WHEN 0 THEN publish_date_to>-1 END";
		$q.=  " ORDER BY ".$myorder;
		$qq = Db::memAuto($q, 10);
		$result_count = count($qq);
		
		$mainViewScript = Registry::get('serverdata/root').'/mwms/modules/cms/view/website/template.cms.content.php';
		
		if(file_exists($mainViewScript)){
			ob_start();
			require($mainViewScript);
			$html = ob_get_contents();
			ob_end_clean();
		}
		
	}
	
	return $html;
	//return self::contentModels();
}

public static function logImpression($id_content, $count){
	
	$eArticleReading = Registry::get('cms_article_readings');
	$now = time();
	$interval = (int)self::getCfg('impression_timeout');
	$update = false;
	
	if($eArticleReading === false)
	{
		Registry::set('cms_article_readings', array());
		$eArticleReading = Registry::get('cms_article_readings');
	}
	
	if(array_key_exists($id_content, $eArticleReading))
	{
		$timeout = $now - (int)$eArticleReading[$id_content];
		if($timeout > $interval){
			$update = true;
			$eArticleReading[$id_content] = time();
		}
	}else{
		$eArticleReading[$id_content] = time();
		$update = true;
	}

	Registry::set('cms_article_readings', $eArticleReading);

	if($update === true)
	{
		$q = "UPDATE "._SQLPREFIX_."_cms_content SET impress = '".(int)$count."' WHERE id_content = '".(int)$id_content."' ";
		Db::query($q);
	}

}

public static function bindRss(){
	return __CMS_PAGE_RSS__>0 ? '<div class="weebo_rss_bar"><a href="'.Registry::get('serverdata/site').'/'.__CMS_MAP__.'/rss.xml">RSS</a></div>': null;
}

public static function content()
{
	$actual_page = __CMS_PAGE_ACTUAL_PAGE__; 
	$query_limit = ($actual_page-1)*__CMS_PAGE_PAGE_DEFAULT__; 
	
	$myformat = Lng::get('cms/date_format');
	$myorder = __CMS_PAGE_DEFAULT_ORDER__; 

	$q = "SELECT * FROM "._SQLPREFIX_."_cms_content WHERE id_link = '".__CMS_PAGE_ID__."' ";
	//$q.= Registry::get('userdata/logged_in') === 1 ? " AND secure IN(0,1) ": " AND secure IN(0,2) ";
	$q.= defined('_WEEBO_PREVIEW_') && _WEEBO_PREVIEW_ === false ? " AND id_public = 1 ": null;
	$q.= defined('_WEEBO_PREVIEW_') && _WEEBO_PREVIEW_ === false ? " AND date_public <= ".__CMS_LTIME__." ": null;
	$q.=" AND CASE id_date_restrict WHEN 1 THEN publish_date_from <= ".__CMS_LTIME__." WHEN 0 THEN publish_date_from>-1 END";
	$q.=" AND CASE id_date_restrict WHEN 1 THEN publish_date_to >= ".__CMS_LTIME__." WHEN 0 THEN publish_date_to>-1 END";
	$q.=  " ORDER BY ".$myorder;
	
	//$qq = Db::result($q);
	$qq = Db::memAuto($q, 10);
	
	$result_count = count($qq); 
	$query_limit = __CMS_PAGE_PAGE_DEFAULT__ * ( __CMS_PAGE_ACTUAL_PAGE__ - 1 );
	$list = __CMS_PAGE_ID_PAGER__ == 1 && __CMS_PAGE_PAGE_DEFAULT__>0 && $result_count > __CMS_PAGE_PAGE_DEFAULT__ ? Db::final_items($qq, $query_limit, __CMS_PAGE_PAGE_DEFAULT__): $qq;
	$pager = __CMS_PAGE_ID_PAGER__ == 1 && __CMS_PAGE_PAGE_DEFAULT__>0 && $result_count > __CMS_PAGE_PAGE_DEFAULT__ ? Navigator::pager_ajax_rewrite($result_count, __CMS_PAGE_PAGE_DEFAULT__, $custom_uri = Registry::get('serverdata/site').'/'.__CMS_MAP__.'/?', $actual_page, 'page', 3): null;

	$html =  $pager;
	
	foreach($list as $d)
	{
		$html .=  '
			<!-- start of html block -->
			<div class="textbox textbox_'.$d['id_content'].'" role="article">
			';
		
		$pubTitle = mb_strlen($d['title'])>0 ? $d['title']: null;
		$pubDate = $d['id_date_display']==1 ? '<span class="content_date">'.date($myformat, $d['date_public']).'</span>': null;
		$pubDateUpdate = $d['id_date_display']==1 && ($d['date_public']+(int)self::getCfg('updateInfoReleaseTime'))<$d['date_upd'] ? '<span class="content_date_update">'.Lng::get('cms/weebo_site_content_update').' '.date($myformat, $d['date_upd']).'</span>': null;
		$pubImpress = $d['impress']>0 ? '<span class="content_impress">'.Lng::get('cms/weebo_site_content_impress').' '.$d['impress'].'x</span>': null;
		$pubAnnotation = mb_strlen($d['annotation_text'])>0 ? $d['annotation_text'] : $d['content'];
		$pubLink = mb_strlen($d['ext_link'])>0 ? $d['ext_link']: Registry::get('serverdata/site').'/'.__CMS_MAP__.'/'.$d['textmap'].'.html';
		$pubLinkTarget = $d['id_blank']>0 ? ' target="_blank"': null;
		$backlink = isset($_GET['page']) && $_GET['page']>0 ? '?page='.$_GET['page']: null;	
		$pubKeywords = mb_strlen($d['keywords'])>0 ? self::showKeywords($d['keywords']) : null;
		
		
		//if($d['id_brief_allow']==1){
		switch($d['id_brief_level']){
		case 1:
			/* LINK */  
			if(isset($pubTitle)){
				$html .=  '
					<h2 class="content_header_link">
						<span class="content_header_text"><a href="'.$pubLink.$backlink.'" title="'.$pubTitle.'"'.$pubLinkTarget.'>'.$pubTitle.'</a></span>
					</h2>
					';
				$html .=  !is_null($pubDate) ? '<div class="content_meta_info">'.$pubDate.' '.$pubDateUpdate.' '.$pubImpress.'</div>': null;  
			}   
				
		break;
		case 2:
			/* ANOTATION */
				$html .=  '
					<div class="list_annotation" id="list_annotation_'.$d['id_content'].'">
				'; 
				
			if(isset($pubTitle)){
				$html .=  '
					<h2 class="content_header_annotation_link">
						<span class="content_header_text"><a href="'.$pubLink.$backlink.'" title="'.$pubTitle.'"'.$pubLinkTarget.'>'.$pubTitle.'</a></span>
					</h2>
					';
				$html .=  !is_null($pubDate) ? '<div class="content_meta_info">'.$pubDate.' '.$pubDateUpdate.' '.$pubImpress.'</div>': null;  
				$html .=  !is_null($pubKeywords) ? '<div class="content_keywords">'.$pubKeywords.'</div>': null;  
				
			} 
				
			$anoteWidget = array('
				<a href="'.$pubLink.'"  title="'.$pubTitle.'"'.$pubLinkTarget.' class="list_annotation_image_link annotation_no_image">
					<span class="annotation_no_image_wrapper">&nbsp;</span>
				</a>',
				' annotation_image_noshow');
		
			if(mb_strlen($d['annotation_image'])>0 && file_exists(Registry::get('serverdata/root').'/'.$d['annotation_image'])){
				
				$anoteDir = dirname($d['annotation_image']);
				$anoteFile = basename($d['annotation_image']);
				
				if( file_exists(Registry::get('serverdata/root').'/'.$anoteDir.'/th_'.$anoteFile) )
				{
					
					$anoteWidget = array('
						<a href="'.$pubLink.'" title="'.$pubTitle.'"'.$pubLinkTarget.' class="list_annotation_image_link">
							<span class="annotation_image_wrapper"><img src="'.Registry::get('serverdata/site').'/'.$anoteDir.'/th_'.$anoteFile.'" alt="'.$pubTitle.'" /></span>
						</a>',
						' annotation_image_show'); 
				}
			}
			
			$html .=  $anoteWidget[0];
			$html .=  mb_strlen($pubAnnotation)>0 ? '<p class="content_annotation'.$anoteWidget[1].'">'.mb_substr(strip_tags($pubAnnotation), 0, Registry::get('moduledata/cms/brief_size')).'</p>' : null;
			$html .=  '
				</div>
				'; 

		break;

		default: 
			/* FULL */
			if(isset($pubTitle)){
				$html .=  '
					<h1 class="content_header">
						<span class="content_header_text">'.$pubTitle.'</span>
					</h1>
				';

			} 
			
			//self::logImpression($d['id_content'], $d['impress'] + 1);
			$html .=  $d['content']; 
		}
		 
		$html .=  '
			</div>
		'; 

		if($d['id_brief_level'] == 0){
			
			$viewScript = Registry::get('serverdata/root').'/mwms/modules/'.$d['display_script'];
			
			if(file_exists($viewScript)){
				ob_start();
				require($viewScript);
				$html .= ob_get_contents();
				ob_end_clean();
			}
		}
		
		$html .= '
			<div class="clear clearfix"></div>
			<!-- end of html block -->
		'; 

	}
	
	$html .= $pager;
	
	return $html;
}


public static function contentDetail()
{
	$actual_page = __CMS_PAGE_ACTUAL_PAGE__; 
	$myformat = Lng::get('cms/date_format');
	$html = null;
	
	$hashDocument = explode("-", __CMS_DOCUMENT__);
	$hdl = count($hashDocument) - 1;
	$hash = $hashDocument[$hdl];
	
	$q = "SELECT * FROM "._SQLPREFIX_."_cms_content WHERE id_link = '".__CMS_PAGE_ID__."' ";
	//$q.= Registry::get('userdata/logged_in') === 1 ? " AND secure IN(0,1) ": " AND secure IN(0,2) ";
	$q.= defined('_WEEBO_PREVIEW_') && _WEEBO_PREVIEW_ === false ? " AND id_public = 1 AND textmap LIKE '%-".$hash."' ": " AND textmap LIKE '".__CMS_DOCUMENT__."' ";
	$q.= defined('_WEEBO_PREVIEW_') && _WEEBO_PREVIEW_ === false ? " AND date_public <= ".__CMS_LTIME__." ": null;
	$q.=" AND CASE id_date_restrict WHEN 1 THEN publish_date_from <= ".__CMS_LTIME__." WHEN 0 THEN publish_date_from>-1 END";
	$q.=" AND CASE id_date_restrict WHEN 1 THEN publish_date_to >= ".__CMS_LTIME__." WHEN 0 THEN publish_date_to>-1 END";
	$q.=  " LIMIT 1";
	
	//$qq = Db::result($q);
	$qq = Db::memAuto($q, 10);
	$result_count = count($qq); 
	
	if($result_count==1)
	{
		$d = $qq[0];
		self::logImpression($d['id_content'], $d['impress'] + 1);
		$html =  '<!-- start of html block --><div class="textbox_detail textbox_detail_'.$d['id_content'].'" role="article">';

		$pubTitle = mb_strlen($d['title'])>0 ? $d['title']: null;
		$pubDate = $d['id_date_display']==1 ? '<span class="content_date">'.date($myformat, $d['date_public']).'</span>': null;
		$pubDateUpdate = $d['id_date_display']==1 && ($d['date_public']+(int)self::getCfg('updateInfoReleaseTime'))<$d['date_upd'] ? '<span class="content_date_update">'.Lng::get('cms/weebo_site_content_update').' '.date($myformat, $d['date_upd']).'</span>': null;
		$pubImpress = $d['impress']>0 ? '<span class="content_impress">'.Lng::get('cms/weebo_site_content_impress').' '.$d['impress'].'x</span>': null;
		$pubAnnotation = mb_strlen($d['annotation_text'])>0 ? $d['annotation_text'] : null;
		$anoteimg = mb_strlen($d['annotation_image'])>0 && file_exists('./'.$d['annotation_image']) ? '<p class="detail_annotation_image"><img src="'.Registry::get('serverdata/site').'/'.$d['annotation_image'].'" alt="'.$d['title'].'" /></p>': null;
		$pubLink = mb_strlen($d['ext_link'])>0 ? $d['ext_link']: Registry::get('serverdata/site').'/'.__CMS_MAP__.'/'.$d['textmap'].'.html';
		$pubLinkTarget = $d['id_blank']>0 ? ' target="_blank"': null;
		$backlink = isset($_GET['page']) && $_GET['page']>0 ? '?page='.$_GET['page']: null;	
		$pubKeywords = mb_strlen($d['keywords'])>0 ? self::showKeywords($d['keywords']) : null;
		
		/*
		$adv = $d['display_script'] == 'cms/view/website/view.content.php' || 
		$d['display_script'] == 'cms/view/website/view.gallery.php' ? '<div class="banner" id="adv1"></div>': null;
		*/
		
		/* FULL */
		$html .=  '<h1 class="content_header">';
		$html .=  '<span class="content_header_text">'.$d['title'].'</span></h1>';
		$html .=  !is_null($pubDate) ? '<div class="content_meta_info">'.$pubDate.' '.$pubDateUpdate.' '.$pubImpress.'</div>': null;  
		$html .=  !is_null($pubKeywords) ? '<div class="content_keywords">'.$pubKeywords.'</div>': null;  
		//$html .=  $adv; 
		if(mb_strlen($d['annotation_text'])>0){
			$html .=  '<div class="list_annotation"><p>'.$d['annotation_text'].'</p> '.$anoteimg.'</div>';
		}
		 
		$html .=  $d['content']; 
		$html .=  '</div>'; 
		$viewScript = Registry::get('serverdata/root').'/mwms/modules/'.$d['display_script'];
		if(file_exists($viewScript)){
			ob_start();
			require($viewScript);
			$html .= ob_get_contents();
			ob_end_clean();
		}
		$html .=  '
		<div class="clear clearfix"></div>
		<!-- end of html block -->
		';
	}
	
	return $html;
}






public static function showTagCloud($toList = true){
	
	$q = "SELECT *, "._SQLPREFIX_."_cms_content.keywords AS kws FROM "._SQLPREFIX_."_cms_content ";
	$q .= " INNER JOIN "._SQLPREFIX_."_cms_links ON "._SQLPREFIX_."_cms_content.id_link = "._SQLPREFIX_."_cms_links.id_link ";
	//$q.= Registry::get('userdata/logged_in') === 1 ? " AND secure IN(0,1) ": " AND secure IN(0,2) ";
	$q.=" WHERE "._SQLPREFIX_."_cms_content.id_public = 1 AND "._SQLPREFIX_."_cms_links.id_public = 1 AND LENGTH("._SQLPREFIX_."_cms_content.keywords)>0 AND date_public <= ".__CMS_LTIME__." AND "._SQLPREFIX_."_cms_links.domain LIKE '"._CMS_DOMAIN_."' ";
	$q.=" AND CASE id_date_restrict WHEN 1 THEN publish_date_from <= ".__CMS_LTIME__." WHEN 0 THEN publish_date_from>-1 END";
	$q.=" AND CASE id_date_restrict WHEN 1 THEN publish_date_to >= ".__CMS_LTIME__." WHEN 0 THEN publish_date_to>-1 END";
	
	//echo $q;
	
	//$qq = Db::result($q);
	$qq = Db::memAuto($q, 10);
	
	$keycount = 0;
	$html = null;
	$result = array();
	
	foreach($qq as $d){
		
		if(mb_strlen($d['kws'])>0){
			$kw = explode(',', $d['kws']);
			foreach($kw as $keyword){
				array_push($result, trim($keyword));
			}
		}
	}
	
	$result = array_unique($result);
	
	if(count($result)>0)
	{
		sort($result);
		$html = $toList === true ? '
			<ul class="tag-cloud">
			': '
			<div class="tag-cloud">
			';
		
		$o = new CmsOutput;
		$contentData = $o->getContentByView('cms/view/website/view.keyword.search.php');
		
		if(count($contentData)==1){
			$pageData = $o->getLinkData($contentData[0]['id_link']);
			$___map = $pageData['textmap'];
		}else{
			$___map = '404.html';
		}
		
		$resultCount = array();
		
		foreach($result as $keyword){
			$keycount = self::getKeywordImpressions($keyword);
			$resultCount[$keyword] = $keycount;
		}
		
		$hPercent = (int)max($resultCount);
		
		foreach($resultCount as $keyword => $keycount){
			$keycount = self::getKeywordImpressions($keyword);
			$rating = self::percentage($hPercent, $keycount);
			$percentage = $rating['precise'];
			$np = $rating['non-precise'];
			$html .= $toList === true ? '
						<li class="tag tag-count-'.$percentage.' tag-rating-'.$np.'"><a href="'.Registry::get('serverdata/site').'/'.$___map.'/'.urlencode($keyword).'/">'.$keyword.' <span class="keyword-count">('.$keycount.')</span></a></li>
						': '
						<span class="tag tag-count-'.$percentage.' tag-rating-'.$np.'"><a href="'.Registry::get('serverdata/site').'/'.$___map.'/'.urlencode($keyword).'/">'.$keyword.' <span class="keyword-count">('.$keycount.')</span></a></span>
						';
		}
		
		$html .=  $toList === true ? '
			</ul>
			': '
			</div>
			';
	}
	
	return $html;
}

public static function percentage($max, $value){
	
	$result =array();
	
	$p = $value > 0 ? $value/($max/100): 0;
	$result['precise'] = (int)$p == 0 ? 1: (int)$p;
	
	if($p<=10){
		$result['non-precise'] = 10;
	}elseif($p>10 && $p<26){
		$result['non-precise'] = 20;
	}elseif($p>25 && $p<36){
		$result['non-precise'] = 30;
	}elseif($p>35 && $p<46){
		$result['non-precise'] = 40;
	}elseif($p>45 && $p<56){
		$result['non-precise'] = 50;
	}elseif($p>55 && $p<66){
		$result['non-precise'] = 60;
	}elseif($p>65 && $p<76){
		$result['non-precise'] = 70;
	}elseif($p>75 && $p<86){
		$result['non-precise'] = 80;
	}elseif($p>85 && $p<96){
		$result['non-precise'] = 90;
	}elseif($p>95){
		$result['non-precise'] = 100;
	}
	
	return $result;
}

public static function getKeywordImpressions($keyword){
	
	$q = "SELECT *, SUM(impress) AS keycount FROM "._SQLPREFIX_."_cms_content ";
	$q .= " LEFT JOIN "._SQLPREFIX_."_cms_links ON "._SQLPREFIX_."_cms_content.id_link = "._SQLPREFIX_."_cms_links.id_link ";
	//$q.= Registry::get('userdata/logged_in') === 1 ? " AND secure IN(0,1) ": " AND secure IN(0,2) ";
	$q.=" WHERE "._SQLPREFIX_."_cms_content.id_public = 1 AND date_public <= ".__CMS_LTIME__." AND "._SQLPREFIX_."_cms_links.domain LIKE '"._CMS_DOMAIN_."' AND "._SQLPREFIX_."_cms_content.keywords LIKE '%".Db::escapeField($keyword)."%' ";
	$q.=" AND CASE id_date_restrict WHEN 1 THEN publish_date_from <= ".__CMS_LTIME__." WHEN 0 THEN publish_date_from>-1 END";
	$q.=" AND CASE id_date_restrict WHEN 1 THEN publish_date_to >= ".__CMS_LTIME__." WHEN 0 THEN publish_date_to>-1 END";
	
	//$qq = Db::result($q);
	$qq = Db::memAuto($q, 10);
	
	return count($qq)>0 ? $qq[0]['keycount']: 0;
}

public static function contentWidget(
	$pageID = 0, 
	$config = array( 
		'briefLevel' => 0, // 0,1,2 OR ALL
		'title' => false,
		'titleLink' => false,
		'archiveLink' => false,
		'limit' => 10,
		'rss' => false,
		'keywords' => false
	)
)
{
	$p = "SELECT * FROM "._SQLPREFIX_."_cms_links WHERE id_link = '".$pageID."' AND id_public = 1 LIMIT 1";
	$qp = Db::result($p);
	$pageData = count($qp)==1 ? $qp[0]: null;
	
	$myformat = Lng::get('cms/date_format');
	
	$html = null;
	
	if( !is_null($pageData) )
	{
		
		$myorder = $pageData['default_order'];
		
		$q = "SELECT * FROM "._SQLPREFIX_."_cms_content WHERE id_link = '".(int)$pageID."' AND id_public = 1 ";
		//$q.= Registry::get('userdata/logged_in') === 1 ? " AND secure IN(0,1) ": " AND secure IN(0,2) ";
		$q.= $config['briefLevel'] === 'ALL' ? null: " AND id_brief_level IN ('".(string)$config['briefLevel']."') ";
		$q.=" AND date_public <= ".__CMS_LTIME__." ";
		$q.=" AND CASE id_date_restrict WHEN 1 THEN publish_date_from <= ".(int)__CMS_LTIME__." WHEN 0 THEN publish_date_from>-1 END";
		$q.=" AND CASE id_date_restrict WHEN 1 THEN publish_date_to >= ".(int)__CMS_LTIME__." WHEN 0 THEN publish_date_to>-1 END";
		$q.=  " ORDER BY ".$myorder;
		$q.= (int)$config['limit'] > 0 ? " LIMIT ".(int)$config['limit']: null;
		
		//$qq = Db::result($q);
		$qq = Db::memAuto($q, 10);
		
		$result_count = count($qq); 
		
		$pageTitle = mb_strlen($pageData['title']) > 0 && $config['titleLink'] === true ? '<a href="'.Registry::get('serverdata/site').'/'.$pageData['textmap'].'/">'.$pageData['title'].'</a>': $pageData['title'];
		$pageTitle = mb_strlen($pageData['title']) > 0 && $config['title'] === true ? '<h2 class="weebo_widget_title">'.$pageTitle.'</h2>': null;
		
		$html =  $pageTitle;
		
		$html .=  '
			<div class="widget-container">
		';
		
		foreach($qq as $d)
		{
			$html .=  '<!-- start of html block -->
					<div class="widget widget_'.$d['id_content'].'" role="article">
				';
			$pubTitle = mb_strlen($d['title'])>0 ? $d['title']: null;
			$pubDate = $d['id_date_display']==1 ? '<span class="content_date">'.date($myformat, $d['date_public']).'</span>': null;
			$pubDateUpdate = $d['id_date_display']==1 && ($d['date_public']+(int)self::getCfg('updateInfoReleaseTime'))<$d['date_upd'] ? '<span class="content_date_update">'.Lng::get('cms/weebo_site_content_update').' '.date($myformat, $d['date_upd']).'</span>': null;
			$pubImpress = $d['impress']>0 ? '<span class="content_impress">'.Lng::get('cms/weebo_site_content_impress').' '.$d['impress'].'x</span>': null;
			$pubAnnotation = mb_strlen($d['annotation_text'])>0 ? $d['annotation_text'] : $d['content'];
			$pubLink = mb_strlen($d['ext_link'])>0 ? $d['ext_link']: Registry::get('serverdata/site').'/'.$pageData['textmap'].'/'.$d['textmap'].'.html';
			$pubLinkTarget = $d['id_blank']>0 ? ' target="_blank"': null;
			$backlink = isset($_GET['page']) && $_GET['page']>0 ? '?page='.$_GET['page']: null;	
			$pubKeywords = mb_strlen($d['keywords'])>0 ? self::showKeywords($d['keywords']) : null;
			
			switch($config['briefLevel']){
			case 1:
				/* LINK */  
				if(isset($pubTitle)){
					$html .=  '<h2 class="content_header_link">';
					$html .=  '<span class="content_header_text"><a href="'.$pubLink.$backlink.'" title="'.$d['title'].'"'.$pubLinkTarget.'>'.$d['title'].'</a></span></h2>';
					$html .=  !is_null($pubDate) ? '<div class="content_meta_info">'.$pubDate.' '.$pubDateUpdate.' '.$pubImpress.'</div>': null;  
				}   
					
			break;
			case 2:
				/* ANOTATION */
					echo '<div class="list_annotation" id="list_annotation_'.$d['id_content'].'">'; 
					
				if(isset($pubTitle)){
					$html .=  '<h2 class="content_header_annotation_link">';
					$html .=  '<span class="content_header_text"><a href="'.$pubLink.$backlink.'" title="'.$d['title'].'"'.$pubLinkTarget.'>'.$d['title'].'</a></span></h2>';
					$html .=  !is_null($pubDate) ? '<div class="content_meta_info">'.$pubDate.' '.$pubDateUpdate.' '.$pubImpress.'</div>': null;  
					$html .=  !is_null($pubKeywords) && array_key_exists('keywords', $config) && $config['keywords'] === true ? '<div class="content_keywords">'.$pubKeywords.'</div>': null;  
				} 
					
				$anoteWidget = array('
					<a href="'.$pubLink.'"  title="'.$d['title'].'"'.$pubLinkTarget.' class="list_annotation_image_link annotation_no_image">
						<span class="annotation_no_image_wrapper">&nbsp;</span>
					</a>',
					' annotation_image_noshow');
			
				if(mb_strlen($d['annotation_image'])>0 && file_exists(Registry::get('serverdata/root').'/'.$d['annotation_image'])){
					
					$anoteDir = dirname($d['annotation_image']);
					$anoteFile = basename($d['annotation_image']);
					
					if( file_exists(Registry::get('serverdata/root').'/'.$anoteDir.'/th_'.$anoteFile) )
					{
						
						$anoteWidget = array('
							<a href="'.$pubLink.'" title="'.$d['title'].'"'.$pubLinkTarget.' class="list_annotation_image_link">
								<span class="annotation_image_wrapper"><img src="'.Registry::get('serverdata/site').'/'.$anoteDir.'/th_'.$anoteFile.'" alt="'.$d['title'].'" /></span>
							</a>',
							' annotation_image_show'); 
					}
				}
				
				$html .= $anoteWidget[0];
				$html .=  mb_strlen($pubAnnotation)>0 ? '<p class="content_annotation'.$anoteWidget[1].'">'.mb_substr(strip_tags($pubAnnotation), 0, Registry::get('moduledata/cms/brief_size')).'</p>' : null;
				$html .=  '</div>'; 
			
			break;
			default: 
				/* FULL */
				if(isset($pubTitle)){
					$html .=  '<h2 class="content_header">';
					$html .=  '<span class="content_header_text">'.$d['title'].'</span></h2>';
					//$html .=  !is_null($pubDate) ? '<div class="content_meta_info">'.$pubDate.' '.$pubDateUpdate.' '.$pubImpress.'</div>': null;  
				} 
				 
				$html .=  $d['content']; 
			}
			 
			$html .=  '
					</div>
				'; 
			
			if($d['id_brief_level'] == 0){
				
				$viewScript = Registry::get('serverdata/root').'/mwms/modules/'.$d['display_script'];
				
				$isWidget = true;
				
				if(file_exists($viewScript)){
					ob_start();
					require($viewScript);
					$html .= ob_get_contents();
					ob_end_clean();
				}
			}
			
			$html .=  '
				<!-- end of html block -->
			'; 

		}
		
		$html .=  '
			</div>
			<div class="clear clearfix"></div>
		';
		
		$archiveTitle = mb_strlen($pageData['title']) < 1 || $config['archiveLink'] === false ? null: '<div class="widget_archive_link"><a href="'.Registry::get('serverdata/site').'/'.$pageData['textmap'].'/">'.$config['archiveLink'].'</a></div>';
		$html .=  $archiveTitle;

	}
	
	return $html;
}

public static function siteSearchUrl(){
	
	$o = new CmsOutput;
	$contentData = $o->getContentByView('cms/view/website/view.search.php');
	$map = '404.html';
	
	if(count($contentData)>0){
		$pageData = $o->getLinkData($contentData[0]['id_link']);
		$map = $pageData['textmap'];
	}
	return $map;
}

public static function siteSearch($wrapToForm = true){
	
	$o = new CmsOutput;
	$contentData = $o->getContentByView('cms/view/website/view.search.php');
	
	if($wrapToForm === true && count($contentData)==1){
		$pageData = $o->getLinkData($contentData[0]['id_link']);
		$___map = $pageData['textmap'];
	}else{
		$___map = '404.html';
	}
	
	$html = $wrapToForm === true ? '<form class="weebo_site_search_form" method="get" action="'.Registry::get('serverdata/site').'/'.$___map.'/">': null;
	$html .= '
		<input type="text" name="___wss" class="weebo_site_search" />
		<button class="weebo_site_search_button btn" title="'.Lng::get('cms/weebo_site_search_button_title').'">'.Lng::get('cms/weebo_site_search_button').'</button>
	';
	$html .= $wrapToForm === true ? '</form>': null;
	
	return $html;
}

public static function siteSearchResult($str, $origin){
	
	$results = null;
	$result_count = 0;
	
	if(mb_strlen($str)>2)
	{
		$q = "SELECT 
			*,
			"._SQLPREFIX_."_cms_links.title AS pagetitle, 
			"._SQLPREFIX_."_cms_links.textmap AS pagemap, 
			
			"._SQLPREFIX_."_cms_content.title AS doctitle, 
			"._SQLPREFIX_."_cms_content.content AS doctext, 
			"._SQLPREFIX_."_cms_content.textmap AS docmap, 
			"._SQLPREFIX_."_cms_content.ext_link AS doc_ext_link,
			"._SQLPREFIX_."_cms_content.id_blank AS doc_id_blank
			
			FROM "._SQLPREFIX_."_cms_content 
			LEFT JOIN "._SQLPREFIX_."_cms_links 
			ON "._SQLPREFIX_."_cms_content.id_link = "._SQLPREFIX_."_cms_links.id_link 
			WHERE 
			(
				"._SQLPREFIX_."_cms_content.title LIKE '%".Db::escapeField($str)."%' 
				OR "._SQLPREFIX_."_cms_content.content LIKE '%".Db::escapeField($str)."%' 
				OR "._SQLPREFIX_."_cms_content.annotation_text LIKE '%".Db::escapeField($str)."%' 
				OR "._SQLPREFIX_."_cms_content.keywords LIKE '%".Db::escapeField($str)."%' 
			) 
			AND "._SQLPREFIX_."_cms_links.lng LIKE '".__CMS_PAGE_LNG__."'
			AND "._SQLPREFIX_."_cms_links.id_public = 1 
			AND "._SQLPREFIX_."_cms_content.id_public = 1 
			
			AND "._SQLPREFIX_."_cms_content.date_public <= ".__CMS_LTIME__." 
			AND CASE "._SQLPREFIX_."_cms_content.id_date_restrict WHEN 1 THEN "._SQLPREFIX_."_cms_content.publish_date_from <= ".(int)__CMS_LTIME__." WHEN 0 THEN "._SQLPREFIX_."_cms_content.publish_date_from>-1 END 
			AND CASE "._SQLPREFIX_."_cms_content.id_date_restrict WHEN 1 THEN "._SQLPREFIX_."_cms_content.publish_date_to >= ".(int)__CMS_LTIME__." WHEN 0 THEN "._SQLPREFIX_."_cms_content.publish_date_to>-1 END 
			
			ORDER BY "._SQLPREFIX_."_cms_links.id_position, "._SQLPREFIX_."_cms_links.public_order, "._SQLPREFIX_."_cms_content.public_order, id_content DESC 
		";
		
		//$qq = Db::result($q);
		$qq = Db::memAuto($q, 10);
		
		$result_count = count($qq); 
		
		if($result_count>0)
		{
		
			foreach($qq as $d){
				
				$path = $d['id_brief_level'] == 0 ? Registry::get('serverdata/site').'/'.$d['pagemap'].'/': Registry::get('serverdata/site').'/'.$d['pagemap'].'/'.$d['docmap'].'.html';
				$title = $d['id_brief_level'] == 0 ? $d['pagetitle']: $d['doctitle'];
				
				$path = mb_strlen($d['doc_ext_link'])>0 ? $d['doc_ext_link']: $path;
				$pubLinkTarget = $d['doc_id_blank']>0 ? ' target="_blank"': null;
				
				$results .= '
					<div class="weebo_search_item">
						<a href="'.$path.'" '.$pubLinkTarget.' class="weebo_search_item_link" title="'.$title.'">
							'.$title.'
						</a>
					</div>
					';
			}
			$results .=  '
					<div class="clear clearfix"></div>
				';
		}else{
			$results .=  '
				<div class="weebo_search_not_found">
					<p>'.Lng::get('cms/weebo_site_search_not_found').'</p>
				</div>
			';
		}
	}elseif(mb_strlen($str) != mb_strlen($origin)){
		$results .=  '
				<div class="weebo_search_not_found">
					<p>'.Lng::get('cms/weebo_site_search_possible_attack').'</p>
				</div>
		';
	}else{
		$results .=  '
				<div class="weebo_search_not_found">
					<p>'.Lng::get('cms/weebo_site_search_not_found').'</p>
				</div>
		';
	}

	$html = '
		<!-- start of search result block -->
		<div class="weebo_search_wrapper">
			<div class="weebo_search_result_term">'.Lng::get('cms/weebo_site_search_term').' <span class="search_term">'.htmlspecialchars($str).'</span></div>
			<div class="weebo_search_result_count">'.Lng::get('cms/weebo_site_search_count').' <span class="search_count_num">'.$result_count.'</span></div>
			'.$results.'
		</div>
		<!-- start of search result block -->
	';

	return $html;
}

public static function showKeywords($keywords){
	$kw = explode(',', $keywords);
	$html = null;
	
	$o = new CmsOutput;
	$contentData = $o->getContentByView('cms/view/website/view.keyword.search.php');
	
	if(count($contentData)==1){
		$pageData = $o->getLinkData($contentData[0]['id_link']);
		$___map = $pageData['textmap'];
	}else{
		$___map = '404.html';
	}
	
	foreach($kw as $k => $w){
		$html .= $k == 0 ? null: ', ';
		$html .= '<a href="'.Registry::get('serverdata/site').'/'.$___map.'/'.urlencode(trim($w)).'/">'.trim($w).'</a>';
	}
	
	return $html;
}

public static function siteSearchByKeywords($str, $origin){
	
	$results = null;
	$result_count = 0;
	
	if(mb_strlen($str)>2)
	{
		$q = "SELECT 
			*,
			"._SQLPREFIX_."_cms_links.title AS pagetitle, 
			"._SQLPREFIX_."_cms_links.textmap AS pagemap,
			
			"._SQLPREFIX_."_cms_content.title AS doctitle, 
			"._SQLPREFIX_."_cms_content.content AS doctext, 
			"._SQLPREFIX_."_cms_content.textmap AS docmap, 
			"._SQLPREFIX_."_cms_content.keywords AS dockeywords, 
			"._SQLPREFIX_."_cms_content.ext_link AS doc_ext_link, 
			"._SQLPREFIX_."_cms_content.id_blank AS doc_id_blank, 
			"._SQLPREFIX_."_cms_content.date_upd AS docUpdate 
			
			FROM "._SQLPREFIX_."_cms_content 
			LEFT JOIN "._SQLPREFIX_."_cms_links 
			ON "._SQLPREFIX_."_cms_content.id_link = "._SQLPREFIX_."_cms_links.id_link 
			WHERE "._SQLPREFIX_."_cms_content.keywords LIKE '%".Db::escapeField($str)."%' 
			AND "._SQLPREFIX_."_cms_links.lng LIKE '".__CMS_PAGE_LNG__."'
			AND "._SQLPREFIX_."_cms_links.id_public = 1 
			AND "._SQLPREFIX_."_cms_content.id_public = 1 
			
			AND "._SQLPREFIX_."_cms_content.date_public <= ".__CMS_LTIME__." 
			AND CASE "._SQLPREFIX_."_cms_content.id_date_restrict WHEN 1 THEN "._SQLPREFIX_."_cms_content.publish_date_from <= ".(int)__CMS_LTIME__." WHEN 0 THEN "._SQLPREFIX_."_cms_content.publish_date_from>-1 END 
			AND CASE "._SQLPREFIX_."_cms_content.id_date_restrict WHEN 1 THEN "._SQLPREFIX_."_cms_content.publish_date_to >= ".(int)__CMS_LTIME__." WHEN 0 THEN "._SQLPREFIX_."_cms_content.publish_date_to>-1 END 
			
			ORDER BY "._SQLPREFIX_."_cms_links.id_position, "._SQLPREFIX_."_cms_links.public_order, "._SQLPREFIX_."_cms_content.public_order, id_content DESC 
		";
		
		//$qq = Db::result($q);
		$qq = Db::memAuto($q, 10);
		
		$result_count = count($qq); 
		
		if($result_count>0)
		{
			$actual_page = __CMS_PAGE_ACTUAL_PAGE__; 
			$query_limit = ($actual_page-1)*__CMS_PAGE_PAGE_DEFAULT__; 
			
			$myformat = Lng::get('cms/date_format');
			$myorder = __CMS_PAGE_DEFAULT_ORDER__; 
			$query_limit = __CMS_PAGE_PAGE_DEFAULT__ * ( __CMS_PAGE_ACTUAL_PAGE__ - 1 );
			
			$list = __CMS_PAGE_ID_PAGER__ == 1 && $result_count > __CMS_PAGE_PAGE_DEFAULT__ ? Db::final_items($qq, $query_limit, __CMS_PAGE_PAGE_DEFAULT__): $qq;
			$pager = __CMS_PAGE_ID_PAGER__ == 1 && $result_count > __CMS_PAGE_PAGE_DEFAULT__ ? Navigator::pager_ajax_rewrite($result_count, __CMS_PAGE_PAGE_DEFAULT__, $custom_uri = Registry::get('serverdata/site').'/'.__CMS_MAP__.'/'.$str.'/?', $actual_page, 'page', 3): null;

			$results .=   $pager;
			
			foreach($list as $d)
			{
				$results .=   '
					<!-- start of html block -->
					<div class="textbox textbox_'.$d['id_content'].'" role="article">
					';
				
				$pubTitle = mb_strlen($d['doctitle'])>0 ? $d['doctitle']: $d['pagetitle'];
				$pubDate = $d['id_date_display']==1 ? '<span class="content_date">'.date($myformat, $d['date_public']).'</span>': null;
				$pubDateUpdate = $d['id_date_display']==1 && ($d['date_public']+(int)self::getCfg('updateInfoReleaseTime'))<$d['docUpdate'] ? '<span class="content_date_update">'.Lng::get('cms/weebo_site_content_update').' '.date($myformat, $d['docUpdate']).'</span>': null;
				$pubImpress = $d['impress']>0 ? '<span class="content_impress">'.Lng::get('cms/weebo_site_content_impress').' '.$d['impress'].'x</span>': null;
				$pubAnnotation = mb_strlen($d['annotation_text'])>0 ? $d['annotation_text'] : $d['content'];
				$pubLink = mb_strlen($d['doc_ext_link'])>0 ? $d['doc_ext_link']: Registry::get('serverdata/site').'/'.$d['pagemap'].'/'.$d['docmap'].'.html';
				$pubLinkTarget = $d['doc_id_blank']>0 ? ' target="_blank"': null;
				//$backlink = isset($_GET['page']) && $_GET['page']>0 ? '?page='.$_GET['page']: null;	
				$backlink = null;	
				$pubKeywords = mb_strlen($d['dockeywords'])>0 ? self::showKeywords($d['dockeywords']) : null;
				
					// ANOTATION 
						$results .=   '
							<div class="list_annotation" id="list_annotation_'.$d['id_content'].'">
						'; 
						
					if(isset($pubTitle)){
						$results .=   '
							<h2 class="content_header_annotation_link">
								<span class="content_header_text"><a href="'.$pubLink.$backlink.'" title="'.$pubTitle.'"'.$pubLinkTarget.'>'.$pubTitle.'</a></span>
							</h2>
							';
						$results .=   !is_null($pubDate) ? '<div class="content_meta_info">'.$pubDate.' '.$pubDateUpdate.' '.$pubImpress.'</div>': null;  
						$results .=   !is_null($pubKeywords) ? '<div class="content_keywords">'.$pubKeywords.'</div>': null;  
						
					} 
						
					$anoteWidget = array('
						<a href="'.$pubLink.'"  title="'.$pubTitle.'"'.$pubLinkTarget.' class="list_annotation_image_link annotation_no_image">
							<span class="annotation_no_image_wrapper">&nbsp;</span>
						</a>',
						' annotation_image_noshow');
				
					if(mb_strlen($d['annotation_image'])>0 && file_exists(Registry::get('serverdata/root').'/'.$d['annotation_image'])){
						
						$anoteDir = dirname($d['annotation_image']);
						$anoteFile = basename($d['annotation_image']);
						
						if( file_exists(Registry::get('serverdata/root').'/'.$anoteDir.'/th_'.$anoteFile) )
						{
							
							$anoteWidget = array('
								<a href="'.$pubLink.'" title="'.$pubTitle.'"'.$pubLinkTarget.' class="list_annotation_image_link">
									<span class="annotation_image_wrapper"><img src="'.Registry::get('serverdata/site').'/'.$anoteDir.'/th_'.$anoteFile.'" alt="'.$pubTitle.'" /></span>
								</a>',
								' annotation_image_show'); 
						}
					}
					
					$results .=   $anoteWidget[0];
					$results .=   mb_strlen($pubAnnotation)>0 ? '<p class="content_annotation'.$anoteWidget[1].'">'.mb_substr(strip_tags($pubAnnotation), 0, Registry::get('moduledata/cms/brief_size')).'</p>' : null;
					$results .=   '
						</div>
						'; 

				 
				$results .=   '
					</div>
				'; 

				$results .=   '
					<div class="clear clearfix"></div>
					<!-- end of html block -->
				'; 

			}
			
			$results .=   $pager;
			
			
			
		}else{
			$results .=  '
				<div class="weebo_search_not_found">
					<p>'.Lng::get('cms/weebo_site_search_not_found').'</p>
				</div>
			';
		}

	// FORMAL IDS DETECTION, USE YOUR OWN IDS LIBRARY
	}elseif(mb_strlen($str) != mb_strlen($origin)){
		$results .=  '
				<div class="weebo_search_not_found">
					<p>'.Lng::get('cms/weebo_site_search_possible_attack').'</p>
				</div>
		';
	}else{
		$results .=  '
				<div class="weebo_search_not_found">
					<p>'.Lng::get('cms/weebo_site_search_not_found').'</p>
				</div>
		';
	}

	$html = '
		<!-- start of search result block -->
		<div class="weebo_search_wrapper">
			<div class="weebo_search_result_term">'.Lng::get('cms/weebo_site_search_term').' <span class="search_term">'.htmlspecialchars($str).'</span></div>
			<div class="weebo_search_result_count">'.Lng::get('cms/weebo_site_search_count').' <span class="search_count_num">'.$result_count.'</span></div>
			'.$results.'
		</div>
		<!-- start of search result block -->
	';

	return $html;
	
	
}

public static function createSitePath($id_link, $init = true){
	
	$drops = array();
	
	$o = new CmsOutput;
	
	$d = $o->getLinkData($id_link);
	$t = $d['title'];
	$m = $d['textmap'];
	$drops[$m] = $t;
	
	if($d['id_sub']>0){
		$drops = array_merge($drops, self::createSitePath($d['id_sub'], false));
	}
	
	return $drops;
}

public static function createSitePathHTML($data = array()){
	
	$html = null;
	$data = array_reverse($data);
	$i = 0;
	
	foreach($data as $link => $title){
		$map = Registry::get('serverdata/site').'/'.$link.'/';
		$html .= $i > 0 ? ' <span class="drop-sep">&gt;</span> ': null;
		
		if( (count($data) -1 ) == $i && isset($_GET['page']) ){
			$map .= '?page='.(int)$_GET['page'];
		}
		$html .= '<a href="'.$map.'">'.$title.'</a>';
		$i++;
	}
	
	return $html;
}

public static function linksToArray($id_position = 0, $id_link = 0, $domain = 'www', $level = 1)
{
	$out = new CmsOutput;
	$qq = $out->getLinks($id_link, $id_position, $domain);
	
	return $qq;
}

public static function links($id_position = 0, $id_link = 0, $domain = 'www', $level = 1)
{
	$html = null;
	$out = new CmsOutput;
	$qq = $out->getLinks($id_link, $id_position, $domain);
	
	if(count($qq)>0){
	
		$uc = $level > 1 ? 'dropdown-menu': 'nav navbar-nav navbar-left';
		$html .= '
		<ul class="'.$uc.'">';
		foreach($qq as $d){
			
			$title = mb_strlen($d['link_title'])>0 ? $d['link_title']: $d['title'];
			$url = mb_strlen($d['ext_link'])>0 ? $d['ext_link']: Registry::get('serverdata/site').'/'.$d['textmap'].'/';
			$target = $d['id_blank'] == 1 ? ' target="_blank"': null;
			
			$subHtml = self::links($id_position, $d['id_link'], $domain, $level + 1);
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


public static function showNav($config = array())
{
	$id_position = System::chef($config, 'id_position', 0);
	$id_link = System::chef($config, 'id_link', 0);
	$domain = System::chef($config, 'domain', 'www');
	$level = System::chef($config, 'level', 1);
	$cssClass = System::chef($config, 'cssClass', null);
	$lockOnLevel = System::chef($config, 'lockOnLevel', false);
	
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
			
			$subHtml = $lockOnLevel === false ? self::showNav(array(
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



public static function showGallery($id_dir = 0){
	
	$q = "SELECT * FROM "._SQLPREFIX_."_cms_media_list  
			WHERE id_dir = '".(int)$id_dir."' 
			ORDER BY public_ord, id_media DESC
		";
	
	//$qq = Db::result($q);
	$qq = Db::memAuto($q, 10);
	
	$result_count = count($qq); 
	$__root = Registry::get('serverdata/site').'/content/';
	
	$html = null;
	
	if($result_count>0)
	{
		
		$html =  '<!-- start of gallery block -->
		<div class="weebo_gallery_wrapper">
		';
		foreach($qq as $d){

			$fname = basename($d['path']);
			$dirname = dirname($d['path']);
			$html .=  '<div class="weebo_gallery_item">
					<a href="'.$__root.$d['path'].'" class="weebo_gallery_item_link" rel="tag" target="_blank" title="'.$d['title'].'">
						<img src="'.$__root.$dirname.'/th/th_'.$fname.'" alt="'.$d['title'].'" />
						<!-- '.$d['title'].' -->
					</a>
				</div>';
		}
		$html .=  '<div class="clear clearfix"></div>
		</div>
		<!-- start of gallery block -->';
	}
	
	return $html;
}

public static function sitemap($id_link = 0)
{
	$html = null;
	$out = new CmsOutput;
	$qq = $out->getSitemap($id_link, __CMS_DOMAIN__, __CMS_PAGE_LNG__);
	
	if(count($qq)>0){
	
		$html .= '<ul>';
		foreach($qq as $d){
			
			$title = mb_strlen($d['link_title'])>0 ? $d['link_title']: $d['title'];
			$url = mb_strlen($d['ext_link'])>0 ? $d['ext_link']: Registry::get('serverdata/site').'/'.$d['textmap'];
			$target = $d['id_blank'] == 1 ? ' target="_blank"': null;
			
			$html .= '<li><a href="'.$url.'" '.$target.' title="'.$title.'">'.$d['title'].'</a>
				'.self::sitemap($d['id_link']).'
			</li>';

		}
		$html .= '</ul>';
	}
	
	return $html;
}

/* Alternate navi */
public static function linksAlternate($id_position = 0, $id_link = 0, $domain = 'www')
{
	$html = null;
	$out = new CmsOutput;
	$qq = $out->getLinks($id_link, $id_position, $domain);
	
	if(count($qq)>0){
	
		foreach($qq as $d){
			
			$title = mb_strlen($d['link_title'])>0 ? $d['link_title']: $d['title'];
			$url = mb_strlen($d['ext_link'])>0 ? $d['ext_link']: Registry::get('serverdata/site').'/'.$d['textmap'].'/';
			$target = $d['id_blank'] == 1 ? ' target="_blank"': null;
			$actualClass = $d['textmap'] == __CMS_MAP__ ? ' class="menu-item menu-item-'.$d['id_link'].' menu-item-actual"': ' class="menu-item menu-item-'.$d['id_link'].'"'; 
			$ids = 'menu-item-'.$d['id_link']; 
			$actualLinkClass = $d['textmap'] == __CMS_MAP__ ? ' class="menu-item-link menu-item-link-'.$d['id_link'].' menu-item-actual-link"': ' class="menu-item-link menu-item-link-'.$d['id_link'].'"'; 
			
			$html .= '<div'.$actualClass.' id="'.$ids.'"><a href="'.$url.'" '.$target.' title="'.$title.'"'.$actualLinkClass.'>'.$d['title'].'</a></div>';
		}

	}
	
	return $html;
}

public static function contentSubWidget(
	$pageID = 0, 
	$config = array( 
		'briefLevel' => 0, // 0,1,2 OR ALL
		'title' => false,
		'titleLink' => false,
		'content' => false,
		'archiveLink' => false,
		'limit' => 10,
		'rss' => false,
		'keywords' => false
	)
)
{
	$p = "SELECT * FROM "._SQLPREFIX_."_cms_links WHERE id_link = '".(int)$pageID."' AND id_public = 1 LIMIT 1";
	//$qp = Db::result($p);
	$qp = Db::memAuto($p, 10);
	
	$pageData = count($qp)==1 ? $qp[0]: null;
	
	$myformat = Lng::get('cms/date_format');
	
	$html = null;
	
	if( !is_null($pageData) )
	{
		
		$myorder = $pageData['default_order'];
		
		$q = "SELECT * FROM "._SQLPREFIX_."_cms_content WHERE id_link = '".(int)$pageID."' AND id_public = 1 ";
		//$q.= Registry::get('userdata/logged_in') === 1 ? " AND secure IN(0,1) ": " AND secure IN(0,2) ";
		$q.= $config['briefLevel'] === 'ALL' ? null: " AND id_brief_level IN ('".(string)$config['briefLevel']."') ";
		$q.=" AND date_public <= ".__CMS_LTIME__." ";
		$q.=" AND CASE id_date_restrict WHEN 1 THEN publish_date_from <= ".(int)__CMS_LTIME__." WHEN 0 THEN publish_date_from>-1 END";
		$q.=" AND CASE id_date_restrict WHEN 1 THEN publish_date_to >= ".(int)__CMS_LTIME__." WHEN 0 THEN publish_date_to>-1 END";
		$q.=  " ORDER BY ".$myorder;
		$q.= (int)$config['limit'] > 0 ? " LIMIT ".(int)$config['limit']: null;
		
		//$qq = Db::result($q);
		$qq = Db::memAuto($q, 10);
		
		$result_count = count($qq); 
		
		$pageTitle = mb_strlen($pageData['title']) > 0 && $config['titleLink'] === true ? '<a href="'.Registry::get('serverdata/site').'/'.$pageData['textmap'].'/">'.$pageData['title'].'</a>': $pageData['title'];
		$pageTitle = mb_strlen($pageData['title']) > 0 && $config['title'] === true ? '<h2 class="weebo_widget_title">'.$pageTitle.'</h2>': null;
		
		$html =  $pageTitle;
		
		$html .=  $pageData['id_rss'] == 1 && $config['rss'] === true ? '<div class="weebo_rss_bar"><a href="'.Registry::get('serverdata/site').'/'.$pageData['textmap'].'/rss.xml">RSS</a></div>': null;
		
		$x = 0;
		
		$briefMode = $config['content'] === false ? 1: $config['briefLevel'];
		
		foreach($qq as $d)
		{
			$x++;
			$html .=  '<!-- start of html block -->
					<div class="subwidget subwidget'.$d['id_content'].' subwidget-'.$x.'">
				';
			$pubTitle = mb_strlen($d['title'])>0 ? $d['title']: null;
			$pubDate = $d['id_date_display']==1 ? '<span class="content_date">'.date($myformat, $d['date_public']).'</span>': null;
			$pubDateUpdate = $d['id_date_display']==1 && ($d['date_public']+(int)self::getCfg('updateInfoReleaseTime'))<$d['date_upd'] ? '<span class="content_date_update">'.Lng::get('cms/weebo_site_content_update').' '.date($myformat, $d['date_upd']).'</span>': null;
			$pubImpress = $d['impress']>0 ? '<span class="content_impress">'.Lng::get('cms/weebo_site_content_impress').' '.$d['impress'].'x</span>': null;
			$pubAnnotation = mb_strlen($d['annotation_text'])>0 ? $d['annotation_text'] : $d['content'];
			$pubLink = mb_strlen($d['ext_link'])>0 ? $d['ext_link']: Registry::get('serverdata/site').'/'.$pageData['textmap'].'/'.$d['textmap'].'.html';
			$pubLinkTarget = $d['id_blank']>0 ? ' target="_blank"': null;
			$backlink = isset($_GET['page']) && $_GET['page']>0 ? '?page='.$_GET['page']: null;	
			$pubKeywords = mb_strlen($d['keywords'])>0 ? self::showKeywords($d['keywords']) : null;
			
			switch($briefMode){
			case 1:
				/* LINK */  
				if(isset($pubTitle)){
					$html .=  '<h2 class="content_header_link">';
					$html .=  '<span class="content_header_text"><a href="'.$pubLink.$backlink.'" title="'.$d['title'].'"'.$pubLinkTarget.'>'.$d['title'].'</a></span></h2>';
					$html .=  !is_null($pubDate) ? '<div class="content_meta_info">'.$pubDate.' '.$pubDateUpdate.' '.$pubImpress.'</div>': null;  
				}   
					
			break;
			case 2:
				/* ANOTATION */
					$html .=  '<div class="list_annotation" id="list_annotation_'.$d['id_content'].'">'; 
					
				if(isset($pubTitle)){
					$html .=  '<h2 class="content_header_annotation_link">';
					$html .=  '<span class="content_header_text"><a href="'.$pubLink.$backlink.'" title="'.$d['title'].'"'.$pubLinkTarget.'>'.$d['title'].'</a></span></h2>';
					$html .=  !is_null($pubDate) ? '<div class="content_meta_info">'.$pubDate.' '.$pubDateUpdate.' '.$pubImpress.'</div>': null;  
					$html .=  !is_null($pubKeywords) && array_key_exists('keywords', $config) && $config['keywords'] === true ? '<div class="content_keywords">'.$pubKeywords.'</div>': null;  
				} 
					
				$anoteWidget = array('
					<a href="'.$pubLink.'"  title="'.$d['title'].'"'.$pubLinkTarget.' class="list_annotation_image_link annotation_no_image">
						<span class="annotation_no_image_wrapper">&nbsp;</span>
					</a>',
					' annotation_image_noshow');
			
				if(mb_strlen($d['annotation_image'])>0 && file_exists(Registry::get('serverdata/root').'/'.$d['annotation_image'])){
					
					$anoteDir = dirname($d['annotation_image']);
					$anoteFile = basename($d['annotation_image']);
					
					if( file_exists(Registry::get('serverdata/root').'/'.$anoteDir.'/th_'.$anoteFile) )
					{
						
						$anoteWidget = array('
							<a href="'.$pubLink.'" title="'.$d['title'].'"'.$pubLinkTarget.' class="list_annotation_image_link">
								<span class="annotation_image_wrapper"><img src="'.Registry::get('serverdata/site').'/'.$anoteDir.'/th_'.$anoteFile.'" alt="'.$d['title'].'" /></span>
							</a>',
							' annotation_image_show'); 
					}
				}
				
				$html .=  $anoteWidget[0];
				$html .=  mb_strlen($pubAnnotation)>0 ? '<p class="content_annotation'.$anoteWidget[1].'">'.mb_substr(strip_tags($pubAnnotation), 0, Registry::get('moduledata/cms/brief_size')).'</p>' : null;
				$html .=  '</div>'; 
			
			break;
			default: 
				/* FULL */
				if(isset($pubTitle)){
					$html .=  '<h2 class="content_header">';
					$html .=  '<span class="content_header_text">'.$d['title'].'</span></h2>';
					//$html .=  !is_null($pubDate) ? '<div class="content_meta_info">'.$pubDate.' '.$pubDateUpdate.' '.$pubImpress.'</div>': null;  
				} 
				 
				$html .=  $d['content']; 
			}
			 
			$html .=  '
					</div>
				'; 
			
			if($d['id_brief_level'] == 0){
				
				$viewScript = Registry::get('serverdata/root').'/mwms/modules/'.$d['display_script'];
				
				if(file_exists($viewScript)){
					ob_start();
					require($viewScript);
					$html .= ob_get_contents();
					ob_end_clean();
				}
			}
		
			$html .=  '
				<!-- end of html block -->
			'; 

		}
		
		$html .=  '';
		
		$archiveTitle = mb_strlen($pageData['title']) < 1 || $config['archiveLink'] === false ? null: '<div class="widget_archive_link"><a href="'.Registry::get('serverdata/site').'/'.$pageData['textmap'].'/">'.$config['archiveLink'].'</a></div>';
		$html .=  $archiveTitle;

	}
	
	return $html;
}

}
?>
