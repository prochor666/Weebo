<?php
class ImageLinks extends Dload{

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
	$html = str_get_html(strip_tags($sourceHTML, '<a><img>'));
	
	$items = $html->find($this->input);
	$title = basename(dirname($linfo['path'])).'-'.basename($linfo['path']);
	
	if(mb_strlen($title)<2){
		$title = $link.'-'.System::hash($sourceHTML);
	}
	
	$dir = $this->config['mediaDir'].'/000il/'.Filter::makeUrlString($title);
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
				$getURL = $th->src;
				
				if($this->isRelativeUrl($getURL) === false){
					$tld = parse_url($this->link);
					$getURL = $tld['scheme'].'://'.$tld['host'].$tld['path'].'/'.$getURL;
				}
				
				$imageTH = @file_get_contents($getURL);
				
				$data['thumbs']++;
				
				if($imageTH !== false)
				{
					file_put_contents($targetTH, $imageTH); 
					$data['thumbsRead']++;
				}
			}
			
			if($i2 === false || (is_array($i2) && $i2['size'] == 0))
			{
				$getURL = $image->href;
				
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
		<p>TOTAL THUMBS: '.$data['thumbs'].'</p>
		<p>TOTAL IMAGES: '.$data['images'].'</p>
		<p>DOWNLOADED THUMBS: '.$data['thumbsRead'].'</p>
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
