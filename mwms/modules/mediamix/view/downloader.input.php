<?php
$tplGet = isset($_GET['tpl']) ? $_GET['tpl']: null;

if(!is_null($tplGet))
{
	$d = new Dload;
	$d->template = $d->tpl[$tplGet];
	$d->template['key'] = $tplGet;
	
	echo $d->showTemplateInput();
}
?>
<script type="text/javascript">
/* <![CDATA[ */
$(document).ready(function(){
	
	$('#tpl-run').on('click', function()
	{
		var targetOut = $('#tpl-output');
		var tplURI = weebo.settings.AjaxCall + "require&file=/mwms/modules/mediamix/view/downloader.output.php";
		
		var tplData = {
			tpluri : $('#tpl-uri').val(),
			tplkey : $('#tpl-key').val(),
			tplinput : $('#tpl-input').val(),
			tplreferer : $('#tpl-referer').val(),
			tplsuffix : $('#tpl-suffix').val(),
			tplname : $('#tpl-name').val()
		};
		
		targetOut.html('');
		MediaMix.showPreloader(targetOut, 100);
		
		$.ajax({
			url: tplURI,
			type: 'post',
			dataType: 'text',
			data : tplData,
			async: true,
			cache: false,
			success: function(response) {
				targetOut.html(response);
			},
			error: function(x, t, m) {
				targetOut.html('App error');
			}
		});
		
	}).button({
		icons : {
			primary : 'ui-icon-arrowthickstop-1-s'
		}
	});
	
});
/* ]]> */
</script>
