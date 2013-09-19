<?php
if(array_key_exists('imageSet', $_POST)===true && array_key_exists('id_article', $_POST)===true){
	$imgs = json_encode($_POST['imageSet']);
	Db::query("UPDATE "._SQLPREFIX_."_mm_meta SET value = '".Db::escapeField($imgs)."' WHERE tag LIKE 'imageSet' AND id_article = ".(int)$_POST['id_article']." LIMIT 1");
	echo 'OK';
}else{
	echo 'FAIL';
}
?>
