<div id="uploader-box-wrapper"></div>
<?php
$id_dir = isset($_GET['id_dir']) ? (int)$_GET['id_dir']: 0;

if($id_dir>0){
	$ass = new MediaBrowserTemplate;
	$ass->id_dir = $id_dir;
	echo $ass->showContent();
	
	$dirinfo = $ass->getDirData($id_dir);
?>
<script type="text/javascript">
/* <![CDATA[ */
var itemsCount = parseInt('<?php echo $ass->resultList; ?>');

$(document).ready(function(){
	
	$( 'img.media-thumb' ).tooltip({
		show: {
			effect: "slideDown",
			duration: 170
		},
		hide: {
			effect: "slideUp",
			duration: 80
		},
		position: { my: "left top", at: "right+15 top-15", collision: "flipfit" },
		content: function() {
			return '<img src="' + $(this).attr('src').replace('th/th_', '') + '" alt="~" style="width: 300px;" />';
		}
	});
	
	var idDir = parseInt('<?php echo $id_dir ?>');
	var extensionFilter = 'jpg,png,gif';
	var path = '<?php echo $dirinfo['path']; ?>';
	
	/* Pager */
	var targetContainer = $('div#mwms_content_show');
	
	$("div.weebo_pager_fixed a").each( function(){
			var pager_uri = $(this).attr("href");
			
			$(this).button().click( function(){
				targetContainer.html('');
				cms.showPreloader(targetContainer, 100);
				targetContainer.load( pager_uri );
				return false;
			});
	});

	/* Order */
	$("div.order_box a").each( function(){
			var order_uri = $(this).attr("href");
			
			$(this).click( function(){
				targetContainer.html('');
				cms.showPreloader(targetContainer, 100);
				targetContainer.load( order_uri + '&id_dir='+idDir );
				return false;
			});
	});

	$('button.mwms_media_new').button({
		icons: {
			primary: "ui-icon-newwin"
		}
	}).click( function(){
			
		var tabID = '0';
		var tabURI = weebo.settings.AjaxCall + "require&file=/mwms/modules/cms/view/media.dir.uploader.php&id_content=" + tabID + "&id_dir=" + idDir;
		cms.addTab( tabURI, tabID, $(this).attr('title'), true);
		
	});

	
	/* Filter button */
	$('button.media_search_send').button({
		icons: {
			primary: "ui-icon-search"
		}
	});
	
	$('button.media_search_reset').button({
		icons: {
			primary: "ui-icon-circle-close"
		}
	});
	
	/* Fulltext */
	$("#content_browser").each( function(){
			var search_uri = $(this).find('input#media_search_path').val();
			var search_button = $(this).find('button.media_search_send');
			var reset_button = $(this).find('button.media_search_reset');
			var search_field = $('input#media_search');
			
			reset_button.click(

				function(){
					$('input#media_search').val('');
					
					targetContainer.html('');
					cms.showPreloader(targetContainer, 100);
					targetContainer.load( search_uri + '&media_search_term=' );
					
					return false;
				}
			);	

			search_button.click(
				
				function(){
					var search_term = encodeURIComponent( search_field.val() );
				
					if(search_term.length >= <?php echo $ass->search_term_length_min; ?>){
						
						targetContainer.html('');
						cms.showPreloader(targetContainer, 100);
						targetContainer.load( search_uri + '&media_search_term=' + $.trim(search_term) );
						
						return false;
					}else{
						alert('<?php echo Lng::get('cms/search_term_short'); ?>');
					}
				
				}
			);

			search_field.bind('keypress', function(e) {
					if(e.keyCode==13){
							// si entruj
							var search_term = encodeURIComponent( search_field.val() );
						
							if(search_term.length >= <?php echo $ass->search_term_length_min; ?>){
								
								targetContainer.html('');
								cms.showPreloader(targetContainer, 100);
								targetContainer.load( search_uri + '&media_search_term=' + $.trim(search_term) );
						
							}else{
								alert('<?php echo Lng::get('cms/search_term_short'); ?>');
							}
						
					}
					
			});

	});

	/* New tab opening */
	$(".content_cast").each( function(){
			
			$(this).disableSelection();
			
			var tabIDstr = $(this).attr('id').split('_');
			var tabID = parseInt(tabIDstr[2]);
			var tabURI = "require&file=/mwms/modules/cms/view/media.file.detail.process.php&id_media=" + tabID + "&id_dir=" + idDir;
			var delURI = "require&file=/mwms/modules/cms/view/media.file.action.php&id_media=" + tabID + "&id_dir=" + idDir; 
			
			if(itemsCount<3){
				delURI = "require&file=/mwms/modules/cms/view/media.file.action.php&id_media=" + tabID + "&id_dir=" + idDir + '&reset_pager=1'; 
			}
			
			var tabTitle = $(this).attr('title');
			
			var tbutton = new Array();
					
			tbutton[0] = {
				title : '<?php echo Lng::get('cms/mwms_content_edit'); ?>',
				name : 'edit',
				icon : 'ui-icon-pencil',
				text : false,
				xcall: function(){
					cms.addTab( weebo.settings.AjaxCall + tabURI, tabID, tabTitle, true );
					$('div.mwms-data-widget').remove();
					return false;
				}
			};	
				
			tbutton[1] = {
				title : '<?php echo Lng::get('cms/mwms_content_del'); ?>',
				name : 'del',
				icon : 'ui-icon-closethick',
				text : false,
				xcall: function(){
					var question = '<?php echo Lng::get('cms/mwms_confirm_del'); ?>: ' + tabTitle +'?';
					var confirmDelete = confirm(question);
					
					if(confirmDelete === true)
					{
						cms.removeItem( delURI, '<?php echo Lng::get('cms/mwms_content_del'); ?>' );
						$('div.mwms-data-widget').remove();
						return false;
					}
				}
			};
			
			var conf = {
				title : tabTitle,
				id : tabID,
				element : '#'+$(this).attr('id')+' .toolbar',
				buttons : tbutton
			}
			 
			cms.toggleToolbar( conf );
			
			$(this).rightClick(
				function(){
					
					cms.addTab( weebo.settings.AjaxCall + tabURI, tabID, tabTitle, false );
				}
			).click(
				function(){
					//cms.addTab( weebo.settings.AjaxCall + tabURI, tabID, tabTitle, true );
				}
			).css({ 'cursor': 'pointer'  });
	});

});
/* ]]> */
</script>
<?php } ?>
