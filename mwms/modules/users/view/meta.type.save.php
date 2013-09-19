<?php
if(Login::is_site_root()){
 
$id_meta = isset($_POST['id_meta']) && (int)$_POST['id_meta']>0 ? (int)$_POST['id_meta']: 0;

$ass = new Meta($id_meta);

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
