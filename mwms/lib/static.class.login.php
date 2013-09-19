<?php
/**
* login.class.php - WEEBO framework lib.
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
* @package Login
* @author Jan Prochazka aka prochor <prochor666@gmail.com>
* @copyright 2011 Jan Prochazka aka prochor <prochor666@gmail.com>
* @license http://opensource.org/licenses/mit-license.php MIT License
* @version 1.0 (2011-04-28)
* @link 
*/

class Login {

final public function __construct() { throw new WeeboException("Cannot instantiate static class!"); }
final public function __clone() { throw new WeeboException("Cannot clone static class!"); }

public static function show_admin_login_form(){
	$html = '
	<div class="mwms_login_box">
	<div id="mwms_logo"></div>
	<form id="mwms_login_form" action="'.Registry::get('serverdata/path').'/?module=mwms-login" method="post" enctype="application/x-www-form-urlencoded">
	  <label for="mwms_login_name" class="mwms_login_name">'.Lng::get('system/mwms_login_'._USERLOGINFIELD_).'<span class="login_wrap"><input type="text" size="10" value="" name="login_name" id="mwms_login_name" /></span></label>
	  <label for="mwms_login_pw" class="mwms_login_pw">'.Lng::get('system/mwms_login_pw').'<span class="password_wrap"><input type="password" size="10" value="" name="login_pw" id="mwms_login_pw" /></span></label>
	  <button type="submit" title="'.Lng::get('system/mwms_login_button').'" id="mwms_login_bt" class="button">'.Lng::get('system/mwms_login_button').'</button>
	</form>
	</div>
	';
	return $html;
}

public static function show_login_info(){
	$html = '
		<span class="mwms_user_name">'.Registry::get('userdata/username').'</span>
		<span class="mwms_client_ip" id="mwms_client_ip">'.System::getClientIp('REMOTE_ADDR').'</span>
		<button type="button" title="'.Lng::get('system/mwms_logout_button').'" id="mwms_logout_bt" onclick="document.location.href=\'?logout=1\'" class="button">'.Lng::get('system/mwms_logout_button').'</button>
	';
	return $html;
}

public static function save_dashboard_config($positions){
	$id_user = self::get_user_id();
	
	if(self::is_site_admin() && $id_user>0){
		
		$moduleOrder = explode(',', $positions);
		
		self::createConfigXML($moduleOrder);
		
		return ' <div class="mwms_dashboard_info ui-widget">
				<div class="ui-state-highlight ui-corner-all"> 
						<span class="ui-icon ui-icon-info"></span>
						'.Lng::get("system/mwms_dashboard_saved").' 
				</div>
			</div>';
	}
}

public static function save_dashboard_config_exclude($positions, $exclude){
	$id_user = self::get_user_id();
	
	if(self::is_site_admin() && $id_user>0){
		$orig = explode(',', $positions);

		foreach($orig as $key => $value){
			if($value == $exclude){ unset($orig[$key]); }
		}

		$moduleOrder = $orig;
		
		self::createConfigXML($moduleOrder);
		
		return ' <div class="mwms_dashboard_info ui-widget">
				<div class="ui-state-highlight ui-corner-all">
						<span class="ui-icon ui-icon-info"></span>
						'.Lng::get("system/mwms_dashboard_saved").'
				</div>
			</div>';
	}
}

public static function createConfigXML($moduleOrder, $moduleRun = null, $lng = null){
	$id_user = self::get_user_id();
	
	$mo = null;
	
	if(is_null($lng)){
		$lng = Registry::get('lng');
	}
	
	if(is_null($moduleRun)){
		$moduleRun = Registry::get('userdata/autorun');
	}
	
	foreach($moduleOrder as $k => $v){
		$mo .= '
				<module>'.$v.'</module>
		';
	}

	$xml = '
		<root>
			<desktop>
				'.$mo.'
			</desktop>
			<startup>'.$moduleRun.'</startup>
			<lng>'.$lng.'</lng>
		</root>
	';
	//echo "UPDATE "._SQLPREFIX_."_users SET dashboard_config = '".Db::escapeField($xml)."' WHERE id_user = ".$id_user;
	Db::query("UPDATE "._SQLPREFIX_."_users SET dashboard_config = '".Db::escapeField($xml)."' WHERE id_user = ".$id_user);
}

public static function getUseLasttime(){
	return date(Lng::get('system/date_time_format_precise'), (int)Registry::get('userdata/lasttime'));
}

public static function getActiveGroups(){
	return Registry::get('userdata/active_groups');
}

public static function get_user_id(){
	return (int)Registry::get('userdata/logged_in') === 1 ? (int)Registry::get('userdata/id_user'): 0;
}

public static function is_site_admin(){
	return (int)Registry::get('userdata/logged_in') === 1 && (int)Registry::get('userdata/admin') === 1  ? true: false;
}

public static function is_site_root(){
	return (int)Registry::get('userdata/logged_in') === 1 && (int)Registry::get('userdata/root') === 1  ? true: false;
}

public static function loadtime(){
	return date( Lng::get('system/date_time_format_precise'), time() );
}

public static function logout(){
	$a = new Auth();
	$a->logout();
}

}
?>
