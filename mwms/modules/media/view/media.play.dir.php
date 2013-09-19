<?php
$mf = new Media;

if(isset($_POST['group']))
{
	echo $mf->playDir($_POST['group']);
}
?>
