<?php
$id_media = isset($_POST['id_media']) && (int)$_POST['id_media']>0 ? (int)$_POST['id_media']: 0;

$srcData = $_POST;

if($id_media>0){
	$srcData['meta_value_id_upd'] = Registry::get('userdata/id_user');
	$srcData['meta_value_date_upd'] = time();
}else{
	$srcData['meta_value_id_ins'] = Registry::get('userdata/id_user');
	$srcData['meta_value_date_ins'] = time();
}
 
$ass = new DataProcessXmlWizard;

$ass->input = array();
$ass->input['sourceData'] = $srcData;
$ass->input['id'] = $id_media;
$ass->input['fieldName'] = 'id_media';
$ass->input['tableName'] = '_cms_media_list';
$ass->input['tableData'] = array(
	'title' => array('title' => Lng::get('cms/mwms_media_title'), 'system_type' => 'text', 'validate' => true, 'unique' => true, 'predefined' => 0, 'size' => 255),
	'public_ord' => array('title' => Lng::get('cms/mwms_media_public_ord'), 'system_type' => 'int', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 11),
	'path' => array('title' => Lng::get('cms/mwms_media_file'), 'system_type' => 'method', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 65535, 'default_value' => 'Cms::getMediaThumb')
);

if($id_media>0){
	$ass->input['tableData']['id_upd'] = array('title' => 'hidden', 'system_type' => 'int', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 10);
	$ass->input['tableData']['date_upd'] = array('title' => 'hidden', 'system_type' => 'int', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 10);
}else{
	$ass->input['tableData']['id_ins'] = array('title' => 'hidden', 'system_type' => 'int', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 10);
	$ass->input['tableData']['date_ins'] = array('title' => 'hidden', 'system_type' => 'int', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 10);
}

$ass->input['metaUse'] = false;
$ass->input['metaConnectId'] = 'id_conect';
$ass->input['metaTypesTableName'] = null;
$ass->input['metaDataTableName'] = null;

$ass->init();

echo $ass->extract();

if($ass->allowsave){
	Storage::makeDir('content/'.$srcData['meta_value_path']);
}
?>
<script type="text/javascript">
/* <![CDATA[ */
var allowsave = <?php echo !$ass->allowsave ? 0: 1; ?>;

$(document).ready(function(){
	
	$('.meta_head div.warn').remove();

	$('div.warn').each(function()
	{
		var elem = $(this).attr('title');
		var header = $('#'+elem).parent('td').parent('tr').find('td.meta_head');
		header.append($(this));
	});
	
	if( allowsave == 1 ){
		var selected = $('#tabs').tabs('option', 'selected');
		$("#tabs").tabs('remove', selected);
		$("#tabs").tabs('load', 1);
		$("#tabs").tabs('select', 1);
	}
});
/* ]]> */
</script>
