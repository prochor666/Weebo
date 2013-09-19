<?php
if(Login::is_site_root()){

$id_meta = isset($_GET['id_meta']) && (int)$_GET['id_meta']>0 ? (int)$_GET['id_meta']: 0;

$ass = new Meta($id_meta);

$ass->load();

echo $ass->show();

echo '
<script type="text/javascript">
/* <![CDATA[ */
$(document).ready(function(){
	
	/* Predefined */
	$("#edit_form_default_value").parent("td").parent("tr").hide();
	
	var predefined = $("input[name$=\"predefined\"]:checked").length;
	
	if(predefined == 1){
		$("#edit_form_default_value").parent("td").parent("tr").show();
		$("select[name$=\"system_type\"]").val("blob");
		weeboMeta.addMetaWidget("'.$ass->lng['mwms_meta_add_predefined_value'].'");
	}
	
	$("input[name$=\"predefined\"]").change(function()
	{
		if($(this).prop("checked")){

			$("select[name$=\"system_type\"]").val("blob");
			$("#edit_form_default_value").parent("td").parent("tr").show();
			$("#edit_field_default_value").hide();
			weeboMeta.addMetaWidget("'.$ass->lng['mwms_meta_add_predefined_value'].'");

		}else{
			
			$("#edit_field_default_value").show();
			weeboMeta.removeMetaWidget();
			$("#edit_form_default_value").parent("td").parent("tr").hide();

		}
	});

});
/* ]]> */
</script>
';

}
?>
