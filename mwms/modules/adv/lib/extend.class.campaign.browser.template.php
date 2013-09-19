<?php
class CampaignBrowserTemplate extends CampaignBrowser{

protected $default_browser_mode;

public function __construct(){
	parent::__construct();
}

public function showContent(){
	$mode = is_null($this->filterReg['campaign_search_term']) || mb_strlen($this->search_term)<$this->search_term_length_min ? 'base': 'search';
	$this->ajax_view_url_suffix = '&amp;sysadmin='.Registry::get('userdata/id_user'); //.'&amp;campaign_search_term='.$this->filterReg['campaign_search_term'].''
	$result = $this->getContentResult($mode);
	$this->result_count = count($result); 
	$this->query_limit = $this->page_default * ( $this->filterReg['campaign_page'] - 1 );
	$list = Db::final_items($result, $this->query_limit, $this->page_default);
	$pager = Navigator::pager_ajax($this->result_count, $this->page_default, $custom_uri = $this->ajax_view_url.'campaign.browser.inner.php'.$this->ajax_view_url_suffix.'&amp;campaign_order='.$this->filterReg['campaign_order'].'&amp;campaign_order_direction='.$this->filterReg['campaign_order_direction'], $force = $this->filterReg['campaign_page'], 'campaign_page' );
	
	return $pager.$this->tableShow($list).$pager;
}

/*
 * Table list basic
 * */
private function tableShow($list){

$html = '<table class="mwms_data_list contentmod_data_list">
	<caption id="campaign_browser"><div id="browser_filter">'.$this->setFilterForm().'</div></caption>
	<thead>
		<tr>
			<th style="width:80px;">'.$this->lng['adv_campaign_id'].' ('.$this->result_count.') '.$this->setOrder('campaign_order','id_campaign', 'campaign_order_direction', $this->lng['adv_campaign_id'],'campaign.browser.inner.php', true).'</th>
			<th>'.$this->lng['adv_campaign_title'].' '.$this->setOrder('campaign_order','title', 'campaign_order_direction', $this->lng['adv_campaign_title'],'campaign.browser.inner.php', true).'</th>
			<th style="width:140px;">'.$this->lng['adv_campaign_date_ins'].' '.$this->setOrder('campaign_order','date_ins', 'campaign_order_direction', $this->lng['adv_campaign_date_ins'],'campaign.browser.inner.php', true).'</th>
			<th style="width:140px;">'.$this->lng['adv_campaign_date_upd'].' '.$this->setOrder('campaign_order','date_upd', 'campaign_order_direction', $this->lng['adv_campaign_date_upd'],'campaign.browser.inner.php', true).'</th>
			<th class="toolbar">
				<button class="adv_campaign_new button" title="'.$this->lng['adv_campaign_new'].'">'.$this->lng['adv_campaign_new'].'</button>
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
		
		$html .= '<tr id="content_cast_'.$d['id_campaign'].'" class="content_cast" title="'.$d['title'].'">
			<td>'.$d['id_campaign'].' <input type="hidden" name="id_campaign" value="'.$d['id_campaign'].'" /></td>
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
