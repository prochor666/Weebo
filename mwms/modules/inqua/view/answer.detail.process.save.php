<?php
$id_answer = isset($_POST['id_answer']) && (int)$_POST['id_answer']>0 ? (int)$_POST['id_answer']: 0;

$srcData = $_POST;

if($id_answer>0){
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
$mm->input['id'] = $id_answer;
$mm->input['fieldName'] = 'id_answer';
$mm->input['tableName'] = '_inqua_answers';
$mm->input['tableData'] = array(
	'title' => array('title' => Lng::get('inqua/inqua_answer_title'), 'system_type' => 'text', 'validate' => true, 'unique' => false, 'predefined' => 0, 'size' => 255),
	'votes' => array('title' => Lng::get('inqua/inqua_answer_votes'), 'system_type' => 'int', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 10, 'default_value' => 0),
	'public_order' => array('title' => Lng::get('inqua/inqua_answer_order'), 'system_type' => 'int', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 10, 'default_value' => 0),
	'id_inquiry' => array('title' => Lng::get('inqua/inqua_answer_inquiry'), 'system_type' => 'method', 'validate' => true, 'unique' => false, 'predefined' => 0, 'size' => 11, 'default_value' => 'WeeboInqua::getInquiryList' ),
);

if($id_answer>0){
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
		InquaAdmin.closeTab(selected);
		$("#tabs").tabs('option', 'active', 0);
		$("#tabs").tabs('load', 0);
	}
});
/* ]]> */
</script>
