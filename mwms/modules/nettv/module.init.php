<?php
$mwms_module_init = array(
'auto_script' => array('nettv.load.php'),
'admin_script' => array('view/nettv.control.php'),
'icon' => array('img/television.png'),
'lng_dir' => array('lng'),
'lib_dir' => array('lib'),
'image_size' => array(
	'origWidth' => 750,
	'origHeight' => 562,
	'thumbWidth' => 96,
	'thumbHeight' => 96
),

'imageSizes' => array(
	0 => array(
		'name' => 'small',
		'width' => 150,
		'height' => 84,
		'method' => 'toWidth',
	), 
	1 => array(
		'name' => 'medium',
		'width' => 760,
		'height' => 428,
		'method' => 'toWidth',
	), 
),

'fileLoadStart' => 300,
'dvdLoadStart' => 900,

'cmsPageDefault' => 10,

'mediaDir' => 'content/nettv/media',
'importDir' => 'content/nettv/import',
'importDVDDir' => 'content/nettv/dvd/in',
'exportDVDDir' => 'content/nettv/dvd/out',
'pidDir' => 'content/nettv/pid',
'logDir' => 'content/nettv/pid',

'imageFile' => array('jpg','png','gif','bmp','pcx','tga'),
'videoFile' => array('264','3g2','3gp','3gp2','3gpp','3gpp2','asf','avi','divx','flv','mgv','mj2','mjp','mjpg','mmv','mnv','mkv','mp2','mp21','mp2v','mp4','mp4v','mpe','mpeg','mpeg4','mpg','mpg2','moi','moov','mov','movie','mts','mtv','ogv','rm','vob','webm','wmv','m4v'),
'audioFile' => array('mp3','wav','oga','wma'),
'otherFile' => array('pdf'),
'dvdFile' => array('vob','bup','ifo','sub','idx'),

'js' => array(
				'js/nettv.core.js', 
				'js/nettv.init.js', 
				'../../../shared/tinymce/jquery.tinymce.js', 
				'../../../shared/plupload/js/plupload.full.js', 
				'../../../shared/plupload/js/jquery.ui.plupload/jquery.ui.plupload.js',
				'../../../shared/swfobject/swfobject.js', 
				'../media/js/media.core.js', 
				'../../../shared/jwplayer/jwplayer.js',
				),
'css' => array(
				'css/nettv.css', 
				'../media/css/media.core.css', 
				'../media/css/file.types.css', 
				'../../../shared/plupload/js/jquery.ui.plupload/css/jquery.ui.plupload.css',
				),

'api_whitelist' => array(
		'NettvEmbed::tvGuide',
		'NettvEmbed::mediaSurvey',
		'NettvEmbed::encoderControl',
		'NettvEmbed::dvdSurvey'
	)
);
?>
