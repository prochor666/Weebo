<?php
$ass = new UserBrowserTemplate;

echo $ass->showBrowserMenu(); 

?>
<script type="text/javascript">
/* <![CDATA[ */
$(document).ready(function(){
	users.initTabs();
	$("#tabs").tabs('option', 'active', 0);
});
/* ]]> */
</script>

