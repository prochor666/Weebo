<?php
$id_article = isset($_GET['id_article']) && (int)$_GET['id_article']>0 ? (int)$_GET['id_article']: 0;

$mm = new DataProcessXmlWizard;

$srcData = $_POST;

$mm->input = array();
$mm->input['sourceData'] = $srcData;
$mm->input['id'] = $id_article;
$mm->input['fieldName'] = 'id_article';
$mm->input['tableName'] = '_mm_articles';
$mm->input['tableData'] = array(
	'title' => array('title' => Lng::get('mediamix/mwms_article_title'), 'system_type' => 'text', 'validate' => true, 'unique' => false, 'predefined' => 0, 'size' => 255),
	'public_order' => array('title' => Lng::get('mediamix/mwms_source_public_order'), 'system_type' => 'text', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 255, 'default_value' => 0),
	'data' => array('title' => Lng::get('mediamix/mwms_article_data'), 'system_type' => 'blob', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 65535, 'cleanup' => 1),
	'link' => array('title' => Lng::get('mediamix/mwms_article_link'), 'system_type' => 'blob', 'validate' => true, 'unique' => false, 'predefined' => 0, 'size' => 65535, 'cleanup' => 1),
	'date_public' => array('title' => Lng::get('mediamix/mwms_article_date_public'), 'system_type' => 'datetime', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 255, 'default_value' => time()),
	'id_public' => array('title' => Lng::get('mediamix/mwms_article_public'), 'system_type' => 'bool', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 1),
	//'id_type' => array('title' => Lng::get('mediamix/mwms_article_type'), 'system_type' => 'bool', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 1),
);

if($id_article>0){
	$mm->input['tableData']['id_upd'] = array('title' => 'hidden', 'system_type' => 'source', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 10);
	$mm->input['tableData']['date_upd'] = array('title' => 'hidden', 'system_type' => 'source', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 10);
}else{
	$mm->input['tableData']['id_ins'] = array('title' => 'hidden', 'system_type' => 'source', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 10);
	$mm->input['tableData']['date_ins'] = array('title' => 'hidden', 'system_type' => 'source', 'validate' => false, 'unique' => false, 'predefined' => 0, 'size' => 10);
}

$mm->input['metaUse'] = false;
$mm->input['metaConnectId'] = 'id_article';
$mm->input['metaTypesTableName'] = null;
$mm->input['metaDataTableName'] = null;

$mm->init();

echo $mm->showForm();

/* META EDIT */
//System::dump($mm->profileData);

if(array_key_exists('profileData', $mm) === true && is_array($mm->profileData) && array_key_exists('id_source', $mm->profileData) === true && $id_article>0){
	
	$a = new ArticleBrowserTemplate;
	
	$item = $mm->profileData;
	$s = $a->getSourceData($item['id_source']);
	
	if( array_key_exists('template', $s) && $s['template'] == 'autobazary' )
	{
		$meta = null;
		
		$mq = $a->getMeta($item);
		
		if(count($mq)){
			
			$meta .= '<table class="mediamix-meta">';
			
			foreach($mq as $m){
				
				if($m['tag'] == 'imageSet'){
					
					$images = '<div class="mediamix-images">';
					
					$imageTemp = $imageOrigin = json_decode($m['value']);
					array_walk($imageTemp, array($a, 'setFullPath'));
					
					$imageCount = 0;
					
					foreach($imageTemp as $key => $img){
						$images .= '<div class="mediamix-thumb" rel="mm-item-'.$item['id_article'].'" title="'.$imageOrigin[$key].'">
						<img src="'.$img.'" alt="'.$item['title'].'" /><br />
						</div>';
						$imageCount++;
					}
					
					$images .= '</div>';
					
				}else{
					
					if(mb_strtolower($m['tag']) == 'značka vozu'){
						$vendor = trim($m['value']);
					}
					
					if(mb_strtolower($m['tag']) == 'model vozu'){
						$model = trim($m['value']);
					}
					
					if(mb_strtolower($m['tag']) == 'motor'){
						$motor = trim($m['value']);
					}
					
					if(mb_strtolower($m['tag']) == 'rok výroby' && (int)$m['value'] > 1900){
						$year = trim($m['value']);
					}
					
					if(mb_strtolower($m['tag']) == 'cena'){
						$price = number_format(str_replace(' ', '', trim($m['value'])), 2, ',', ' ');
					}
					// Convert nl2br to ul/li html
					$_val = count( $_arr = explode("\n", trim($m['value'])) ) > 1 && array_walk($_arr, 'trim') === true ? '<textarea rows="10" cols="10" class="mm_text" name="'.$m['tag'].'">'.$m['value'].'</textarea>': '<input type="text" value="'.trim($m['value'].'" name="'.$m['tag'].'" class="mm_text" />'); 
					
					$meta .= '<tr class="mediamix-meta-rec"><th>'.$m['tag'].'</th><td>'.$_val.'</td></tr>';
				}
				
			}
			
			$meta .= '</table>';
		}
		
		echo $images.$meta;
	
	}

}


?>
<script type="text/javascript">
/* <![CDATA[ */
$(document).ready(function(){
	
<?php
if(array_key_exists('profileData', $mm) === true && is_array($mm->profileData) && array_key_exists('id_source', $mm->profileData) === true && $id_article>0){
	if( array_key_exists('template', $s) && $s['template'] == 'autobazary' )
	{
?>
	$('.mediamix-thumb').each(function(i){
		if(i<5){
			$(this).addClass('image-set-spot');
		}
	});
	
	$('.mediamix-images').sortable({
		stop: function( event, ui ) {
			
			var imageSet = $('.mediamix-thumb'); 
			var imageData = [];
			
			imageSet.removeClass('image-set-spot').each(function(i){
				t = $(this).attr('title');
				imageData.push(t);
				if(i<5){
					$(this).addClass('image-set-spot');
				}
 			});
			
			var itplURI = weebo.settings.AjaxCall + "require&file=/mwms/modules/mediamix/view/image.set.save.php";
			
			$.ajax({
				url: itplURI,
				type: 'post',
				dataType: 'text',
				data : { id_article: <?php echo (int)$id_article; ?>, imageSet: imageData },
				async: true,
				cache: false,
				success: function(response) {
					//console.log(response);
				},
				error: function(x, t, m) {
					//console.log(m);
					alert('Save error, server offline!');
				}
			});

		}
	});
	
	$('.mediamix-meta-rec').each(function(i){
		var tag = $(this).find('th:first').text();
		var ed = $(this).find('td:first .mm_text:first');
		
		ed.on('change', function(){
			var itplURI = weebo.settings.AjaxCall + "require&file=/mwms/modules/mediamix/view/meta.save.php";
			var that = $(this);
			var metaRow = $(this).val();
			
			$.ajax({
				url: itplURI,
				type: 'post',
				dataType: 'text',
				data : { id_article: <?php echo (int)$id_article; ?>, tag: tag, value: metaRow },
				async: true,
				cache: false,
				success: function(response) {
					//console.log(response);
					$('.mm-ok-message').remove();
					that.parent('td').append('<span class="mm-ok-message"><?php echo $a->lng['saved_message']; ?></span>');
					setTimeout("$('.mm-ok-message').remove();", 1000);
				},
				error: function(x, t, m) {
					//console.log(m);
					alert('Save error, server offline!');
				}
			});
			
		});
	});

<?php
	}
}
?>
	/* MAIN FORM */
	$('button.detail_save_meta_<?php echo $mm->input['tableName'].'_'.$mm->id; ?>').click(
		function(){
			weeboMeta.applyCallback('<?php echo $mm->input['tableName'].'_'.$mm->id; ?>', 'require&file=/mwms/modules/mediamix/view/articles.detail.process.save.php');
		}
	).button({
		icons: {
			primary: "ui-icon-circle-check",
			text: false
		}
	});
});
/* ]]> */
</script>
