<?php
class AnswerBrowserTemplate extends AnswerBrowser{

protected $default_browser_mode;

public function __construct(){
	parent::__construct();
}

public function showContent(){
	$mode = is_null($this->filterReg['answer_search_term']) || mb_strlen($this->search_term)<$this->search_term_length_min ? 'base': 'search';
	$this->ajax_view_url_suffix = '&amp;sysadmin='.Registry::get('userdata/id_user'); //.'&amp;answer_search_term='.$this->filterReg['answer_search_term'].''
	$result = $this->getContentResult($mode);
	$this->result_count = count($result); 
	$this->query_limit = $this->page_default * ( $this->filterReg['answer_page'] - 1 );
	$list = Db::final_items($result, $this->query_limit, $this->page_default);
	$pager = Navigator::pager_ajax($this->result_count, $this->page_default, $custom_uri = $this->ajax_view_url.'answer.browser.inner.php'.$this->ajax_view_url_suffix.'&amp;answer_order='.$this->filterReg['answer_order'].'&amp;answer_order_direction='.$this->filterReg['answer_order_direction'], $force = $this->filterReg['answer_page'], 'answer_page' );
	
	return $pager.$this->tableShow($list).$pager;
}

/*
 * Table list basic
 * */
private function tableShow($list){

$html = '<table class="mwms_data_list contentmod_data_list">
	<caption id="answer_browser"><div id="browser_filter">
		'.$this->setFilterForm().'
		<span class="label">'.$this->lng['inqua_answer_inquiry'].'</span> '.$this->getInquiryListFilter((int)Registry::get('inqua_id_inquiry_active')).'
	</div></caption>
	<thead>
		<tr>
			<th style="width:80px;">'.$this->lng['inqua_answer_id'].' ('.$this->result_count.') '.$this->setOrder('answer_order','id_answer', 'answer_order_direction', $this->lng['inqua_answer_id'],'answer.browser.inner.php', true).'</th>
			<th>'.$this->lng['inqua_answer_title'].' '.$this->setOrder('answer_order','title', 'answer_order_direction', $this->lng['inqua_answer_title'],'answer.browser.inner.php', true).'</th>
			<th>'.$this->lng['inqua_answer_order'].' '.$this->setOrder('answer_order','public_order', 'answer_order_direction', $this->lng['inqua_answer_order'],'answer.browser.inner.php', true).'</th>
			<th>'.$this->lng['inqua_answer_votes'].' '.$this->setOrder('answer_order','votes', 'answer_order_direction', $this->lng['inqua_answer_votes'],'answer.browser.inner.php', true).'</th>
			<th style="width:140px;">'.$this->lng['inqua_answer_date_ins'].' '.$this->setOrder('answer_order','date_ins', 'answer_order_direction', $this->lng['inqua_answer_date_ins'],'answer.browser.inner.php', true).'</th>
			<th style="width:140px;">'.$this->lng['inqua_answer_date_upd'].' '.$this->setOrder('answer_order','date_upd', 'answer_order_direction', $this->lng['inqua_answer_date_upd'],'answer.browser.inner.php', true).'</th>
			<th class="toolbar">
				<button class="inqua_answer_new button" title="'.$this->lng['inqua_answer_new'].'">'.$this->lng['inqua_answer_new'].'</button>
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
		
		$html .= '<tr id="content_cast_'.$d['id_answer'].'" class="content_cast" title="'.$d['title'].'">
			<td>'.$d['id_answer'].' <input type="hidden" name="id_answer" value="'.$d['id_answer'].'" /></td>
			<td>'.$d['title'].'</td>
			<td>'.$d['public_order'].'</td>
			<td>'.$d['votes'].'</td>
			<td>'.$dateI.$strI.'</td>
			<td>'.$dateU.$strU.'</td>
			<td class="toolbar"></td>
		</tr>';
	
	}
	return $html.'</tbody></table>';
}


}
?>
