<?php
$___myParamData = explode(':', $d['display_script_param']);

$mmr = new MediaMixChannelTemplates;

$_detail = $mmr->isDetail();

if($_detail === false)
{
	echo $mmr->loadChannelBy('id_source', $___myParamData[1]);
}else{
	echo $mmr->loadChannelArticle($_detail, 'id_source', $___myParamData[1]);
}
?>
