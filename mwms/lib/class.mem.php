<?php
/**
* class.mem.php - WEEBO framework lib.
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
* @package Mem
* @author Jan Prochazka aka prochor <prochor666@gmail.com>
* @copyright 2011 Jan Prochazka aka prochor <prochor666@gmail.com>
* @license http://opensource.org/licenses/mit-license.php MIT License
* @version 1.0 (2011-04-28)
* @link 
*/

class Mem{
	
public $key, $data, $keepalive, $server, $port, $output, $connection;

public function __construct(){
	$this->server = _MEMCACHESERVER_;
	$this->port = _MEMCACHEPORT_;
	$this->keepalive = 30;
	$this->key = null;
	$this->data = null;
	$this->connection = false;
	$this->output = false;
}

public function memTest(){
	$m = new Memcache;
	$this->connection = @$m->connect($this->server, $this->port, 1);
}

public function retrieve(){
	$m = new Memcache;
	$this->connection = @$m->connect($this->server, $this->port, 1);
	if($this->connection === true){
		$this->output = $m->get($this->key);
		$m->close();
	}
}

public function store(){
	$m = new Memcache;
	$this->connection = @$m->connect($this->server, $this->port, 1);
	if($this->connection === true){
		//echo 'SET key: '.$this->key;
		$m->set($this->key, $this->data, 0, $this->keepalive);
		$m->close();
	}
}

public function get(){
	$m = new Memcache;
	$this->connection = @$m->connect($this->server, $this->port, 1);
	if($this->connection === true){
		$this->output = $m->get($this->key);
		$m->close();
	}
}


}
?>
