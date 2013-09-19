<?php
class DataProcessXmlWizard extends DataProcessInit{

private $formConfig;

public function __construct(){

	parent::__construct();
	$this->formConfig = null;
}

public function	showForm(){

	$rows = $this->viewBase();

	$rows .= $this->metaUse ? $this->viewMeta(): null;

	$fc = htmlspecialchars('<xml>'.$this->formConfig.'</xml>');

	$html = '
	
	<table class="mwms_detail_table">
		<tr>
			<td>
				<form action="" method="post" id="form_call_'.$this->HtmlIdSuffix.'" enctype="multipart/form-data">
					<input type="hidden" name="'.$this->fieldName.'" value="'.$this->id.'" />
					<input type="hidden" name="form_config" value="'.$fc.'" />
			
					<table class="mwms_edit mwms_meta_detail">
						<tbody id="mode_call_'.$this->HtmlIdSuffix.'">
							'.
							$rows
							.'
							<tr class="metadata filtered">
								<td colspan="2" id="result_'.$this->HtmlIdSuffix.'">
								</td>
							</tr>
						</tbody>
					</table>
				</form>
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<div class="edit_panel_'.$this->HtmlIdSuffix.' metadata-toolbar">
					<button class="detail_save_meta_'.$this->HtmlIdSuffix.' button">'.Lng::get('system/mwms_save').'</button>
				</div>
			</td>
		</tr>
	</table>
	<div class="cleaner"></div>';

	return $html;
}


protected function viewBase(){
	$html = null;
	$d = $this->profileData;
	$table = 'base';

	foreach($this->tableData as $key => $data){

		$metaConfig = $this->createXml($key, $data, $table);
		$this->addConfig($metaConfig);
		$html .= $data['system_type'] != 'source' ? '
		<tr class="metadata filtered">
			<td class="meta_head"><label for="edit_field_'.$this->HtmlIdSuffix.'_'.$key.'">'.$data['title'].'</label></td>
			<td class="meta_edit_cell">'.$this->createFormField($metaConfig, $d[$key]).'</td>
		</tr>
		': null;
	}

	return $html;
}


protected function viewMeta(){
	$html = null;
	$table = 'meta';

	foreach($this->metaData as $d){

		$metaConfig = $this->createXml($d['id'], $d, $table);
		$this->addConfig($metaConfig);
		$html .= '
		<tr class="metadata filtered">
			<td class="meta_head"><label for="edit_field_'.$this->HtmlIdSuffix.'_'.$d['id'].'">'.$d['title'].'</label></td>
			<td class="meta_edit_cell">'.$this->createFormField($metaConfig, $this->loadMetaRow($d['id'], $d['system_type'])).'</td>
		</tr>';
	}

	return $html;
}


protected function addConfig($xml){
	$this->formConfig .= $xml;
}


protected function createFormField($metaConfig, $value){

	$symbolConfig = $this->extractXml($metaConfig);

	$type = $symbolConfig['system_type'];
	$real_type = $symbolConfig['predefined'] ==  1 ? 'multi': $symbolConfig['system_type'];
	$size = $symbolConfig['size'];
	$id_meta = $symbolConfig['name'];
	$table = $symbolConfig['table'];
	$cleanup = $symbolConfig['cleanup'];

	$valueShow = mb_strlen(DataValidator::displayData($value, $type))<1 && array_key_exists('default_value', $symbolConfig) ? DataValidator::displayData($symbolConfig['default_value'], $type): DataValidator::displayData($value, $type);

	$html = '<div class="edit_form" id="edit_form_'.$this->HtmlIdSuffix.'_'.$id_meta.'">';

	$meta_name = 'meta_value_'.$id_meta;
	$field_id = 'edit_field_'.$this->HtmlIdSuffix.'_'.$id_meta;

	switch($real_type){	
		case "text": case "int": case "float": 

			$html .= '<input type="text" name="'.$meta_name.'" class="text meta_live_edit" size="'.$size.'" value="'.htmlspecialchars($valueShow).'" id="'.$field_id.'" />';

		break; case "bool": 
			
			$html .= '<input type="checkbox" name="'.$meta_name.'" class="bool meta_live_edit" value="1" id="'.$field_id.'" '.Validator::checked($valueShow, 1).' />
				';
			/*
			$html .= '<input type="radio" name="'.$meta_name.'" class="text meta_live_edit" value="1" id="'.$field_id.'_1" '.Validator::checked($valueShow, 1).' />
					<input type="radio" name="'.$meta_name.'" class="text meta_live_edit" value="0" id="'.$field_id.'_0" '.Validator::checked($valueShow, 0).' />
				';
				*/ 
		break; case "mail": 

			$html .= '<input type="text" name="'.$meta_name.'" class="text meta_live_edit" size="'.$size.'" value="'.htmlspecialchars($valueShow).'" id="'.$field_id.'" />';

		break; case "password": 

			$html .= '<input type="password" name="'.$meta_name.'" class="text meta_live_edit" size="'.$size.'" value="" id="'.$field_id.'" />
					<input type="password" name="'.$meta_name.'_check" class="text meta_live_edit" size="'.$size.'" value="" id="'.$field_id.'_check" />
					<script type="text/javascript">
					/* <![CDATA[ */
					$(function(){

					});
					/* ]]> */
					</script>
			';

		break; case "datetime":	

			$html .= '<input type="text" name="'.$meta_name.'" class="datetime meta_live_edit" size="'.$size.'" value="'.$valueShow.'" id="'.$field_id.'" />
						<script type="text/javascript">
						/* <![CDATA[ */
						$(function(){

							var options = ({

								closeText: "'.Lng::get('system/mwms_date_time_closeText').'",
								prevText: "'.Lng::get('system/mwms_date_time_prevText').'",
								nextText: "'.Lng::get('system/mwms_date_time_nextText').'",
								currentText: "'.Lng::get('system/mwms_date_time_currentText').'",
								monthNames: '.Lng::get('system/mwms_date_time_monthNames').',
								monthNamesShort: '.Lng::get('system/mwms_date_time_monthNamesShort').',
								dayNames: '.Lng::get('system/mwms_date_time_dayNames').',
								dayNamesShort: '.Lng::get('system/mwms_date_time_dayNamesShort').',
								dayNamesMin: '.Lng::get('system/mwms_date_time_dayNamesMin').',
								weekHeader: "'.Lng::get('system/mwms_date_time_weekHeader').'",
								firstDay: '.Lng::get('system/mwms_date_time_firstDay').',
								isRTL: false,
								showMonthAfterYear: false,
								//yearSuffix: "'.Lng::get('system/mwms_date_time_yearSuffix').'",
								showSecond: true,
								timeFormat: "'.Lng::get('system/time_format_js_precise').'",
								stepHour: parseInt('.Lng::get('system/mwms_date_time_stepHour').'),
								stepMinute: parseInt('.Lng::get('system/mwms_date_time_stepMinute').'),
								stepSecond: parseInt('.Lng::get('system/mwms_date_time_stepSecond').'),
								timeOnlyTitle: "'.Lng::get('system/mwms_date_time_timeOnlyTitle').'",
								timeText: "'.Lng::get('system/mwms_date_time_timeText').'",
								hourText: "'.Lng::get('system/mwms_date_time_hourText').'",
								minuteText: "'.Lng::get('system/mwms_date_time_minuteText').'",
								secondText: "'.Lng::get('system/mwms_date_time_secondText').'",
								dateFormat: "'.mb_strtolower(Lng::get('system/date_format_js')).'",
								//changeMonth: true,
								numberOfMonths: 2,
								//changeYear: true,
								yearRange: "c-10:c+50",
								addSliderAccess: true,
								sliderAccessArgs: { touchonly: false }
							});

							ValidatorDataTypes.datetime("input#'.$field_id.'", options);
						});
						/* ]]> */
						</script>	
			';

		break; case "date":	

				$html .= '<input type="text" name="'.$meta_name.'" class="date meta_live_edit" size="'.$size.'" value="'.$valueShow.'" id="'.$field_id.'" />
					<script type="text/javascript">
					/* <![CDATA[ */
					$(function(){

						var options = ({

								closeText: "'.Lng::get('system/mwms_date_time_closeText').'",
								prevText: "'.Lng::get('system/mwms_date_time_prevText').'",
								nextText: "'.Lng::get('system/mwms_date_time_nextText').'",
								currentText: "'.Lng::get('system/mwms_date_time_currentText').'",
								monthNames: '.Lng::get('system/mwms_date_time_monthNames').',
								monthNamesShort: '.Lng::get('system/mwms_date_time_monthNamesShort').',
								dayNames: '.Lng::get('system/mwms_date_time_dayNames').',
								dayNamesShort: '.Lng::get('system/mwms_date_time_dayNamesShort').',
								dayNamesMin: '.Lng::get('system/mwms_date_time_dayNamesMin').',
								weekHeader: "'.Lng::get('system/mwms_date_time_weekHeader').'",
								firstDay: '.Lng::get('system/mwms_date_time_firstDay').',
								isRTL: false,
								showMonthAfterYear: false,
								yearSuffix: "'.Lng::get('system/mwms_date_time_yearSuffix').'",
							
								dateFormat: "'.mb_strtolower(Lng::get('system/date_format_js')).'",
								changeMonth: true,
								changeYear: true,
								yearRange: "c-100:c+100"

						});
						ValidatorDataTypes.date("input#'.$field_id.'", options);

					});
					/* ]]> */
					</script>	
			';


		break; case "blob":	

			$html .= '<textarea cols="20" rows="20" name="'.$meta_name.'" class="blob meta_live_edit" id="'.$field_id.'">'.$valueShow.'</textarea>';
			
			$default_lock_url = Registry::get('serverdata/site');
			if(array_key_exists('default_lock_url', $symbolConfig)){
				$default_lock_url = 'document_base_url : "/'.$symbolConfig['default_lock_url'].'",';
			}
			
			if(!$cleanup){
				$html .= '<script type="text/javascript">
					//<![CDATA[

					$(function(){
						var editorWidth = $("textarea#'.$field_id.'").css("width");
						var options = ({
								// Location of TinyMCE script
								script_url : "'.Registry::get('serverdata/site').'/shared/tinymce/tiny_mce.js",

								// General options
								theme : "advanced",
								skin : "o2k7",
								skin_variant : "silver",
								plugins : "pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,visualblocks",
								language : "'.Registry::get('lng').'",
								entity_encoding : "raw",
								width: parseInt(editorWidth),
								height: 300,
								schema: "html5",

								// Theme options
								theme_advanced_buttons1 : "search,replace,|,undo,redo,|,bold,italic,underline,strikethrough,|,pastetext,pasteword,|,justifyleft,justifycenter,justifyright,justifyfull,|,forecolor,backcolor,|,sub,sup,|,formatselect,fontsizeselect",
								theme_advanced_buttons2 : "bullist,numlist,|,outdent,indent,blockquote,|,link,unlink,anchor,image,media,charmap,hr,|,cleanup,removeformat,visualaid,|,tablecontrols,|,code",
								theme_advanced_buttons3 : "insertlayer,moveforward,movebackward,absolute,|,styleprops,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,pagebreak,|,fullscreen,visualblocks",
								theme_advanced_toolbar_location : "top",
								theme_advanced_toolbar_align : "left",
								theme_advanced_statusbar_location : "bottom",
								theme_advanced_resizing : true,
								theme_advanced_font_sizes : "10px,11px,12px,14px,15px,16px,17px,18px,19px,20px,22px,24px,26px,28px",

								'.$default_lock_url.'
								relative_urls : false, 
								
								content_css : "'.Registry::get('serverdata/site').'/mwms/modules/cms/css/cms.public.css",
								flash_video_player_url : false,
								file_browser_callback : "media.editorFiles",
								
								setup : function(ed)
								{
									ed.onInit.add(function(ed)
									{
										var e = ed.getDoc();
										e.body.style.fontSize="12px";
									});
								}
							});

						ValidatorDataTypes.blob("textarea#'.$field_id.'", options);

					});

					//]]>
					</script>
				';
			}
		
		break; case "code":	

			$html .= '<textarea cols="20" rows="20" name="'.$meta_name.'" class="blob meta_live_edit" id="'.$field_id.'">'.$valueShow.'</textarea>';
			
			if(!$cleanup){
				$html .= '<script type="text/javascript">
					//<![CDATA[

					$(function(){
						
					});

					//]]>
					</script>
				';
			}
		
		
		break; case "multi":	

			$xml = simplexml_load_string($symbolConfig['default_value'], 'SimpleXMLElement', LIBXML_NOCDATA);
			$html .= '<select name="'.$meta_name.'" class="select meta_live_edit" id="'.$field_id.'">';

			foreach($xml->item as $item){
				
				$real_value = isset($item->key) ? htmlspecialchars((string)$item->key): htmlspecialchars((string)$item->value);
				
				$html .= '<option value="'.$real_value.'"'.Validator::selected($valueShow, $real_value).'>'.(string)$item->value.'</option>';
			}

			$html .= '</select>';

		break; case "custom":	

			$html .= $symbolConfig['default_value'];

		break; case "custom_options":
			/* Need serialized associative array with key -> values */
			$html .= '<select name="'.$meta_name.'" class="select meta_live_edit" id="'.$field_id.'">'.$symbolConfig['default_value'].'</select>';

		break; case "method":
			/* Need serialized associative array with key -> values */
			$method = $symbolConfig['default_value'];
			$call = explode('::', $method);
			
			$___class = new $call[0];
			$html .= call_user_func_array(array($___class, $call[1]), array($symbolConfig, $this, $value));
			
		break; case "static-method":
			/* Need serialized associative array with key -> values */
			$method = $symbolConfig['default_value'];
			$call = explode('::', $method);
			
			$html .= call_user_func_array(array($call[0] ,$call[1]),array($symbolConfig, $this, $value)); 

		break; case "source":	

			$html .= null;

		break; default:	

			$html .= '<input type="text" name="'.$meta_name.'" class="text meta_live_edit" size="'.$size.'" value="'.htmlspecialchars($valueShow).'" id="'.$field_id.'" />';
	}

	$html .= '</div>';

	return $html;
}







}
?>
