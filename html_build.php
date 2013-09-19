<?php
$____out = new CmsOutput;
$____data = $____out->releaseTemplate();

$templateData = $____data['collection'];

define('__HTML_BUILD_MEMTOP__', memory_get_usage(true));
define('__HTML_BUILD_MEMPEAKTOP__', memory_get_peak_usage(true));

if(file_exists($____data['template'])){
	require_once($____data['template']);
}
?>
