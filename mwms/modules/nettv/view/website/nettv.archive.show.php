<?php
if(!defined('_NETTV_RUN_'))
{
	define('_NETTV_RUN_', true);
}

$g = new WeeboNettvRender;

if($g->moduleOk === true)
{
	echo $g->renderArchiveItems();
?>
<script type="text/javascript">
/* <![CDATA[ */
$(document).ready(function(){
	
});  
/* ]]> */
</script>
<?php } ?>
