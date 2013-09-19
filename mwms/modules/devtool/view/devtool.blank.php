<?php
$f = isset($_GET['f']) ? (string)$_GET['f']: null; 

if(!is_null($f) && file_exists($f)){
	require_once($f);
}
?>
