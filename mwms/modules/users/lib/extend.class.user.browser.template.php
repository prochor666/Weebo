<?php
class UserBrowserTemplate extends UserBrowser{

protected $default_browser_mode;

public function __construct(){
	parent::__construct();
}

public function showUsers(){
	
	$mode = is_null($this->filterReg['users_search_term']) || mb_strlen($this->search_term)<$this->search_term_length_min ? 'base': 'search';
	$this->ajax_view_url_suffix = '&amp;sysadmin='.Registry::get('userdata/id_user'); //.'&amp;users_search_term='.$this->filterReg['users_search_term'].''
	$result = $this->getUserResult($mode);
	
	$this->result_count = count($result); 
	
	$this->query_limit = $this->page_default * ( $this->filterReg['users_page'] - 1 );

	$list = Db::final_items($result, $this->query_limit, $this->page_default);
	
	$pager = Navigator::pager_ajax($this->result_count, $this->page_default, $custom_uri = $this->ajax_view_url.'user.browser.control.php'.$this->ajax_view_url_suffix.'&amp;users_order='.$this->filterReg['users_order'].'&amp;users_order_direction='.$this->filterReg['users_order_direction'], $force = $this->filterReg['users_page'], 'users_page' );

	return $pager.$this->tableShow($list).$pager;
}	

/*
 * Table list basic
 * */
private function tableShow($list){

$filter = Login::is_site_root() ? '<caption id="user_browser"><div id="browser_filter">'.$this->setFilterForm().'</div></caption>': null; 

$html = '<table class="mwms_data_list usermod_data_list">
	'.$filter.'
	<thead>
	<tr>
		<th style="width:80px;">'.$this->lng['mwms_user_id'].' ('.$this->result_count.') '.$this->setOrder('users_order','id_user', 'users_order_direction', $this->lng['mwms_user_id'],'user.browser.control.php', true).'</th>
		<th style="width:100px;">'.$this->lng['mwms_mail'].' '.$this->setOrder('users_order','mail', 'users_order_direction', $this->lng['mwms_mail'],'user.browser.control.php', true).'</th>
		<th style="width:160px;">'.$this->lng['mwms_username'].' '.$this->setOrder('users_order','username', 'users_order_direction', $this->lng['mwms_username'],'user.browser.control.php', true).'</th>
		<th>'.$this->lng['mwms_lastname'].' '.$this->setOrder('users_order','lastname', 'users_order_direction', $this->lng['mwms_lastname'],'user.browser.control.php', true).'</th>
		<th>'.$this->lng['mwms_firstname'].' '.$this->setOrder('users_order','firstname', 'users_order_direction', $this->lng['mwms_firstname'],'user.browser.control.php', true).'</th>
		<th>'.$this->lng['mwms_root_col'].' '.$this->setOrder('users_order','root', 'users_order_direction', $this->lng['mwms_root_col'],'user.browser.control.php', true).'</th>
		<th>'.$this->lng['mwms_admin_label'].' '.$this->setOrder('users_order','admin', 'users_order_direction', $this->lng['mwms_admin_label'],'user.browser.control.php', true).'</th>
		<th style="width:250px;">'.$this->lng['mwms_last_time'].' '.$this->setOrder('users_order','lasttime', 'users_order_direction', $this->lng['mwms_last_time'],'user.browser.control.php', true).'</th>
		<th class="toolbar">
			<button class="user_new button" title="'.$this->lng['mwms_user_new'].'">'.$this->lng['mwms_user_new'].'</button>
		</th>
	</tr></thead><tbody>';
        
	foreach($list as $d){
		$hi = in_array($d['id_user'], $this->filterReg['users_dropbox']) ? ' highlight': null;
		
		$rootInfo = $this->lng['mwms_user_roles_list'];
		
		$html .= '<tr id="user_cast_'.$d['id_user'].'" class="user_cast'.$hi.'" title="'.$d['username'].'">
			<td>'.$d['id_user'].'</td>
			<td>'.$d['mail'].'</td>
			<td>'.$d['username'].'</td>
			<td>'.$d['lastname'].'</td>
			<td>'.$d['firstname'].'</td>
			<td>'.$rootInfo[$d['root']].'</td>
			<td><span class="toggle-icon '.$this->toggleOnOff($d['admin']).'"></span></td>
			<td>'.date(Lng::get('system/date_time_format_precise'), $d['lasttime']).'</td>
			<td class="toolbar">
				
			</td>
			
		</tr>';
	
	}

	return $html.'</tbody></table>';
}	


}
?>
