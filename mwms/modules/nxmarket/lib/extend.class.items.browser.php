<?php
class ItemBrowser extends NxMarket{

public function __construct(){
	parent::__construct();
	$this->page_default = 20;
	$this->query_limit = 0;
	$this->result_count = 0;
	$this->result_limit = 1000;
	$this->search_term_length_min = 3;
	$this->order_index = array();
	$this->default_custom_order = null;
	$this->default_custom_order_term = null;
	
	$this->view_url_suffix = '&amp;sysadmin='.Registry::get('userdata/id_user'); //.'&amp;items_search_term='.$this->search_term.''
	
	$this->filterInit();
	
	$this->search_term = $this->filterReg['items_search_term'];
}


protected function getContentResult($mode="base"){

	switch($mode){
		case "base":
			$q = "SELECT * FROM "._SQLPREFIX_."_nxmarket_items ORDER BY ".$this->filterReg['items_order'].' '.$this->filterReg['items_order_direction'];
			$__PRECACHE = Db::result($q);
			return Db::result($q);
		break; case "search":
			$q = "SELECT * FROM "._SQLPREFIX_."_nxmarket_items WHERE title LIKE '%".$this->search_term."%' ORDER BY ".$this->filterReg['items_order'].' '.$this->filterReg['items_order_direction'];
			return Db::result($q);
		break; default:
			return array();	
	}
}


public function showBrowserMenu(){
	
	$ls = null;
	
	$html = '
		<div class="mwms_tabs" id="tabs">
			<ul>
				<li><a href="'.$this->ajax_view_url.'items.browser.control.php'.$this->ajax_view_url_suffix.'"><em class="ui-icon ui-icon-folder-open mwms-floating-icon"></em> '.$this->lng['items_data_list'].' '.$ls.'<span>&nbsp;</span></a></li>
			</ul>
		</div>
	';
  
	return $html;
}

protected function setFilterForm(){
	
	$url = html_entity_decode($this->ajax_view_url.'items.browser.inner.php'.$this->ajax_view_url_suffix.'&amp;items_order='.urlencode(trim($this->filterReg['items_order'])).'&amp;items_order_direction='.$this->filterReg['items_order_direction'].'&amp;items_page=1');
	
	$html = '
		<input type="hidden" id="items_search_path" name="items_search_path" value="'.$url.'" /> 
		<input type="text" id="items_search" class="text" name="items_search_term" value="'.$this->filterReg['items_search_term'].'" /> 
		<button class="items_search_send button" title="'.$this->lng['search_button'].'">'.$this->lng['search_button'].'</button>
		<button class="items_search_reset button" title="'.$this->lng['reset_button'].'">'.$this->lng['reset_button'].'</button>
	';
	
	return $html;
}


public function itemDelete($id_item){
	$q1 = "DELETE FROM "._SQLPREFIX_."_nxmarket_items WHERE id_item = ".$id_item." ";
	Db::query($q1);
}


}
?>
