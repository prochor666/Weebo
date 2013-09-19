<?php
$g = new WeeboNettvRender;
if(_HP_ == false){
	echo $g->nowPlaying();
}
echo $g->renderShowList();
?>
<script type="text/javascript">
/* <![CDATA[ */
$(document).ready(function(){
	$('.textbox').remove();
	$('.nettv-show-detail').remove();
	$('.weebo_gallery_wrapper').remove();
});  
/* ]]> */
</script>
