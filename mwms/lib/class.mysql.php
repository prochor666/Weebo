<?php
/**
* mysql.class.php - WEEBO framework lib.
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
* @package Mysqldb
* @author Jan Prochazka aka prochor <prochor666@gmail.com>
* @copyright 2011 Jan Prochazka aka prochor <prochor666@gmail.com>
* @license http://opensource.org/licenses/mit-license.php MIT License
* @version 1.2 (2013-05-12)
* @link 
*/

class Mysqldb{

   private $host, $db, $user, $pass, $driver;

    /* connection */
	public function __construct($conf = null){
		
		if(
			function_exists('mysqli_init') && 
			function_exists('mysqli_connect') && 
			function_exists('mysqli_select_db') && 
			function_exists('mysqli_set_charset') && 
			function_exists('mysqli_connect_errno') && 
			function_exists('mysqli_fetch_array') && 
			function_exists('mysqli_fetch_assoc') && 
			function_exists('mysqli_fetch_row') && 
			function_exists('mysqli_fetch_object') && 
			function_exists('mysqli_real_escape_string') 
		){
			$this->MySQLDriver = 'ext/mysqli';
		}else{
			$this->MySQLDriver = 'ext/mysql';
		}
		
		
		if($conf != null && is_array($conf) && count($conf)>3 && isset( $conf['host'], $conf['db'], $conf['user'], $conf['pass'] ) ){
			$this->host =  $conf['host'];
			$this->user = $conf['user'];
			$this->pass = $conf['pass'];
			$this->db = $conf['db'];
		}else{
			$this->host = _HOST_;
			$this->user = _USER_;
			$this->pass = _PASS_;
			$this->db =   _MYDB_;
		}

	}

	/* connection */
	private function mydb_connect(){
		
		if($this->MySQLDriver == 'ext/mysqli'){
			
			$link = mysqli_init();
			if (!$link) {
				$DBLINK = false;
			}else{
				$DBLINK = mysqli_connect($this->host, $this->user, $this->pass);
				if($DBLINK !== false){
					$DBSELECT = mysqli_select_db($DBLINK, $this->db); 
					mysqli_set_charset($DBLINK, "utf8");
				}
			}
		}else{
			$DBLINK = mysql_connect($this->host, $this->user, $this->pass);
			if($DBLINK !== false){ 
				$DBSELECT = mysql_select_db($this->db,$DBLINK); 
				mysql_query("SET NAMES 'utf8'");
			}
		}
		
		return  $DBLINK;
	}

	/* test connection */
	public function test_connection(){
		if($this->MySQLDriver == 'ext/mysqli'){
			
			$link = mysqli_init();
			if (!$link) {
				$DBLINK = false;
			}else{
				$DBLINK = @mysqli_connect($this->host, $this->user, $this->pass);
				mysqli_set_charset($DBLINK, "utf8");
			}
		}else{
			$DBLINK = @mysql_connect($this->host, $this->user, $this->pass);
		}
		return $DBLINK;
	}
	
	/* test database */
	public function test_db(){
		
		$DBSELECT = false;
		
		if($this->MySQLDriver == 'ext/mysqli'){
			
			$link = mysqli_init();
			if (!$link) {
				$DBLINK = false;
			}else{
			
				$DBLINK = mysqli_connect($this->host, $this->user, $this->pass);
				$DBSELECT = @mysqli_select_db($DBLINK, $this->db); 
				mysqli_set_charset($DBLINK, "utf8");
			}
		}else{
			$DBLINK = @mysql_connect($this->host, $this->user, $this->pass);
			$DBSELECT = false;
			if($DBLINK !== false){ 
				$DBSELECT = @mysql_select_db($this->db,$DBLINK); 
				@mysql_query("SET NAMES 'utf8'");
			}
		}
		return $DBSELECT;
	}
	
	/* BASIC SQL query */
	public function query($sqlquery){
		$DBLINK = $this->mydb_connect();
		
		if($this->MySQLDriver == 'ext/mysqli')
		{
			$RESULT = !$DBLINK ? false: mysqli_query($DBLINK, $sqlquery);
			$_SESSION['weebo_sql_error'] = !$RESULT ? mysqli_connect_errno(): false;
			mysqli_close($DBLINK);
		}else{
			$RESULT = !mysql_error() ? mysql_query(trim($sqlquery), $DBLINK ): false;
			$_SESSION['weebo_sql_error'] = !mysql_error() ? false: mysql_error();
			mysql_close($DBLINK);
		}
		return $RESULT;
	}

	/* SQL result into array / object */
	public function result($sqlquery, $array_type = 'assoc'){
		$__RESULT = $this->query($sqlquery);
		$__PRECACHE = array();
		$_SESSION['weebo_sql_error_description'] = false;
		
		//echo $_SESSION['weebo_sql_error'];
		
		//echo $_SESSION['weebo_sql_error_description'];
		
		if($this->MySQLDriver == 'ext/mysqli')
		{
			if(!$_SESSION['weebo_sql_error'] && mysqli_num_rows($__RESULT)>0){
				switch($array_type){
					case "array":
						while($__TESTROW = mysqli_fetch_array($__RESULT)){
							array_push($__PRECACHE,$__TESTROW);
						}
					break; case "row":
						while($__TESTROW = mysqli_fetch_row($__RESULT)){
							array_push($__PRECACHE,$__TESTROW);
						}
					break; case "assoc": default:
						while($__TESTROW = mysqli_fetch_assoc($__RESULT)){
							array_push($__PRECACHE,$__TESTROW);
						}
				}

				mysqli_free_result($__RESULT);
			}else{
				$_SESSION['weebo_sql_error_description'] = '<pre>Q: '.trim($sqlquery).' E:'.$_SESSION['weebo_sql_error'].'</pre>';
			}
		}else{
			if(!$_SESSION['weebo_sql_error'] && mysql_num_rows($__RESULT)>0){
				switch($array_type){
					case "array":
						while($__TESTROW = mysql_fetch_array($__RESULT)){
							array_push($__PRECACHE,$__TESTROW);
						}
					break; case "row":
						while($__TESTROW = mysql_fetch_row($__RESULT)){
							array_push($__PRECACHE,$__TESTROW);
						}
					break; case "assoc": default:
						while($__TESTROW = mysql_fetch_assoc($__RESULT)){
							array_push($__PRECACHE,$__TESTROW);
						}
				}

				mysql_free_result($__RESULT);
			}else{
				$_SESSION['weebo_sql_error_description'] = '<pre>Q: '.trim($sqlquery).' E:'.$_SESSION['weebo_sql_error'].'</pre>';
			}
		}
	 return $__PRECACHE;
	}

  	/* LAST inserted ID, call it strictly after last query / in innodb transaction */
	public function get_last_id($table){
		$RESULT = $this->result('SHOW TABLE STATUS LIKE "'.$table.'"', 'assoc');
		return $RESULT !== false && count($RESULT) === 1 ? $RESULT[0]['Auto_increment'] - 1: 0;
	}
	
	public function escapeField($data){
		
		if($this->MySQLDriver == 'ext/mysqli')
		{
			$DBLINK = $this->mydb_connect();
			$RESULT = !$DBLINK ? false: mysqli_real_escape_string($DBLINK, $data);
			$_SESSION['weebo_sql_error'] = !$RESULT ? mysqli_connect_errno(): false;
			mysqli_close($DBLINK);
		}else{
			@mysql_set_charset('utf8');
			$DBLINK = $this->mydb_connect();
			$RESULT = !mysql_error() ? mysql_real_escape_string($data, $DBLINK): false;
			$_SESSION['weebo_sql_error'] = !mysql_error() ? false: mysql_error();
			mysql_close($DBLINK);
		}
		return $RESULT; 
	}

}
?>
