<?php
$___str = isset($_GET['___wss']) && mb_strlen(trim(urldecode(strip_tags($_GET['___wss']))))>0 ? trim(urldecode(strip_tags($_GET['___wss']))): null;
$___origin = isset($_GET['___wss']) && mb_strlen(trim($_GET['___wss']))>0 ? trim($_GET['___wss']): null;

echo Render::siteSearchResult($___str, $___origin);
?>
