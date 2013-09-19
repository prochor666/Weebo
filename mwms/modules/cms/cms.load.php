<?php
if(_SYSADMINMODE_){
	Registry::set('mwms_module_path', array('cms' => Lng::get('cms/mwms_module_name') ));
}else{
	Render::definePage();
}
?>
