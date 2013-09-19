<?php
/**
* gui.class.php - WEEBO framework lib.
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
* @package Gui
* @author Jan Prochazka aka prochor <prochor666@gmail.com>
* @copyright 2011 Jan Prochazka aka prochor <prochor666@gmail.com>
* @license http://opensource.org/licenses/mit-license.php MIT License
* @version 1.0 (2011-04-28)
* @link 
*/

class Gui
{

private $modules, $core_modules;

public function __construct()
{
	$this->modules = Registry::get('moduledata');
	$this->dashboard_config = $this->is_site_admin() ? Registry::get('userdata/desktop'): null;
	$_COOKIE['dashboard_config'] = $this->dashboard_config;
}

/* Strucutred weird procedure */
public function load_workspace()
{

 if($this->is_site_admin()){
 
	$module_id = $this->get_module_id();

	switch($module_id){

		case 'mwms':
		case 'dashboard':

			$html = '
				<div id="mwms_core_modules_list">
					'.$this->run_custom_dashboard_content().'
				<div class="cleaner"></div>
				</div>
				<script type="text/javascript">
				/* <![CDATA[ */
				$(function() {
						$("#mwms_core_modules_list").sortable({
								items: "div.portlet",
								helper: "clone",
								cursor: "move",
								tolerance: "pointer",
								revert: true,
								opacity: 0.7,
								update: function (){
									var DashboardResult = $(this).sortable("toArray");
									weebo.saveDashboardConfig(DashboardResult);
								}
						});

							 $(".portlet").addClass("ui-widget ui-widget-content ui-helper-clearfix ui-corner-all")
									.find(".portlet-header")
											.addClass("ui-widget-header ui-corner-all")
											.end();
					  
						$("#mwms_core_modules_list").disableSelection();
				});
				/* ]]> */
				</script>
			';

		break; default:

			$html = '
				<div id="mwms_module_run">
					'.$this->load_module_admin($module_id).'
				</div>
			';
		}

	}else{
		return Login::show_admin_login_form();
	}

	return $html;
}

public function load_header(){
  
	if($this->is_site_admin()){
		$module_id = $this->get_module_id();
		$html = $module_id == 'mwms' ? Login::show_login_info().$this->show_module_widget(): Login::show_login_info().$this->show_module_widget();
		return $html;
	}
	
	return null;  
}

public function load_dashboard(){
	return $this->is_site_admin() ? $this->load_dashboard_content(): null;
}

public function load_footer(){
	$html = '&copy; '.date('Y', time()).' '._WEEBOSITETITLE_.' | '.System::load_time().' s | '.mb_internal_encoding();
	return $html;
}


public function load_modules(){
  
	$html = null;
	foreach($this->modules as $module_id => $core_module){
	  
		$module_name = Lng::get($module_id.'/mwms_module_name'); 
		$module_description = array_key_exists('mwms_module_description', Lng::get($module_id)) ? Lng::get($module_id.'/mwms_module_description'): null; 
		$module_version = array_key_exists('mwms_module_version', Lng::get($module_id)) ? '<span class="portlet-version">'.Lng::get($module_id.'/mwms_module_version').'</span>': null;
		$html .= '
					<div class="portlet" id="'.$module_id.'">
						<div class="portlet-header">
							<a href="'.System::serial_uri(array('module' => $module_id), true).'">
								<img src="'.Registry::get('serverdata/site').'/'.$this->show_icon($module_id).'" alt="'.$module_id.'" />
								<span>'.$module_name.'</span>'.$module_version.'
							</a>
						</div>
						<div class="portlet-description">'.$module_description.'</div>
					</div>
				';
	}

	return $html;
}

private function show_icon($module_id){

	$module = $this->modules[$module_id];

	if(array_key_exists('icon', $module) && is_array($module['icon']) && count($module['icon'])>0){
		$img = $module['module_path'].'/'.$module_id.'/'.$module['icon'][0];
		if(file_exists(System::fs_path(Registry::get('serverdata/root').'/'.$img))){
			return $img;
		}
	}
}

public function get_module_id(){
	$module_id = Registry::get('active_admin_module');
	return $module_id;
}

public function load_module_admin($module_id){
	if($this->is_site_admin()){
		$module = Registry::get('moduledata/'.$module_id);
		System::lib_call($module['module_path'].'/'.$module_id.'/'.$module['admin_script'][0]);
	}else{
		System::redirect(System::app_root().'/?module=mwms');
	}
}

private function run_custom_dashboard_content(){

	if($this->is_site_admin() && is_array($this->dashboard_config)){

		$html = null;
		foreach($this->dashboard_config as $module_id){
			
			if(array_key_exists($module_id, $this->modules)){
			
				$module = Registry::get('moduledata/'.$module_id);
				$module_name = Lng::get($module_id.'/mwms_module_name');
				$module_description = array_key_exists('mwms_module_description', Lng::get($module_id)) ? Lng::get($module_id.'/mwms_module_description'): null;
				$module_version = array_key_exists('mwms_module_version', Lng::get($module_id)) ? '<span class="portlet-version">'.Lng::get($module_id.'/mwms_module_version').'</span>': null;
				$html .= '
							<div class="portlet" id="'.$module_id.'">
								<div class="portlet-header">
									<a href="'.System::serial_uri(array('module' => $module_id), true).'">
										<img src="'.Registry::get('serverdata/site').'/'.$this->show_icon($module_id).'" alt="'.$module_id.'" />
										<span>'.$module_name.'</span>'.$module_version.'
									</a>
								</div>
								<div class="portlet-description">'.$module_description.'</div>
							</div>
						';
			}	
		}
		return $html;

	}

  return null;
}

public function show_module_widget(){
  
	$html = '<div id="module_widget">';
	foreach($this->modules as $module_id => $core_module){

		$module_name = Lng::get($module_id.'/mwms_module_name');
		
		$ch = is_array($this->dashboard_config) && in_array($module_id, $this->dashboard_config) ? 'checked="checked"': null;
	  
		$html .= '
			<label for="widget_'.$module_id.'">
				<img src="'.Registry::get('serverdata/site').'/'.$this->show_icon($module_id).'" alt="'.$module_id.'" />
				'.$module_name.'
				<input type="checkbox" value="'.$module_id.'" id="widget_'.$module_id.'" name="'.$module_id.'" class="module_item" '.$ch.' />
			</label>
		';
	}

	return $html.'
		'.$this->getLngSet().'
		</div>
		<script type="text/javascript">
		/* <![CDATA[ */
		$(function() {
			$("div#module_widget input").each(
			function(){
				$(this).change(
					function(){
						var widgetResult = $("div#module_widget input:checked");
						var newDashboardResult = [];
						for (var i = 0; i < widgetResult.length; i++) {
							newDashboardResult.push( $(widgetResult[i]).val() );
						}
						weebo.saveDashboardConfigFromMain(newDashboardResult);
					});	
			});
		
		$(".admin_lng_set_widget").on("change", function(){
				weebo.saveSystemLng( $(this).val() );
			});
		});
		/* ]]> */
		</script>
	';
}

public function save_system_lng($lng){
	Login::createConfigXML($this->dashboard_config, Registry::get('userdata/autorun'), $lng);
}

private function getLngSet(){
	$html = '<select class="admin_lng_set_widget" name="admin_lng_set_widget">';
	foreach(Lng::get('system/active_lng_set') as $lng => $title){
		$html .= '<option value="'.$lng.'" '.Validator::selected($lng, Registry::get('userdata/lng')).'>'.$title.'</option>';
	}
	$html .= '</select>';
	return $html;
}

private function load_dashboard_content(){
	if($this->is_site_admin()){
		$board = new Dashboard;
		if($this->get_module_id()!= 'mwms' && $this->get_module_id()!= 'dashboard'){ $board->add(); }
		return $board->show();
	}
}


/* n00b wrappers */
private function is_site_root(){
	return Login::is_site_root();
}

private function is_site_admin(){
	return Login::is_site_admin();
}


}
?>
