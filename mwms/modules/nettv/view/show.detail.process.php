<?php
$id_show = isset($_GET['id_show']) && (int)$_GET['id_show']>0 ? (int)$_GET['id_show']: 0;

$mm = new DataProcessXmlWizard;

$srcData = $_POST;

$mm->input = array();
$mm->input['sourceData'] = $srcData;
$mm->input['id'] = $id_show;
$mm->input['fieldName'] = 'id_show';
$mm->input['tableName'] = '_nettv_shows';
$mm->input['tableData'] = array(
	'title' => array('title' => Lng::get('nettv/tv_show_title'), 'system_type' => 'text', 'validate' => true, 'unique' => false, 'predefined' => 0, 'size' => 255),
	'id_dir' => array('title' => Lng::get('nettv/tv_id_dir'), 'system_type' => 'method', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 65535, 'default_value' => 'WeeboNettv::chooseGallery'),
	'id_active' => array('title' => Lng::get('nettv/tv_show_active'), 'system_type' => 'bool', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 65535, 'default_value' => 0),
	'id_archive' => array('title' => Lng::get('nettv/tv_show_archive'), 'system_type' => 'bool', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 65535, 'default_value' => 0),
	'description_short' => array('title' => Lng::get('nettv/tv_show_description_short'), 'system_type' => 'blob', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 65535),
	'description' => array('title' => Lng::get('nettv/tv_show_description'), 'system_type' => 'blob', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 65535),
	'image' => array('title' => Lng::get('nettv/tv_show_image'), 'system_type' => 'method', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 255, 'default_value' => 'WeeboNettv::getImageFile'),
);

if($id_show>0){
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
?>
<script type="text/javascript">
/* <![CDATA[ */
$(document).ready(function(){
	
	/* INIT */
	var aDir = 'require&file=/mwms/modules/nettv/view/nettv.media.admin.php';
	
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
	
	/* UPLOADER END */
	$('button.detail_save_meta_<?php echo $mm->input['tableName'].'_'.$mm->id; ?>').click(
		function(){
			weeboMeta.applyCallback('<?php echo $mm->input['tableName'].'_'.$mm->id; ?>', 'require&file=/mwms/modules/nettv/view/show.detail.process.save.php');
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
