<?php
class GroupBrowser extends Users{

public function __construct(){
	parent::__construct();
	$this->page_default = 20;
	$this->query_limit = 0;
	$this->result_count = 0;
	$this->result_limit = 1000;
	$this->search_term_length_min = 3;
	$this->order_index = array();

	$this->view_url_suffix = '&amp;sysadmin='.Registry::get('userdata/id'); //.'&amp;users_search_term='.$this->search_term.''
	
	$this->filterInit();
	
	$this->search_term = null;
}

protected function getGroupResult(){
	$q = "SELECT * FROM "._SQLPREFIX_."_user_groups ORDER BY ".$this->filterReg['groups_order']." ".$this->filterReg['groups_order_direction'];
	return Db::result($q);
}

protected function getGroupData($id){
	$q = "SELECT * FROM "._SQLPREFIX_."_user_groups WHERE id_group = '".$id."' ";
	$qq = Db::result($q);	
	return $qq[0];
}


}
?>
