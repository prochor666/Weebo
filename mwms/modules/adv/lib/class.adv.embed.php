<?php
/**
* class.adv.embed.php - WEEBO framework adv module lib.
*
* THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
* IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
* FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
* AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
* LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
* OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
* SOFTWARE.
* --
*
* @package AdvEmbed
* @author Jan Prochazka aka prochor <prochor666@gmail.com>
* @copyright 2011 Jan Prochazka aka prochor <prochor666@gmail.com>
* @license http://opensource.org/licenses/mit-license.php MIT License
* @version 1.0 (2012-05-10)
* @link 
*/

class AdvEmbed{

public $positionsGet, $assetRelease, $format, $timeLimit, $action, $idAsset, $length;

private $assetQueue, $assetGrid, $assetBinData, $output, $domain, $routeTime, $jsuffix, $config, $lng;

final public function __construct() { 
	
	if(Registry::get('adv_public_position_active') === false){
		Registry::set('adv_public_position_active', array());
	}
	
	$this->registry = Registry::get('adv_public_position_active'); //aktivni id_assetu
	$this->positionAssets = array(); // nactene assety
	$this->assetRelease = array(); // bundle pro zobrazeni
	$this->positionsGet = array(); // pozice k nacteni
	$this->format = 'json'; // format vystupu
	$this->timeLimit = time();
	$this->assetBinData = array(); // output array
	$this->output = null; // output ascii
	$this->domain = Registry::get('serverdata/site');
	$this->action = 0;
	
	$this->length = 0;
	$this->jsuffix = System::rnd(10);
	
	$this->lng = Lng::get('adv');
	$this->config = Registry::get('moduledata/adv');
} 

public function route(){
	
}

public function release(){
	
	$this->getPositions(); // nacteni
	$this->getAssetStd();
	
	switch($this->format)
	{
		case 'json':
			header('Content-type: text/json');
			header('Content-type: application/json');
			
			$this->assetOutputJSON();
		break; case 'xml':
			header('Content-Type: application/xml; charset=UTF-8');
			
			$data = $this->assetOutputXML();
		break; case 'script':
			
			$data = $this->assetOutputJS();
		break; default:
			$data = null;
	}
	
	return $this->output;
}

protected function getPositions(){
	
	if(is_array($this->positionsGet)){
		$posGet = "'".implode("','", $this->positionsGet)."'";
	}else{
		$posGet = "'".implode("','", explode(",",$this->positionsGet))."'";
	}
	
	$limiter = count($this->positionsGet)>0 ? "WHERE id_position IN(".$posGet.") ": null;
	
	$q = "SELECT * FROM "._SQLPREFIX_."_adv_positions 
	".$limiter."
	ORDER BY id_position
	";
	
	$qq = Db::result($q);
	
	foreach($qq as $d)
	{
		$key = $d['id_position'];
		$assetIndex = $this->createPositionQueue($key);
		
		if(!is_null($assetIndex)){
			$this->assetRelease[$key] = $this->positionAssets[$key][$assetIndex];
		}
	}
}

protected function createPositionQueue($id_position){
	
	$queue = $this->getPositionAssets($id_position);
	
	$maxcount = count($queue);
	
	if($maxcount>0){
		$assetIndex = $this->setReg($id_position, $maxcount, $queue);
		
		/* 
		// Comment LATER //
		$helper = $this->get_position($id_position);
		$queue[$pos]['position_title'] = $helper['title'];
		$queue[$pos]['position_queue_actual'] = $pos;
		$queue[$pos]['position_queue'] = $maxcount;
		*/
		return $assetIndex;
	}
	
	return null;
}

/* REGISTRY, queue rotator */
private function setReg($id_position, $maxcount, $queue){
	$mx = $maxcount - 1;
	
	if(!array_key_exists($id_position, $this->registry)){ 
		$this->registry[$id_position] = 0;
	}else{
		$this->registry[$id_position] = $this->registry[$id_position] < $mx ? (int)$this->registry[$id_position]+1: 0;
	}	
	Registry::set('adv_public_position_active', $this->registry);
	return $this->registry[$id_position];
}

private function getAssetStd(){
	
	foreach($this->assetRelease as $d)
	{
		switch($this->action){
			case 0:
				// no
			break; case 1:
				$this->writeAction($d['id_asset'], 1, $this->length);
			break; case 2:
				$this->writeAction($d['id_asset'], 2, $this->length);
			break; case 3:
				$this->writeAction($d['id_asset'], 2, $this->length);
				$this->writeAction($d['id_asset'], 1, $this->length);
			break; default:
				// no
		}
		
		$f = explode('x', $d['format']);
		
		$this->assetBinData[] = array(
			'idMedia' => $d['id_asset'],
			'idPosition' => $d['id_position'],
			'mediaUrl' => $this->domain.'/'.$d['file'],
			'advUrl' => $d['url'],
			'mediaType' => 'image',
			'status' => 1,
			'time' => time(),
			'width' => $f[0],
			'height' => $f[1],
			'wmode' => $d['id_wmode'],
			'ext' => $d['id_blank'],
			'timeout' => $d['timeout']
		); 
	}
}

private function assetOutputJSON(){	
	$this->output = json_encode($this->assetBinData);
}

private function assetOutputXML(){
	$this->output = '<'.'?xml version="1.0" encoding="utf-8"?'.'><root>';
	
	foreach($this->assetBinData as $d)
	{
		$this->output .= '<adv>';
		foreach($d as $k => $v)
		{
			$this->output .= '<'.$k.'>'.$v.'</'.$k.'>';
		}
		$this->output .= '</adv>';
	}
	$this->output .= '</root>';
}

private function assetOutputJS(){
	$js = '
			'.$this->remoteJS().'
			
			$(document).ready(function(){
			';
	
	foreach($this->assetBinData as $d)
	{
		$int_url = $this->domain.'/apistream/?weeboapi=alias&amp;fn=AdvApi::route&amp;plan='.$d['idMedia'].'&amp;ref=';
		
		switch(System::extension($d['mediaUrl'])){
		case "swf":
			$js .= '
				AdvOut'.$this->jsuffix.'.placeFlash("'.$d['mediaUrl'].'", "'.urlencode($int_url).'", "adv'.$d['idPosition'].'", '.$d['width'].', '.$d['height'].',"'.$d['wmode'].'","'.$d['timeout'].'");
			';
		break; case "png": case "jpg": case "gif":
			$js .= '
				AdvOut'.$this->jsuffix.'.placeImage("'.$d['mediaUrl'].'", "'.$int_url.'", "adv'.$d['idPosition'].'", "'.$d['ext'].'","'.$d['timeout'].'");
			';
		break; case "flv": case "mp4": case "m4v":
			$js .= '
				AdvOut'.$this->jsuffix.'.placeVideo("'.$d['mediaUrl'].'", "'.$int_url.'", "#adv'.$d['idPosition'].'", "'.$d['ext'].'");
			';
		break; default:
			$js .= null;
		}
	}
	
	$js .= '
			});
		';
		
	$myjs = stripslashes($js);
	$encoding = 62;   // 0 - none, 10 - numeric, 62 - Normal ASCII, 95 - High ASCII
	$fast_decode = true;
	$special_char = false;
	$packer = new JavaScriptPacker($myjs, $encoding, $fast_decode, $special_char);

	$output = $packer->pack();
	//$output = $js;
	
	$this->output = '
	<script type="text/javascript">
	/* <![CDATA[ */
		'.$output.'
	/* ]]> */
	</script>
	';
}

private function remoteJS(){
	
	$embedText = mb_strlen($this->lng['adv_embed_text']) > 0 ? '<div class="banner-embed-text">'.$this->lng['adv_embed_text'].'</div>': null;
	
	$myjs = '
			var AdvOut'.$this->jsuffix.' = {
				
				placeFlash : function(file, link, dom_id, width, height, wmode, timeout)
				{
					weeboAdvMain.timeout = timeout*1000;
					var flashvars = {
						clickTag : link,
						ClickTag : link,
						clicktag : link,
						Clicktag : link,
						
						clickThru : link,
						ClickThru : link,
						clickthru : link,
						Clickthru : link,
						position_name : dom_id
					};
					
					var params = {
						menu: "false",
						allowfullscreen: "true", 
						allowscriptaccess: "always"
					};
					
					if(wmode == 1){ params.wmode = "transparent"; }
				
					var attributes = {
						name: "banner_adv"
					};
					
					var dominner = dom_id+"-inner";
					var domfill = dom_id+"-fill";
					
					$("#"+dom_id).append(\'<div class="banner-inner" id="\'+dominner+\'">'.$embedText.'<div class="banner-fill" id="\'+domfill+\'"></div></div>\');
					
					swfobject.embedSWF(file, domfill, width, height, "9.0.0", false, flashvars, params, attributes);
				},
				
				placeImage : function(file, link, dom_id, external, timeout)
				{
					weeboAdvMain.timeout = timeout*1000;
					var blank = external == 1 ? \' target = "_blank"\': \'\';
					var newfile = \'<a href="\' + link + \'" \' + blank + \'><img src="\' + file + \'" alt="\' + dom_id + \'" /></a>\'; 
					
					var dominner = dom_id+"-inner";
					var domfill = dom_id+"-fill";
					
					$("#"+dom_id).append(\'<div class="banner-inner" id="\'+dominner+\'">'.$embedText.'<div class="banner-fill" id="\'+domfill+\'"></div></div>\');
					
					$("#"+domfill).html(newfile);
				},
				
				placeVideo : function(file, link, dom_id, external)
				{
					return false;
				}
			};
	';

	return $myjs;
}

private function getPositionAssets($id_position){
	$qq = Db::result("
						SELECT * FROM "._SQLPREFIX_."_adv_assets 
						LEFT JOIN "._SQLPREFIX_."_adv_banners 
						ON "._SQLPREFIX_."_adv_assets.id_banner = "._SQLPREFIX_."_adv_banners.id_banner 
						WHERE id_position = ".$id_position." 
						AND id_active = 1 
						AND date_from < ".$this->timeLimit." 
						AND date_to > ".$this->timeLimit." 
						ORDER BY id_asset 
					");
	$this->positionAssets[$id_position] = $qq;
	return count($qq)>0 ? $qq: array();
}

public function getBannerLink($id_asset){
	$q = "
		SELECT * FROM "._SQLPREFIX_."_adv_assets 
		LEFT JOIN "._SQLPREFIX_."_adv_banners 
		ON "._SQLPREFIX_."_adv_assets.id_banner = "._SQLPREFIX_."_adv_banners.id_banner 
		WHERE id_asset = '".(int)$id_asset."'
	";
	$qq = Db::result($q);
	return count($qq) == 1 ? $qq[0]: array();
}

public function writeAction($id_asset, $action, $length, $ref = null){
	$q = "INSERT INTO "._SQLPREFIX_."_adv_asset_stats (id_asset, length, action_type, action_time, ref) VALUES ('".Db::escapeField((int)$id_asset)."', '".Db::escapeField((int)$length)."', '".Db::escapeField((int)$action)."', ".time().", '".Db::escapeField($ref)."')";
	Db::query($q);
}


}
?>
