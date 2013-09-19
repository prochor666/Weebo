<?php
function do_post_request($url, $data)
{
	$opts = array(
		'http' => array(
		'method'=> "POST",
		'header'=>	"Accept-language: en\r\n" .
					"Content-type: application/x-www-form-urlencoded\r\n" ,
		'content'=> http_build_query(array('source_content' => $data))
		)
	);
	
	//echo "Start \n";
	
	$context = stream_context_create($opts);
	$fp = fopen($url, 'rb', false, $context);
	$meta = stream_get_meta_data($fp);
	$res = stream_get_contents($fp);
	
	//echo $res;
	//echo "Done \n";
	fclose($fp);
	
	return $res;
}

$hostname = function_exists('gethostname') ? gethostname(): php_uname('n');

//$json = '{"dataSource":"/mnt/ftp/ftproot","dataTarget":"/var/www/"}';
$json = null;
$apiurl = 'http://'.$hostname.'/apistream/?weeboapi=alias&fn=MediaMixEmbed::rssDbSaveAll';

echo do_post_request($apiurl, $json);
?>
