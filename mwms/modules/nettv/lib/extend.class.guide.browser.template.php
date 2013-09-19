<?php
class GuideBrowserTemplate extends GuideBrowser{

protected $default_browser_mode;

public function __construct(){
	parent::__construct();
}

public function showContent(){
	$mode = is_null($this->filterReg['guide_search_term']) || mb_strlen($this->search_term)<$this->search_term_length_min ? 'base': 'search';
	$this->ajax_view_url_suffix = '&amp;sysadmin='.Registry::get('userdata/id_user'); //.'&amp;guide_search_term='.$this->filterReg['guide_search_term'].''
	$result = $this->getContentResult($mode);
	$this->result_count = count($result); 
	$this->query_limit = $this->page_default * ( $this->filterReg['guide_page'] - 1 );
	$list = Db::final_items($result, $this->query_limit, $this->page_default);
	$pager = Navigator::pager_ajax($this->result_count, $this->page_default, $custom_uri = $this->ajax_view_url.'guide.browser.inner.php'.$this->ajax_view_url_suffix.'&amp;guide_order='.$this->filterReg['guide_order'].'&amp;guide_order_direction='.$this->filterReg['guide_order_direction'], $force = $this->filterReg['guide_page'], 'guide_page' );
	
	return $pager.$this->tableShow($list).$pager;
}

/*
 * Table list basic
 * */
private function tableShow($list){

$html = '<table class="mwms_data_list contentmod_data_list">
	<caption id="guide_browser"><div id="browser_filter">'.$this->setFilterForm().'</div></caption>
	<thead>
		<tr>
			<th style="width:80px;">'.$this->lng['tv_guide_id'].' ('.$this->result_count.') </th>
			<th>'.$this->lng['tv_guide_title'].'</th>
			<th>'.$this->lng['tv_guide_from'].'</th>
			<th>'.$this->lng['tv_show_load'].'</th>
			<th style="width:140px;">'.$this->lng['tv_guide_date_ins'].'</th>
			<th style="width:140px;">'.$this->lng['tv_guide_date_upd'].'</th>
			<th class="toolbar">
				<button class="tv_guide_new button" title="'.$this->lng['tv_guide_new'].'">'.$this->lng['tv_guide_new'].'</button>
			</th>
		</tr>
	</thead><tbody>
	';

	foreach($list as $d){
		
		$strI = null;
		$strU = null;
		
		if($d['id_ins'] > 0 || $d['id_upd'] > 0){
			$uD = new UserBrowser;
			$useriData = $uD->getUserData($d['id_ins']);
			$useruData = $uD->getUserData($d['id_upd']);
			$iusername = count($useriData)>0 ? '<br />'.$useriData['username']: null;
			$uusername = count($useruData)>0 ? '<br />'.$useruData['username']: null;
			$strI = $d['id_ins'] > 0 ? $iusername: $strI;
			$strU = $d['id_upd'] > 0 ? $uusername: $strU;
		}
		
		$dateI = null;
		$dateU = null;
		
		if($d['date_ins'] > 0 || $d['id_upd'] > 0){
			$dateI = $d['date_ins'] > 0 ? date(Lng::get('system/date_time_format_precise'), $d['date_ins']): $dateI;
			$dateU = $d['date_upd'] > 0 ? date(Lng::get('system/date_time_format_precise'), $d['date_upd']): $dateU;
		}
		
		$sd = $this->getShowData($d['id_show']);
		
		$showTitle = count($sd)>0 ? $sd['title']: '-';
		
		$html .= '<tr id="content_cast_'.$d['id_guide'].'" class="content_cast" title="'.$d['title'].'">
			<td>'.$d['id_guide'].' <input type="hidden" name="id_guide" value="'.$d['id_guide'].'" /></td>
			<td>'.$d['title'].'</td>
			<td>'.$this->lng['tv_days_locale'][date('l', $d['date_from'])].' '.date(Lng::get('system/date_time_format_precise'), $d['date_from']).'</td>
			<td>'.$showTitle.'</td>
			<td>'.$dateI.$strI.'</td>
			<td>'.$dateU.$strU.'</td>
			<td class="toolbar"></td>
		</tr>';
	
	}

	return $html.'</tbody></table>';
}



}
?>
