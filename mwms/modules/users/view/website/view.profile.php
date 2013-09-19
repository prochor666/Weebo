<?php
//echo UserRender::showProfile();
$id_user = Login::get_user_id();
if($id_user>0)
{

$ass = new DataProcessXmlWizard;

$ass->input = array();
$ass->input['sourceData'] = $_POST;
$ass->input['id'] = $id_user;
$ass->input['fieldName'] = 'id_user';
$ass->input['tableName'] = '_users';
$ass->input['tableData'] = array(
	'username' => array('title' => Lng::get('users/mwms_username'), 'system_type' => 'text', 'validate' => true, 'unique' => true, 'predefined' => 0, 'size' => 50),
	'mail' => array('title' => Lng::get('users/mwms_mail'), 'system_type' => 'mail', 'validate' => true, 'unique' => true, 'predefined' => 0, 'size' => 50),
	'firstname' => array('title' => Lng::get('users/mwms_firstname'), 'system_type' => 'text', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 50),
	'lastname' => array('title' => Lng::get('users/mwms_lastname'), 'system_type' => 'text', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 50),
	'pw' => array('title' => Lng::get('users/mwms_password').';'.Lng::get('users/mwms_password_retype'), 'system_type' => 'password', 'validate' => true, 'unique' => false, 'predefined' => 0, 'size' => 5),
	//'root' => array('title' => Lng::get('users/mwms_root_col'), 'system_type' => 'method', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 11, 'default_value' => 'Users::getUserRole'),
	//'admin' => array('title' => Lng::get('users/mwms_admin_label'), 'system_type' => 'bool', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 1)
);

/*
if(Login::is_site_root()){
	$ass->input['tableData']['root'] = array('title' => Lng::get('users/mwms_root_col'), 'system_type' => 'method', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 11, 'default_value' => 'Users::getUserRole');
}
*/

//$ass->input['tableData']['admin'] = array('title' => Lng::get('users/mwms_admin_label'), 'system_type' => 'bool', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 1);

$ass->input['metaUse'] = true;
$ass->input['metaConnectId'] = 'id_connect';
$ass->input['metaTypesTableName'] = '_user_meta_types';
$ass->input['metaDataTableName'] = '_user_meta';

$ass->init();

if(isset($_POST['id_user'])){
	echo $ass->extract();
}

echo $ass->showForm();
?>
<script type="text/javascript">
/* <![CDATA[ */
$(document).ready(function(){
	
	$('button.detail_save_meta_<?php echo $ass->input['tableName'].'_'.$ass->id; ?>').click(
		function(){
			$('#form_call_<?php echo $ass->input['tableName'].'_'.$ass->id; ?>').submit();
		}
	);
	
	var pwt = $('label[for$="edit_field_<?php echo $ass->input['tableName'].'_'.$ass->id; ?>_pw"]').text().split(';');
	$('label[for$="edit_field_<?php echo $ass->input['tableName'].'_'.$ass->id; ?>_pw"]').text(pwt[0]);
	var pwr = '<label for="edit_field_<?php echo $ass->input['tableName'].'_'.$ass->id; ?>_pw_check" class="pw_confirm" style="float:left">'+pwt[1]+'</label>';
	
	$('#form_call_<?php echo $ass->input['tableName'].'_'.$ass->id; ?>').find('input:password').css({
		"float": "left",
		"width": "120px"
	}).eq(0).after(pwr);
	
	
});
/* ]]> */
</script>

<script type="text/javascript">
/* <![CDATA[ */
$(document).ready(function(){
	$('.meta_edit_cell div.warn').remove();
		
	$('div.warn').each(function()
	{
		var elem = $(this).attr('title');
		$(this).appendTo('#'+elem);
	});
	
});
/* ]]> */
</script>

<?php } ?>
