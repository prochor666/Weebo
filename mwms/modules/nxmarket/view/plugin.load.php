<?php
ignore_user_abort(true);
set_time_limit(0);

$plugin = isset($_GET['plugin']) && $_GET['plugin'] != '..' ? $_GET['plugin']: null;

$p = new NXMPlugins;

if( !is_null($plugin) ){
	$p->plugin = $plugin;
	echo $p->run(); 
}
?>
