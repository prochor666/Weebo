<?php
$___myParamData = explode(':', $d['display_script_param']);

if(count($___myParamData) > 1)
{
	$s = new WeeboNettvRender;
	echo $s->renderShowDetail((int)$___myParamData[1]);
}

echo '<div class="cleaner"></div>';

echo '<div id="e1">'.$s->embed_1.'</div>';

echo '<div id="e2">'.$s->embed_2.'</div>';

echo '<div id="e3">'.$s->embed_3.'</div>';
?>
<script type="text/javascript">
// <![CDATA[
// fix layout
$(document).ready(function(){

if(weeboPublic.activeDocument.length > 0)
{
	$(".textbox_detail").prepend('<div class="w-social-bar"></div>');

	bSocial.loadButtons($('.w-social-bar'));
	bSocial.loadComments($(".content-wrapper"));
}

}); 
// ]]>
</script>
