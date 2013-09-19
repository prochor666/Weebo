<?php
class TeamBrowserTemplate extends TeamBrowser{

protected $default_browser_mode;

public function __construct(){
	parent::__construct();
}

public function teamContent(){
	$mode = is_null($this->filterReg['team_search_term']) || mb_strlen($this->search_term)<$this->search_term_length_min ? 'base': 'search';
	$this->ajax_view_url_suffix = '&amp;sysadmin='.Registry::get('userdata/id_user'); //.'&amp;team_search_term='.$this->filterReg['team_search_term'].''
	$result = $this->getContentResult($mode);
	$this->result_count = count($result); 
	$this->query_limit = $this->page_default * ( $this->filterReg['team_page'] - 1 );
	$list = Db::final_items($result, $this->query_limit, $this->page_default);
	$pager = Navigator::pager_ajax($this->result_count, $this->page_default, $custom_uri = $this->ajax_view_url.'team.browser.inner.php'.$this->ajax_view_url_suffix.'&amp;team_order='.$this->filterReg['team_order'].'&amp;team_order_direction='.$this->filterReg['team_order_direction'], $force = $this->filterReg['team_page'], 'team_page' );
	
	return $pager.$this->tableTeam($list).$pager;
}

/*
 * Table list basic
 * */
private function tableTeam($list){

$html = '<table class="mwms_data_list contentmod_data_list">
	<caption id="team_browser"><div id="browser_filter">'.$this->setFilterForm().'</div></caption>
	<thead>
		<tr>
			<th style="width:80px;">'.$this->lng['tv_team_id'].' ('.$this->result_count.') '.$this->setOrder('team_order','id_team', 'team_order_direction', $this->lng['tv_team_id'],'team.browser.inner.php', true).'</th>
			<th>'.$this->lng['tv_team_title'].' '.$this->setOrder('team_order','title', 'team_order_direction', $this->lng['tv_team_title'],'team.browser.inner.php', true).'</th>
			<th style="width:140px;">'.$this->lng['tv_show_active'].' '.$this->setOrder('team_order','id_active', 'team_order_direction', $this->lng['tv_show_active'],'team.browser.inner.php', true).'</th>
			<th style="width:140px;">'.$this->lng['tv_team_date_ins'].' '.$this->setOrder('team_order','date_ins', 'team_order_direction', $this->lng['tv_team_date_ins'],'team.browser.inner.php', true).'</th>
			<th style="width:140px;">'.$this->lng['tv_team_date_upd'].' '.$this->setOrder('team_order','date_upd', 'team_order_direction', $this->lng['tv_team_date_upd'],'team.browser.inner.php', true).'</th>
			<th class="toolbar">
				<button class="tv_team_new button" title="'.$this->lng['tv_team_new'].'">'.$this->lng['tv_team_new'].'</button>
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
		
		$html .= '<tr id="content_cast_'.$d['id_team'].'" class="content_cast" title="'.$d['title'].'">
			<td>'.$d['id_team'].' <input type="hidden" name="id_team" value="'.$d['id_team'].'" /></td>
			<td>'.$d['title'].'</td>
			<td><span class="toggle-icon '.$this->toggleOnOff($d['id_active']).'">&nbsp;</span></td>
			<td>'.$dateI.$strI.'</td>
			<td>'.$dateU.$strU.'</td>
			<td class="toolbar"></td>
		</tr>';
	
	}
	return $html.'</tbody></table>';
}


}
?>
