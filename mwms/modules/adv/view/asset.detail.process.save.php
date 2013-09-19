<?php
$id_asset = isset($_POST['id_asset']) && (int)$_POST['id_asset']>0 ? (int)$_POST['id_asset']: 0;

$srcData = $_POST;

if($id_asset>0){
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
$mm->input['id'] = $id_asset;
$mm->input['fieldName'] = 'id_asset';
$mm->input['tableName'] = '_adv_assets';
$mm->input['tableData'] = array(
	'title' => array('title' => Lng::get('adv/adv_asset_title'), 'system_type' => 'text', 'validate' => true, 'unique' => false, 'predefined' => 0, 'size' => 255),
	'id_banner' => array('title' => Lng::get('adv/adv_asset_banner'), 'system_type' => 'method', 'validate' => true, 'unique' => false, 'predefined' => 0, 'size' => 11, 'default_value' => 'WeeboAdv::getBannerList'),
	'id_position' => array('title' => Lng::get('adv/adv_asset_position'), 'system_type' => 'method', 'validate' => true, 'unique' => false, 'predefined' => 0, 'size' => 11, 'cleanup' => 1, 'default_value' => 'WeeboAdv::getPositionList'),
	'id_campaign' => array('title' => Lng::get('adv/adv_asset_campaign'), 'system_type' => 'method', 'validate' => true, 'unique' => false, 'predefined' => 0, 'size' => 11, 'default_value' => 'WeeboAdv::getCampaignList'),
	'date_from' => array('title' => Lng::get('adv/adv_asset_date_from'), 'system_type' => 'datetime', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 11, 'default_value' => time()),
	'date_to' => array('title' => Lng::get('adv/adv_asset_date_to'), 'system_type' => 'datetime', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 11, 'default_value' => (time() + 86400) ),
	'id_active' => array('title' => Lng::get('adv/adv_asset_id_active'), 'system_type' => 'bool', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 1, 'default_value' => 1),
	'max_impressions' => array('title' => Lng::get('adv/adv_asset_max_impressions'), 'system_type' => 'int', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 11, 'default_value' => 0),
);

if($id_asset>0){
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
