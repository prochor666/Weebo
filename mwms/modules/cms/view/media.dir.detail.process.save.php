<?php
$id_dir = isset($_POST['id_dir']) && (int)$_POST['id_dir']>0 ? (int)$_POST['id_dir']: 0;

$srcData = $_POST;

$domainKey = Registry::get('active_domain');
$domains = Lng::get('cms/cms_public_domains');

if($id_dir>0){
	$srcData['meta_value_id_upd'] = Registry::get('userdata/id_user');
	$srcData['meta_value_date_upd'] = time();
	$srcData['meta_value_domain'] = $domains[$domainKey]['name'];
	//$srcData['meta_value_path'] = $srcData['meta_value_path'];
}else{
	$srcData['meta_value_id_ins'] = Registry::get('userdata/id_user');
	$srcData['meta_value_date_ins'] = time();
	$srcData['meta_value_domain'] = $domains[$domainKey]['name'];
	$srcData['meta_value_path'] = $domains[$domainKey]['name'].'/gallery/gallery-'.date('Y-m-d-H-i-s');
}
 
$ass = new DataProcessXmlWizard;

$ass->input = array();
$ass->input['sourceData'] = $srcData;
$ass->input['id'] = $id_dir;
$ass->input['fieldName'] = 'id_dir';
$ass->input['tableName'] = '_cms_media_dir';
$ass->input['tableData'] = array(
	'title' => array('title' => Lng::get('cms/mwms_media_dir_title'), 'system_type' => 'text', 'validate' => true, 'unique' => true, 'predefined' => 0, 'size' => 255),
	'description' => array('title' => Lng::get('cms/mwms_media_dir_description'), 'system_type' => 'text', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 255),
	'type' => array('title' => Lng::get('cms/mwms_media_dir_type'), 'system_type' => 'method', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 20, 'default_value' => 'Cms::selectDirType'),
	'path' => array('title' => Lng::get('cms/mwms_media_dir_path'), 'system_type' => 'method', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 255, 'default_value' => 'Cms::setPath'),
);

if($id_dir>0){
	$ass->input['tableData']['domain'] = array('title' => 'hidden', 'system_type' => 'text', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 10);
	$ass->input['tableData']['id_upd'] = array('title' => 'hidden', 'system_type' => 'int', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 10);
	$ass->input['tableData']['date_upd'] = array('title' => 'hidden', 'system_type' => 'int', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 10);
}else{
	$ass->input['tableData']['domain'] = array('title' => 'hidden', 'system_type' => 'text', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 10);
	$ass->input['tableData']['id_ins'] = array('title' => 'hidden', 'system_type' => 'int', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 10);
	$ass->input['tableData']['date_ins'] = array('title' => 'hidden', 'system_type' => 'int', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 10);
}

$ass->input['metaUse'] = false;
$ass->input['metaConnectId'] = 'id_dir';
$ass->input['metaTypesTableName'] = null;
$ass->input['metaDataTableName'] = null;

$ass->init();

echo $ass->extract();

$chng = $id_dir;

$idRefresh = $id_dir == 0 ? Db::get_last_id(_SQLPREFIX_.'_cms_media_dir'): $id_dir;

$idRefresh = $ass->allowsave ? $idRefresh: 0;

Registry::set('cms_active_gallery', $idRefresh);

if($ass->allowsave){
	Storage::makeDir('content/'.$srcData['meta_value_path']);
}
?>
<script type="text/javascript">
/* <![CDATA[ */
var linkChange = <?php echo $chng; ?>;
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
		document.location.href = weebo.settings.SiteRoot + '?module=cms&sub=media.browser';
	}
});
/* ]]> */
</script>
