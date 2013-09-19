<?php
class ArticleBrowser extends MediaMix{

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
	
	$this->view_url_suffix = '&amp;sysadmin='.Registry::get('userdata/id_user'); //.'&amp;articles_search_term='.$this->search_term.''
	
	$this->filterInit();
	
	$this->search_term = $this->filterReg['articles_search_term'];
}


protected function getContentResult($mode="base"){
	
	$id_source = (int)Registry::get('mediamix_id_source_active'); 
	
	$sqlif = " WHERE id_article>0 "; 
	$sqlif .= $id_source > -1 ? " AND id_source = ".(int)$id_source." ": null;
	
	switch($mode){
		case "base":
			$q = "SELECT * FROM "._SQLPREFIX_."_mm_articles ".$sqlif." ORDER BY ".$this->filterReg['articles_order'].' '.$this->filterReg['articles_order_direction'];
			$__PRECACHE = Db::result($q);
			return Db::result($q);
		break; case "search":
			$q = "SELECT * FROM "._SQLPREFIX_."_mm_articles ".$sqlif." AND title LIKE '%".$this->search_term."%' ORDER BY ".$this->filterReg['articles_order'].' '.$this->filterReg['articles_order_direction'];
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
				<li><a href="'.$this->ajax_view_url.'articles.browser.control.php'.$this->ajax_view_url_suffix.'"><em class="ui-icon ui-icon-folder-open mwms-floating-icon"></em> '.$this->lng['mwms_article_data_list'].' '.$ls.'<span>&nbsp;</span></a></li>
			</ul>
		</div>
		
	';
  
	return $html;
}

protected function setFilterForm(){
	
	$url = html_entity_decode($this->ajax_view_url.'articles.browser.inner.php'.$this->ajax_view_url_suffix.'&amp;articles_order='.urlencode($this->filterReg['articles_order']).'&amp;articles_order_direction='.$this->filterReg['articles_order_direction'].'&amp;articles_page=1');
	
	$html = '
		<input type="hidden" id="articles_search_path" name="articles_search_path" value="'.$url.'" /> 
		<input type="text" id="articles_search" class="text" name="articles_search_term" value="'.$this->filterReg['articles_search_term'].'" /> 
		<button class="articles_search_send button" title="'.$this->lng['search_button'].'">'.$this->lng['search_button'].'</button>
		<button class="articles_search_reset button" title="'.$this->lng['reset_button'].'">'.$this->lng['reset_button'].'</button>
	';
	
	return $html;
}

public function getArticleSourceFilter($a){
	
	$html = '<select name="id_source" class="select filter_id_source" id="id_source">';
	$bData = $this->getSources();
	
	$html .= '<option value="-1" '.Validator::selected($a, -1).'>'.$this->lng['mwms_source_filter_none'].'</option>';
	$html .= '<option value="0" '.Validator::selected($a, 0).'>'.$this->lng['mwms_source_filter_manual'].'</option>';
	
	foreach($bData as $d){
		$html .= '<option value="'.$d['id_source'].'" '.Validator::selected($a, $d['id_source']).'>'.$d['title'].'</option>';
	}
	$html .= '</select>';
	
	return $html;
}

public function articleDelete($id_article){
		Db::query("DELETE FROM "._SQLPREFIX_."_mm_articles WHERE id_article = ".$id_article." ");
}


}
?>
