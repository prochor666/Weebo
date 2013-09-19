<?php
// HTTP headers for no cache etc
/*
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
*/
// Settings
//$targetDir = ini_get("upload_tmp_dir") . DIRECTORY_SEPARATOR . "plupload";

$md = new Media;

if(isset($_GET['dir']))
{
	$targetDir = $md->splitDatadir($_GET['dir']);
	$targetDir = System::fsDir('/'.$targetDir).'/';
	$uniqueStr = isset($_GET['uif']) ? '-'.trim($_GET['uif']): null;
	
	//$targetDir = System::root().'/'._GLOBALCACHEDIR_.'/';

	// (int) minutes execution time
	@set_time_limit(0);

	// Get parameters
	$chunk = isset($_POST["chunk"]) ? $_POST["chunk"] : 0;
	$chunks = isset($_POST["chunks"]) ? $_POST["chunks"] : 0;
	$fileName = isset($_POST["name"]) ? $_POST["name"] : '';

	// Clean the fileName for security reasons
	//$fileName = preg_replace('/[^\w\._]+/', '', $fileName);
	$fileName = System::sanitizeFileName(System::autoUTF($fileName));
	
	$ext = System::extension($fileName);
	$fName = System::fileNameOnly($fileName);
	
	$fileName = $fName.$uniqueStr.'.'.$ext;
	
	// Create target dir
	if (!file_exists($targetDir)){
		Storage::makeDir($targetDir);
		//@mkdir($targetDir);
	}
	
	// Look for the content type header
	if (isset($_SERVER["HTTP_CONTENT_TYPE"])){
		$contentType = $_SERVER["HTTP_CONTENT_TYPE"];
	}
	
	if (isset($_SERVER["CONTENT_TYPE"])){
		$contentType = $_SERVER["CONTENT_TYPE"];
	}
	
	// Handle non multipart uploads older WebKit versions didn't support multipart in HTML5
	if (strpos($contentType, "multipart") !== false) 
	{
		if (isset($_FILES['file']['tmp_name']) && is_uploaded_file($_FILES['file']['tmp_name'])) {
			// Open temp file
			$out = fopen($targetDir .'/'. $fileName, $chunk == 0 ? "wb" : "ab");
			if ($out) {
				// Read binary input stream and append it to temp file
				$in = fopen($_FILES['file']['tmp_name'], "rb");

				if ($in) {
					while ($buff = fread($in, 4096))
						fwrite($out, $buff);
				} else
					die('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "Failed to open input stream."}, "id" : "id"}');
				fclose($in);
				fclose($out);
				@unlink($_FILES['file']['tmp_name']);
			} else
				die('{"jsonrpc" : "2.0", "error" : {"code": 102, "message": "Failed to open output stream."}, "id" : "id"}');
		} else
			die('{"jsonrpc" : "2.0", "error" : {"code": 103, "message": "Failed to move uploaded file."}, "id" : "id"}');
	} else {
		
		// Open temp file
		$out = fopen($targetDir .'/'. $fileName, $chunk == 0 ? "wb" : "ab");
		if ($out) {
			// Read binary input stream and append it to temp file
			$in = fopen("php://input", "rb");
			if ($in) {
				while ($buff = fread($in, 4096))
					fwrite($out, $buff);
			} else {
				die('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "Failed to open input stream."}, "id" : "id"}');
				fclose($in);
				fclose($out);
			}
		} else
			die('{"jsonrpc" : "2.0", "error" : {"code": 102, "message": "Failed to open output stream."}, "id" : "id"}');
	}

	/* Move &&|| rename */
	/*
	if(file_exists($realDir .'/'.$fileName) && is_file($realDir .'/'.$fileName)){
		
		$ext = System::extension($fileName);
		$n = System::fileNameOnly($fileName);
	
		$fileName = $n.'-copy.'.$ext;
	}
	
	Storage::moveFile($targetDir .'/'. $fileName, $realDir .'/'. $fileName);
	*/
	// Return JSON-RPC response
	die('{"jsonrpc" : "2.0", "result" : null, "id" : "id"}');
}
?>
