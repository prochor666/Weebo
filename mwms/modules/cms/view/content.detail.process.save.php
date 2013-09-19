<?php
$id_content = isset($_POST['id_content']) && (int)$_POST['id_content']>0 ? (int)$_POST['id_content']: 0;
$id_link = isset($_GET['id_link']) ? (int)$_GET['id_link']: 0;

$srcData = $_POST;

if($id_content>0){
	//$srcData['meta_value_id_link'] = $id_link;
	$srcData['meta_value_id_upd'] = Registry::get('userdata/id_user');
	$srcData['meta_value_date_upd'] = time();
	$srcData['meta_value_textmap'] = Filter::makeUrlString($srcData['meta_value_title']).'-'.hash('crc32', $id_content);
}else{
	//$srcData['meta_value_id_link'] = $id_link;
	$srcData['meta_value_id_ins'] = Registry::get('userdata/id_user');
	$srcData['meta_value_date_ins'] = time();
	$lastID = Db::get_last_id(_SQLPREFIX_.'_cms_content');
	$srcData['meta_value_textmap'] = Filter::makeUrlString($srcData['meta_value_title']).'-'.hash('crc32' ,($lastID + 1));
}


if(mb_strlen($srcData['meta_value_annotation_image'])){
	$cms = new Cms;
	$srcData['meta_value_annotation_image'] = $cms->annotationThumb($srcData['meta_value_annotation_image']);
}

$ass = new DataProcessXmlWizard;

$ass->input = array();
$ass->input['sourceData'] = $srcData;
$ass->input['id'] = $id_content;
$ass->input['fieldName'] = 'id_content';
$ass->input['tableName'] = '_cms_content';
$ass->input['tableData'] = array(
	'title' => array('title' => Lng::get('cms/mwms_content_title'), 'system_type' => 'text', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 255),
	'display_script' => array('title' => Lng::get('cms/mwms_link_display_script'), 'system_type' => 'method', 'validate' => true, 'unique' => false, 'predefined' => 0, 'size' => 65535, 'default_value' => 'Cms::getViews'),
	'display_script_param' => array('title' => Lng::get('cms/mwms_link_display_script_param'), 'system_type' => 'method', 'validate' => true, 'unique' => false, 'predefined' => 0, 'size' => 65535, 'default_value' => 'Cms::getMethodValue'),
	'annotation_text' => array('title' => Lng::get('cms/mwms_content_annotation_text'), 'system_type' => 'blob', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 65535, 'cleanup' => 1),
	'annotation_image' => array('title' => Lng::get('cms/mwms_content_annotation_image'), 'system_type' => 'method', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 255, 'default_value' => 'Cms::getAnnotationImage'),
	'id_brief_level' => array('title' => Lng::get('cms/mwms_content_id_brief_level'), 'system_type' => 'method', 'validate' => true, 'unique' => false, 'predefined' => 0, 'size' => 10, 'default_value' => 'Cms::getDisplayModes'),
	'id_link' => array('title' => Lng::get('cms/mwms_content_link'), 'system_type' => 'method', 'validate' => true, 'unique' => false, 'predefined' => 0, 'size' => 10, 'default_value' => 'Cms::contentLinksSelect'),
	'content' => array('title' => Lng::get('cms/mwms_content_text'), 'system_type' => 'blob', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 16777215),
	'keywords' => array('title' => Lng::get('cms/mwms_content_keywords'), 'system_type' => 'text', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 255),
	'ext_link' => array('title' => Lng::get('cms/mwms_content_ext_link'), 'system_type' => 'text', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 65535),
	'public_order' => array('title' => Lng::get('cms/mwms_content_public_order'), 'system_type' => 'int', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 10, 'default_value' => 0),
	'id_public' => array('title' => Lng::get('cms/mwms_content_id_public'), 'system_type' => 'bool', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 1, 'default_value' => 0),
	'id_blank' => array('title' => Lng::get('cms/mwms_content_id_blank'), 'system_type' => 'bool', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 1, 'default_value' => 0),
	'id_rss' => array('title' => Lng::get('cms/mwms_content_id_rss'), 'system_type' => 'bool', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 1, 'default_value' => 0),
	'id_sticky' => array('title' => Lng::get('cms/mwms_content_id_sticky'), 'system_type' => 'bool', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 1, 'default_value' => 0),
	'id_date_display' => array('title' => Lng::get('cms/mwms_id_date_display'), 'system_type' => 'bool', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 1, 'default_value' => 0),
	'date_public' => array('title' => Lng::get('cms/mwms_content_date_public'), 'system_type' => 'datetime', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 10, 'default_value' => time()),
	'id_date_restrict' => array('title' => Lng::get('cms/mwms_content_id_date_restrict'), 'system_type' => 'bool', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 1),
	'publish_date_from' => array('title' => Lng::get('cms/mwms_content_publish_date_from'), 'system_type' => 'datetime', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 10, 'default_value' => time()),
	'publish_date_to' => array('title' => Lng::get('cms/mwms_content_publish_date_to'), 'system_type' => 'datetime', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 10, 'default_value' => time()),
);

if($id_content>0){
	//$ass->input['tableData']['id_link'] = array( 'title' => 'hidden', 'system_type' => 'int', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 10);
	$ass->input['tableData']['id_upd'] = array('title' => 'hidden', 'system_type' => 'int', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 10);
	$ass->input['tableData']['date_upd'] = array('title' => 'hidden', 'system_type' => 'int', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 10);
	$ass->input['tableData']['textmap'] = array('title' => 'hidden', 'system_type' => 'text', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 255);
}else{
	//$ass->input['tableData']['id_link'] = array( 'title' => 'hidden', 'system_type' => 'int', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 10);
	$ass->input['tableData']['id_ins'] = array('title' => 'hidden', 'system_type' => 'int', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 10);
	$ass->input['tableData']['date_ins'] = array('title' => 'hidden', 'system_type' => 'int', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 10);
	$ass->input['tableData']['textmap'] = array('title' => 'hidden', 'system_type' => 'text', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 255);
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
	var allowsave = <?php echo !$ass->allowsave ? 0: 1; ?>;
	contentChange = <?php echo $id_link; ?>;
	
	$('.meta_head div.warn').remove();
		
	$('div.warn').each(function()
	{
		var elem = $(this).attr('title');
		var header = $('#'+elem).parent('td').parent('tr').find('td.meta_head');
		header.append($(this));
	});
	
	if(allowsave == 1){
		var selected = $('#tabs').tabs('option', 'active');
		cms.closeTab(selected);
		$("#tabs").tabs('option', 'active', 1);
		$("#tabs").tabs('load', 1);
	}
});
/* ]]> */
</script>
