<?php
$mm = new MediaMix;

if(isset($_GET['sub'])){
	$activeSubmodule = trim($_GET['sub']);
}else{
	$activeSubmodule = $mm->initial_sub;
}

echo '<div id="cms_load_dashboard">'.$mm->showStaticDashboard().'</div>';
?>
<script type="text/javascript">
/* <![CDATA[ */
$(document).ready(function(){
	
	MediaMix.initUI();

	$("a[href$='<?php echo $activeSubmodule; ?>']").addClass('ui-state-active').mouseover(
		function(){
			$(this).addClass('ui-state-active'); 
			return false; 
	}).mouseout( 
		function(){
			$(this).addClass('ui-state-active'); 
			return false; 
	});

});  
/* ]]> */
</script>
<?php 
require_once($activeSubmodule.'.php');
?>

