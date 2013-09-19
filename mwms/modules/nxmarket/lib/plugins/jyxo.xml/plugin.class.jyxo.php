<?php
class JyxoXML extends NXMPlugins {

public $sourceUrl, $data;

protected $meta; 

public function __construct(){
	parent::__construct();
	$this->sourceUrl = null;
	$this->meta = array();
	$this->data = false;
}

public function init(){
	$xml = @simplexml_load_file($this->sourceUrl);
	$this->data = json_encode($xml);
}

public function run(){
	
	$html = '<p>NONE</p>';
	
	if(!is_null($this->sourceUrl))
	{
		$xml = @simplexml_load_file($this->sourceUrl);
		
		if(!$xml){
			$xmlss = @file_get_contents($this->sourceUrl);
			$xmlStr = explode("\n", (string)$xmlss);
			$errors = libxml_get_errors();
			$eStr = null;
			
			foreach ($errors as $error) {
				$eStr .= '<div>'.$this->display_xml_error($error, $xmlss).'</div>';
			}
			libxml_clear_errors();
			return $eStr;
		}else{
			$itemList = $xml->SHOPITEM;
			if(count($itemList)>0)
			{
				$html = '<p>'.count($itemList).'</p>';
				foreach($itemList as $o){
					$this->importItem($o);
				}
			}
		}
	}
}


public function importItemData($o){
	$id_item = (int)$o['ID'];
	$title = (string)$o['PRODUCT'];
	$description = (string)$o['DESCRIPTION'];
	$code = (string)$o['PRODUCTNO'];
	$price = strtr((string)$o['PRICE'], ',', '.');
	$vat = strtr((string)$o['VAT'], ',', '.');
	$price_vat = strtr((string)$o['PRICE_VAT'], ',', '.');
	
	$imgurl = (string)$o['IMGURL'];
	$cat = (string)$o['CATEGORYTEXT'];
	$warranty = (string)$o['WARRANTY'];
	
	$this->meta['Výrobce'] = (string)$o['MANUFACTURER'];
	$this->meta['EAN'] = (string)$o['EAN'];
	$this->meta['Origin'] = (string)$o['URL'];
	
	foreach($o['PARAM'] as $p){
		$this->meta[(string)$p['PARAM_NAME']] = (string)$p['VAL'];
	}
	
	if($this->itemExists($id_item) === false){
		
		Db::query("INSERT INTO "._SQLPREFIX_."_nxmarket_items 
			(id_item, title, code, description, images, price, vat, id_public, id_ins, date_ins) 
			VALUES
			(".$id_item.", '".$title."', '".$code."', '".$description."', '[]', '".$price."', '".$vat."', 1, 1, ".time()." )
		");
		
		$id_item = Db::get_last_id(_SQLPREFIX_."_nxmarket_items");
		
		$imageLocal = $this->saveImageResource($id_item, $imgurl);
		
		if($imageLocal !== false)
		{
			$images = json_encode(array(array('image' => $imageLocal, 'title' => $title, 'new' => false)));
			
			Db::query("UPDATE "._SQLPREFIX_."_nxmarket_items SET
					images = '".$images."' 
				WHERE id_item = ".$id_item
			);
		}
	}else{
		
		$images = [];
		$imageLocal = $this->saveImageResource($id_item, $imgurl);
		
		if($imageLocal !== false)
		{
			$images = json_encode(array(array('image' => $imageLocal, 'title' => $title, 'new' => false)));
		}
		
		Db::query("UPDATE "._SQLPREFIX_."_nxmarket_items SET
				title = '".$title."', 
				code = '".$code."', 
				description = '".$description."', 
				images = '".$images."', 
				price = '".$price."', 
				vat = '".$vat."', 
				id_ins = 1, 
				date_ins = ".time()."
			WHERE id_item = ".$id_item
		);
	}
	
	$this->saveMeta($id_item);
	
	return '<p>Delivered item: '.$id_item.'</p>';
}


protected function importItem($o){
	$id_item = (int)$o->ID;
	$title = (string)$o->PRODUCT;
	$description = (string)$o->DESCRIPTION;
	$code = (string)$o->PRODUCTNO;
	$price = strtr((string)$o->PRICE, ',', '.');
	$vat = strtr((string)$o->VAT, ',', '.');
	$price_vat = strtr((string)$o->PRICE_VAT, ',', '.');
	
	$imgurl = (string)$o->IMGURL;
	$cat = (string)$o->CATEGORYTEXT;
	$warranty = (string)$o->WARRANTY;
	
	$this->meta['Výrobce'] = (string)$o->MANUFACTURER;
	$this->meta['EAN'] = (string)$o->EAN;
	$this->meta['Origin'] = (string)$o->URL;
	
	foreach($o->PARAM as $p){
		$this->meta[(string)$p->PARAM_NAME] = (string)$p->VAL;
	}
	
	if($this->itemExists($id_item) === false){
		
		Db::query("INSERT INTO "._SQLPREFIX_."_nxmarket_items 
			(id_item, title, code, description, images, price, vat, id_public, id_ins, date_ins) 
			VALUES
			(".$id_item.", '".$title."', '".$code."', '".$description."', '[]', '".$price."', '".$vat."', 1, 1, ".time()." )
		");
		
		$id_item = Db::get_last_id(_SQLPREFIX_."_nxmarket_items");
		
		$imageLocal = $this->saveImageResource($id_item, $imgurl);
		
		if($imageLocal !== false)
		{
			$images = json_encode(array(array('image' => $imageLocal, 'title' => $title, 'new' => false)));
			
			Db::query("UPDATE "._SQLPREFIX_."_nxmarket_items SET
					images = '".$images."' 
				WHERE id_item = ".$id_item
			);
		}
	}else{
		
		$images = [];
		$imageLocal = $this->saveImageResource($id_item, $imgurl);
		
		if($imageLocal !== false)
		{
			$images = json_encode(array(array('image' => $imageLocal, 'title' => $title, 'new' => false)));
		}
		
		Db::query("UPDATE "._SQLPREFIX_."_nxmarket_items SET
				title = '".$title."', 
				code = '".$code."', 
				description = '".$description."', 
				images = '".$images."', 
				price = '".$price."', 
				vat = '".$vat."', 
				id_public = 1, 
				id_ins = 1, 
				date_ins = ".time()."
			WHERE id_item = ".$id_item
		);
	}
	
	$this->saveMeta($id_item);
}

protected function saveMeta($id_item){
	foreach($this->meta as $tag => $val){
		if($this->metaExists($id_item, $tag) === false){
			Db::query("INSERT INTO "._SQLPREFIX_."_nxmarket_meta 
			(id_item, tag, value) 
			VALUES
			(".$id_item.", '".$tag."', '".$val."' )
		");
		}else{
			Db::query("UPDATE "._SQLPREFIX_."_nxmarket_meta SET
				value = '".$val."'
				WHERE id_item = ".$id_item." AND tag LIKE  '".$tag."' "
		);
		}
	}
}

protected function itemExists($id_item){
	$qq = Db::result("SELECT id_item FROM "._SQLPREFIX_."_nxmarket_items WHERE id_item = ".$id_item." LIMIT 1");
	return count($qq) == 1 ? $qq[0]: false;
}

protected function metaExists($id_item, $tag){
	$qq = Db::result("SELECT id_item, tag FROM "._SQLPREFIX_."_nxmarket_meta WHERE id_item = ".$id_item." AND tag LIKE '".$tag."' LIMIT 1");
	return count($qq) == 1 ? true: false;
}

protected function saveImageResource($id_item, $url){
	
	$url = str_replace('content/catalog/th_', 'content/catalog/', $url);
	$itemDir = $this->config['mediaDir'].'/'.$id_item;
	
	Storage::makeDir($itemDir);
	
	$newFileContent = @file_get_contents($url);
	
	//echo '<p>'.$url.'</p>';
	
	if($newFileContent !== false)
	{
		// clear dir
		$md = opendir(Registry::get('serverdata/root').'/'.$itemDir);
		
		while(false!==($item = readdir($md))){
			if($item != "." && $item != ".." && !is_dir(Registry::get('serverdata/root').'/'.$itemDir.'/'.$item)){
				@Storage::deleteFile($itemDir.'/'.$item);
				@Storage::deleteFile($itemDir.'/th_'.$item);
			}
		}
		closedir($md);
		
		
		// new files && thumbs
		$newFile = basename($url);
		
		$srcFile = Registry::get('serverdata/root').'/'.$itemDir.'/'.$newFile;
		$thFile = Registry::get('serverdata/root').'/'.$itemDir.'/th_'.$newFile;
		file_put_contents($srcFile, $newFileContent);
		
		$ext = System::extension($newFile);
		
		if ( $ext == 'jpg' || $ext == 'png' || $ext == 'gif' )
		{
			// THUMB 
			$th = new SimpleImage();
			$th->load($srcFile);
			
			if($this->config['image_thumb_preffer_axxis'] === true)
			{
				$th->resizeToWidth($this->config['image_size']['thWidth']);
			}else{
				$th->resizeToHeight($this->config['image_size']['thHeight']);
			}
			
			$th->save($thFile);
			umask(0000);
			chmod($thFile, 0777);
		
			// RESIZE
			$image = new SimpleImage();
			$image->load($srcFile);
			
			if($this->config['image_thumb_preffer_axxis'] === true)
			{
				$image->resizeToWidth($this->config['image_size']['origWidth']);
			}else{
				$image->resizeToHeight($this->config['image_size']['origHeight']);
			}
			
			$image->save($srcFile);
			umask(0000);
			chmod($srcFile, 0777);
			
			return $itemDir.'/'.$newFile;
		}
	}
	
	return null;
}

protected function display_xml_error($error, $xml)
{
	$return  = $xml[$error->line - 1] . "<br />";
	$return .= str_repeat('-', $error->column) . "^<br />";
	switch ($error->level) {
		case LIBXML_ERR_WARNING:
			$return .= "Warning ".$error->code.": ";
			break;
		 case LIBXML_ERR_ERROR:
			$return .= "Error ".$error->code.": ";
			break;
		case LIBXML_ERR_FATAL:
			$return .= "Fatal Error ".$error->code.": ";
			break;
	}

	$return .= trim($error->message) .
			"<br />  Line: ".$error->line .
			"<br />  Column: ".$error->column;

	if ($error->file){
		$return .= "<br />  File: ".$error->file;
	}

	return $return."<br /><br />--------------------------------------------<br /><br />";
}


}
?>
