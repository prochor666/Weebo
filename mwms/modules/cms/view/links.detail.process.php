<?php
$id_link = isset($_GET['id_link']) && (int)$_GET['id_link']>0 ? (int)$_GET['id_link']: 0;

Registry::set('cms_active_link', $id_link);

$ass = new DataProcessXmlWizard;

$ass->input = array();
$ass->input['sourceData'] = $_POST;
$ass->input['id'] = $id_link;
$ass->input['fieldName'] = 'id_link';
$ass->input['tableName'] = '_cms_links';
$ass->input['tableData'] = array(
	'title' => array('title' => Lng::get('cms/mwms_link_title'), 'system_type' => 'text', 'validate' => true, 'unique' => false, 'predefined' => 0, 'size' => 255),
	'link_title' => array('title' => Lng::get('cms/mwms_link_head_title'), 'system_type' => 'text', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 255),
	'description' => array('title' => Lng::get('cms/mwms_link_description'), 'system_type' => 'text', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 255),
	'keywords' => array('title' => Lng::get('cms/mwms_link_keywords'), 'system_type' => 'text', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 255),
	'template' => array('title' => Lng::get('cms/mwms_link_template'), 'system_type' => 'method', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 255, 'default_value' => 'Cms::getTemplates'),
	'id_sub' => array('title' => Lng::get('cms/mwms_id_sub'), 'system_type' => 'method', 'validate' => true, 'unique' => false, 'predefined' => 0, 'size' => 10, 'default_value' => 'LinksBrowserTemplate::linksSelect'),
	'id_position' => array('title' => Lng::get('cms/mwms_id_position'), 'system_type' => 'method', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 5, 'default_value' => 'Cms::getMenuPosition'),
	'lng' => array('title' => Lng::get('cms/mwms_link_lng'), 'system_type' => 'method', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 10, 'default_value' => 'Cms::getLngList'),
	'ext_link' => array('title' => Lng::get('cms/mwms_link_ext_link'), 'system_type' => 'text', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 65535),
	'public_order' => array('title' => Lng::get('cms/mwms_link_public_order'), 'system_type' => 'text', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 255),
	'default_order' => array('title' => Lng::get('cms/mwms_link_default_order'), 'system_type' => 'method', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 255, 'default_value' => 'Cms::getOrderTypes'),
	'id_public' => array('title' => Lng::get('cms/mwms_link_id_public'), 'system_type' => 'bool', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 1),
	'id_blank' => array('title' => Lng::get('cms/mwms_link_id_blank'), 'system_type' => 'bool', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 1),
	'id_rss' => array('title' => Lng::get('cms/mwms_link_id_rss'), 'system_type' => 'bool', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 1),
	'id_menu' => array('title' => Lng::get('cms/mwms_link_id_menu'), 'system_type' => 'bool', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 1),
	'id_sitemap' => array('title' => Lng::get('cms/mwms_link_id_sitemap'), 'system_type' => 'bool', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 1),
	'id_pager' => array('title' => Lng::get('cms/mwms_link_id_pager'), 'system_type' => 'bool', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 1),
	'pager_default' => array('title' => Lng::get('cms/mwms_link_pager_default'), 'system_type' => 'text', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 255)
);

if($id_link>0){
	$ass->input['tableData']['domain'] = array('title' => 'hidden', 'system_type' => 'source', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 10);
	$ass->input['tableData']['id_upd'] = array('title' => 'hidden', 'system_type' => 'source', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 10);
	$ass->input['tableData']['date_upd'] = array('title' => 'hidden', 'system_type' => 'source', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 10);
	$ass->input['tableData']['textmap'] = array('title' => 'hidden', 'system_type' => 'source', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 255);
}else{
	$ass->input['tableData']['domain'] = array('title' => 'hidden', 'system_type' => 'source', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 10);
	$ass->input['tableData']['id_ins'] = array('title' => 'hidden', 'system_type' => 'source', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 10);
	$ass->input['tableData']['date_ins'] = array('title' => 'hidden', 'system_type' => 'source', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 10);
	$ass->input['tableData']['textmap'] = array('title' => 'hidden', 'system_type' => 'source', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 255);
}

$ass->input['metaUse'] = false;
$ass->input['metaConnectId'] = 'id_connect';
$ass->input['metaTypesTableName'] = null;
$ass->input['metaDataTableName'] = null;

$ass->init();

echo $ass->showForm();

$_SESSION['cms_id_sub'] = $ass->profileData['id_sub'];
$_SESSION['cms_link_title'] = $ass->profileData['title'];

//System::dump($ass->profileData);
?>
<script type="text/javascript">
/* <![CDATA[ */
$(document).ready(function(){
	
	var id_link = <?php echo $id_link; ?>;
	var activeDomainLng = '<?php echo Registry::get('active_domain_lng'); ?>';
	
	if(id_link == 0){
		$('select[name="meta_value_lng"]').val(activeDomainLng);
	}
	
	$('button.detail_save_meta_<?php echo $ass->input['tableName'].'_'.$ass->id; ?>').click(
		function(){
			weeboMeta.applyCallback('<?php echo $ass->input['tableName'].'_'.$ass->id; ?>', 'require&file=/mwms/modules/cms/view/links.detail.process.save.php');
		}
	).button({
		icons: {
			primary: "ui-icon-circle-check",
			text: false
		}
	});
	
	$('select[name="meta_value_id_sub"]').change(
		function(){
			var linkChange = 1;
		}
	);
});
/* ]]> */
</script>


