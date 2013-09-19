<?php
class AnswerBrowser extends WeeboInqua{

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
	
	$this->view_url_suffix = '&amp;sysadmin='.Registry::get('userdata/id_user'); //.'&amp;answer_search_term='.$this->search_term.''
	
	$this->filterInit();
	
	$this->search_term = $this->filterReg['answer_search_term'];
}


protected function getContentResult($mode="base"){
	
	$id_inquiry = (int)Registry::get('inqua_id_inquiry_active'); 
	
	$sqlif = " WHERE id_answer>0 "; 
	$sqlif .= $id_inquiry > 0 ? " AND id_inquiry = ".(int)$id_inquiry." ": null;
	
	switch($mode){
		case "base":
			$q = "SELECT * FROM "._SQLPREFIX_."_inqua_answers ".$sqlif." ORDER BY ".$this->filterReg['answer_order'].' '.$this->filterReg['answer_order_direction'];
			return Db::result($q);
		break; case "search":
			$q = "SELECT * FROM "._SQLPREFIX_."_inqua_answers ".$sqlif." AND title LIKE '%".$this->search_term."%' ORDER BY ".$this->filterReg['answer_order'].' '.$this->filterReg['answer_order_direction'];
			return Db::result($q);
		break; default:
			return array();	
	}
	
}


public function showBrowserMenu(){
	
	$html = '
		<div class="mwms_tabs" id="tabs">
			<ul>
				<li><a href="'.$this->ajax_view_url.'answer.browser.control.php'.$this->ajax_view_url_suffix.'"><em class="ui-icon ui-icon-folder-open mwms-floating-icon"></em> '.$this->lng['inqua_answer_tab'].'<span>&nbsp;</span></a></li>
			</ul>
		</div>
		
	';
  
	return $html;
}

protected function setFilterForm(){
	
	$url = html_entity_decode($this->ajax_view_url.'answer.browser.inner.php'.$this->ajax_view_url_suffix.'&amp;answer_order='.urlencode($this->filterReg['answer_order']).'&amp;answer_order_direction='.$this->filterReg['answer_order_direction'].'&amp;answer_page=1');
	
	$html = '
		<input type="hidden" id="answer_search_path" name="answer_search_path" value="'.$url.'" /> 
		<input type="text" id="answer_search" class="text" name="content_search" value="'.$this->filterReg['answer_search_term'].'" /> 
		<button class="answer_search_send button" title="'.$this->lng['search_button'].'">'.$this->lng['search_button'].'</button>
		<button class="answer_search_reset button" title="'.$this->lng['reset_button'].'">'.$this->lng['reset_button'].'</button>
	';
	
	return $html;
}

public function getInquiryListFilter($a){
	
	$html = '<select name="id_inquiry" class="select filter_id_inquiry" id="id_inquiry">';
	$bData = $this->getInquiries();
	
	$html .= '<option value="0" '.Validator::selected($a, 0).'>'.$this->lng['inqua_answer_filter'].'</option>';
	
	foreach($bData as $d){
		$html .= '<option value="'.$d['id_inquiry'].'" '.Validator::selected($a, $d['id_inquiry']).'>'.$d['title'].'</option>';
	}
	$html .= '</select>';
	
	return $html;
}

public function answerDelete($id_answer){
		Db::query("DELETE FROM "._SQLPREFIX_."_inqua_answers WHERE id_answer = ".$id_answer." ");
}


}
?>
