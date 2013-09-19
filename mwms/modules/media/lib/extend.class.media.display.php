<?php
class MediaDisplay extends Media{

public function __construct() {
	parent::__construct();
}

public function directoryRead($directory){
	set_time_limit(0);
	$html = null;
	
	if(@is_readable($directory)){
		
		$data = $this->processDir($directory);
		
		foreach($data['dirs'] as $item){
			
			$name = $item;
			$icon = @is_readable($directory.'/'.$item) ? 'dir_readable': 'dir_unreadable';
			$icon = $name == '..' ? 'dir_up': $icon;
			$path = $name == '..' ? $this->level_up($directory): $directory.'/'.$item;
			
			$del = Storage::isEmptyDir($path) && Storage::isLink($path) === false ? ' <a href="'.$path.'" class="remove-dir weebo_link" title="'.$this->lng['media_delete'].'">x</a>': null;
			
			$html .= $name == '..' && $directory == $this->rootDir ? null: '<div class="'.$icon.' dir"><a href="'.$path.'" title="'.$path.'" class="dir weebo_link">'.System::autoUTF($name).'</a> '.$del.'</div>'; 

		}  
		unset($dirs);
		
		foreach($data['files'] as $item){
			
			$name = System::autoUTF($item);
			$ext = System::extension($name);
			
			$path = $directory.'/'.$item;
			$this->dynTree($directory.'/'.$item);
			$i = stat(System::getFsFile('/'.$this->splitDatadir($path)));
			
			$html .= ( !is_null($this->extViewFilter) && is_array($this->extViewFilter) && in_array($ext, $this->extViewFilter) && Storage::isLink($path) === false ) || ( is_null($this->extViewFilter) && Storage::isLink($path) === false ) ? '<div class="file">'.$this->set_media_action($directory, $name).' <span class="size">['.System::fsFileSize($i['size']).']</span> <a href="'.$directory.'/'.$name.'" class="remove-file weebo_link" title="'.$this->lng['media_delete'].'">x</a></div>': null;
		}
		unset($files);
		
	}else{
		$name = System::autoUTF($directory);
		$html .= '<div class="dir_unreadable"><span>'.$name.'</span></div>';
	}
	
	set_time_limit(30); 
	return $html;  
}

public function file_view($path){
	set_time_limit(0);
	$dir = $this->level_up($path);
	$file = $this->last_level($path); 
	
	$filename = System::autoUTF($file);
	$dirname = System::autoUTF($dir);
	$html = '<div id="mediaspace">'.$this->set_media_view($dirname, $filename).'</div>'; /* <div class="dir_readable"><a href="'.$dirname.'" rel="'.$encoding.'" title="'.$dirname.'">'.$dirname.'</a></div> */
	
	set_time_limit(30); 
	return $html;  
}

/* View action */
public function set_media_view($dir, $file){
	
	$ext = strtolower(System::extension($file));
	if(in_array($ext, $this->config['image'])){
		$html = $this->view_image($dir, $file);
	}elseif(in_array($ext, $this->config['video'])){
		$html = $this->view_video($dir, $file);
	}elseif(in_array($ext, $this->config['audio'])){
		$html = $this->view_audio($dir, $file);
	}elseif(in_array($ext, $this->config['doc'])){
		$html = $this->view_doc($dir, $file);
	}elseif(in_array($ext, $this->config['pdf'])){
		$html = $this->view_pdf($dir, $file);
	}elseif(in_array($ext, $this->config['ascii'])){
		$html = $this->view_ascii($dir, $file);
	}elseif(in_array($ext, $this->config['archive'])){
		$html = $this->view_archive($dir, $file);
	}elseif(in_array($ext, $this->config['url'])){
		$html = $this->view_url($dir, $file);
	}elseif(in_array($ext, $this->config['embed'])){
		$html = $this->view_embed($dir, $file);
	}else{
		$html = $this->others($dir, $file);
	}
	return $html;
}

private function view_image($dir, $file){
	$html = '<div class="image"><img src="'.$dir.'/'.$file.'" alt="'.$file.'" /></div>';
	return $html;
} 

private function view_video($dir, $file){
	$fmd = System::hash($dir.'/'.$file).rand(2, 7);
	
	$html = '
		<video id="player_'.$fmd.'" poster="/shared/jwplayer/splash.png" width="700" height="400" controls="controls">
			<source src="'.$dir.'/'.$file.'" type="video/'.strtolower(System::extension($file)).'" />
		</video>
		<script type="text/javascript">
			jwplayer("player_'.$fmd.'").setup({
				"skin": weebo.settings.SiteRoot + "/shared/jwplayer/skins/lulu.zip",
				"stretching": "exactfit", //uniform,fill,exactfit,bestfit,none
				"flashplayer": weebo.settings.SiteRoot + "/shared/jwplayer/player.swf",
				"autostart": false,
				"provider": "http",
				"events" : {
					onReady: function (event) {
						/*
						this.play();
						this.seek(0.1);
						this.pause();
						*/
					}
				}
			});
			
		</script>
	';
	return $html;
} 

private function view_audio($dir, $file){
	//$html = '<a href="'.$dir.'/'.$file.'" class="audio">'.$file.'</a>';
	$fmd = System::hash($dir.'/'.$file).rand(2, 7);
	
	$html = '
		<audio id="player_'.$fmd.'" width="700" height="32" controls="controls">
			<source src="'.$dir.'/'.$file.'" type="audio/'.strtolower(System::extension($file)).'" />
		</audio>
		<script type="text/javascript">
			jwplayer("player_'.$fmd.'").setup({
				"skin": weebo.settings.SiteRoot + "/shared/jwplayer/skins/lulu.zip",
				"flashplayer": weebo.settings.SiteRoot + "/shared/jwplayer/player.swf",
				"provider": "http",
				"controlbar": "bottom",
				"width": "700",
				"height": "32"
				
			});
		</script>
	';
	
	return $html;
} 

private function view_doc($dir, $file){
	$html = '<a href="'.$dir.'/'.$file.'" class="doc">'.$file.'</a>';
	return $html;
} 

private function view_pdf($dir, $file){
	$html = '<a href="'.$dir.'/'.$file.'" class="pdf">'.$file.'</a>';
	return $html;
} 

private function view_ascii($dir, $file){
	$html = '<pre>'.htmlspecialchars(file_get_contents(System::root().'/'.$dir.'/'.$file)).'</pre>';
	
	return $html;
} 

private function view_url($dir, $file){
	$conf = parse_ini_file(System::root().'/'.$dir.'/'.$file);
	$html = '<pre>'.htmlspecialchars(file_get_contents(System::root().'/'.$dir.'/'.$file)).'</pre>
		<iframe src="'.$conf['URL'].'" width="700" height="350" style="overflow: scroll; border: 1px solid #888;">IFRAME ERROR</iframe>
	';
	return $html;
} 

private function view_embed($dir, $file){
	$html = '<pre>'.htmlspecialchars(file_get_contents(System::root().'/'.$dir.'/'.$file)).'</pre>';
	$html .= '<div class="media_embed">'.file_get_contents(System::root().'/'.$dir.'/'.$file).'</div>
	';
	return $html;
} 

private function view_archive($dir, $file){
	$html = '<a href="'.$dir.'/'.$file.'" class="archive">'.$file.'</a>';
	return $html;
} 


/* Click action */
public function set_media_action($dir, $file){
	
	//System::dump($this->config);
	$ext = strtolower(System::extension($file));
	if(in_array($ext, $this->config['image'])){
		return $this->image($dir, $file);
	}elseif(in_array($ext, $this->config['video'])){
		return $this->video($dir, $file);
	}elseif(in_array($ext, $this->config['audio'])){
		return $this->audio($dir, $file);
	}elseif(in_array($ext, $this->config['doc'])){
		return $this->doc($dir, $file);
	}elseif(in_array($ext, $this->config['pdf'])){
		return $this->pdf($dir, $file);
	}elseif(in_array($ext, $this->config['ascii'])){
		return $this->ascii($dir, $file);
	}elseif(in_array($ext, $this->config['archive'])){
		return $this->archive($dir, $file);
	}elseif(in_array($ext, $this->config['url'])){
		return $this->url($dir, $file);
	}elseif(in_array($ext, $this->config['embed'])){
		return $this->embed($dir, $file);
	}else{
		return $this->others($dir, $file);
	}
}

private function view_others($dir, $file){
	$html = '<a href="'.$dir.'/'.$file.'" class="other" title="'.$file.'"><span></span>'.$file.'</a>';
	return $html;
} 

private function image($dir, $file){
	
	$ext = System::extension($file);
	$cd = $this->thumbCache;
	
	if(!file_exists($cd) || !is_dir($cd)){
		umask(0000);
		mkdir($cd, $this->dirMask);
	}
	
	if(!file_exists($cd.'/'.System::hash($dir.'/'.$file).'.'.$ext)){
		$image = new SimpleImage();
		$image->load($dir.'/'.$file);
		@$image->resizeToHeight(48);
		@$image->save($cd.'/'.System::hash($dir.'/'.$file).'.'.$ext);
	}
	
	$html = '<a href="'.$dir.'/'.$file.'" class="weebo_link file image" title="'.$file.'"><span><img src="'.$this->thumbCacheUri.'/'.System::hash($dir.'/'.$file).'.'.$ext.'" alt="" /></span>'.$file.'</a>';
	return $html;
} 

private function video($dir, $file){
	$html = '<a href="'.$dir.'/'.$file.'" class="weebo_link file video" title="'.$file.'"><span></span>'.$file.'</a>';
	return $html;
} 

private function audio($dir, $file){
	$html = '<a href="'.$dir.'/'.$file.'" class="weebo_link file audio" title="'.$file.'"><span></span>'.$file.'</a>';
	return $html;
} 

private function doc($dir, $file){
	$html = '<a href="'.$dir.'/'.$file.'" class="weebo_link file doc" title="'.$file.'"><span></span>'.$file.'</a>';
	return $html;
} 

private function pdf($dir, $file){
	$html = '<a href="'.$dir.'/'.$file.'" class="weebo_link file pdf" title="'.$file.'"><span></span>'.$file.'</a>';
	return $html;
} 

private function ascii($dir, $file){
	$html = '<a href="'.$dir.'/'.$file.'" class="weebo_link file ascii" title="'.$file.'"><span></span>'.$file.'</a>';
	return $html;
} 

private function url($dir, $file){
	$html = '<a href="'.$dir.'/'.$file.'" class="weebo_link file url" title="'.$file.'"><span></span>'.$file.'</a>';
	return $html;
} 

private function embed($dir, $file){
	$html = '<a href="'.$dir.'/'.$file.'" class="weebo_link file embed" title="'.$file.'"><span></span>'.$file.'</a>';
	return $html;
} 

private function archive($dir, $file){
	$html = '<a href="'.$dir.'/'.$file.'" class="weebo_link file archive" title="'.$file.'"><span></span>'.$file.'</a>';
	return $html;
} 

private function others($dir, $file){
	$html = '<a href="'.$dir.'/'.$file.'" class="weebo_link file other" title="'.$file.'"><span></span>'.$file.'</a>';
	return $html;
} 


/* convert .. to /some/path */
private function level_up($path){
	$path = explode('/', $path);
	array_pop($path);
	return implode('/', $path);
}

private function last_level($path){
	$path = explode('/', $path);
	$i = count($path) - 1;
	return $path[$i];
}

private function processDir($xdir){

	$md = opendir($xdir);
	$fl = array(
		"files" => array(),
		"dirs" => array()
	);
	
	while(false!==($item = readdir($md))){
		if(!in_array($item, $this->config['protectedDirs']) && is_dir($xdir.'/'.$item)){
			array_push($fl['dirs'], $item);
		}elseif(!in_array($item, $this->config['protectedFiles']) && !in_array($item, $this->config['protectedDirs']) && $item != ".." && is_file($xdir.'/'.$item)){
			array_push($fl['files'], $item);
		}
	}
	
	closedir($md);
	usort($fl['dirs'], 'strcasecmp');
	usort($fl['files'], 'strcasecmp');
	return $fl;
}

}
?>
