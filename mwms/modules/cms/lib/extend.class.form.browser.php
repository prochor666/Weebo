<?php
class FormBrowser extends Cms{

public function __construct(){
	parent::__construct();
	$this->page_default = 20;
	$this->query_limit = 0;
	$this->result_count = 0;
	$this->result_limit = 1000;
	$this->search_term_length_min = 3;
	$this->order_index = array();
	$this->id_form = 0;
	
	$this->view_url_suffix = '&amp;sysadmin='.Registry::get('userdata/id_user'); //.'&amp;form_search_term='.$this->search_term.''
	
	$this->filterInit();
	
	$this->search_term = $this->filterReg['form_search_term'];
}


protected function getFormResult($mode="base"){

	$activeDomainKey = Registry::get('active_domain');
	$activeDomain = $this->lng['cms_public_domains'][$activeDomainKey]['name'];
	
	$activeDomainLng = Registry::get('active_domain_lng');

	switch($mode){
		case "base":
			$q = "SELECT * FROM "._SQLPREFIX_."_cms_forms WHERE domain LIKE '".trim($activeDomain)."' ORDER BY ".$this->filterReg['form_order']." ".$this->filterReg['form_order_direction'];
			$__PRECACHE = Db::result($q);
			return Db::result($q);
		break; case "search":
			$q = "SELECT * FROM "._SQLPREFIX_."_cms_forms WHERE (title LIKE '%".$this->search_term."%' OR description LIKE '%".$this->search_term."%') AND domain LIKE '".trim($activeDomain)."' ORDER BY ".$this->filterReg['form_order']." ".$this->filterReg['form_order_direction'];
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
				<li><a href="'.$this->ajax_view_url.'forms.browser.control.php'.$this->ajax_view_url_suffix.'"><em class="ui-icon ui-icon-folder-open mwms-floating-icon"></em> '.$this->lng['mwms_form_data_list'].' '.$ls.'<span>&nbsp;</span></a></li>
			</ul>
		</div>
		
	';
  
	return $html;
}

protected function setFilterForm(){
	
	$url = html_entity_decode($this->ajax_view_url.'forms.browser.inner.php'.$this->ajax_view_url_suffix.'&amp;form_order='.urlencode($this->filterReg['form_order']).'&amp;form_order_direction='.$this->filterReg['form_order_direction'].'&amp;form_page=1');
	
	
	$html = '
		<input type="hidden" id="form_search_path" name="form_search_path" value="'.$url.'&amp;id_form='.$this->id_form.'" /> 
		<input type="text" id="form_search" class="text" name="form_search" value="'.$this->filterReg['form_search_term'].'" /> 
		<button class="form_search_send button" title="'.$this->lng['search_button'].'">'.$this->lng['search_button'].'</button>
		<button class="form_search_reset button" title="'.$this->lng['reset_button'].'">'.$this->lng['reset_button'].'</button>
	';
	
	return $html;
}

public function formDelete($id_form){
	
	Db::query("DELETE FROM "._SQLPREFIX_."_cms_forms WHERE id_form = '".$id_form."' ");
	
}

}
?>
