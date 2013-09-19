<?php
class FormBrowserTemplate extends FormBrowser{

protected $default_browser_mode;

public function __construct(){
	parent::__construct();
}

public function showContent(){
	$mode = is_null($this->filterReg['form_search_term']) || mb_strlen($this->search_term)<$this->search_term_length_min ? 'base': 'search';
	$this->ajax_view_url_suffix = '&amp;sysadmin='.Registry::get('userdata/id_user'); //.'&amp;form_search_term='.$this->filterReg['form_search_term'].''
	$result = $this->getFormResult($mode);
	$this->result_count = count($result); 
	$this->query_limit = $this->page_default * ( $this->filterReg['form_page'] - 1 );
	$list = Db::final_items($result, $this->query_limit, $this->page_default);
	
	if(count($list)<1){
		$this->filterReg['form_page'] = 1;
		$this->query_limit = $this->page_default * ( $this->filterReg['form_page'] - 1 );
		$list = Db::final_items($result, $this->query_limit, $this->page_default);
	}
	$pager = Navigator::pager_ajax($this->result_count, $this->page_default, $custom_uri = $this->ajax_view_url.'forms.browser.inner.php'.$this->ajax_view_url_suffix.'&amp;id_form='.$this->id_form.'&amp;form_order='.urlencode($this->filterReg['form_order']).'&amp;form_order_direction='.$this->filterReg['form_order_direction'], $force = $this->filterReg['form_page'], 'form_page' );
	
	$linkData = $this->getFormData($this->id_form);
	
	return $pager.$this->tableShow($list).$pager;
}

/*
 * Table list basic
 * */
private function tableShow($list){

$html = '<table class="mwms_data_list contentmod_data_list">
	<caption id="form_browser"><div id="browser_filter">'.$this->setFilterForm().'</div></caption>
	<thead>
		<tr>
			<th style="width:80px;">'.$this->lng['mwms_form_id'].' ('.$this->result_count.') '.$this->setOrder('form_order','id_form', 'form_order_direction', $this->lng['mwms_form_id'],'forms.browser.inner.php', true).'</th>
			<th style="width:200px;">'.$this->lng['mwms_form_title'].' '.$this->setOrder('form_order','title', 'form_order_direction', $this->lng['mwms_form_title'],'forms.browser.inner.php', true).'</th>
			<th style="width:140px;">'.$this->lng['mwms_form_date_ins'].' '.$this->setOrder('form_order','date_ins', 'form_order_direction', $this->lng['mwms_form_date_ins'],'forms.browser.inner.php', true).'</th>
			<th style="width:140px;">'.$this->lng['mwms_form_date_upd'].' '.$this->setOrder('form_order','date_upd', 'form_order_direction', $this->lng['mwms_form_date_upd'],'forms.browser.inner.php', true).'</th>
			<th class="toolbar">
				<button class="mwms_form_new button" title="'.$this->lng['mwms_form_new'].'">'.$this->lng['mwms_form_new'].'</button>
			</th>
		</tr>
	</thead><tbody>
	';

	foreach($list as $d){
		$hi = in_array($d['id_form'], $this->filterReg['form_dropbox']) ? ' highlight': null;
		
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
		
		$html .= '<tr id="form_cast_'.$d['id_form'].'" class="form_cast'.$hi.'" title="'.$d['title'].'">
			<td>'.$d['id_form'].'</td>
			<td>'.$d['title'].'</td>
			<td>'.$dateI.$strI.'</td>
			<td>'.$dateU.$strU.'</td>
			<td class="toolbar"></td>
		</tr>';
	
	}

	return $html.'</tbody></table>';
}



}
?>
