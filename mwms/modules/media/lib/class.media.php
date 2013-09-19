<?php
class Media{

protected $status, $module, $mount, $thumbcache, $dynView, $allExtensions, $dirMask;

public $config, $rootDir, $extViewFilter;

public function __construct(){
	$this->lng = Lng::get('media');
	$this->config = Registry::get('moduledata/media');
	$this->thumbCache = System::root().'/'._GLOBALCACHEDIR_.'/media_module_cache';
	$this->thumbCacheUri = System::path().'/'._GLOBALCACHEDIR_.'/media_module_cache';
	$this->allExtensions = array_merge($this->config['video'],$this->config['image'],$this->config['audio'],$this->config['ascii'],$this->config['doc'],$this->config['pdf'],$this->config['archive'],$this->config['url'],$this->config['embed'], $this->config['exec']);
	$this->playlistExtensions = $this->getPlaylistExtensions();
	$this->rootDir = _GLOBALDATADIR_;
	$this->extViewFilter = null;
	$this->dirMask = '0777';
}

public function getAllFiles(){
	return $this->allExtensions;
}

protected function getPlaylistExtensions(){
	
	$pe = array();
	
	foreach($this->config['playlist'] as $index){
		$pe = array_merge($this->config[$index], $pe);
	}
	return $pe;
}

protected function getExtensionGroup($ext){
	foreach($this->config['playlist'] as $index){
		if(in_array($ext, $this->config[$index])){
			return $index; 
		}
	}
	return null;
}

public function test_mount($mount_point){
	return @is_dir($mount_point) ? true: false;	
}

public function splitDatadir($path){
	$test = explode('/', $path);
	
	if($test[0] == _GLOBALDATADIR_){
		unset($test[0]);
	}
	
	return implode('/', $test);
}

public function dynTree($file){
	
	$ext = strtolower(System::extension($file));
	
	$dynViev = Registry::get('mediatree');
	/*
	echo 'ADD: '.$file;
	System::dump(Registry::get('mediatree'));
	*/
	if(in_array($ext, $this->playlistExtensions))
	{
		$group = $this->getExtensionGroup($ext);
		if(!array_key_exists($group, $dynViev)){
			$dynViev[$group] = array();
		}
		//echo 'IN GROUP '.$group.' '.count($dynViev[$group]).' ADD:'.$file;
		
		array_push($dynViev[$group], $file);
	}

	Registry::set('mediatree', $dynViev);
}

private function jsPlaylist($group){
	
	$jsa = '';
	
	if(array_key_exists($group, $this->config))
	{
		$dynViev = Registry::get('mediatree');
		
		if(array_key_exists($group, $dynViev) && count($dynViev[$group])>0 )
		{
			$jsa = '
			,"playlist.position": "bottom",
			"playlist.size": "200",
			"playlist" : [';

			foreach($dynViev[$group] as $key => $file)
			{
				
				$jsa .= $key == 0 ? null: ', ';
				
				$jsa .= '
					{
						"file": "'.$file.'",
						"title": "'.$file.'"
					}
					';
			}

			$jsa .= ']';
		}

	}
	
	return $jsa;
}

public function playDir($group){
	
	$html = null;
	
	$dynViev = Registry::get('mediatree');
	
	if( in_array($group, $this->config['playlist']) && array_key_exists($group, $dynViev) && count($dynViev[$group])>0 )
	{
		//echo $group;
		$fmd = System::hash($group).rand(2, 7);
		
		$pl = $this->jsPlaylist($group);
		
		$html .= '
			<div id="player_'.$fmd.'"></div>
			<script type="text/javascript">
				jwplayer("player_'.$fmd.'").setup({
					"skin": weebo.settings.SiteRoot + "/shared/jwplayer/skins/lulu.zip",
					"flashplayer": weebo.settings.SiteRoot + "/shared/jwplayer/player.swf",
					"controlbar": "bottom",
					"provider": "http",
					"width": "700",
					"height": "400"
					'.$pl.'
				});
				
				jwplayer("player_'.$fmd.'").onComplete(function(){ jwplayer("player_'.$fmd.'").playlistNext() }).play();;
			</script>
		';
	}

	return $html;
}

public function playDirButtons(){
	
	$html = null;
	
	$dynViev = Registry::get('mediatree');
	
	foreach($this->config['playlist'] as $group)
	{
		if(array_key_exists($group, $dynViev) && count($dynViev[$group])>0 )
		{
		
			$fmd = System::hash($group).rand(2, 7);
			
			$pl = $this->jsPlaylist($group);
			
			$html .= '<button id="'.$group.'">'.$this->lng['media_play_group'].' '.$group.'</button>';
		}
	}
	
	return $html;
}


}
?>
