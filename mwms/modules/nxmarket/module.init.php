<?php
$mwms_module_init = array(
'auto_script' => array('nxmarket.load.php'),
'admin_script' => array('view/nxmarket.control.php'),
'icon' => array('img/shopping_cart.png'),
'lng_dir' => array('lng'),
'lib_dir' => array('lib'),
'dataDir' => 'content/nxmarket/data',
'pluginDir' => 'mwms/modules/nxmarket/lib/plugins',
'mediaDir' => 'content/nxmarket/media',
'deleteSourceFiles' => false,
'image_thumb_preffer_axxis' => true,
'image_size' => array(
	'origWidth' => '1280',
	'origHeight' => '1024',
	'thWidth' => '100',
	'thHeight' => '100',
	'quality' => '90',
	'maxSize'=> '1048576'
	),
'js' => array(
				'js/nxmarket.core.js', 
				'js/nxmarket.init.js', 
				'../../../shared/tinymce/jquery.tinymce.js', 
				'../../../shared/plupload/js/plupload.full.js', 
				'../../../shared/plupload/js/jquery.ui.plupload/jquery.ui.plupload.js',
				'../media/js/media.core.js', 
				'../../../shared/jwplayer/jwplayer.js',
				),
'css' => array(
				'css/nxmarket.css', 
				'../media/css/media.core.css', 
				'../media/css/file.types.css', 
				'../../../shared/plupload/js/jquery.ui.plupload/css/jquery.ui.plupload.css',
				),

'api_whitelist' => array(
		'NXMApi::loadPlugin',
		'NxMarketEmbed::loadPlugin',
	)
);
?>
