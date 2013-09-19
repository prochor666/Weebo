<?php
$tplkey = isset($_POST['tplkey']) ? $_POST['tplkey']: null;
$tpluri = isset($_POST['tpluri']) ? $_POST['tpluri']: null;
$tplsuffix = isset($_POST['tplsuffix']) ? $_POST['tplsuffix']: null;
$tplinput = isset($_POST['tplinput']) ? $_POST['tplinput']: null;
$tplreferer = isset($_POST['tplreferer']) ? $_POST['tplreferer']: null;
$tplname = isset($_POST['tplname']) ? $_POST['tplname']: null;

if(!is_null($tplkey) && !is_null($tplname) && !is_null($tplinput) && !is_null($tpluri))
{
	$uriSet = explode("\n", $tpluri);
	
	//echo $tpluri;
	
	foreach($uriSet as $uri){
		if(mb_strlen($uri)>5 && filter_var($uri, FILTER_VALIDATE_URL) !== false)
		{
			$t = new $tplkey;
			$t->template = $t->tpl[$tplkey];
			$t->template['key'] = $tplkey;
			$t->link = $uri;
			$t->suffix = $tplsuffix;
			$t->referer = $tplreferer;
			$t->input = $tplinput;
			
			echo $t->run();
		}
	}
}
?>
<script type="text/javascript">
/* <![CDATA[ */
$(document).ready(function(){

});
/* ]]> */
</script>
