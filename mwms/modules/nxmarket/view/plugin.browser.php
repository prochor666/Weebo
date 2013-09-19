<?php
$plugin = isset($_GET['plugin']) && $_GET['plugin'] != '..' ? $_GET['plugin']: null;
$pluginContent = null;

$p = new NXMPlugins;

if( !is_null($plugin) ){
	$p->plugin = $plugin;
	$pluginContent = $p->run(); 
}

echo '
<div id="plugin-src">
	'.$p->showPlugins().'
	<div id="plugin-config"></div>
</div>
<div id="plugin-run"><div id="plugin-run-inner">'.$pluginContent.'</div></div>
';
?>

<script type="text/javascript">
/* <![CDATA[ */
$(document).ready(function(){
	
	$('#plugin').on('change', function(){
		v = $(this).val();
		if(v == '..')
		{
			clearArea();
		}else{
			//loadPlugin(v);
			document.location.href = '?module=nxmarket&sub=plugin.browser&plugin='+v;
		}
	});
	
	/*
	NxMarket.initTabs();
	$("#tabs").tabs('option', 'active', 1);
	*/
});

var loadPlugin = function(plugin){
	
	NxMarket.showPreloader( '#plugin-run-inner', 100 );
	var pluginPath = weebo.settings.AjaxCall + "require&file=/mwms/modules/nxmarket/view/plugin.load.php&plugin=" + plugin;
	var conf = {};
	
	$.ajax({
		url: pluginPath,
		type: 'POST',
		data: conf,
		dataType: 'text',
		async: true,
		cache: false,
		error: function(jqXHR, textStatus, errorThrown){
			
		},
		success: function(response) {
			$('#plugin-run-inner').html(response);
		}
	});
}

var clearArea = function(){
	$('#plugin-run-inner').html('');
}
/* ]]> */
</script>
