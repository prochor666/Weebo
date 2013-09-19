<?php
/**
* class.cache.php - WEEBO framework lib.
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
* @package Cache
* @author Jan Prochazka aka prochor <prochor666@gmail.com>
* @copyright 2011 Jan Prochazka aka prochor <prochor666@gmail.com>
* @license http://opensource.org/licenses/mit-license.php MIT License
* @version 1.0 (2011-04-28)
* @link 
*/

class Cache{
	
public $storage, $data, $keepalive, $forceRewrite, $cacheFilename, $sourceFilename;	

private $meta, $dirMask;
	
public function __construct(){
	$this->storage = Registry::get('serverdata/root').'/'._GLOBALCACHEDIR_;
	$this->keepalive = 86400;
	$this->meta = null;
	$this->data = null;
	$this->sourceFilename = null;
	$this->cacheFilename = null;
	$this->dirMask = '0777';
}

public function compare(){
	
	if(is_null($this->sourceFilename)){
		return $this->data;
	}

	$this->cacheFilename = $this->storage.'/'.$this->sourceFilename;
	
	$this->getMeta();

	if(!is_array($this->meta) || !array_key_exists('mtime',$this->meta) || $this->isExpired() || $this->meta['size']<1 || $this->keepalive == 0 ){
	   $this->cacheStore(); 
	}
	
	return $this->cacheRead();
}

public function isExpired(){

	if(is_null($this->cacheFilename)){
		$this->cacheFilename = $this->storage.'/'.$this->sourceFilename;
		$this->getMeta();
	}

	$time_file_expires = !is_null($this->meta) ? ((int)$this->keepalive + $this->meta['mtime']): 0;
	$time_system = time();

	return ((int)$this->keepalive + $this->meta['mtime']) < time() ? true: false;
}

private function cacheStore(){
	file_put_contents($this->cacheFilename, $this->data);
	umask(0000); 
	@chmod($this->cacheFilename,0777);
}

public function cacheDirectRead(){
	return file_get_contents($this->storage.'/'.$this->cacheFilename);
}

private function cacheRead(){
	return file_get_contents($this->cacheFilename);
}

private function getMeta(){
	$this->meta = file_exists($this->cacheFilename) && !is_dir($this->cacheFilename) ? @stat($this->cacheFilename): null;
}

}
