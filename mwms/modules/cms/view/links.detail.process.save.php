<?php
$id_link = isset($_POST['id_link']) && (int)$_POST['id_link']>0 ? (int)$_POST['id_link']: 0;
$active_link = $id_link>0 ? $id_link: ( (int) Db::get_last_id(_SQLPREFIX_."_cms_links") ) + 1;

Registry::set('cms_active_link', $active_link);

$srcData = $_POST;

$domainKey = Registry::get('active_domain');
$domains = Lng::get('cms/cms_public_domains');

$textMap = Filter::makeUrlString($srcData['meta_value_title']);

$x = new Cms;

$linkct = $x->uniqueLink($id_link, $domains[$domainKey]['name'], $textMap); 

if($linkct>0){
	$textMap = $textMap.'-'.$active_link;
}

if($id_link>0){
	$srcData['meta_value_id_upd'] = Registry::get('userdata/id_user');
	$srcData['meta_value_date_upd'] = time();
	$srcData['meta_value_textmap'] = $textMap;
	$srcData['meta_value_domain'] = $domains[$domainKey]['name'];
}else{
	$srcData['meta_value_id_ins'] = Registry::get('userdata/id_user');
	$srcData['meta_value_date_ins'] = time();
	$srcData['meta_value_textmap'] = $textMap;
	$srcData['meta_value_domain'] = $domains[$domainKey]['name'];
}

$ass = new DataProcessXmlWizard;

$ass->input = array();
$ass->input['sourceData'] = $srcData;
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
	'lng' => array('title' => Lng::get('cms/mwms_link_lng'), 'system_type' => 'text', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 255, 'default_value' => 'Cms::getLngList'),
	'ext_link' => array('title' => Lng::get('cms/mwms_link_ext_link'), 'system_type' => 'text', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 65535),
	'public_order' => array('title' => Lng::get('cms/mwms_link_public_order'), 'system_type' => 'text', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 255),
	'default_order' => array('title' => Lng::get('cms/mwms_link_default_order'), 'system_type' => 'text', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 255),
	'id_public' => array('title' => Lng::get('cms/mwms_link_id_public'), 'system_type' => 'bool', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 1),
	'id_blank' => array('title' => Lng::get('cms/mwms_link_id_blank'), 'system_type' => 'bool', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 1),
	'id_rss' => array('title' => Lng::get('cms/mwms_link_id_rss'), 'system_type' => 'bool', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 1),
	'id_menu' => array('title' => Lng::get('cms/mwms_link_id_menu'), 'system_type' => 'bool', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 1),
	'id_sitemap' => array('title' => Lng::get('cms/mwms_link_id_sitemap'), 'system_type' => 'bool', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 1),
	'id_pager' => array('title' => Lng::get('cms/mwms_link_id_pager'), 'system_type' => 'bool', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 1),
	'pager_default' => array('title' => Lng::get('cms/mwms_link_pager_default'), 'system_type' => 'text', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 255)
);

if($id_link>0){
	$ass->input['tableData']['domain'] = array('title' => 'hidden', 'system_type' => 'text', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 255);
	$ass->input['tableData']['id_upd'] = array('title' => 'hidden', 'system_type' => 'int', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 10);
	$ass->input['tableData']['date_upd'] = array('title' => 'hidden', 'system_type' => 'int', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 10);
	$ass->input['tableData']['textmap'] = array('title' => 'hidden', 'system_type' => 'text', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 255);
}else{
	$ass->input['tableData']['domain'] = array('title' => 'hidden', 'system_type' => 'text', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 255);
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

$cms_id_sub_chng = $_POST['meta_value_id_sub'];
$cms_link_title_chng = $_POST['meta_value_title'];
$chng = $id_link == 0 || $cms_id_sub_chng != $_SESSION['cms_id_sub'] || $_SESSION['cms_link_title'] != $cms_link_title_chng ? 1: 0;
$_SESSION['cms_id_sub'] = $ass->profileData['id_sub'];

$last = $id_link>0 ? $id_link: Db::get_last_id(_SQLPREFIX_."_cms_links");

$last = !$ass->allowsave ? 0: $last;

Registry::set('cms_active_link', $last);
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
	
	if(linkChange == 1  && allowsave == 1){
		document.location.href = weebo.settings.SiteRoot + '?module=cms';
	}
});
/* ]]> */
</script>
