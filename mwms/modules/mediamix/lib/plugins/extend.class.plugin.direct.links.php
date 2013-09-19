<?php
class DirectLinks extends Dload{

public function __construct(){
	parent::__construct();
}

// run method
public function run(){
	
	$linkSRC = $this->link;
	$linfo = parse_url($linkSRC);
	
	//ini_set('user_agent', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:16.0) Gecko/20100101 Firefox/16.0');
	
	//$html = file_get_html(urldecode($link));
	
	$sourceHTML = $this->browser(urldecode($linkSRC));
	$html = str_get_html(strip_tags($sourceHTML, '<a>'));
	
	$items = $html->find($this->input);
	$title = basename(dirname($linfo['path'])).'-'.basename($linfo['path']);
	
	if(mb_strlen($title)<2){
		$title = $link.'-'.System::hash($sourceHTML);
	}
	
	$dir = $this->config['mediaDir'].'/000dl/'.Filter::makeUrlString($title);
	@Storage::makeDir($dir);
	
	$data = array();
	
	$data['dir'] = $dir;
	$data['total'] = count($items);
	$data['links'] = 0;
	$data['linksRead'] = 0;
	
	foreach($items as $link){

		if(isset($link->src)){
			$newSrc = $link->href;
			//echo '<img src="'.$newSrc.'" />';
			
			$targetLINK = Registry::get('serverdata/root').'/'.$dir.'/th/'.basename($newSrc);
			
			$i1 = false;
			
			if(file_exists($targetLINK))
			{
				$i1 = stat($targetLINK);
			}
			
			if($i1 === false || (is_array($i1) && $i1['size'] == 0))
			{
				$getURL = $link->href;
				
				if($this->isRelativeUrl($getURL) === false){
					$tld = parse_url($this->link);
					$getURL = $tld['scheme'].'://'.$tld['host'].$tld['path'].'/'.$getURL;
				}
				
				$linkCONTENT = @file_get_contents($getURL);
				
				$data['links']++;
				
				if($linkCONTENT !== false)
				{
					file_put_contents($targetLINK, $linkCONTENT); 
					$data['linksRead']++;
				}
			}
			
		} 

	}

	$html = '
		<p>URL: '.$linkSRC.'</p>
		<p>DIR: '.$data['dir'].'</p>
		<p>TOTAL RECORDS: '.$data['total'].'</p>
		<p>TOTAL LINKS: '.$data['links'].'</p>
		<p>DOWNLOADED LINKS: '.$data['linksRead'].'</p>
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
