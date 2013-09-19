<?php
$id_media = isset($_GET['id_media']) ? (int)$_GET['id_media']: 0;

$ass = new DataProcessXmlWizard;

$srcData = $_POST;

$ass->input = array();
$ass->input['sourceData'] = $srcData;
$ass->input['id'] = $id_media;
$ass->input['fieldName'] = 'id_media';
$ass->input['tableName'] = '_cms_media_list';
$ass->input['tableData'] = array(
	'title' => array('title' => Lng::get('cms/mwms_media_title'), 'system_type' => 'text', 'validate' => true, 'unique' => false, 'predefined' => 0, 'size' => 255),
	'public_ord' => array('title' => Lng::get('cms/mwms_media_public_ord'), 'system_type' => 'int', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 11),
	'path' => array('title' => Lng::get('cms/mwms_media_file'), 'system_type' => 'method', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 65535, 'default_value' => 'Cms::getMediaThumb')
);

if($id_media>0){
	$ass->input['tableData']['id_upd'] = array('title' => 'hidden', 'system_type' => 'source', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 10);
	$ass->input['tableData']['date_upd'] = array('title' => 'hidden', 'system_type' => 'source', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 10);
}else{
	$ass->input['tableData']['id_ins'] = array('title' => 'hidden', 'system_type' => 'source', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 10);
	$ass->input['tableData']['date_ins'] = array('title' => 'hidden', 'system_type' => 'source', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 10);
}

$ass->input['metaUse'] = false;
$ass->input['metaConnectId'] = 'id_conect';
$ass->input['metaTypesTableName'] = null;
$ass->input['metaDataTableName'] = null;

$ass->init();

echo $ass->showForm();
?>
<script type="text/javascript">
/* <![CDATA[ */
$(document).ready(function(){
	
	/* SAVE INIT */
	$('button.detail_save_meta_<?php echo $ass->input['tableName'].'_'.$ass->id; ?>').click(
		function(){
			weeboMeta.applyCallback('<?php echo $ass->input['tableName'].'_'.$ass->id; ?>', 'require&file=/mwms/modules/cms/view/media.file.detail.process.save.php&id_media=<?php echo $id_media; ?>');
		}
	).button({
		icons: {
			primary: "ui-icon-circle-check",
			text: false
		}
	});

	// Image tooltip
	$( '#edit_field_<?php echo $ass->input['tableName'].'_'.$ass->id; ?>_path_thumb' ).tooltip({
		show: {
			effect: "slideDown",
			duration: 170
		},
		hide: {
			effect: "slideUp",
			duration: 80
		},
		position: { my: "left top", at: "right+15 top-5", collision: "flipfit" },
		content: function() {
			return '<img src="' + weebo.settings.SiteRoot +'/<?php echo _GLOBALDATADIR_; ?>/' + $( '#edit_field_<?php echo $ass->input['tableName'].'_'.$ass->id; ?>_path' ).val() + '" alt="~" style="width: 300px;" />';
		}
	});

});
/* ]]> */
</script>
