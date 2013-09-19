<?php
$mwms_module_init = array(
'auto_script' => array('cms.load.php'),
'admin_script' => array('view/cms.control.php'),
'icon' => array('img/applications.png'),
'js' => array(
				'js/cms.core.js', 
				'js/cms.init.js', 
				'../../../shared/tinymce/jquery.tinymce.js', 
				'../../../shared/plupload/js/plupload.full.js', 
				'../../../shared/plupload/js/jquery.ui.plupload/jquery.ui.plupload.js',
				'../media/js/media.core.js', 
				'../../../shared/jwplayer/jwplayer.js',
				'../../../shared/codemirror/lib/codemirror.js', 
				'../../../shared/codemirror/mode/xml/xml.js'
				),
'css' => array(
				'css/cms.css', 
				'../media/css/media.core.css', 
				'../media/css/file.types.css', 
				'../../../shared/plupload/js/jquery.ui.plupload/css/jquery.ui.plupload.css',
				'../../../shared/codemirror/lib/codemirror.css', 
				'../../../shared/codemirror/theme/eclipse.css'
				),

'impression_timeout' => 900, // seconds to impression recount
'lng_dir' => array('lng'),
'lib_dir' => array('lib', '../../lib/ondemand', '../../lib/ondemand/phpmailer'),
'brief_size' => 300,
'rss_default' => 15,
'default_lng' => 'cs', 
'lng_list' => array('cs', 'en', 'de'),
'image_thumb_preffer_axxis' => true, // true -> size to width, false -> resize to height
'anote_image_folder' => 'annotations',
'mailer_cache' => 'content/cms/mail',
'anote_image_size' => array(
	'origWidth' => '750',
	'origHeight' => '562',
	'thWidth' => '140',
	'thHeight' => '140',
	'quality' => '90',
	'maxSize'=> '1048576'
	),
'image_size' => array(
	'origWidth' => '1280',
	'origHeight' => '1024',
	'thWidth' => '140',
	'thHeight' => '105',
	'quality' => '90',
	'maxSize'=> '1048576'
	),
'api_whitelist' => array(
	'CmsXmlSitemap::build_sitemap',
	'Cms::getMethod',
	'RSSManager::outputRSS'
	)
);
?>
