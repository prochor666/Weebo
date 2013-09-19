<?php
$mf = new MediaDisplay;

if(isset($_POST['dir']) && isset($_POST['newDir'])){
	Storage::makeDir($_POST['dir'].'/'.Filter::makeUrlString(urldecode($_POST['newDir'])));
}else{
	echo 'NO LINK';
}
?>
