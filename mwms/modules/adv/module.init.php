<?php
$mwms_module_init = array(
'auto_script' => array('adv.load.php'),
'admin_script' => array('view/adv.control.php'),
'icon' => array('img/globe_computer.png'),
'lng_dir' => array('lng'),
'lib_dir' => array('lib'),

'export_dir' => 'content/adv/export',

'stat_memcache_timeout' => 60,

'js' => array(
				'js/adv.core.js', 
				'js/adv.init.js', 
				'../../../shared/plupload/js/plupload.full.js', 
				'../../../shared/plupload/js/jquery.ui.plupload/jquery.ui.plupload.js',
				'../../../shared/swfobject/swfobject.js', 
				'../media/js/media.core.js', 
				'../../../shared/jquery.jqplot/jquery.jqplot.min.js',
				'../../../shared/jquery.jqplot/plugins/jqplot.cursor.min.js',
				'../../../shared/jquery.jqplot/plugins/jqplot.dateAxisRenderer.min.js',
				'../../../shared/jquery.jqplot/plugins/jqplot.dateAxisRenderer.min.js',
				'../../../shared/jquery.jqplot/plugins/jqplot.canvasTextRenderer.min.js',
				'../../../shared/jquery.jqplot/plugins/jqplot.canvasAxisTickRenderer.min.js',
				'../../../shared/jquery.jqplot/plugins/jqplot.categoryAxisRenderer.min.js',
				'../../../shared/jquery.jqplot/plugins/jqplot.highlighter.min.js',
				'../../../shared/jquery.jqplot/plugins/jqplot.pointLabels.min.js'
				),
'css' => array(
				'css/adv.css', 
				'../media/css/media.core.css', 
				'../media/css/file.types.css', 
				'../../../shared/plupload/js/jquery.ui.plupload/css/jquery.ui.plupload.css',
				'../../../shared/jquery.jqplot/jquery.jqplot.min.css',
				),

'api_whitelist' => array(
		'AdvApi::getPos',
		'AdvApi::route'
	)
);
?>
