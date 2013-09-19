<?php
class ShowItemBrowser extends WeeboNettv{

public $hiddenShow;

public function __construct(){
	parent::__construct();
	$this->page_default = 10;
	$this->query_limit = 0;
	$this->result_count = 0;
	$this->result_limit = 1000;
	$this->search_term_length_min = 3;
	$this->order_index = array();
	$this->default_custom_order = null;
	$this->default_custom_order_term = null;
	
	$this->hiddenShow = 0;
	
	$this->view_url_suffix = '&amp;sysadmin='.Registry::get('userdata/id_user'); //.'&amp;show_items_search_term='.$this->search_term.''
	
	$this->filterInit();
	
	$this->search_term = $this->filterReg['show_items_search_term'];
}

protected function getContentResult($mode="base"){
	
	$id_public = (int)Registry::get('nettv_state_view'); 
	$id_show = (int)Registry::get('nettv_id_show_active'); 
	
	$sqlif = " WHERE id_item>0 "; //$this->hiddenShow == 1 ? " WHERE id_item>0 ": " WHERE id_item>0 AND id_hidden = 0 ";
	$sqlif .= $id_public == 1 ? " AND id_public  = 1 ": "";
	$sqlif .= $id_show > 0 ? " AND id_show = ".$id_show." ": "";
	
	switch($mode){
		case "base":
			$q = "SELECT * FROM "._SQLPREFIX_."_nettv_show_items ".$sqlif." ORDER BY ".$this->filterReg['show_items_order'].' '.$this->filterReg['show_items_order_direction'];
			return Db::result($q);
		break; case "search":
			$q = "SELECT * FROM "._SQLPREFIX_."_nettv_show_items ".$sqlif." AND title LIKE '%".$this->search_term."%' ORDER BY ".$this->filterReg['show_items_order'].' '.$this->filterReg['show_items_order_direction'];
			return Db::result($q);
		break; default:
			return array();	
	}
}

public function showStateFilter(){
	
	$html = '
		<label for="nettv_state_view"><input type="checkbox" value="1" name="nettv_state_view" id="nettv_state_view" '.Validator::checked(Registry::get('nettv_state_view'), 1).' /> '.$this->lng['tv_show_item_hide_unpublished'].'</label> 
	';
	
	return $html;
}

public function getShowListFilter($a){
	
	$html = '<select name="id_show" class="select filter_id_show" id="id_show">';
	$bData = $this->getShows();
	
	$html .= '<option value="0" '.Validator::selected($a, 0).'>'.$this->lng['tv_show_load_default'].'</option>';
	
	foreach($bData as $d){
		$html .= '<option value="'.$d['id_show'].'" '.Validator::selected($a, $d['id_show']).'>'.$d['title'].'</option>';
	}
	$html .= '</select>';
	
	return $html;
}

public function showBrowserMenu(){
	
	$html = '
		<div class="mwms_tabs" id="tabs">
			<ul>
				<li><a href="'.$this->ajax_view_url.'show.item.browser.control.php'.$this->ajax_view_url_suffix.'"><em class="ui-icon ui-icon-folder-open mwms-floating-icon"></em> '.$this->lng['tv_show_items_tab'].'<span>&nbsp;</span></a></li>
			</ul>
		</div>
		
	';
  
	return $html;
}

protected function setFilterForm(){
	
	$url = html_entity_decode($this->ajax_view_url.'show.item.browser.inner.php'.$this->ajax_view_url_suffix.'&amp;show_items_order='.urlencode($this->filterReg['show_items_order']).'&amp;show_items_order_direction='.$this->filterReg['show_items_order_direction'].'&amp;show_items_page=1');
	
	$html = '
		<input type="hidden" id="show_items_search_path" name="show_items_search_path" value="'.$url.'" /> 
		<input type="text" id="show_items_search" class="text" name="content_search" value="'.$this->filterReg['show_items_search_term'].'" /> 
		<button class="show_items_search_send button" title="'.$this->lng['search_button'].'">'.$this->lng['search_button'].'</button>
		<button class="show_items_search_reset button" title="'.$this->lng['reset_button'].'">'.$this->lng['reset_button'].'</button>
	';
	
	return $html;
}

public function recheckImportItem($id_import, $job_done){
	$q = "UPDATE "._SQLPREFIX_."_nettv_import SET job_done = ".(int)$job_done.", job_done_at = 0 WHERE id_import = ".(int)$id_import." ";
	Db::query($q);
}
 
public function itemDelete($id_item, $id_import = 0){
	
	$qs = Db::result("SELECT * FROM "._SQLPREFIX_."_nettv_show_items WHERE id_item = ".$id_item." ");
	$qi = Db::result("SELECT * FROM "._SQLPREFIX_."_nettv_import WHERE id_import = ".$id_import." ");
	
	if(count($qs) == 1)
	{
		$d = $qs[0];
		$media = json_decode($d['media']);
		
		$video = isset($media->video) && is_array($media->video) && count($media->video)>0 ? (string)$media->video[0]: null;
		
		if(!is_null($video))
		{
			if(file_exists($video) && is_file($video))
			{
				unlink($video);
			}
		}
		
		if(isset($media->images) && is_array($media->images) && count($media->images)>0)
		{
			foreach($media->images as $i){
				
				if(file_exists($i) && is_file($i))
				{
					unlink($i);
				}
			}
		}
		
		Db::query("DELETE FROM "._SQLPREFIX_."_nettv_show_items WHERE id_item = ".$id_item." ");
	}
	
	if(count($qi) == 1)
	{
		$d = $qi[0];
		$videos = explode("|", $d['description']);
		
		if(count($videos)>0)
		{
			foreach($videos as $video){
				if(file_exists($video) && is_file($video))
				{
					unlink($video);
				}
			}
		}

		Db::query("DELETE FROM "._SQLPREFIX_."_nettv_import WHERE id_import = ".$id_import." ");
	}

}


}
?>
