<?php
$id_campaign = isset($_POST['id_campaign']) && (int)$_POST['id_campaign']>0 ? (int)$_POST['id_campaign']: 0;

$srcData = $_POST;

if($id_campaign>0){
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
$mm->input['id'] = $id_campaign;
$mm->input['fieldName'] = 'id_campaign';
$mm->input['tableName'] = '_adv_campaigns';
$mm->input['tableData'] = array(
	'title' => array('title' => Lng::get('adv/adv_campaign_title'), 'system_type' => 'text', 'validate' => true, 'unique' => false, 'predefined' => 0, 'size' => 255),
);

if($id_campaign>0){
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
