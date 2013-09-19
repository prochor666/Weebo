<?php
class ChannelBrowserTemplate extends ChannelBrowser{

protected $default_browser_mode;

public function __construct(){
	parent::__construct();
}

public function showContent(){
	$mode = is_null($this->filterReg['channels_search_term']) || mb_strlen($this->search_term)<$this->search_term_length_min ? 'base': 'search';
	$this->ajax_view_url_suffix = '&amp;sysadmin='.Registry::get('userdata/id_user'); //.'&amp;channels_search_term='.$this->filterReg['channels_search_term'].''
	$result = $this->getContentResult($mode);
	$this->result_count = count($result); 
	$this->query_limit = $this->page_default * ( $this->filterReg['channels_page'] - 1 );
	$list = Db::final_items($result, $this->query_limit, $this->page_default);
	$pager = Navigator::pager_ajax($this->result_count, $this->page_default, $custom_uri = $this->ajax_view_url.'channels.browser.inner.php'.$this->ajax_view_url_suffix.'&amp;channels_order='.$this->filterReg['channels_order'].'&amp;channels_order_direction='.$this->filterReg['channels_order_direction'], $force = $this->filterReg['channels_page'], 'channels_page' );
	
	$linkData = $this->getArticles();
	
	return $pager.$this->tableShow($list).$pager;
}

/*
 * Table list basic
 * */
private function tableShow($list){

$html = '<table class="mwms_data_list contentmod_data_list">
	<caption id="channels_browser"><div id="browser_filter">
		'.$this->setFilterForm().'
		<span class="label">'.$this->lng['mwms_channel_source'].'</span> '.$this->getArticleSourceFilter((int)Registry::get('nxmarket_id_source_active')).'
	</div></caption>
	<thead>
		<tr>
			<th style="width:80px;">'.$this->lng['mwms_channel_id'].' ('.$this->result_count.') '.$this->setOrder('channels_order','id_channel', 'channels_order_direction', $this->lng['mwms_channel_id'],'channels.browser.inner.php', true).'</th>
			<th>'.$this->lng['mwms_channel_title'].' '.$this->setOrder('channels_order','title', 'channels_order_direction', $this->lng['mwms_channel_title'],'channels.browser.inner.php', true).'</th>
			<th>'.$this->lng['mwms_channel_date_public'].' '.$this->setOrder('channels_order','date_public', 'channels_order_direction', $this->lng['mwms_channel_date_public'],'channels.browser.inner.php', true).'</th>
			<th style="width:100px;">'.$this->lng['mwms_channel_public'].' '.$this->setOrder('channels_order','id_public', 'channels_order_direction', $this->lng['mwms_channel_public'],'channels.browser.inner.php', true).'</th>
			<th style="width:140px;">'.$this->lng['mwms_channel_date_ins'].' '.$this->setOrder('channels_order','date_ins', 'channels_order_direction', $this->lng['mwms_channel_date_ins'],'channels.browser.inner.php', true).'</th>
			<th style="width:140px;">'.$this->lng['mwms_channel_date_upd'].' '.$this->setOrder('channels_order','date_upd', 'channels_order_direction', $this->lng['mwms_channel_date_upd'],'channels.browser.inner.php', true).'</th>
			<th class="toolbar">
				<button class="mwms_channel_new button" title="'.$this->lng['mwms_channel_new'].'">'.$this->lng['mwms_channel_new'].'</button>
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
		
		$html .= '<tr id="content_cast_'.$d['id_channel'].'" class="content_cast" title="'.$d['title'].'">
			<td>'.$d['id_channel'].' <input type="hidden" name="id_channel" value="'.$d['id_channel'].'" /></td>
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
	return Db::result("SELECT * FROM "._SQLPREFIX_."_nxmarket_meta WHERE id_channel = ".(int)$item['id_channel']);
}

public function getEditImages($item){
	return Db::result("SELECT * FROM "._SQLPREFIX_."_nxmarket_meta WHERE id_channel = ".(int)$item['id_channel']." AND tag LIKE 'imageSetEdit'");
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
		
		$meta .= '<table class="nxmarket-meta">';
		
		foreach($mq as $m){
			
			if($m['tag'] == 'imageSet'){
				
				$images = '<div class="nxmarket-images">';
				
				$imageTemp = json_decode($m['value']);
				array_walk($imageTemp, array($this, 'setFullPath', ));
				
				foreach($imageTemp as $img){
					$images .= '<a href="'.$img.'" target="_blank" class="nxmarket-thumb" rel="mm-item-'.$item['id_channel'].'" title="'.$item['title'].'"><img src="'.$img.'" alt="'.$item['title'].'" title="'.$item['title'].'" /></a>';
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
