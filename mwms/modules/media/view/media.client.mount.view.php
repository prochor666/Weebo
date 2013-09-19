<?php
Registry::set('mediatree', array());

$mf = new MediaDisplay;

$mf->rootDir = _GLOBALDATADIR_;

$dir = isset($_POST['dir']) ? $_POST['dir']: $mf->rootDir;

$fileMask = isset($_GET['fileMask']) ? $_GET['fileMask']: 'all';
	
switch($fileMask){
	case 'all':
		$mf->extViewFilter = null;
	break; default:
		$mf->extViewFilter = explode(',', $fileMask);
		$mf->extViewFilter = $fileMask == '*' ? null: $mf->extViewFilter;
}

echo $mf->directoryRead(System::autoUTF($dir));
?>
<script type="text/javascript">
/* <![CDATA[ */
$(document).ready( function()
{
	/* HANDLE FILE DELETE CLICK */
	$('a.remove-file').remove();

	/* HANDLE DIR DELETE CLICK */
	$('a.remove-dir').remove();

});
/* ]]> */
</script>
