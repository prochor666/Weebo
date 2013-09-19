<?php
class WeeboAdv{

public 	$page_default, $query_limit, $result_count, 
		$order_direction, $search_term, $view_url, $view_url_suffix, 
		$thumb_url_prefix, $thumb_url_suffix, $filterReg, $order_case, 
		$filter_indexes, $order_default_direction,$lng,$config;

protected $default_aspect_ratio, $default_thumb_x, $default_thumb_y, $browser_modes;

public function __construct(){
	$this->lng = Lng::get('adv');
	$this->config = Registry::get('moduledata/adv');
	$this->page_default = 20;
	$this->query_limit = 0;
	$this->result_count = 0;
	$this->view_url = Registry::get('serverdata/path').'?module='.Registry::get('active_admin_module').'&amp;sub=';
	$this->view_url_suffix = null;
	$this->initial_sub = 'asset.browser';
	$this->ajax_view_url = Ajax::path().'require&amp;file=/mwms/modules/adv/view/';
	$this->ajax_view_url_suffix = '&amp;sysadmin='.Registry::get('userdata/id_user').'';
	$this->rootDir = _GLOBALDATADIR_;
	
	if(!isset($_SESSION['cms_filter_registry'])){
		$_SESSION['cms_filter_registry'] = array();
	}
	
	$this->filterReg = $_SESSION['cms_filter_registry']; 
	
	$this->order_case = array(
		"banner" => "id_banner",
		"campaign" => "id_campaign",
		"position" => "id_position",
		"asset" => "id_asset",
		"stat" => "id_asset",
	);
	
	$this->order_default_direction = array(
		"banner" => "DESC",
		"campaign" => "DESC",
		"position" => "DESC",
		"asset" => "DESC",
		"stat" => "DESC",
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
		<div class="adv_menu">
			<a href="?module=adv&amp;sub=asset.browser" id="asset.browser" title="'.$this->lng['mwms_adv_assets'].'">'.$this->lng['mwms_adv_assets'].'</a>
			<a href="?module=adv&amp;sub=banner.browser" id="banner.browser" title="'.$this->lng['mwms_adv_banners'].'">'.$this->lng['mwms_adv_banners'].'</a>
			<a href="?module=adv&amp;sub=campaign.browser" id="campaign.browser" title="'.$this->lng['mwms_adv_campaigns'].'">'.$this->lng['mwms_adv_campaigns'].'</a>
			<a href="?module=adv&amp;sub=position.browser" id="position.browser" title="'.$this->lng['mwms_adv_positions'].'">'.$this->lng['mwms_adv_positions'].'</a>
			<a href="?module=adv&amp;sub=stat.browser" id="stat.browser" title="'.$this->lng['mwms_adv_stats'].'">'.$this->lng['mwms_adv_stats'].'</a>
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

public function toggleOnOff($onoff){
	return $onoff == 1 ? 'on': 'off';
}

public function getBannerList($symbolConfig, $obj, $value){
	
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
	$bData = $this->getBanners();
	
	foreach($bData as $d){
		$html .= '<option value="'.$d['id_banner'].'" '.Validator::selected($valueShow, $d['id_banner']).'>'.$d['title'].' ('.$d['format'].')</option>';
	}
	$html .= '</select><div class="bp_wrap" id="'.$field_id.'_prewiev"></div>';
	
	return $html;
}

public function getPositionList($symbolConfig, $obj, $value){
	
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
	$bData = $this->getPositions();
	
	foreach($bData as $d){
		$html .= '<option value="'.$d['id_position'].'" '.Validator::selected($valueShow, $d['id_position']).'>'.$d['title'].'</option>';
	}
	$html .= '</select>';
	
	return $html;
}

public function getCampaignList($symbolConfig, $obj, $value){
	
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
	$bData = $this->getCampaigns();
	
	foreach($bData as $d){
		$html .= '<option value="'.$d['id_campaign'].'" '.Validator::selected($valueShow, $d['id_campaign']).'>'.$d['title'].'</option>';
	}
	$html .= '</select>';
	
	return $html;
}


/* detail methods */
public function getFormats($symbolConfig, $obj, $value){
	
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
	
	$w = Lng::get('adv/adv_formats');
	
	//$w = $this->lng['cms_public_views'];
	
	foreach($w as $format => $name){
		$html .= '<option value="'.$format.'" '.Validator::selected($valueShow, $format).'>'.$name.' ('.$format.')</option>';
	}
	$html .= '</select>';
	
	return $html;
}

public function getAdvFile($symbolConfig, $obj, $value){
	
	$type = $symbolConfig['system_type'];
	$real_type = $symbolConfig['predefined'] ==  1 ? 'multi': $symbolConfig['system_type'];
	$size = $symbolConfig['size'];
	$id_meta = $symbolConfig['name'];
	$table = $symbolConfig['table'];
	$cleanup = $symbolConfig['cleanup'];

	$valueShow = mb_strlen(DataValidator::displayData($value, $type))<1 ? null: DataValidator::displayData($value, $type);

	$meta_name = 'meta_value_'.$id_meta;
	$field_id = 'edit_field_'.$obj->HtmlIdSuffix.'_'.$id_meta;
	
	$img = null;
	$proxy = null;
	$cssinit = 'hide';
	
	if(mb_strlen($valueShow)>0){
		$img = $valueShow;
	
		$cssinit = '';
		$fileName = basename($img);
		$dirName = dirname($img);
		
		$proxy = Registry::get('serverdata/site').'/'.$dirName.'/th_'.$fileName;
	}
	
	$html = '<input type="text" name="'.$meta_name.'" class="text meta_live_edit content_annotation_thumb" id="'.$field_id.'" value="'.$valueShow.'" />';
	return $html;
}

public function getBannerMediaById($id_banner){
	$d = $this->getBannerData($id_banner);
	if(count($d)>0){
		return $d;
	}else{
		return array('status' => 0);
	}
}

protected function getBannerData($id_banner){
	$q = "SELECT * FROM "._SQLPREFIX_."_adv_banners WHERE id_banner = '".(int)$id_banner."' ";
	$qq = Db::result($q);	
	return count($qq) == 1 ? $qq[0]: array();
}


protected function getCampaignData($id_campaign){
	$q = "SELECT * FROM "._SQLPREFIX_."_adv_campaigns WHERE id_campaign = '".(int)$id_campaign."' ";
	$qq = Db::result($q);	
	return count($qq) == 1 ? $qq[0]: array();
}

protected function getPositionData($id_position){
	$q = "SELECT * FROM "._SQLPREFIX_."_adv_positions WHERE id_position = '".(int)$id_position."' ";
	$qq = Db::result($q);	
	return count($qq) == 1 ? $qq[0]: array();
}

public function getBanners(){
	
	$q = "SELECT * FROM "._SQLPREFIX_."_adv_banners ORDER BY id_banner DESC";
	$qq = Db::result($q);	
	return count($qq) >0  ? $qq: array();
}

public function getCampaigns(){
	
	$q = "SELECT * FROM "._SQLPREFIX_."_adv_campaigns ORDER BY id_campaign";
	$qq = Db::result($q);	
	return count($qq) >0  ? $qq: array();
}

public function getPositions(){
	
	$q = "SELECT * FROM "._SQLPREFIX_."_adv_positions ORDER BY id_position";
	$qq = Db::result($q);	
	return count($qq) >0  ? $qq: array();
}




}
?>
