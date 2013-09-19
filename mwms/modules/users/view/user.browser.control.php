<?php
$ass = new UserBrowserTemplate;

//$ass->filter_reg['users_order'] = is_null($ass->filter_reg['users_order']) ? 'id_asset': $ass->filter_reg['users_order'];

echo $ass->showUsers();
?>

<script type="text/javascript">
/* <![CDATA[ */
$(document).ready(function(){
	
<?php if(Login::is_site_root()){ ?>
	/* ADMIN */
	var contentURI = "require&file=/mwms/modules/users/view/user.assign.dialog.php";

	dropBox.settings = {
		element : '#browser_filter',
		title : '<?php echo Lng::get('users/mwms_user_dropbox_selected'); ?>',
		buttonDoTitle : '<?php echo Lng::get('users/mwms_user_dropbox_do'); ?>',
		buttonResetTitle : '<?php echo Lng::get('users/mwms_user_dropbox_reset'); ?>',
		initURI : 'require&file=/mwms/modules/users/view/drop.box.php',
		reg : "users",
		item : "",
		docall : function(){
			
			users.processItems( '#user-action' ,contentURI, '<?php echo Lng::get('users/mwms_user_dropbox_do'); ?>' );
		},
		resetcall : function(){
			
			dropBox.reset();
			dropBox.unassignAll("tr.user_cast");
		} 
	}
	
	dropBox.init();
	dropBox.create();
	
	/* Pager */
	var targetContainer = $('div#mwms_load_content_inner');
	
	$("div.weebo_pager_fixed a").each( function(){
			var pager_uri = $(this).attr("href");
			
			$(this).button().click( function(){
				targetContainer.html('');
				users.showPreloader(targetContainer, 100);
				targetContainer.load( pager_uri );
				return false;
			});
	});


	$('button.user_new').button({
		icons: {
			primary: "ui-icon-newwin"
		}
	}).click( function(){
			
			var tabID = '0';
			
			var tabURI = weebo.settings.AjaxCall + "require&file=/mwms/modules/users/view/user.detail.process.php&id_user=" + tabID;
			users.addTab( tabURI, tabID, $(this).attr('title'), true);
			
	});

	
	/* Filter button */
	$('button.users_search_send').button({
		icons: {
			primary: "ui-icon-search"
		}
	});
	
	$('button.users_search_reset').button({
		icons: {
			primary: "ui-icon-circle-close"
		}
	});
	
	/* Fulltext */
	$("#user_browser").each( function(){
			var search_uri = $(this).find('input#users_search_path').val();
			var search_button = $(this).find('button.users_search_send');
			var reset_button = $(this).find('button.users_search_reset');
			var search_field = $('input#users_search');
			
			reset_button.click(
				
				function(){
					$('input#users_search').val('');
					
					targetContainer.html('');
					users.showPreloader(targetContainer, 100);
					targetContainer.load( search_uri + '&users_search_term=' );
					
					return false;
				}
			);	
			
			search_button.click(
				
				function(){
					var search_term = search_field.val();
				
					if(search_term.length >= <?php echo $ass->search_term_length_min; ?>){
						
						targetContainer.html('');
						users.showPreloader(targetContainer, 100);
						targetContainer.load( search_uri + '&users_search_term=' + $.trim(search_term) );
						
						return false;
					}else{
						alert('<?php echo Lng::get('users/search_term_short'); ?>');
					}
				
				}
			);	
			
			
			search_field.bind('keypress', function(e) {
					if(e.keyCode==13){
							// si entruj
							var search_term = search_field.val();
						
							if(search_term.length >= <?php echo $ass->search_term_length_min; ?>){
								
								targetContainer.html('');
								users.showPreloader(targetContainer, 100);
								targetContainer.load( search_uri + '&users_search_term=' + $.trim(search_term) );
						
							}else{
								alert('<?php echo Lng::get('users/search_term_short'); ?>');
							}
						
					}
					
			});

			
	});

<?php }else{ ?>

	$('button.user_new').button({
		icons: {
			primary: 'ui-icon-newwin'
		},
		disabled: true
	});

<?php } ?>
	/* Order */
	$("div.order_box a").each( function(){
			var order_uri = $(this).attr("href");
			
			$(this).click( function(){
				targetContainer.html('');
				users.showPreloader(targetContainer, 100);
				targetContainer.load( order_uri );
				return false;
			});
	});

	/* New tab opening */
	$(".user_cast").each( function(){
			
			//$(this).disableSelection();
			
			var tabIDstr = $(this).attr('id').split('_');
			var tabID = parseInt(tabIDstr[2]);
			var tabURI = "require&file=/mwms/modules/users/view/user.detail.process.php&id_user=" + tabID;
			var delURI = "require&file=/mwms/modules/users/view/user.action.php&id_user=" + tabID;
			var tabTitle = $(this).attr('title');
			
			var tbutton = new Array();
					
			tbutton[0] = {
				title : '<?php echo Lng::get('users/mwms_user_edit'); ?>',
				name : 'edit',
				icon : 'ui-icon-pencil',
				text : true,
				xcall: function(){
					users.addTab( weebo.settings.AjaxCall + tabURI, tabID, tabTitle, true );
					$('div.mwms-data-widget').remove();
					return false;
				}
			};	
	
	<?php if(Login::is_site_root()){ ?>
	/* ADMIN */
			if(tabID != <?php echo Registry::get('userdata/id_user'); ?>)
			{
				tbutton[1] = {
					title : '<?php echo Lng::get('users/mwms_user_del'); ?>',
					name : 'del',
					icon : 'ui-icon-closethick',
					text : true,
					xcall: function(){
						users.removeItem( delURI, '<?php echo Lng::get('users/mwms_user_del'); ?>' );
						$('div.mwms-data-widget').remove();
						return false;
					}
				};
			}
	<?php } ?>
	
			var conf = {
				title : tabTitle,
				id : tabID,
				element : '#'+$(this).attr('id')+' .toolbar',
				buttons : tbutton
				
			}
			 
			users.toggleToolbar( conf );
			
			
			$(this).rightClick(
				function(){
					
					users.addTab( weebo.settings.AjaxCall + tabURI, tabID, tabTitle, false );
				}
			)
			
		<?php if(Login::is_site_root()){ ?>
		/* ADMIN */
			.click(
				function(){
					
					if ( $(this).hasClass('highlight') ) {
						
						dropBox.remove(tabID);
						dropBox.unassignItem('tr#user_cast_'+tabID);
						
					} else {
						
						dropBox.add(tabID);
						dropBox.assignItem('tr#user_cast_'+tabID);
						
					}
					
					/*
					var button = new Array();
					
					button[0] = {
						title : '<?php echo Lng::get('users/mwms_user_edit'); ?>',
						name : 'edit',
						xcall: function(){
							users.addTab( tabURI, tabID, tabTitle, true );
							$('div.mwms-data-widget').remove();
							return false;
						}
					};	
						
					button[1] = {
						title : '<?php echo Lng::get('users/mwms_user_widget_assign'); ?>',
						name : 'assign',
						xcall: function(){
							users.dropBoxAdd( actionURI );
							users.assignItem(tabID);
							users.dropBoxUpdate();
							$('div.mwms-data-widget').remove();
							return false;
						}
					};	
					
					button[2] = {
						title : '<?php echo Lng::get('users/mwms_user_widget_unassign'); ?>',
						name : 'unassign',
						xcall: function(){
							users.dropBoxRemove( actionURI );
							users.unassignItem(tabID);
							users.dropBoxUpdate();
							$('div.mwms-data-widget').remove();
							return false;
						}
					};	
					
					button[3] = {
						title : '<?php echo Lng::get('users/mwms_user_del'); ?>',
						name : 'del',
						xcall: function(){
							users.removeItem( delURI, '<?php echo Lng::get('users/mwms_user_del'); ?>' );
							$('div.mwms-data-widget').remove();
							return false;
						}
					};
					
					var conf = {
						title : tabTitle,
						id : tabID,
						x : e.pageX,
						y : e.pageY,
						buttons : button
						
					}
					 
					users.toggleWidget( conf );
					*/
				}
			)
			
		<?php } ?>
			
			.css({ 'cursor': 'pointer'  });
	});

		
});

/* ]]> */
</script>
