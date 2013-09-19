<?php
$frm = new FormBrowser;
echo '
<div id="cms_main">
'.$frm->showBrowserMenu().'
</div>
';
?>
<script type="text/javascript">
/* <![CDATA[ */
$(document).ready(function(){
	cms.initTabs();
});
/* ]]> */
</script>
