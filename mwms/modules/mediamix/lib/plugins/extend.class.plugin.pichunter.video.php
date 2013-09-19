<?php
class PichunterVideo extends Dload{

protected $videos;

public function __construct(){
	parent::__construct();
	
	$this->videos = array(
		'wmv','mpg','mpeg','mov','avi','flv','mp4','ogv','webm','divx','m2v'
	);
}

// run method
public function run(){
	
	$linkSRC = $this->link;
	$linfo = parse_url($linkSRC);
	
	$html = file_get_html(urldecode($linkSRC));
	$items = $html->find($this->input);
	$title = basename(dirname($linfo['path'])).'-'.basename($linfo['path']);
	
	$dir = $this->config['mediaDir'].'/pchnt-video/'.Filter::makeUrlString($title);
	@Storage::makeDir($dir.'/th');
	
	$data = array();
	
	$data['dir'] = $dir;
	$data['total'] = count($items);
	$data['thumbs'] = 0;
	$data['videos'] = 0;
	$data['thumbsRead'] = 0;
	$data['videosRead'] = 0;
	
	foreach($items as $link){

		if(isset($link->href) && in_array(System::extension($link->href), $this->videos) === true){
			
			$th = $link->first_child();
			
			$targetTH =  isset($th->src) ? Registry::get('serverdata/root').'/'.$dir.'/th/'.basename($th->src): null;
			$targetFULL = Registry::get('serverdata/root').'/'.$dir.'/'.basename($link->href);
			
			$i1 = false;
			$i2 = false;
			
			if(!is_null($targetTH) && file_exists($targetTH))
			{
				$i1 = stat($targetTH);
			}
			
			if(file_exists($targetFULL))
			{
				$i2 = stat($targetFULL);
			}
			
			if( ($i1 === false || (is_array($i1) && $i1['size'] == 0)) && isset($th->src) )
			{
				$imageTH = @file_get_contents($th->src);
				
				$data['thumbs']++;
				
				if($imageTH !== false)
				{
					file_put_contents($targetTH, $imageTH); 
					$data['thumbsRead']++;
				}
			}
			
			if($i2 === false || (is_array($i2) && $i2['size'] == 0))
			{
				$imageFULL = @file_get_contents($link->href);
				
				$data['videos']++;
				
				if($imageFULL !== false)
				{
					file_put_contents($targetFULL, $imageFULL); 
					$data['videosRead']++;
				}
			}
		} 

	}

	$html = '
		<p>URL: '.$linkSRC.'</p>
		<p>DIR: '.$data['dir'].'</p>
		<p>TOTAL RECORDS: '.$data['total'].'</p>
		<p>TOTAL THUMBS: '.$data['thumbs'].'</p>
		<p>TOTAL VIDEOS: '.$data['videos'].'</p>
		<p>DOWNLOADED THUMBS: '.$data['thumbsRead'].'</p>
		<p>DOWNLOADED VIDEOS: '.$data['videosRead'].'</p>
	';
	
	return $html;
}

}
?>
