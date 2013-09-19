<?php
$action = isset($_GET['action']) ? $_GET['action']: 'undefined';
$id_link = isset($_GET['id_link']) ? $_GET['id_link']: 0;
$id_link = $id_link == 0 && isset($_POST['id_link']) && (int)$_POST['id_link']>0 ? $_POST['id_link']: $id_link;
$id_link = !is_array($id_link) ? array($id_link): $id_link;

$ass = new LinksBrowser;

	if($action == 'del'){

		$id_link = array_unique(array_map("Filter::makeInt", $id_link));
		
		/*
		echo "<pre>";
			var_dump(array_filter($id_link, "Filter::isId"));
		echo "</pre>";
		*/
		
		echo '<div id="links-action-init">'.$ass->linksDelete($id_link).'</div>';
		
		foreach($id_link as $id){
			
			if( array_key_exists($id, $_SESSION['cms_filter_registry']['links_dropbox']) ){
				unset( $_SESSION['cms_filter_registry']['links_dropbox'][$id] );
			}
		}
		
	echo '
		<script type="text/javascript">
		/* <![CDATA[ */
		$(document).ready(function(){
			setTimeout(\'$("#weebo-modal-dialog-content").dialog("close");location.reload(true);\', 2000);
		});

		/* ]]> */
		</script>
		';
	}
?>
