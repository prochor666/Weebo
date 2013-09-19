<?php
echo '<div class="mwms_module_header">'.Lng::get('sysinfo/mwms_module_description').'</div>';
$sysinfo = new Sysinfo;
echo $sysinfo->show();
?>
