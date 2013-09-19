<?php
$sfile = array_key_exists('sfile', $_GET) ? $_GET['sfile']: null;
if(!is_null($sfile) && file_exists(Registry::get('serverdata/root').'/'.$sfile) && is_file(Registry::get('serverdata/root').'/'.$sfile))
{
header('Pragma: public');
header('Expires: 0');
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
header('Content-Description: File Transfer');
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename='.basename($sfile).';');
header('Content-Transfer-Encoding: binary');
echo file_get_contents(Registry::get('serverdata/root').'/'.$sfile);
}
?>
