<?php
class Cms{

public $page_default, $query_limit, $result_count, 
		$order_direction, $search_term, $view_url, $view_url_suffix, 
		$thumb_url_prefix, $thumb_url_suffix, $filterReg, $order_case, 
		$filter_indexes, $order_default_direction,$lng,$config;

protected $default_aspect_ratio, $default_thumb_x, $default_thumb_y, $browser_modes;

public function __construct(){
	$this->lng = Lng::get('cms') + Lng::get('system');
	$this->config = Registry::get('moduledata/cms');
	$this->page_default = 20;
	$this->query_limit = 0;
	$this->result_count = 0;
	$this->view_url = Registry::get('serverdata/path').'?module='.Registry::get('active_admin_module').'&amp;sub=';
	$this->view_url_suffix = null;
	$this->initial_sub = 'links.browser';
	$this->ajax_view_url = Ajax::path().'require&amp;file=/mwms/modules/cms/view/';
	$this->ajax_view_url_suffix = '&amp;sysadmin='.Registry::get('userdata/id_user').'';
	$this->thumbCache = System::root().'/'._GLOBALCACHEDIR_.'/media_module_cache';
	$this->thumbCacheUri = System::path().'/'._GLOBALCACHEDIR_.'/media_module_cache';
	$this->initialDirType = 'images';
	$this->rootDir = _GLOBALDATADIR_;
	
	if(!isset($_SESSION['cms_filter_registry'])){
		$_SESSION['cms_filter_registry'] = array();
	}
	
	$this->filterReg = $_SESSION['cms_filter_registry']; 
	
	$this->order_case = array(
		"links" => "id_position,public_order,id_link",
		"content" => "id_sticky DESC, public_order, id_content",
		"media" => "public_ord",
		"form" => "id_form"
	);
	
	$this->order_default_direction = array(
		"links" => "ASC",
		"content" => "DESC",
		"media" => "ASC",
		"form" => "DESC"
	);
	
	$this->filter_indexes = array();
}

public function dropBox($reg){
	
	$html = '<div class="mwms-dropbox-items">';
	foreach($this->filterReg[$reg."_dropbox"]  as $id){
		$html .= '<input type="checkbox" name="src-'.$id.'[]" value="'.$id.'" id="src-'.$id.'" checked="checked" />';
	}
	$html .= '</div>';
	
	return $html; 
}

public function dropBoxCount($reg){
	
	return count($this->filterReg[$reg."_dropbox"]); 
}

protected function xmlTransform($data, $header = false, $rootElement = "item"){
	
	$xml = $header ? '<?xml version="1.0" encoding="utf-8"?><'.$rootElement.'>':'<'.$rootElement.'>';
	
	foreach($data as $key => $value){
		$xml .= '<'.$key.'>'.$value.'</'.$key.'>';	
	}
	
	$xml .= '</'.$rootElement.'>';
	
	return $xml;
}

public function filterInit(){
	
	$last = array();
	
	foreach($this->order_case as $submod => $default){
		
		$last[$submod."_order"] = array_key_exists($submod."_order",$this->filterReg) ? $this->filterReg[$submod."_order"]: $this->setDefaultFilterState($submod);
		$last[$submod."_order_direction"] = array_key_exists($submod."_order_direction",$this->filterReg) ? $this->filterReg[$submod."_order_direction"]: $this->setDefaultDirection($submod);
		
		$this->filter_indexes[$submod."_dropbox"] = array_key_exists($submod."_dropbox",$this->filterReg) && is_array($this->filterReg[$submod."_dropbox"]) ? $this->filterReg[$submod."_dropbox"]: array();
		$this->filter_indexes[$submod."_order"] = array_key_exists($submod."_order",$this->filterReg) ? $this->filterReg[$submod."_order"]: $this->setDefaultFilterState($submod);
		$this->filter_indexes[$submod."_order_direction"] = array_key_exists($submod."_order_direction",$this->filterReg) ? $this->filterReg[$submod."_order_direction"]: $this->setDefaultDirection($submod);
		
		$this->filter_indexes[$submod."_page"] = array_key_exists($submod."_page",$this->filterReg) ? $this->filterReg[$submod."_page"]: 1;
		
		
		$this->filter_indexes[$submod."_search_term"] = array_key_exists($submod."_search_term",$this->filterReg) ? $this->filterReg[$submod."_search_term"]: null;
	}
	
	foreach($this->filter_indexes as $key => $index){
		
		$_SESSION['cms_filter_registry'][$key] = array_key_exists($key, $_SESSION['cms_filter_registry']) ? $_SESSION['cms_filter_registry'][$key]: $this->filter_indexes[$key];
		
		if(is_array($index)){
			
			if(isset($_GET[$key]) && !array_key_exists($_GET[$key], $index) && !isset($_GET['regaction']))
			{ 
				$index[$_GET[$key]] = $_GET[$key]; 
			}elseif(isset($_GET[$key]) && array_key_exists($_GET[$key], $index) && isset($_GET['regaction']) && $_GET['regaction']=='remove'){
				unset($index[$_GET[$key]]);
			}elseif(isset($_GET['regaction']) && $_GET['regaction']=='reset'){
				$index = array();
			}
			$index = array_unique($index);
			$_SESSION['cms_filter_registry'][$key] = $index;

		}else{
			
			$ident = explode("_", $key);
			$pageChange[$ident[0]] = true;
			
			if( 
			
				( 
					count($ident) == 2 
					&& $ident[1] == "order" 
					&& isset($_GET[$ident[0]."_order"]) 
					&& $last[$ident[0]."_order"] != $_GET[$ident[0]."_order"] 
				) 
				
				|| 
				
				( 
					count($ident) == 3 
					&& $ident[1] == "order" 
					&& $ident[2] == "direction" 
					&& isset($_GET[$ident[0]."_order_direction"]) 
					&& $last[$ident[0]."_order_direction"] != $_GET[$ident[0]."_order_direction"] 
				) 
				
			)
			{
				$_GET[$ident[0]."_page"] = 1;
				$pageChange[$ident[0]] = false;
			}
			
			$_SESSION['cms_filter_registry'][$key] = isset($_GET[$key]) ? $_GET[$key]: $_SESSION['cms_filter_registry'][$key];
		}
	}
	
	if(isset($_GET[$submod."_order"]) && $last[$submod."_order"] != $_GET[$submod."_order"] ){
		$_SESSION['cms_filter_registry'][$submod."_page"] = 1;
	}
	
	$this->filterReg = $_SESSION['cms_filter_registry'];
}

public function setDefaultDirection($submod){
	return $this->order_default_direction[$submod];
}

public function setDefaultFilterState($index){
	return $this->order_case[$index];
}

public function showStaticDashboard(){
	$html = '
		<div class="cms_menu">
			<span id="domain-setter"><strong>'.$this->lng['mwms_link_domain_lng'].'</strong> '.$this->setDomainLng().' <strong>'.$this->lng['mwms_link_domain'].'</strong> '.$this->setDomain().'</span>
			<a href="?module=cms&amp;sub=links.browser" id="links.browser" title="'.$this->lng['mwms_cms_links'].'">'.$this->lng['mwms_cms_links'].'</a>
			<a href="?module=cms&amp;sub=media.browser" id="media.browser" title="'.$this->lng['mwms_cms_gallery'].'">'.$this->lng['mwms_cms_gallery'].'</a>
			<a href="?module=cms&amp;sub=forms.browser" id="forms.browser" title="'.$this->lng['mwms_cms_forms'].'">'.$this->lng['mwms_cms_forms'].'</a>
		</div>
	';
  
	return $html;
}

public function setDomain(){
	
	$domains = $this->lng['cms_public_domains'];
	$html = '<select name="set_domain" class="select domain-set" id="set_domain">';
	foreach($domains as $domain => $data){
		$html .= '<option value="'.$domain.'" '.Validator::selected($domain, Registry::get('active_domain')).'>'.$data['title'].' ['.$data['name'].']</option>';
	}
	$html .= '</select>';
	
	return $html;
}

public function setDomainLng(){
	
	$lngs = $this->lng['lng_list'];
	$html = '<select name="set_domain_lng" class="select domain-lng-set" id="set_domain_lng">';
	foreach($lngs as $lng => $name){
		$html .= '<option value="'.$lng.'" '.Validator::selected($lng, Registry::get('active_domain_lng')).'>'.$name.'</option>';
	}
	$html .= '</select>';
	
	return $html;
}

public function getLngList($symbolConfig, $obj, $value){

	$type = $symbolConfig['system_type'];
	$real_type = $symbolConfig['predefined'] ==  1 ? 'multi': $symbolConfig['system_type'];
	$size = $symbolConfig['size'];
	$id_meta = $symbolConfig['name'];
	$table = $symbolConfig['table'];
	$cleanup = $symbolConfig['cleanup'];

	$valueShow = mb_strlen(DataValidator::displayData($value, $type))<1 && array_key_exists('default_value', $symbolConfig) ? DataValidator::displayData($symbolConfig['default_value'], $type): DataValidator::displayData($value, $type);

	$meta_name = 'meta_value_'.$id_meta;
	$field_id = 'edit_field_'.$obj->HtmlIdSuffix.'_'.$id_meta;

	$html = '<select name="'.$meta_name.'" class="select meta_live_edit" id="'.$field_id.'">';
	
	foreach($this->lng['lng_list'] as $script => $name){
		$html .= '<option value="'.$script.'" '.Validator::selected($valueShow, $script).'>'.$name.'</option>';
	}
	$html .= '</select>';
	
	return $html;
}

public function setOrder($index, $value, $sqldirection, $title, $view, $ajax = false){
	
	$prefix = !$ajax ? $this->view_url: $this->ajax_view_url;
	$suffix = !$ajax ? $this->view_url_suffix: $this->ajax_view_url_suffix;
	
	$value = urlencode($value);
	
	return '<div class="order_box">
				<a class="order_asc_icon" title="'.$this->lng['order_title_asc'].' '.$title.'" href="'.$prefix.$view.$suffix.'&amp;'.$index.'='.$value.'&amp;'.$sqldirection.'=ASC"><span class="ui-icon ui-icon-triangle-1-n"></span></a> 
				<a class="order_desc_icon" title="'.$this->lng['order_title_desc'].' '.$title.'" href="'.$prefix.$view.$suffix.'&amp;'.$index.'='.$value.'&amp;'.$sqldirection.'=DESC"><span class="ui-icon ui-icon-triangle-1-s"></span></a>
			</div>
		';
} 

/* viewlist */
public function getViews($symbolConfig, $obj, $value){
	
	$type = $symbolConfig['system_type'];
	$real_type = $symbolConfig['predefined'] ==  1 ? 'multi': $symbolConfig['system_type'];
	$size = $symbolConfig['size'];
	$id_meta = $symbolConfig['name'];
	$table = $symbolConfig['table'];
	$cleanup = $symbolConfig['cleanup'];

	$valueShow = mb_strlen(DataValidator::displayData($value, $type))<1 && array_key_exists('default_value', $symbolConfig) ? DataValidator::displayData($symbolConfig['default_value'], $type): DataValidator::displayData($value, $type);

	$meta_name = 'meta_value_'.$id_meta;
	$field_id = 'edit_field_'.$obj->HtmlIdSuffix.'_'.$id_meta;

	$html = '<select name="'.$meta_name.'" class="select meta_live_edit" id="'.$field_id.'">';
	
	$w = Registry::get('output_scripts');
	
	ksort($w);
	//$w = $this->lng['cms_public_views'];
	
	foreach($w as $script => $name){
		$html .= '<option value="'.$script.'" '.Validator::selected($valueShow, $script).'>'.$name.'</option>';
	}
	$html .= '</select>';
	
	return $html;
}

/* methodlist */
public function getMethodValue($symbolConfig, $obj, $value){
	
	$type = $symbolConfig['system_type'];
	$real_type = $symbolConfig['predefined'] ==  1 ? 'multi': $symbolConfig['system_type'];
	$size = $symbolConfig['size'];
	$id_meta = $symbolConfig['name'];
	$table = $symbolConfig['table'];
	$cleanup = $symbolConfig['cleanup'];

	$valueShow = mb_strlen(DataValidator::displayData($value, $type))<1 ? null: DataValidator::displayData($value, $type);

	$meta_name = 'meta_value_'.$id_meta;
	$field_id = 'edit_field_'.$obj->HtmlIdSuffix.'_'.$id_meta;

	//$w = $this->lng['cms_public_views'];
	$html = '<input type="hidden" name="'.$meta_name.'" class="text meta_live_edit" id="'.$field_id.'" value="'.$valueShow.'" /><div id="'.$field_id.'_method_helper"></div>';
	
	return $html;
}

public function getMethod($script, $data){
	
	$html = null;
	$w = Registry::get('output_methods');
	
	$call = array_key_exists($script, $w) ? $w[$script]: null;
	
	if(!is_null($call)){
		$___request_method = $call;
		$___request_params = array($data);
		$___request_method = explode('::', $___request_method);
		
		$html = call_user_func_array(array($___request_method[0] ,$___request_method[1]), $___request_params);
	}

	return $html;
}


/* content order types */
public function getOrderTypes($symbolConfig, $obj, $value){
	
	$type = $symbolConfig['system_type'];
	$size = $symbolConfig['size'];
	$id_meta = $symbolConfig['name'];
	$table = $symbolConfig['table'];
	$cleanup = $symbolConfig['cleanup'];

	$valueShow = mb_strlen(DataValidator::displayData($value, $type))<1 && array_key_exists('default_value', $symbolConfig) ? DataValidator::displayData($symbolConfig['default_value'], $type): DataValidator::displayData($value, $type);

	$meta_name = 'meta_value_'.$id_meta;
	$field_id = 'edit_field_'.$obj->HtmlIdSuffix.'_'.$id_meta;

	$html = '<select name="'.$meta_name.'" class="select meta_live_edit" id="'.$field_id.'">';
	
	foreach($this->lng['cms_public_orders'] as $order => $name){
		$html .= '<option value="'.$order.'" '.Validator::selected($valueShow, $order).'>'.$name.'</option>';
	}
	$html .= '</select>';
	
	return $html;
}

/* content display mode */
public function getDisplayModes($symbolConfig, $obj, $value){
	
	$type = $symbolConfig['system_type'];
	$real_type = $symbolConfig['predefined'] ==  1 ? 'multi': $symbolConfig['system_type'];
	$size = $symbolConfig['size'];
	$id_meta = $symbolConfig['name'];
	$table = $symbolConfig['table'];
	$cleanup = $symbolConfig['cleanup'];

	$valueShow = mb_strlen(DataValidator::displayData($value, $type))<1 && array_key_exists('default_value', $symbolConfig) ? DataValidator::displayData($symbolConfig['default_value'], $type): DataValidator::displayData($value, $type);

	$meta_name = 'meta_value_'.$id_meta;
	$field_id = 'edit_field_'.$obj->HtmlIdSuffix.'_'.$id_meta;

	$html = '<select name="'.$meta_name.'" class="select meta_live_edit" id="'.$field_id.'">';
	
	foreach($this->lng['cms_public_display_modes'] as $mode => $name){
		$html .= '<option value="'.$mode.'" '.Validator::selected($valueShow, $mode).'>'.$name.'</option>';
	}
	$html .= '</select>';
	
	return $html;
}

/* content menu positions */
public function getMenuPosition($symbolConfig, $obj, $value){
	
	$type = $symbolConfig['system_type'];
	$real_type = $symbolConfig['predefined'] ==  1 ? 'multi': $symbolConfig['system_type'];
	$size = $symbolConfig['size'];
	$id_meta = $symbolConfig['name'];
	$table = $symbolConfig['table'];
	$cleanup = $symbolConfig['cleanup'];

	$valueShow = mb_strlen(DataValidator::displayData($value, $type))<1 && array_key_exists('default_value', $symbolConfig) ? DataValidator::displayData($symbolConfig['default_value'], $type): DataValidator::displayData($value, $type);

	$meta_name = 'meta_value_'.$id_meta;
	$field_id = 'edit_field_'.$obj->HtmlIdSuffix.'_'.$id_meta;

	$html = '<select name="'.$meta_name.'" class="select meta_live_edit" id="'.$field_id.'">';
	
	$domain = Registry::get('active_domain');
	
	//foreach($this->lng['cms_public_positions'] as $position => $name){
	foreach($this->lng['cms_public_domains'][$domain]['cms_public_positions'] as $position => $name){
		$html .= '<option value="'.$position.'" '.Validator::selected($valueShow, $position).'>'.$name.'</option>';
	}
	$html .= '</select>';
	
	return $html;
}

/* page template */
public function getTemplates($symbolConfig, $obj, $value){
	$type = $symbolConfig['system_type'];
	$real_type = $symbolConfig['predefined'] ==  1 ? 'multi': $symbolConfig['system_type'];
	$size = $symbolConfig['size'];
	$id_meta = $symbolConfig['name'];
	$table = $symbolConfig['table'];
	$cleanup = $symbolConfig['cleanup'];

	$valueShow = mb_strlen(DataValidator::displayData($value, $type))<1 && array_key_exists('default_value', $symbolConfig) ? DataValidator::displayData($symbolConfig['default_value'], $type): DataValidator::displayData($value, $type);

	$meta_name = 'meta_value_'.$id_meta;
	$field_id = 'edit_field_'.$obj->HtmlIdSuffix.'_'.$id_meta;

	$html = '<select name="'.$meta_name.'" class="select meta_live_edit" id="'.$field_id.'">';
	
	$domain = Registry::get('active_domain');
	
	foreach($this->lng['cms_public_domains'][$domain]['templates'] as $template => $name){
		$html .= '<option value="'.$template.'" '.Validator::selected($valueShow, $template).'>'.$name.'</option>';
	}
	$html .= '</select>';
	
	return $html;
}

public function getAnnotationImage($symbolConfig, $obj, $value){
	
	$type = $symbolConfig['system_type'];
	$real_type = $symbolConfig['predefined'] ==  1 ? 'multi': $symbolConfig['system_type'];
	$size = $symbolConfig['size'];
	$id_meta = $symbolConfig['name'];
	$table = $symbolConfig['table'];
	$cleanup = $symbolConfig['cleanup'];

	$valueShow = mb_strlen(DataValidator::displayData($value, $type))<1 ? null: DataValidator::displayData($value, $type);

	$meta_name = 'meta_value_'.$id_meta;
	$field_id = 'edit_field_'.$obj->HtmlIdSuffix.'_'.$id_meta;
	//System::dump($symbolConfig);
	
	$img = null;
	$proxy = null;
	$cssinit = 'hide';
	
	if(mb_strlen($valueShow)>0){
		$img = $valueShow;
		/*
		$ext = System::extension($img);
		$cssinit = '';
		$proxy = $this->thumbCacheUri.'/'.System::hash($img).'.'.$ext;
		*/
		$cssinit = '';
		$fileName = basename($img);
		$dirName = dirname($img);
		
		$proxy = Registry::get('serverdata/site').'/'.$dirName.'/th_'.$fileName;
	}
	
	$html = '<input type="hidden" name="'.$meta_name.'" class="text meta_live_edit content_annotation_thumb" id="'.$field_id.'" value="'.$valueShow.'" /><img src="'.$proxy.'" alt="thumb" id="'.$field_id.'_thumb" class="content_annotation '.$cssinit.'" />';
	return $html;
}

public function getMediaThumb($symbolConfig, $obj, $value){
	
	$type = $symbolConfig['system_type'];
	$real_type = $symbolConfig['predefined'] ==  1 ? 'multi': $symbolConfig['system_type'];
	$size = $symbolConfig['size'];
	$id_meta = $symbolConfig['name'];
	$table = $symbolConfig['table'];
	$cleanup = $symbolConfig['cleanup'];

	$valueShow = mb_strlen(DataValidator::displayData($value, $type))<1 ? null: DataValidator::displayData($value, $type);

	$meta_name = 'meta_value_'.$id_meta;
	$field_id = 'edit_field_'.$obj->HtmlIdSuffix.'_'.$id_meta;
	//System::dump($symbolConfig);
	
	$html = '<input type="hidden" name="'.$meta_name.'" class="text meta_live_edit content_annotation_thumb" id="'.$field_id.'" value="'.$valueShow.'" />';
	
	if(mb_strlen($valueShow)>0){
		
		$base = basename($valueShow);
		$dir = dirname($valueShow);
			
		$imgThPath = System::getFileUrl($dir.'/th/th_'.$base);
		
		$html .= '<img src="'.$imgThPath.'" alt="thumb" id="'.$field_id.'_thumb" title="'.$dir.'/'.$base.'" class="content_annotation" />';
		
	}
	
	return $html;
}


public function selectDirType($symbolConfig, $obj, $value){
	
	$type = $symbolConfig['system_type'];
	$real_type = $symbolConfig['predefined'] ==  1 ? 'multi': $symbolConfig['system_type'];
	$size = $symbolConfig['size'];
	$id_meta = $symbolConfig['name'];
	$table = $symbolConfig['table'];
	$cleanup = $symbolConfig['cleanup'];

	$valueShow = mb_strlen(DataValidator::displayData($value, $type))<1 ? null: DataValidator::displayData($value, $type);

	$meta_name = 'meta_value_'.$id_meta;
	$field_id = 'edit_field_'.$obj->HtmlIdSuffix.'_'.$id_meta;
	
	$html = '<select name="'.$meta_name.'" class="select meta_live_edit" id="'.$field_id.'">';
	
	foreach($this->lng['mwms_media_dir_types'] as $type => $name){
		$html .= '<option value="'.$type.'" '.Validator::selected($valueShow, $type).'>'.$name.'</option>';
	}
	
	$html .= '</select>';
	
	return $html;
}

public function setPath($symbolConfig, $obj, $value){
	
	$type = $symbolConfig['system_type'];
	$real_type = $symbolConfig['predefined'] ==  1 ? 'multi': $symbolConfig['system_type'];
	$size = $symbolConfig['size'];
	$id_meta = $symbolConfig['name'];
	$table = $symbolConfig['table'];
	$cleanup = $symbolConfig['cleanup'];

	$valueShow = mb_strlen(DataValidator::displayData($value, $type))<1 ? null: DataValidator::displayData($value, $type);

	$meta_name = 'meta_value_'.$id_meta;
	$field_id = 'edit_field_'.$obj->HtmlIdSuffix.'_'.$id_meta;
	
	$html = '<input type="hidden" name="'.$meta_name.'" class="text meta_live_edit content_annotation_thumb" id="'.$field_id.'" value="'.$valueShow.'" /><span id="'.$field_id.'_pathinfo">'.$valueShow.'</span>';
	return $html;
	
	return $html;
}

public function toggleOnOff($onoff){
	return $onoff == 1 ? 'on': 'off';
}

public function isImage($file){
	$ext = System::extension($file);
	return $ext == 'jpg' || $ext == 'png' || $ext == 'gif' ? true: false;
}

public function checkDomainDataDir($domain){
	Storage::makeDir('content/'.$domain);
	Storage::makeDir('content/'.$domain.'/gallery');
	Storage::makeDir('content/'.$domain.'/media');
	Storage::makeDir('content/'.$domain.'/annotations');
	Storage::makeDir('content/'.$domain.'/users');
}

public function contentLinksSelect($symbolConfig, $obj, $value){
	
	$type = $symbolConfig['system_type'];
	$real_type = $symbolConfig['predefined'] ==  1 ? 'multi': $symbolConfig['system_type'];
	$size = $symbolConfig['size'];
	$id_meta = $symbolConfig['name'];
	$table = $symbolConfig['table'];
	$cleanup = $symbolConfig['cleanup'];

	$valueShow = DataValidator::displayData($obj->input['sourceData']['id_link'], $type);
	$meta_name = 'meta_value_'.$id_meta;
	$field_id = 'edit_field_'.$obj->HtmlIdSuffix.'_'.$id_meta;

	$html = '<select name="'.$meta_name.'" class="select meta_live_edit" id="'.$field_id.'">';
	$html .= $this->contentLinksToSelect(0, $valueShow);
	$html .= '</select>';
	
	return $html;
}

public function contentLinksToSelect($id_sub, $active, $padding = 0){
	
	$activeDomainKey = Registry::get('active_domain');
	$activeDomain = $this->lng['cms_public_domains'][$activeDomainKey]['name'];
	
	$activeLng = Registry::get('active_domain_lng');
	
	$result = $this->getAllLinks($id_sub, $activeDomain, $activeLng );
	$data = null;
	if(count($result)>0){
		foreach($result as $d){
			$data .= '<option value="'.$d['id_link'].'" '.Validator::selected($active, $d['id_link']).'>'.$this->setSpacing($padding).''.$d['title'].'</option>';
			$data .= $this->contentLinksToSelect($d['id_link'], $active, $padding + 3);
		}
	}
	return $data;
}

public function setSpacing($spaces){
	
	$str = null;
	
	for($i = 0; $i < $spaces; $i++){
		$str .= '&nbsp;';
	}
	$str .= $spaces > 0 ? '&raquo;&nbsp;': null;
	return $str;
}

public function annotationThumb($path){
	
	$dir = dirname($path);
	$file = basename($path);
	
	$ad = explode('/', $dir);
	
	$isAnnotationDir = $ad[2] == $this->config['anote_image_folder'] ? true: false;
	
	$originPath = $ad[0].'/'.$ad[1].'/'.$this->config['anote_image_folder'].'/'.$file;
	$originThumbPath = $ad[0].'/'.$ad[1].'/'.$this->config['anote_image_folder'].'/th_'.$file;
	
	if($isAnnotationDir === false){
		Storage::copyFile($path, $originPath);
	}
	
	$size = getimagesize($originPath);
	
	if($this->config['image_thumb_preffer_axxis'] === true){
		$passAxxis = $size[0];
		$pass = $this->config['anote_image_size']['origWidth'];
	}else{
		$passAxxis = $size[1];
		$pass = $this->config['anote_image_size']['origHeight'];
	}
	
	if( $passAxxis > $pass ){
		$image = new SimpleImage();
		$image->load($originPath);
		
		if($this->config['image_thumb_preffer_axxis'] === true)
		{
			$image->resizeToWidth($pass);
		}else{
			$image->resizeToHeight($pass);
		}
		
		$image->save($originPath);
		umask(0000);
		chmod($originPath, 0777);
	}
	
	if(!file_exists($originThumbPath)){
		$image = new SimpleImage();
		$image->load($originPath);
		
		if($this->config['image_thumb_preffer_axxis'] === true && $passAxxis > $this->config['anote_image_size']['thWidth'])
		{
			$image->resizeToWidth($this->config['anote_image_size']['thWidth']);
		}elseif($this->config['image_thumb_preffer_axxis'] === false && $passAxxis > $this->config['anote_image_size']['thHeight']){
			$image->resizeToHeight($this->config['anote_image_size']['thHeight']);
		}
		
		$image->save($originThumbPath);
		umask(0000);
		chmod($originThumbPath, 0777);
	}
	
	return $originPath;
}

public function uniqueLink($id_link, $domain, $textmap){
	
	$q = "SELECT * FROM "._SQLPREFIX_."_cms_links WHERE id_link != '".(int)$id_link."' AND domain LIKE '".$domain."' AND textmap LIKE '".trim($textmap)."' ";
	$qq = Db::result($q);	
	return count($qq);
}

protected function getLinkData($id_link){
	$q = "SELECT * FROM "._SQLPREFIX_."_cms_links WHERE id_link = '".(int)$id_link."' ";
	$qq = Db::result($q);	
	return $qq[0];
}

public function getDirData($id_dir){
	
	$q = "SELECT * FROM "._SQLPREFIX_."_cms_media_dir WHERE id_dir = '".(int)$id_dir."' ";
	$qq = Db::result($q);	
	return count($qq)>0 ? $qq[0]: array();
}

public function getDirMedia($id_dir){
	
	$q = "SELECT * FROM "._SQLPREFIX_."_cms_media_list WHERE id_dir = '".(int)$id_dir."' ORDER BY public_ord, id_media DESC ";
	$qq = Db::result($q);
	return count($qq)>0 ? $qq: array();
}

public function getMediaData($path){
	
	$q = "SELECT * FROM "._SQLPREFIX_."_cms_media_list WHERE path LIKE '".$path."' LIMIT 1 ";
	$qq = Db::result($q);	
	return count($qq) == 1 ? $qq: array();
}


protected function getAllLinks($id_sub, $domain, $lng = null){
	
	$sqlif = !is_null($lng) ? " AND lng LIKE '".$lng."' ": null;
	
	$q = "SELECT * FROM "._SQLPREFIX_."_cms_links WHERE domain LIKE '".$domain."' AND id_sub = '".(int)$id_sub."' ".$sqlif." ORDER BY id_position, public_order, id_link ";
	$qq = Db::result($q);	
	return $qq;
}

public function getDirList($type="images"){
	
	$activeDomainKey = Registry::get('active_domain');
	$activeDomain = $this->lng['cms_public_domains'][$activeDomainKey]['name'];
	
	$activeDomainLng = Registry::get('active_domain_lng');
	
	$q = "SELECT * FROM "._SQLPREFIX_."_cms_media_dir WHERE type LIKE '".$type."' AND domain LIKE '".$activeDomain."' ORDER BY id_dir";
	$__PRECACHE = Db::result($q);
	
	return $__PRECACHE;
}

public function getContentData($id_content){
	$q = "SELECT * FROM "._SQLPREFIX_."_cms_content WHERE id_content = '".$id_content."' ";
	$qq = Db::result($q);	
	return $qq[0];
}

public function getForms()
{
	$activeDomainKey = Registry::get('active_domain');
	$activeDomain = $this->lng['cms_public_domains'][$activeDomainKey]['name'];
	
	$q = "SELECT * FROM "._SQLPREFIX_."_cms_forms WHERE domain LIKE '".$activeDomain."' ORDER BY title";
	$qq = Db::result($q);
	return $qq;
}

public function getFormData($id_form)
{
	$activeDomainKey = Registry::get('active_domain');
	$activeDomain = $this->lng['cms_public_domains'][$activeDomainKey]['name'];
	
	$q = "SELECT * FROM "._SQLPREFIX_."_cms_forms WHERE domain LIKE '".$activeDomain."' AND id_form = '".$id_form."' LIMIT 1";
	$qq = Db::result($q);
	return count($qq) == 1 ? $qq[0]: false;
}


}
?>
