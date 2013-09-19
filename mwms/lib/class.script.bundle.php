<?php
/**
* script.bundle.class.php - WEEBO framework lib.
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
* @package Scriptbundle
* @author Jan Prochazka aka prochor <prochor666@gmail.com>
* @copyright 2011 Jan Prochazka aka prochor <prochor666@gmail.com>
* @license http://opensource.org/licenses/mit-license.php MIT License
* @version 1.2 (2011-04-28)
* @link 
*/

class ScriptBundle{
	
public $scripts, $keepalive, $apply, $finalJsScript, $finalCSSScript;

private $finalScript, $bundle, $output;
	
public function __construct(){
	$this->scripts = array();
	$this->keepalive = 3600;
	$this->apply = true;
	$this->bundle = null;
	$this->finalScript = null;
	$this->finalJsScript = 'weebo.script.bundle.js';
	$this->finalCSSScript = 'weebo.script.bundle.css';
}

public function bundleJs(){

	if($this->apply){		
		
		//$this->finalScript = System::hash(serialize($this->scripts)).'.'.$this->finalJsScript;
		$this->finalScript = $this->finalJsScript;
		
		if(Storage::isExpired($this->finalScript, $this->keepalive)){

			foreach($this->scripts as $script => $min){
				$source = @file_get_contents(Registry::get('serverdata/root').$script);
				$this->bundle .= PHP_EOL.PHP_EOL.'/* '.$this->sanitizeScriptName($script);
				$this->bundle .= $min ? ' -> minified by WEEBO engine */'.PHP_EOL: ' -> not minified by WEEBO engine */'.PHP_EOL;
				$this->packSourceJs($min, $source);
				//echo $script;
			}
		
			$this->cacheStore();
		}
		
		return PHP_EOL.'<script type="text/javascript" charset="utf-8" src="'.Registry::get('serverdata/path').'/'._GLOBALCACHEDIR_.'/'.$this->finalScript.'"></script>';	
	
	}else{	
		
		foreach($this->scripts as $script => $min){
			$this->bundle .= PHP_EOL.'<script type="text/javascript" charset="utf-8" src="'.Registry::get('serverdata/path').$script.'"></script>';
		}
		
		return $this->bundle;
	}	
}

public function bundleCss(){
	
	if($this->apply){
		
		//$this->finalScript = System::hash(serialize($this->scripts)).'.'.$this->finalCSSScript;
		$this->finalScript = $this->finalCSSScript;
		
		if(Storage::isExpired($this->finalScript, $this->keepalive)){

			foreach($this->scripts as $script => $min){
				$source = @file_get_contents(Registry::get('serverdata/root').$script);
				$source = $this->rerouteMediaUrl($script, $source);
				$this->bundle .= PHP_EOL.PHP_EOL.'/* '.$this->sanitizeScriptName($script);
				$this->bundle .= $min ? ' -> minified by WEEBO engine */'.PHP_EOL: ' -> not minified by WEEBO engine */'.PHP_EOL;
				$this->packSourceCss($min, $source);
			}
		
			$this->cacheStore();
		}

		return PHP_EOL.'<link type="text/css" rel="stylesheet" media="all" href="'.Registry::get('serverdata/path').'/'._GLOBALCACHEDIR_.'/'.$this->finalScript.'" />';
	
	}else{	
		
		foreach($this->scripts as $script => $min){
			$this->bundle .= PHP_EOL.'<link type="text/css" rel="stylesheet" media="all" href="'.Registry::get('serverdata/path').$script.'" />';
		}
		
		return $this->bundle;
	}	
}

private function rerouteMediaUrl($script, $source){
	$mediaPrefix = (string)$this->sanitizeScriptName($script, true)."/";
	
	//$mediaPrefix = mb_strlen(Registry::get('serverdata/rel'))>0 ? Registry::get('serverdata/path').$mediaPrefix: $mediaPrefix;
	$mediaPrefix = Registry::get('serverdata/root').$mediaPrefix;
	$css = Minify_CSS_UriRewriter::rewrite($source, $mediaPrefix);
	//echo nl2br(Minify_CSS_UriRewriter::$debugText);
	return $css;
}

private function sanitizeScriptName($script, $getDir = false){
	$safe = explode("/", $script);
	$i = count($safe) - 1;
	
	if($getDir){
		array_pop($safe);
		return implode("/", $safe);
	}
	
	return $safe[$i];
}

private function cacheStore(){
	Storage::cache($this->finalScript, $this->bundle, $this->keepalive);
}

private function packSourceCss($min, $source){
	
	$this->bundle .= $min ? Minify_CSS_Compressor::process($source): $source; 	
}

private function packSourceJs($min, $source){
	$this->bundle .= $min ? JSMin::minify($source): $source; 	
}

}
?>
