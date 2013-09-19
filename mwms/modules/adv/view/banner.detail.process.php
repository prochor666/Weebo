<?php
$id_banner = isset($_GET['id_banner']) && (int)$_GET['id_banner']>0 ? (int)$_GET['id_banner']: 0;

$mm = new DataProcessXmlWizard;

$srcData = $_POST;

$mm->input = array();
$mm->input['sourceData'] = $srcData;
$mm->input['id'] = $id_banner;
$mm->input['fieldName'] = 'id_banner';
$mm->input['tableName'] = '_adv_banners';
$mm->input['tableData'] = array(
	'title' => array('title' => Lng::get('adv/adv_banner_title'), 'system_type' => 'text', 'validate' => true, 'unique' => false, 'predefined' => 0, 'size' => 255),
	'file' => array('title' => Lng::get('adv/adv_banner_file'), 'system_type' => 'method', 'validate' => true, 'unique' => false, 'predefined' => 0, 'size' => 255, 'default_value' => 'WeeboAdv::getAdvFile'),
	'url' => array('title' => Lng::get('adv/adv_banner_url'), 'system_type' => 'blob', 'validate' => true, 'unique' => false, 'predefined' => 0, 'size' => 65535, 'cleanup' => 1),
	'format' => array('title' => Lng::get('adv/adv_banner_format'), 'system_type' => 'method', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 255, 'default_value' => 'WeeboAdv::getFormats'),
	'id_blank' => array('title' => Lng::get('adv/adv_banner_id_blank'), 'system_type' => 'bool', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 1, 'default_value' => 1),
	'id_wmode' => array('title' => Lng::get('adv/adv_banner_wmode'), 'system_type' => 'bool', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 1, 'default_value' => 1),
	'clickthru' => array('title' => Lng::get('adv/adv_banner_clickthru'), 'system_type' => 'bool', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 1, 'default_value' => 1),
	'timeout' => array('title' => Lng::get('adv/adv_banner_timeout'), 'system_type' => 'int', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 1, 'default_value' => 0),
);

if($id_banner>0){
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
	var aDir = 'require&file=/mwms/modules/adv/view/adv.media.admin.php';
	
	/* UPLOADER */
	$('#edit_field_<?php echo $mm->input['tableName'].'_'.$mm->id; ?>_file').attr('readonly', 'readonly').parent().append('<div id="uploader-panel"><button id="pickfile_<?php echo $mm->input['tableName'].'_'.$mm->id; ?>" class="pickfile button"><?php echo Lng::get('adv/adv_file_load'); ?></button></div><div id="uploader-box-wrapper"></div>');
	
	// Set image button
	$('#pickfile_<?php echo $mm->input['tableName'].'_'.$mm->id; ?>').click(
		function(){
			AdvAdmin.newFile(aDir, '<?php echo Lng::get('adv/adv_file_load'); ?>');
			
			/* CLEAR & HANDLE MEDIAMANAGER FILE CLICK */
			$(document).off('click', 'a.file');
			$(document).on('click', 'a.file', function()
			{
				var xFile = $(this).attr('href');
				AdvAdmin.attachFile('#edit_field_<?php echo $mm->input['tableName'].'_'.$mm->id; ?>_file', xFile);
				$('#weebo-modal-dialog-content').dialog("close");
				return false;
			});
			
			return false;
		}
	).button();
	
	
	
	$('button.detail_save_meta_<?php echo $mm->input['tableName'].'_'.$mm->id; ?>').click(
		function(){
			weeboMeta.applyCallback('<?php echo $mm->input['tableName'].'_'.$mm->id; ?>', 'require&file=/mwms/modules/adv/view/banner.detail.process.save.php');
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
