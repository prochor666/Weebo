<?php
class CampaignBrowser extends WeeboAdv{

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
	
	$this->view_url_suffix = '&amp;sysadmin='.Registry::get('userdata/id_user'); //.'&amp;campaign_search_term='.$this->search_term.''
	
	$this->filterInit();
	
	$this->search_term = $this->filterReg['campaign_search_term'];
}


protected function getContentResult($mode="base"){
	
	switch($mode){
		case "base":
			$q = "SELECT * FROM "._SQLPREFIX_."_adv_campaigns ORDER BY ".$this->filterReg['campaign_order'].' '.$this->filterReg['campaign_order_direction'];
			$__PRECACHE = Db::result($q);
			return Db::result($q);
		break; case "search":
			$q = "SELECT * FROM "._SQLPREFIX_."_adv_campaigns WHERE title LIKE '%".$this->search_term."%' ORDER BY ".$this->filterReg['campaign_order'].' '.$this->filterReg['campaign_order_direction'];
			return Db::result($q);
		break; default:
			return array();	
	}
}


public function showBrowserMenu(){
	
	$html = '
		<div class="mwms_tabs" id="tabs">
			<ul>
				<li><a href="'.$this->ajax_view_url.'campaign.browser.control.php'.$this->ajax_view_url_suffix.'"><em class="ui-icon ui-icon-folder-open mwms-floating-icon"></em> '.$this->lng['adv_campaign_tab'].'<span>&nbsp;</span></a></li>
			</ul>
		</div>
		
	';
  
	return $html;
}

protected function setFilterForm(){
	
	$url = html_entity_decode($this->ajax_view_url.'campaign.browser.inner.php'.$this->ajax_view_url_suffix.'&amp;campaign_order='.urlencode($this->filterReg['campaign_order']).'&amp;campaign_order_direction='.$this->filterReg['campaign_order_direction'].'&amp;campaign_page=1');
	
	$html = '
		<input type="hidden" id="campaign_search_path" name="campaign_search_path" value="'.$url.'" /> 
		<input type="text" id="campaign_search" class="text" name="content_search" value="'.$this->filterReg['campaign_search_term'].'" /> 
		<button class="campaign_search_send button" title="'.$this->lng['search_button'].'">'.$this->lng['search_button'].'</button>
		<button class="campaign_search_reset button" title="'.$this->lng['reset_button'].'">'.$this->lng['reset_button'].'</button>
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

public function campaignDelete($id_campaign){
		Db::query("DELETE FROM "._SQLPREFIX_."_adv_campaigns WHERE id_campaign = ".$id_campaign." ");
}


}
?>
