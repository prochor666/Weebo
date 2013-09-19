<?php
class Pichunter extends Dload{

public function __construct(){
	parent::__construct();
}

// run method
public function run(){
	
	$link = $this->link;
	$linfo = parse_url($link);
	
	$html = file_get_html(urldecode($link));
	$items = $html->find($this->input);
	$title = basename(dirname($linfo['path'])).'-'.basename($linfo['path']);
	
	$dir = $this->config['mediaDir'].'/pchnt/'.Filter::makeUrlString($title);
	@Storage::makeDir($dir.'/th');
	
	$data = array();
	
	$data['dir'] = $dir;
	$data['total'] = count($items);
	$data['thumbs'] = 0;
	$data['images'] = 0;
	$data['thumbsRead'] = 0;
	$data['imagesRead'] = 0;
	
	foreach($items as $image){

		if(isset($image->href)){
			
			$th = $image->first_child();
			
			$targetTH = Registry::get('serverdata/root').'/'.$dir.'/th/'.basename($image->href);
			$targetFULL = Registry::get('serverdata/root').'/'.$dir.'/'.basename($image->href);
			
			$i1 = false;
			$i2 = false;
			
			if(file_exists($targetTH))
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
				$imageFULL = @file_get_contents($image->href);
				
				$data['images']++;
				
				if($imageFULL !== false)
				{
					file_put_contents($targetFULL, $imageFULL); 
					$data['imagesRead']++;
				}
			}
		} 

	}

	$html = '
		<p>URL: '.$link.'</p>
		<p>DIR: '.$data['dir'].'</p>
		<p>TOTAL RECORDS: '.$data['total'].'</p>
		<p>TOTAL THUMBS: '.$data['thumbs'].'</p>
		<p>TOTAL IMAGES: '.$data['images'].'</p>
		<p>DOWNLOADED THUMBS: '.$data['thumbsRead'].'</p>
		<p>DOWNLOADED IMAGES: '.$data['imagesRead'].'</p>
	';
	
	return $html;
}

}
?>
