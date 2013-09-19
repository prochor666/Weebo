<?php
$ass = new LinksBrowserTemplate;

echo $ass->showLinks();
?>
<script type="text/javascript">
/* <![CDATA[ */
var myActiveLink = <?php echo (int)Registry::get('cms_active_link'); ?>;

$(document).ready(function(){

	$('button.mwms_link_new').button({
		icons: {
			primary: "ui-icon-newwin"
		}
	}).click( function(){
			var cURI = "require&file=/mwms/modules/cms/view/links.detail.process.php&id_link=0";
			cms.newLink(cURI, $(this).attr('title'));
	});

	/* New tab opening */
	$("li.nav_link").each( function(index)
	{		
		var lnk = $(this).find('a:first');
		
		$(this).disableSelection();
		
		var tabIDstr = lnk.attr('id').split('_');
		var tabID = parseInt(tabIDstr[2]);
		
		if(myActiveLink > 0)
		{
			var _icon_ = tabID == myActiveLink ? 'ui-icon-folder-open': 'ui-icon-document';
			lnk.html( '<span class="ui-icon '+ _icon_ +'"></span>' + lnk.text());
			
			if(tabID == myActiveLink){
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
		
		var tabURI = "require&file=/mwms/modules/cms/view/links.detail.process.php&id_link=" + tabID;
		var delURI = "require&file=/mwms/modules/cms/view/links.action.php&id_link=" + tabID;
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
			element : '#'+$(this).attr('id')+' .links_toolbar',
			buttons : tbutton
		}
		 
		cms.toggleToolbar( conf );
	});

});

/* ]]> */
</script>
