<?php
$id_article = isset($_POST['id_article']) && (int)$_POST['id_article']>0 ? (int)$_POST['id_article']: 0;

$srcData = $_POST;

if($id_article>0){
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
$mm->input['id'] = $id_article;
$mm->input['fieldName'] = 'id_article';
$mm->input['tableName'] = '_mm_articles';
$mm->input['tableData'] = array(
	'title' => array('title' => Lng::get('mediamix/mwms_article_title'), 'system_type' => 'text', 'validate' => true, 'unique' => false, 'predefined' => 0, 'size' => 255),
	'public_order' => array('title' => Lng::get('mediamix/mwms_source_public_order'), 'system_type' => 'text', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 255, 'default_value' => 0),
	'data' => array('title' => Lng::get('mediamix/mwms_article_data'), 'system_type' => 'blob', 'validate' => true, 'unique' => false, 'predefined' => 0, 'size' => 65535, 'cleanup' => 1),
	'link' => array('title' => Lng::get('mediamix/mwms_article_link'), 'system_type' => 'blob', 'validate' => true, 'unique' => false, 'predefined' => 0, 'size' => 65535, 'cleanup' => 1),
	'date_public' => array('title' => Lng::get('mediamix/mwms_article_date_public'), 'system_type' => 'datetime', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 255, 'default_value' => 0),
	'id_public' => array('title' => Lng::get('mediamix/mwms_article_public'), 'system_type' => 'bool', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 1),
	//'id_type' => array('title' => Lng::get('mediamix/mwms_article_type'), 'system_type' => 'bool', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 1),
);

if($id_article>0){
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
		MediaMix.closeTab(selected);
		$("#tabs").tabs('option', 'active', 0);
		$("#tabs").tabs('load', 0);
	}
});
/* ]]> */
</script>
