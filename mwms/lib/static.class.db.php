<?php
/**
* db.class.php - WEEBO framework lib.
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
* @package Db
* @author Jan Prochazka aka prochor <prochor666@gmail.com>
* @copyright 2011 Jan Prochazka aka prochor <prochor666@gmail.com>
* @license http://opensource.org/licenses/mit-license.php MIT License
* @version 1.0 (2011-04-28)
* @link 
*/

class Db{

final public function __construct() { throw new WeeboException("Cannot instantiate static class!"); }
final public function __clone() { throw new WeeboException("Cannot clone static class!"); }

public static function final_items($id_list=array(), $from = 0, $to = 0){
  $new_dataset = array();

  if(is_array($id_list) && count($id_list)>0 && $to > 0){
     $new_dataset = array_slice($id_list, $from, $to);
     unset($id_list);
  }
  
return $new_dataset;
}

/* use automemcache */
public static function memAuto($query, $timeout = 10){
	$result = self::memGet(System::hash($query));
	if($result === false){ Registry::set('memc', 'No'); }else{ Registry::set('memc', 'Yes'); }
	$result = $result === false ? self::memSet(System::hash($query), self::result($query), $timeout): $result;
	return $result;
}

/* Use memcache manualy */
public static function memGet($key){
	
	$m = new Mem;
	$m->key = $key;

	if(_MEMCACHEENABLED_ === true && _MEMCACHESERVEROK_ === true)
	{
		$m->retrieve();
		return $m->connection === true ? $m->output: false;
	}
	
	return false;
}

public static function memSet($key, $data, $timeout = 10){
	
	$m = new Mem;
	$m->key = $key;
	$m->data = $data;
	$m->keepalive = $timeout;
	if(_MEMCACHEENABLED_ === true && _MEMCACHESERVEROK_ === true)
	{
		$m->store();
	}
	
	return $data;
}

public static function test_connection($conf = null, $driver = _SYSTEMDBDRIVER_){
	$db = new $driver($conf);
	return @$db->test_connection();
}

public static function test_db($conf = null, $driver = _SYSTEMDBDRIVER_){
	$db = new $driver($conf);
	return @$db->test_db();
}

public static function result($sqlquery, $array_type = 'assoc', $conf = null, $driver = _SYSTEMDBDRIVER_){
  $db = new $driver($conf);
  return @$db->result($sqlquery, $array_type );
}

public static function query($sqlquery, $conf = null, $driver = _SYSTEMDBDRIVER_){
  $db = new $driver($conf);
  return @$db->query($sqlquery);
}

public static function get_last_id($table, $conf = null, $driver = _SYSTEMDBDRIVER_){
  $db = new $driver($conf);
  return @$db->get_last_id($table);
}

public static function row_exists($table, $sql_statement, $conf = null, $driver = _SYSTEMDBDRIVER_){
	 /*
	 (string) $table --prefix
	 (int) $sql_statement = WHERE id = (int)$xxx
	 */
    $data = self::result("SELECT * FROM "._SQLPREFIX_.$table." ".$sql_statement." ", $conf, $driver);
    return $data;
}

public static function row_delete($table, $sql_statement, $conf = null, $driver = _SYSTEMDBDRIVER_){
	 /*
	 (string) $table --prefix
	 (int) $sql_statement = WHERE id = (int)$xxx
	 */
    $data = self::result("DELETE FROM "._SQLPREFIX_.$table." ".$sql_statement." ", $conf, $driver);
    return $data;
}

public static function row_affect($table, $cols_and_values, $id = 0, $conf = null, $driver = _SYSTEMDBDRIVER_){
    $sql = is_numeric($id) && $id>0 ?  "UPDATE ": "INSERT INTO ";
    $sql .= _SQLPREFIX_.$table." SET ";

    $i = 0;
    foreach($cols_and_values as $col => $value){
      $sql .= $i==0 ? $col." = '".htmlspecialchars($value)."' ": ", ".$col." = '".self::escapeField(trim($value))."' ";
      $i++;
    }
    $sql .= is_numeric($id) && $id>0 ? " WHERE id = '".$id."'": null;
    //echo $sql;
    self::query($sql, $conf, $driver);
}

public static function escapeField($data, $conf = null, $driver = _SYSTEMDBDRIVER_){
  $db = new $driver($conf);
  return $db->escapeField($data);
}

}

?>
