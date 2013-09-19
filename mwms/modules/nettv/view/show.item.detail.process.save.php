<?php
$id_item = isset($_POST['id_item']) && (int)$_POST['id_item']>0 ? (int)$_POST['id_item']: 0;

$srcData = $_POST;

if($id_item>0){
	//$srcData['meta_value_id_link'] = $id_link;
	$srcData['meta_value_id_upd'] = Registry::get('userdata/id_user');
	$srcData['meta_value_date_upd'] = time();
}else{
	//$srcData['meta_value_id_link'] = $id_link;
	$srcData['meta_value_id_ins'] = Registry::get('userdata/id_user');
	$srcData['meta_value_date_ins'] = time();
}
/*
if(mb_strlen($srcData['meta_value_image'])>0){
	$convert = new WeeboNettv;
	$convert->createImageSet2($srcData['meta_value_image']);
}
*/
$mm = new DataProcessXmlWizard;

$mm->input = array();
$mm->input['sourceData'] = $srcData;
$mm->input['id'] = $id_item;
$mm->input['fieldName'] = 'id_item';
$mm->input['tableName'] = '_nettv_show_items';
$mm->input['tableData'] = array(
	'title' => array('title' => Lng::get('nettv/tv_show_items_title'), 'system_type' => 'text', 'validate' => true, 'unique' => false, 'predefined' => 0, 'size' => 255),
	'date_public' => array('title' => Lng::get('nettv/tv_show_items_date_public'), 'system_type' => 'datetime', 'validate' => true, 'unique' => false, 'predefined' => 0, 'size' => 20, 'default_value' => time()),
	'id_public' => array('title' => Lng::get('nettv/tv_show_items_publish'), 'system_type' => 'bool', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 1, 'default_value' => 0),
	'id_show' => array('title' => Lng::get('nettv/tv_show_load'), 'system_type' => 'method', 'validate' => true, 'unique' => false, 'predefined' => 0, 'size' => 20, 'default_value' => 'WeeboNettv::getShowList'),
	'series' => array('title' => Lng::get('nettv/tv_show_items_series'), 'system_type' => 'int', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 11, 'default_value' => 0),
	'episode' => array('title' => Lng::get('nettv/tv_show_items_episode'), 'system_type' => 'int', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 11, 'default_value' => 0),
	//'id_team' => array('title' => Lng::get('nettv/tv_show_items_team'), 'system_type' => 'method', 'validate' => true, 'unique' => false, 'predefined' => 0, 'size' => 20, 'default_value' => 'WeeboNettv::getTeamList'),
	'format' => array('title' => Lng::get('nettv/tv_archive_format'), 'system_type' => 'text', 'validate' => true, 'unique' => false, 'predefined' => 0, 'size' => 20),
	'description' => array('title' => Lng::get('nettv/tv_show_items_description'), 'system_type' => 'blob', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 65535),
);

if($id_item>0){
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
		NettvAdmin.closeTab(selected);
		$("#tabs").tabs('option', 'active', 0);
		$("#tabs").tabs('load', 0);
	}
});
/* ]]> */
</script>
