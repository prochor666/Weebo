<?php
class CmsApi{

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

public static function moduleContent(){
	if(!is_null(__CMS_DOCUMENT__)){
		return self::contentDetail();
	}
	
	return self::content();
}

public static function logImpression($id_content){
	
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
		$q = "UPDATE "._SQLPREFIX_."_cms_content SET impress = impress+1 WHERE id_content = '".(int)$id_content."' ";
		Db::query($q);
	}

}

public static function getLinkByID($id_link = 0)
{
	$c = new CmsOutput;
	$d = $c->getLinkData($id_link);
	header('content-type: application/json; charset=utf-8');
	
	if(count($d)>0)
	{
		define('__CMS_PAGE_ID__', (int)$d['id_link']);
		define('__CMS_MAP__', $d['textmap']);
		define('__CMS_DOMAIN__', $d['domain']);
		define('__CMS_PAGE_NAME__', $d['title']);
		define('__CMS_PAGE_TITLE__', $d['link_title']);
		define('__CMS_PAGE_DEFAULT_ORDER__', $d['default_order']);
		define('__CMS_PAGE_LNG__', $d['lng']);
		define('__CMS_LTIME__', time());
		
		$page = array(
			'status' => true, 
			'query' => (int)$id_link,
			'id_link' => __CMS_PAGE_ID__,
			'textmap' => __CMS_MAP__,
			'name' => __CMS_PAGE_NAME__,
			'title' => __CMS_PAGE_TITLE__,
			'description' => $d['description'],
			'content' => self::content(),
		);
		
		return json_encode($page);
	}

return json_encode(array('status' => false, 'query' => $id_link));
}

public static function getLinkByTextmap($textmap = null)
{
	$c = new CmsOutput;
	$d = $c->getLinkID($textmap);
	header('content-type: application/json; charset=utf-8');
	
	if(count($d)>0)
	{
		define('__CMS_PAGE_ID__', (int)$d['id_link']);
		define('__CMS_MAP__', $d['textmap']);
		define('__CMS_DOMAIN__', $d['domain']);
		define('__CMS_PAGE_NAME__', $d['title']);
		define('__CMS_PAGE_TITLE__', $d['link_title']);
		define('__CMS_PAGE_DEFAULT_ORDER__', $d['default_order']);
		define('__CMS_PAGE_LNG__', $d['lng']);
		define('__CMS_LTIME__', time());
		
		$page = array(
			'status' => true, 
			'query' => $textmap,
			'id_link' => __CMS_PAGE_ID__,
			'textmap' => __CMS_MAP__,
			'name' => __CMS_PAGE_NAME__,
			'title' => __CMS_PAGE_TITLE__,
			'description' => $d['description'],
			'content' => self::content(),
		);
		
		return json_encode($page);
	}

return json_encode(array('status' => false, 'query' => $textmap));
}

public static function content()
{
	$mode = 0;
	if(isset($_GET['mode'])){
		$mode = (int)$_GET['mode']>=0 && (int)$_GET['mode']<4 ? (int)$_GET['mode']: 0;
	}
	
	$items = array();
	
	$myformat = Lng::get('cms/date_format');
	
	$q = "SELECT * FROM "._SQLPREFIX_."_cms_content WHERE id_link = '".__CMS_PAGE_ID__."' ";
	$q.= " AND id_public = 1 ";
	$q.= " AND date_public <= ".__CMS_LTIME__."";
	$q.= " AND CASE id_date_restrict WHEN 1 THEN publish_date_from <= ".__CMS_LTIME__." WHEN 0 THEN publish_date_from>-1 END";
	$q.= " AND CASE id_date_restrict WHEN 1 THEN publish_date_to >= ".__CMS_LTIME__." WHEN 0 THEN publish_date_to>-1 END";
	$q.=  " ORDER BY ".__CMS_PAGE_DEFAULT_ORDER__;
	
	$qq = Db::memAuto($q, 10);
	
	foreach($qq as $d)
	{
		$d['id_content'] = (int)$d['id_content'];
		$d['date_public'] = (int)$d['date_public'];
		$annotationImageDirRel = dirname($d['annotation_image']);
		$annotationImageFileRel = basename($d['annotation_image']);
		$d['annotation_image'] = mb_strlen($annotationImageFileRel) > 5 ? Registry::get('serverdata/site').'/'.$annotationImageDirRel.'/'.$annotationImageFileRel: null;
		$d['annotation_image_thumb'] = mb_strlen($annotationImageFileRel) > 5 ? Registry::get('serverdata/site').'/'.$annotationImageDirRel.'/th_'.$annotationImageFileRel: null;
		
		if($mode == 1 || $mode == 0){
			$d['content'] = str_replace('content/'.__CMS_DOMAIN__, Registry::get('serverdata/site').'/'._GLOBALDATADIR_.'/'.__CMS_DOMAIN__, $d['content']);
			$d['content'] = str_replace('/http://', 'http://', $d['content']);
		}else{
			unset($d['content']);
		}
		
		if($mode == 2 || $mode == 0 ){
			switch($d['display_script']){
				case 'cms/view/website/view.gallery.php';
					$d['contentType'] = 'gallery';
					$id_dir = explode(':', $d['display_script_param']);
					$d['extraData'] = self::showGallery((int)$id_dir[1]);
				break; default:
					$d['contentType'] = 'simple';
					$d['extraData'] = $d['display_script_param'];
			}
			
		}
		
		unset($d['display_script'], $d['display_script_param'], $d['textmap'], $d['ext_link'], $d['keywords'], $d['id_public'], $d['id_blank'], $d['id_brief_level'], $d['id_type'], $d['id_rss'], $d['id_sticky'], $d['id_date_display'], $d['id_date_restrict'], $d['publish_date_from'], $d['publish_date_to'], $d['id_link'], $d['id_ins'], $d['id_upd'], $d['date_ins'], $d['date_upd'], $d['public_order'], $d['impress']);
		
		array_push($items, $d);
		self::logImpression((int)$d['id_content']);
	}
	
	return $items;
}


public static function contentDetail($id_content = 0)
{
	header('content-type: application/json; charset=utf-8');
	
	$myformat = Lng::get('cms/date_format');
	define('__CMS_LTIME__', time());
	
	$q = "SELECT * FROM "._SQLPREFIX_."_cms_content WHERE id_content = '".$id_content."' ";
	//$q.= Registry::get('userdata/logged_in') === 1 ? " AND secure IN(0,1) ": " AND secure IN(0,2) ";
	//$q.= " AND id_public = 1 AND textmap LIKE '".__CMS_DOCUMENT__."' ";
	$q.= " AND id_public = 1 ";
	$q.= " AND date_public <= ".__CMS_LTIME__." ";
	$q.= " AND CASE id_date_restrict WHEN 1 THEN publish_date_from <= ".__CMS_LTIME__." WHEN 0 THEN publish_date_from>-1 END";
	$q.= " AND CASE id_date_restrict WHEN 1 THEN publish_date_to >= ".__CMS_LTIME__." WHEN 0 THEN publish_date_to>-1 END";
	$q.= " LIMIT 1";
	
	//$qq = Db::result($q);
	$qq = Db::memAuto($q, 10);
	$result_count = count($qq); 
	
	$d = array();
	
	if($result_count==1)
	{
		$d = $qq[0];
		
		$c = new CmsOutput;
		$l = $c->getLinkData($d['id_link']);
		
		define('__CMS_PAGE_ID__', (int)$l['id_link']);
		define('__CMS_MAP__', $l['textmap']);
		define('__CMS_DOMAIN__', $l['domain']);
		define('__CMS_PAGE_NAME__', $l['title']);
		define('__CMS_PAGE_TITLE__', $l['link_title']);
		define('__CMS_PAGE_DEFAULT_ORDER__', $l['default_order']);
		define('__CMS_PAGE_LNG__', $l['lng']);
		
		$d['id_content'] = (int)$d['id_content'];
		$d['date_public'] = (int)$d['date_public'];
		$annotationImageDirRel = dirname($d['annotation_image']);
		$annotationImageFileRel = basename($d['annotation_image']);
		$d['annotation_image'] = mb_strlen($annotationImageFileRel) > 5 ? Registry::get('serverdata/site').'/'.$annotationImageDirRel.'/'.$annotationImageFileRel: null;
		$d['annotation_image_thumb'] = mb_strlen($annotationImageFileRel) > 5 ? Registry::get('serverdata/site').'/'.$annotationImageDirRel.'/th_'.$annotationImageFileRel: null;
		
		$d['content'] = str_replace('content/'.__CMS_DOMAIN__, Registry::get('serverdata/site').'/'._GLOBALDATADIR_.'/'.__CMS_DOMAIN__, $d['content']);
		$d['content'] = str_replace('/http://', 'http://', $d['content']);
		
		switch($d['display_script']){
			case 'cms/view/website/view.gallery.php';
				$d['contentType'] = 'gallery';
				$id_dir = explode(':', $d['display_script_param']);
				$d['extraData'] = self::showGallery((int)$id_dir[1]);
			break; default:
				$d['contentType'] = 'simple';
				$d['extraData'] = $d['display_script_param'];
		}
		
		unset($d['display_script'], $d['display_script_param'], $d['textmap'], $d['ext_link'], $d['keywords'], $d['id_public'], $d['id_blank'], $d['id_brief_level'], $d['id_type'], $d['id_rss'], $d['id_sticky'], $d['id_date_display'], $d['id_date_restrict'], $d['publish_date_from'], $d['publish_date_to'], $d['id_ins'], $d['id_upd'], $d['date_ins'], $d['date_upd'], $d['public_order'], $d['impress']);
		
		self::logImpression((int)$d['id_content']);
		
		return json_encode(array('status' => true, 'query' => $id_content, 'content' => $d));
	}
	
	return json_encode(array('status' => false, 'query' => $id_content));
}

public static function links($lng = null)
{
	$domain = array_shift(explode(".",$_SERVER['HTTP_HOST']));
	define('__CMS_DOMAIN__', $domain);
	
	$items = array();
	$out = new CmsOutput;
	$qq = $out->getLinksByLng($lng);
	
	if(count($qq)>0)
	{
		foreach($qq as $d)
		{
			$page = array(
				'status' => true, 
				'query' => $lng,
				'id_link' => (int)$d['id_link'],
				'textmap' => $d['textmap'],
				'name' => $d['title'],
				'title' => $d['link_title'],
				'lng' => $d['lng'],
				'description' => $d['description'],
			);
			array_push($items, $page);
		}
		return json_encode($items);
	}

return json_encode(array('status' => false, 'query' => $lng));
}


public static function showGallery($id_dir = 0){
	
	$q = "SELECT * FROM "._SQLPREFIX_."_cms_media_list  
			WHERE id_dir = '".(int)$id_dir."' 
			ORDER BY public_ord, id_media DESC
		";
	
	//$qq = Db::result($q);
	$qq = Db::memAuto($q, 10);
	
	$result_count = count($qq); 
	
	$items = array();
	
	if($result_count>0)
	{
		foreach($qq as $d){
			$fname = basename($d['path']);
			$dirname = dirname($d['path']);
			
			$imageDirRel = dirname($d['path']);
			$imageFileRel = basename($d['path']);
			
			$image = array(
				'id_media' => (int)$d['id_media'],
				'title' => $d['title'],
				'image' => Registry::get('serverdata/site').'/'._GLOBALDATADIR_.'/'.$imageDirRel.'/'.$imageFileRel,
				'image_thumb' => Registry::get('serverdata/site').'/'._GLOBALDATADIR_.'/'.$imageDirRel.'/th/th_'.$imageFileRel,
			);
			array_push($items, $image);
		}
	}
	
	return $items;
}


}
?>
