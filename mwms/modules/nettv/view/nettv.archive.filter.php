<?php
$id_public = isset($_GET['id_public']) ? $_GET['id_public']: 0;
$id_show = isset($_GET['id_show']) ? $_GET['id_show']: 0;

Registry::set('nettv_state_view', $id_public);
Registry::set('nettv_id_show_active', $id_show);
?>
