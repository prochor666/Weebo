<?php
class ArticleBrowserTemplate extends ArticleBrowser{

protected $default_browser_mode;

public function __construct(){
	parent::__construct();
}

public function showContent(){
	$mode = is_null($this->filterReg['articles_search_term']) || mb_strlen($this->search_term)<$this->search_term_length_min ? 'base': 'search';
	$this->ajax_view_url_suffix = '&amp;sysadmin='.Registry::get('userdata/id_user'); //.'&amp;articles_search_term='.$this->filterReg['articles_search_term'].''
	$result = $this->getContentResult($mode);
	$this->result_count = count($result); 
	$this->query_limit = $this->page_default * ( $this->filterReg['articles_page'] - 1 );
	$list = Db::final_items($result, $this->query_limit, $this->page_default);
	$pager = Navigator::pager_ajax($this->result_count, $this->page_default, $custom_uri = $this->ajax_view_url.'articles.browser.inner.php'.$this->ajax_view_url_suffix.'&amp;articles_order='.$this->filterReg['articles_order'].'&amp;articles_order_direction='.$this->filterReg['articles_order_direction'], $force = $this->filterReg['articles_page'], 'articles_page' );
	
	$linkData = $this->getArticles();
	
	return $pager.$this->tableShow($list).$pager;
}

/*
 * Table list basic
 * */
private function tableShow($list){

$html = '<table class="mwms_data_list contentmod_data_list">
	<caption id="articles_browser"><div id="browser_filter">
		'.$this->setFilterForm().'
		<span class="label">'.$this->lng['mwms_article_source'].'</span> '.$this->getArticleSourceFilter((int)Registry::get('mediamix_id_source_active')).'
	</div></caption>
	<thead>
		<tr>
			<th style="width:80px;">'.$this->lng['mwms_article_id'].' ('.$this->result_count.') '.$this->setOrder('articles_order','id_article', 'articles_order_direction', $this->lng['mwms_article_id'],'articles.browser.inner.php', true).'</th>
			<th>'.$this->lng['mwms_article_title'].' '.$this->setOrder('articles_order','title', 'articles_order_direction', $this->lng['mwms_article_title'],'articles.browser.inner.php', true).'</th>
			<th>'.$this->lng['mwms_article_date_public'].' '.$this->setOrder('articles_order','date_public', 'articles_order_direction', $this->lng['mwms_article_date_public'],'articles.browser.inner.php', true).'</th>
			<th style="width:100px;">'.$this->lng['mwms_article_public'].' '.$this->setOrder('articles_order','id_public', 'articles_order_direction', $this->lng['mwms_article_public'],'articles.browser.inner.php', true).'</th>
			<th style="width:140px;">'.$this->lng['mwms_article_date_ins'].' '.$this->setOrder('articles_order','date_ins', 'articles_order_direction', $this->lng['mwms_article_date_ins'],'articles.browser.inner.php', true).'</th>
			<th style="width:140px;">'.$this->lng['mwms_article_date_upd'].' '.$this->setOrder('articles_order','date_upd', 'articles_order_direction', $this->lng['mwms_article_date_upd'],'articles.browser.inner.php', true).'</th>
			<th class="toolbar">
				<button class="mwms_article_new button" title="'.$this->lng['mwms_article_new'].'">'.$this->lng['mwms_article_new'].'</button>
			</th>
		</tr>
	</thead><tbody>
	';

	foreach($list as $d){
		
		$strI = '<br />SYSTEM';
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
		
		if($d['date_ins'] > 0 || $d['id_upd'] > 0){
			$dateI = $d['date_ins'] > 0 ? date(Lng::get('system/date_time_format_precise'), $d['date_ins']): $dateI;
			$dateU = $d['date_upd'] > 0 ? date(Lng::get('system/date_time_format_precise'), $d['date_upd']): $dateU;
		}
		
		$_s = $this->getSourceData($d['id_source']);
		
		$title = $_s['template'] == 'autobazary' ? $this->autobazarAdminShow($d): $d['title'];
		
		$html .= '<tr id="content_cast_'.$d['id_article'].'" class="content_cast" title="'.$d['title'].'">
			<td>'.$d['id_article'].' <input type="hidden" name="id_article" value="'.$d['id_article'].'" /></td>
			<td>'.$title.'</td>
			<td>'.date(Lng::get('system/date_time_format_precise'), $d['date_public']).'</td>
			<td><span class="toggle-icon '.$this->toggleOnOff($d['id_public']).'"></span></td>
			<td>'.$dateI.$strI.'</td>
			<td>'.$dateU.$strU.'</td>
			<td class="toolbar"></td>
		</tr>';
	
	}

	return $html.'</tbody></table>';
}

public function getMeta($item){
	return Db::result("SELECT * FROM "._SQLPREFIX_."_mm_meta WHERE id_article = ".(int)$item['id_article']);
}

public function getEditImages($item){
	return Db::result("SELECT * FROM "._SQLPREFIX_."_mm_meta WHERE id_article = ".(int)$item['id_article']." AND tag LIKE 'imageSetEdit'");
}

public function autobazarAdminShow($item){
	
	$images = null; 
	$meta = null;
	$newTitle = null;
	$price = null;
	
	$model = null;
	$vendor = null;
	$motor = null;
	$year = null;
	
	$mq = $this->getMeta($item);
	
	if(count($mq)){
		
		$meta .= '<table class="mediamix-meta">';
		
		foreach($mq as $m){
			
			if($m['tag'] == 'imageSet'){
				
				$images = '<div class="mediamix-images">';
				
				$imageTemp = json_decode($m['value']);
				array_walk($imageTemp, array($this, 'setFullPath', ));
				
				foreach($imageTemp as $img){
					$images .= '<a href="'.$img.'" target="_blank" class="mediamix-thumb" rel="mm-item-'.$item['id_article'].'" title="'.$item['title'].'"><img src="'.$img.'" alt="'.$item['title'].'" title="'.$item['title'].'" /></a>';
				}
				
				$images .= '</div>';
				
			}else{
				
				if(mb_strtolower($m['tag']) == 'značka vozu'){
					$vendor = trim($m['value']);
				}
				
				if(mb_strtolower($m['tag']) == 'model vozu'){
					$model = trim($m['value']);
				}
				
				if(mb_strtolower($m['tag']) == 'motor'){
					$motor = trim($m['value']);
				}
				
				if(mb_strtolower($m['tag']) == 'rok výroby' && (int)$m['value'] > 1900){
					$year = trim($m['value']);
				}
				
				if(mb_strtolower($m['tag']) == 'cena'){
					$price = number_format(str_replace(' ', '', trim($m['value'])), 2, ',', ' ');
				}
				// Convert nl2br to ul/li html
				$_val = count( $_arr = explode("\n", trim($m['value'])) ) > 1 && array_walk($_arr, 'trim') === true ? '<ul><li>'.implode('</li><li>', $_arr).'</li></ul>': trim($m['value']); 
				
				$meta .= '<tr><th>'.$m['tag'].'</th><td>'.$_val.'</td></tr>';
			}
			
		}
		
		$meta .= '</table>';
	}
	
	$newTitle .= mb_strlen($vendor)>0 ? $vendor: null;
	$newTitle .= mb_strlen($model)>0 ? ' '.$model: null;
	$newTitle .= mb_strlen($motor)>0 ? ', '.$motor: null;
	$newTitle .= mb_strlen($year)>0 ? ', '.$year: null;
	
	$title = mb_strlen($newTitle)>0 ? $newTitle: $item['title'];
	
	return $item['title'].' > '.$title;
}


}
?>
