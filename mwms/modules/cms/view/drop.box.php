<?php
if(isset($_GET['reg'])){
	$u = new Cms;
	$u->filterInit();

	echo isset($_GET['count']) ? $u->dropBoxCount($_GET['reg']): $u->dropBox($_GET['reg']);
}
?>
