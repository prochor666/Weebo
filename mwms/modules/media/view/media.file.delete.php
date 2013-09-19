<?php
$mf = new MediaDisplay;

if(isset($_POST['file'])){
	Storage::deleteFile(urldecode($_POST['file']));
}else{
	echo 'NO LINK';
}
?>
