<?php
class ContentBrowserTemplate extends ContentBrowser{

protected $default_browser_mode;

public function __construct(){
	parent::__construct();
	
}

public function showContent(){
	
	
	$mode = is_null($this->filterReg['content_search_term']) || mb_strlen($this->search_term)<$this->search_term_length_min ? 'base': 'search';
	$this->ajax_view_url_suffix = '&amp;sysadmin='.Registry::get('userdata/id_user'); //.'&amp;content_search_term='.$this->filterReg['content_search_term'].''
	$result = $this->getContentResult($mode);
	$this->result_count = count($result); 
	$this->query_limit = $this->page_default * ( $this->filterReg['content_page'] - 1 );
	$list = Db::final_items($result, $this->query_limit, $this->page_default);
	
	if(count($list)<1){
		$this->filterReg['content_page'] = 1;
		$this->query_limit = $this->page_default * ( $this->filterReg['content_page'] - 1 );
		$list = Db::final_items($result, $this->query_limit, $this->page_default);
	}
	$pager = Navigator::pager_ajax($this->result_count, $this->page_default, $custom_uri = $this->ajax_view_url.'content.browser.inner.php'.$this->ajax_view_url_suffix.'&amp;id_link='.$this->id_link.'&amp;content_order='.urlencode($this->filterReg['content_order']).'&amp;content_order_direction='.$this->filterReg['content_order_direction'], $force = $this->filterReg['content_page'], 'content_page' );
	
	$linkData = $this->getLinkData($this->id_link);
	$this->default_custom_order = $this->ajax_view_url.'content.browser.inner.php'.$this->ajax_view_url_suffix.'&amp;content_order=custom&amp;content_order_direction=custom';
	
	$this->default_custom_order = $this->getDefaultLinkOrder();
	
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
			<th style="width:80px;">'.$this->lng['mwms_content_id'].' ('.$this->result_count.') '.$this->setOrder('content_order','id_content', 'content_order_direction', $this->lng['mwms_content_id'],'content.browser.inner.php', true).'</th>
			<th style="width:200px;">'.$this->lng['mwms_content_title'].' '.$this->setOrder('content_order','title', 'content_order_direction', $this->lng['mwms_content_title'],'content.browser.inner.php', true).'</th>
			<th style="width:200px;">'.$this->lng['mwms_content_public_order'].' '.$this->setOrder('content_order','public_order', 'content_order_direction', $this->lng['mwms_content_public_order'],'content.browser.inner.php', true).'</th>
			<th style="width:140px;">'.$this->lng['mwms_content_date_ins'].' '.$this->setOrder('content_order','date_ins', 'content_order_direction', $this->lng['mwms_content_date_ins'],'content.browser.inner.php', true).'</th>
			<th style="width:140px;">'.$this->lng['mwms_content_date_upd'].' '.$this->setOrder('content_order','date_upd', 'content_order_direction', $this->lng['mwms_content_date_upd'],'content.browser.inner.php', true).'</th>
			<th>'.$this->lng['mwms_content_multiwin'].'</th>
			<th>'.$this->lng['mwms_content_id_sticky'].'</th>
			<th>'.$this->lng['mwms_content_id_rss'].'</th>
			<th class="toolbar">
				<button class="mwms_content_new button" title="'.$this->lng['mwms_content_new'].'">'.$this->lng['mwms_content_new'].'</button>
				<button id="mwms_content_default_order" class="button" title="'.$this->lng['mwms_content_default_order'].'">'.$this->lng['mwms_content_default_order'].'</button>
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
		
		$html .= '<tr id="content_cast_'.$d['id_content'].'" class="content_cast" title="'.$d['title'].'">
			<td>'.$d['id_content'].' 
				<input type="hidden" name="id_content" value="'.$d['id_content'].'" />
				<input type="hidden" name="textmap" class="textmap" value="'.$d['textmap'].'" />
				<input type="hidden" name="id_brief_level" value="'.$d['id_brief_level'].'" />
			</td>
			<td>'.$d['title'].'</td>
			<td>'.$d['public_order'].'</td>
			<td>'.$dateI.$strI.'</td>
			<td>'.$dateU.$strU.'</td>
			<td><span class="toggle-icon '.$this->toggleOnOff($d['id_public']).'">&nbsp;</span></td>
			<td><span class="toggle-icon '.$this->toggleOnOff($d['id_sticky']).'">&nbsp;</span></td>
			<td><span class="toggle-icon '.$this->toggleOnOff($d['id_rss']).'">&nbsp;</span></td>
			<td class="toolbar"></td>
		</tr>';
	
	}

	return $html.'</tbody></table>';
}



}
?>
