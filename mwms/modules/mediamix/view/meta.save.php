<?php
if(array_key_exists('tag', $_POST)===true && array_key_exists('value', $_POST)===true && array_key_exists('id_article', $_POST)===true){
	Db::query("UPDATE "._SQLPREFIX_."_mm_meta SET value = '".Db::escapeField($_POST['value'])."' WHERE tag LIKE '".$_POST['tag']."' AND id_article = ".(int)$_POST['id_article']." LIMIT 1");
	echo 'OK';
}else{
	echo 'FAIL';
}
?>
