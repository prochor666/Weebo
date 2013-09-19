<?php
$mm = new WeeboInqua;

if(isset($_GET['sub'])){
	$activeSubmodule = trim($_GET['sub']);
}else{
	$activeSubmodule = $mm->initial_sub;
}

echo '<div id="cms_load_dashboard">'.$mm->showStaticDashboard().'</div>';
?>
<script type="text/javascript">
/* <![CDATA[ */
inquaLng.resetButton = '<?php echo Lng::get('inqua/reset_button'); ?>';

$(document).ready(function(){

	InquaAdmin.initUI();

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

