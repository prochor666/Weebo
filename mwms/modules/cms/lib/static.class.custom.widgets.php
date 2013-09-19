<?php
class CmsWidget{

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
		
		/*
		$html .=  '
			<div class="widget-container">
		';
		*/
			
		foreach($qq as $d)
		{
			$html .=  '<!-- start of html block -->
					<div class="widget row widget_'.$d['id_content'].'" role="article"><div class="col-lg-12">
				';
			$pubTitle = mb_strlen($d['title'])>0 ? $d['title']: null;
			$pubDate = $d['id_date_display']==1 ? '<span class="content_date">'.date($myformat, $d['date_public']).'</span>': null;
			$pubDateUpdate = $d['id_date_display']==1 && ($d['date_public']+(int)self::getCfg('updateInfoReleaseTime'))<$d['date_upd'] ? '<span class="content_date_update">'.Lng::get('cms/weebo_site_content_update').' '.date($myformat, $d['date_upd']).'</span>': null;
			$pubImpress = $d['impress']>0 ? '<span class="content_impress">'.Lng::get('cms/weebo_site_content_impress').' '.$d['impress'].'x</span>': null;
			$pubAnnotation = mb_strlen($d['annotation_text'])>0 ? $d['annotation_text'] : $d['content'];
			$pubLink = mb_strlen($d['ext_link'])>0 ? $d['ext_link']: Registry::get('serverdata/site').'/'.$pageData['textmap'].'/'.$d['textmap'].'.html';
			$pubLinkTarget = $d['id_blank']>0 ? ' target="_blank"': null;
			$backlink = isset($_GET['page']) && $_GET['page']>0 ? '?page='.$_GET['page']: null;	
			$pubKeywords = mb_strlen($d['keywords'])>0 ? Render::showKeywords($d['keywords']) : null;
			
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
					//echo '<div class="list_annotation" id="list_annotation_'.$d['id_content'].'">'; 
					
				if(isset($pubTitle)){
					$html .=  '<article class="entry"><div class="detail hover-angle"><h2 class="widget_header">';
					$html .=  '<a href="'.$pubLink.$backlink.'" class="widget_link" title="'.$d['title'].'"'.$pubLinkTarget.'>'.$d['title'].'';
					//$html .=  !is_null($pubDate) ? '<div class="content_meta_info">'.$pubDate.' '.$pubDateUpdate.' '.$pubImpress.'</div>': null;  
					$html .=  !is_null($pubDate) ? ''.$pubDate.'': null;  
					$html .= '</a></h2></div>';
					$html .=  !is_null($pubKeywords) && array_key_exists('keywords', $config) && $config['keywords'] === true ? '<div class="widget_keywords">'.$pubKeywords.'</div>': null;  
				} 
					
				$anoteWidget = array('
						<span class="annotation_no_image"></span>
					',
					' annotation_image_noshow');
			
				if(mb_strlen($d['annotation_image'])>0 && file_exists(Registry::get('serverdata/root').'/'.$d['annotation_image'])){
					
					$anoteDir = dirname($d['annotation_image']);
					$anoteFile = basename($d['annotation_image']);
					
					if( file_exists(Registry::get('serverdata/root').'/'.$anoteDir.'/th_'.$anoteFile) )
					{
						
						$anoteWidget = array('
								<img src="'.Registry::get('serverdata/site').'/'.$anoteDir.'/th_'.$anoteFile.'" alt="'.$d['title'].'" />
							',
							' annotation_image_show'); 
					}
				}
				
				$html .= $anoteWidget[0];
				//$html .=  mb_strlen($pubAnnotation)>0 ? '<p class="content_annotation'.$anoteWidget[1].'">'.mb_substr(strip_tags($pubAnnotation), 0, Registry::get('moduledata/cms/brief_size')).'</p>' : null;
				$html .=  '</article></div>'; 
			
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
			
			
		';
		
		$archiveTitle = mb_strlen($pageData['title']) < 1 || $config['archiveLink'] === false ? null: '<div class="widget_archive_link"><a href="'.Registry::get('serverdata/site').'/'.$pageData['textmap'].'/">'.$config['archiveLink'].'</a></div>';
		$html .=  $archiveTitle;

	}
	
	return $html;
}


}

?>
