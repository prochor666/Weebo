<?php
/**
* shell.class.php - WEEBO framework lib.
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
* @package Shell
* @author Jan Prochazka aka prochor <prochor666@gmail.com>
* @copyright 2011 Jan Prochazka aka prochor <prochor666@gmail.com>
* @license http://opensource.org/licenses/mit-license.php MIT License
* @version 1.0 (2011-04-28)
* @link 
*/


class Shell{
/*
* System shell
* ver. 1.0
*/
final public function __construct() { throw new WeeboException("Cannot instantiate static class!"); }
final public function __clone() { throw new WeeboException("Cannot clone static class!"); }

public static function show(){
	
	Registry::set('shell_state', 1);
	
	$html ='
		<button id="weebo-shell-toggle">'.Lng::get('system/shell').'</button>
		<div id="weebo-shell">
		
			<div id="weebo-shell-header">
				'.Lng::get('system/shell').'@'.Registry::get('serverdata/site').'
				<button id="weebo-shell-close">'.Lng::get('system/shell_close').'</button>
			</div>
			
			<div id="weebo-shell-output">
			</div>
			
			<div id="weebo-shell-commandline">
				<input type="text" id="weebo-shell-commandline-input" name="commandline" value="" />
			</div>
		
		</div>
	';
	return $html;
}


public static function hide(){
	Registry::set('shell_state', 0);
}

public static function setHistory($time, $command){
	if(!isset($_SESSION['weebo_shell_history'])){
		$_SESSION['weebo_shell_history'] = array();	
	}
	$_SESSION['weebo_shell_history'][$time] = $command;
}


/* 
 * command interpret
 * 
*/
public static function com($ccom = null, $cparam = null){
	
	if( is_null($ccom) || mb_strlen($ccom)<1 ){
		return 'weebo >'; 	
	}
	
	//$param = !is_array($cparam) ? implode(" ", explode(",", trim($cparam))): $cparam;
	$param = !is_array($cparam) ? array(str_replace(" ", ",", trim($cparam))): $cparam;
	
	$com = 'c_'.trim($ccom);
	
	if($ccom != 'entry_text'){ self::setHistory(time(), $ccom.' '.$cparam); }
	
	$dsp = is_array($param) && count($param)>0 ? implode('', $param): (string)$param;
	
	return method_exists(__CLASS__, $com) === true ? call_user_func_array(array('self', $com), $param): Lng::get('system/shell_illegal_command').' '.$ccom.' '.$dsp;
}

/*
 * commands
 * 
*/
public static function c_entry_text($arr = null){
	$str = Lng::get('system/shell_entry');
	return $str;
}

public static function c_hide($arr = null){
	self::hide();
	$str = Lng::get('system/shell_entry');
	return $str;
}

public static function c_reset(){
	if(isset($_SESSION['weebo_shell_history'])){
		unset($_SESSION['weebo_shell_history']);	
	}
	return Lng::get('system/shell_reset'); 
}

public static function c_module($arr){
	$site = Registry::get('serverdata/site');
	
	if(is_null($arr) || mb_strlen($arr)<1){ 
		return Lng::get('system/module_not_specified');
	}
	
	$modules = Registry::get('moduledata');
	
	
	return array_key_exists($arr, $modules) || $arr == 'mwms' ? '--> '.$arr.' <script type="text/javascript">window.location.href = "'.$site.'/?module='.$arr.'";</script>': Lng::get('system/module_not_exist');
}

public static function c_logout(){
	$site = Registry::get('serverdata/site');
	return '--> logout <script type="text/javascript">window.location.href = "'.$site.'/?logout=1";</script>';
}

public static function c_history(){
	$arr = $_SESSION['weebo_shell_history'];
	
	if(count($arr)>0){
		$table = '<table>'; 

		foreach($arr as $k => $v){
			
			$val = is_array($v) ? self::arrayToTable($v): $v;
			
			$table .= '<tr><th>'.date(Lng::get('system/date_time_format_precise'), $k).' > </th><td class="cmd-input-history" title="'.Lng::get('system/shell_use_command_again').'">'.$val.'</td></tr>';	
		}
		
		$table .= '</table>';
		
		return $table;
	}
}

public static function c_help($arr = null){
	$h = Lng::get('system/shell_help');
	ksort($h);
	$str = !is_null($arr) && mb_strlen($arr)>0 && array_key_exists($arr, $h) ? $h[$arr]: self::arrayToTable($h);
	return $str;
}

public static function c_users($arr = null){
	$str = null;
	$f = is_null($arr) || mb_strlen($arr)<1 ? '*': str_replace(' ', ',',$arr);
	$o = $f == '*' ? array('id_user'): explode(' ', $arr);
	$qq = Db::result("SELECT ".$f." FROM "._SQLPREFIX_."_users ORDER BY ".$o[0]);
	
	foreach($qq as $d){
		$str .= self::arrayToTable($d);	
	}
	
	return $str;
}

public static function c_modules(){
	$str = null;
	
	$directories = array(
		'mwms/modules',
	);
	$lib_config = array();

	foreach($directories as $xdir){

		$directory =  System::fs_path($xdir);

		$md = opendir(System::root().'/'.$directory);
		$html = null;

		while(false!==($lib_id = readdir($md))){

			if($lib_id != "." && $lib_id != ".." && is_dir(System::root().'/'.$directory.'/'.$lib_id) && file_exists(System::root().'/'.$directory.'/'.$lib_id.'/module.init.php') && is_file(System::root().'/'.$directory.'/'.$lib_id.'/module.init.php')){
				array_push($lib_config, $lib_id);
			}

		}
		closedir($md);

	}
	ksort($lib_config);
	
	$str = null;	
	foreach($lib_config as $mod){
        $str .= '<div class="module">'.$mod.'</div>';		   
	}
   
	unset($lib_config);
     
	return $str;
}

public static function c_js($arr = null){
	$str = !is_null($arr) && mb_strlen($arr)>0 ? '<script type="text/javascript">'.$arr.'</script>': Lng::get('system/shell_param_mismatch');
	return $str;
}

public static function c_hello(){
	return 'Hello, how are you?';
}

public static function c_fine(){
	return 'Amazing';
}

public static function c_test(){
	return 'Weebo shell test';
}

public static function c_reg($arr = null){
	$reg = is_null($arr) || mb_strlen($arr)<1 ? Registry::readall(): Registry::get($arr);
	return is_array($reg) ? self::arrayToTable($reg): $reg;
}

public static function arrayToTable($arr){

if(count($arr)>0){
	$table = '<table>'; 

	foreach($arr as $k => $v){
		
		$val = is_array($v) ? self::arrayToTable($v): $v;
		
		$table .= '<tr><th>'.$k.' > </th><td>'.$val.'</td></tr>';	
	}
	
	$table .= '</table>';
	return $table;
}
}

	
}
?>
