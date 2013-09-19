<?php
$activeDomainLng = isset($_POST['active_domain_lng']) ? $_POST['active_domain_lng']: Registry::get('active_domain_lng');
Registry::set('active_domain_lng', $activeDomainLng);
Registry::set('cms_active_link', 0);
?>
