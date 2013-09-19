<?php
class ShowItemBrowserTemplate extends ShowItemBrowser{

protected $default_browser_mode;

public function __construct(){
	parent::__construct();
}

public function showContent(){
	$mode = is_null($this->filterReg['show_items_search_term']) || mb_strlen($this->search_term)<$this->search_term_length_min ? 'base': 'search';
	$this->ajax_view_url_suffix = '&amp;sysadmin='.Registry::get('userdata/id_user'); //.'&amp;show_items_search_term='.$this->filterReg['show_items_search_term'].''
	$result = $this->getContentResult($mode);
	$this->result_count = count($result); 
	$this->query_limit = $this->page_default * ( $this->filterReg['show_items_page'] - 1 );
	$list = Db::final_items($result, $this->query_limit, $this->page_default);
	$pager = Navigator::pager_ajax($this->result_count, $this->page_default, $custom_uri = $this->ajax_view_url.'show.item.browser.inner.php'.$this->ajax_view_url_suffix.'&amp;show_items_order='.$this->filterReg['show_items_order'].'&amp;show_items_order_direction='.$this->filterReg['show_items_order_direction'], $force = $this->filterReg['show_items_page'], 'show_items_page' );
	
	return $pager.$this->tableShow($list).$pager;
}

/*
 * Table list basic
 * */
private function tableShow($list){

$html = '<table class="mwms_data_list contentmod_data_list">
	<caption id="show_items_browser"><div id="browser_filter">
	'.$this->setFilterForm().'
	<span class="label">'.$this->lng['tv_show_load'].'</span> '.$this->getShowListFilter((int)Registry::get('nettv_id_show_active')).'
	'.$this->showStateFilter().'
	</div></caption>
	<thead>
		<tr>
			<th style="width:80px;">'.$this->lng['tv_show_items_id'].' ('.$this->result_count.') </th>
			<th>'.$this->lng['tv_show_items_title'].'</th>
			<th>'.$this->lng['tv_show_items_publish'].'</th>
			<th>'.$this->lng['tv_show_items_date_public'].'</th>
			<th>'.$this->lng['tv_show_items_video'].'</th>
			<th>'.$this->lng['tv_show_load'].'</th>
			<th>'.$this->lng['tv_show_item_status'].'</th>
			<th style="width:140px;">'.$this->lng['tv_show_items_date_ins'].'</th>
			<th style="width:140px;">'.$this->lng['tv_show_items_date_upd'].'</th>
			<th class="toolbar">
				<button class="tv_show_items_new button" title="'.$this->lng['tv_show_items_new'].'">'.$this->lng['tv_show_items_new'].'</button>
			</th>
		</tr>
	</thead><tbody>
	';

	foreach($list as $d){
		
		$strI = '<br />'.$this->lng['tv_auto_user'];
		$strU = '<br />'.$this->lng['tv_auto_user'];
		
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
			$dateI = $d['date_ins'] > 0 ? date(Lng::get('system/date_time_format_precise'), $d['date_ins']).$strI: $dateI;
			$dateU = $d['date_upd'] > 0 ? date(Lng::get('system/date_time_format_precise'), $d['date_upd']).$strU: $dateU;
		}
		
		// get import feed data
		$xd = $this->getImportData($d['id_import']);
		$jobDone = count($xd)>0 ? $xd['job_done']: 10;
		
		// get show data
		$sd = $this->getShowData($d['id_show']);
		
		$showTitle = count($sd)>0 ? $sd['title']: '-';
		$media = json_decode($d['media']);
		
		$video = isset($media->video) && is_array($media->video) && count($media->video)>0 ? (string)$media->video[0]: null;
		
		$mediaButton = null;
		if(!is_null($video) && $jobDone == 1)
		{
			$rootDirLength = mb_strlen(System::root());
			$targetFileSuffix = mb_substr($video, $rootDirLength);
			$videoFile = $targetFileSuffix;
			
			if(file_exists($video))
			{
				$mediaButton = '<a class="vpr" href="'.$videoFile.'" title="'.$this->lng['tv_show_play_item'].': '.$video.'">'.$this->lng['tv_show_play_item'].'</a>';
			}else{
				$jobDone = $jobDone != -2 && $jobDone != 10 ? -3: $jobDone;
			}
		}
		
		$jobDoneStr = $jobDone != '!' ? $this->lng['tv_job_state_job_done'][$jobDone]: $jobDone;
		
		$img = null;
		
		if(isset($media->images) && is_array($media->images) && count($media->images)>0)
		{
			foreach($media->images as $i){
				$rootDirLength = mb_strlen(System::root());
				$targetFileSuffix = mb_substr((string)$i, $rootDirLength);
				$path = $targetFileSuffix;
				$img .= file_exists($i) && $this->isImage($i) ? '<img alt="prev" src="'.Registry::get('serverdata/site').'/'.$path.'" class="nettv-prev" title="'.Registry::get('serverdata/site').'/'.$path.'" />': null;
			}
		}
		
		$html .= '<tr id="content_cast_'.$d['id_item'].'" class="content_cast" title="'.$d['title'].'">
			<td>'.$d['id_item'].' 
				<input type="hidden" name="id_item" value="'.$d['id_item'].'" />
				<input type="hidden" name="id_import" value="'.$d['id_import'].'" />
				<input type="hidden" name="job_done" value="'.$jobDone.'" />
			</td>
			<td>'.$d['title'].'<br />'.$img.'</td>
			<td><span class="toggle-icon '.$this->toggleOnOff($d['id_public']).'">&nbsp;</span></td>
			<td>'.date($this->lng['date_time'], $d['date_public']).'</td>
			<td>'.$mediaButton.'</td>
			<td>'.$showTitle.'</td>
			<td><span id="status-'.$d['id_import'].'">'.$jobDoneStr.'</span></td>
			<td>'.$dateI.'</td>
			<td>'.$dateU.'</td>
			<td class="toolbar"></td>
		</tr>';
	
	}

	return $html.'</tbody></table>';
}

}
?>
