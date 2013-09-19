<?php
$id_show = isset($_POST['id_show']) && (int)$_POST['id_show']>0 ? (int)$_POST['id_show']: 0;

$srcData = $_POST;

if($id_show>0){
	//$srcData['meta_value_id_link'] = $id_link;
	$srcData['meta_value_id_upd'] = Registry::get('userdata/id_user');
	$srcData['meta_value_date_upd'] = time();
}else{
	//$srcData['meta_value_id_link'] = $id_link;
	$srcData['meta_value_id_ins'] = Registry::get('userdata/id_user');
	$srcData['meta_value_date_ins'] = time();
}

if(mb_strlen($srcData['meta_value_image'])>0){
	$convert = new WeeboNettv;
	$convert->createImageSet($srcData['meta_value_image']);
}

$mm = new DataProcessXmlWizard;

$mm->input = array();
$mm->input['sourceData'] = $srcData;
$mm->input['id'] = $id_show;
$mm->input['fieldName'] = 'id_show';
$mm->input['tableName'] = '_nettv_shows';
$mm->input['tableData'] = array(
	'title' => array('title' => Lng::get('nettv/tv_show_title'), 'system_type' => 'text', 'validate' => true, 'unique' => false, 'predefined' => 0, 'size' => 255),
	'id_dir' => array('title' => Lng::get('nettv/tv_id_dir'), 'system_type' => 'method', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 65535, 'default_value' => 'WeeboNettv::chooseGallery'),
	'id_active' => array('title' => Lng::get('nettv/tv_show_active'), 'system_type' => 'bool', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 1, 'default_value' => 0),
	'id_archive' => array('title' => Lng::get('nettv/tv_show_archive'), 'system_type' => 'bool', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 1, 'default_value' => 0),
	'description_short' => array('title' => Lng::get('nettv/tv_show_description_short'), 'system_type' => 'blob', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 65535),
	'description' => array('title' => Lng::get('nettv/tv_show_description'), 'system_type' => 'blob', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 65535),
	'image' => array('title' => Lng::get('nettv/tv_show_image'), 'system_type' => 'method', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 255, 'default_value' => 'WeeboNettv::getImageFile'),
);

if($id_show>0){
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
