<?php
$aq = new Users;

if(isset($_GET['sub'])){
	$activeSubmodule = trim($_GET['sub']);
}else{
	$activeSubmodule = $aq->initial_sub;
}

echo '<div id="users_load_dashboard">'.$aq->showStaticDashboard().'</div>';
?>
<script type="text/javascript">
/* <![CDATA[ */
var UsersDateTimePrecise = '<?php echo Lng::get('system/date_time_format_precise'); ?>';
usersLng.buttonSave = '<?php echo Lng::get('users/mwms_group_save'); ?>';

$(document).ready(function(){

<?php if(Login::is_site_root()){ ?>
	users.initUI();
<?php }else{ ?>
	users.initUIUser();
<?php } ?>
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

