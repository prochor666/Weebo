<?php
class MetaBrowserTemplate extends MetaBrowser{

protected $default_browser_mode;

public function __construct(){
	parent::__construct();
}

public function showMeta(){
	
	$this->ajax_view_url_suffix = '&amp;sysadmin='.Registry::get('userdata/id_user'); //.'&amp;meta_search_term='.$this->filterReg['meta_search_term'].''
	$result = $this->getMetaResult();
	$this->result_count = count($result); 
	$this->query_limit = $this->page_default * ( $this->filterReg['meta_page'] - 1 );
	$list = Db::final_items($result, $this->query_limit, $this->page_default);
	$pager = Navigator::pager_ajax($this->result_count, $this->page_default, $custom_uri = $this->view_url.'meta.browser'.$this->view_url_suffix.'&amp;meta_order='.$this->filterReg['meta_order'].'&amp;meta_order_direction='.$this->filterReg['meta_order_direction'], $force = $this->filterReg['meta_page'], 'meta_page' );

	return $pager.$this->tableShow($list).$pager;
}	

/*
 * Table list basic
 * */
private function tableShow($list){

$html = '<table class="mwms_data_list usermod_data_list">
	<caption id="user_browser">
		<button class="mwms_meta_new button" title="'.$this->lng['mwms_meta_new'].'">'.$this->lng['mwms_meta_new'].'</button>
	</caption>
	<thead>
	<tr>
		<th style="width:80px;">'.$this->lng['mwms_meta_id'].' ('.$this->result_count.') '.$this->setOrder('meta_order','id', 'meta_order_direction', $this->lng['mwms_meta_id'],'meta.browser', false).'</th>
		<th>'.$this->lng['mwms_meta_title'].' '.$this->setOrder('meta_order','title', 'meta_order_direction', $this->lng['mwms_meta_title'],'meta.browser', false).'</th>
		<th style="width:100px;">'.$this->lng['mwms_meta_active'].' '.$this->setOrder('meta_order','active', 'meta_order_direction', $this->lng['mwms_meta_active'],'meta.browser', false).'</th>
		<th style="width:150px;">'.$this->lng['mwms_meta_data_type'].'</th>
		<th style="width:80px;">'.$this->lng['mwms_meta_order'].' '.$this->setOrder('meta_order','public_ord', 'meta_order_direction', $this->lng['mwms_meta_order'],'meta.browser', false).'</th>
		<th style="width:100px;">-</th>
		
	</tr></thead><tbody>';

	foreach($list as $d){
		
		$html .= '<tr id="meta_cast_'.$d['id'].'" class="meta_cast" title="'.$d['title'].'">
			<td class="id_meta">'.$d['id'].'</td>
			<td>'.$d['title'].'</td>
			<td>'.$d['active'].'</td>
			<td>'.$this->lng['mwms_meta_datatype_list'][$d['system_type']].'</td>
			<td>'.$d['public_ord'].'</td>
			<td></td>
			
		</tr>';
	
	}

	return $html.'</tbody></table>';
}	

}
?>
