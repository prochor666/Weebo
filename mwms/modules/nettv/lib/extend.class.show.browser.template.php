<?php
class ShowBrowserTemplate extends ShowBrowser{

protected $default_browser_mode;

public function __construct(){
	parent::__construct();
}

public function showContent(){
	$mode = is_null($this->filterReg['show_search_term']) || mb_strlen($this->search_term)<$this->search_term_length_min ? 'base': 'search';
	$this->ajax_view_url_suffix = '&amp;sysadmin='.Registry::get('userdata/id_user'); //.'&amp;show_search_term='.$this->filterReg['show_search_term'].''
	$result = $this->getContentResult($mode);
	$this->result_count = count($result); 
	$this->query_limit = $this->page_default * ( $this->filterReg['show_page'] - 1 );
	$list = Db::final_items($result, $this->query_limit, $this->page_default);
	$pager = Navigator::pager_ajax($this->result_count, $this->page_default, $custom_uri = $this->ajax_view_url.'show.browser.inner.php'.$this->ajax_view_url_suffix.'&amp;show_order='.$this->filterReg['show_order'].'&amp;show_order_direction='.$this->filterReg['show_order_direction'], $force = $this->filterReg['show_page'], 'show_page' );
	
	return $pager.$this->tableShow($list).$pager;
}

/*
 * Table list basic
 * */
private function tableShow($list){

$html = '<table class="mwms_data_list contentmod_data_list">
	<caption id="show_browser"><div id="browser_filter">'.$this->setFilterForm().'</div></caption>
	<thead>
		<tr>
			<th style="width:80px;">'.$this->lng['tv_show_id'].' ('.$this->result_count.') '.$this->setOrder('show_order','id_show', 'show_order_direction', $this->lng['tv_show_id'],'show.browser.inner.php', true).'</th>
			<th>'.$this->lng['tv_show_title'].' '.$this->setOrder('show_order','title', 'show_order_direction', $this->lng['tv_show_title'],'show.browser.inner.php', true).'</th>
			<th style="width:140px;">'.$this->lng['tv_show_active'].' '.$this->setOrder('show_order','id_active', 'show_order_direction', $this->lng['tv_show_active'],'show.browser.inner.php', true).'</th>
			<th style="width:140px;">'.$this->lng['tv_show_archive'].' '.$this->setOrder('show_order','id_archive', 'show_order_direction', $this->lng['tv_show_archive'],'show.browser.inner.php', true).'</th>
			<th style="width:140px;">'.$this->lng['tv_show_date_ins'].' '.$this->setOrder('show_order','date_ins', 'show_order_direction', $this->lng['tv_show_date_ins'],'show.browser.inner.php', true).'</th>
			<th style="width:140px;">'.$this->lng['tv_show_date_upd'].' '.$this->setOrder('show_order','date_upd', 'show_order_direction', $this->lng['tv_show_date_upd'],'show.browser.inner.php', true).'</th>
			<th class="toolbar">
				<button class="tv_show_new button" title="'.$this->lng['tv_show_new'].'">'.$this->lng['tv_show_new'].'</button>
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
		
		$html .= '<tr id="content_cast_'.$d['id_show'].'" class="content_cast" title="'.$d['title'].'">
			<td>'.$d['id_show'].' <input type="hidden" name="id_show" value="'.$d['id_show'].'" /></td>
			<td>'.$d['title'].'</td>
			<td><span class="toggle-icon '.$this->toggleOnOff($d['id_active']).'">&nbsp;</span></td>
			<td><span class="toggle-icon '.$this->toggleOnOff($d['id_archive']).'">&nbsp;</span></td>
			<td>'.$dateI.$strI.'</td>
			<td>'.$dateU.$strU.'</td>
			<td class="toolbar"></td>
		</tr>';
	
	}
	return $html.'</tbody></table>';
}


}
?>
