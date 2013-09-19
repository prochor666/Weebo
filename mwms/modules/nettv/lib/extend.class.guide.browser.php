<?php
class GuideBrowser extends WeeboNettv{

public function __construct(){
	parent::__construct();
	$this->page_default = 200;
	$this->query_limit = 0;
	$this->result_count = 0;
	$this->result_limit = 1000;
	$this->search_term_length_min = 3;
	$this->order_index = array();
	$this->default_custom_order = null;
	$this->default_custom_order_term = null;
	
	$this->view_url_suffix = '&amp;sysadmin='.Registry::get('userdata/id_user'); //.'&amp;guide_search_term='.$this->search_term.''
	
	$this->filterInit();
	
	$this->search_term = $this->filterReg['guide_search_term'];
}


protected function getContentResult($mode="base"){
	
	switch($mode){
		case "base":
			$q = "SELECT * FROM "._SQLPREFIX_."_nettv_guide ORDER BY ".$this->filterReg['guide_order'].' '.$this->filterReg['guide_order_direction'];
			return Db::result($q);
		break; case "search":
			$q = "SELECT * FROM "._SQLPREFIX_."_nettv_guide WHERE title LIKE '%".$this->search_term."%' ORDER BY ".$this->filterReg['guide_order'].' '.$this->filterReg['guide_order_direction'];
			return Db::result($q);
		break; default:
			return array();	
	}
}


public function showBrowserMenu(){
	
	$html = '
		<div class="mwms_tabs" id="tabs">
			<ul>
				<li><a href="'.$this->ajax_view_url.'guide.browser.control.php'.$this->ajax_view_url_suffix.'"><em class="ui-icon ui-icon-folder-open mwms-floating-icon"></em> '.$this->lng['tv_guide_tab'].'<span>&nbsp;</span></a></li>
			</ul>
		</div>
		
	';
  
	return $html;
}

protected function setFilterForm(){
	
	$url = html_entity_decode($this->ajax_view_url.'guide.browser.inner.php'.$this->ajax_view_url_suffix.'&amp;guide_order='.urlencode($this->filterReg['guide_order']).'&amp;guide_order_direction='.$this->filterReg['guide_order_direction'].'&amp;guide_page=1');
	
	$html = '
		<input type="hidden" id="guide_search_path" name="guide_search_path" value="'.$url.'" /> 
		<input type="text" id="guide_search" class="text" name="content_search" value="'.$this->filterReg['guide_search_term'].'" /> 
		<button class="guide_search_send button" title="'.$this->lng['search_button'].'">'.$this->lng['search_button'].'</button>
		<button class="guide_search_reset button" title="'.$this->lng['reset_button'].'">'.$this->lng['reset_button'].'</button>
	';
	
	return $html;
}


public function guideDelete($id_guide){
	Db::query("DELETE FROM "._SQLPREFIX_."_nettv_guide WHERE id_guide = ".$id_guide." ");
}


protected function checkShow($str){
	
	$find = trim(htmlspecialchars_decode($str));
	$idShow = 0;
	$x = explode(' ', $str);
	
	$q = "SELECT * FROM "._SQLPREFIX_."_nettv_shows WHERE title LIKE '".Db::escapeField($find)."%' LIMIT 1";
	$qq = Db::result($q);
	
	$idShow = count($qq) == 1 ? $qq[0]['id_show'] : 0;
	
	if($idShow == 0 && count($x)>1){
		
		$find = $x[0].' '.$x[1];
		$q = "SELECT * FROM "._SQLPREFIX_."_nettv_shows WHERE title LIKE '".Db::escapeField($find)."%' LIMIT 1";
		$qq = Db::result($q);
		
		$idShow = count($qq) == 1 ? $qq[0]['id_show'] : 0;
		
		if($idShow == 0 && count($x)>2){
			$x = explode(' ', $str);
			$find = $x[0].' '.$x[1].' '.$x[2];
			$q = "SELECT * FROM "._SQLPREFIX_."_nettv_shows WHERE title LIKE '".Db::escapeField($find)."%' LIMIT 1";
			$qq = Db::result($q);
			
			$idShow = count($qq) == 1 ? $qq[0]['id_show'] : 0;
			
			if($idShow == 0 && count($x)>3){
				$x = explode(' ', $str);
				$find = $x[0].' '.$x[1].' '.$x[2];
				$q = "SELECT * FROM "._SQLPREFIX_."_nettv_shows WHERE title LIKE '".Db::escapeField($find)."%' LIMIT 1";
				$qq = Db::result($q);
				
				$idShow = count($qq) == 1 ? $qq[0]['id_show'] : 0;
			}
		}
		
	}
	
	return $idShow;
}


protected function recordExists($time){
	$q = "SELECT * FROM "._SQLPREFIX_."_nettv_guide WHERE date_from = '".$time."'";
	$qq = Db::result($q);
	return count($qq) > 0 ? true: false;
}


}
?>
