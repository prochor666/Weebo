<?php
$id_asset = isset($_GET['id_asset']) && (int)$_GET['id_asset']>0 ? (int)$_GET['id_asset']: 0;

$mm = new DataProcessXmlWizard;

$srcData = $_POST;

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
	
	var lP = '#edit_field_<?php echo $mm->input['tableName'].'_'.$mm->id; ?>_id_banner_prewiev';
	var t = $('#edit_field_<?php echo $mm->input['tableName'].'_'.$mm->id; ?>_id_banner');
	AdvAdmin.showBannerMedia(lP, t.val());
	
	// Live prewiev
	t.off('change').on('change', 
		function(){
			AdvAdmin.showBannerMedia(lP, $(this).val());
			return false;
	});
	
	$('button.detail_save_meta_<?php echo $mm->input['tableName'].'_'.$mm->id; ?>').click(
		function(){
			weeboMeta.applyCallback('<?php echo $mm->input['tableName'].'_'.$mm->id; ?>', 'require&file=/mwms/modules/adv/view/asset.detail.process.save.php');
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
