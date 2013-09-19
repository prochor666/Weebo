<?php
class ItemBrowserTemplate extends ItemBrowser{

protected $default_browser_mode;

public function __construct(){
	parent::__construct();
}

public function showContent(){
	$mode = is_null($this->filterReg['items_search_term']) || mb_strlen($this->search_term)<$this->search_term_length_min ? 'base': 'search';
	$this->ajax_view_url_suffix = '&amp;sysadmin='.Registry::get('userdata/id_user'); //.'&amp;items_search_term='.$this->filterReg['items_search_term'].''
	$result = $this->getContentResult($mode);
	$this->result_count = count($result); 
	$this->query_limit = $this->page_default * ( $this->filterReg['items_page'] - 1 );
	$list = Db::final_items($result, $this->query_limit, $this->page_default);
	$pager = Navigator::pager_ajax($this->result_count, $this->page_default, $custom_uri = $this->ajax_view_url.'items.browser.inner.php'.$this->ajax_view_url_suffix.'&amp;items_order='.$this->filterReg['items_order'].'&amp;items_order_direction='.$this->filterReg['items_order_direction'], $force = $this->filterReg['items_page'], 'items_page' );
	
	$linkData = $this->getContentResult();
	
	return $pager.$this->tableShow($list).$pager;
}

/*
 * Table list basic
 * */
private function tableShow($list){

$html = '<table class="mwms_data_list contentmod_data_list">
	<caption id="items_browser"><div id="browser_filter">'.$this->setFilterForm().'</div></caption>
	<thead>
		<tr>
			<th style="width:80px;">'.$this->lng['items_id'].' ('.$this->result_count.') '.$this->setOrder('items_order','id_item', 'items_order_direction', $this->lng['items_id'],'items.browser.inner.php', true).'</th>
			<th>'.$this->lng['items_title'].' '.$this->setOrder('items_order','title', 'items_order_direction', $this->lng['items_title'],'items.browser.inner.php', true).'</th>
			<th style="width:100px;">'.$this->lng['items_price'].' '.$this->setOrder('items_order','price', 'articles_order_direction', $this->lng['items_price'],'items.browser.inner.php', true).'</th>
			<th style="width:100px;">'.$this->lng['items_vat'].' '.$this->setOrder('items_order','vat', 'articles_order_direction', $this->lng['items_vat'],'items.browser.inner.php', true).'</th>
			<th style="width:100px;">'.$this->lng['items_public'].' '.$this->setOrder('items_order','id_public', 'items_order_direction', $this->lng['items_public'],'items.browser.inner.php', true).'</th>
			<th style="width:140px;">'.$this->lng['items_date_ins'].' '.$this->setOrder('items_order','date_ins', 'items_order_direction', $this->lng['items_date_ins'],'items.browser.inner.php', true).'</th>
			<th style="width:140px;">'.$this->lng['items_date_upd'].' '.$this->setOrder('items_order','date_upd', 'items_order_direction', $this->lng['items_date_upd'],'items.browser.inner.php', true).'</th>
			<th class="toolbar">
				<button class="items_new button" title="'.$this->lng['items_new'].'">'.$this->lng['items_new'].'</button>
			</th>
		</tr>
	</thead><tbody>
	';

	foreach($list as $d){
		
		$strI = null;
		$strU = null;
		
		if($d['id_ins'] > 0 || $d['id_upd'] > 0){
			$uD = new UserBrowser;
			$userIData = $d['id_ins'] > 0 ? $uD->getUserData($d['id_ins']): null;
			$userUData = $d['id_upd'] > 0 ? $uD->getUserData($d['id_upd']): null;
			$strI = $d['id_ins'] > 0 ? '<br />'.$userIData['username']: $strI;
			$strU = $d['id_upd'] > 0 ? '<br />'.$userUData['username']: $strU;
		}
		
		$dateI = null;
		$dateU = null;
		$dateLast = null;
		
		if($d['date_ins'] > 0 || $d['id_upd'] > 0){
			$dateI = $d['date_ins'] > 0 ? date(Lng::get('system/date_time_format_precise'), $d['date_ins']): $dateI;
			$dateU = $d['date_upd'] > 0 ? date(Lng::get('system/date_time_format_precise'), $d['date_upd']): $dateU;
		}
		
		$html .= '<tr id="content_cast_'.$d['id_item'].'" class="item_cast" title="'.$d['title'].'">
			<td>'.$d['id_item'].' <input type="hidden" name="id_item" value="'.$d['id_item'].'" /></td>
			<td>'.$d['title'].'</td>
			<td>'.$d['price'].'</td>
			<td>'.$d['vat'].'</td>
			<td><span class="toggle-icon '.$this->toggleOnOff($d['id_public']).'"></span></td>
			<td>'.$dateI.$strI.'</td>
			<td>'.$dateU.$strU.'</td>
			<td class="toolbar"></td>
		</tr>';
	
	}

	return $html.'</tbody></table>';
}



}
?>
