<?php
$id_import = isset($_GET['id_import']) ? (int)$_GET['id_import']: 0;
$job_done = isset($_GET['job_done']) ? (int)$_GET['job_done']: 0;

if($id_import>0)
{
	$sp = new ShowItemBrowser;
	$sp->recheckImportItem($id_import, $job_done);
	echo $sp->lng['tv_job_state_job_done'][$job_done];
}
?>
