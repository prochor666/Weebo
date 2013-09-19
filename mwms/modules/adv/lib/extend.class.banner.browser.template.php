<?php
class BannerBrowserTemplate extends BannerBrowser{

protected $default_browser_mode;

public function __construct(){
	parent::__construct();
}

public function showContent(){
	$mode = is_null($this->filterReg['banner_search_term']) || mb_strlen($this->search_term)<$this->search_term_length_min ? 'base': 'search';
	$this->ajax_view_url_suffix = '&amp;sysadmin='.Registry::get('userdata/id_user'); //.'&amp;banner_search_term='.$this->filterReg['banner_search_term'].''
	$result = $this->getContentResult($mode);
	$this->result_count = count($result); 
	$this->query_limit = $this->page_default * ( $this->filterReg['banner_page'] - 1 );
	$list = Db::final_items($result, $this->query_limit, $this->page_default);
	$pager = Navigator::pager_ajax($this->result_count, $this->page_default, $custom_uri = $this->ajax_view_url.'banner.browser.inner.php'.$this->ajax_view_url_suffix.'&amp;banner_order='.$this->filterReg['banner_order'].'&amp;banner_order_direction='.$this->filterReg['banner_order_direction'], $force = $this->filterReg['banner_page'], 'banner_page' );
	
	return $pager.$this->tableShow($list).$pager;
}

/*
 * Table list basic
 * */
private function tableShow($list){

$html = '<table class="mwms_data_list contentmod_data_list">
	<caption id="banner_browser"><div id="browser_filter">'.$this->setFilterForm().'</div></caption>
	<thead>
		<tr>
			<th style="width:80px;">'.$this->lng['adv_banner_id'].' ('.$this->result_count.') '.$this->setOrder('banner_order','id_banner', 'banner_order_direction', $this->lng['adv_banner_id'],'banner.browser.inner.php', true).'</th>
			<th>'.$this->lng['adv_banner_title'].' '.$this->setOrder('banner_order','title', 'banner_order_direction', $this->lng['adv_banner_title'],'banner.browser.inner.php', true).'</th>
			<th>'.$this->lng['adv_banner_format'].'</th>
			<th>'.$this->lng['adv_banner_url'].'</th>
			<th>'.$this->lng['adv_banner_file'].' '.$this->setOrder('banner_order','file', 'banner_order_direction', $this->lng['adv_banner_file'],'banner.browser.inner.php', true).'</th>
			<th style="width:140px;">'.$this->lng['adv_banner_date_ins'].' '.$this->setOrder('banner_order','date_ins', 'banner_order_direction', $this->lng['adv_banner_date_ins'],'banner.browser.inner.php', true).'</th>
			<th style="width:140px;">'.$this->lng['adv_banner_date_upd'].' '.$this->setOrder('banner_order','date_upd', 'banner_order_direction', $this->lng['adv_banner_date_upd'],'banner.browser.inner.php', true).'</th>
			<th class="toolbar">
				<button class="adv_banner_new button" title="'.$this->lng['adv_banner_new'].'">'.$this->lng['adv_banner_new'].'</button>
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
		
		$html .= '<tr id="content_cast_'.$d['id_banner'].'" class="content_cast" title="'.$d['title'].'">
			<td>'.$d['id_banner'].' <input type="hidden" name="id_banner" value="'.$d['id_banner'].'" /></td>
			<td>'.$d['title'].'</td>
			<td>'.$d['format'].'</td>
			<td><a href="'.$d['url'].'" rel="nofollow" class="urltest" target="_blank" title="'.$d['url'].'">'.$this->lng['adv_banner_url_go'].'</a></td>
			<td>'.basename($d['file']).'</td>
			<td>'.$dateI.$strI.'</td>
			<td>'.$dateU.$strU.'</td>
			<td class="toolbar"></td>
		</tr>';
	
	}

	return $html.'</tbody></table>';
}



}
?>
