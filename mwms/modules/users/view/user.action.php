<?php
$id_user = isset($_POST['id_user']) ? $_POST['id_user']: 0;
$id_group = isset($_POST['id_group']) ? $_POST['id_group']: 0;

$id_user = $id_user == 0 && isset($_GET['id_user']) && (int)$_GET['id_user']>0 ? (int)$_GET['id_user']: $id_user;
$id_group = $id_group == 0 && isset($_GET['id_group']) && (int)$_GET['id_group']>0 ? (int)$_GET['id_group']: $id_group;

$id_user = !is_array($id_user) ? array($id_user): $id_user;
$id_group = !is_array($id_group) ? array($id_group): $id_group;
$action = isset($_GET['action']) && mb_strlen($_GET['action'])>0 ? (string)$_GET['action']: null;

$ass = new DataProcessAction;

$ass->input = array();

$ass->input['id'] = $id_user;
$ass->input['AssignData'] = $id_group;
$ass->input['action'] = $action;
$ass->input['fieldName'] = 'id_user';
$ass->input['tableName'] = '_users';
$ass->input['metaUse'] = true;
$ass->input['metaConnectId'] = 'id_connect';
$ass->input['metaDataTableName'] = '_user_meta';
$ass->input['assignDataTableName'] = '_user_groups';
$ass->input['assignTableName'] = '_user_group_assign';
$ass->input['metaAssignId'] = 'id_group';

echo '<div id="user-action-init">'.$ass->initAction().'</div>';

//System::dump($_POST);

if($ass->allowSave()){

	if($action == 'del'){
		
		foreach($id_user as $id){
			
			if( array_key_exists($id, $_SESSION['user_filter_registry']['users_dropbox']) ){
				unset( $_SESSION['user_filter_registry']['users_dropbox'][$id] );
			}
			
			//echo '<script type="text/javascript">users.dropBoxRemove("'.Ajax::path().'require&file=/mwms/modules/users/view/drop.box.php&dropbox='.$id.'");</script>';
		}
		
	}
echo '
	<script type="text/javascript">
	/* <![CDATA[ */
	$(document).ready(function(){
		
		$("#tabs").tabs("option", "active", 0);
		$("#tabs").tabs("load", 0);
		$("#weebo-modal-dialog-content").dialog("close");
		//setTimeout("location.reload(true);", 200);

	});

	/* ]]> */
	</script>
	';
}
?>
