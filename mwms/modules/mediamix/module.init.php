<?php
$mwms_module_init = array(
'auto_script' => array('mm.load.php'),
'admin_script' => array('view/mediamix.control.php'),
'icon' => array('img/rss.png'),
'lng_dir' => array('lng'),
'lib_dir' => array('lib'),
'lib_dir' => array('lib','lib/plugins'), // if used without cms module
'rssData' => 'content/mediamix/data',
'mediaDir' => 'content/mediamix/media',
'dumpLifetime' => 10,
'loadOnDemand' => true,
'js' => array(
				'js/mm.core.js', 
				'js/mm.init.js', 
				'../../../shared/jwplayer/jwplayer.js',
				),
'css' => array(
				'css/mm.css', 
				),

'dload_templates' => array(
	//'Custom' => array('name' => 'Custom', 'source' => '', 'referer' => 'none'),
	'DirectLinks' => array('name' => 'Direct links', 'source' => 'a', 'referer' => 'none'),
	'ImageLinks' => array('name' => 'Image links', 'source' => 'a[href$=jpg], a[href$=gif], a[href$=png]', 'referer' => 'none'),
	'DirectImages' => array('name' => 'Images', 'source' => 'img', 'referer' => 'none'),
),

'api_whitelist' => array(
		'MediaMixEmbed::rssDbSaveAll',
		'MediaMixEmbed::outputRSS',
		'MediaMixEmbed::api',
		'MediaMixEmbed::apiLastItem',
		'MediaMixEmbed::dload',
		'MediaMixEmbedCustom::getarticleItem1'
	)
);
?>
