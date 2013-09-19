<?php
class ContentBrowser extends Cms{

public $linkMap;

public function __construct(){
	parent::__construct();
	$this->page_default = 20;
	$this->query_limit = 0;
	$this->result_count = 0;
	$this->result_limit = 1000;
	$this->search_term_length_min = 3;
	$this->order_index = array();
	$this->id_link = 0;
	$this->default_custom_order = null;
	$this->default_custom_order_term = null;
	
	$this->view_url_suffix = '&amp;sysadmin='.Registry::get('userdata/id_user'); //.'&amp;content_search_term='.$this->search_term.''
	
	$this->filterInit();
	
	$this->search_term = $this->filterReg['content_search_term'];
}


protected function getContentResult($mode="base"){
	
	$lm = $this->getLinkData($this->id_link);

	$this->linkMap = $lm['textmap'];
	
	$linkData = $this->getLinkData($this->id_link);
	$this->default_custom_order_term = $linkData['default_order'];
	$myOrder =  $this->filterReg['content_order'] != 'id_custom' ? $this->filterReg['content_order']." ".$this->filterReg['content_order_direction']: $this->default_custom_order_term;

	switch($mode){
		case "base":
			$q = "SELECT * FROM "._SQLPREFIX_."_cms_content WHERE id_link = '".$this->id_link."' ORDER BY ".$myOrder;
			$__PRECACHE = Db::result($q);
			return Db::result($q);
		break; case "search":
			$q = "SELECT * FROM "._SQLPREFIX_."_cms_content WHERE  id_link = '".$this->id_link."' AND (title LIKE '%".$this->search_term."%' OR keywords LIKE '%".$this->search_term."%') ORDER BY ".$myOrder;
			return Db::result($q);
		break; default:
			return array();	
	}
}


public function showBrowserMenu(){
	
	$ls = null;
	
	if($this->id_link>0){
		$l = $this->getLinkData($this->id_link);
		$ls = $l['title'];
	}
	
	$html = '
		<div class="mwms_tabs" id="tabs">
			<ul>
				<li><a href="'.$this->ajax_view_url.'links.detail.process.php'.$this->ajax_view_url_suffix.'&amp;id_link='.$this->id_link.'"><em class="ui-icon ui-icon-document mwms-floating-icon"></em> '.$this->lng['mwms_link_edit'].': '.$ls.'<span>&nbsp;</span></a></li>
				<li><a href="'.$this->ajax_view_url.'content.browser.php'.$this->ajax_view_url_suffix.'&amp;id_link='.$this->id_link.'"><em class="ui-icon ui-icon-folder-open mwms-floating-icon"></em> '.$this->lng['mwms_content_data_list'].' '.$ls.'<span>&nbsp;</span></a></li>
			</ul>
		</div>
		
	';
  
	return $html;
}

protected function setFilterForm(){
	
	$url = html_entity_decode($this->ajax_view_url.'content.browser.inner.php'.$this->ajax_view_url_suffix.'&amp;content_order='.urlencode($this->filterReg['content_order']).'&amp;content_order_direction='.$this->filterReg['content_order_direction'].'&amp;content_page=1');
	
	
	$html = '
		<input type="hidden" id="content_search_path" name="content_search_path" value="'.$url.'&amp;id_link='.$this->id_link.'" /> 
		<input type="text" id="content_search" class="text" name="content_search" value="'.$this->filterReg['content_search_term'].'" /> 
		<button class="content_search_send button" title="'.$this->lng['search_button'].'">'.$this->lng['search_button'].'</button>
		<button class="content_search_reset button" title="'.$this->lng['reset_button'].'">'.$this->lng['reset_button'].'</button>
	';
	
	return $html;
}

/* Data views */
public function actionSelect(){
	$html = null;
	
	if(count($this->lng['cms_content_action'])>0){
		$html = '<div id="action-set">';
		
		foreach($this->lng['cms_content_action'] as $key => $label){
			$ch = $key == 'assign' ? ' checked="checked"': null; 
			$html .= '<label for="action-'.$key.'">'.$label.'</label> <input type="radio" value="'.$key.'" name="user-action" class="user-action" id="action-'.$key.'" '.$ch.' />';
		}
		
		$html .= '</div>';
	}
	
	return $html;
}

public function contentDelete($links = array()){
	
	if(is_array($links) && count($links)>0){
		Db::query("DELETE FROM "._SQLPREFIX_."_cms_content WHERE id_content IN(".implode(',', $links).") ");
		Db::query("DELETE FROM "._SQLPREFIX_."_cms_content_links WHERE id_content IN(".implode(',', $links).") ");
	}
	
}

protected function getDefaultLinkOrder(){
	$d = $this->getLinkData($this->id_link);
	return $d['default_order'];
}

}
?>
