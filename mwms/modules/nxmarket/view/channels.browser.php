<?php
$mm = new SourceBrowserTemplate;
echo $mm->showBrowserMenu();
?>
<script type="text/javascript">
/* <![CDATA[ */
$(document).ready(function(){
	MediaMix.initTabs();
	$("#tabs").tabs('select', 1);
});
/* ]]> */
</script>
