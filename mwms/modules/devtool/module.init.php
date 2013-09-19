<?php
$mwms_module_init = array(
	'auto_script' => array('devtool.load.php'),
	'admin_script' => array('view/devtool.control.php'),
	'icon' => array('img/process.png'),
	'js' => array(
					'../../../shared/codemirror/lib/codemirror.js', 
					'../../../shared/codemirror/addon/edit/matchbrackets.js',
					'../../../shared/codemirror/mode/htmlmixed/htmlmixed.js',
					'../../../shared/codemirror/mode/xml/xml.js',
					'../../../shared/codemirror/mode/javascript/javascript.js',
					'../../../shared/codemirror/mode/css/css.js',
					'../../../shared/codemirror/mode/clike/clike.js',
					'../../../shared/codemirror/mode/php/php.js',
					'js/devtool.core.js', 
				),
	'css' => array(
					'css/devtool.core.css', 
					'../../../shared/codemirror/lib/codemirror.css', 
					'../../../shared/codemirror/theme/eclipse.css'
				),
	'lng_dir' => array('lng'),
	'lib_dir' => array('lib'),
	'api_whitelist' => array('DevTool::get_php_source', 'DevTool::delete_php_source', 'DevTool::js_packer', 'DevTool::php_run_test', 'DevTool::to_hash', 'DevTool::date_to_int', 'DevTool::int_to_date', 'DevTool::process_url', 'DevTool::crypt_performance_test')
);
?>
