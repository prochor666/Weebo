<?php
class MediaMix{

public 	$page_default, $query_limit, $result_count, 
		$order_direction, $search_term, $view_url, $view_url_suffix, 
		$thumb_url_prefix, $thumb_url_suffix, $filterReg, $order_case, 
		$filter_indexes, $order_default_direction,$lng,$config;

protected $default_aspect_ratio, $default_thumb_x, $default_thumb_y, $browser_modes;

public function __construct(){
	$this->lng = Lng::get('mediamix');
	$this->config = Registry::get('moduledata/mediamix');
	$this->page_default = 20;
	$this->query_limit = 0;
	$this->result_count = 0;
	$this->view_url = Registry::get('serverdata/path').'?module='.Registry::get('active_admin_module').'&amp;sub=';
	$this->view_url_suffix = null;
	$this->initial_sub = 'sources.browser';
	$this->ajax_view_url = Ajax::path().'require&amp;file=/mwms/modules/mediamix/view/';
	$this->ajax_view_url_suffix = '&amp;sysadmin='.Registry::get('userdata/id_user').'';
	$this->rootDir = _GLOBALDATADIR_;
	
	if(!isset($_SESSION['cms_filter_registry'])){
		$_SESSION['cms_filter_registry'] = array();
	}
	
	$this->filterReg = $_SESSION['cms_filter_registry']; 
	
	$this->order_case = array(
		"sources" => "public_order, id_source",
		"articles" => "date_public",
	);
	
	$this->order_default_direction = array(
		"sources" => "DESC",
		"articles" => "DESC"
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
		<div class="mm_menu">
			<a href="?module=mediamix&amp;sub=sources.browser" id="sources.browser" title="'.$this->lng['mwms_mediamix_sources'].'">'.$this->lng['mwms_mediamix_sources'].'</a>
			<a href="?module=mediamix&amp;sub=articles.browser" id="articles.browser" title="'.$this->lng['mwms_mediamix_articles'].'">'.$this->lng['mwms_mediamix_articles'].'</a>
			<a href="?module=mediamix&amp;sub=downloader.browser" id="downloader.browser" title="'.$this->lng['mwms_mediamix_downloader'].'">'.$this->lng['mwms_mediamix_downloader'].'</a>
		</div>
	';
  
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

public function getTemplateList($symbolConfig, $obj, $value){
	
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
	
	foreach($this->lng['mwms_source_templates'] as $k => $d){
		$html .= '<option value="'.$k.'" '.Validator::selected($valueShow, $k).'>'.$d['title'].'</option>';
	}
	$html .= '</select>';
	
	return $html;
}

public function setFullPath(&$relPath){
	$relPath = Registry::get('serverdata/site').'/'.$relPath;
	return $relPath;
}

public function toggleOnOff($onoff){
	return $onoff == 1 ? 'on': 'off';
}

public function getSourceData($id_source){
	$q = "SELECT * FROM "._SQLPREFIX_."_mm_sources WHERE id_source = '".(int)$id_source."' ";
	$qq = Db::result($q);	
	return count($qq) == 1 ? $qq[0]: array();
}

public function getSources(){
	
	$q = "SELECT * FROM "._SQLPREFIX_."_mm_sources ORDER BY public_order, id_source DESC";
	$qq = Db::result($q);	
	return count($qq) >0  ? $qq: array();
}

protected function getArticleData($id_article){
	$q = "SELECT * FROM "._SQLPREFIX_."_mm_articles WHERE id_article = '".(int)$id_article."' ";
	$qq = Db::result($q);	
	return count($qq) == 1 ? $qq[0]: array();
}

public function getArticles(){
	
	$q = "SELECT * FROM "._SQLPREFIX_."_mm_articles ORDER BY public_order, id_article DESC";
	$qq = Db::result($q);	
	return count($qq) >0  ? $qq: array();
}

public function getActiveSources(){
	
	$q = "SELECT * FROM "._SQLPREFIX_."_mm_sources WHERE id_public = 1 ORDER BY id_source DESC";
	$qq = Db::result($q);	
	return count($qq) > 0 ? $qq: array();
}




}
?>
