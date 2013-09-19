<?php
class WeeboNettvRender extends WeeboNettv{
	
public $id_show, $id_team, $moduleOk, $archiveOrder, $archiveShowOrder;

protected $archiveLink, $archiveTitle, $showLink, $showTitle, $videoLink, $videoTitle;

public function __construct(){
	parent::__construct();
	$this->id_show = 0;
	$this->id_team = 0;

	$this->archiveLink = 'UNDEFINED';
	$this->archiveTitle = 'UNDEFINED';
	
	$this->showLink = 'UNDEFINED';
	$this->showTitle = 'UNDEFINED';
	
	$this->videoLink = 'UNDEFINED';
	$this->videoTitle = 'UNDEFINED';
	
	$this->moduleOk = false;
	
	$this->initLinks();
	
	$this->pageDefault = $this->config['cmsPageDefault'];
	$this->actualPage =  isset($_GET['page']) ? (int)$_GET['page']: 1;
	$this->archiveOrder = isset($_GET['ao']) ? (int)$_GET['ao']: -1;
	$this->archiveShowOrder = isset($_GET['aso']) ? (int)$_GET['aso']: -1;
}

public function initLinks(){
	// archive show catalog
	$archiveData = Db::memGet('getContentByView.nettv.archive.list.php');
	if($archiveData === false){
		$archiveData = Db::memSet('getContentByView.nettv.archive.list.php', $this->getContentByView('nettv/view/website/nettv.archive.list.php'), 15);
	}
	
	if(count($archiveData)>0)
	{
		$this->archiveID = $archiveData['id_link'];
		$this->archiveLink = $archiveData['linkmap'];
		$this->archiveTitle = $archiveData['title'];
		
		// show videos
		$showData = Db::memGet('getContentByView.nettv.archive.show.php');
		if($showData === false){
			$showData = Db::memSet('getContentByView.nettv.archive.show.php', $this->getContentByView('nettv/view/website/nettv.archive.show.php'), 15);
		}
		
		if(count($showData)>0)
		{
			$this->showID = $showData['id_link'];
			$this->showLink = $showData['linkmap'];
			$this->showTitle = $showData['title'];
			
			// video detail
			$videoData = Db::memGet('getContentByView.nettv.archive.detail.php');
			if($videoData === false){
				$videoData = Db::memSet('getContentByView.nettv.archive.detail.php', $this->getContentByView('nettv/view/website/nettv.archive.detail.php'), 15);
			}
			
			if(count($videoData)>0)
			{
				$this->videoID = $videoData['id_link'];
				$this->videoLink = $videoData['linkmap'];
				$this->videoTitle = $videoData['title'];
				
				$this->moduleOk = true;
			}
		
		}
	
	}
}

public function dropsData(){
	
	$data = array();
	$doc = null;
	
	switch(__CMS_MAP__){
		case $this->showLink:
			
			$url = $_SERVER['REQUEST_URI'];
			
			$urlset = explode('/', $url);
			$index = count($urlset) - 2;
			
			$xid = explode('-', $urlset[$index]);
			$index2 = count($xid) - 1;
			
			$id_show = (int)$xid[$index2];
			
			//$showData = $this->getShowData($id_show);
			$showData = Db::memGet('getShowData'.$id_show);
			if($showData === false){
				$showData = Db::memSet('getShowData'.$id_show, $this->getShowData($id_show), 15);
			}
			
			$data[$this->archiveLink] = $this->archiveTitle;
			
			if(is_array($showData) && count($showData)>0)
			{
				$doc = $showData['title'];
			}
		break; case $this->videoLink:
			
			$url = $_SERVER['REQUEST_URI'];
			
			$urlset = explode('/', $url);
			$index = count($urlset) - 2;
			
			$xid = explode('-', $urlset[$index]);
			$index2 = count($xid) - 1;
			
			$id_item = (int)$xid[$index2];
			
			//$d = $this->getShowItemData($id_item);
			$d = Db::memGet('getShowItemData'.$id_item);
			if($d === false){
				$d = Db::memSet('getShowItemData'.$id_item, $this->getShowItemData($id_item), 15);
			}
			
			// link na archiv poradu
			//$showData = $this->getShowData($d['id_show']);
			$showData = Db::memGet('getShowData'.$d['id_show']);
			if($showData === false){
				$showData = Db::memSet('getShowData'.$d['id_show'], $this->getShowData($d['id_show']), 15);
			}
			
			$showmap = Filter::makeUrlString($showData['title'].' - '.$showData['id_show']);
			
			$data[$this->showLink.'/'.$showmap.'/?aso='.$this->archiveShowOrder.'&amp;page='.$this->actualPage] = $showData['title'];
			
			$data[$this->archiveLink] = $this->archiveTitle;
			
			$doc = $d['title'];
	}
	
	return array($data, $doc);
}

/* In show order, filter */
public function filterShowSet($site){
	
	$orderSet = null;
	
	foreach($this->lng['web_order'] as $k => $o)
	{
		$a = abs($this->archiveShowOrder) == $k ? 'filter-on': 'filter-off';
		$orderDirection = abs($this->archiveShowOrder) != $k || $this->archiveShowOrder>0 ? '-'.$k: $k;
		$orderDirectionClass = abs($this->archiveShowOrder) == $k && $this->archiveShowOrder>0 ? 'order-asc': 'order-desc';
		$orderSet .= '<a href="'.$site.'?aso='.$orderDirection.'" class="nettv-show-order-filter '.$a.'">'.$o['title'].'<span class="'.$orderDirectionClass.'"></span></a>';
	}
	
	$html = '
		<div class="nettv-show-order-filter-wrapper">
			'.$orderSet.'
		</div>
	';
	
	return $html;
}

/* ARCHIVES, porady v archivu */
public function renderArchiveList(){
	$html = null;
	
	$s = Db::memGet('getShowsArchive');
	if($s === false){
		$s = Db::memSet('getShowsArchive', $this->getShowsArchive(), 15);
	}
	
	if(count($s)>0){
		
		$html .= '
		<div class="nettv-shows-wrapper">
		';
		foreach($s as $d){
			
			$showImageSrcFile = basename($d['image']);
			$showImageSrcDir = dirname($d['image']);
			$fname = System::fileNameOnly($showImageSrcFile);
			$ext = System::extension($showImageSrcFile);
			
			$showImage = file_exists(Registry::get('serverdata/root').'/'.$d['image']) ? '<span class="nettv-show-image"><img src="'.$this->site.'/'.$showImageSrcDir.'/'.$fname.'.'.$ext.'" alt="" /></span>': '<span class="nettv-show-no-image"></span>';
			
			$title = $d['title'];
			
			$href = $this->site.'/'.$this->showLink.'/'.Filter::makeUrlString($d['title'].'-'.$d['id_show']).'/';
			$title = '
					<a class="nettv-show-link" href="'.$href.'" title="'.$d['title'].'">
						'.$showImage.'
						<span class="nettv-show-text">'.$d['title'].'</span>
					</a>';
			$html .= '
				<div class="nettv-show-item">
					<h2 class="nettv-show-title">
						'.$title.'
					</h2>
				</div>
			';
		}
		$html .= '
		</div>
		';
	}
	
	return $html;
}

/* ARCHIVES, epizody v poradu */
public function renderArchiveItems(){
	$html = null;
	
	$id_show = 0;
	
	$url = $_SERVER['REQUEST_URI'];
	$urlset = explode('/', $url);
	$index = count($urlset) - 2;
	
	$xid = explode('-', $urlset[$index]);
	$index2 = count($xid) - 1;
	
	$id_show = (int)$xid[$index2];
	
	if($id_show>0)
	{
		$qq = Db::memGet('getShowItems'.$id_show.$this->archiveShowOrder);
		if($qq === false){
			$qq = Db::memSet('getShowItems'.$id_show.$this->archiveShowOrder, $this->getShowItems($id_show, $this->archiveShowOrder), 1);
		}
	}
	
	if(count($qq)>0 && $id_show>0){
		
		$siteFull = $this->site.'/'.$this->showLink.'/'.$urlset[$index].'/';
		
		$result_count = count($qq); 
		$query_limit = $this->pageDefault * ( $this->actualPage - 1 );
		$list = $this->pageDefault>0 && $result_count > $this->pageDefault ? Db::final_items($qq, $query_limit, $this->pageDefault): $qq;
		$pager = $this->pageDefault>0 && $result_count > $this->pageDefault ? Navigator::pager_ajax_rewrite($result_count, $this->pageDefault, $custom_uri = $siteFull.'?aso='.$this->archiveShowOrder.'&amp;', $this->actualPage, 'page', 3): null;
		
		$html .= $pager.$this->filterShowSet($siteFull);
		
		$html .= '
		<div class="nettv-archive-wrapper">
		';
		foreach($list as $d){
			$href = $this->site.'/'.$this->videoLink.'/'.Filter::makeUrlString($d['title'].'-'.$d['id_item']).'/?aso='.$this->archiveShowOrder.'&amp;page='.$this->actualPage;
			$itemData = json_decode($d['media']);
			
			$rootDirLength = mb_strlen(System::root());
			
			$imageSrcFile = isset($itemData->images[0]) && count($itemData->images)>0 && mb_strlen($itemData->images[0])>0 && file_exists($itemData->images[0]) ? mb_substr($itemData->images[0], $rootDirLength): null;
			$videoSrcFile = isset($itemData->video[0]) && count($itemData->video)>0 && mb_strlen($itemData->video[0])>0 && file_exists($itemData->video[0]) ? mb_substr($itemData->video[0], $rootDirLength): null;
			
			$showImageSrc = !is_null($imageSrcFile) ? '<a href="'.$href.'" title="'.$d['title'].'"><img src="'.$this->site.'/'.$imageSrcFile.'" alt="" /></a>': null;

			$format = explode('x', $d['format']);
			
			$videoDesc = $d['date_public']>0 ? '<span class="nettv-archive-desc-date">'.date($this->lng['date'], $d['date_public']).'</span>': null;
			$videoDesc .= $d['series']>0 ? ' <span class="nettv-archive-desc-series">'.$this->lng['web_series'].$d['series'].'</span>': null;
			$videoDesc .= $d['episode']>0 ? ' <span class="nettv-archive-desc-episode">'.$this->lng['web_episode'].$d['episode'].'</span>': null;
			$videoDesc .= $d['impress']>0 ? ' <span class="nettv-archive-desc-impress">'.$this->lng['web_impress'].$d['impress'].'</span>': null;
			
			$html .= '
				<div class="nettv-archive-item">
					<div class="nettv-archive-desc">'.$videoDesc.'</div>
					<h2><a href="'.$href.'" title="'.$d['title'].'">'.$d['title'].'</a></h2>
					<div class="nettv-archive-image">'.$showImageSrc.'</div>
				</div>
			';
		}
		
		$html .= '<div class="clear clearfix"></div>
		</div>
		'.$pager;
	}
	
	return $html;
}

/* Archiv, VIDEO DETAIL */
public function renderArchiveDetail(){
	$html = null;
	
	$id_item = 0;
	
	$url = $_SERVER['REQUEST_URI'];
	$urlset = explode('/', $url);
	$index = count($urlset) - 2;
	
	$xid = explode('-', $urlset[$index]);
	$index2 = count($xid) - 1;
	
	$id_item = (int)$xid[$index2];
	
	//$d = $this->getShowItemData($id_item);
	$d = Db::memGet('getShowItemData'.$id_item);
	if($d === false){
		$d = Db::memSet('getShowItemData'.$id_item, $this->getShowItemData($id_item), 15);
	}
	
	if(count($d)>0){
		
		$this->logImpression($id_item, $d['impress']+1);
		
		$html .= '
		<div class="nettv-archive-detail">
		<h2>'.$d['title'].'</h2>
		';
		$itemData = json_decode($d['media']);
		
		$rootDirLength = mb_strlen(System::root());
		
		$imageSrcFile = isset($itemData->images[0]) && count($itemData->images)>0 && mb_strlen($itemData->images[0])>0 && file_exists($itemData->images[0]) ? mb_substr($itemData->images[0], $rootDirLength): null;
		$videoSrcFile = isset($itemData->video[0]) && count($itemData->video)>0 && mb_strlen($itemData->video[0])>0 && file_exists($itemData->video[0]) ? mb_substr($itemData->video[0], $rootDirLength): null;
		
		$showImageSrc = !is_null($imageSrcFile) ? 'poster="'.$this->site.'/'.$imageSrcFile.'"': null;

		$format = explode('x', $d['format']);
		
		$videoDesc = date($this->lng['date'], $d['date_public']);
		
		$videoDesc = $d['date_public']>0 ? '<span class="nettv-archive-desc-date">'.date($this->lng['date'], $d['date_public']).'</span>': null;
		$videoDesc .= $d['series']>0 ? ' <span class="nettv-archive-desc-series">'.$this->lng['web_episode'].$d['series'].'</span>': null;
		$videoDesc .= $d['episode']>0 ? ' <span class="nettv-archive-desc-episode">'.$this->lng['web_series'].$d['episode'].'</span>': null;
		$videoDesc .= $d['impress']>0 ? ' <span class="nettv-archive-desc-impress">'.$this->lng['web_impress'].$d['impress'].'</span>': null;
		
		$html .= '
			<div class="nettv-archive-video"><video width="'.$format[0].'" height="'.$format[1].'" src="'.$videoSrcFile.'" preload="none" '.$showImageSrc.'></video></div>
			<p class="nettv-archive-short-description">'.$videoDesc.'</p>
			<div class="nettv-archive-long-description">'.$d['description'].'</div>
		';
		
		$html .= '
		</div>
		';
		
		if($d['id_show']>0){
			
			$html .= $this->renderShowDetail($d['id_show'], true, true);
			
		}
	}
	
	return $html;
}

public function logImpression($id_obj, $count){

	$eArticleReading = Registry::get('nettv_article_readings');
	$now = time();
	$interval = (int)Render::getCfg('impression_timeout');
	$update = false;
	
	if($eArticleReading === false)
	{
		Registry::set('nettv_article_readings', array());
		$eArticleReading = Registry::get('nettv_article_readings');
	}
	
	if(array_key_exists($id_obj, $eArticleReading))
	{
		$timeout = $now - (int)$eArticleReading[$id_obj];
		if($timeout > $interval){
			$update = true;
			$eArticleReading[$id_obj] = time();
		}
	}else{
		$eArticleReading[$id_obj] = time();
		$update = true;
	}

	Registry::set('nettv_article_readings', $eArticleReading);

	if($update === true)
	{
		$q = "UPDATE "._SQLPREFIX_."_nettv_show_items SET impress = '".(int)$count."' WHERE id_item = '".(int)$id_obj."' ";
		Db::query($q);
	}

}

/* Archiv, VIDEO DETAIL, posledni, HP */
public function renderArchiveLast($limit = 1){
	$html = null;

	//$idShow = $this->showID;
	
	$qq = Db::memGet('getLastShowItemData'.$limit);
	if($qq === false){
		$qq = Db::memSet('getLastShowItemData'.$limit, $this->getLastShowItemData($limit), 15);
	}
	
	if(count($qq)>0){
		
		foreach($qq as $d)
		{
		
			$href = $this->site.'/'.$this->videoLink.'/'.Filter::makeUrlString($d['title'].' - '.$d['id_item']).'/';
			$itemData = json_decode($d['media']);
			
			$rootDirLength = mb_strlen(System::root());
			
			$imageSrcFile = isset($itemData->images[0]) && count($itemData->images)>0 && mb_strlen($itemData->images[0])>0 && file_exists($itemData->images[0]) ? mb_substr($itemData->images[0], $rootDirLength): null;
			$videoSrcFile = isset($itemData->video[0]) && count($itemData->video)>0 && mb_strlen($itemData->video[0])>0 && file_exists($itemData->video[0]) ? mb_substr($itemData->video[0], $rootDirLength): null;
			
			$showImageSrc = !is_null($imageSrcFile) ? '<a href="'.$href.'" title="'.$d['title'].'"><img src="'.$this->site.'/'.$imageSrcFile.'" alt="" /></a>': null;

			$videoDesc = $d['date_public']>0 ? '<span class="nettv-archive-desc-preview-date">'.date($this->lng['date'], $d['date_public']).'</span>': null;
			$videoDesc .= $d['series']>0 ? ' <span class="nettv-archive-desc-preview-series">'.$this->lng['web_series'].$d['series'].'</span>': null;
			$videoDesc .= $d['episode']>0 ? ' <span class="nettv-archive-desc-preview-episode">'.$this->lng['web_episode'].$d['episode'].'</span>': null;
			$videoDesc .= $d['impress']>0 ? ' <span class="nettv-archive-desc-preview-impress">'.$this->lng['web_impress'].$d['impress'].'</span>': null;
			
			$html .= '
				<div class="nettv-archive-item-preview">
					<div class="nettv-archive-preview-desc">'.$videoDesc.'</div>
					<strong><a href="'.$href.'" title="'.$d['title'].'">'.$d['title'].'</a></strong>
					<div class="nettv-archive-preview-image">'.$showImageSrc.'</div>
				</div>
			';
		}
	}
	
	return $html;
}


/* detail poradu */
public function renderShowDetail($id_show, $short = false, $showTitle = false){
	$html = null;
	
	//$s = $this->getShowData($id_show);
	$d= Db::memGet('getShowData'.$id_show);
	if($d === false){
		$d = Db::memSet('getShowData'.$id_show, $this->getShowData($id_show), 15);
	}
	
	if(count($d)>0){
		
		$html .= '
		<div class="nettv-show-detail">
		';
		
		$showImageSrcFile = basename($d['image']);
		$showImageSrcDir = dirname($d['image']);
		$fname = System::fileNameOnly($showImageSrcFile);
		$ext = System::extension($showImageSrcFile);
		
		$showImage = file_exists(Registry::get('serverdata/root').'/'.$d['image']) ? '<span class="nettv-show-image-detail"><img src="'.$this->site.'/'.$showImageSrcDir.'/'.$fname.'.'.$ext.'" alt="" /></span>': '<span class="nettv-show-no-image-detail"></span>';
		
		$desc = $short === true ? $d['description_short']: $d['description'];
		
		$link = Db::memGet('getContentByView.nettv.show.detail.php'.$d['id_show']);
		if($link === false){
			$link = Db::memSet('getContentByView.nettv.show.detail.php'.$d['id_show'], $this->getContentByView('nettv/view/website/nettv.show.detail.php', 'id_show:'.$d['id_show']), 15);
		}
		
		$title = $showTitle === true ? '<h2 class="nettv-show-detail-title">'.$d['title'].'</h2>': null;
		
		if(count($link)>0 && $showTitle === true){
			$href = $this->site.'/'.$link['linkmap'].'/'.$link['cmap'].'.html';
			$title = '<h2 class="nettv-show-detail-title"><a href="'.$href.'" title="'.$d['title'].'">'.$d['title'].'</a></h2>';
		}
			
		$html .= '
			'.$title.'
			<div class="nettv-show-description">'.$showImage.' '.$desc.'</div>
		';
		
		$html .= '
		</div>
		';
		
		if($d['id_dir']>0 && $short === false){
			$html .= $this->showGallery($d['id_dir']);
		}
	}
	
	return $html;
}

/* moderatori */
public function renderTeamList(){
	$html = null;
	
	//$s = $this->getTeam();
	$s= Db::memGet('getTeam');
	if($s === false){
		$s = Db::memSet('getTeam', $this->getTeam(), 15);
	}
	
	if(count($s)>0){
		
		$html .= '
		<div class="nettv-team-wrapper">
		';
		foreach($s as $d){
			
			$showImageSrcFile = basename($d['image']);
			$showImageSrcDir = dirname($d['image']);
			$fname = System::fileNameOnly($showImageSrcFile);
			$ext = System::extension($showImageSrcFile);
			
			$showImageSrc = $this->site.'/'.$showImageSrcDir.'/'.$fname.'_thumb_h96.'.$ext;
			
			//$link = $this->getContentByView('nettv/view/website/nettv.team.detail.php', 'id_team:'.$d['id_team']);
			$link= Db::memGet('getContentByView.nettv.team.detail.php'.$d['id_team']);
			if($link === false){
				$link = Db::memSet('getContentByView.nettv.team.detail.php'.$d['id_team'], $this->getContentByView('nettv/view/website/nettv.team.detail.php', 'id_team:'.$d['id_team']), 15);
			}
			
			$title = $d['title'];
			
			if(count($link)>0){
				$href = $this->site.'/'.$link['linkmap'].'/'.$link['cmap'].'.html';
				$title = 
				$title = '<a class="nettv-team-link" href="'.$href.'" title="'.$d['title'].'">
							<span class="nettv-team-image">
								<img src="'.$showImageSrc.'" alt="" />
							</span>
							<span class="nettv-team-text">'.$d['title'].'</span>
							</a>';
			}else{
				$title = '
							<span class="nettv-team-image">
								<img src="'.$showImageSrc.'" alt="" />
							</span>
							<span class="nettv-team-text">'.$d['title'].'</span>
							';
			}
		
			$html .= $d['id_active'] == 1 ? '
				<div class="nettv-team-item">
					
					<h2 class="nettv-team-title">
						'.$title.'
					</h2>
					
				</div>
			': null;
			
		}
		$html .= '
		</div>
		';
	}
	
	return $html;
}

/* detail moderatora */
public function renderTeamDetail($id_team){
	$html = null;
	
	//$s = $this->getTeamData($id_team);
	$s= Db::memGet('getTeamData'.$id_team);
	if($s === false){
		$s = Db::memSet('getTeamData'.$id_team, $this->getTeamData($id_team), 15);
	}
	
	if(count($s)>0){
		
		$html .= '
		<div class="nettv-team-detail">
		';
		$d = $s;
			
		$showImageSrcFile = basename($d['image']);
		$showImageSrcDir = dirname($d['image']);
		$fname = System::fileNameOnly($showImageSrcFile);
		$ext = System::extension($showImageSrcFile);
			
		$showImageSrc = $this->site.'/'.$showImageSrcDir.'/'.$fname.'_half_w350.'.$ext;
		
		$html .= '
			<div class="nettv-team-image-detail">
				<img src="'.$showImageSrc.'" alt="program-thumb" />
			</div>
			<!--
			<h2 class="nettv-team-title">
				'.$d['title'].'
			</h2>
			-->
			<div class="nettv-team-description">'.$d['description'].'</div>
		';
		
		$html .= '
		</div>
		';
		
		if($d['id_dir']>0){
			$html .= $this->showGallery($d['id_dir']);
		}
		
	}
	
	return $html;
}


/* tv program */
public function renderGuideList()
{
	$today = strtotime(date('Y-m-d', time()).' 05:00');
	$html = '
	<div class="program-tabs">
	';
	$todayX = $today;
	for($i=1; $i<=7; $i++)
	{
		$sel = $i == 1 ? ' selected': null;
		$html .= '
		<div id="day-'.$i.'-link" class="program-day'.$sel.'"><a href="#day-'.$i.'" class="program-day-title">'.$this->lng['tv_days_locale'][date('l', $todayX)].', '.date($this->lng['date_short'], $todayX).'</a></div>
		';
		$todayX = $todayX + 86400;
	}
	
	$html .= '
	</div>
	';

	$todayX = $today;
	for($i=1; $i<=7; $i++)
	{
		//$dayData = $this->getGuide($todayX);
		$dayData = Db::memGet('getGuide'.$todayX);
		if($dayData === false){
			$dayData = Db::memSet('getGuide'.$todayX, $this->getGuide($todayX), 30);
		}
		
		$sel = $i == 1 ? ' wrap-selected': null;
		
		$html .= '
			<div class="program-wrap'.$sel.'" id="day-'.$i.'">
			';
		$side = 'left';
		
		foreach($dayData as $d){
			
			//$sd = $d['id_show']>0 ? $this->getShowData($d['id_show']): array();
			
			if($d['id_show']>0){
				$sd = Db::memGet('getShowData'.$d['id_show']);
				if($sd === false){
					$sd = Db::memSet('getShowData'.$d['id_show'], $this->getShowData($d['id_show']), 30);
				}
			}else{
				$sd = array();
			}
			
			$title = $d['title'];
			
			$showImageSrc = null;
			$showImage = null;
			
			if(count($sd)>0){
				$showImageSrcFile = basename($sd['image']);
				$showImageSrcDir = dirname($sd['image']);
				$fname = System::fileNameOnly($showImageSrcFile);
				$ext = System::extension($showImageSrcFile);
				
				$showImageSrc = $this->site.'/'.$showImageSrcDir.'/'.$fname.'_thumb_h96.'.$ext;
				$showImage = '<img src="'.$showImageSrc.'" alt="" />';
				
				//$link = $this->getContentByView('nettv/view/website/nettv.show.detail.php', 'id_show:'.$sd['id_show']);
				$link = Db::memGet('getContentByView.nettv.show.detail.php'.$sd['id_show']);
				if($link === false){
					$link = Db::memSet('getContentByView.nettv.show.detail.php'.$sd['id_show'], $this->getContentByView('nettv/view/website/nettv.show.detail.php', 'id_show:'.$sd['id_show']), 15);
				}
				
				if(count($link)>0){
					$href = $this->site.'/'.$link['linkmap'].'/'.$link['cmap'].'.html';
					$title = '<a class="nettv-guide-item-link" href="'.$href.'" title="'.$d['title'].'">
								'.$d['title'].'
							</a>';
					
					$showImage = '<a href="'.$href.'"><img src="'.$showImageSrc.'" alt="" /></a></span>';
				}
			} 
			
			$sideDisplay = $d['date_from']>time() ? $side: $side.' act-guide-tiem';
			$sideDisplay = $d['day']==1 ? $side.' item-star': $side;
			
			$desc = mb_strlen($d['description'])>0 ? '<div class="program-name">'.$d['description'].'</div>': null;
			
			$html .= '
				<div class="program-item '.$sideDisplay.'">
					<span class="num">'.date($this->lng['time'], $d['date_from']).'</span>
					<span class="program-thumb">'.$showImage.'</span>
					<span class="program-title">'.$title.'</span>
					'.$desc.'
				</div>';
			$side = $side == 'left' ? 'right': 'left';
		}
		
		$html .= '
			</div>
			';
		
		$todayX = $todayX + 86400;
	}

	return $html;
}


/* tv program API */
public function tvGuide($from=1, $to=7)
{
	$today = strtotime(date('Y-m-d', time()).' 05:00');
	$days = array();
	$todayX = $today;
	
	for($i=1; $i<=7; $i++)
	{
		
		if($i>=$from && $i<=$to)
		{
		
			$dayData = Db::memGet('getGuide'.$todayX);
			if($dayData === false){
				$dayData = Db::memSet('getGuide'.$todayX, $this->getGuide($todayX), 60);
			}
			
			$day = array(
				'date' => date('d.m.Y', $todayX),
				'name' => $this->lng['tv_days_locale'][date('l', $todayX)].', '.date($this->lng['date_short'], $todayX),
				'items' => array()
			);
			
			foreach($dayData as $d){
				
				$item = array(
				'start' => date('H:i', $d['date_from']),
				'start_timestamp' => $d['date_from'],
				'title' => htmlspecialchars($d['title']),
				'description' => htmlspecialchars($d['description'])
				);
				
				array_push($day['items'], $item);
			}
			
			array_push($days, $day);
			
		}
		$todayX = $todayX + 86400;
	}

	return $days;
}


public function showGallery($id_dir = 0){
$html = null;
$q = "SELECT * FROM "._SQLPREFIX_."_cms_media_list  
		WHERE id_dir = '".$id_dir."' 
		ORDER BY public_ord, id_media DESC
	";

//$qq = Db::result($q);
$qq = Db::memAuto($q, 20);

$result_count = count($qq); 
$__root = Registry::get('serverdata/site').'/content/';

if($result_count>0)
{
	
	$html .= '<!-- start of gallery block -->
	<div class="weebo_gallery_wrapper">
	';
	foreach($qq as $d){

		$fname = basename($d['path']);
		$dirname = dirname($d['path']);
		$html .= '<div class="weebo_gallery_item">
				<a href="'.$__root.$d['path'].'" class="weebo_gallery_item_link" rel="tag" target="_blank" title="'.$d['title'].'">
					<img src="'.$__root.$dirname.'/th/th_'.$fname.'" alt="'.$d['title'].'" />
					<!-- '.$d['title'].' -->
				</a>
			</div>';
	}
	$html .= '<div class="clear clearfix"></div>
	</div>
	<!-- start of gallery block -->';
}

return $html;
}


}
?>
