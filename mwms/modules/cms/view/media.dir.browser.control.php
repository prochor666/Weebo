<?php
$ass = new MediaBrowserTemplate;

echo $ass->showDirs();
?>
<script type="text/javascript">
/* <![CDATA[ */
var myActiveDir = <?php echo (int)Registry::get('cms_active_gallery'); ?>;

$(document).ready(function(){

	$('button.mwms_media_new_dir').button({
		icons: {
			primary: "ui-icon-newwin"
		}
	}).on('click', function(){
			var cURI = "require&file=/mwms/modules/cms/view/media.dir.detail.process.php&id_dir=0";
			cms.newDir(cURI, $(this).attr('title'));
	});
	
	
	
	/* New tab opening */
	$("li.nav_link").each( function(index)
	{		
		var lnk = $(this).find('a:first');
		
		$(this).disableSelection();
		
		var tabIDstr = lnk.attr('id').split('_');
		var tabID = parseInt(tabIDstr[2]);
		
		if(myActiveDir > 0)
		{
			var _icon_ = tabID == myActiveDir ? 'ui-icon-folder-open': 'ui-icon-document';
			lnk.html( '<span class="ui-icon '+ _icon_ +'"></span>' + lnk.text());
			
			if(tabID == myActiveDir){
				lnk.removeClass('nh').addClass('highlight');
			}else{
				lnk.removeClass('highlight').addClass('nh');
			}
			
		}else{
			var _icon_ = index == 0 ? 'ui-icon-folder-open': 'ui-icon-document';
			lnk.html( '<span class="ui-icon '+ _icon_ +'"></span>' + lnk.text());
			
			if(index == 0){
				lnk.removeClass('nh').addClass('highlight');
			}else{
				lnk.removeClass('highlight').addClass('nh');
			}
		}
		
		var tabURI = "require&file=/mwms/modules/cms/view/media.dir.detail.process.php&id_dir=" + tabID;
		var delURI = "require&file=/mwms/modules/cms/view/media.dir.action.php&action=del&id_dir=" + tabID;
		var tabTitle = lnk.attr('title');
		
		var tbutton = new Array();
		
		tbutton[0] = {
			title : '<?php echo Lng::get('cms/mwms_link_del'); ?>',
			name : 'del',
			icon : 'ui-icon-closethick',
			text : false,
			xcall: function(){
				var question = '<?php echo Lng::get('cms/mwms_confirm_del'); ?>: ' + tabTitle +'?';
				var confirmDelete = confirm(question);
				
				if(confirmDelete === true)
				{
					cms.removeItem( delURI, '<?php echo Lng::get('cms/mwms_link_del'); ?>' );
					$('div.mwms-data-widget').remove();
					return false;
				}
			}
		};
		
		var conf = {
			title : tabTitle,
			id : tabID,
			element : '#'+$(this).attr('id')+' .dir_toolbar',
			buttons : tbutton
		}
		 
		cms.toggleToolbar( conf );
	});

});
/* ]]> */
</script>
