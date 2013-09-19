<?php
class CatBrowser extends NxMarket{

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
	
	$this->view_url_suffix = '&amp;sysadmin='.Registry::get('userdata/id_user'); //.'&amp;cats_search_term='.$this->search_term.''
	
	$this->filterInit();
	
	$this->search_term = $this->filterReg['cats_search_term'];
}


protected function getContentResult($mode="base"){

	switch($mode){
		case "base":
			$q = "SELECT * FROM "._SQLPREFIX_."_nxmarket_cats ORDER BY ".$this->filterReg['cats_order'].' '.$this->filterReg['cats_order_direction'];
			$__PRECACHE = Db::result($q);
			return Db::result($q);
		break; case "search":
			$q = "SELECT * FROM "._SQLPREFIX_."_nxmarket_cats WHERE title LIKE '%".$this->search_term."%' ORDER BY ".$this->filterReg['cats_order'].' '.$this->filterReg['cats_order_direction'];
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
				<li><a href="'.$this->ajax_view_url.'cats.browser.control.php'.$this->ajax_view_url_suffix.'"><em class="ui-icon ui-icon-folder-open mwms-floating-icon"></em> '.$this->lng['mwms_cat_data_list'].' '.$ls.'<span>&nbsp;</span></a></li>
			</ul>
		</div>
		
	';
  
	return $html;
}

protected function setFilterForm(){
	
	$url = html_entity_decode($this->ajax_view_url.'cats.browser.inner.php'.$this->ajax_view_url_suffix.'&amp;cats_order='.urlencode($this->filterReg['cats_order']).'&amp;cats_order_direction='.$this->filterReg['cats_order_direction'].'&amp;cats_page=1');
	
	$html = '
		<input type="hidden" id="cats_search_path" name="cats_search_path" value="'.$url.'" /> 
		<input type="text" id="cats_search" class="text" name="cats_search_term" value="'.$this->filterReg['cats_search_term'].'" /> 
		<button class="cats_search_send button" title="'.$this->lng['search_button'].'">'.$this->lng['search_button'].'</button>
		<button class="cats_search_reset button" title="'.$this->lng['reset_button'].'">'.$this->lng['reset_button'].'</button>
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

public function catDelete($id_cat){
	$q1 = "DELETE FROM "._SQLPREFIX_."_nxmarket_cats WHERE id_cat = ".$id_cat." ";
	Db::query($q1);
}


}
?>
