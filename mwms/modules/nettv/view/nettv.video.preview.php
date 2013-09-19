<?php
$file = isset($_GET['pfile']) ? $_GET['pfile']: null;

echo '
<div id="vpr">This text will be replaced</div>
';
?>
<script type="text/javascript">
jwplayer("vpr").setup({
	"skin": weebo.settings.SiteRoot + "/shared/jwplayer/skins/lulu.zip",
	"stretching": "exactfit", //uniform,fill,exactfit,bestfit,none
	"flashplayer": weebo.settings.SiteRoot + "/shared/jwplayer/player.swf",
	"autostart": true,
	"file" : weebo.settings.SiteRoot + '/<?php echo $file; ?>',
	'controlbar': 'bottom',
	'width': '700',
	'height': '400',
	'provider' : 'http',
	'bufferlength' : '10'
});
</script>
