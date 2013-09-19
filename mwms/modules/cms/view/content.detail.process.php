<?php
$id_content = isset($_GET['id_content']) && (int)$_GET['id_content']>0 ? (int)$_GET['id_content']: 0;
$id_link = isset($_GET['id_link']) ? (int)$_GET['id_link']: 0;

$ass = new DataProcessXmlWizard;

$srcData = $_POST;

if($id_content>0){
	$srcData['id_link'] = $id_link;
}else{
	$srcData['id_link'] = $id_link;
}

$ass->input = array();
$ass->input['sourceData'] = $srcData;
$ass->input['id'] = $id_content;
$ass->input['fieldName'] = 'id_content';
$ass->input['tableName'] = '_cms_content';
$ass->input['tableData'] = array(
	'title' => array('title' => Lng::get('cms/mwms_content_title'), 'system_type' => 'text', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 255),
	'display_script' => array('title' => Lng::get('cms/mwms_link_display_script'), 'system_type' => 'method', 'validate' => true, 'unique' => false, 'predefined' => 0, 'size' => 65535, 'default_value' => 'Cms::getViews'),
	'display_script_param' => array('title' => Lng::get('cms/mwms_link_display_script_param'), 'system_type' => 'method', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 65535, 'default_value' => 'Cms::getMethodValue'),
	'annotation_text' => array('title' => Lng::get('cms/mwms_content_annotation_text'), 'system_type' => 'blob', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 65535, 'cleanup' => 1),
	'annotation_image' => array('title' => Lng::get('cms/mwms_content_annotation_image'), 'system_type' => 'method', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 65535, 'default_value' => 'Cms::getAnnotationImage'),
	'id_brief_level' => array('title' => Lng::get('cms/mwms_content_id_brief_level'), 'system_type' => 'method', 'validate' => true, 'unique' => false, 'predefined' => 0, 'size' => 10, 'default_value' => 'Cms::getDisplayModes'),
	'id_link' => array('title' => Lng::get('cms/mwms_content_link'), 'system_type' => 'method', 'validate' => true, 'unique' => false, 'predefined' => 0, 'size' => 10, 'default_value' => 'Cms::contentLinksSelect'),
	'content' => array('title' => Lng::get('cms/mwms_content_text'), 'system_type' => 'blob', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 16777215),
	'keywords' => array('title' => Lng::get('cms/mwms_content_keywords'), 'system_type' => 'text', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 255),
	'ext_link' => array('title' => Lng::get('cms/mwms_content_ext_link'), 'system_type' => 'text', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 65535),
	'public_order' => array('title' => Lng::get('cms/mwms_content_public_order'), 'system_type' => 'int', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 10, 'default_value' => 0),
	'id_public' => array('title' => Lng::get('cms/mwms_content_id_public'), 'system_type' => 'bool', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 1, 'default_value' => 0),
	'id_blank' => array('title' => Lng::get('cms/mwms_content_id_blank'), 'system_type' => 'bool', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 1, 'default_value' => 0),
	'id_rss' => array('title' => Lng::get('cms/mwms_content_id_rss'), 'system_type' => 'bool', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 1, 'default_value' => 0),
	'id_sticky' => array('title' => Lng::get('cms/mwms_content_id_sticky'), 'system_type' => 'bool', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 1, 'default_value' => 0),
	'id_date_display' => array('title' => Lng::get('cms/mwms_id_date_display'), 'system_type' => 'bool', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 1, 'default_value' => 0),
	'date_public' => array('title' => Lng::get('cms/mwms_content_date_public'), 'system_type' => 'datetime', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 10, 'default_value' => time()),
	'id_date_restrict' => array('title' => Lng::get('cms/mwms_content_id_date_restrict'), 'system_type' => 'bool', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 1),
	'publish_date_from' => array('title' => Lng::get('cms/mwms_content_publish_date_from'), 'system_type' => 'datetime', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 10, 'default_value' => time()),
	'publish_date_to' => array('title' => Lng::get('cms/mwms_content_publish_date_to'), 'system_type' => 'datetime', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 10, 'default_value' => time()),
);

if($id_content>0){
	//$ass->input['tableData']['id_link'] = array( 'title' => 'hidden', 'system_type' => 'source', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 10);
	$ass->input['tableData']['id_upd'] = array('title' => 'hidden', 'system_type' => 'source', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 10);
	$ass->input['tableData']['date_upd'] = array('title' => 'hidden', 'system_type' => 'source', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 10);
	$ass->input['tableData']['textmap'] = array('title' => 'hidden', 'system_type' => 'source', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 255);
}else{
	//$ass->input['tableData']['id_link'] = array( 'title' => 'hidden', 'system_type' => 'source', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 10);
	$ass->input['tableData']['id_ins'] = array('title' => 'hidden', 'system_type' => 'source', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 10);
	$ass->input['tableData']['date_ins'] = array('title' => 'hidden', 'system_type' => 'source', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 10);
	$ass->input['tableData']['textmap'] = array('title' => 'hidden', 'system_type' => 'source', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 255);
}

$ass->input['metaUse'] = false;
$ass->input['metaConnectId'] = 'id_connect';
$ass->input['metaTypesTableName'] = null;
$ass->input['metaDataTableName'] = null;

$ass->init();

echo $ass->showForm();
?>
<script type="text/javascript">
/* <![CDATA[ */
$(document).ready(function(){
	
	/* INIT */
	var aDir = 'require&file=/mwms/modules/cms/view/cms.media.admin.php';
	
	/* METHOD CHOOSER */
	var methodURL = 'method&fn=Cms::getMethod&qs='+$('#edit_field_<?php echo $ass->input['tableName'].'_'.$ass->id; ?>_display_script').val()+'|<?php echo $ass->id; ?>';
	cms.chooseScriptMethod(<?php echo $ass->id; ?>, methodURL);
	
	$('#edit_field_<?php echo $ass->input['tableName'].'_'.$ass->id; ?>_display_script').change( function()
	{
		var methodURL = 'method&fn=Cms::getMethod&qs='+$(this).val()+'|<?php echo $ass->id; ?>';
		cms.chooseScriptMethod(<?php echo $ass->id; ?>, methodURL);
	});
	
	/* UPLOADER */
	$('#edit_field__cms_content_<?php echo $id_content; ?>_annotation_image').attr('readonly', 'readonly').parent().append('<div id="uploader-panel"><button id="pickfile_<?php echo $ass->input['tableName'].'_'.$ass->id; ?>" class="pickfile button"><?php echo Lng::get('cms/mwms_content_annotation_image_load'); ?></button><button id="delfile_<?php echo $ass->input['tableName'].'_'.$ass->id; ?>" class="pickfile button"><?php echo Lng::get('cms/mwms_content_annotation_image_delete'); ?></button></div><div id="uploader-box-wrapper"></div>');
	
	// Set image button
	$('#pickfile_<?php echo $ass->input['tableName'].'_'.$ass->id; ?>').click(
		function(){
			cms.newFile(aDir, '<?php echo Lng::get('cms/mwms_content_annotation_image_load'); ?>');
			
			/* CLEAR & HANDLE MEDIAMANAGER FILE CLICK */
			$(document).off('click', 'a.file');
			$(document).on('click', 'a.file', function()
			{
				var xFile = $(this).attr('href');
				var xThumb = $(this).find('img').attr('src');
				cms.attachAnnotationFile('#edit_field_<?php echo $ass->input['tableName'].'_'.$ass->id; ?>_annotation_image', xFile, xThumb);
				$('#delfile_<?php echo $ass->input['tableName'].'_'.$ass->id; ?>').show();
				$('#weebo-modal-dialog-content').dialog("close");
				return false;
			});
			
			return false;
		}
	).button();
	
	// Delete button event
	$('#delfile_<?php echo $ass->input['tableName'].'_'.$ass->id; ?>').click(
		function(){
			cms.detachAnnotationFile('#edit_field_<?php echo $ass->input['tableName'].'_'.$ass->id; ?>_annotation_image');
			$(this).hide();
			return false;
		}
	).button();
	
	// Hide delete button, when no image
	if( $('#edit_field_<?php echo $ass->input['tableName'].'_'.$ass->id; ?>_annotation_image').val().length < 1 ){
		$('#delfile_<?php echo $ass->input['tableName'].'_'.$ass->id; ?>').hide();
	}
	
	// Annotation length
	var statusPane<?php echo $ass->id; ?> = $('<div id="a_<?php echo $ass->input['tableName'].'_'.$ass->id; ?>_annotation_status">'+(<?php echo Registry::get('moduledata/cms/brief_size'); ?> - $('#edit_field_<?php echo $ass->input['tableName'].'_'.$ass->id; ?>_annotation_text').val().length)+'/<?php echo Registry::get('moduledata/cms/brief_size'); ?></div>');
	
	$('#edit_field_<?php echo $ass->input['tableName'].'_'.$ass->id; ?>_annotation_text').after(statusPane<?php echo $ass->id; ?>).on('change keyup keypress blur click paste' , function(){   
		
		var v = $(this).val();
		var maxL = <?php echo Registry::get('moduledata/cms/brief_size'); ?>;
		
		rest = maxL - v.length;
		
		statusPane<?php echo $ass->id; ?>.html(rest+'/'+maxL);
		
		if(v.length >= maxL){
			$(this).val(v.substr(0, maxL));
			return false;
		}
	}); 
	
	/* UPLOADER END */
	$('button.detail_save_meta_<?php echo $ass->input['tableName'].'_'.$ass->id; ?>').click(
		function(){
			weeboMeta.applyCallback('<?php echo $ass->input['tableName'].'_'.$ass->id; ?>', 'require&file=/mwms/modules/cms/view/content.detail.process.save.php&id_link=<?php echo $id_link; ?>');
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
