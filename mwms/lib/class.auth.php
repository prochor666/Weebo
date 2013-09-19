<?
/**
* auth.class.php - WEEBO framework lib.
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
* @package Auth
* @author Jan Prochazka aka prochor <prochor666@gmail.com>
* @copyright 2011 Jan Prochazka aka prochor <prochor666@gmail.com>
* @license http://opensource.org/licenses/mit-license.php MIT License
* @version 1.0 (2011-04-28)
* @link 
*/

class Auth {

public $username, $id, $firstname, $lastname, $root, $userimage, $lasttime, $is_logged, $is_blocked;
private $login_pw, $session_login_string, $checktimelimit, $table, $login_name, $allow_db_request, $___config;

public function __construct(){

	$this->retest_session_variables();
	$this->session_login_string = $this->test_sql($this->session_login_string);
	$this->login_name = $this->test_sql($this->login_name);
	$this->login_pw = $this->test_sql($this->login_pw);
	$this->table = _SQLPREFIX_._USERLOGINTABLE_;
	$this->tableGroups = _SQLPREFIX_.'_user_group_assign';
	$this->login_field = _USERLOGINFIELD_;
	$this->ip = System::getClientIp();
	$this->logintm = time();
	// delka casoveho limitu v sekundach od posledniho pristupu
	$this->checktimelimit = time() + _AUTOLOGOUT_;
	$this->autologout = _AUTOLOGOUT_;
	$this->___config = array();
	$this->is_logged = $this->logged();
	//$this->is_logged = Registry::get('userdata/logged_in' );
	$this->load();
}

// prihlaseni/odmitnuti uzivatele
public function first_login(){

	$this->set_session_variables();

	if($this->allow_db_request && mb_strlen($this->login_name)>0 ){

		$new_pw = System::hash($this->login_pw);

		$query="SELECT session FROM ".$this->table." WHERE ".$this->login_field." LIKE '".Db::escapeField($this->login_name)."' AND pw LIKE '".Db::escapeField($new_pw)."' AND root>0 LIMIT 1";
		$result = Db::result($query);

		if(count($result)==1){
			// ok prilogovat
			$this->session_login_string = System::hash(uniqid(rand()));
			$query="UPDATE ".$this->table." SET session='".Db::escapeField($this->session_login_string)."', ip='".Db::escapeField($this->ip)."', lasttime = '".(int)$this->logintm."' WHERE ".$this->login_field." LIKE '".Db::escapeField($this->login_name)."' AND pw LIKE '".Db::escapeField($new_pw)."' ";
			Db::query($query);

			$_SESSION["session_login_string"]=$this->session_login_string;
			$_SESSION["login_name"]=$this->login_name;

			return true;
		}
	}

	return false;
}

private function set_session_variables($glob = null){

	$glob = $glob === null || !is_array($glob) ? $_POST: $glob;

	if(isset($glob['login_name']) && strlen($glob['login_name'])>2 && isset($glob['login_pw']) && strlen($glob['login_pw'])>3 ){
		$this->login_name = htmlspecialchars($glob['login_name']);
		$this->login_pw = htmlspecialchars($glob['login_pw']);
		$this->allow_db_request = true;
	}else{
		$this->allow_db_request = false;
	}
}

private function retest_session_variables(){
	
	if(isset($_SESSION['login_name']) && mb_strlen($_SESSION['login_name'])>2 && isset($_SESSION['session_login_string']) && mb_strlen($_SESSION['session_login_string'])==32 ){
		$this->login_name = $_SESSION['login_name'];
		$this->session_login_string = $_SESSION['session_login_string'];
		
	}else{
		$this->login_name = null;
		$this->session_login_string = null;
	}

}

// testovani zda je uzivatel jiz prihlasen
private function logged(){
	if(mb_strlen($this->login_name)>0){
		$query="SELECT * FROM ".$this->table." WHERE session='".Db::escapeField($this->session_login_string)."' AND ".$this->login_field." LIKE '".Db::escapeField($this->login_name)."' LIMIT 1";
		//$query="SELECT * FROM ".$this->table." WHERE session='".$this->session_login_string."' AND ".$this->login_field." LIKE '".$this->login_name."' AND lasttime <= ".$this->checktimelimit." LIMIT 1";
		$result = Db::result($query);
		$_SESSION['lastactivity'] = $this->logintm;
		
	}else{
		$result = array();
	}
	
	if(count($result)==1 && $result[0]['lasttime'] <= ($_SESSION['lastactivity'] + $this->autologout) ){
		$query="UPDATE ".$this->table." SET lasttime = ".(int)$this->logintm." WHERE session='".Db::escapeField($this->session_login_string)."' AND ".$this->login_field." LIKE '".Db::escapeField($this->login_name)."' ";
		Db::query($query);
		return true;
	}else{
		return false;
	}
}


private function getGroups(){
	$query="SELECT id_group FROM ".$this->tableGroups." WHERE id_user='".(int)$this->id."' ORDER BY id_group";
	$result = Db::result($query);
	return $result;
}

// naplneni promennych
private function load(){
	
	if(mb_strlen($this->login_name)>0){
		$query="SELECT * FROM ".$this->table." WHERE session='".Db::escapeField($this->session_login_string)."' AND ".$this->login_field." LIKE '".Db::escapeField($this->login_name)."' LIMIT 1";
		$result = Db::result($query);
	}else{
		$result = array();
	}	
	
	if(count($result) === 1){

		$data = $result[0];
		$this->id = $data['id_user'];
		$this->lasttime = $data['lasttime'];
		$this->username = $data['username'];
		$this->mail = $data['mail'];
		$this->root = $data['root'];
		$this->admin = $data['admin'];
		$data['logged_in'] = 1;
		$data['active_groups'] = $this->getGroups();
		unset($data['pw']);
	}else{

		$data = array(
			"id_user" => 0,
			"root" => 0,
			"firstname" => null,
			"lastname" => null,
			"session" => null,
			"ip" => null,
			"lasttime" => 0,
			"mail" => null,
			"username" => null,
			"userimage" => null,
			"admin" => null,
			"lng" => null,
			"lp_token" => null,
			"logged_in" => 0,
			"active_groups" => array(),
			"dashboard_config" => "<root></root>"
		);

	}
	
	$this->loadConfig($data['dashboard_config']);
	
	$data['ip'] = $this->ip;
	$data['desktop'] = $this->___config['desktop'];
	$data['autorun'] = $this->___config['autorun'];
	$data['lng'] = $this->___config['lng'];
	
	Registry::set('userdata', $data );
}

private function loadConfig($data){
	$sConf = @simplexml_load_string($data);
	
	$this->___config['desktop'] = $this->extractDesktop($sConf);
	$this->___config['autorun'] = $this->extractAutorun($sConf);
	$this->___config['lng'] = $this->extractLng($sConf);
}

private function extractDesktop($data){
	$desktopModules = array();
	
	if(is_object($data) && is_object($data->desktop) && is_object($data->desktop->module) ){
		foreach($data->desktop->module as $module){
			$desktopModules[] = (string)$module;
		}
	}
	
	return $desktopModules;
}

private function extractAutorun($data){
	return is_object($data) && is_object($data->startup) && mb_strlen((string)$data->startup)>0 ? (string)$data->startup: 'mwms';
}

private function extractLng($data){
	return is_object($data) && is_object($data->lng) && mb_strlen((string)$data->startup)>0 ? (string)$data->lng: _WEEBODEFAULTADMINLNG_;
}

// odhlaseni uzivatele
public function logout(){
		$query="UPDATE ".$this->table." SET session='".System::hash(uniqid(rand()))."' WHERE session='".Db::escapeField($this->session_login_string)."' ";
		Db::query($query);
		$this->session_login_string = System::hash(uniqid(rand()));
		$this->login_name = null;
		session_unset();
		session_destroy();
		$this->logged();
}

// no SQL inject
private function test_sql($teststring){
	$teststring=strtr($teststring," ","x");
	$teststring=strtr($teststring,"\\","x");
	//$teststring=strtr($teststring,"--","x");
	$teststring=strtr($teststring,"&","x");
	
	return $teststring;
}

}
?>
