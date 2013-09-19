<?php
$b = new UserBrowser;

echo '<div id="user-action-result"><form action="" method="post" id="user-action">';

echo $b->actionSelect();

echo $b->groupSelect();

echo $b->userSelect();

echo '</form></div>';

echo '
	<script type="text/javascript">
	/* <![CDATA[ */
	$(document).ready(function(){
		$("#action-set").buttonset();
		
		$("#group-set label, #user-set label").addClass("ui-widget ui-widget-content ui-helper-clearfix ui-corner-all");
		
		$("#action-set input").on("change", function(){
			
			var xid = $(this).prop("checked");
			var id = $(this).attr("id");
			if(xid){
				
				switch(id){
					case "action-assign":
						$("#group-set").show("fast");
					break; case "action-unassign":
						$("#group-set").show("fast");
					break; case "action-del":
						$("#group-set").hide("fast");
					break; default:
						alert("Error - no action defined");
				}
				
			}
			
		});
		
		
		
	});

	/* ]]> */
	</script>
	';
?>
