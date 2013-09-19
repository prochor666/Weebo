<?php
if(!defined('_NETTV_RUN_'))
{
	define('_NETTV_RUN_', false);
}

$url = $_SERVER['REQUEST_URI'];
$urlIndex = explode('/', mb_substr($url, 1, -1));
$viewLevel = count($urlIndex);

$g = new WeeboNettvRender;

if($g->moduleOk === true)
{
	switch($viewLevel)
	{
		case 1:
			echo $g->renderArchiveList();
		break; case 2:
			echo $g->renderArchiveItems();
		break; case 3:
			echo $s->renderArchiveDetail();
		break; default:
			echo 'NO TRACE';
	}
}
?>
<script type="text/javascript">
/* <![CDATA[ */
$(document).ready(function(){

});  
/* ]]> */
</script>
