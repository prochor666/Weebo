<?php
$id_dir = isset($_GET['id_dir']) ? (int)$_GET['id_dir']: 0;

$ass = new DataProcessXmlWizard;

$srcData = $_POST;

if($id_dir>0){
	
}else{
	//$srcData['date_public'] = time();
}

$domainKey = Registry::get('active_domain');
$domains = Lng::get('cms/cms_public_domains');

$lnk = new MediaBrowserTemplate;

$ass->input = array();
$ass->input['sourceData'] = $srcData;
$ass->input['id'] = $id_dir;
$ass->input['fieldName'] = 'id_dir';
$ass->input['tableName'] = '_cms_media_dir';
$ass->input['tableData'] = array(
	'title' => array('title' => Lng::get('cms/mwms_media_dir_title'), 'system_type' => 'text', 'validate' => true, 'unique' => true, 'predefined' => 0, 'size' => 255),
	'description' => array('title' => Lng::get('cms/mwms_media_dir_description'), 'system_type' => 'text', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 255),
	'type' => array('title' => Lng::get('cms/mwms_media_dir_type'), 'system_type' => 'method', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 20, 'default_value' => 'Cms::selectDirType'),
	'path' => array('title' => Lng::get('cms/mwms_media_dir_path'), 'system_type' => 'method', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 255, 'default_value' => 'Cms::setPath'),
);

if($id_dir>0){
	$ass->input['tableData']['domain'] = array('title' => 'hidden', 'system_type' => 'source', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 10);
	$ass->input['tableData']['id_upd'] = array('title' => 'hidden', 'system_type' => 'source', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 10);
	$ass->input['tableData']['date_upd'] = array('title' => 'hidden', 'system_type' => 'source', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 10);
}else{
	$ass->input['tableData']['domain'] = array('title' => 'hidden', 'system_type' => 'source', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 10);
	$ass->input['tableData']['id_ins'] = array('title' => 'hidden', 'system_type' => 'source', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 10);
	$ass->input['tableData']['date_ins'] = array('title' => 'hidden', 'system_type' => 'source', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 10);
}

$ass->input['metaUse'] = false;
$ass->input['metaConnectId'] = 'id_dir';
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
			weeboMeta.applyCallback('<?php echo $ass->input['tableName'].'_'.$ass->id; ?>', 'require&file=/mwms/modules/cms/view/media.dir.detail.process.save.php&id_dir=<?php echo $id_dir; ?>');
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
