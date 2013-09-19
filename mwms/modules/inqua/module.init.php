<?php
$mwms_module_init = array(
'auto_script' => array('inqua.load.php'),
'admin_script' => array('view/inqua.control.php'),
'icon' => array('img/comment_accept.png'),
'lng_dir' => array('lng'),
'lib_dir' => array('lib'),

'export_dir' => 'content/inqua/csv',

'stat_memcache_timeout' => 5,

'js' => array(
				'js/inqua.core.js', 
				'js/inqua.init.js', 
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
				'css/inqua.css', 
				'../../../shared/jquery.jqplot/jquery.jqplot.min.css',
				),

'api_whitelist' => array(
		'InquaApi::vote'
	)
);
?>
