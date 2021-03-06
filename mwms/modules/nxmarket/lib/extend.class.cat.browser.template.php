<?php
class CatBrowserTemplate extends CatBrowser{

protected $default_browser_mode;

public function __construct(){
	parent::__construct();
}

public function showContent(){
	$mode = is_null($this->filterReg['cats_search_term']) || mb_strlen($this->search_term)<$this->search_term_length_min ? 'base': 'search';
	$this->ajax_view_url_suffix = '&amp;sysadmin='.Registry::get('userdata/id_user'); //.'&amp;cats_search_term='.$this->filterReg['cats_search_term'].''
	$result = $this->getContentResult($mode);
	$this->result_count = count($result); 
	$this->query_limit = $this->page_default * ( $this->filterReg['cats_page'] - 1 );
	$list = Db::final_items($result, $this->query_limit, $this->page_default);
	$pager = Navigator::pager_ajax($this->result_count, $this->page_default, $custom_uri = $this->ajax_view_url.'cats.browser.inner.php'.$this->ajax_view_url_suffix.'&amp;cats_order='.$this->filterReg['cats_order'].'&amp;cats_order_direction='.$this->filterReg['cats_order_direction'], $force = $this->filterReg['cats_page'], 'cats_page' );
	
	$linkData = $this->getCats();
	
	return $pager.$this->tableShow($list).$pager;
}

/*
 * Table list basic
 * */
private function tableShow($list){

$html = '<table class="mwms_data_list contentmod_data_list">
	<caption id="cats_browser"><div id="browser_filter">'.$this->setFilterForm().'</div></caption>
	<thead>
		<tr>
			<th style="width:80px;">'.$this->lng['mwms_cat_id'].' ('.$this->result_count.') '.$this->setOrder('cats_order','id_cat', 'cats_order_direction', $this->lng['mwms_cat_id'],'cats.browser.inner.php', true).'</th>
			<th>'.$this->lng['mwms_cat_title'].' '.$this->setOrder('cats_order','title', 'cats_order_direction', $this->lng['mwms_cat_title'],'cats.browser.inner.php', true).'</th>
			<th>'.$this->lng['mwms_cat_template'].' '.$this->setOrder('articles_order','template', 'articles_order_direction', $this->lng['mwms_cat_template'],'articles.browser.inner.php', true).'</th>
			<th style="width:140px;">'.$this->lng['mwms_cat_last_update'].' '.$this->setOrder('cats_order','last_update', 'cats_order_direction', $this->lng['mwms_cat_last_update'],'cats.browser.inner.php', true).'</th>
			<th style="width:100px;">'.$this->lng['mwms_cat_public'].' '.$this->setOrder('cats_order','id_public', 'cats_order_direction', $this->lng['mwms_cat_public'],'cats.browser.inner.php', true).'</th>
			<th style="width:140px;">'.$this->lng['mwms_cat_date_ins'].' '.$this->setOrder('cats_order','date_ins', 'cats_order_direction', $this->lng['mwms_cat_date_ins'],'cats.browser.inner.php', true).'</th>
			<th style="width:140px;">'.$this->lng['mwms_cat_date_upd'].' '.$this->setOrder('cats_order','date_upd', 'cats_order_direction', $this->lng['mwms_cat_date_upd'],'cats.browser.inner.php', true).'</th>
			<th class="toolbar">
				<button class="mwms_cat_new button" title="'.$this->lng['mwms_cat_new'].'">'.$this->lng['mwms_cat_new'].'</button>
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
		
		if($d['last_update'] > 0){
			$dateLast = date(Lng::get('system/date_time_format_precise'), $d['last_update']);
		}
		
		$template = '! '.$d['template'];
		if( array_key_exists($d['template'], $this->lng['mwms_cat_templates']) ){
			$template = $this->lng['mwms_cat_templates'][$d['template']]['title'];
		}
		
		$html .= '<tr id="content_cast_'.$d['id_cat'].'" class="content_cast" title="'.$d['title'].'">
			<td>'.$d['id_cat'].' <input type="hidden" name="id_cat" value="'.$d['id_cat'].'" /></td>
			<td>'.$d['title'].'</td>
			<td>'.$template.'</td>
			<td>'.$dateLast.'</td>
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
