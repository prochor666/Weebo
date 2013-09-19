<?php
class AssetBrowserTemplate extends AssetBrowser{

protected $default_browser_mode;

public function __construct(){
	parent::__construct();
}

public function showContent(){
	$mode = is_null($this->filterReg['asset_search_term']) || mb_strlen($this->search_term)<$this->search_term_length_min ? 'base': 'search';
	$this->ajax_view_url_suffix = '&amp;sysadmin='.Registry::get('userdata/id_user'); //.'&amp;asset_search_term='.$this->filterReg['asset_search_term'].''
	$result = $this->getContentResult($mode);
	$this->result_count = count($result); 
	$this->query_limit = $this->page_default * ( $this->filterReg['asset_page'] - 1 );
	$list = Db::final_items($result, $this->query_limit, $this->page_default);
	$pager = Navigator::pager_ajax($this->result_count, $this->page_default, $custom_uri = $this->ajax_view_url.'asset.browser.inner.php'.$this->ajax_view_url_suffix.'&amp;asset_order='.$this->filterReg['asset_order'].'&amp;asset_order_direction='.$this->filterReg['asset_order_direction'], $force = $this->filterReg['asset_page'], 'asset_page' );
	
	return $pager.$this->tableShow($list).$pager;
}

/*
 * Table list basic
 * */
private function tableShow($list){

$html = '<table class="mwms_data_list contentmod_data_list">
	<caption id="asset_browser"><div id="browser_filter">'.$this->setFilterForm().'</div></caption>
	<thead>
		<tr>
			<th style="width:80px;">'.$this->lng['adv_asset_id'].' ('.$this->result_count.') '.$this->setOrder('asset_order','id_asset', 'asset_order_direction', $this->lng['adv_asset_id'],'asset.browser.inner.php', true).'</th>
			<th>'.$this->lng['adv_asset_title'].' ('.$this->lng['adv_asset_banner'].') '.$this->setOrder('asset_order','title', 'asset_order_direction', $this->lng['adv_asset_title'],'asset.browser.inner.php', true).'</th>
			<th>'.$this->lng['adv_banner_format'].'</th>
			<th>'.$this->lng['adv_asset_position'].'</th>
			<th>'.$this->lng['adv_asset_campaign'].'</th>
			<th>'.$this->lng['adv_asset_date_from'].'/'.$this->lng['adv_asset_date_to'].'</th>
			<th>'.$this->lng['adv_asset_stat_go'].'</th>
			<th>'.$this->lng['adv_banner_url'].'</th>
			<th>'.$this->lng['adv_asset_id_active'].' '.$this->setOrder('asset_order','title', 'asset_order_direction', $this->lng['adv_asset_id_active'],'asset.browser.inner.php', true).'</th>
			<th style="width:140px;">'.$this->lng['adv_asset_date_ins'].' '.$this->setOrder('asset_order','date_ins', 'asset_order_direction', $this->lng['adv_asset_date_ins'],'asset.browser.inner.php', true).'</th>
			<th style="width:140px;">'.$this->lng['adv_asset_date_upd'].' '.$this->setOrder('asset_order','date_upd', 'asset_order_direction', $this->lng['adv_asset_date_upd'],'asset.browser.inner.php', true).'</th>
			<th class="toolbar">
				<button class="adv_asset_new button" title="'.$this->lng['adv_asset_new'].'">'.$this->lng['adv_asset_new'].'</button>
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
		
		$_b = $this->getBannerData($d['id_banner']);
		$_p = $this->getPositionData($d['id_position']);
		$_c = $this->getCampaignData($d['id_campaign']);
		
		$st = $d['date_to'] > time() ? ' style="color:#060;"': ' style="color:#f00;"';
		$st = $d['id_active'] == 0 ? null: $st;
				
		$html .= '<tr '.$st.' id="content_cast_'.$d['id_asset'].'" class="content_cast" title="'.$d['title'].'">
			<td>'.$d['id_asset'].' <input type="hidden" name="id_asset" value="'.$d['id_asset'].'" /></td>
			<td>'.$d['title'].' ('.$_b['title'].')</td>
			<td>'.$_b['format'].'</td>
			<td>'.$d['id_position'].' - '.$_p['title'].'</td>
			<td>'.$_c['title'].'</td>
			<td>'.date(Lng::get('adv/date_time'), $d['date_from']).'<br />'.date(Lng::get('adv/date_time'), $d['date_to']).'</td>
			<td><a href="?module=adv&amp;sub=stat.browser&amp;id_asset='.$d['id_asset'].'" class="asset-stats">'.$this->lng['adv_asset_stat_go'].'</a></td>
			<td><a href="'.$_b['url'].'" rel="nofollow" class="urltest" target="_blank" title="'.$_b['url'].'">'.$this->lng['adv_banner_url_go'].'</a></td>
			<td><span class="toggle-icon '.$this->toggleOnOff($d['id_active']).'"></span></td>
			<td>'.$dateI.$strI.'</td>
			<td>'.$dateU.$strU.'</td>
			<td class="toolbar"></td>
		</tr>';
	
	}

	return $html.'</tbody></table>';
}



}
?>
