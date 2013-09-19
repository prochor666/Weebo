<?php
$id_banner = isset($_GET['id_banner']) ? $_GET['id_banner']: 0;

$adv = new WeeboAdv;

$data = $adv->getBannerMediaById($id_banner);

if(count($data)>1)
{
	$data['extension'] = System::extension($data['file']);
}

echo json_encode($data);
?>
