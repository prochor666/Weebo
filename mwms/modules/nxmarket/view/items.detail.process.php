<?php
$id_item = isset($_GET['id_item']) && (int)$_GET['id_item']>0 ? (int)$_GET['id_item']: 0;

$mm = new DataProcessXmlWizard;

$srcData = $_POST;

$mm->input = array();
$mm->input['sourceData'] = $srcData;
$mm->input['id'] = $id_item;
$mm->input['fieldName'] = 'id_item';
$mm->input['tableName'] = '_nxmarket_items';
$mm->input['tableData'] = array(
	'title' => array('title' => Lng::get('nxmarket/items_title'), 'system_type' => 'text', 'validate' => true, 'unique' => false, 'predefined' => 0, 'size' => 255),
	'code' => array('title' => Lng::get('nxmarket/items_code'), 'system_type' => 'text', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 255),
	'id_public' => array('title' => Lng::get('nxmarket/items_public'), 'system_type' => 'bool', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 1, 'default_value' => 1),
	'public_order' => array('title' => Lng::get('nxmarket/items_public_order'), 'system_type' => 'int', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 255, 'default_value' => 0),
	'price' => array('title' => Lng::get('nxmarket/items_price'), 'system_type' => 'float', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 255, 'default_value' => 0.00),
	'vat' => array('title' => Lng::get('nxmarket/items_vat'), 'system_type' => 'float', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 255, 'default_value' => Lng::get('nxmarket/items_default_vat')),
	'fake_price' => array('title' => Lng::get('nxmarket/items_fake_price'), 'system_type' => 'float', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 255, 'default_value' => 0.00),
	'stock' => array('title' => Lng::get('nxmarket/items_stock'), 'system_type' => 'int', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 255, 'default_value' => 0),
	'units' => array('title' => Lng::get('nxmarket/items_units'), 'system_type' => 'text', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 20, 'default_value' => Lng::get('nxmarket/items_default_unit') ),
	'description' => array('title' => Lng::get('nxmarket/items_description'), 'system_type' => 'blob', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 65535), //, 'default_lock_url' => 'nxmarket'
	'images' => array('title' => Lng::get('nxmarket/items_images'), 'system_type' => 'blob', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 65535, 'cleanup' => 1),
	'internal' => array('title' => Lng::get('nxmarket/items_internal'), 'system_type' => 'bool', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 1, 'default_value' => 0),
);

if($id_item>0){
	$mm->input['tableData']['id_upd'] = array('title' => 'hidden', 'system_type' => 'source', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 10);
	$mm->input['tableData']['date_upd'] = array('title' => 'hidden', 'system_type' => 'source', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 10);
}else{
	$mm->input['tableData']['id_ins'] = array('title' => 'hidden', 'system_type' => 'source', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 10);
	$mm->input['tableData']['date_ins'] = array('title' => 'hidden', 'system_type' => 'source', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 10);
}

$mm->input['metaUse'] = false;
$mm->input['metaConnectId'] = 'id_item';
$mm->input['metaTypesTableName'] = null;
$mm->input['metaDataTableName'] = null;

$mm->init();

echo $mm->showForm();

$ijs = '[]';

if(array_key_exists('profileData', $mm) === true && is_array($mm->profileData) && array_key_exists('id_item', $mm->profileData) === true && $id_item>0)
{
//System::dump($mm->profileData);

	$item = $mm->profileData;

	$imageTemp = json_decode($item['images']);
	
	if(is_array($imageTemp))
	{
		$ijs = $item['images'];
	}
}

?>

<script type="text/javascript">
/* <![CDATA[ */

// Create manipulation unique object;
var imageProcessing<?php echo $id_item; ?> = {}

imageProcessing<?php echo $id_item; ?>.imageSource = $('#edit_field__nxmarket_items_<?php echo $id_item; ?>_images');
imageProcessing<?php echo $id_item; ?>.imageStr = 'nxmarket-imageset-<?php echo $id_item; ?>';
imageProcessing<?php echo $id_item; ?>.imageContainer = $('<ul class="nxmarket-thumbs nxmarket-images-<?php echo $id_item; ?>" id="'+imageProcessing<?php echo $id_item; ?>.imageStr+'"></ul>');

imageProcessing<?php echo $id_item; ?>.imageSource.hide();

// init from db
imageProcessing<?php echo $id_item; ?>.initImageSet = function(){
	
	var imageData = <?php echo $ijs; ?>;
	
	for(i in imageData){
		var xFile = imageData[i].image;
		var xThumb = weebo.settings.SiteRoot+'/'+imageData[i].image;
		
		var newImage = $('<li class="nxmarket-thumb ui-state-default" title="'+xFile+'"><img src="'+xThumb+'" alt="'+xFile+'" /> <button class="closethick" style="float: right;">&nbsp;</button><span class="thumb-path">'+xFile+'</span><span class="thumb-title-wrap"><input type="text" value="'+imageData[i].title.toString()+'" name="thumb-title" class="thumb-title text" /></span> <div class="clear"></div></li>');
		
		imageProcessing<?php echo $id_item; ?>.imageContainer.append(newImage); 
	}
	
	imageProcessing<?php echo $id_item; ?>.updateImageSet();
}

// reindex
imageProcessing<?php echo $id_item; ?>.sortImages = function(){
	var tgt = $('#'+imageProcessing<?php echo $id_item; ?>.imageStr);
	tgt.sortable('refresh');
	tgt.find( '.nxmarket-thumb img' ).tooltip({
		show: {
			effect: "slideDown",
			duration: 170
		},
		hide: {
			effect: "slideUp",
			duration: 80
		},
		track: true,
		content: function() {
			return '<img src="' + weebo.settings.SiteRoot +'/' + $( this ).attr('title') + '" alt="~" style="width: 300px; float: left" />';
		}
	});
	
	imageProcessing<?php echo $id_item; ?>.imageContainer.disableSelection();
}

// buttons
imageProcessing<?php echo $id_item; ?>.buttonsUpdate = function(){
	
	var buttons = $('#'+imageProcessing<?php echo $id_item; ?>.imageStr).find('button');
	var inputs = $('#'+imageProcessing<?php echo $id_item; ?>.imageStr).find('input');
	
	buttons.on('click', function(e){
		e.preventDefault();
		$(this).parent('li').remove();
		imageProcessing<?php echo $id_item; ?>.sortImages();
		imageProcessing<?php echo $id_item; ?>.updateImageSet();
	}).button({
		icons : {
			primary : 'ui-icon-closethick'
		},
		text : false 
	});
	
	inputs.on('keyup', function(e){
		//e.preventDefault();
		imageProcessing<?php echo $id_item; ?>.sortImages();
		imageProcessing<?php echo $id_item; ?>.updateImageSet();
	});
	
	inputs.on('change', function(e){
		//e.preventDefault();
		imageProcessing<?php echo $id_item; ?>.sortImages();
		imageProcessing<?php echo $id_item; ?>.updateImageSet();
	});
}

// update source
imageProcessing<?php echo $id_item; ?>.updateImageSet = function(){
	
	var imageSet = $('#'+imageProcessing<?php echo $id_item; ?>.imageStr+' li'); 
	var imageData = [];
	
	imageSet.each(function(i){
		t = { 'image': $(this).attr('title'), 'title': $(this).find('input.thumb-title').val(), 'new': true };
		imageData[i]=t;
	});
	
	imageProcessing<?php echo $id_item; ?>.imageSource.val(JSON.stringify(imageData));
}


// RUN
$(document).ready(function(){

// UPLOADER 
var aDir = 'require&file=/mwms/modules/nxmarket/view/nxmarket.media.admin.php';

imageProcessing<?php echo $id_item; ?>.imageSource.parent('div').parent('td').append('<div id="uploader-panel"><button id="pickfile_<?php echo $mm->input['tableName'].'_'.$id_item; ?>" class="pickfile button"><?php echo Lng::get('nxmarket/image_upload'); ?></button></div><div id="uploader-box-wrapper"></div>').append(imageProcessing<?php echo $id_item; ?>.imageContainer);

imageProcessing<?php echo $id_item; ?>.initImageSet();

imageProcessing<?php echo $id_item; ?>.imageContainer.sortable({
	//helper: "ui-state-highlight",
	placeholder: "ui-state-highlight",
	stop: function( event, ui ) {
		imageProcessing<?php echo $id_item; ?>.updateImageSet();
	}
});

imageProcessing<?php echo $id_item; ?>.buttonsUpdate();

// Set image button
$('#pickfile_<?php echo $mm->input['tableName'].'_'.$id_item; ?>').on('click',
	function(){
		NxMarket.newFile(aDir, '<?php echo Lng::get('nxmarket/image_upload'); ?>');
		
		// CLEAR & HANDLE MEDIAMANAGER FILE CLICK 
		$(document).off('click', 'a.file');
		$(document).on('click', 'a.file', function(e)
		{
			e.preventDefault();
			var xFile = $(this).attr('href');
			var xThumb = $(this).find('img').attr('src');
			
			var newImage = $('<li class="nxmarket-thumb ui-state-default" title="'+xFile+'"><img src="'+xThumb+'" alt="'+xFile+'" /> <button class="closethick" style="float: right;">&nbsp;</button> <span class="thumb-path">'+xFile+'</span><span class="thumb-title-wrap"><input type="text" value="" name="thumb-title" class="thumb-title text" /></span> <div class="clear"></div></li>');
			
			$('#'+imageProcessing<?php echo $id_item; ?>.imageStr).append(newImage);
			
			imageProcessing<?php echo $id_item; ?>.sortImages();
			imageProcessing<?php echo $id_item; ?>.updateImageSet();
			imageProcessing<?php echo $id_item; ?>.buttonsUpdate();
			//$('#weebo-modal-dialog-content').dialog("close");
			return false;
		});
		
		return false;
	}
).button();

<?php
if(array_key_exists('profileData', $mm) === true && is_array($mm->profileData) && array_key_exists('id_item', $mm->profileData) === true && $id_item>0){
?>
	
	
	
	
<?php

}
?>
	imageProcessing<?php echo $id_item; ?>.imageContainer.find( '.nxmarket-thumb img' ).tooltip({
		show: {
			effect: "slideDown",
			duration: 170
		},
		hide: {
			effect: "slideUp",
			duration: 80
		},
		track: true,
		content: function() {
			return '<img src="' + weebo.settings.SiteRoot +'/' + $( this ).attr('title') + '" alt="~" style="width: 300px; float: left" />';
		}
	});
	
	// MAIN FORM 
	$('button.detail_save_meta_<?php echo $mm->input['tableName'].'_'.$mm->id; ?>').on('click', 
		function(e){
			e.preventDefault();
			weeboMeta.applyCallback('<?php echo $mm->input['tableName'].'_'.$mm->id; ?>', 'require&file=/mwms/modules/nxmarket/view/items.detail.process.save.php');
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
