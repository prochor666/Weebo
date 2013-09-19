<?php
$mf = new MediaDisplay;

if(isset($_POST['file'])){
		echo $mf->file_view($_POST['file']);
}else{
	echo 'NO LINK';
}
?>
