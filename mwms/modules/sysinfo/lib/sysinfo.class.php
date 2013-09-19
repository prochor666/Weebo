<?php
class Sysinfo{

private $status, $excluded_constants;

public function __construct(){
  $this->status = true;
  $this->excluded_constants = array(
      '_USER_',
      '_PASS_',
      '_HOST_',
      '_PGUSER_',
      '_PGPASS_',
      '_PGHOST_',
      '_PGPORT_',
      '_USINGSMTP_',
      '_ADMINCOPY_',
      '_USINGSMTP',
      '_ADMINCOPY_',
      '_ADMINMAIL_',
      '_SMTPSERVER_',
      '_SMTPPORT_',
      '_SMTPTIMEOUT_',
      '_SMTPUSER_',
      '_SMTPPASSWORD_',
      '_SMTPLOCALE_',
      '_SMTPNEWLINE_',
      '__MWMS_LOAD_BEGIN__',
      '_SYSADMINMODE_'
  );
}

public function show(){

if(_SYSADMINMODE_){
  $html = '
  <div class="ui-widget">
    <table class="log_table mwms_data_list" id="sysinfo">
     <thead>
     <tr>
      <th>'.Lng::get('sysinfo/constant').'</th>
      <th>'.Lng::get('sysinfo/constant_value').'</th>
     </tr>
     </thead>
     <tbody>
  ';

  $constants = $this->show_constants(true);
 
  foreach($constants as $k => $v){
    
    if(is_bool($v)){
      $v = $v ? Lng::get('sysinfo/enabled'): Lng::get('sysinfo/disabled');
    }elseif(is_null($v) || strlen($v)<1){
      $v = '-';
    }

    $html .= !in_array($k, $this->excluded_constants) ? '
     <tr>
      <td>'.$k.'</td>
      <td>'.$v.'</td>
     </tr>
    ': null;
  }
 
  $html .= '</tbody></table></div>';
 }else{

   $html = '<div class="mwms_deny_access">'.Lng::get('sysinfo/deny_access').'</div>';

 }
 
  return $html;
}

private function show_constants(){
 $sys = get_defined_constants(true);
 return $sys['user'];
}


private function show_web(){
  
}

}
?>
