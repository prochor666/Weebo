<?php
if(Login::is_site_root()){

$ass = new GroupBrowserTemplate;

echo $ass->showGroups(); //.'<div id="mwms_load_content_inner">'.$ass->show_assets().'</div>';
?>
<script type="text/javascript">
/* <![CDATA[ */
$(document).ready(function(){
	
	/* Pager */
	var targetContainer = $('div#mwms_load_content_inner');
	
	$('button.mwms_user_group_new').button({
		icons: {
			primary: "ui-icon-newwin"
		}
	}).click( function(){
			
			var groupID = 0;
			
			var contentUri = "require&file=/mwms/modules/users/view/group.detail.php&id_group=" + groupID;
			var saveUri = "require&file=/mwms/modules/users/view/group.save.php";
			
			var frm = 'form_call_' + groupID;
			var xtitle = $(this).attr('title');
			var w = 845;
			var h = 500;
					
			weeboMeta.showDialog(groupID, frm, contentUri, saveUri, xtitle, w, h);
	});

	/* New dialog opening */
	$(".group_cast").each( function(){
			
			var groupID = parseInt( $(this).find("td.id_group").text() );
			
			var contentUri = "require&file=/mwms/modules/users/view/group.detail.php&id_group=" + groupID;
			var saveUri = "require&file=/mwms/modules/users/view/group.save.php";
			
			$(this).click(
				function(){
					var frm = 'form_call_' + groupID;
					var xtitle = $(this).attr('title');
					var w = 845;
					var h = 500;
					
					weeboMeta.showDialog(groupID, frm, contentUri, saveUri, xtitle, w, h);
				}
			).css({ 'cursor': 'pointer'  });
	});
	
});

/* ]]> */
</script>
<?php } ?>
