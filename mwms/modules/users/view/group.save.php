<?php 
if(Login::is_site_root()){

$id_group = isset($_POST['id_group']) && (int)$_POST['id_group']>0 ? (int)$_POST['id_group']: 0;

$ass = new Group($id_group);

echo $ass->extract($_POST);

if($ass->allowsave){

echo '
<script type="text/javascript">
/* <![CDATA[ */
$(document).ready(function(){

	$("#weebo-modal-dialog-content").dialog("close");
	setTimeout("location.reload(true);", 300);

});

/* ]]> */
</script>
';

}

}
?>
