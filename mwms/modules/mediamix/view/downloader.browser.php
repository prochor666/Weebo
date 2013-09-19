<?php
$d = new Dload;

echo $d->showTemplates();

echo '
	<div id="tpl-set"></div>
	
	<div id="tpl-output"></div>
';
?>
<script type="text/javascript">
/* <![CDATA[ */
$(document).ready(function(){
	
	var targetContainer = $('#tpl-set');
	var targetOut = $('#tpl-output');
	
	$('#tpl-list input').on('change', function(){
		
		targetContainer.html('');
		targetOut.html('');
		MediaMix.showPreloader(targetContainer, 100);
		
		var tplURI = weebo.settings.AjaxCall + "require&file=/mwms/modules/mediamix/view/downloader.input.php&tpl=" + $(this).val();
		targetContainer.load(tplURI);
		
	});
	$('#tpl-list').buttonset();
	
});
/* ]]> */
</script>
