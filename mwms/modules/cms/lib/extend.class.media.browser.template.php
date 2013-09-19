<?php
class MediaBrowserTemplate extends MediaBrowser{

protected $default_browser_mode;

public $resultList;

public function __construct(){
	parent::__construct();
	
	$this->resultList = 0;
}

public function showContent(){
	$mode = is_null($this->filterReg['media_search_term']) || mb_strlen($this->search_term)<$this->search_term_length_min ? 'base': 'search';
	$this->ajax_view_url_suffix = '&amp;sysadmin='.Registry::get('userdata/id_user'); //.'&amp;content_search_term='.$this->filterReg['content_search_term'].''
	$result = $this->getDirResult($mode);
	$this->result_count = count($result); 
	$this->query_limit = $this->page_default * ( $this->filterReg['media_page'] - 1 );
	$list = Db::final_items($result, $this->query_limit, $this->page_default);
	
	if(count($list)<1){
		$this->filterReg['media_page'] = 1;
		$this->query_limit = $this->page_default * ( $this->filterReg['media_page'] - 1 );
		$list = Db::final_items($result, $this->query_limit, $this->page_default);
	}
	$pager = Navigator::pager_ajax($this->result_count, $this->page_default, $custom_uri = $this->ajax_view_url.'media.browser.inner.php'.$this->ajax_view_url_suffix.'&amp;id_dir='.$this->id_dir.'&amp;media_order='.urlencode($this->filterReg['media_order']).'&amp;media_order_direction='.$this->filterReg['media_order_direction'], $force = $this->filterReg['media_page'], 'media_page' );
	
	$this->resultList = count($list);
	
	return $pager.$this->tableShow($list).$pager;
}

/*
 * Table list basic
 * */
private function tableShow($list){

$html = '<table class="mwms_data_list contentmod_data_list">
	<caption id="content_browser"><div id="browser_filter">'.$this->setFilterForm().'</div></caption>
	<thead>
	<tr>
		<th style="width:80px;">'.$this->lng['mwms_media_id'].' ('.$this->result_count.') '.$this->setOrder('media_order','id_media', 'media_order_direction', $this->lng['mwms_content_id'],'media.browser.inner.php', true).'</th>
		<th>'.$this->lng['mwms_media_file'].' '.$this->setOrder('media_order','path', 'media_order_direction', $this->lng['mwms_content_title'],'media.browser.inner.php', true).'</th>
		<th style="width:200px;">'.$this->lng['mwms_media_title'].' '.$this->setOrder('media_order','title', 'media_order_direction', $this->lng['mwms_content_title'],'media.browser.inner.php', true).'</th>
		<th style="width:140px;">'.$this->lng['mwms_media_date_ins'].' '.$this->setOrder('media_order','date_ins', 'media_order_direction', $this->lng['mwms_content_date_ins'],'media.browser.inner.php', true).'</th>
		<th style="width:140px;">'.$this->lng['mwms_media_date_upd'].' '.$this->setOrder('media_order','date_upd', 'media_order_direction', $this->lng['mwms_content_date_upd'],'media.browser.inner.php', true).'</th>
		<th class="toolbar"> ('.$this->filterReg['media_page'].')
			<button class="mwms_media_new button" title="'.$this->lng['mwms_media_new'].'">'.$this->lng['mwms_media_new'].'</button>
		</th>
	</tr></thead><tbody>';
	
	foreach($list as $d){
		$date_upd = $d['date_ins'] < $d['date_upd'] && $d['date_upd'] > 0 ? date(Lng::get('system/date_time_format_precise'), $d['date_upd']) : null;
		$html .= '<tr id="content_cast_'.$d['id_media'].'" class="content_cast" title="'.$d['title'].'">
			<td>'.$d['id_media'].'</td>
			<td>'.$this->showGalleryThumb($d['path']).'</td>
			<td>'.$d['title'].'</td>
			<td>'.date(Lng::get('system/date_time_format_precise'), $d['date_ins']).'</td>
			<td>'.$date_upd.'</td>
			<td class="toolbar"></td>
		</tr>';
	}

	return $html.'</tbody></table>';
}

protected function showGalleryThumb($path){
	
	$base = basename($path);
	$dir = dirname($path);
	
	$f = System::getFileUrl($dir.'/th/th_'.$base);
	
	return '<img src="'.$f.'" alt="thumb" class="media-thumb" title="'.$dir.'/'.$base.'" />';
}

protected function switchDirType(){
	
	$active = mb_strlen(Registry::get('active_domain_dir_type'))>0 ? Registry::get('active_domain_dir_type'): $this->initialDirType;
	
	$html = '<select name="cms-dir_type" class="cms-dir_type">';
	
	foreach($this->lng['mwms_media_dir_types'] as $type => $name){
		$html .= '<option value="'.$type.'" '.Validator::selected($active, $type).'>'.$name.'</option>';
	}
	
	$html .= '</select>';
	
	return $html;
}

/*
 * Table list basic
 */
public function showDirs(){

	$mode = mb_strlen(Registry::get('active_domain_dir_type'))>0 ? Registry::get('active_domain_dir_type'): $this->initialDirType;

	//$mode = 'images';
	$result = $this->getDirList($mode);

	$html = '
	<div class="dirs-remote-panel">
		<button class="mwms_media_new_dir button" title="'.$this->lng['mwms_media_new_dir'].'">'.$this->lng['mwms_media_new_dir'].'</button>
		'.$this->switchDirType().'
	</div>
	';
	
	if(count($result)>0)
	{
		$html .= '<ul class="dir_list">';
		
		foreach($result as $d){
			
			$html .= '<li class="nav_link" id="dir_link_'.$d['id_dir'].'">
				<a href="'.html_entity_decode($this->ajax_view_url.'media.browser.control.php'.$this->ajax_view_url_suffix).'&amp;id_dir='.$d['id_dir'].'&amp;media_page=1" id="dir_cast_'.$d['id_dir'].'" class="dir_target dir_cast" title="'.$d['title'].'">'.$d['title'].'</a>
				<div class="dir_toolbar"></div>';
			
			$html .= '</li>';
		
		}
		
		$html .= '</ul>';
	}
	
	return $html;
}

}
?>
