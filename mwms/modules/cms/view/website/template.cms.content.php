<?php
/* DEFUALT CMS LIST VIEW */

$query_limit = __CMS_PAGE_PAGE_DEFAULT__ * ( __CMS_PAGE_ACTUAL_PAGE__ - 1 );
$list = __CMS_PAGE_ID_PAGER__ == 1 && __CMS_PAGE_PAGE_DEFAULT__>0 && $result_count > __CMS_PAGE_PAGE_DEFAULT__ ? Db::final_items($qq, $query_limit, __CMS_PAGE_PAGE_DEFAULT__): $qq;
$pager = __CMS_PAGE_ID_PAGER__ == 1 && __CMS_PAGE_PAGE_DEFAULT__>0 && $result_count > __CMS_PAGE_PAGE_DEFAULT__ ? Navigator::pager_ajax_rewrite($result_count, __CMS_PAGE_PAGE_DEFAULT__, $custom_uri = Registry::get('serverdata/site').'/'.__CMS_MAP__.'/?', $actual_page, 'page', 3): null;

$html =  $pager;

foreach($list as $d)
{
	$html .=  '
		<!-- start of html block -->
		<div class="article textbox textbox_'.$d['id_content'].'" role="article">
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



/*
 * 
 * 
 * GO
 * 
 * */
 
echo $html; 
?>
