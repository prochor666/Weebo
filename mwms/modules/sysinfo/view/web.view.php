<?php
echo '<div class="module-header">'.Lng::get('sysinfo/module_name').'</div>';

$sysinfo = new Sysinfo;
echo $sysinfo->show();
?>
