<?php
//$g = new WeeboNettvRender;
//echo $g->nowPlaying();
echo '<div id="tvlive-channel-wrap"><div id="tvlive-channel"></div></div>';
?>
<script type="text/javascript">
// <![CDATA[
mediaPlayer.options = {
	elementID : '#tvlive-channel',
	file : 'rtmp://stream1.nxtv.cz/nxtv/nx_low',
	width : 774,
	height : 436,
	autostart : true,
	repeat : true,
	image : '',
	live : true,
	player : "/shared/smp.osmf/StrobeMediaPlayback.swf"
}

mediaPlayer.init();

mediaPlayer.create();
// ]]>
</script>




