<?php
/* DEFUALT CMS DETAIL VIEW */

$html =  '<!-- start of html block --><div class="article textbox_detail textbox_detail_'.$d['id_content'].'" role="article">';

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




/*
 * 
 * 
 * GO
 * 
 * */
 
echo $html; 
?>
