<?php
$id_item = isset($_POST['id_item']) && (int)$_POST['id_item']>0 ? (int)$_POST['id_item']: 0;

$srcData = $_POST;

if($id_item>0){
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

$lastInsert = Db::get_last_id(_SQLPREFIX_.'_nxmarket_items');

if($mm->allowsave === true){
	
	$config = Registry::get('moduledata/nxmarket');
	
	$images = json_decode($srcData['meta_value_images'], true);
	$id_item = $id_item == 0 ? (int)$lastInsert: $id_item;
	$itemDir = $config['mediaDir'].'/'.$id_item;
	$newItems = array();
	$allItems = array();
	
	Storage::makeDir($itemDir);
	//System::dump($images);
	
	foreach($images as $im){
		if($im['new'] === true){
			
			$from = $im['image'];
			$to = $itemDir.'/'.basename($im['image']);
			
			if($config['deleteSourceFiles'] === true)
			{
				Storage::moveFile($from, $to);
				
			}else{
				Storage::copyFile($from, $to);
			}
			
			$newItems[$to] = $im['title'];
		}
		
		$allItems[$to] = $im['title'];
	}
	
	$md = opendir(Registry::get('serverdata/root').'/'.$itemDir);
	$fl = array(
		"files" => array()
	);
	
	while(false!==($item = readdir($md))){
		if($item != "." && $item != ".." && is_dir(Registry::get('serverdata/root').'/'.$itemDir.'/'.$item)){
			//array_push($fl['dirs'], $item);
		}elseif($item != "." && $item != ".." && is_file(Registry::get('serverdata/root').'/'.$itemDir.'/'.$item) && mb_substr($item, 0, 3) != 'th_' ){
			array_push($fl['files'], $itemDir.'/'.$item);
		}
	}

	closedir($md);
	usort($fl['files'], 'strcasecmp');

	
	foreach($fl['files'] as $file)
	{
		
		$ext = System::extension(basename($file));
		
		if ( ( $ext == 'jpg' || $ext == 'png' || $ext == 'gif' ) && array_key_exists($file, $allItems) )
		{
			// THUMB 
			if( !file_exists(Registry::get('serverdata/root').'/'.$itemDir.'/th_'.basename($file)) && array_key_exists($file, $allItems) )
			{
				$th = new SimpleImage();
				$th->load(Registry::get('serverdata/root').'/'.$file);
				
				if($config['image_thumb_preffer_axxis'] === true)
				{
					$th->resizeToWidth($config['image_size']['thWidth']);
				}else{
					$th->resizeToHeight($config['image_size']['thHeight']);
				}
				
				$th->save(Registry::get('serverdata/root').'/'.$itemDir.'/th_'.basename($file));
				
				umask(0000);
				chmod(Registry::get('serverdata/root').'/'.$itemDir.'/th_'.basename($file), 0777);
			}
			
			// RESIZE
			if( array_key_exists($file, $newItems) )
			{
				$image = new SimpleImage();
				$image->load(Registry::get('serverdata/root').'/'.$itemDir.'/'.basename($file));
				
				if($config['image_thumb_preffer_axxis'] === true)
				{
					$image->resizeToWidth($config['image_size']['origWidth']);
				}else{
					$image->resizeToHeight($config['image_size']['origHeight']);
				}
				
				$image->save(Registry::get('serverdata/root').'/'.$file);
				
				umask(0000);
				chmod(Registry::get('serverdata/root').'/'.$file, 0777);
			}
			
		}else{
			$ukey = $file;
			
			unset($allItems[$file]);
			unset($newItems[$file]);
			
			Storage::deleteFile($file);
			@Storage::deleteFile($itemDir.'/th_'.basename($file));
		}
	}
	
	$save = array();
	foreach($allItems as $key => $item){
		//System::dump( $item);
		$save[] = array('image' => $key, 'title' => $item, 'new' => false);
	}
	
	Db::query("UPDATE "._SQLPREFIX_."_nxmarket_items SET images = '".json_encode($save)."' WHERE id_item = ".(int)$id_item);
}
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
		NxMarket.closeTab(selected);
		$("#tabs").tabs('option', 'active', 0);
		$("#tabs").tabs('load', 0);
	}
	
});
/* ]]> */
</script>
