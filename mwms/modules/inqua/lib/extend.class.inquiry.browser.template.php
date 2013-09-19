<?php
class InquiryBrowserTemplate extends InquiryBrowser{

protected $default_browser_mode;

public function __construct(){
	parent::__construct();
}

public function showContent(){
	$mode = is_null($this->filterReg['inquiry_search_term']) || mb_strlen($this->search_term)<$this->search_term_length_min ? 'base': 'search';
	$this->ajax_view_url_suffix = '&amp;sysadmin='.Registry::get('userdata/id_user'); //.'&amp;inquiry_search_term='.$this->filterReg['inquiry_search_term'].''
	$result = $this->getContentResult($mode);
	$this->result_count = count($result); 
	$this->query_limit = $this->page_default * ( $this->filterReg['inquiry_page'] - 1 );
	$list = Db::final_items($result, $this->query_limit, $this->page_default);
	$pager = Navigator::pager_ajax($this->result_count, $this->page_default, $custom_uri = $this->ajax_view_url.'inquiry.browser.inner.php'.$this->ajax_view_url_suffix.'&amp;inquiry_order='.$this->filterReg['inquiry_order'].'&amp;inquiry_order_direction='.$this->filterReg['inquiry_order_direction'], $force = $this->filterReg['inquiry_page'], 'inquiry_page' );
	
	return $pager.$this->tableShow($list).$pager;
}

/*
 * Table list basic
 * */
private function tableShow($list){

$html = '<table class="mwms_data_list contentmod_data_list">
	<caption id="inquiry_browser"><div id="browser_filter">'.$this->setFilterForm().'</div></caption>
	<thead>
		<tr>
			<th style="width:80px;">'.$this->lng['inqua_inquiry_id'].' ('.$this->result_count.') '.$this->setOrder('inquiry_order','id_inquiry', 'inquiry_order_direction', $this->lng['inqua_inquiry_id'],'inquiry.browser.inner.php', true).'</th>
			<th>'.$this->lng['inqua_inquiry_title'].' '.$this->setOrder('inquiry_order','title', 'inquiry_order_direction', $this->lng['inqua_inquiry_title'],'inquiry.browser.inner.php', true).'</th>
			<th>'.$this->lng['inqua_inquiry_answer_count'].'</th>
			<th style="width:140px;">'.$this->lng['inqua_inquiry_date_ins'].' '.$this->setOrder('inquiry_order','date_ins', 'inquiry_order_direction', $this->lng['inqua_inquiry_date_ins'],'inquiry.browser.inner.php', true).'</th>
			<th style="width:140px;">'.$this->lng['inqua_inquiry_date_upd'].' '.$this->setOrder('inquiry_order','date_upd', 'inquiry_order_direction', $this->lng['inqua_inquiry_date_upd'],'inquiry.browser.inner.php', true).'</th>
			<th class="toolbar">
				<button class="inqua_inquiry_new button" title="'.$this->lng['inqua_inquiry_new'].'">'.$this->lng['inqua_inquiry_new'].'</button>
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
		
		$html .= '<tr id="content_cast_'.$d['id_inquiry'].'" class="content_cast" title="'.$d['title'].'">
			<td>'.$d['id_inquiry'].' <input type="hidden" name="id_inquiry" value="'.$d['id_inquiry'].'" /></td>
			<td>'.$d['title'].'</td>
			<td>0</td>
			<td>'.$dateI.$strI.'</td>
			<td>'.$dateU.$strU.'</td>
			<td class="toolbar"></td>
		</tr>';
	
	}

	return $html.'</tbody></table>';
}



}
?>
