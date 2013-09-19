<?php
$id_source = isset($_GET['id_source']) ? $_GET['id_source']: -1;

Registry::set('mediamix_id_source_active', $id_source);
?>
