<?php
$id_user = isset($_GET['id_user']) && (int)$_GET['id_user']>0 ? (int)$_GET['id_user']: 0;
$id_user = Login::is_site_root() ? $id_user: Login::get_user_id();

$ass = new DataProcessXmlWizard;

$sourceData = $_POST;

$ass->input = array();
$ass->input['sourceData'] = $sourceData;
$ass->input['id'] = $id_user;
$ass->input['fieldName'] = 'id_user';
$ass->input['tableName'] = '_users';
$ass->input['tableData'] = array(
	'username' => array('title' => Lng::get('users/mwms_username'), 'system_type' => 'text', 'validate' => true, 'unique' => true, 'predefined' => 0, 'size' => 255),
	'mail' => array('title' => Lng::get('users/mwms_mail'), 'system_type' => 'mail', 'validate' => true, 'unique' => true, 'predefined' => 0, 'size' => 255),
	'firstname' => array('title' => Lng::get('users/mwms_firstname'), 'system_type' => 'text', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 255),
	'lastname' => array('title' => Lng::get('users/mwms_lastname'), 'system_type' => 'text', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 255),
	'pw' => array('title' => Lng::get('users/mwms_password').';'.Lng::get('users/mwms_password_retype'), 'system_type' => 'password', 'validate' => true, 'unique' => false, 'predefined' => 0, 'size' => 5),
	//'root' => array('title' => Lng::get('users/mwms_root_col'), 'system_type' => 'method', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 11, 'default_value' => 'Users::getUserRole'),
	//'admin' => array('title' => Lng::get('users/mwms_admin_label'), 'system_type' => 'bool', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 1)
);

if(Login::is_site_root()){
	$ass->input['tableData']['root'] = array('title' => Lng::get('users/mwms_root_col'), 'system_type' => 'method', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 11, 'default_value' => 'Users::getUserRole');
}

$ass->input['tableData']['admin'] = array('title' => Lng::get('users/mwms_admin_label'), 'system_type' => 'bool', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 1);

$ass->input['metaUse'] = true;
$ass->input['metaConnectId'] = 'id_connect';
$ass->input['metaTypesTableName'] = '_user_meta_types';
$ass->input['metaDataTableName'] = '_user_meta';

$ass->init();

if(isset($_POST['id_user'])){
	echo $ass->extract();
}else{
	echo $ass->showForm();
}
?>
<script type="text/javascript">
/* <![CDATA[ */
$(document).ready(function(){
	
	$('button.detail_save_meta_<?php echo $ass->input['tableName'].'_'.$ass->id; ?>').click(
		function(){
			weeboMeta.applyCallback('<?php echo $ass->input['tableName'].'_'.$ass->id; ?>', 'require&file=/mwms/modules/users/view/user.detail.process.save.php');
		}
	).button({
		icons: {
			primary: "ui-icon-circle-check",
			text: false
		}
	});
	
	var pwt = $('label[for$="edit_field_<?php echo $ass->input['tableName'].'_'.$ass->id; ?>_pw"]').text().split(';');
	$('label[for$="edit_field_<?php echo $ass->input['tableName'].'_'.$ass->id; ?>_pw"]').text(pwt[0]);
	var pwr = '<label for="edit_field_<?php echo $ass->input['tableName'].'_'.$ass->id; ?>_pw_check" class="pw_confirm">'+pwt[1]+'</label>';
	
	$('#form_call_<?php echo $ass->input['tableName'].'_'.$ass->id; ?>').find('input:password').css({
		"float": "left",
		"width": "120px"
	}).eq(0).after(pwr);
	
});
/* ]]> */
</script>


