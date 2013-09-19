<?php
class MediaMixChannelTemplates extends MediaMixChannel{

public function __construct(){
	parent::__construct();
}

/*
 * 
 * 
 * RSS & Atom
 * 
 * 
 * */
public function rssGet($d, $xml){
	
	$i = 0;
	
	if(!is_null($xml) && $xml !== false && is_object($xml))
	{ 
	
		if(is_object($xml->channel->item) && count($xml->channel->item)>0)
		{
			foreach($xml->channel->item as $item)
			{
				$title = null;
				$date = time();
				$link = null;
				$description = null;
				$token = null;
				
				if(
					$this->valid($item->title) === true
					// && ( $this->valid($item->pubDate) === true || $this->valid($item->date) === true )
					 && $this->valid($item->link) === true
				)
				{
					if($this->valid($item->pubDate) === true || $this->valid($item->date) === true )
					{
						$date_public = $this->valid($item->pubDate) === true ? strtotime($item->pubDate): strtotime($item->date);
					}else{
						$date_public = $date;
					}
					
					$title = (string)$item->title;
					$link =htmlspecialchars($item->link);
					
					if($this->valid($item->description) === true){
						$description = $item->description;
					}
					
					$token = System::hash($title.' '.$link.' '.$date_public);
					
					if($this->articleExists($token) === false)
					{
						$uq = "INSERT INTO "._SQLPREFIX_."_mm_articles 
								(title, date_public, data, link, token, id_public, id_source, date_ins)
								VALUES 
								('".Db::escapeField($title)."', 
								'".(int)$date_public."', 
								'".Db::escapeField($description)."', 
								'".Db::escapeField($link)."', 
								'".Db::escapeField($token)."',
								1,
								'".(int)$d['id_source']."',
								'".time()."' 
								) 
								";
						Db::query($uq);
						$i++;
					}
				}
			}
			
			Db::query("UPDATE "._SQLPREFIX_."_mm_sources SET last_update = '".time()."' WHERE id_source = '".(int)$d['id_source']."' ");
		}

	}
	
	return $i;
	
}

public function rssDisplay($d, $qq){
	
	$html = null;
	
	$id_source = is_array($d) && array_key_exists('id_source', $d) ? $d['id_source']: 0;
		
	$html .= '
	<div class="textbox mediamix-set-wrapper rss-'.$id_source.'">
	';
	
	$html .= '
		<div class="textbox mediamix-set rss-set-'.$id_source.'">
	';
	
	foreach($qq as $item)
	{
		$sourceTitle = $item['sourceTitle'];
		$id_type = $item['id_type'];
		
		if(
			mb_strlen($item['title']) > 0
			 && $item['date_public'] > 0
			 && mb_strlen($item['link']) > 1 
		)
		{
			$title = $item['title'];
			$date = date(Lng::get('mediamix/date_time'), $item['date_public']);
			$html .= '
						<div class="rss-item rss-item-source-'.$item['id_source'].'">
							<h2 class="rss-header">
								<a href="'.$item['link'].'" target="_blank">'.$title.'</a> 
							</h2>
							<span class="rss-source">'.$sourceTitle.'</span>
							<span class="rss-date">'.$date.'</span>
				';
			if(mb_strlen($item['data']) > 0){
				$dhtml = $id_type == 1 ? $item['data']: strip_tags($item['data']);
				$html .= '<div class="rss-description">'.$dhtml.'</div>';
			}
			$html .= '
						</div>
				';
		}
	}
	
	$html .= '
		</div>
	</div>
	';
	
	return $html;
}

public function rssExport($d){
	
}

/*
 * 
 * Autobazary
 * 
 * 
 * */
public function autobazarReadXML($d, $xml){
	
	$i = 0;
	
	if(!is_null($xml) && $xml !== false && is_object($xml))
	{ 
	
		if(is_object($xml->items->item) && count($xml->items->item)>0)
		{
			foreach($xml->items->item as $item)
			{
				$id_public = 1;
				$title = null;
				$date = time();
				$link = null;
				$description = null;
				$token = null;
				
				if(
					$this->valid($item->id) === true
					 && $this->valid($item->price) === true
					 && $this->valid($item->status) === true
				)
				{
					$date_public = time();
					
					$title = (string)$item->id;
					$link = (string)$item->id;
					
					$description = null;
					
					$token = System::hash($title);
					
					if($this->articleExists($token) === false)
					{
						$uq = "INSERT INTO "._SQLPREFIX_."_mm_articles 
								(title, date_public, data, link, token, id_public, id_source, date_ins)
								VALUES 
								('".Db::escapeField($title)."', 
								'".(int)$date_public."', 
								'".Db::escapeField($description)."', 
								'".Db::escapeField($link)."', 
								'".Db::escapeField($token)."',
								0,
								'".(int)$d['id_source']."',
								'".time()."' 
								) 
								";
						Db::query($uq);
						$i++;
						
						$lastID = (int)Db::get_last_id(_SQLPREFIX_."_mm_articles");
						
						/* Read params */
						if( $lastID > 0 )
						{
							@Db::query("INSERT INTO "._SQLPREFIX_."_mm_meta 
								(id_article, tag, value)
								VALUES 
								('".(int)$lastID."', 
								'Cena', 
								'".Db::escapeField((string)$item->price)."'
								) 
							");

							@Db::query("INSERT INTO "._SQLPREFIX_."_mm_meta 
								(id_article, tag, value)
								VALUES 
								('".(int)$lastID."', 
								'Status', 
								'".Db::escapeField((string)$item->status)."'
								) 
							");
							
							
							if($this->valid($item->param) === true)
							{
								
								foreach($item->param as $param)
								{
									$_att = (array)$param->attributes();
									$att = $_att['@attributes'];
									
									if(array_key_exists('name', $att))
									{
										if($tag == 'status' && (int)$value != 1){
											$id_public = 0;
										}
										
										$tag = (string)$att['name'];
										$value = (string)$param;
										
										$ms = "INSERT INTO "._SQLPREFIX_."_mm_meta 
												(id_article, tag, value)
												VALUES 
												('".(int)$lastID."', 
												'".Db::escapeField($tag)."', 
												'".Db::escapeField($value)."'
												) 
												";
										@Db::query($ms);
									}
									
								}
								
							}
							
							if($this->valid($item->images) === true && $this->valid($item->images->image) === true)
							{
								$ijs = array();
								
								foreach($item->images->image as $image)
								{
									$d['article_dir'] = $lastID;
									$imageLocal = $this->saveResourceLink($d, (string)$image);
									$ijs[] = $imageLocal;
								}
								
								$tag = 'imageSet';
								
								if(count($ijs)<5){
									Db::query("UPDATE "._SQLPREFIX_."_mm_articles SET id_public = 0 WHERE id_article = ".(int)$lastID);
								}
								
								$js = json_encode($ijs);
								
								$is = "INSERT INTO "._SQLPREFIX_."_mm_meta 
										(id_article, tag, value)
										VALUES 
										('".(int)$lastID."', 
										'".Db::escapeField($tag)."', 
										'".$js."'
										) 
										";
								@Db::query($is);
							}
						}
						
					}else{
						if($id_public != 1){
							Db::query("UPDATE "._SQLPREFIX_."_mm_articles SET id_public = 0 WHERE id_article = ".(int)$id_article);
						}
					}
				}
			}
			
			Db::query("UPDATE "._SQLPREFIX_."_mm_sources SET last_update = '".time()."' WHERE id_source = '".(int)$d['id_source']."' ");
		}

	}
	
	return $i;
}

public function autobazarDisplay($d, $qq){
	
	$html = null;
	
	$id_source = is_array($d) && array_key_exists('id_source', $d) ? $d['id_source']: 0;
	
	$html .= '
	<div class="textbox mediamix-set-wrapper rss-'.$id_source.'">
	';
	
	$html .= '
		<div class="textbox mediamix-set rss-set-'.$id_source.'">
	';
	
	foreach($qq as $item)
	{
		$sourceTitle = $item['sourceTitle'];
		$id_type = $item['id_type'];
		
		if(
			mb_strlen($item['title']) > 0
			 && $item['date_public'] > 0
			 && mb_strlen($item['link']) > 1 
		)
		{
			// META SET
			$mq = Db::result("SELECT * FROM "._SQLPREFIX_."_mm_meta WHERE id_article = ".(int)$item['id_article']);
			
			$image = null; 
			$meta = null;
			$newTitle = null;
			$price = null;
			
			$model = null;
			$vendor = null;
			$motor = null;
			$year = null;
			
			if(count($mq)){
				
				foreach($mq as $m){
					
					if($m['tag'] == 'imageSet'){
						
						$imageTemp = json_decode($m['value']);
						$image  = '<img src="'.$this->setFullPath(current($imageTemp)).'" alt="'.$item['title'].'" title="'.$item['title'].'" />';
					
					}else{
						
						if(mb_strtolower($m['tag']) == 'značka vozu'){
							$vendor = trim($m['value']);
						}
						
						if(mb_strtolower($m['tag']) == 'model vozu'){
							$model = trim($m['value']);
						}
						
						if(mb_strtolower($m['tag']) == 'motor'){
							$motor = trim($m['value']);
						}
						
						if(mb_strtolower($m['tag']) == 'rok výroby' && (int)$m['value'] > 1900){
							$year = trim($m['value']);
						}
						
						
						if(mb_strtolower($m['tag']) == 'cena'){
							$price = number_format(str_replace(' ', '', trim($m['value'])), 2, ',', ' ').' CZK';
						}
						
					}
					
				}
				
			}
			
			$newTitle .= mb_strlen($vendor)>0 ? $vendor: null;
			$newTitle .= mb_strlen($model)>0 ? ' '.$model: null;
			$newTitle .= mb_strlen($motor)>0 ? ', '.$motor: null;
			$newTitle .= mb_strlen($year)>0 ? ', '.$year: null;
			
			$title = mb_strlen($newTitle)>0 ? $item['title'].', '.$newTitle: $item['title'];
			$date = date(Lng::get('mediamix/date_time'), $item['date_public']);
			
			$priceDisplay = mb_strlen($price)>0 ? '<span class="mediamix-price">'.$price.'</span>': null;
			
			//<span class="mediamix-source">'.$sourceTitle.'</span>
			
			$html .= '
						<div class="mediamix-item mediamix-item-source-'.$item['id_source'].'">
							<h2 class="mediamix-header">
								<a href="'.Registry::get('serverdsata/site').'/'.__CMS_MAP__.'/'.Filter::makeUrlString($title).'-'.$item['id_article'].'" class="mediamix-link">'.$image.' '.$title.'</a> 
								<span class="mediamix-date">'.$date.'</span>
								'.$priceDisplay.'
							</h2>
				';
			if(mb_strlen($item['data']) > 0){
				$dhtml = $id_type == 1 ? $item['data']: strip_tags($item['data']);
				$html .= '<div class="mediamix-description">'.$dhtml.'</div>';
			}
			
			$html .= '
						</div>
				';
		}
	}
	
	$html .= '
		</div>
	</div>
	';
	
	return $html;
}

public function getAutoArticleData($d, $qq){
	
	$html = null;
	
	$id_source = is_array($d) && array_key_exists('id_source', $d) ? $d['id_source']: 0;
	
	$html .= '
	<div class="textbox mediamix-set-wrapper rss-'.$id_source.'">
	';
	
	$html .= '
		<div class="textbox mediamix-set rss-set-'.$id_source.'">
	';
	
	foreach($qq as $item)
	{
		$sourceTitle = $item['sourceTitle'];
		$id_type = $item['id_type'];
		
		if(
			mb_strlen($item['title']) > 0
			 && $item['date_public'] > 0
			 && mb_strlen($item['link']) > 1 
		)
		{
			// META SET
			$mq = Db::result("SELECT * FROM "._SQLPREFIX_."_mm_meta WHERE id_article = ".(int)$item['id_article']);
			
			$images = null; 
			$meta = null;
			$newTitle = null;
			$price = null;
			
			$model = null;
			$vendor = null;
			$motor = null;
			$year = null;
			
			if(count($mq)){
				
				$meta .= '<table class="mediamix-meta">';
				
				foreach($mq as $m){
					
					if($m['tag'] == 'imageSet'){
						
						$images = '<div class="mediamix-images">';
						
						$imageTemp = json_decode($m['value']);
						array_walk($imageTemp, array($this, 'setFullPath', ));
						
						foreach($imageTemp as $img){
							$images .= '<a href="'.$img.'" target="_blank" class="mediamix-thumb" rel="mm-item-'.$item['id_article'].'" title="'.$item['title'].'"><img src="'.$img.'" alt="'.$item['title'].'" title="'.$item['title'].'" /></a>';
						}
						
						$images .= '</div>';
						
					}else{
						
						if(mb_strtolower($m['tag']) == 'značka vozu'){
							$vendor = trim($m['value']);
						}
						
						if(mb_strtolower($m['tag']) == 'model vozu'){
							$model = trim($m['value']);
						}
						
						if(mb_strtolower($m['tag']) == 'motor'){
							$motor = trim($m['value']);
						}
						
						if(mb_strtolower($m['tag']) == 'rok výroby' && (int)$m['value'] > 1900){
							$year = trim($m['value']);
						}
						
						
						if(mb_strtolower($m['tag']) == 'cena'){
							$price = number_format(str_replace(' ', '', trim($m['value'])), 2, ',', ' ');
						}
						// Convert nl2br to ul/li html
						$_val = count( $_arr = explode("\n", trim($m['value'])) ) > 1 && array_walk($_arr, 'trim') === true ? '<ul><li>'.implode('</li><li>', $_arr).'</li></ul>': trim($m['value']); 
						
						$meta .= '<tr><th>'.$m['tag'].'</th><td>'.$_val.'</td></tr>';
					}
					
				}
				
				$meta .= '</table>';
			}
			
			$newTitle .= mb_strlen($vendor)>0 ? $vendor: null;
			$newTitle .= mb_strlen($model)>0 ? ' '.$model: null;
			$newTitle .= mb_strlen($motor)>0 ? ', '.$motor: null;
			$newTitle .= mb_strlen($year)>0 ? ', '.$year: null;
			
			$title = mb_strlen($newTitle)>0 ? $newTitle: $item['title'];
			$date = date(Lng::get('mediamix/date_time'), $item['date_public']);
			
			$priceDisplay = mb_strlen($price)>0 ? '<span class="mediamix-price">'.$price.'</span>': null;
			
			//<span class="mediamix-source">'.$sourceTitle.'</span>
			
			$html .= '
						<div class="mediamix-item mediamix-item-source-'.$item['id_source'].'">
							<h2 class="mediamix-header">'.$title.'
							'.$priceDisplay.'
							</h2>
							
							<span class="mediamix-date">'.$date.'</span>
				';
			if(mb_strlen($item['data']) > 0){
				$dhtml = $id_type == 1 ? $item['data']: strip_tags($item['data']);
				$html .= '<div class="mediamix-description">'.$dhtml.'</div>';
			}
			
			$html .= '<div class="mediamix-detail">'.$meta.$images.'<div class="clear clearfix"></div></div>
						</div>
				';
		}
	}
	
	$html .= '
		</div>
	</div>
	';
	
	return $html;
}


public function autobazarExport($qq){
	
	$imageSet = array();
	
	$items = array('Status' => 'Ok', 'Message' => 'Listing source', 'items' => array());
	
	foreach($qq as $d){
		
		$meta = Db::result("SELECT * FROM "._SQLPREFIX_."_mm_meta WHERE id_article = ".$d['id_article']);
		
		if(count($meta)>0)
		{
			$item = array( 'id_item' => $d['id_article'], 'meta' => array(
				'id' => array('id', $d['title'])
			) );
			
			foreach($meta as $m){
				
				$tag = $m['tag'];
				
				switch($tag){
					case 'imageSet':
						
						$imageTemp = json_decode($m['value']);
						
						array_walk($imageTemp, array($this, 'setFullPath', ));
						
						$item[$tag] = $imageTemp;
						
					break; default:
						
						$rows = explode("\n", $m['value']);
						$new = trim($m['value']);
						
						if(count($rows)>1)
						{
							$_n = array();
							foreach($rows as $row){
								$_n[] = trim($row);
							}
							$new = implode("\n", $_n);
						}
						
						if(mb_strtolower($tag) == 'cena'){
							$new = number_format(str_replace(' ', '', trim($m['value'])), 0, ',', ' ').' Kč včetně DPH';
							//$new = 'Nasrat a rozmazat';
						}
						
						if(mb_strtolower($tag) == 'leasing'){
							$new = (int)$m['value'] == 1 ? 'Ukončený operativní leasing': '';
						}
						
						/* tag fixes */
						if(mb_strtolower($tag) == 'stav'){
							$tag = 'Stav vozu';
						}
						
						$tagKey = Filter::makeUrlString($tag);
						
						$item['meta'][$tagKey] = array($tag, $new);
				}
			}
			
			array_push($items['items'], $item);
		}
	}
	
	return $items;
}



}












