<?php
class Imagefap extends Dload{

public function __construct(){
	parent::__construct();
}

// run method
public function run(){
	
	$link = strpos($this->link, $this->suffix) === false ? $this->link.$this->suffix: $this->link;
	$linfo = parse_url($link);
	
	//ini_set('user_agent', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:16.0) Gecko/20100101 Firefox/16.0');
	
	//$html = file_get_html(urldecode($link));
	
	$sourceHTML = $this->browser(urldecode($link));
	
	/*
	$tbl = array("\x80"=>"","\x81"=>"","\x82"=>"","\x83"=>"","\x84"=>"\"","\x85"=>"...","\x86"=>"","\x87"=>"","\x88"=>"",
	"\x89"=>"","\x8a"=>"","\x8b"=>"","\x8c"=>"","\x8d"=>"","\x8e"=>"","\x8f"=>"","\x90"=>"","\x91"=>"",
	"\x92"=>"","\x93"=>"\"","\x94"=>"\"","\x95"=>"o","\x96"=>"","\x97"=>"","\x98"=>"","\x99"=>"TM","\x9a"=>"","\x9b"=>">",
	"\x9c"=>"","\x9d"=>"","\x9e"=> "","\x9f"=>"","\xa1"=>"","\xa5"=>"","\xa6"=>"|","\xa9"=>"(c)","\xab"=>"","\xac"=>"not",
	"\xae"=>"(R)","\xb1"=>"+/-","\xb5"=>"u","\xb6"=>"P","\xb7"=>".","\xb9"=>"","\xbb"=>">>","\xbc"=>"","\xbe"=>"", "\x20AC" => "");
	
	//$sourceHTML = strtr($sourceHTML, $tbl);
	
	$sourceHTML = iconv("UTF-8", "UTF-8//IGNORE", $sourceHTML);
	*/
	$sourceHTML = strip_tags($sourceHTML, '<table><tr><td><a><img>');
	
	//echo $sourceHTML;
	
	//System::dump($sourceHTML);
	
	$html = str_get_html($sourceHTML);
	
	$items = $html->find($this->input);
	$title = basename(dirname($linfo['path'])).'-'.basename($linfo['path']);
	
	$dir = $this->config['mediaDir'].'/imgfp/'.Filter::makeUrlString($title);
	@Storage::makeDir($dir.'/th');
	
	$data = array();
	
	$data['dir'] = $dir;
	$data['total'] = count($items);
	$data['thumbs'] = 0;
	$data['images'] = 0;
	$data['thumbsRead'] = 0;
	$data['imagesRead'] = 0;
	
	foreach($items as $image){

		if(isset($image->src)){
			$newSrc = str_replace('thumb' , 'full', $image->src);
			//echo '<img src="'.$newSrc.'" />';
			
			$targetTH = Registry::get('serverdata/root').'/'.$dir.'/th/'.basename($newSrc);
			$targetFULL = Registry::get('serverdata/root').'/'.$dir.'/'.basename($newSrc);
			
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
			
			if($i1 === false || (is_array($i1) && $i1['size'] == 0))
			{
				$imageTH = @file_get_contents($image->src);
				
				$data['thumbs']++;
				
				if($imageTH !== false)
				{
					file_put_contents($targetTH, $imageTH); 
					$data['thumbsRead']++;
				}
			}
			
			if($i2 === false || (is_array($i2) && $i2['size'] == 0))
			{
				$imageFULL = @file_get_contents($newSrc);
				
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
	
	/*
	$opts = array(
		'http' => array(
		'method'=> "GET",
		'header'=>
				"User-Agent: Mozilla/5.0 (Windows NT 6.1; WOW64; rv:16.0) Gecko/20100101 Firefox/16.0\r\n".
				"Accept: text/html,application/xhtml+xml,application/xml;q=0.9;q=0.8\r\n".
				"Accept-Language: en-us,en;q=0.5\r\n".
				"Accept-Encoding: gzip,deflate\r\n".
				"Accept-Charset: U-8859-1,utf-8;q=0.7,*;q=0.7\r\n".
				"Keep-Alive: 300\r\n".
				"Connection: keep-alive"
		)
	);
	
	if(version_compare(PHP_VERSION, '5.3.0') == -1){ 
		ini_set('user_agent', $opts['http']['header']); 
	}
	 
	$context = stream_context_create($opts);
	$res = @file_get_contents($link, false, $context);
	//$res = stream_get_contents($fp);
	
	return $res;
	*/
}

}
?>
