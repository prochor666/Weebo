<?php
/**
* static.class.nettv.embed.php - WEEBO framework nettv module lib.
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
* @package NettvEmbed
* @author Jan Prochazka aka prochor <prochor666@gmail.com>
* @copyright 2011 Jan Prochazka aka prochor <prochor666@gmail.com>
* @license http://opensource.org/licenses/mit-license.php MIT License
* @version 1.0 (2011-07-28)
* @link 
*/

class NettvEmbed{

final public function __construct() { throw new WeeboException("Can not instantiate static class!"); }
final public function __clone() { throw new WeeboException("Can not clone static class!"); }

public static function getCfg($var)
{
	$out = new WeeboNettv;
	return $out->config[$var];
}

public static function getLng($var)
{
	$out = new WeeboNettv;
	return $out->lng[$var];
}

public static function listShows($id_content)
{
	$param = null;
	$cms = new Cms;
	$tv = new WeeboNettv;
	$s = null;
	
	if($id_content > 0){
		$d = $cms->getContentData($id_content);
		$param = $d['display_script_param'];
	}
	
	$paramList = $tv->getShows(); 
	
	if(count($paramList)>0){
		$s = '<select id="param_select_method_data_'.$id_content.'" name="param_select_method_data" class="select meta_live_edit param_select_method_data">';
		foreach($paramList as $shows){
			$s .= '<option value="id_show:'.$shows['id_show'].'" '.Validator::selected('id_show:'.$shows['id_show'], $param).'>'.$shows['title'].'</option>';
		}
		$s .= '</select>';
	}
	
	return $s;
}

public static function listTeam($id_content)
{
	$param = null;
	$cms = new Cms;
	$tv = new WeeboNettv;
	$s = null;
	
	if($id_content > 0){
		$d = $cms->getContentData($id_content);
		$param = $d['display_script_param'];
	}
	
	$paramList = $tv->getTeam(); 
	
	if(count($paramList)>0){
		$s = '<select id="param_select_method_data_'.$id_content.'" name="param_select_method_data" class="select meta_live_edit param_select_method_data">';
		foreach($paramList as $team){
			$s .= '<option value="id_team:'.$team['id_team'].'" '.Validator::selected('id_team:'.$team['id_team'], $param).'>'.$team['title'].'</option>';
		}
		$s .= '</select>';
	}
	
	return $s;
}

public static function userArchiveID($id_content)
{
	$param = null;
	$cms = new Cms;
	$tv = new WeeboNettv;
	$s = null;
	
	$param = null;
	
	if($id_content > 0){
		$d = $cms->getContentData($id_content);
		$param = $d['display_script_param'];
	}
	
	$s .= '<input type="hidden" id="param_select_method_data_'.$id_content.'" name="param_select_method_data" class="meta_live_edit" value="'.$param.'" />';
	
	return $s;
}

public static function tvGuide(){
	$n = new WeeboNettvRender;
	
	$format = isset($_GET['format']) && $_GET['format'] == 'xml' ? 'xml': 'json';
	
	$from = isset($_GET['from']) ? (int)$_GET['from']: 1;
	$from = $from > 0 && $from < 8 ? $from: 1;
	
	$to = isset($_GET['to']) ? (int)$_GET['to']: 7;
	$to = $to > 0 && $to < 8 ? $to: 7;
	
	$data = $n->tvGuide($from, $to);
	
	$enc = null;
	
	switch($format)
	{
		case 'json':
			header('Content-type: text/json');
			header('Content-type: application/json');
			
			$enc = json_encode($data);
		break; case 'xml':
			header('Content-Type: application/xml; charset=UTF-8');
			
			$enc = self::array2xml($data);
		break; default:
			$data = null;
	}
	
	return $enc;
}


public static function array2xml($data){
	
	$xml  = '<?xml version="1.0" encoding="utf-8" ?>
<root>
';
	
	foreach($data as $day){
		
		$xml .= '
	<day>';
		$xml .= '
		<date>'.$day['date'].'</date>
		<name>'.$day['name'].'</name>
		';
		foreach($day['items'] as $d){
			
			$xml .= '
		<item>
			<start>'.$d['start'].'</start>
			<start_timestamp>'.$d['start_timestamp'].'</start_timestamp>
			<title>'.$d['title'].'</title>
			<description>'.$d['description'].'</description>
		</item>
			';
		}
		
		$xml .= '
	</day>';
	}
	
	$xml .= '
</root>
';
	
	return $xml;
}

public static function encodeAudio($d){
	// ignore audio for now
	//echo 'SKIPPING AUDIO'."\n";
}
public static function encodeOther($d){
	// ignore other types
	//echo 'SKIPING OTHERS'."\n";
}
public static function encodeImage($d){
	// only copy & make thumbs
	//echo 'SKIPPING IMAGES'."\n";
}

public static function encodeVideo($d){
	
	// encode video & make thmubs
	if(self::checkPID(0) === false)
	{
		$log = '"'.date(self::getLng('date_time_precise'), time()).'";"STARTING JOB '.$d['id_import'].'"'."\n";
		self::log($log);
		
		self::createPID(json_encode(array('id_import' => $d['id_import'], 'file' => $d['description'])), 0);
		
		if($d['job_done'] == 0)
		{
			$jobData = json_decode($d['data'], false, 256);
		}else{
			
			$extLength = mb_strlen(System::extension($d['description']));
			$mediaDir = System::root().'/'.self::getCfg('mediaDir');
			$importDirLength = mb_strlen(dirname($d['description']));
			$targetDir = $mediaDir.'/'.mb_substr($d['description'], $importDirLength);
			$targetFile = mb_substr(basename($d['description']), 0, -$extLength);
	
			$videoTargetProbe = dirname($targetDir).'/videos/'.$targetFile.'mp4';
			
			$commandResTarget = self::probeMedia($videoTargetProbe);
			$jobData = json_decode($commandResTarget, false, 256);
		}
		
		$detectVideoBitrate = true;
		$detectAudioBitrate = true;
		
		$bitrateVideo = 500000;
		$bitrateAudio = 128000;
		
		$videoWidth = 320;
		$videoHeight = 240;
		
		foreach($jobData->streams as $stream)
		{
			if($stream->codec_type == 'video' && $detectVideoBitrate === true)
			{
				$bitrateVideo = isset($stream->bit_rate) ? (int)$stream->bit_rate: $bitrateVideo;
				$videoWidth = isset($stream->width) ? (int)$stream->width: $videoWidth;
				$videoHeight = isset($stream->height) ? (int)$stream->height: $videoHeight;
				$detectVideoBitrate = false;
			}

			if($stream->codec_type == 'audio' && $detectAudioBitrate === true)
			{
				$bitrateAudio =  is_object($stream->bit_rate) ? (int)$stream->bit_rate: $bitrateAudio;
				$detectAudioBitrate = false;
			}
		}
		
		if($bitrateVideo > 899999){
			$bitrateVideo = 1000000;
		}elseif($bitrateVideo < 899999){
			$bitrateVideo = 850000;
		}elseif($bitrateVideo < 559999){
			$bitrateVideo = 500000;
		}elseif($bitrateVideo < 339999){
			$bitrateVideo = 300000;
		}elseif($bitrateVideo < 189999){
			$bitrateVideo = 180000;
		}
		
		if($bitrateAudio > 99999){
			$bitrateAudio = 128000;
		}elseif($bitrateAudio < 99999){
			$bitrateAudio = 96000;
		}elseif($bitrateAudio < 69999){
			$bitrateAudio = 64000;
		}elseif($bitrateAudio < 399999){
			$bitrateAudio = 32000;
		}elseif($bitrateAudio < 16999){
			$bitrateAudio = 16000;
		}
		
		$log = '"'.date(self::getLng('date_time_precise'), time()).'";"CHECK FILE '.$d['description'].'"'."\n";
		self::log($log);
		
		if(file_exists($d['description']))
		{
			$log = '"'.date(self::getLng('date_time_precise'), time()).'";"FILE OK: '.$d['description'].'"'."\n";
			self::log($log);
			
			$aspectR = $videoHeight/$videoWidth;
			
			$extLength = mb_strlen(System::extension($d['description']));
			$mediaDir = System::root().'/'.self::getCfg('mediaDir');
			$importDirLength = mb_strlen(dirname($d['description']));
			$targetDir = $mediaDir.'/'.mb_substr($d['description'], $importDirLength);
			$targetFile = mb_substr(basename($d['description']), 0, -$extLength);
			
			$imSize = self::getCfg('image_size');
			
			$thumbWidth = (int)$imSize['origWidth'];
			$thumbHeight = (int)($imSize['origWidth']*$aspectR);
			
			$thumb_1_get = $d['job_done'] == 0 ? $jobData->format->duration / 10: $jobData->format->duration / 4;
			$thumb_2_get = $d['job_done'] == 0 ? $jobData->format->duration / 2: $jobData->format->duration / 3;
			$thumb_3_get = $d['job_done'] == 0 ? $jobData->format->duration / 1.4: $jobData->format->duration / 1.1;
			
			$progressFile = System::root().'/'.self::getCfg('pidDir').'/encoder.0.progress';
			
			$thumb1 = dirname($targetDir).'/videos/th_1_'.$targetFile.'jpg';
			$thumb2 = dirname($targetDir).'/videos/th_2_'.$targetFile.'jpg';
			$thumb3 = dirname($targetDir).'/videos/th_3_'.$targetFile.'jpg';
			$videoTarget = dirname($targetDir).'/videos/'.$targetFile.'mp4';
			
			if(file_exists($videoTarget) && is_file($videoTarget) && $d['job_done'] == 0){
				@unlink($videoTarget);
			}
			
			@unlink($thumb1);
			@unlink($thumb2);
			@unlink($thumb3);
			
			$thumbSource = $d['job_done'] == 0 ? $d['description']: $videoTarget;
			
			$encoderThumbCommand1 = 'ffmpeg -y -ss '.self::durationToStr($thumb_1_get, true).' -t 1 -i '.$thumbSource.' -f mjpeg -s '.$thumbWidth .'x'.$thumbHeight.' '.$thumb1;
			$encoderThumbCommand2 = 'ffmpeg -y -ss '.self::durationToStr($thumb_2_get, true).' -t 1 -i '.$thumbSource.' -f mjpeg -s '.$thumbWidth .'x'.$thumbHeight.' '.$thumb2;
			$encoderThumbCommand3 = 'ffmpeg -y -ss '.self::durationToStr($thumb_3_get, true).' -t 1 -i '.$thumbSource.' -f mjpeg -s '.$thumbWidth .'x'.$thumbHeight.' '.$thumb3;
			$encoderEncodeCommand = 'ffmpeg -y -i \''.$d['description'].'\' -vcodec libx264 -b:v '.$bitrateVideo.' -acodec libfaac -b:a '.$bitrateAudio.' '.$videoTarget.' 2>'.$progressFile;
			
			ob_start();
			passthru($encoderThumbCommand1, $commandRes1);
			passthru($encoderThumbCommand2, $commandRes2);
			passthru($encoderThumbCommand3, $commandRes3);
			if($d['job_done'] == 0)
			{
				passthru($encoderEncodeCommand, $commandRes4);
			}
			$commandRes = ob_get_contents();
			ob_end_clean();
			
			self::fixThumbSize($thumb1, $thumbWidth);
			self::fixThumbSize($thumb2, $thumbWidth);
			self::fixThumbSize($thumb3, $thumbWidth);
			
			$log = '"'.date(self::getLng('date_time_precise'), time()).'";"IMAGE1 '.$encoderThumbCommand1.'"'."\n";
			self::log($log);
			$log = '"'.date(self::getLng('date_time_precise'), time()).'";"IMAGE2 '.$encoderThumbCommand2.'"'."\n";
			self::log($log);
			$log = '"'.date(self::getLng('date_time_precise'), time()).'";"IMAGE3 '.$encoderThumbCommand3.'"'."\n";
			self::log($log);
			if($d['job_done'] == 0)
			{
				$log = '"'.date(self::getLng('date_time_precise'), time()).'";"VIDEO '.$encoderEncodeCommand.'"'."\n";
				self::log($log);
			}
			$q = "UPDATE "._SQLPREFIX_."_nettv_import SET job_done = 1, job_done_at = ".time()." WHERE id_import =".(int)$d['id_import'];
			Db::query($q);
			
			$mediaSource = json_encode( array('video' => array($videoTarget), 'images' => array($thumb1, $thumb2, $thumb3)) );
			$videoTitle = basename($d['description']);
			$videoTitle = isset($jobData->format->tags) && isset($jobData->format->tags->title) ? (string)$jobData->format->tags->title: $videoTitle;
			
			$id_item = self::checkItem($d['id_import']);
			
			if($id_item == 0)
			{
				$q = "
				INSERT INTO "._SQLPREFIX_."_nettv_show_items 
				(title, description, id_public, id_import, media, type, format, date_public, date_ins) 
				VALUES 
				('".Db::escapeField($videoTitle)."', '', 0, ".(int)$d['id_import'].", '".Db::escapeField($mediaSource)."', 'video','".Db::escapeField($videoWidth .'x'.$videoHeight)."', ".time().", ".time()." )
				";
			}else{
				$q = "
				UPDATE "._SQLPREFIX_."_nettv_show_items SET
				 media = '".Db::escapeField($mediaSource)."', 
				 type = 'video', 
				 format = '".Db::escapeField($videoWidth .'x'.$videoHeight)."', 
				 id_upd = 0,
				 date_upd = ".time()." 
				 WHERE id_item = ".$id_item."
				 ";
			}
			Db::query($q);
			self::deletePID(0);
			
			return false;
		}else{
			$log = '"'.date(self::getLng('date_time_precise'), time()).'";"FILE FAILED: '.$d['description'].'"'."\n";
			self::log($log);
			
			$q = "UPDATE "._SQLPREFIX_."_nettv_import SET job_done = -2, job_done_at = ".time()." WHERE id_import =".(int)$d['id_import'];
			Db::query($q);
			
			self::deletePID(0);
			
			return false;
		}
	}else{
		
		$pidFile = System::root().'/'.self::getCfg('pidDir').'/encoder.'.$t.'.pid';
		$findFile = file_get_contents($pidFile);
		$_es = json_decode(trim($findFile));
		
		$_file = basename((string)$_es->file);
		$_id_import = (string)$_es->id_import;
		
		$log = '"'.date(self::getLng('date_time_precise'), time()).'";"JOB '.$_id_import.' IN PROGRESS (media '.$_file.')"'."\n";
		self::log($log);
		
		return false;
	}
	
	return true;
}

public static function encodeDVDVideo($d){
	
	// encode video & make thmubs
	if(self::checkPID(0) === false)
	{
		$log = '"'.date(self::getLng('date_time_precise'), time()).'";"STARTING JOB '.$d['id_import'].'"'."\n";
		self::log($log);
		
		self::createPID(json_encode(array('id_import' => $d['id_import'], 'file' => $d['description'])), 0);
		
		$jobData = json_decode($d['data'], false, 256);
		
		$x = true;
		
		if($x === true)
		{
			//$log = '"'.date(self::getLng('date_time_precise'), time()).'";"FILES OK: '.$d['description'].'"'."\n";
			//self::log($log);
			
			$progressFile = System::root().'/'.self::getCfg('pidDir').'/encoder.0.progress';
			$targetDir = System::root().'/'.self::getCfg('exportDVDDir');
			
			$targetFile = $d['title'];
			
			$videoTarget = $targetDir.'/'.$targetFile.'.mpg';
			
			if(file_exists($videoTarget) && is_file($videoTarget)){
				@unlink($videoTarget);
			}
			
			//$encoderEncodeCommand = "cat '".str_replace('|', '\' \'', $d['description'])."' | ffmpeg -y -i -  -vcodec copy -acodec mp2 -ab 256k '".$videoTarget."' 2>".$progressFile;
			$encoderEncodeCommand = "ffmpeg -i 'concat:".$d['description']."' -vcodec copy -acodec mp2 -ab 256k '".$videoTarget."' 2>".$progressFile;
			
			ob_start();
			passthru($encoderEncodeCommand, $commandRes4);
			$commandRes = ob_get_contents();
			ob_end_clean();
			
			/*
			// Probe additional info for additional actions
			$probeInfoCommand = 'ffprobe -i "'.$videoTarget.'" -print_format json -show_format -show_streams';

			ob_start();
			passthru($probeInfoCommand, $commandResProbeTarget);
			$commandResProbeTarget = ob_get_contents();
			ob_end_clean();
			
			$q = "UPDATE "._SQLPREFIX_."_nettv_import SET data = '".Db::escapeField($commandResProbeTarget)."' WHERE id_import =".(int)$d['id_import'];
			Db::query($q);
			
			$jobTargetData = json_decode($commandResProbeTarget, false, 256);
		
			$videoWidth = 720;
			$videoHeight = 576;
			
			foreach($jobTargetData->streams as $stream)
			{
				if($stream->codec_type == 'video')
				{
					$videoWidth = isset($stream->width) ? (int)$stream->width: $videoWidth;
					$videoHeight = isset($stream->height) ? (int)$stream->height: $videoHeight;
				}
			}
			
			$aspectR = $videoHeight/$videoWidth;
			
			$imSize = self::getCfg('image_size');
			
			$thumbWidth = (int)$imSize['origWidth'];
			$thumbHeight = (int)($imSize['origWidth']*$aspectR);
			
			$thumb_1_get = $jobTargetData->format->duration / 10;
			$thumb_2_get = $jobTargetData->format->duration / 2;
			$thumb_3_get = $jobTargetData->format->duration / 1.4;
			
			$thumb1 = $targetDir.'/th_1_'.$targetFile.'.jpg';
			$thumb2 = $targetDir.'/th_2_'.$targetFile.'.jpg';
			$thumb3 = $targetDir.'/th_3_'.$targetFile.'.jpg';
			
			$encoderThumbCommand1 = 'ffmpeg -y -ss '.self::durationToStr((int)$thumb_1_get).' -t 1 -i '.$videoTarget.' -f mjpeg -s '.$thumbWidth .'x'.$thumbHeight.' '.$thumb1;
			$encoderThumbCommand2 = 'ffmpeg -y -ss '.self::durationToStr((int)$thumb_2_get).' -t 1 -i '.$videoTarget.' -f mjpeg -s '.$thumbWidth .'x'.$thumbHeight.' '.$thumb2;
			$encoderThumbCommand3 = 'ffmpeg -y -ss '.self::durationToStr((int)$thumb_3_get).' -t 1 -i '.$videoTarget.' -f mjpeg -s '.$thumbWidth .'x'.$thumbHeight.' '.$thumb3;
			
			ob_start();
			passthru($encoderThumbCommand1, $commandRes1);
			passthru($encoderThumbCommand2, $commandRes2);
			passthru($encoderThumbCommand3, $commandRes3);
			$commandRes = ob_get_contents();
			ob_end_clean();
			
			self::fixThumbSize($thumb1, $thumbWidth);
			self::fixThumbSize($thumb2, $thumbWidth);
			self::fixThumbSize($thumb3, $thumbWidth);
			*/
			$q = "UPDATE "._SQLPREFIX_."_nettv_import SET job_done = 1, job_done_at = ".time()." WHERE id_import = ".(int)$d['id_import'];
			Db::query($q);
			
			//$mediaSource = json_encode( array('video' => array($videoTarget), 'images' => array($thumb1, $thumb2, $thumb3) );
			$mediaSource = json_encode( array('video' => array($videoTarget), 'images' => array(null, null, null)) );
			$videoTitle = $targetFile;
			
			$id_item = self::checkItem($d['id_import']);
			
			if($id_item == 0)
			{
				$q = "
				INSERT INTO "._SQLPREFIX_."_nettv_show_items 
				(title, description, id_public, id_import, media, type, format, date_public, date_ins) 
				VALUES 
				('".Db::escapeField($videoTitle)."',  '".Db::escapeField($mediaSource)."', 1, ".(int)$d['id_import'].", '".Db::escapeField($mediaSource)."', 'dvd-video','".Db::escapeField('720x576')."', ".time().", ".time()." )
				";
			}else{
				$q = "
				UPDATE "._SQLPREFIX_."_nettv_show_items SET
				 title = '".Db::escapeField($videoTitle)."', 
				 media = '".Db::escapeField($mediaSource)."', 
				 type = 'dvd-video', 
				 format = '".Db::escapeField('720x576')."', 
				 date_public = ".time().", 
				 date_upd = ".time()." 
				 WHERE id_item = ".$id_item."
				 ";
			}
			Db::query($q);
			self::deletePID(0);
			
			return false;
		}else{
			
			$log = '"'.date(self::getLng('date_time_precise'), time()).'";"FILE FAILED: '.$d['description'].'"'."\n";
			self::log($log);
			
			$q = "UPDATE "._SQLPREFIX_."_nettv_import SET job_done = -2, job_done_at = ".time()." WHERE id_import =".(int)$d['id_import'];
			Db::query($q);
			
			self::deletePID(0);
			
			return false;
		}
	}else{
		
		$pidFile = System::root().'/'.self::getCfg('pidDir').'/encoder.'.$t.'.pid';
		$findFile = file_get_contents($pidFile);
		$_es = json_decode(trim($findFile));
		
		$_file = basename((string)$_es->file);
		$_id_import = (string)$_es->id_import;
		
		$log = '"'.date(self::getLng('date_time_precise'), time()).'";"JOB '.$_id_import.' IN PROGRESS (media '.$_file.')"'."\n";
		self::log($log);
		
		return false;
	}
	
	return true;
}

public static function checkItem($id_import){
	$q = "SELECT id_item, id_import FROM "._SQLPREFIX_."_nettv_show_items WHERE id_import =".(int)$id_import;
	$qq = Db::result($q);
	return count($qq)>0 ? $qq[0]['id_item']: 0;
}

public static function fixThumbSize($file, $width){
	$image = new SimpleImage();
	$image->load($file);
	$image->resizeToWidth($width);
	$image->save($file);
	umask(0000);
	chmod($file, 0777);
}

public static function encoderControl(){
	$q = "SELECT * FROM "._SQLPREFIX_."_nettv_import WHERE job_done IN(-1,0) AND date_ins < ".(time()-30)." ORDER BY id_import";
	$qq = Db::result($q);
	
	//echo count($qq);
	//$log = '"'.date(self::getLng('date_time_precise'), time()).'";"ITEMS:'.count($qq).'"'."\n";
	//self::log($log);
	
	foreach($qq as $d){
		
		$fCheck = explode("|", $d['description']);
		
		$ext = count($fCheck) < 2 ? strtolower(System::extension(basename($d['description']))): strtolower(System::extension(basename($fCheck[0])));
		
		$videoTypes = self::getCfg('videoFile');
		$imageTypes = self::getCfg('imageFile');
		$audioTypes = self::getCfg('audioFile');
		$otherTypes = self::getCfg('otherFile');
		$dvdTypes = self::getCfg('dvdFile');
		
		if(in_array($ext, $videoTypes) && count($fCheck) < 2){
			
			$log = '"'.date(self::getLng('date_time_precise'), time()).'";"CHECK FILE OUTER: '.$d['description'].'"'."\n";
			self::log($log);
			
			if(file_exists($d['description']))
			{
				//$log = '"'.date(self::getLng('date_time_precise'), time()).'";"TRY: '.$d['id_import'].' '.$d['title'].'"'."\n";
				//self::log($log);
				
				$res = self::encodeVideo($d);
				if($res === false){
					//echo "Aborting list, previous job in progress \n";
					break;
				}
			}else{
				$log = '"'.date(self::getLng('date_time_precise'), time()).'";"CHECK FILE OUTER FAIL: '.$d['description'].'"'."\n";
				self::log($log);
				
				$q = "UPDATE "._SQLPREFIX_."_nettv_import SET job_done = -2, job_done_at = ".time()." WHERE id_import =".(int)$d['id_import'];
				Db::query($q);
			}
		}
		
		if(in_array($ext, $imageTypes)){
			self::encodeImage($d);
		}
		
		if(in_array($ext, $dvdTypes)){
			self::encodeDVDVideo($d);
		}
		
		if(in_array($ext, $audioTypes)){
			self::encodeAudio($d);
		}
		
		if(in_array($ext, $otherTypes)){
			self::encodeOther($d);
		}
	}
}

public static function parseProgressFileV1($t = 0){
	$progressFile = System::root().'/'.self::getCfg('pidDir').'/encoder.'.$t.'.progress';
	$pidFile = System::root().'/'.self::getCfg('pidDir').'/encoder.'.$t.'.pid';
	
	$data = array('progress' => 100, 'duration' => '000:00:00.0', 'current' => '000:00:00.0', 'file' => '', 'id_import' => 0);
	
	if(self::checkPID($t) === true && file_exists($progressFile))
	{
		$findFile = file_get_contents($pidFile);
		$_es = json_decode(trim($findFile));
		
		$data['file'] = basename((string)$_es->file);
		$data['id_import'] = (string)$_es->id_import;
		
		$_pgd = file_get_contents($progressFile);
		
		// Find Duration STR
		preg_match('/Duration:([^,]+)/', $_pgd, $matches);
		$duration = $matches[1];
		
		// Find last progresss line with time
		$_pgda = explode("\r", $_pgd);
		
		$i = count($_pgda) - 2;
		$lastLine = $_pgda[$i];
		
		$setMark = preg_match('/time=([^ ]+)/', $lastLine, $matches2);
		
		$i2 = count($matches2)>1 ? count($matches2) - 1: -1;
		$timeMark = $i2 > -1 ? $matches2[$i2]: $duration;
		
		//return $timeMark;
		
		$realDuration = (int)@self::durationToInt($duration);
		$realTime = (int)@self::durationToInt($timeMark);
		
		$data['duration'] = $duration;
		$data['current'] = $timeMark;
		
		if($realDuration > $realTime){
			$progress = round(($realTime/($realDuration/100)), 2);
			$data['progress'] = $progress;
		}
	}
	
	return json_encode($data);
}

public static function durationToStr($duration, $noFrames = false){
	$tm = explode(".", (float)$duration);
	
	$h = ($tm[0] / 60) / 60;
	$m = ($tm[0] - (floor($h)*60*60)) / 60;
	$s = ($tm[0] - (floor($h)*60*60) - (floor($m)*60));
	
	$s = count($tm)<2 || $noFrames === true ? $s.'.0': round((float)$s.'.'.$tm[1], 2);
	
	return str_pad(floor($h), 2, "0", STR_PAD_LEFT).':'.str_pad(floor($m), 2, "0", STR_PAD_LEFT).':'.str_pad($s, 2, "0", STR_PAD_LEFT);
}

public static function durationToInt($duration){
	$tm = explode(":", $duration);
	
	$h = (int)$tm[0]*3600; 
	$m = (int)$tm[1]*60;
	$s = (int)$tm[2];
	
	return $h + $m + $s;
}

public static function log($msg){
	$logFile = System::root().'/'.self::getCfg('pidDir').'/encoder.0.log';
	file_put_contents($logFile, $msg, FILE_APPEND | LOCK_EX);
}

public static function createPID($content, $thread = 0){
	$pidFile = System::root().'/'.self::getCfg('pidDir').'/encoder.'.$thread.'.pid';
	file_put_contents($pidFile, $content);
}

public static function deletePID($thread = 0){
	$pidFile = System::root().'/'.self::getCfg('pidDir').'/encoder.'.$thread.'.pid';
	if(file_exists($pidFile))
	{
		unlink($pidFile);
	}
}

public static function checkPID($thread = 0){
	$pidFile = System::root().'/'.self::getCfg('pidDir').'/encoder.'.$thread.'.pid';
	return file_exists($pidFile);
} 

public static function isImported($sourceFile){
	$q = "SELECT id_import, title FROM "._SQLPREFIX_."_nettv_import WHERE description LIKE '".Db::escapeField($sourceFile)."' LIMIT 1";
	$qq = Db::result($q);
	return count($qq) == 1 ? true: false;
}

public static function probeMedia($sourceFile){
	$probeInfoCommand = 'ffprobe -i "'.$sourceFile.'" -print_format json -show_format -show_streams';

	ob_start();
	passthru($probeInfoCommand, $commandRes);
	$commandRes = ob_get_contents();
	ob_end_clean();
	
	return $commandRes;
}

public static function saveImport($sourceFile, $specialCommand = false){
	
	if(self::isImported($sourceFile) === false)
	{
		$title = basename($sourceFile);
		
		$commandRes = self::probeMedia($sourceFile);
		
		// SPECIAL COMMAND OVERRIDE
		if($specialCommand === true){
			
			$v = explode('|', $sourceFile);
			$index = count($v) - 1;
			$v = explode('/', dirname($v[$index]));
			$index = count($v) - 1;
			
			$title = $v[$index];
		}
		
		$q = "INSERT INTO "._SQLPREFIX_."_nettv_import (title, description, data, date_ins) VALUES ('".Db::escapeField($title)."', '".Db::escapeField($sourceFile)."', '".Db::escapeField($commandRes)."', ".time().")";
		Db::query($q);
	}
}

// PROBE media files
public static function mediaSurvey($dir = null){
	
	$dirSource = is_null($dir) ? System::root().'/'.self::getCfg('importDir'): $dir;
	$res =  $dirSource.' ';
	
	if(file_exists($dirSource) && is_dir($dirSource))
	{
		$oDir = opendir($dirSource);
		
		while(false!==($dObj = readdir($oDir)))
		{
			$sourceFile = $dirSource.'/'.$dObj;
			
			// read file
			if(is_file($sourceFile)){
				
				$_s = stat($sourceFile);
				$_mt = $_s['mtime'];
				
				if($_mt < ( time() - self::getCfg('fileLoadStart')) )
				{
					$res .=  $sourceFile;
					self::saveImport($sourceFile);
				}
			}
			// subdir
			if($dObj != "." && $dObj != ".." && is_dir($dirSource.'/'.$dObj))
			{
				self::mediaSurvey($dirSource.'/'.$dObj);
			}
		}
		closedir($oDir);
	}
}

// PROBE DVD sources
public static function dvdSurvey($dir = null, $searchForVob = false){
	
	$dirSource = is_null($dir) ? System::root().'/'.self::getCfg('importDVDDir'): $dir;
	$res =  $dirSource.' ';
	//echo '---';
	if(file_exists($dirSource) && is_dir($dirSource))
	{
		$oDir = opendir($dirSource);
		$myVOBs = array();
		
		while(false!==($dObj = readdir($oDir)))
		{
			$sourceFile = $dirSource.'/'.$dObj;
			
			// probe files in subdir
			if(is_file($sourceFile) && $searchForVob === true){
				
				$res .=  $sourceFile;
				if((System::extension(basename($sourceFile)) == 'VOB' || System::extension(basename($sourceFile)) == 'vob') && ( mb_substr(basename($sourceFile), 0, 4) == 'VTS_' || mb_substr(basename($sourceFile), 0, 4) == 'vts_' || mb_substr(basename($sourceFile), 0, 4) == 'vts-' )){
					$myVOBs[] = $sourceFile;
				}
			}
			
			// probe subdir for vobs
			if($dObj != "." && $dObj != ".." && is_dir($dirSource.'/'.$dObj) && $searchForVob === false)
			{
				$_s = stat($dirSource.'/'.$dObj);
				$_mt = $_s['mtime'];
				
				if($_mt < ( time() - self::getCfg('dvdLoadStart')) )
				{
					$res = self::dvdSurvey($dirSource.'/'.$dObj, true);
					//echo 'concat:'.implode('|', $res).'';
					self::saveImport(implode('|', $res), true);
				}
			}
		}
		closedir($oDir);
		
		if($searchForVob === true){
			sort($myVOBs);
			return $myVOBs;
		}
	}
}



}
?>
