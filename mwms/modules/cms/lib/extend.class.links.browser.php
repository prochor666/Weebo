<?php
class LinksBrowser extends Cms{

public function __construct(){
	parent::__construct();
	$this->page_default = 20;
	$this->query_limit = 0;
	$this->result_count = 0;
	$this->result_limit = 1000;
	$this->search_term_length_min = 3;
	$this->order_index = array();

	$this->view_url_suffix = '&amp;sysadmin='.Registry::get('userdata/id_user'); //.'&amp;links_search_term='.$this->search_term.''
	
	$this->filterInit();
	
	$this->search_term = $this->filterReg['links_search_term'];
}


protected function getLinkResult($id_link = 0, $mode="base"){
	
	$activeDomainKey = Registry::get('active_domain');
	$activeDomain = $this->lng['cms_public_domains'][$activeDomainKey]['name'];
	
	$activeDomainLng = Registry::get('active_domain_lng');
	
	
	
	switch($mode){
		case "base":
		
			$q = "SELECT * FROM "._SQLPREFIX_."_cms_links WHERE id_sub = '".(int)$id_link."' AND domain LIKE '".trim($activeDomain)."' AND lng LIKE '".trim($activeDomainLng)."' ORDER BY ".$this->filterReg['links_order']." ".$this->filterReg['links_order_direction'];
			
			return Db::result($q);
		
		break; case "search":
			
			$q = "SELECT * FROM "._SQLPREFIX_."_cms_links WHERE title LIKE '%".$this->search_term."%' OR link_title LIKE '%".$this->search_term."%' OR keywords LIKE '%".$this->search_term."%' ORDER BY ".$this->filterReg['links_order']." ".$this->filterReg['links_order_direction'];
			
			return Db::result($q);
				
		break; default:
			
			return array();	
	}
	
}

protected function setFilterForm(){
	
	$url = html_entity_decode($this->ajax_view_url.'links.browser.control.php'.$this->ajax_view_url_suffix.'&amp;links_order='.$this->filterReg['links_order'].'&amp;links_order_direction='.$this->filterReg['links_order_direction'].'&amp;links_page=1');
	
	$html = '
		<input type="hidden" id="links_search_path" name="links_search_path" value="'.$url.'" /> 
		<input type="text" id="links_search" class="text" name="links_search" value="'.$this->filterReg['links_search_term'].'" /> 
		<button class="links_search_send button" title="'.$this->lng['search_button'].'">'.$this->lng['search_button'].'</button>
		<button class="links_search_reset button" title="'.$this->lng['reset_button'].'">'.$this->lng['reset_button'].'</button>
	';
	
	return $html;
}

/* Data views */
public function actionSelect(){
	$html = null;
	
	if(count(Lng::get('users/mwms_user_action'))>0){
		$html = '<div id="action-set">';
		
		foreach(Lng::get('users/mwms_user_action') as $key => $label){
			$ch = $key == 'assign' ? ' checked="checked"': null; 
			$html .= '<label for="action-'.$key.'">'.$label.'</label> <input type="radio" value="'.$key.'" name="user-action" class="user-action" id="action-'.$key.'" '.$ch.' />';
		}
		
		$html .= '</div>';
	}
	
	return $html;
}


public function linksDelete($links = array()){
	
	$q = "SELECT * FROM "._SQLPREFIX_."_cms_links WHERE id_link IN(".implode(",", $links).") ";
	$qq = Db::result($q);
	$ids = array();
	
	foreach($qq as $d){
		
		$ids[] = $d['id_link'];
		$chData = $this->hasChild($d['id_link']);
		if(count($chData)>0){
			$ids = array_merge($ids, $chData);
		}
	}
	
	if(in_array(Registry::get('cms_active_link'), $ids)){
		Registry::set('cms_active_link', null);
	}
	
	if(is_array($links) && count($links)>0){
		$q1 = "DELETE FROM "._SQLPREFIX_."_cms_links WHERE id_link IN(".implode(',', $ids).") ";
		$q2 = "DELETE FROM "._SQLPREFIX_."_cms_content WHERE id_link IN(".implode(',', $ids).") ";
		Db::query($q1);
		Db::query($q2); 
	}
}

protected function hasChild($id_link){
	$q = "SELECT * FROM "._SQLPREFIX_."_cms_links WHERE id_sub = '".$id_link."' ";
	$qq = Db::result($q);
	
	$ids = array();
	
	foreach($qq as $d){
		
		$ids[] = $d['id_link'];
		$chData = $this->hasChild($d['id_link']);
		
		if(count($chData)>0){
			$ids = array_merge($ids, $chData);
		}
	}

	return $ids;
}

public function groupSelect(){
	
	$q = "SELECT * FROM "._SQLPREFIX_."_user_groups ORDER BY title, id_group";
	$qq = Db::result($q);	
	$html = null;
	
	if(count($qq)>0){
		$html = '<div id="group-set">';
		
		foreach($qq as $d){
			$html .= '<label for="group-'.$d['id_group'].'"><input type="checkbox" value="'.$d['id_group'].'" name="id_group[]" id="group-'.$d['id_group'].'" /> '.$d['title'].'</label>';
		}
		
		$html .= '</div>';
	}
	
	return $html;
}

public function userSelect(){
	$html = null;
	
	if(count($this->filterReg["dropbox"])>0){
		$html = '<div id="user-set">';
		
		foreach($this->filterReg["dropbox"] as $key => $id){
			$user = $this->getUserData($id);
			$html .= '<label for="user-'.$id.'"><input type="checkbox" value="'.$id.'" name="id_user[]" id="user-'.$id.'" checked="checked" /> '.$user['username'].'</label>';
		}
		
		$html .= '</div>';
	}
	
	return $html;
}

}
?>
