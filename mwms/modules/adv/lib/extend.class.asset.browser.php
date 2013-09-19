<?php
class AssetBrowser extends WeeboAdv{

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
	
	$this->view_url_suffix = '&amp;sysadmin='.Registry::get('userdata/id_user'); //.'&amp;asset_search_term='.$this->search_term.''
	
	$this->filterInit();
	
	$this->search_term = $this->filterReg['asset_search_term'];
}


protected function getContentResult($mode="base"){
	
	switch($mode){
		case "base":
			$q = "SELECT * FROM "._SQLPREFIX_."_adv_assets ORDER BY ".$this->filterReg['asset_order'].' '.$this->filterReg['asset_order_direction'];
			$__PRECACHE = Db::result($q);
			return Db::result($q);
		break; case "search":
			$q = "SELECT * FROM "._SQLPREFIX_."_adv_assets WHERE title LIKE '%".$this->search_term."%' ORDER BY ".$this->filterReg['asset_order'].' '.$this->filterReg['asset_order_direction'];
			return Db::result($q);
		break; default:
			return array();	
	}
}


public function showBrowserMenu(){
	
	$html = '
		<div class="mwms_tabs" id="tabs">
			<ul>
				<li><a href="'.$this->ajax_view_url.'asset.browser.control.php'.$this->ajax_view_url_suffix.'"><em class="ui-icon ui-icon-folder-open mwms-floating-icon"></em> '.$this->lng['adv_asset_tab'].'<span>&nbsp;</span></a></li>
			</ul>
		</div>
		
	';
  
	return $html;
}

protected function setFilterForm(){
	
	$url = html_entity_decode($this->ajax_view_url.'asset.browser.inner.php'.$this->ajax_view_url_suffix.'&amp;asset_order='.urlencode($this->filterReg['asset_order']).'&amp;asset_order_direction='.$this->filterReg['asset_order_direction'].'&amp;asset_page=1');
	
	$html = '
		<input type="hidden" id="asset_search_path" name="asset_search_path" value="'.$url.'" /> 
		<input type="text" id="asset_search" class="text" name="content_search" value="'.$this->filterReg['asset_search_term'].'" /> 
		<button class="asset_search_send button" title="'.$this->lng['search_button'].'">'.$this->lng['search_button'].'</button>
		<button class="asset_search_reset button" title="'.$this->lng['reset_button'].'">'.$this->lng['reset_button'].'</button>
	';
	
	return $html;
}


public function assetDelete($id_asset){
		Db::query("DELETE FROM "._SQLPREFIX_."_adv_assets WHERE id_asset = ".$id_asset." ");
}


}
?>
