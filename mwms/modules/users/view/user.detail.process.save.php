<?php
$id_user = isset($_POST['id_user']) && (int)$_POST['id_user']>0 ? (int)$_POST['id_user']: 0;
$id_user = Login::is_site_root() ? $id_user: Login::get_user_id();

$ass = new DataProcessXmlWizard;

$ass->input = array();
$ass->input['sourceData'] = $_POST;
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

echo $ass->extract();

?>
<script type="text/javascript">
/* <![CDATA[ */
$(document).ready(function(){
	var allowsave = <?php echo !$ass->allowsave ? 0: 1; ?>;
	
	$('.meta_head div.warn').remove();
	
	$('div.warn').each(function()
	{
		var elem = $(this).attr('title');
		var header = $('#'+elem).parent('td').parent('tr').find('td.meta_head');
		header.append($(this));
	});
	
	if(allowsave == 1){
		var selected = $('#tabs').tabs('option', 'active');
		users.closeTab(selected);
		$("#tabs").tabs('option', 'active', 0);
		$("#tabs").tabs('load', 0);
	}
	
});
/* ]]> */
</script>
