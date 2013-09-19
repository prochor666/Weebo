<?php
$mf = new MediaDisplay;

if(isset($_POST['rdir'])){
	Storage::deleteDir(urldecode($_POST['rdir']));
}else{
	echo 'NO LINK';
}
?>
