<?php
$id_banner = isset($_POST['id_banner']) && (int)$_POST['id_banner']>0 ? (int)$_POST['id_banner']: 0;

$srcData = $_POST;

if($id_banner>0){
	//$srcData['meta_value_id_link'] = $id_link;
	$srcData['meta_value_id_upd'] = Registry::get('userdata/id_user');
	$srcData['meta_value_date_upd'] = time();
}else{
	//$srcData['meta_value_id_link'] = $id_link;
	$srcData['meta_value_id_ins'] = Registry::get('userdata/id_user');
	$srcData['meta_value_date_ins'] = time();
}

$mm = new DataProcessXmlWizard;

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
	$mm->input['tableData']['id_upd'] = array('title' => 'hidden', 'system_type' => 'int', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 10);
	$mm->input['tableData']['date_upd'] = array('title' => 'hidden', 'system_type' => 'int', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 10);
}else{
	$mm->input['tableData']['id_ins'] = array('title' => 'hidden', 'system_type' => 'int', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 10);
	$mm->input['tableData']['date_ins'] = array('title' => 'hidden', 'system_type' => 'int', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 10);
}

$mm->input['metaUse'] = false;
$mm->input['metaConnectId'] = 'id_connect';
$mm->input['metaTypesTableName'] = null;
$mm->input['metaDataTableName'] = null;

$mm->init();

echo $mm->extract();
?>

<script type="text/javascript">
/* <![CDATA[ */
$(document).ready(function(){
	var allowsave = <?php echo !$mm->allowsave ? 0: 1; ?>;
	
	$('.meta_head div.warn').remove();
		
	$('div.warn').each(function()
	{
		var elem = $(this).attr('title');
		var header = $('#'+elem).parent('td').parent('tr').find('td.meta_head');
		header.append($(this));
	});
	
	if(allowsave == 1){
		var selected = $('#tabs').tabs('option', 'active');
		AdvAdmin.closeTab(selected);
		$("#tabs").tabs('option', 'active', 0);
		$("#tabs").tabs('load', 0);
	}
});
/* ]]> */
</script>
