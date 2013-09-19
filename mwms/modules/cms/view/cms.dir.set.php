<?php
$activeDomainDirType = isset($_POST['active_domain_dir_type']) ? $_POST['active_domain_dir_type']: 'images';
Registry::set('active_domain_dir_type', $activeDomainDirType);
Registry::set('cms_active_dir', 0);
?>
