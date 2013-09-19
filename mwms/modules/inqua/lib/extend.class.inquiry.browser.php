<?php
class InquiryBrowser extends WeeboInqua{

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
	
	$this->view_url_suffix = '&amp;sysadmin='.Registry::get('userdata/id_user'); //.'&amp;inquiry_search_term='.$this->search_term.''
	
	$this->filterInit();
	
	$this->search_term = $this->filterReg['inquiry_search_term'];
}


protected function getContentResult($mode="base"){
	
	switch($mode){
		case "base":
			$q = "SELECT * FROM "._SQLPREFIX_."_inqua_inquiries ORDER BY ".$this->filterReg['inquiry_order'].' '.$this->filterReg['inquiry_order_direction'];
			$__PRECACHE = Db::result($q);
			return Db::result($q);
		break; case "search":
			$q = "SELECT * FROM "._SQLPREFIX_."_inqua_inquiries WHERE title LIKE '%".$this->search_term."%' ORDER BY ".$this->filterReg['inquiry_order'].' '.$this->filterReg['inquiry_order_direction'];
			return Db::result($q);
		break; default:
			return array();	
	}
}


public function showBrowserMenu(){
	
	$html = '
		<div class="mwms_tabs" id="tabs">
			<ul>
				<li><a href="'.$this->ajax_view_url.'inquiry.browser.control.php'.$this->ajax_view_url_suffix.'"><em class="ui-icon ui-icon-folder-open mwms-floating-icon"></em> '.$this->lng['inqua_inquiry_tab'].'<span>&nbsp;</span></a></li>
			</ul>
		</div>
		
	';
  
	return $html;
}

protected function setFilterForm(){
	
	$url = html_entity_decode($this->ajax_view_url.'inquiry.browser.inner.php'.$this->ajax_view_url_suffix.'&amp;inquiry_order='.urlencode($this->filterReg['inquiry_order']).'&amp;inquiry_order_direction='.$this->filterReg['inquiry_order_direction'].'&amp;inquiry_page=1');
	
	$html = '
		<input type="hidden" id="inquiry_search_path" name="inquiry_search_path" value="'.$url.'" /> 
		<input type="text" id="inquiry_search" class="text" name="content_search" value="'.$this->filterReg['inquiry_search_term'].'" /> 
		<button class="inquiry_search_send button" title="'.$this->lng['search_button'].'">'.$this->lng['search_button'].'</button>
		<button class="inquiry_search_reset button" title="'.$this->lng['reset_button'].'">'.$this->lng['reset_button'].'</button>
	';
	
	return $html;
}

/* Data views */
public function actionSelect(){
	$html = null;
	
	if(count($this->lng['mediamix_content_action'])>0){
		$html = '<div id="action-set">';
		
		foreach($this->lng['mediamix_content_action'] as $key => $label){
			$ch = $key == 'assign' ? ' checked="checked"': null; 
			$html .= '<label for="action-'.$key.'">'.$label.'</label> <input type="radio" value="'.$key.'" name="user-action" class="user-action" id="action-'.$key.'" '.$ch.' />';
		}
		
		$html .= '</div>';
	}
	
	return $html;
}

public function inquiryDelete($id_inquiry){
	Db::query("DELETE FROM "._SQLPREFIX_."_inqua_inquiries WHERE id_inquiry = ".$id_inquiry." ");
	Db::query("DELETE FROM "._SQLPREFIX_."_inqua_answers WHERE id_inquiry = ".$id_inquiry." ");
}


}
?>
