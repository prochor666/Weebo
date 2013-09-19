<?php
$id_inquiry = isset($_GET['id_inquiry']) ? $_GET['id_inquiry']: 0;

Registry::set('inqua_id_inquiry_active', $id_inquiry);
?>
