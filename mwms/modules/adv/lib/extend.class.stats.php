<?php
class AdvStats extends WeeboAdv{
	
protected $vmode;

public function __construct(){
	parent::__construct();
}

public function getCampaignSet($id_asset){
	
	$o = array();
	
	if($id_asset>0){
		
		$q = "SELECT * FROM "._SQLPREFIX_."_adv_assets WHERE id_asset = ".(int)$id_asset." ORDER BY id_asset";
		//$qq = Db::result($q);
		$qq = Db::memAuto($q, $this->config['stat_memcache_timeout']);
		
		foreach($qq as $d){
			
			$a = $this->getAssetGrid($d['id_asset'], $d['date_from'], $d['date_to']);
			
			$xi = array();

			foreach($a['x'] as $day){
			  array_push($xi, '"'.date($this->lng['date'], strtotime($day)).'"');
			}
			
			/*
			$x = implode(",", $xi);
			$iy = implode(",", $a['iy']);
			$cy = implode(",", $a['cy']);
			*/
		}
	}
	
	$o['dayimpressions'] = null;
	$o['dayclicks'] = null;
	$o['dayCTR'] = null;
	$csvRow = '"'.$this->lng['csv_header_date'].'";"'.$this->lng['csv_header_impress'].'";"'.$this->lng['csv_header_click'].'";"'.$this->lng['csv_header_ctr'].'"'."\r\n";
	
	$dayImpressionsAll = 0;
	$dayClicksAll = 0;
	
	foreach($xi as $i => $v){
		// CSV + CTR count
		$dayImpressions = (int)$a['iy'][$i];
		$dayClicks = (int)$a['cy'][$i];
		
		$rowCTR = $dayClicks>0 && $dayImpressions>0 ? round(($dayClicks / $dayImpressions)*100, 1): 0;
		
		$csvRow .= $v.';'.$dayImpressions.';'.$dayClicks.';'.str_replace('.', ',', (string)$rowCTR).''."\r\n";
		
		// impress plot
		$o['dayimpressions'] .= $i == 0 ? '['.$v.', '.$dayImpressions.']': ',['.$v.', '.$dayImpressions.']';
		// click plot
		$o['dayclicks'] .= $i == 0 ? '['.$v.', '.$dayClicks.']': ',['.$v.', '.$dayClicks.']';
		// ctr plot
		$o['dayCTR'] .= $i == 0 ? '['.$v.', '.$rowCTR.']': ',['.$v.', '.$rowCTR.']';
		
		$dayImpressionsAll += $dayImpressions;
		$dayClicksAll += $dayClicks;
	}
	
	$rowCTRAll = $dayClicksAll>0 && $dayImpressionsAll>0 ? round(($dayClicksAll / $dayImpressionsAll)*100, 1): 0;
	$csvRow .= '" ";" ";" ";" "'."\r\n";
	$csvRow .= '" ";" ";" ";"'.$this->lng['adv_stat_ctr_summary'].'"'."\r\n";
	$csvRow .= '"'.$this->lng['adv_stat_summary'].'";'.$dayImpressionsAll.';'.$dayClicksAll.';'.str_replace('.', ',', (string)$rowCTRAll).''."\r\n";
	
	$o['title'] = $this->lng['adv_stat_main_label'].': '.$d['title'];
	$o['label_click'] = $this->lng['adv_stat_click_label'];
	$o['label_impress'] = $this->lng['adv_stat_impress_label'];
	$o['label_date'] = $this->lng['adv_stat_date_label'];
	$o['label_ctr'] = $this->lng['csv_header_ctr'];
	$o['export_filename'] = $this->config['export_dir'].'/'.'export.'.$id_asset.'.csv';
	
	$o['dayimpressions'] = '['.$o['dayimpressions'].']';
	$o['dayclicks'] = '['.$o['dayclicks'].']';
	$o['dayCTR'] = '['.$o['dayCTR'].']';
	
	file_put_contents(Registry::get('serverdata/root').'/'.$o['export_filename'], iconv("UTF-8", $this->lng['csv_download_encoding'], $csvRow));
	
	return $o;
}

protected function getAssetGrid($id_asset, $from, $to){
	
	$iy = array();  // impressions
	$cy = array();  // clikcs
	$x = array();   // dates
	$axxis_data = array(); // main container

	$from = strtotime(date("Y-m-d", $from));
	$to = strtotime(date("Y-m-d", $to));

	$days = ($to - $from) / 86400;

	for($day = 0; $day <= $days; $day ++){
		
		$day_str = date("Y-m-d", $from + ($day*86400) );
		
		if(!in_array($day_str, $x)){
			array_push($x, $day_str);
		}
	}

	$item_last_store_time = 0;

	foreach($x as $item){
		
		$item_store_time = strtotime($item);
		
		$qi = "SELECT * FROM "._SQLPREFIX_."_adv_asset_stats WHERE id_asset = ".$id_asset." AND action_time >= ".$item_store_time." AND action_time < ".($item_store_time + 86400)." AND action_type = 2 ";
		$qc = "SELECT * FROM "._SQLPREFIX_."_adv_asset_stats WHERE id_asset = ".$id_asset." AND action_time >= ".$item_store_time." AND action_time < ".($item_store_time + 86400)." AND action_type = 1 ";
		//$di = Db::result($qi);
		//$dc = Db::result($qc);
		$di = Db::memAuto($qi, $this->config['stat_memcache_timeout']);
		$dc = Db::memAuto($qc, $this->config['stat_memcache_timeout']);

		array_push($iy, count($di));
		array_push($cy, count($dc));
	}

	$axxis_data['x'] = $x;
	$axxis_data['iy'] = $iy;
	$axxis_data['cy'] = $cy;

return $axxis_data;	
}


}
?>
