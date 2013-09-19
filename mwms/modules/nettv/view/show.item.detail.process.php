<?php
$id_item = isset($_GET['id_item']) && (int)$_GET['id_item']>0 ? (int)$_GET['id_item']: 0;

$mm = new DataProcessXmlWizard;

$srcData = $_POST;

$mm->input = array();
$mm->input['sourceData'] = $srcData;
$mm->input['id'] = $id_item;
$mm->input['fieldName'] = 'id_item';
$mm->input['tableName'] = '_nettv_show_items';
$mm->input['tableData'] = array(
	'title' => array('title' => Lng::get('nettv/tv_show_items_title'), 'system_type' => 'text', 'validate' => true, 'unique' => false, 'predefined' => 0, 'size' => 255),
	'date_public' => array('title' => Lng::get('nettv/tv_show_items_date_public'), 'system_type' => 'datetime', 'validate' => true, 'unique' => false, 'predefined' => 0, 'size' => 20, 'default_value' => time()),
	'id_public' => array('title' => Lng::get('nettv/tv_show_items_publish'), 'system_type' => 'bool', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 1, 'default_value' => 0),
	'id_show' => array('title' => Lng::get('nettv/tv_show_load'), 'system_type' => 'method', 'validate' => true, 'unique' => false, 'predefined' => 0, 'size' => 20, 'default_value' => 'WeeboNettv::getShowList'),
	'series' => array('title' => Lng::get('nettv/tv_show_items_series'), 'system_type' => 'int', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 11, 'default_value' => 0),
	'episode' => array('title' => Lng::get('nettv/tv_show_items_episode'), 'system_type' => 'int', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 11, 'default_value' => 0),
	//'id_team' => array('title' => Lng::get('nettv/tv_show_items_team'), 'system_type' => 'method', 'validate' => true, 'unique' => false, 'predefined' => 0, 'size' => 20, 'default_value' => 'WeeboNettv::getTeamList'),
	'format' => array('title' => Lng::get('nettv/tv_archive_format'), 'system_type' => 'text', 'validate' => true, 'unique' => false, 'predefined' => 0, 'size' => 50),
	'image_active' =>array('title' => Lng::get('nettv/tv_chart_items_image'), 'system_type' => 'int', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 11, 'default_value' => 0),
	'description' => array('title' => Lng::get('nettv/tv_show_items_description'), 'system_type' => 'blob', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 65535),
);

if($id_item>0){
	$mm->input['tableData']['id_upd'] = array('title' => 'hidden', 'system_type' => 'source', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 10);
	$mm->input['tableData']['date_upd'] = array('title' => 'hidden', 'system_type' => 'source', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 10);
}else{
	$mm->input['tableData']['id_ins'] = array('title' => 'hidden', 'system_type' => 'source', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 10);
	$mm->input['tableData']['date_ins'] = array('title' => 'hidden', 'system_type' => 'source', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 10);
}

$mm->input['metaUse'] = false;
$mm->input['metaConnectId'] = 'id_connect';
$mm->input['metaTypesTableName'] = null;
$mm->input['metaDataTableName'] = null;

$mm->init();

echo $mm->showForm();

$myMedia = '{"video":[],"images":[]}';
$image_active = 0;

if($id_item>0){
	//$ntv = new WeeboNettv;
	//$myMedia = $ntv->getShowItemData($id_item);
	$myMedia = $mm->profileData['media'];
	$image_active = (int)$mm->profileData["image_active"];
}

?>
<script type="text/javascript">
/* <![CDATA[ */
$(document).ready(function(){
	
	/* INIT */
	var myMedia = <?php echo $myMedia; ?>;
	var id_item = parseInt(<?php echo $id_item; ?>);
	var image_active = parseInt(<?php echo $image_active; ?>);
	
	//$('#edit_field__nettv_show_items_'+id_item+'_image_active').hide();
	
	iclass = image_active == 0 ? ' nettv_edit_thumb_active': '';
	ch = image_active == 0 ? ' checked="checked"': '';
	
	var imageSelector = '<label for="edit_field__nettv_show_items_'+id_item+'_image_active_0" class="nettv_edit_thumb'+iclass+'"><input type="radio" value="0" id="edit_field__nettv_show_items_'+id_item+'_image_active_0" name="meta_value_image_active" '+ch+' /><br /><?php echo Lng::get('nettv/tv_chart_items_image_off'); ?></label>&nbsp;';
	
	for(i in myMedia.images){
		
		realIndex = parseInt( parseInt(i)+1 ); 
		myFsFile = myMedia.images[i];
		rootDir = '<?php echo Registry::get('serverdata/root'); ?>';
		rootDirLength = rootDir.length;
		targetFileSuffix = myFsFile.substr(rootDirLength);
		path = weebo.settings.SiteRoot + targetFileSuffix;
		
		iclass = realIndex == image_active ? ' nettv_edit_thumb_active': '';
		ch = realIndex == image_active ? ' checked="checked"': '';
		
		imageSelector += '<label for="edit_field__nettv_show_items_'+id_item+'_image_active_'+realIndex+'" class="nettv_edit_thumb'+iclass+'"><input type="radio" value="'+realIndex+'" id="edit_field__nettv_show_items_'+id_item+'_image_active_'+realIndex+'" name="meta_value_image_active" '+ch+' /><img src="'+path+'" alt="'+path+'" title="~" /></label>&nbsp;';
	}
	
	$('#edit_field__nettv_show_items_'+id_item+'_image_active').replaceWith( imageSelector );
	
	// Image tooltip
	$( '.nettv_edit_thumb img' ).tooltip({
		show: {
			effect: "slideDown",
			duration: 170
		},
		hide: {
			effect: "slideUp",
			duration: 80
		},
		position: { my: "left top", at: "right+15 top-15", collision: "flipfit" },
		content: function() {
			return '<img src="' + $(this).attr('src') + '" alt="~" style="width: 300px;" />';
		}
	});
	
	
	var showID = parseInt($('#edit_field_<?php echo $mm->input['tableName'].'_'.$mm->id; ?>_id_show').val());
	var showTitle = $('#edit_field_<?php echo $mm->input['tableName'].'_'.$mm->id; ?>_title');
	if( showID > 0 && showTitle.val().length == 0 )
	{
		showTitle.val( $('#edit_field_<?php echo $mm->input['tableName'].'_'.$mm->id; ?>_id_show option[value="'+showID+'"]').text() );
	}
	
	/* SHOW CHANGE */
	$('#edit_field_<?php echo $mm->input['tableName'].'_'.$mm->id; ?>_id_show').on('change', function()
	{
		var showID = parseInt($(this).val());
		if( showID > 0  && showTitle.val().length == 0 )
		{
			newVal = $('#edit_field_<?php echo $mm->input['tableName'].'_'.$mm->id; ?>_id_show  option[value="'+showID+'"]').text();
		}else{
			newVal = showTitle.val();
		}
		
		showTitle.val(newVal);
		return false;
	});
	
	var aDir = 'require&file=/mwms/modules/nettv/view/nettv.media.admin.php';
	var bDir = 'require&file=/mwms/modules/nettv/view/nettv.media.video.admin.php';
	
	/* UPLOADER */
	$('#edit_field_<?php echo $mm->input['tableName'].'_'.$mm->id; ?>_image').attr('readonly', 'readonly').parent().append('<div id="uploader-panel"><button id="pickfile_<?php echo $mm->input['tableName'].'_'.$mm->id; ?>" class="pickfile button"><?php echo Lng::get('nettv/tv_file_load'); ?></button></div><div id="uploader-box-wrapper"></div>');
	
	// Set image button
	$('#pickfile_<?php echo $mm->input['tableName'].'_'.$mm->id; ?>').click(
		function(){
			NettvAdmin.newFile(aDir, '<?php echo Lng::get('nettv/tv_file_load'); ?>');
			
			/* CLEAR & HANDLE MEDIAMANAGER FILE CLICK */
			$(document).off('click', 'a.file');
			$(document).on('click', 'a.file', function()
			{
				var xFile = $(this).attr('href');
				NettvAdmin.attachFile('#edit_field_<?php echo $mm->input['tableName'].'_'.$mm->id; ?>_image', xFile);
				$('#weebo-modal-dialog-content').dialog("close");
				return false;
			});
			
			return false;
		}
	).button();
	
	/* UPLOADER */
	$('#edit_field_<?php echo $mm->input['tableName'].'_'.$mm->id; ?>_video').attr('readonly', 'readonly').parent().append('<div id="uploader-panel"><button id="pickfile2_<?php echo $mm->input['tableName'].'_'.$mm->id; ?>" class="pickfile button"><?php echo Lng::get('nettv/tv_file_load'); ?></button></div><div id="uploader-box-wrapper2"></div>');
	
	// Set image button
	$('#pickfile2_<?php echo $mm->input['tableName'].'_'.$mm->id; ?>').click(
		function(){
			NettvAdmin.newFile(bDir, '<?php echo Lng::get('nettv/tv_file_load'); ?>');
			
			/* CLEAR & HANDLE MEDIAMANAGER FILE CLICK */
			$(document).off('click', 'a.file');
			$(document).on('click', 'a.file', function()
			{
				var xFile = $(this).attr('href');
				NettvAdmin.attachFile('#edit_field_<?php echo $mm->input['tableName'].'_'.$mm->id; ?>_video', xFile);
				$('#weebo-modal-dialog-content').dialog("close");
				return false;
			});
			
			return false;
		}
	).button();
	
	$('button.detail_save_meta_<?php echo $mm->input['tableName'].'_'.$mm->id; ?>').click(
		function(){
			weeboMeta.applyCallback('<?php echo $mm->input['tableName'].'_'.$mm->id; ?>', 'require&file=/mwms/modules/nettv/view/show.item.detail.process.save.php');
		}
	).button({
		icons: {
			primary: "ui-icon-circle-check",
			text: false
		}
	});
	
});
/* ]]> */
</script>
