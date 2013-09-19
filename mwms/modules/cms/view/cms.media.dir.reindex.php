<?php
$id_dir = isset($_GET['id_dir']) ? (int)$_GET['id_dir']: 0;
$dir = isset($_GET['dir']) ? $_GET['dir']: 0;

if($id_dir > 0){
	
	$f = new MediaBrowser;
	echo $f->reindexDir($id_dir);
}
?>
<script type="text/javascript">
/* <![CDATA[ */
$(document).ready(function(){
	
});

/* ]]> */
</script>
