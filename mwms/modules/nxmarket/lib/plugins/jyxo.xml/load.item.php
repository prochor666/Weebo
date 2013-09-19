<?php 
//System::dump($_POST);
require_once dirname(__FILE__).'/plugin.class.jyxo.php';

$n = new JyxoXML();

echo $n->importItemData($_POST);
?>
