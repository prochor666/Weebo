<?php
class MediaBrowser extends Cms{
	public function __construct(){
	parent::__construct();
	$this->page_default = 20;
	$this->query_limit = 0;
	$this->result_count = 0;
	$this->result_limit = 1000;
	$this->search_term_length_min = 3;
	$this->order_index = array();
	$this->id_dir = 0;
	
	$this->view_url_suffix = '&amp;sysadmin='.Registry::get('userdata/id_user'); //.'&amp;media_search_term='.$this->search_term.''
	
	$this->filterInit();
	
	$this->search_term = $this->filterReg['media_search_term'];
}


protected function getDirResult($mode="base"){
	
	switch($mode){
		case "base":
		
			$q = "SELECT * FROM "._SQLPREFIX_."_cms_media_list WHERE id_dir = '".$this->id_dir."' ORDER BY ".$this->filterReg['media_order']." ".$this->filterReg['media_order_direction'];
			$__PRECACHE = Db::result($q);
			
			return Db::result($q);
		
		break; case "search":
			
			$q = "SELECT * FROM "._SQLPREFIX_."_cms_media_list WHERE title LIKE '%".$this->search_term."%' ORDER BY ".$this->filterReg['media_order']." ".$this->filterReg['media_order_direction'];
			
			return Db::result($q);
				
		break; default:
			
			return array();	
	}
}

public function reindexDir($id_dir){

	$d = $this->getDirData($id_dir);
	$path = $d['path'];

	Storage::makeDir('content/'.$path);

	$md = opendir('content/'.$path);
	$fl = array(
		"files" => array(),
		"dirs" => array()
	);
	$xdir = 'content/'.$path;
	while(false!==($item = readdir($md))){
		if($item != "." && is_dir($xdir.'/'.$item)){
			//array_push($fl['dirs'], $item);
		}elseif($item != "." && $item != ".." && is_file($xdir.'/'.$item)){
			array_push($fl['files'], $item);
		}
	}

	$html = null;

	closedir($md);
	//usort($fl['dirs'], 'strcasecmp');
	usort($fl['files'], 'strcasecmp');
	
	$notify = false;
	
	foreach($fl['files'] as $file){
		$fileData = $this->getMediaRecord($path.'/'.$file);
		
		if(is_null($fileData) && $this->isImage($file)){
			$xp = System::fsDir($path);
			Storage::makeDir('content/'.$path.'/th');
			$this->hasThumb($xp, $file);
			$notify = true;
			
			/* RESIZE */
			$image = new SimpleImage();
			$image->load($xp.'/'.$file);
			
			if($this->config['image_thumb_preffer_axxis'] === true)
			{
				$image->resizeToWidth($this->config['image_size']['origWidth']);
			}else{
				$image->resizeToHeight($this->config['image_size']['origHeight']);
			}
			
			$image->save($xp.'/'.$file);
			
			umask(0000);
			chmod($xp.'/'.$file, 0777);
			
			$html .= $this->saveMediaRecord($id_dir, $path, $file);
		}
	}
	
	$html = $notify ? $html: $this->lng['mwms_media_dir_reindex_no_new'];
	
	return $html;
}

protected function saveMediaRecord($id_dir, $dir, $file){
	$q = "INSERT INTO "._SQLPREFIX_."_cms_media_list (title, id_dir, date_ins, path) VALUES ('".$file."', '".(int)$id_dir."', '".time()."','".$dir.'/'.$file."')";
	$qq = Db::query($q);
	return '<p class="notify">'.$this->lng['mwms_media_dir_reindex'].': '.$file.'</p>';
}

protected function hasThumb($dir, $file){
	
	if(!file_exists($dir.'/th/th_'.$file)){
		$image = new SimpleImage();
		$image->load($dir.'/'.$file);
		
		if($this->config['image_thumb_preffer_axxis'] === true)
		{
			$image->resizeToWidth($this->config['image_size']['thWidth']);
		}else{
			$image->resizeToHeight($this->config['image_size']['thHeight']);
		}
		
		$image->save($dir.'/th/th_'.$file);
		
		umask(0000);
		chmod($dir.'/th/th_'.$file, 0777);
	}
	
	return $dir.'/th/th_'.$file;
}

protected function getMediaRecord($file){
	$d = $this->getMediaData($file);
	return count($d)==1 ? $d[0]: null;
}

public function showBrowserMenu(){
	
	$ls = null;
	
	if($this->id_dir>0){
		$l = $this->getDirData($this->id_dir);
		$ls = $l['title'];
	}
	
	$html = '
		<div class="mwms_tabs" id="tabs">
			<ul>
				<li><a href="'.$this->ajax_view_url.'media.dir.detail.process.php'.$this->ajax_view_url_suffix.'&id_dir='.$this->id_dir.'"><em class="ui-icon ui-icon-document mwms-floating-icon"></em> '.$this->lng['mwms_media_dir_edit'].': '.$ls.'<span>&nbsp;</span></a></li>
				<li class="default-min-tab"><a href="'.$this->ajax_view_url.'media.browser.init.php'.$this->ajax_view_url_suffix.'&amp;id_dir='.$this->id_dir.'"><em class="ui-icon ui-icon-folder-open mwms-floating-icon"></em> '.$this->lng['mwms_media_dir_content'].' '.$ls.'<span>&nbsp;</span></a></li>
			</ul>
		</div>
		
	';

	return $html;
}


protected function setFilterForm(){
	
	$url = html_entity_decode($this->ajax_view_url.'media.browser.inner.php'.$this->ajax_view_url_suffix.'&amp;media_order='.$this->filterReg['media_order'].'&amp;media_order_direction='.$this->filterReg['media_order_direction'].'&amp;media_page=1');
	
	$html = '
		<input type="hidden" id="media_search_path" name="media_search_path" value="'.$url.'&amp;id_dir='.$this->id_dir.'" /> 
		<input type="text" id="media_search" class="text" name="media_search" value="'.$this->filterReg['media_search_term'].'" /> 
		<button class="media_search_send button" title="'.$this->lng['search_button'].'">'.$this->lng['search_button'].'</button>
		<button class="media_search_reset button" title="'.$this->lng['reset_button'].'">'.$this->lng['reset_button'].'</button>
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

public function fileDelete($id_media){
	$x = "SELECT * FROM "._SQLPREFIX_."_cms_media_list WHERE id_media = '".(int)$id_media."' LIMIT 1 ";
	$qq = Db::result($x);
	$d = count($qq)==1 ? $qq[0]: null;
	
	if(!is_null($d))
	{
		$base = basename($d['path']);
		$dir = dirname($d['path']);
		
		$delThPath = 'content/'.$dir.'/th/th_'.$base;
		$delPath = 'content/'.$d['path'];
		
		echo 'DELETE THUMB: '.$delThPath.'<br />';
		echo 'DELETE IAMGE: '.$delPath.'<br />';
		
		Storage::deleteFile($delThPath);
		Storage::deleteFile($delPath);

		$q1 = "DELETE FROM "._SQLPREFIX_."_cms_media_list WHERE id_media = '".(int)$id_media."' ";
		Db::query($q1);
	}
}

public function dirDelete($id_dir){
	
	$x = "SELECT * FROM "._SQLPREFIX_."_cms_media_dir WHERE id_dir = '".(int)$id_dir."' LIMIT 1 ";
	$qd = Db::result($x);
	$dirData = count($qd)==1 ? $qd[0]: null;
	
	if(!is_null($dirData))
	{
	
		$galleryDir = $dirData['path'];

		$q = "SELECT * FROM "._SQLPREFIX_."_cms_media_list WHERE id_dir = '".(int)$id_dir."' ";
		$qq = Db::result($q);
		
		foreach($qq as $d){
			
			$base = basename($d['path']);
			$dir = dirname($d['path']);
			
			$delThPath = 'content/'.$dir.'/th/th_'.$base;
			$delPath = 'content/'.$d['path'];
			
			echo 'DELETE THUMB: '.$delThPath.'<br />';
			echo 'DELETE IAMGE: '.$delPath.'<br />';
			
			Storage::deleteFile($delThPath);
			Storage::deleteFile($delPath);
		}
		
		if(file_exists('content/'.$galleryDir.'/th')){ Storage::deleteDir('content/'.$galleryDir.'/th'); }
		if(file_exists('content/'.$galleryDir)){ Storage::deleteDir('content/'.$galleryDir); }
		
		echo 'DELETE THUMB: content/'.$galleryDir.'/th<br />';
		echo 'DELETE IAMGE: content/'.$galleryDir.'<br />';
		
		if(Registry::get('cms_active_gallery') ==  $id_dir){
			Registry::set('cms_active_gallery', null);
		}
		
		$q1 = "DELETE FROM "._SQLPREFIX_."_cms_media_dir WHERE id_dir = '".(int)$id_dir."' ";
		$q2 = "DELETE FROM "._SQLPREFIX_."_cms_media_list WHERE id_dir = '".(int)$id_dir."' ";
		Db::query($q1);
		Db::query($q2); 
	}
}

}
?>
