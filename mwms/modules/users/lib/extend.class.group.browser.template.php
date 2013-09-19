<?php
class GroupBrowserTemplate extends GroupBrowser{

protected $default_browser_mode;

public function __construct(){
	parent::__construct();
}

public function showGroups(){
	
	$this->ajax_view_url_suffix = '&amp;sysadmin='.Registry::get('userdata/id_user'); //.'&amp;params_search_term='.$this->filterReg['groups_search_term'].''
	$result = $this->getGroupResult();
	
	$this->result_count = count($result); 
	
	$this->query_limit = $this->page_default * ( $this->filterReg['groups_page'] - 1 );

	$list = Db::final_items($result, $this->query_limit, $this->page_default);
	
	$pager = Navigator::pager_ajax($this->result_count, $this->page_default, $custom_uri = $this->view_url.'group.browser'.$this->view_url_suffix.'&amp;groups_order='.$this->filterReg['groups_order'].'&amp;groups_order_direction='.$this->filterReg['groups_order_direction'], $force = $this->filterReg['groups_page'], 'params_page' );

	return $pager.$this->tableShow($list).$pager;
}	

/*
 * Table list basic
 * */
private function tableShow($list){

$html = '<table class="mwms_data_list usermod_data_list">
	<caption id="user_browser"><button class="mwms_user_group_new button" title="'.$this->lng['mwms_user_group_new'].'">'.$this->lng['mwms_user_group_new'].'</button> </caption>
	<thead>
	<tr>
		<th style="width:80px;">'.$this->lng['mwms_user_group_id'].' ('.$this->result_count.') '.$this->setOrder('groups_order','id_group', 'groups_order_direction', $this->lng['mwms_user_group_id'],'group.browser', false).'</th>
		<th>'.$this->lng['mwms_user_group_title'].' '.$this->setOrder('groups_order','title', 'groups_order_direction', $this->lng['mwms_user_group_title'],'group.browser', false).'</th>
		<th>'.$this->lng['mwms_user_group_description'].'</th>
		
	</tr></thead><tbody>';
        
	foreach($list as $d){
		
		$html .= '<tr id="group_cast_'.$d['id_group'].'" class="group_cast" title="'.$this->lng['mwms_user_group_edit'].' '.$d['title'].'">
			<td class="id_group">'.$d['id_group'].'</td>
			<td>'.$d['title'].'</td>
			<td>'.$d['description'].'</td>
			
		</tr>';
	
	}

	return $html.'</tbody></table>';
}	

}
?>
