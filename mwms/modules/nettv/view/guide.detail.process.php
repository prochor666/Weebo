<?php
$id_guide = isset($_GET['id_guide']) && (int)$_GET['id_guide']>0 ? (int)$_GET['id_guide']: 0;

$mm = new DataProcessXmlWizard;

$srcData = $_POST;

$mm->input = array();
$mm->input['sourceData'] = $srcData;
$mm->input['id'] = $id_guide;
$mm->input['fieldName'] = 'id_guide';
$mm->input['tableName'] = '_nettv_guide';
$mm->input['tableData'] = array(
	'title' => array('title' => Lng::get('nettv/tv_guide_title'), 'system_type' => 'text', 'validate' => true, 'unique' => false, 'predefined' => 0, 'size' => 255),
	'id_show' => array('title' => Lng::get('nettv/tv_show_load'), 'system_type' => 'method', 'validate' => true, 'unique' => false, 'predefined' => 0, 'size' => 20, 'default_value' => 'WeeboNettv::getShowList'),
	'description' => array('title' => Lng::get('nettv/tv_guide_description'), 'system_type' => 'blob', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 65535),
	'date_from' => array('title' => Lng::get('nettv/tv_guide_from'), 'system_type' => 'datetime', 'validate' => true, 'unique' => false, 'predefined' => 0, 'size' => 20, 'default_value' => time()),
	//'date_to' => array('title' => Lng::get('nettv/tv_guide_to'), 'system_type' => 'datetime', 'validate' => true, 'unique' => false, 'predefined' => 0, 'size' => 20, 'default_value' => time()),
);

if($id_guide>0){
	$mm->input['tableData']['id_upd'] = array('title' => 'hidden', 'system_type' => 'source', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 10);
	$mm->input['tableData']['date_upd'] = array('title' => 'hidden', 'system_type' => 'source', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 10);
}else{
	$mm->input['tableData']['id_ins'] = array('title' => 'hidden', 'system_type' => 'source', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 10);
	$mm->input['tableData']['date_ins'] = array('title' => 'hidden', 'system_type' => 'source', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 10);
}

$mm->input['metaUse'] = false;
$mm->input['metaConnectId'] = 'id_connect';
$mm->input['metaTypesTableName'] = null;
$mm->input['metaDataTableName'] = null;

$mm->init();

echo $mm->showForm();
?>
<script type="text/javascript">
/* <![CDATA[ */
$(document).ready(function(){
	
	/* INIT */
	var showID = parseInt($('#edit_field_<?php echo $mm->input['tableName'].'_'.$mm->id; ?>_id_show').val());
	var showTitle = $('#edit_field_<?php echo $mm->input['tableName'].'_'.$mm->id; ?>_title');
	if( showID > 0 && showTitle.val().length == 0  )
	{
		showTitle.val( $('#edit_field_<?php echo $mm->input['tableName'].'_'.$mm->id; ?>_id_show option[value="'+showID+'"]').text() );
	}
	
	/* SHOW CHANGE */
	$('#edit_field_<?php echo $mm->input['tableName'].'_'.$mm->id; ?>_id_show').on('change', function()
	{
		var showID = parseInt($(this).val());
		if( showID > 0 && showTitle.val().length == 0  )
		{
			newVal = $('#edit_field_<?php echo $mm->input['tableName'].'_'.$mm->id; ?>_id_show  option[value="'+showID+'"]').text();
		}else{
			newVal = showTitle.val();
		}
		
		showTitle.val(newVal);
		return false;
	});
	
	$('button.detail_save_meta_<?php echo $mm->input['tableName'].'_'.$mm->id; ?>').click(
		function(){
			weeboMeta.applyCallback('<?php echo $mm->input['tableName'].'_'.$mm->id; ?>', 'require&file=/mwms/modules/nettv/view/guide.detail.process.save.php');
		}
	).button({
		icons: {
			primary: "ui-icon-circle-check",
			text: false
		}
	});
});
/* ]]> */
</script>
