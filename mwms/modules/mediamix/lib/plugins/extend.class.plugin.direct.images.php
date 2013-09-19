<?php
class DirectImages extends Dload{

public function __construct(){
	parent::__construct();
}

// run method
public function run(){
	
	$link = $this->link;
	$linfo = parse_url($link);
	
	//ini_set('user_agent', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:16.0) Gecko/20100101 Firefox/16.0');
	
	//$html = file_get_html(urldecode($link));
	
	$sourceHTML = $this->browser(urldecode($link));
	$html = str_get_html(strip_tags($sourceHTML, '<img>'));
	
	$items = $html->find($this->input);
	$title = basename(dirname($linfo['path'])).'-'.basename($linfo['path']);
	
	if(mb_strlen($title)<2){
		$title = $link.'-'.System::hash($sourceHTML);
	}
	
	$dir = $this->config['mediaDir'].'/000di/'.Filter::makeUrlString($title);
	@Storage::makeDir($dir);
	
	$data = array();
	
	$data['dir'] = $dir;
	$data['total'] = count($items);
	$data['images'] = 0;
	$data['imagesRead'] = 0;
	
	foreach($items as $image){

		if(isset($image->src)){
			$newSrc = $image->src;
			
			$targetFULL = Registry::get('serverdata/root').'/'.$dir.'/'.basename($newSrc);
			
			$i2 = false;
			
			if(file_exists($targetFULL))
			{
				$i2 = stat($targetFULL);
			}
			
			if($i2 === false || (is_array($i2) && $i2['size'] == 0))
			{
				$getURL = $newSrc;
				
				if($this->isRelativeUrl($getURL) === false){
					$tld = parse_url($this->link);
					$getURL = $tld['scheme'].'://'.$tld['host'].$tld['path'].'/'.$getURL;
				}
				
				$imageFULL = @file_get_contents($getURL);
				
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
		<p>TOTAL IMAGES: '.$data['images'].'</p>
		<p>DOWNLOADED IMAGES: '.$data['imagesRead'].'</p>
	';
	
	return $html;
}

protected function browser($link){
	$res = @file_get_contents($link, false);
	return $res;
}

protected function isRelativeUrl($url){
	return Validator::checkhttp($url);
}

}
?>
