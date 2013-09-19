<?php
if(Login::is_site_root()){

$id_group = isset($_GET['id_group']) && (int)$_GET['id_group']>0 ? (int)$_GET['id_group']: 0;

$ass = new Group($id_group);

$ass->load();

echo $ass->show();

}
?>
