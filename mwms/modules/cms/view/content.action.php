<?php
$action = isset($_GET['action']) ? $_GET['action']: 'undefined';
$id_content = isset($_GET['id_content']) ? $_GET['id_content']: 0;
$id_content = $id_content == 0 && isset($_POST['id_content']) && (int)$_POST['id_content']>0 ? $_POST['id_content']: $id_content;
$id_content = !is_array($id_content) ? array($id_content): $id_content;

$ass = new ContentBrowser;

//System::dump($_POST);

	if($action == 'del'){

		$id_content = array_unique(array_map("Filter::makeInt", $id_content));
/*
		echo "<pre>";
			var_dump(array_filter($id_content, "Filter::isId"));
		echo "</pre>";
*/
		echo '<div id="links-action-init">'.$ass->contentDelete($id_content).'</div>';
		
		foreach($id_content as $id){
			
			if( array_key_exists($id, $_SESSION['cms_filter_registry']['content_dropbox']) ){
				unset( $_SESSION['cms_filter_registry']['content_dropbox'][$id] );
			}
			
		}

	echo '
		<script type="text/javascript">
		/* <![CDATA[ */
		$(document).ready(function(){
			$("#tabs").tabs("option", "active", 1);
			$("#tabs").tabs("load", 1);
			setTimeout(\'$("#weebo-modal-dialog-content").dialog("close");\', 600);
		});
		/* ]]> */
		</script>
		';
	}
?>
