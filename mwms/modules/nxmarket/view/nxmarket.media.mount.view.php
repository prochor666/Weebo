<?php
Registry::set('mediatree', array());

$mf = new MediaDisplay;

$mf->rootDir = _GLOBALDATADIR_.'/nxmarket';

$dir = isset($_POST['dir']) ? $_POST['dir']: $mf->rootDir;

echo $mf->directoryRead(System::autoUTF($dir));

//System::dump(Registry::get('mediatree'));
?>
<script type="text/javascript">
/* <![CDATA[ */
$(document).ready( function()
{
	//var extensionFilter = 'jpg,png,gif';
	var extensionFilter = 'jpg,png,gif,mp4,flv,swf,pdf,xls,odt,doc,xlsx,docx,ods,pdf,txt,ogv,csv';
	media.saveDir('<?php echo $mf->rootDir; ?>');
	
	/* UNHANDLE ALL PREVIOUS */
	$('a.dir').off('click');
	$('a.file').off('click');
	$('a.remove-file').off('click');
	$('a.remove-dir').off('click');
	$('#new-dir').off('click');
	$('#refresh').off('click');
	$('#uploader').off('click');
	
	/* HANDLE DIR CLICK */
	$('a.dir').on('click', function()
	{
		var xDir = $(this).attr('href');
		media.openDir(xDir);
		return false;
	});
	
	/* HANDLE FILE CLICK */
	/*
	$('a.file').on('click', function()
	{
		//var xFile = $(this).attr('href');
		//media.openFile(xFile);
		return false;
	});
	*/
	
	/* HANDLE FILE DELETE CLICK */
	$('a.remove-file').remove();

	/* HANDLE DIR DELETE CLICK */
	$('a.remove-dir').remove();
	
	/* HANDLE DIR MAKE */
	$('#new-dir').button({
		icons: {
			primary: 'ui-icon-folder-open'
		}
	
	}).on('click', function()
	{
		media.makeDir();
		return false;
	});
	
	/* HANDLE DIR REFRESH */
	$('#refresh').button({
		icons: {
			primary: 'ui-icon-refresh'
		},
		text: false
	}).on('click', function()
	{
		var xDir = media.readDir();
		media.openDir(xDir);
		return false;
	});
	
	/* HANDLE UPLOAD BUTTON */
	$('#uploader').button({
		icons: {
			 primary: 'ui-icon-arrowthickstop-1-s'
		} 
	}).on('click', function()
	{
		NxMarket.uploadFiles(extensionFilter);
		return false;
	});
});
/* ]]> */
</script>
