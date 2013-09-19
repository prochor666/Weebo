<?php
$dt = new DevTool;

echo '<div id="mwms_devtool">
'.$dt->list_controls().'
</div>';

?>

<script type="text/javascript">
/* <![CDATA[ */
$(document).ready(function(){
	devtool.init();
});  
/* ]]> */
</script>
