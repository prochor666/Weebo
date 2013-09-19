<?php
$id_form = isset($_GET['id_form']) && (int)$_GET['id_form']>0 ? (int)$_GET['id_form']: 0;

$ass = new DataProcessXmlWizard;

$ass->input = array();
$ass->input['sourceData'] = $_POST;
$ass->input['id'] = $id_form;
$ass->input['fieldName'] = 'id_form';
$ass->input['tableName'] = '_cms_forms';
$ass->input['tableData'] = array(
	'title' => array('title' => Lng::get('cms/mwms_form_title'), 'system_type' => 'text', 'validate' => true, 'unique' => false, 'predefined' => 0, 'size' => 255),
	'description' => array('title' => Lng::get('cms/mwms_form_description'), 'system_type' => 'text', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 255),
	'config' => array('title' => Lng::get('cms/mwms_form_config'), 'system_type' => 'code', 'validate' => true, 'unique' => false, 'predefined' => 0, 'size' => 16777215)
);

if($id_form>0){
	$ass->input['tableData']['domain'] = array('title' => 'hidden', 'system_type' => 'source', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 255);
	$ass->input['tableData']['id_upd'] = array('title' => 'hidden', 'system_type' => 'source', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 10);
	$ass->input['tableData']['date_upd'] = array('title' => 'hidden', 'system_type' => 'source', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 10);
}else{
	$ass->input['tableData']['domain'] = array('title' => 'hidden', 'system_type' => 'source', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 255);
	$ass->input['tableData']['id_ins'] = array('title' => 'hidden', 'system_type' => 'source', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 10);
	$ass->input['tableData']['date_ins'] = array('title' => 'hidden', 'system_type' => 'source', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 10);
}

$ass->input['metaUse'] = false;
$ass->input['metaConnectId'] = 'id_connect';
$ass->input['metaTypesTableName'] = null;
$ass->input['metaDataTableName'] = null;

$ass->init();

echo $ass->showForm();

//System::dump($ass->profileData);
?>
<script type="text/javascript">
/* <![CDATA[ */
$(document).ready(function(){
	
	var myEditor = cms.setEditor('edit_field__cms_forms_<? echo $ass->id; ?>_config');
	
	$('button.detail_save_meta_<?php echo $ass->input['tableName'].'_'.$ass->id; ?>').click(
		function(){
			var codeData = myEditor.getValue();
			$('#edit_form__cms_forms_<? echo $ass->id; ?>_config textarea[name="meta_value_config"]').val(codeData);
			weeboMeta.applyCallback('<?php echo $ass->input['tableName'].'_'.$ass->id; ?>', 'require&file=/mwms/modules/cms/view/form.detail.process.save.php');
		}
	).button({
		icons: {
			primary: "ui-icon-circle-check",
			text: false
		}
	});
	
	//
});
/* ]]> */
</script>


