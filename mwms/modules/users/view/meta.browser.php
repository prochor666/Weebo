<?php
if(Login::is_site_root()){

$ass = new MetaBrowserTemplate;

echo $ass->showMeta(); //.'<div id="mwms_load_content_inner">'.$ass->show_assets().'</div>';
?>
<script type="text/javascript">
/* <![CDATA[ */
$(document).ready(function(){
	
	/* Pager */
	var targetContainer = $('div#mwms_load_content_inner');
	
	$("button.mwms_meta_new").button({
		icons: {
			primary: "ui-icon-newwin"
		},
		text: true
		
	}).click(function()
	{
			var metaID = 0;
			
			var contentUri = "require&file=/mwms/modules/users/view/meta.detail.php&id_meta=" + metaID;
			var saveUri = "require&file=/mwms/modules/users/view/meta.type.save.php";
			
			var frm = 'form_call_' + metaID;
			var xtitle = $(this).attr('title');
			var w = 845;
			var h = 500;
				
			weeboMeta.showDialog(metaID, frm, contentUri, saveUri, xtitle, w, h);
	});
		
	
	
	/* New dialog opening */
	$(".meta_cast").each( function(){

			var metaID = parseInt( $(this).find("td.id_meta").text() );
			
			var contentUri = "require&file=/mwms/modules/users/view/meta.detail.php&id_meta=" + metaID;
			var saveUri = "require&file=/mwms/modules/users/view/meta.type.save.php";
			
			$(this).click(
				function(){
					var frm = 'form_call_' + metaID;
					var xtitle = $(this).attr('title');
					var w = 845;
					var h = 500;
					
					weeboMeta.showDialog(metaID, frm, contentUri, saveUri, xtitle, w, h);
				}
			).css({ 'cursor': 'pointer'  });
	});

});
/* ]]> */
</script>
<?php } ?>
