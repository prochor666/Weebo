<?php
class TeamBrowser extends WeeboNettv{

public function __construct(){
	parent::__construct();
	$this->page_default = 14;
	$this->query_limit = 0;
	$this->result_count = 0;
	$this->result_limit = 1000;
	$this->search_term_length_min = 3;
	$this->order_index = array();
	$this->default_custom_order = null;
	$this->default_custom_order_term = null;
	
	$this->view_url_suffix = '&amp;sysadmin='.Registry::get('userdata/id_user'); //.'&amp;team_search_term='.$this->search_term.''
	
	$this->filterInit();
	
	$this->search_term = $this->filterReg['team_search_term'];
}


protected function getContentResult($mode="base"){
	
	switch($mode){
		case "base":
			$q = "SELECT * FROM "._SQLPREFIX_."_nettv_team ORDER BY ".$this->filterReg['team_order'].' '.$this->filterReg['team_order_direction'];
			return Db::result($q);
		break; case "search":
			$q = "SELECT * FROM "._SQLPREFIX_."_nettv_team WHERE title LIKE '%".$this->search_term."%' ORDER BY ".$this->filterReg['team_order'].' '.$this->filterReg['team_order_direction'];
			return Db::result($q);
		break; default:
			return array();	
	}
}


public function teamBrowserMenu(){
	
	$html = '
		<div class="mwms_tabs" id="tabs">
			<ul>
				<li><a href="'.$this->ajax_view_url.'team.browser.control.php'.$this->ajax_view_url_suffix.'"><em class="ui-icon ui-icon-folder-open mwms-floating-icon"></em> '.$this->lng['tv_team_tab'].'<span>&nbsp;</span></a></li>
			</ul>
		</div>
		
	';
  
	return $html;
}

protected function setFilterForm(){
	
	$url = html_entity_decode($this->ajax_view_url.'team.browser.inner.php'.$this->ajax_view_url_suffix.'&amp;team_order='.urlencode($this->filterReg['team_order']).'&amp;team_order_direction='.$this->filterReg['team_order_direction'].'&amp;team_page=1');
	
	$html = '
		<input type="hidden" id="team_search_path" name="team_search_path" value="'.$url.'" /> 
		<input type="text" id="team_search" class="text" name="content_search" value="'.$this->filterReg['team_search_term'].'" /> 
		<button class="team_search_send button" title="'.$this->lng['search_button'].'">'.$this->lng['search_button'].'</button>
		<button class="team_search_reset button" title="'.$this->lng['reset_button'].'">'.$this->lng['reset_button'].'</button>
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

public function teamDelete($id_team){
		Db::query("DELETE FROM "._SQLPREFIX_."_nettv_team WHERE id_team = ".$id_team." ");
}


}
?>
