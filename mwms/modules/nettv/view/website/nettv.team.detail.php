<?php
$___myParamData = explode(':', $d['display_script_param']);

if(count($___myParamData) > 1)
{
	$s = new WeeboNettvRender;
	echo $s->renderTeamDetail((int)$___myParamData[1]);
}
?>
<script type="text/javascript">
// <![CDATA[
// fix layout
$(document).ready(function(){

if(weeboPublic.activeDocument.length > 0)
{
	$(".textbox_detail").prepend('<div class="w-social-bar"></div>');
}

}); 
// ]]>
</script>
