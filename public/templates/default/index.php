<?php
if(Registry::get('userdata/logged_in') === 1 || Registry::get('userdata/logged_in') === 0){

setlocale(LC_ALL, 'cs'); 
header("Expires: " . gmdate("D, d M Y H:i:s", (time() - 1800) ) . " GMT");
header("Cache-Control: private"); 
header("Cache-Control: no-store, no-cache, must-revalidate"); 
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

$NMD = new Mobile_Detect;
$deviceType = ($NMD->isMobile() ? ($NMD->isTablet() ? 'tablet' : 'phone') : 'computer');

$myPage = new CmsOutput;

$pageContent = Render::moduleContent();
$pageSearch = Render::siteSearch(false);
$pageMainMenu = Render::links(0,0);
$pageFootMenu = Render::linksAlternate(2);
$pageTagCloud = Render::showTagCloud(false);

$___contentData = $myPage->getContentByView('cms/view/website/view.search.php');

if(count($___contentData)==1){
	$pageData = $myPage->getLinkData($___contentData[0]['id_link']);
	$___searchMap = $pageData['textmap'];
}else{
	$___searchMap = '404.html';
}

$_HOME_ = Render::getHomeMap();
$agent = array_key_exists('HTTP_USER_AGENT', $_SERVER) ? $_SERVER['HTTP_USER_AGENT']: 'UNKNOWN';
$ip =  array_key_exists('REMOTE_ADDR', $_SERVER) ? $_SERVER['REMOTE_ADDR']: 'UNKNOWN';
$domain = array_key_exists('REMOTE_HOST', $_SERVER) ? $_SERVER['REMOTE_HOST']: 'UNKNOWN';
$referer = array_key_exists('HTTP_REFERER', $_SERVER) ? $_SERVER['HTTP_REFERER']: 'DIRECT';
$ident = 'WEEBO ANALYTICS';
define('_HP_', false);
$xml = "
<analytics>
	<device>
	<![CDATA[
		".$agent."
	]]>
	</device>
	<referer>".$referer."</referer>
	<ip>".$ip."</ip>
	<domain>".$domain."</domain>
	<source_domain>"._CMS_DOMAIN_."</source_domain>
	<url>".Registry::get('serverdata/site').'/'.__CMS_FULL_PATH__."</url>
	<time>".date('r', time())."</time>
</analytics>
";

System::log($ident, $xml);
$fbImage = Registry::get('serverdata/site').'/public/templates/default/img/logo.png';
?>
<!DOCTYPE html>
<!--[if lt IE 7]>      <html lang="<?php echo Render::getLng('mwms_cms_site_lang'); ?>" class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html lang="<?php echo Render::getLng('mwms_cms_site_lang'); ?>" class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html lang="<?php echo Render::getLng('mwms_cms_site_lang'); ?>" class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html lang="<?php echo Render::getLng('mwms_cms_site_lang'); ?>" class="no-js"> <!--<![endif]-->
<head>
	<meta charset="utf-8" />
	<meta http-equiv="content-Type" content="text/html; charset=utf-8" />

	<title><?php echo __CMS_MAIN_TITLE__.' | '.Render::getLng('mwms_cms_site_title'); ?></title>
	
	<?php
	if (isset($_SERVER['HTTP_USER_AGENT']) && (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== false))
	{
		//echo '<!-- <meta http-equiv="X-UA-Compatible" content="IE=Edge,chrome=IE7" /> -->';
		echo '
		<meta http-equiv="X-UA-Compatible" content="IE=IE7" />
		';
	}
	?>
	
	<meta name="description" content="<?php echo __CMS_PAGE_DESCRIPTION__; ?>" />
	<meta name="keywords" content="<?php echo __CMS_PAGE_KEYWORDS__; ?>" />
	<meta name="author" content="<? echo Render::getLng('mwms_cms_site_author'); ?>" />
	
	<!-- ROBOTS -->
	<meta name="robots" content="index,follow" />
	<meta name='googlebot' content='index,follow,snippet,archive' /> 
	<meta name="rating" content="general" />
	
	<!-- Mobile viewport optimized: j.mp/bplateviewport -->
	<meta name="viewport" content="width=device-width,initial-scale=1" />

	<!-- DNS Prefetching -->
	<link rel="dns-prefetch" href="//<? $dnsPref = explode('//', Registry::get('serverdata/site')); echo $dnsPref[1]; ?>" />
	
	<!-- B-SOCIAL -->
	<meta property="og:title" content="<?php echo __CMS_MAIN_TITLE__.' | '.Render::getLng('mwms_cms_site_title'); ?>" />
	<meta property="og:description" content="<?php echo __CMS_PAGE_DESCRIPTION__; ?>" />
	<meta property="og:image" content="<?php echo $fbImage; ?>" />
	<link rel="image_src" href="<?php echo $fbImage; ?>" />
	<!-- /B-SOCIAL -->
	
	<!-- JS/CSS .nyud.net for CDN -->
	
	<script> var ie = false; </script>
	<!--[if IE 6 ]> <script> var ie = 6; </script> <![endif]-->
	<!--[if IE 7 ]> <script> var ie = 7; </script> <![endif]-->
	<!--[if IE 8 ]> <script> var ie = 8; </script> <![endif]-->
	<!--[if IE 9 ]> <script> var ie = 9; </script> <![endif]-->
	<!--[if IE 10 ]> <script> var ie = 10; </script> <![endif]-->
	<script>
	/* <![CDATA[ */
	var weeboPublic = {
		siteUrl : '<?php echo Registry::get('serverdata/site'); ?>',
		activePage : '<?php echo __CMS_MAP__; ?>',
		activeDocument : '<?php echo __CMS_DOCUMENT__; ?>',
		pageLng : '<?php echo __CMS_PAGE_LNG__; ?>',
		deviceType : '<?php echo $deviceType; ?>'
	}
	
	var weeboAdvMain = {}; // nutne pro reklamni system
	/* ]]> */
	</script>

<?php
	$cb = new ScriptBundle;
	$____path = null; 
	$cb->scripts = array(
		$____path."/public/templates/default/css/bootstrap.min.css" => true,
		//$____path."/public/templates/default/css/bootstrap-responsive.css" => true, // netreba, resi main.css lepe
		$____path."/public/templates/default/css/font/stylesheet.css" => true,
		$____path."/shared/jquery.fancybox/jquery.fancybox.css" => true,
		$____path."/shared/jquery.fancybox/helpers/jquery.fancybox-buttons.css" => true,
		$____path."/public/templates/default/css/main.css" => true,
	);
	
	$cb->finalCSSScript = 'weebo.www.wing.css.bundle.css';
	$cb->keepalive = 86400;  // rebulid time
	$cb->apply = false; // true to production
	echo $cb->bundleCss();
?>
	<!-- HTML5 shiv, for IE6-8 support of HTML5 elements -->
	<!--[if lt IE 9]>
		<script src="<?php echo Registry::get('serverdata/site'); ?>/shared/html5shiv.js"></script>
		<script src="<?php echo Registry::get('serverdata/site'); ?>/shared/respond.min.js"></script>
	<![endif]-->
<?php
	$jb = new ScriptBundle;
	$jb->scripts = array(
		$____path."/shared/jquery.min.js" => false,
		$____path."/public/templates/default/js/bootstrap.min.js" => false,
		$____path."/shared/smp.osmf/jquery.strobemediaplayback.js" => false,
		$____path."/shared/jquery.fancybox/jquery.fancybox.js" => true,
		$____path."/shared/jquery.fancybox/helpers/jquery.fancybox-buttons.js" => true,
		$____path."/shared/swfobject/swfobject.js" => true,
		$____path."/shared/jquery.weebo.jform/jquery.weebo.jform.js" => true,
		$____path."/public/templates/default/js/media.js" => true,
		$____path."/public/templates/default/js/template.js" => true,
	);
	
	$jb->finalJsScript = 'weebo.www.wing.script.bundle.js';
	
	$jb->keepalive = 86400; // rebulid time
	$jb->apply = false; // true to production
	echo $jb->bundleJs();
	
	// DROPS + TEMPLATE PAGE CLASSES
	$____dropData = Render::createSitePath(__CMS_PAGE_ID__);
	$____docTitle = mb_strlen(__CMS_DOCUMENT__)>0 ? ' <span class="drop-sep">&gt;</span> <span class="dct">'.__CMS_DOCUMENT_TITLE__.'</span>': null;
	$____dropMap = Render::createSitePathHTML($____dropData);
	
	$mapToClass = mb_strlen(__CMS_DOCUMENT__) > 0 ? 'detail-page-'.__CMS_PAGE_ID__.' page-'.__CMS_PAGE_ID__: 'page-'.__CMS_PAGE_ID__;
?>
	
	<!-- end JS/CSS-->
	
	<!-- FAVICON -->
	<link rel="icon" href="<?php echo Registry::get('serverdata/site'); ?>/public/templates/default/img/favicon.ico" type="image/x-icon" />
	<link rel="shortcut icon" href="<?php echo Registry::get('serverdata/site'); ?>/public/templates/default/img/favicon.ico" type="image/x-icon" />
	<!-- /FAVICON -->
</head>
<body>
	<div role="navigation" class="navbar navbar-inverse navbar-fixed-top">
	  <div class="navbar-inner">
		<div class="container-fluid">
			<button type="button" class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
			<a class="brand" href="<?php echo Registry::get('serverdata/site').'/'.$_HOME_.'/'; ?>" title="<?php echo __CMS_PAGE_TITLE__.' | '.Render::getLng('mwms_cms_site_title'); ?>"><?php echo Render::getLng('mwms_cms_site_title'); ?></a>
			
			<div class="nav-collapse collapse"  role="search">
				<form class="weebo_site_search_form navbar-form pull-right" method="get" action="<?php echo Registry::get('serverdata/site').'/'.$___searchMap.'/'; ?>">
					<?php echo $pageSearch; ?>
				</form>

				<?php echo $pageMainMenu; ?>
			</div><!--/.nav-collapse -->
		</div>
	  </div>
	</div>
	
	<div id="container-fluid">
		
		<div class="row-fluid" id="main" role="main">
			
			<div class="span12">
				
				<?php 
					echo Render::bindRss(); 
					
					if(count($____dropData)>1 || !is_null($____docTitle))
					{
						echo '<p id="drops">'.$____dropMap.$____docTitle.'</p>';
					}
				?>
				
				<div id="adv1" role="banner"></div>
				
				<?php echo $pageContent; ?>
			</div>
		</div>
		
		<div class="row-fluid" id="promo" role="complementary">
			<div class="span12">
				<div class="header">Tagy</div>
				<?php echo $pageTagCloud; ?>
				
				<?php
				/*
				$sidebarData = $myPage->getLinkData(6);
				 
				echo Render::contentWidget(
					$pageID = 6, 
					$config = array( 
						'briefLevel' => 0,
						'title' => true,
						'titleLink' => false,
						'archiveLink' => false,
						'limit' => 10,
						'rss' => false,
						'keywords' => false
					)
				); 
				*/ 
				?>
				
			</div>
		</div>
		
		<div class="clear"></div>
		
		<?php
			$____time = microtime();
			$____time = explode(" ", $____time);
			define('__MWMS_LOAD_END__', $____time[1] + $____time[0]);
		?>

		<div class="row-fluid" id="footer" role="contentinfo">
			<p>
				<?php echo '(&copy;) prochor666, 2013'; ?>
			</p>
			<nav id="foot-nav">
				<div id="devbar"></div>
				<?php echo $pageFootMenu; ?>
			</nav>
		</div>
		
	</div> 
	
	<!--[if lt IE 8 ]>
		<script src="//ajax.googleapis.com/ajax/libs/chrome-frame/1/CFInstall.min.js"></script>
		<style>
		.chromeFrameOverlayContent { border: 5px solid #000; color: #fff; }
		.chromeFrameOverlayContent iframe { border: 1px solid #fff; }
		.chromeFrameOverlayCloseBar { background-color: #000; color: #fff; padding: 4px 2px;  }
		.chromeFrameOverlayCloseBar button { background: #06c; color: #fff; border: 1px solid #06c; font-weight: bold; }
		.chromeFrameOverlayUnderlay { position: fixed; top: 0; left: 0; background-color: #000; }
		</style>
		<script>
			window.attachEvent('onload',function(){
				CFInstall.check({mode:'overlay'});
			});
		</script>
	<![endif]-->
	
	<?php
		$a = new AdvEmbed;
		$a->format = 'script';
		$a->positionsGet = '1';
		$a->action = 2;
		echo $a->release();
	?>
	
	<img src="<?php echo Registry::get('serverdata/site'); ?>/public/templates/default/img/nebula.jpg" id="fixed-background" alt="fbg" />
</body>
</html>

<?php }else{ echo 'DEV'; }?>
