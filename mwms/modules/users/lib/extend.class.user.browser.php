<?php
class UserBrowser extends Users{

public function __construct(){
	parent::__construct();
	$this->page_default = 20;
	$this->query_limit = 0;
	$this->result_count = 0;
	$this->result_limit = 1000;
	$this->search_term_length_min = 3;
	$this->order_index = array();
	$this->view_url_suffix = '&amp;sysadmin='.Registry::get('userdata/id_user'); //.'&amp;users_search_term='.$this->search_term.''
	$this->filterInit();
	$this->search_term = $this->filterReg['users_search_term'];
}

protected function getUserResult($mode="base"){
	
	switch($mode){
		case "base":
			$q = "SELECT * FROM "._SQLPREFIX_."_users ";
			$q .= Login::is_site_root() && Login::is_site_admin() ? null: " WHERE id_user = ".Registry::get('userdata/id_user');
			$q .= Login::is_site_root() && Login::is_site_admin() ? " ORDER BY ".$this->filterReg['users_order']." ".$this->filterReg['users_order_direction']: null;
			return Db::result($q);
		break; case "search":
			$q = "SELECT * FROM "._SQLPREFIX_."_users WHERE mail LIKE '%".$this->search_term."%' OR firstname LIKE '%".$this->search_term."%' OR lastname LIKE '%".$this->search_term."%' ORDER BY ".$this->filterReg['users_order']." ".$this->filterReg['users_order_direction'];
			return Db::result($q);
		break; default:
			return array();	
	}
}

public function showBrowserMenu(){
	$html = '
		<div class="mwms_tabs" id="tabs">
			<ul>
				<li><a href="'.$this->ajax_view_url.'user.browser.control.base.php'.$this->ajax_view_url_suffix.'"><em class="ui-icon ui-icon-document mwms-floating-icon"></em> '.$this->lng['mwms_user_data_list'].'<span>&nbsp;</span></a></li>
			</ul>
		</div>
		
	';
  
	return $html;
}

protected function setFilterForm(){
	
	$url = html_entity_decode($this->ajax_view_url.'user.browser.control.php'.$this->ajax_view_url_suffix.'&amp;users_order='.$this->filterReg['users_order'].'&amp;users_order_direction='.$this->filterReg['users_order_direction'].'&amp;users_page=1');
	
	$html = '
		<input type="hidden" id="users_search_path" name="users_search_path" value="'.$url.'" /> 
		<input type="text" id="users_search" class="text" name="users_search" value="'.$this->filterReg['users_search_term'].'" /> 
		<button class="users_search_send button" title="'.$this->lng['search_button'].'">'.$this->lng['search_button'].'</button>
		<button class="users_search_reset button" title="'.$this->lng['reset_button'].'">'.$this->lng['reset_button'].'</button>
	';

	return $html;
}

public function getUserData($id){
	$q = "SELECT * FROM "._SQLPREFIX_."_users WHERE id_user = '".$id."' ";
	$qq = Db::result($q);	
	return count($qq)>0 ? $qq[0]: array();
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
	
	if(count($this->filterReg["users_dropbox"])>0){
		$html = '<div id="user-set">';
		foreach($this->filterReg["users_dropbox"] as $key => $id){
			$user = $this->getUserData($id);
			$html .= '<label for="user-'.$id.'"><input type="checkbox" value="'.$id.'" name="id_user[]" id="user-'.$id.'" checked="checked" /> '.$user['username'].'</label>';
		}
		$html .= '</div>';
	}
	return $html;
}

}
?>
