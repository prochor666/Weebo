<?php
$___ch = explode('/', $_SERVER['REQUEST_URI']);

if(count($___ch)>2)
{
	$___str = urldecode($___ch[2]);
	$___origin = urldecode($___ch[2]);

	echo Render::siteSearchByKeywords($___str, $___origin);
}else{
	echo Render::showTagCloud(false);
}
?>
