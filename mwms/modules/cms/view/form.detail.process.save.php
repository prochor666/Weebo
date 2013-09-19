<?php
$id_form = isset($_POST['id_form']) && (int)$_POST['id_form']>0 ? (int)$_POST['id_form']: 0;

$srcData = $_POST;

$domainKey = Registry::get('active_domain');
$domains = Lng::get('cms/cms_public_domains');

if($id_form>0){
	$srcData['meta_value_id_upd'] = Registry::get('userdata/id_user');
	$srcData['meta_value_date_upd'] = time();
	$srcData['meta_value_domain'] = $domains[$domainKey]['name'];
}else{
	$srcData['meta_value_id_ins'] = Registry::get('userdata/id_user');
	$srcData['meta_value_date_ins'] = time();
	$srcData['meta_value_domain'] = $domains[$domainKey]['name'];
}

$ass = new DataProcessXmlWizard;

$ass->input = array();
$ass->input['sourceData'] = $srcData;
$ass->input['id'] = $id_form;
$ass->input['fieldName'] = 'id_form';
$ass->input['tableName'] = '_cms_forms';
$ass->input['tableData'] = array(
	'title' => array('title' => Lng::get('cms/mwms_form_title'), 'system_type' => 'text', 'validate' => true, 'unique' => false, 'predefined' => 0, 'size' => 255),
	'description' => array('title' => Lng::get('cms/mwms_form_description'), 'system_type' => 'text', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 255),
	'config' => array('title' => Lng::get('cms/mwms_form_config'), 'system_type' => 'code', 'validate' => true, 'unique' => false, 'predefined' => 0, 'size' => 16777215)
);

if($id_form>0){
	$ass->input['tableData']['domain'] = array('title' => 'hidden', 'system_type' => 'text', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 255);
	$ass->input['tableData']['id_upd'] = array('title' => 'hidden', 'system_type' => 'int', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 10);
	$ass->input['tableData']['date_upd'] = array('title' => 'hidden', 'system_type' => 'int', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 10);
}else{
	$ass->input['tableData']['domain'] = array('title' => 'hidden', 'system_type' => 'text', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 255);
	$ass->input['tableData']['id_ins'] = array('title' => 'hidden', 'system_type' => 'int', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 10);
	$ass->input['tableData']['date_ins'] = array('title' => 'hidden', 'system_type' => 'int', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 10);
}

$ass->input['metaUse'] = false;
$ass->input['metaConnectId'] = 'id_connect';
$ass->input['metaTypesTableName'] = null;
$ass->input['metaDataTableName'] = null;

$ass->init();

echo $ass->extract();
?>
<script type="text/javascript">
/* <![CDATA[ */
$(document).ready(function(){
	
	$('.meta_head div.warn').remove();

	$('div.warn').each(function()
	{
		var elem = $(this).attr('title');
		var header = $('#'+elem).parent('td').parent('tr').find('td.meta_head');
		header.append($(this));
	});
});
/* ]]> */
</script>
