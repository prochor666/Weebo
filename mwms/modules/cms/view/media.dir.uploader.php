<?php 
$id_dir = isset($_GET['id_dir']) ? (int)$_GET['id_dir']: 0;

if($id_dir > 0)
{
$mb = new MediaBrowser;

$d = $mb->getDirData($id_dir);

$path = 'content/'.$d['path'];

Storage::makeDir($path);
?>
<div><?php echo $path; ?></div>
<div id="dir-uploader-box-wrapper"></div>
<div id="dir-log"></div>
<script type="text/javascript">
/* <![CDATA[ */
$(document).ready(function(){

	$('#tabs').tabs('option', 'disabled', [0,1]);

	$('li[aria-controls="ui-custom-0"]').delegate( "span.ui-icon-close", "click", function() {
		var panel = $( this ).closest( 'li' );
		var panelContent = panel.attr( 'aria-controls' );
		panel.remove();
		$( "#" + panelContent ).remove();
		$('#tabs').tabs( "enable" );
		$('#tabs').tabs( "option", "active", 1 );
	});

	var filter = 'jpg,png,gif';
	var path = '<?php echo $path; ?>';
	var id_dir = '<?php echo $id_dir; ?>';
	
	$('#dir-uploader-box-wrapper').html('<div id="uploader-box"></div>');
	
	var UIF = Math.round((new Date()).getTime() / 1000);
	
	$("#uploader-box").plupload({
		// General settings
		runtimes : 'flash, html5',
		url : weebo.settings.AjaxCall + 'require&file=/mwms/modules/media/view/media.upload.recieve.php&uif='+UIF+'&dir='+path,
		max_file_size : '128mb',
		chunk_size : '100kb',
		unique_names : false,
		// Resize images on clientside if we can
		//resize : {width : 320, height : 240, quality : 90},

		// Specify what files to browse for
		
		filters : [
			{ title : "Image files", extensions : filter }
		],
		
		// Flash settings
		flash_swf_url : weebo.settings.SiteRoot + '/shared/plupload/js/plupload.flash.swf',
		
		// Post init events, bound after the internal events
		init : {
			Refresh: function(up) {
				// Called when upload shim is moved
				
			},

			StateChanged: function(up) {
				// Called when the state of the queue is changed
				
			},

			QueueChanged: function(up) {
				// Called when the files in queue are changed by adding/removing files
				
			},

			UploadProgress: function(up, file) {
				// Called while a file is being uploaded
				
			},

			FilesAdded: function(up, files) {
				// Callced when files are added to queue
				plupload.each(files, function(file) {
					
				});
			},

			FilesRemoved: function(up, files) {
				// Called when files where removed from queue
				
				plupload.each(files, function(file) {
					
				});
			},

			FileUploaded: function(up, file, info) {
				
			},

			ChunkUploaded: function(up, file, info) {
				
			},

			Error: function(up, args) {
				
			},

			UploadComplete: function(up, args) {
				// Fires when complete
				isUploaded(path, id_dir, 'refresh', up);
			}
		}
	});
	
});

function isUploaded(path, id_dir,xevent, obj){
	//&& obj.total.uploaded == obj.files.length
	if(xevent == 'refresh' && obj.files.length > 0)
	{
		//var ref = weebo.settings.AjaxCall + "require&file=/mwms/modules/cms/view/media.browser.inner.php&id_dir=" + id_dir;
		var ri = weebo.settings.AjaxCall + 'require&file=/mwms/modules/cms/view/cms.media.dir.reindex.php&dir='+path+'&id_dir='+id_dir;
		
		cms.showPreloader('#mwms_content_show', 300);
		
		$('#uploader-box').load( ri, function()
		{
			var selected = $('#tabs').tabs('option', 'active');
			$('#tabs').tabs('enable');
			//$('#mwms_content_show').html( response );
			$("#tabs").tabs('option', 'active', 1);
			cms.closeTab(selected);
		});
	}
} 
/* ]]> */
</script>

<?php } ?>
