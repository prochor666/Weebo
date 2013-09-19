<?php
$activeDomain = isset($_POST['active_domain']) ? $_POST['active_domain']: 0;
Registry::set('active_domain', $activeDomain);
Registry::set('cms_active_link', 0);
?>
