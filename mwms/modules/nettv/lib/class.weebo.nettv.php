<?php
class WeeboNettv{

public 	$page_default, $query_limit, $result_count, 
		$order_direction, $search_term, $view_url, $view_url_suffix, 
		$thumb_url_prefix, $thumb_url_suffix, $filterReg, $order_case, 
		$filter_indexes, $order_default_direction,$lng,$config, $site;

protected $default_aspect_ratio, $default_thumb_x, $default_thumb_y, $browser_modes;

public function __construct(){
	$this->site = Registry::get('serverdata/site');
	$this->lng = Lng::get('nettv');
	$this->config = Registry::get('moduledata/nettv');
	$this->page_default = 40;
	$this->query_limit = 0;
	$this->result_count = 0;
	$this->view_url = Registry::get('serverdata/path').'?module='.Registry::get('active_admin_module').'&amp;sub=';
	$this->view_url_suffix = null;
	$this->initial_sub = 'nettv.monitor';
	$this->ajax_view_url = Ajax::path().'require&amp;file=/mwms/modules/nettv/view/';
	$this->ajax_view_url_suffix = '&amp;sysadmin='.Registry::get('userdata/id_user').'';
	$this->rootDir = _GLOBALDATADIR_;
	
	if(!isset($_SESSION['cms_filter_registry'])){
		$_SESSION['cms_filter_registry'] = array();
	}
	
	$this->filterReg = $_SESSION['cms_filter_registry']; 
	
	$this->order_case = array(
		"show" => "title",
		"show_items" => "id_public,date_public",
		"team" => "id_team",
		"guide" => "date_from",
	);
	
	$this->order_default_direction = array(
		"show" => "ASC",
		"show_items" => "DESC",
		"team" => "DESC",
		"guide" => "DESC",
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
		<div class="nettv_menu">
			<a href="?module=nettv&amp;sub=nettv.monitor" id="nettv.monitor" title="'.$this->lng['mwms_tv_monitor'].'">'.$this->lng['mwms_tv_monitor'].'</a>
			<a href="?module=nettv&amp;sub=show.item.browser" id="show.item.browser" title="'.$this->lng['mwms_tv_show_items'].'">'.$this->lng['mwms_tv_show_items'].'</a>
			<a href="?module=nettv&amp;sub=show.browser" id="show.browser" title="'.$this->lng['mwms_tv_shows'].'">'.$this->lng['mwms_tv_shows'].'</a>
			<a href="?module=nettv&amp;sub=guide.browser" id="guide.browser" title="'.$this->lng['mwms_tv_guide'].'">'.$this->lng['mwms_tv_guide'].'</a>
			<a href="?module=nettv&amp;sub=team.browser" id="team.browser" title="'.$this->lng['mwms_tv_team'].'">'.$this->lng['mwms_tv_team'].'</a>
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

public function chooseGallery($symbolConfig, $obj, $value){
	
	$type = $symbolConfig['system_type'];
	$real_type = $symbolConfig['predefined'] ==  1 ? 'multi': $symbolConfig['system_type'];
	$size = $symbolConfig['size'];
	$id_meta = $symbolConfig['name'];
	$table = $symbolConfig['table'];
	$cleanup = $symbolConfig['cleanup'];

	$valueShow = mb_strlen(DataValidator::displayData($value, $type))<1 && array_key_exists('default_value', $symbolConfig) ? DataValidator::displayData($symbolConfig['default_value'], $type): DataValidator::displayData($value, $type);

	$meta_name = 'meta_value_'.$id_meta;
	$field_id = 'edit_field_'.$obj->HtmlIdSuffix.'_'.$id_meta;

	$html = '<select name="'.$meta_name.'" class="select meta_live_edit" id="'.$field_id.'">
			<option value="0" '.Validator::selected($valueShow, 0).'>'.$this->lng['tv_no_value'].'</option>
	';
	
	$bData = $this->getGaleries($valueShow);
	
	foreach($bData as $d){
		$html .= '<option value="'.$d['id_dir'].'" '.Validator::selected($valueShow, $d['id_dir']).'>'.$d['title'].'</option>';
	}
	$html .= '</select>';
	
	return $html;
}

public static function getGaleries($id_dir)
{
	$cms = new Cms;

	$paramList = $cms->getDirList($type="images"); 
	
	return $paramList;
}


public function getShowList($symbolConfig, $obj, $value){
	
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
	$bData = $this->getShows();
	
	$html .= '<option value="0" '.Validator::selected($valueShow, 0).'>'.$this->lng['tv_show_load_default'].'</option>';
	
	foreach($bData as $d){
		$html .= '<option value="'.$d['id_show'].'" '.Validator::selected($valueShow, $d['id_show']).'>'.$d['title'].'</option>';
	}
	$html .= '</select>';
	
	return $html;
}

public function getTeamList($symbolConfig, $obj, $value){
	
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
	$bData = $this->getTeam();
	
	$html .= '<option value="0" '.Validator::selected($valueShow, 0).'>'.$this->lng['tv_show_load_default'].'</option>';
	
	foreach($bData as $d){
		$html .= '<option value="'.$d['id_team'].'" '.Validator::selected($valueShow, $d['id_team']).'>'.$d['title'].'</option>';
	}
	$html .= '</select>';
	
	return $html;
}


public function getVideoFormat($symbolConfig, $obj, $value){
	
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
	$bData = $this->lng['tv_archive_format_data'];
	
	foreach($bData as $f => $r){
		$html .= '<option value="'.$r.'" '.Validator::selected($valueShow, $r).'>'.$f.' ('.$r.')</option>';
	}
	$html .= '</select>';
	
	return $html;
}

public function getImageFile($symbolConfig, $obj, $value){
	
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
		
		$proxy = Registry::get('serverdata/site').'/'.$dirName.'/'.$fileName;
	}
	
	$html = '
				<input type="text" name="'.$meta_name.'" class="text meta_live_edit content_editor_thumb" id="'.$field_id.'" value="'.$valueShow.'" />
				<img src="'.$proxy.'" alt="thumb" id="'.$field_id.'_thumb" class="content_editor_image '.$cssinit.'" />
	';
	return $html;
}

public function getImageFileActive($symbolConfig, $obj, $value){
	
	$type = $symbolConfig['system_type'];
	$real_type = $symbolConfig['predefined'] ==  1 ? 'multi': $symbolConfig['system_type'];
	$size = $symbolConfig['size'];
	$id_meta = $symbolConfig['name'];
	$table = $symbolConfig['table'];
	$cleanup = $symbolConfig['cleanup'];

	$valueShow = mb_strlen(DataValidator::displayData($value, $type))<1 ? null: DataValidator::displayData($value, $type);

	$meta_name = 'meta_value_'.$id_meta;
	$field_id = 'edit_field_'.$obj->HtmlIdSuffix.'_'.$id_meta;
	
	$id_item = (int)$obj->input['id'];
	
	$proxy = '<label class="imprev" for="'.$field_id.'_0"><input type="radio" name="'.$meta_name.'" class="meta_live_radio" id="'.$field_id.'_0" value="0" '.Validator::checked($valueShow, 0).' /><br />'.$this->lng['tv_image_none'].'</label> ';
	
	if($id_item>0)
	{
		$d = $this->getImages($obj->input['id']);
		$proxy .= mb_strlen($d['image_small'])>0 && file_exists($d['image_small']) && $this->isImage($d['image_small']) ? '<label class="imprev" for="'.$field_id.'_1"><input type="radio" name="'.$meta_name.'" class="meta_live_radio" id="'.$field_id.'_1" value="1" '.Validator::checked($valueShow, 1).' /><br /><img alt="prev" src="'.Registry::get('serverdata/site').'/'.$d['image_small'].'" /></label> ': null;
		$proxy .= mb_strlen($d['image_small_2'])>0 && file_exists($d['image_small_2']) && $this->isImage($d['image_small_2']) ? '<label class="imprev" for="'.$field_id.'_2"><input type="radio" name="'.$meta_name.'" class="meta_live_radio" id="'.$field_id.'_2" value="2" '.Validator::checked($valueShow, 2).' /><br /><img alt="prev" src="'.Registry::get('serverdata/site').'/'.$d['image_small_2'].'" /></label> ': null;
		$proxy .= mb_strlen($d['image_small_3'])>0 && file_exists($d['image_small_3']) && $this->isImage($d['image_small_3']) ? '<label class="imprev" for="'.$field_id.'_3"><input type="radio" name="'.$meta_name.'" class="meta_live_radio" id="'.$field_id.'_3" value="3" '.Validator::checked($valueShow, 3).' /><br /><img alt="prev" src="'.Registry::get('serverdata/site').'/'.$d['image_small_3'].'" /></label> ': null;
	}
	
	$html = $proxy.'
				
	';
	return $html;
}

public function getImages($id_item){
	$d = $this->getShowItemData($id_item);
	return $d;
}

public function getVideoFile($symbolConfig, $obj, $value){
	
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
		
		$proxy = Registry::get('serverdata/site').'/'.$dirName.'/'.$fileName;
	}
	
	$html = '
				<input type="text" name="'.$meta_name.'" class="text meta_live_edit" id="'.$field_id.'" value="'.$valueShow.'" />
				
	';
	return $html;
}


public function createImageSet($path){
	
	$originPath = Registry::get('serverdata/root').'/'.$path; 
	
	$dir = dirname($path);
	$file = basename($path);
	$size = getimagesize($originPath);
	
	$fname = System::fileNameOnly($file);
	$ext = System::extension($file);
	
	foreach($this->config['imageSizes'] as $sizeConf)
	{
		
		if($sizeConf['method'] == 'toWidth')
		{
			$originThumbPath = Registry::get('serverdata/root').'/'.$dir.'/thumbs/'.$fname.'_'.$sizeConf['name'].'_w'.$sizeConf['width'].'.'.$ext;
			
			$passAxxis = $size[0];
			$pass = $sizeConf['width'];
			
			$image = new SimpleImage();
			$image->load($originPath);
			
			$image->resizeToWidth($pass);
			
			$image->save($originThumbPath);
			umask(0000);
			chmod($originThumbPath, 0777);

		}else{
			
			$originThumbPath = Registry::get('serverdata/root').'/'.$dir.'/thumbs/'.$fname.'_'.$sizeConf['name'].'_h'.$sizeConf['height'].'.'.$ext;
			
			$passAxxis = $size[1];
			$pass = $sizeConf['height'];
			
			$image = new SimpleImage();
			$image->load($originPath);
			
			$image->resizeToHeight($pass);
			
			$image->save($originThumbPath);
			umask(0000);
			chmod($originThumbPath, 0777);
		}
	
	}
	
	return $originPath;
}


public function isImage($file){
	$p = @getimagesize($file);
	return $p;
}

public function connectNebula(){
	$table = $this->config['nebulaConf']['dbPrefix'].'_users';
	Db::get_last_id($table, $conf = null, $driver = 'Postgresqldb');
}

protected function getImportData($id_import){
	$q = "SELECT * FROM "._SQLPREFIX_."_nettv_import WHERE id_import = '".(int)$id_import."' ";
	$qq = Db::result($q);
	return count($qq) == 1 ? $qq[0]: array();
}
protected function getShowData($id_show){
	$q = "SELECT * FROM "._SQLPREFIX_."_nettv_shows WHERE id_show = '".(int)$id_show."' ";
	$qq = Db::result($q);	
	return count($qq) == 1 ? $qq[0]: array();
}
public function getShowItemData($id_item){
	$q = "SELECT * FROM "._SQLPREFIX_."_nettv_show_items WHERE id_item = '".(int)$id_item."' LIMIT 1";
	$qq = Db::result($q);	
	return count($qq) == 1 ? $qq[0]: array();
}
public function getLastShowItemData($limit = 1, $idShow=0){
	
	$q = (int)$idShow>0 ? "SELECT * FROM "._SQLPREFIX_."_nettv_show_items WHERE id_public = 1 AND id_show = ".(int)$idShow." ORDER BY id_item DESC LIMIT ".(int)$limit: "SELECT * FROM "._SQLPREFIX_."_nettv_show_items WHERE id_public = 1 ORDER BY id_item DESC LIMIT ".(int)$limit;
	$qq = Db::result($q);	
	return count($qq) > 0 ? $qq: array();
}
public function getShowItems($id_show, $order = null){
	$orderIndex = is_null($order) || (int)$order==0 || !array_key_exists(abs((int)$order), $this->lng["web_order"]) ? -1: (int)$order;
	$orderNow = $orderIndex<0 ? $this->lng["web_order"][abs((int)$order)]["orderDESC"]: $this->lng["web_order"][abs((int)$order)]["orderASC"];
	$q = "SELECT * FROM "._SQLPREFIX_."_nettv_show_items WHERE id_show = '".(int)$id_show."' AND id_public = 1 ORDER BY ".$orderNow;
	$qq = Db::result($q);	
	return count($qq) > 0 ? $qq: array();
}
public function getShowItemsByTeam($id_team){
	$q = "SELECT * FROM "._SQLPREFIX_."_nettv_show_items WHERE id_team = '".(int)$id_team."' AND id_public = 1 ORDER BY date_public DESC, title";
	$qq = Db::result($q);	
	return count($qq) > 0 ? $qq: array();
}

protected function getTeamData($id_team){
	$q = "SELECT * FROM "._SQLPREFIX_."_nettv_team WHERE id_team = '".(int)$id_team."' ";
	$qq = Db::result($q);
	return count($qq) == 1 ? $qq[0]: array();
}

public function getPubItems($type = 'all'){
	$sqlif = $type == 'all' ? null: " WHERE type LIKE '".(string)trim($type)."' ";
	$q = "SELECT * FROM "._SQLPREFIX_."_nettv_pub_zone ".$sqlif." ORDER BY id_item DESC";
	$qq = Db::result($q);
	return count($qq)>0  ? $qq: array();
}

public function getPubItem($id_item){
	$q = "SELECT * FROM "._SQLPREFIX_."_nettv_pub_zone WHERE id_item = ".(int)$id_item;
	$qq = Db::result($q);
	return count($qq)>0  ? $qq: array();
}

public function getShows(){
	$q = "SELECT * FROM "._SQLPREFIX_."_nettv_shows ORDER BY title, id_show";
	$qq = Db::result($q);
	return count($qq)>0  ? $qq: array();
}

public function getShowsArchive(){
	$q = "SELECT * FROM "._SQLPREFIX_."_nettv_shows WHERE id_archive = 1 ORDER BY title";
	$qq = Db::result($q);
	return count($qq)>0  ? $qq: array();
}

public function getShowsActive($limit = 0){
	$q = $limit>0 ? "SELECT * FROM "._SQLPREFIX_."_nettv_shows WHERE id_active = 1 ORDER BY title LIMIT ".(int)$limit."": "SELECT * FROM "._SQLPREFIX_."_nettv_shows WHERE id_active = 1 ORDER BY title";
	$qq = Db::result($q);
	return count($qq)>0  ? $qq: array();
}

public function getTeam(){
	$q = "SELECT * FROM "._SQLPREFIX_."_nettv_team ORDER BY title";
	$qq = Db::result($q);
	return count($qq)>0  ? $qq: array();
}
public function getGuide($time){
	$l = (int)$time + 86400;
	$q = "SELECT * FROM "._SQLPREFIX_."_nettv_guide WHERE date_from > ".(int)$time." AND date_from < ".(int)$l." ORDER BY date_from";
	$qq = Db::result($q);
	return count($qq)>0  ? $qq: array();
}

public function getGuideNow($limit = 1){
	$q = "SELECT * FROM "._SQLPREFIX_."_nettv_guide WHERE date_from < ".time()." ORDER BY date_from DESC LIMIT ".(int)$limit;
	$qq = Db::result($q);
	return count($qq)>0 ? $qq: array();
}

public function getGuideNowAfter($limit = 1){
	$q = "SELECT * FROM "._SQLPREFIX_."_nettv_guide WHERE date_from > ".time()." ORDER BY date_from LIMIT ".(int)$limit;
	$qq = Db::result($q);
	return count($qq)>0 ? $qq: array();
}

public function getContent($display_script, $data = null){
	
	$dsp = explode(';', $display_script);
	
	if(count($dsp)<2){
		$w = " "._SQLPREFIX_."_cms_content.display_script LIKE '".trim($display_script)."' ";
	}else{
		
		$w = " ( ";
		
		foreach($dsp as $k => $s){
			$w .=  $k > 0 ? " OR "._SQLPREFIX_."_cms_content.display_script LIKE '".trim($s)."' " : " "._SQLPREFIX_."_cms_content.display_script LIKE '".trim($s)."' "  ;
		}
		
		$w .= " ) ";
	}
	
	$and = !is_null($data) ? " AND id_content = '".(int)$data."' ": null;
	
	$q = "SELECT *, "._SQLPREFIX_."_cms_links.textmap AS linkmap , "._SQLPREFIX_."_cms_content.textmap AS cmap FROM "._SQLPREFIX_."_cms_content 
		LEFT JOIN "._SQLPREFIX_."_cms_links 
		ON "._SQLPREFIX_."_cms_content.id_link = "._SQLPREFIX_."_cms_links.id_link 
		WHERE ".$w." 
		AND domain LIKE '"._CMS_DOMAIN_."' 
		AND lng LIKE '".__CMS_PAGE_LNG__."' 
		".$and." 
		LIMIT 1 
		";
		
	$qq = Db::result($q);
	return count($qq)>0 ? $qq[0]: array();
}


public function getContentByView($display_script, $data = null){
	
	$dsp = explode(';', $display_script);
	
	if(count($dsp)<2){
		$w = " "._SQLPREFIX_."_cms_content.display_script LIKE '".trim($display_script)."' ";
	}else{
		
		$w = " ( ";
		
		foreach($dsp as $k => $s){
			$w .=  $k > 0 ? " OR "._SQLPREFIX_."_cms_content.display_script LIKE '".trim($s)."' " : " "._SQLPREFIX_."_cms_content.display_script LIKE '".trim($s)."' "  ;
		}
		
		$w .= " ) ";
	}
	
	$and = !is_null($data) ? " AND display_script_param LIKE '".$data."' ": null;
	
	$q = "SELECT *, "._SQLPREFIX_."_cms_links.textmap AS linkmap , "._SQLPREFIX_."_cms_content.textmap AS cmap FROM "._SQLPREFIX_."_cms_content 
		LEFT JOIN "._SQLPREFIX_."_cms_links 
		ON "._SQLPREFIX_."_cms_content.id_link = "._SQLPREFIX_."_cms_links.id_link 
		WHERE ".$w." 
		AND domain LIKE '"._CMS_DOMAIN_."' 
		AND lng LIKE '".__CMS_PAGE_LNG__."' 
		".$and." 
		LIMIT 1 
		";
		
	$qq = Db::result($q);
	return count($qq)>0 ? $qq[0]: array();
}

}
?>
