<?php
class ChannelBrowser extends NxMarket{

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
	
	$this->view_url_suffix = '&amp;sysadmin='.Registry::get('userdata/id_user'); //.'&amp;channels_search_term='.$this->search_term.''
	
	$this->filterInit();
	
	$this->search_term = $this->filterReg['channels_search_term'];
}


protected function getContentResult($mode="base"){
	
	$id_source = (int)Registry::get('nxmarket_id_source_active'); 
	
	$sqlif = " WHERE id_channel>0 "; 
	$sqlif .= $id_source > -1 ? " AND id_source = ".(int)$id_source." ": null;
	
	switch($mode){
		case "base":
			$q = "SELECT * FROM "._SQLPREFIX_."_nxmarket_channels ".$sqlif." ORDER BY ".$this->filterReg['channels_order'].' '.$this->filterReg['channels_order_direction'];
			$__PRECACHE = Db::result($q);
			return Db::result($q);
		break; case "search":
			$q = "SELECT * FROM "._SQLPREFIX_."_nxmarket_channels ".$sqlif." AND title LIKE '%".$this->search_term."%' ORDER BY ".$this->filterReg['channels_order'].' '.$this->filterReg['channels_order_direction'];
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
				<li><a href="'.$this->ajax_view_url.'channels.browser.control.php'.$this->ajax_view_url_suffix.'"><em class="ui-icon ui-icon-folder-open mwms-floating-icon"></em> '.$this->lng['mwms_channel_data_list'].' '.$ls.'<span>&nbsp;</span></a></li>
			</ul>
		</div>
		
	';
  
	return $html;
}

protected function setFilterForm(){
	
	$url = html_entity_decode($this->ajax_view_url.'channels.browser.inner.php'.$this->ajax_view_url_suffix.'&amp;channels_order='.urlencode($this->filterReg['channels_order']).'&amp;channels_order_direction='.$this->filterReg['channels_order_direction'].'&amp;channels_page=1');
	
	$html = '
		<input type="hidden" id="channels_search_path" name="channels_search_path" value="'.$url.'" /> 
		<input type="text" id="channels_search" class="text" name="channels_search_term" value="'.$this->filterReg['channels_search_term'].'" /> 
		<button class="channels_search_send button" title="'.$this->lng['search_button'].'">'.$this->lng['search_button'].'</button>
		<button class="channels_search_reset button" title="'.$this->lng['reset_button'].'">'.$this->lng['reset_button'].'</button>
	';
	
	return $html;
}


public function channelDelete($id_channel){
	Db::query("DELETE FROM "._SQLPREFIX_."_nxmarket_channels WHERE id_channel = ".$id_channel." ");
}


}
?>
