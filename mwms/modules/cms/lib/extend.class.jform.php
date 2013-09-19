<?php
/* Mail form creator 1.0 */
class Jform extends CmsOutput{
	
	private $source_page;
	
	protected $formData, $formXml, $securityHash, $sentData, $formDisplayType, $recipients, $copies;
	
	public $idForm, $formHtml, $formName, $idHash, $isValid, $errorMessages, $fieldNames, $fieldValues, $attachments;
	
	/* CONSTRUCT */
	public function __construct(){
		parent::__construct();
		$this->source_page = $_SERVER['REQUEST_URI'];
		$this->idForm = 0;
		$this->formHtml = null;
		$this->formData = false;
		$this->formXml = false;
		$this->formDisplayType = 'div';
		$this->securityHash = 'unique';
		$this->formName = null;
		$this->idHash = 0;
		$this->sentData = $_POST;
		$this->errorMessages = array();
		$this->fieldNames = array();
		$this->fieldValues = array();
		$this->attachments = array();
		$this->copies = array();
		$this->isValid = true;
		$this->recipients = array();
	}
	
	/* HUMAN OUTPUT */
	public function createForm(){
		
		$this->idForm = (int)$this->idForm;
		$this->formData = $this->getFormData();
		$this->formName = System::hash('weebo-jform-'.$this->idForm);
		$this->idHash = System::hash($this->idForm);
		
		if(count($this->formData)>0 && $this->formData!==false){
			
			$this->formXml = @simplexml_load_string($this->formData['config']);
			
			if($this->formXml !== false){
				$this->parseFormData();
			}
		}
	}
	
	private function parseFormData(){
		
		if( count($this->formXml->jform) == 1 ){
			$this->formDisplayType = $this->attr($this->formXml->jform[0], 'type') == 'table' ? 'table': $this->formDisplayType;
			$this->parseSection();
		}
	}
	
	private function parseSection(){
		
		if(is_object($this->formXml->jform->section) && count($this->formXml->jform->section)>0)
		{
			$this->formHtml .= '
						<form id="jform-'.$this->idForm.'" class="jform" method="post" action="'.$this->source_page.'" enctype="multipart/form-data">
						<input type="hidden" name="'.$this->formName.'" value="'.$this->idHash.'" />
					';
			
			$sectionKey = 0;
			foreach($this->formXml->jform->section as $section){
				
				switch($this->formDisplayType){
					case 'table':
						
						$maxFieldCount = $this->countMaxColumns($section);
						
						$this->formHtml .= '
							<table class="jform-section jform-section-'.$sectionKey.'">
						';
						
						if(is_object($section->label))
						{
							$this->formHtml .= '
								<caption class="jform-caption">'.(string)$section->label.'</caption>
							';
						}
						
						if(is_object($section->description))
						{
							$this->formHtml .= '
								<thead class="jform-description"><tr><th colspan="'.$maxFieldCount .'">'.(string)$section->description.' '.count($section->row).'</th></tr></thead>
								';
						}
						
						$this->formHtml .= '
								<tbody class="jform-body">
							';
						
						//System::dump($section->row);
						
						$rowKey = 0;
						foreach($section->row as $row)
						{
							$fieldCount = is_object($row->field) ? count($row->field): 0;
							
							$this->formHtml .= '
									<tr class="jform-row jform-row-'.$rowKey.'">
								';
							
							if($fieldCount>0)
							{
								$fieldKey = 0;
								foreach($row->field as $field)
								{
									$fieldNamePattern = $sectionKey.'-'.$rowKey.'-'.$fieldKey;
									$required = $this->createRequired($field);
									$this->formHtml .= $fieldCount == ($fieldKey+1) && $maxFieldCount > $fieldCount && ($maxFieldCount - $fieldCount)>0 ? '
										<td class="jform-cell jform-cell-'.$fieldNamePattern.$required.'" colspan="'.(($maxFieldCount - $fieldCount)+1).'">'.$this->createDataField($fieldNamePattern, $field).'</td>
									': '
										<td class="jform-cell jform-cell-'.$fieldNamePattern.$required.'">'.$this->createDataField($fieldNamePattern, $field).'</td>
									';
									
									$fieldKey++;
								}
								
							}else{
								$this->formHtml .= '
										<td class="jform-separator" colspan="'.$maxFieldCount.'"></td>
									';
							}
							
							$this->formHtml .= '
									</tr>
									';
							$rowKey++;
						}
						
						$this->formHtml .= '
								</tbody>
							</table>
							';
						
					break; default:
						
						$maxFieldCount = $this->countMaxColumns($section);
						
						$this->formHtml .= '
							<div class="jform-section jform-section-'.$sectionKey.'">
							';
						
						if(is_object($section->label))
						{
							$this->formHtml .= '
								<div class="jform-caption">'.(string)$section->label.'</div>
								';
						}
						
						if(is_object($section->description))
						{
							$this->formHtml .= '
								<div class="jform-description">'.(string)$section->description.'</div>
								';
						}
						
						$this->formHtml .= '
								<div class="jform-body">
								';
						
						$rowKey = 0;
						foreach($section->row as $row)
						{
							$fieldCount = is_object($row->field) ? count($row->field): 0;
							$this->formHtml .= '
									<div class="jform-row jform-row-'.$rowKey.'">
								';
							
							if($fieldCount>0){
								$fieldKey = 0;
								foreach($row->field as $field){
									
									$fieldNamePattern = $sectionKey.'-'.$rowKey.'-'.$fieldKey;
									$required = $this->createRequired($field);
									$this->formHtml .= '
										<div class="jform-cell jform-cell-'.$fieldNamePattern.$required.'">'.$this->createDataField($fieldNamePattern, $field).'</div>
									';
									$fieldKey++;
								}
								
							}else{
								$this->formHtml .= '
										<div class="jform-separator"></div>
									';
							}
							
							$this->formHtml .= '
									</div>
									';
							$rowKey++;
						}
						
						$this->formHtml .= '
								</div>
							</div>
							';
						
				}
				$sectionKey++;
			}
			$this->formHtml .= '
							<div class="jform-panel">
								<button class="jform-send">'.Lng::get('cms/weebo_site_mailer_send').'</button>
							</div>
						</form>
					';
		}
	}
	
	private function createRequired($field){
		return $this->attr($field, 'required') == 1 ? ' jform-required': null;
	}
	
	private function createDataField($fieldNamePattern, $field){
		if(!is_null($this->attr($field, 'type'))){
			$fName = $this->attr($field, 'type').'Create';
			if(method_exists($this, $fName)){
				return $this->$fName($fieldNamePattern, $field);
			}else{
				return 'Error: unknown type';
			}
			
		}
		return null;
	}
	
	private function createLabel($fieldNamePattern, $field, $counter = null){
		$append = !is_null($counter) ? '-'.$counter.'-'.$this->securityHash: '-'.$this->securityHash;
		$cssClass = !is_null($counter) ? 'jform-label-inside label-field-'.$fieldNamePattern.$append: 'jform-label label-field-'.$fieldNamePattern.$append;
		if(is_null($counter)){
			$this->fieldNames['jform-cell-'.$fieldNamePattern] = (string)$field->label;
		}
		
		return is_object($field->label) ? '
										<label class="'.$cssClass.'" for="field-'.$this->idForm.'-'.$fieldNamePattern.$append.'">'.(string)$field->label.'</label>
		': null;
	}

	private function createLabelSpan($fieldNamePattern, $field){
		$append = '-'.$this->securityHash;
		$this->fieldNames['jform-cell-'.$fieldNamePattern] = (string)$field->label;
		
		return is_object($field->label) ? '
										<span class="jform-label-span label-field-'.$this->idForm.'-'.$fieldNamePattern.$append.'">'.(string)$field->label.'</span>
		': null;
	}

	/* text element */
	private function textCreate($fieldNamePattern, $field){
		
		$realValue = $this->getSingleVal($fieldNamePattern, $field);
		if( $this->validateField($realValue, $field) === false ){
			$this->isValid = false;
			$this->errorMessages['jform-cell-'.$fieldNamePattern] = $this->lng['weebo_site_mailer_text_error'];
		}
		
		$this->fieldValues['jform-cell-'.$fieldNamePattern] = $realValue;
		
		return $this->createLabel($fieldNamePattern, $field).'
										<input type="text" value="'.$realValue.'" name="field-'.$fieldNamePattern.'-'.$this->securityHash.'" class="jform-text jform-input" id="field-'.$this->idForm.'-'.$fieldNamePattern.'-'.$this->securityHash.'" />
									';
	}

	/* file element */
	private function fileCreate($fieldNamePattern, $field){
		
		$realValue = $this->getFileVal($fieldNamePattern, $field);
		if( $this->attr($field, 'required') == 1 && (is_null($realValue['name']) || is_null($realValue['path']) || is_null($realValue['size']) || $realValue['size'] == 0) ){
			$this->isValid = false;
			$this->errorMessages['jform-cell-'.$fieldNamePattern] = $this->lng['weebo_site_mailer_file_error'];
		}elseif(mb_strlen((string)$realValue['path'])>0 ){
			array_push($this->attachments, (string)$realValue['path']);
		}
		
		$this->fieldValues['jform-cell-'.$fieldNamePattern] = (string)$realValue['name'];
		
		//System::dump($file);
		return $this->createLabelSpan($fieldNamePattern, $field).'
										<input type="file" name="field-'.$fieldNamePattern.'-'.$this->securityHash.'" class="jform-file jform-input" id="field-'.$this->idForm.'-'.$fieldNamePattern.'-'.$this->securityHash.'" />
									';
	}

	/* email element */
	private function mailCreate($fieldNamePattern, $field){
		
		$realValue = $this->getSingleVal($fieldNamePattern, $field);
		if( $this->validateField($realValue, $field, 255) === false ){
			$this->isValid = false;
			$this->errorMessages['jform-cell-'.$fieldNamePattern] = $this->lng['weebo_site_mailer_mail_error'];
		}elseif($this->validateField($realValue, $field, 255) === true && $this->attr($field, 'copy') == 1){
			$this->copies[] = (string)$realValue;
		}
		
		$this->fieldValues['jform-cell-'.$fieldNamePattern] = $realValue;
		
		return $this->createLabel($fieldNamePattern, $field).'
										<input type="text" value="'.$realValue.'" name="field-'.$fieldNamePattern.'-'.$this->securityHash.'" class="jform-mail jform-input" id="field-'.$this->idForm.'-'.$fieldNamePattern.'-'.$this->securityHash.'" />
									';
	}

	/* URL element */
	private function urlCreate($fieldNamePattern, $field){
		
		$realValue = $this->getSingleVal($fieldNamePattern, $field);
		if( $this->validateField($realValue, $field, 2048) === false ){
			$this->isValid = false;
			$this->errorMessages['jform-cell-'.$fieldNamePattern] = $this->lng['weebo_site_mailer_url_error'];
		}
		
		$this->fieldValues['jform-cell-'.$fieldNamePattern] = $realValue;
		
		return $this->createLabel($fieldNamePattern, $field).'
										<input type="text" value="'.$realValue.'" name="field-'.$fieldNamePattern.'-'.$this->securityHash.'" class="jform-url jform-input" id="field-'.$this->idForm.'-'.$fieldNamePattern.'-'.$this->securityHash.'" />
									';
	}

	/* http element */
	private function httpCreate($fieldNamePattern, $field){
		
		$realValue = $this->getSingleVal($fieldNamePattern, $field);
		if( $this->validateField($realValue, $field, 2048) === false ){
			$this->isValid = false;
			$this->errorMessages['jform-cell-'.$fieldNamePattern] = $this->lng['weebo_site_mailer_http_error'];
		}
		
		$this->fieldValues['jform-cell-'.$fieldNamePattern] = $realValue;
		
		return $this->createLabel($fieldNamePattern, $field).'
										<input type="text" value="'.$realValue.'" name="field-'.$fieldNamePattern.'-'.$this->securityHash.'" class="jform-http jform-input" id="field-'.$this->idForm.'-'.$fieldNamePattern.'-'.$this->securityHash.'" />
									';
	}

	/* datetime element */
	private function datetimeCreate($fieldNamePattern, $field){
		
		$realValue = $this->getSingleVal($fieldNamePattern, $field);
		if( $this->validateField($realValue, $field, 128) === false ){
			$this->isValid = false;
			$this->errorMessages['jform-cell-'.$fieldNamePattern] = $this->lng['weebo_site_mailer_datetime_error'];
		}
		
		$this->fieldValues['jform-cell-'.$fieldNamePattern] = $realValue;
		
		return $this->createLabel($fieldNamePattern, $field).'
										<input type="text" value="'.$realValue.'" name="field-'.$fieldNamePattern.'-'.$this->securityHash.'" class="jform-datetime jform-input" id="field-'.$this->idForm.'-'.$fieldNamePattern.'-'.$this->securityHash.'" />
									';
	}

	/* date element */
	private function dateCreate($fieldNamePattern, $field){
		
		$realValue = $this->getSingleVal($fieldNamePattern, $field);
		if( $this->validateField($realValue, $field, 64) === false ){
			$this->isValid = false;
			$this->errorMessages['jform-cell-'.$fieldNamePattern] = $this->lng['weebo_site_mailer_date_error'];
		}
		
		$this->fieldValues['jform-cell-'.$fieldNamePattern] = $realValue;
		
		return $this->createLabel($fieldNamePattern, $field).'
										<input type="text" value="'.$realValue.'" name="field-'.$fieldNamePattern.'-'.$this->securityHash.'" class="jform-date jform-input" id="field-'.$this->idForm.'-'.$fieldNamePattern.'-'.$this->securityHash.'" />
									';
	}

	/* textarea element */
	private function textareaCreate($fieldNamePattern, $field){
		
		$realValue = $this->getSingleVal($fieldNamePattern, $field);
		if( $this->validateField($realValue, $field) === false ){
			$this->isValid = false;
			$this->errorMessages['jform-cell-'.$fieldNamePattern] = $this->lng['weebo_site_mailer_textarea_error'];
		}
		
		$this->fieldValues['jform-cell-'.$fieldNamePattern] = $realValue;
		
		return $this->createLabel($fieldNamePattern, $field).'
										<textarea rows="10" cols="50" name="field-'.$fieldNamePattern.'-'.$this->securityHash.'" class="jform-textarea jform-input" id="field-'.$this->idForm.'-'.$fieldNamePattern.'-'.$this->securityHash.'">'.$realValue.'</textarea>
									';
	}

	/* select element */
	private function selectCreate($fieldNamePattern, $field){
		
		$html = null;
		$valid = $this->attr($field, 'required') == 1 ? false: true;
		
		if(is_object($field->data))
		{
			$dataKey = 0;
			foreach($field->data as $data)
			{
				$realSelected = Validator::selected($this->attr($data, 'selected'), 1);
				$realValue = $this->getDefaultValue($data, true);
				
				if(
					array_key_exists('field-'.$fieldNamePattern.'-'.$this->securityHash, $this->sentData) 
				)
				{
					$_value = $this->sentData['field-'.$fieldNamePattern.'-'.$this->securityHash];
					
					if( 
						$realValue == $_value
					){
						$realSelected = Validator::selected(1, 1);
						$valid = $valid === false ? true: $valid;
					}else{
						$realSelected = Validator::selected(0, 1);
					}
				}
				
				$_value = isset($_value) ? $_value: $realValue;
				
				$html .= '
											<option value="'.$realValue.'" '.$realSelected.'>'.(string)$data.'</option>';
				$dataKey++;
			}
			
			$this->fieldValues['jform-cell-'.$fieldNamePattern] = $_value;
			
			if( $valid === false ){
				$this->isValid = false;
				$this->errorMessages['jform-cell-'.$fieldNamePattern] = $this->lng['weebo_site_mailer_select_error'];
			}
		}
		return $this->createLabel($fieldNamePattern, $field).'
										<select name="field-'.$fieldNamePattern.'-'.$this->securityHash.'" class="jform-select jform-input" id="field-'.$this->idForm.'-'.$fieldNamePattern.'-'.$this->securityHash.'">'.$html.'
										</select>
								';
	}
	
	/* checkbox element */
	private function checkboxCreate($fieldNamePattern, $field){
		
		$html = null;
		$valid = $this->attr($field, 'required') == 1 ? false: true;
		$___send = array();
		if(is_object($field->data))
		{
			$dataKey = 0;
			foreach($field->data as $data)
			{
				$realChecked = !array_key_exists($this->formName, $this->sentData) ? Validator::checked($this->attr($data, 'checked'), 1): null;
				$realValue = $this->getDefaultValue($data, true);
				
				if(
					array_key_exists('field-'.$fieldNamePattern.'-'.$this->securityHash, $this->sentData) 
					&& is_array($this->sentData['field-'.$fieldNamePattern.'-'.$this->securityHash]) 
					&& count($this->sentData['field-'.$fieldNamePattern.'-'.$this->securityHash])>0 
					&& array_key_exists($this->formName, $this->sentData) && $this->sentData[$this->formName] == $this->idHash
				)
				{
					$_values = $this->sentData['field-'.$fieldNamePattern.'-'.$this->securityHash];
					
					if( 
						array_search($realValue, $_values) !== false
					){
						$realChecked = Validator::checked(1, 1);
						$___send[] = $realValue;
						$valid = $valid === false ? true: $valid;
					}else{
						$realChecked = Validator::checked(0, 1);
					}
				}
				
				$html .= '
										<label class="jform-label-button jform-checkbox-label label-field-'.$fieldNamePattern.'-'.$this->securityHash.'-'.$dataKey.'" for="field-'.$this->idForm.'-'.$fieldNamePattern.'-'.$this->securityHash.'-'.$dataKey.'">
											<input type="checkbox" value="'.$realValue.'" name="field-'.$fieldNamePattern.'-'.$this->securityHash.'[]" class="jform-checkbox jform-input" id="field-'.$this->idForm.'-'.$fieldNamePattern.'-'.$this->securityHash.'-'.$dataKey.'" '.$realChecked.' />
											'.(string)$data.'
										</label>
						';
				$dataKey++;
			}
			
			$this->fieldValues['jform-cell-'.$fieldNamePattern] = implode(', ', $___send);
			
			if( $valid === false ){
				$this->isValid = false;
				$this->errorMessages['jform-cell-'.$fieldNamePattern] = $this->lng['weebo_site_mailer_checkbox_error'];
			}
		}
		return $this->createLabelSpan($fieldNamePattern, $field).'
										'.$html.'
								';
	}
	
	/* radio element */
	private function radioCreate($fieldNamePattern, $field){
		
		$html = null;
		$valid = $this->attr($field, 'required') == 1 ? false: true;
		
		if(is_object($field->data))
		{
			$dataKey = 0;
			foreach($field->data as $data)
			{
				$realChecked = Validator::checked($this->attr($data, 'checked'), 1);
				$realValue = $this->getDefaultValue($data, true);
				
				if(
					array_key_exists('field-'.$fieldNamePattern.'-'.$this->securityHash, $this->sentData) 
				)
				{
					$_value = $this->sentData['field-'.$fieldNamePattern.'-'.$this->securityHash];
					
					if( 
						$realValue == $_value
					){
						$realChecked = Validator::checked(1, 1);
						$valid = $valid === false ? true: $valid;
					}else{
						$realChecked = Validator::checked(0, 1);
					}
				}
				
				$html .= '
										<label class="jform-label-button jform-radio-label label-field-'.$fieldNamePattern.'-'.$this->securityHash.'-'.$dataKey.'" for="field-'.$this->idForm.'-'.$fieldNamePattern.'-'.$this->securityHash.'-'.$dataKey.'">
											<input type="radio" value="'.$realValue.'" name="field-'.$fieldNamePattern.'-'.$this->securityHash.'" class="jform-radio jform-input" id="field-'.$this->idForm.'-'.$fieldNamePattern.'-'.$this->securityHash.'-'.$dataKey.'" '.$realChecked.' />
											'.(string)$data.'
										</label>
						';
				$dataKey++;
			}
			
			$_value = isset($_value) ? $_value: $realValue;
			
			$this->fieldValues['jform-cell-'.$fieldNamePattern] = $_value;
			
			if( $valid === false ){
				$this->isValid = false;
				$this->errorMessages['jform-cell-'.$fieldNamePattern] = $this->lng['weebo_site_mailer_radio_error'];
			}
		}
		return $this->createLabelSpan($fieldNamePattern, $field).'
										'.$html.'
								';
	}
	
	/* Test / validate */
	private function validateField($fieldValue, $field, $length = 65535){
		return $this->attr($field, 'required') == 1 ? DataValidator::validateData($fieldValue, $this->attr($field, 'type'), $length): true;
	}

	/* File val array from $_FILES */
	private function getFileVal($fieldNamePattern, $field){
		
		$data = array('name' => null, 'path' => null, 'size' => 0);
		
		if(
			array_key_exists('field-'.$fieldNamePattern.'-'.$this->securityHash, $_FILES) && sizeof($_FILES)!=0 
		)
		{
			$file = $_FILES['field-'.$fieldNamePattern.'-'.$this->securityHash];
			if(file_exists($file['tmp_name']) && (int)$file['size']>0 && (int)$file['error'] == 0){
				//System::dump($file);
				$newPath = System::root().'/'.$this->config['mailer_cache'].'/'.$file['name'];
				move_uploaded_file($file['tmp_name'], $newPath);
				$data['name'] = $file['name'];
				//$data['data'] = file_get_contents($file['tmp_name']);
				$data['path'] = $newPath;
				$data['size'] = $file['size'];
			}
		}
		
		return $data;
	}

	
	/* Text val from $_POST */
	private function getSingleVal($fieldNamePattern, $field){
		
		$realValue = $this->getDefaultValue($field);
		
		if(
			array_key_exists('field-'.$fieldNamePattern.'-'.$this->securityHash, $this->sentData) 
		)
		{
			$_value = $this->sentData['field-'.$fieldNamePattern.'-'.$this->securityHash];
			
			if( 
				mb_strlen($_value)>0
			){
				$realValue = $_value;
			}else{
				$realValue = $_value;
			}
		}
		$this->fieldValues['jform-cell-'.$fieldNamePattern] = $realValue;
		return nl2br($realValue);
	}
	
	/* Default value from data element */
	private function getDefaultValue($obj, $ofType = false){
		$value = null;
		
		$myObj = $ofType === true ? $obj: $obj->data;
				
		if( is_object($myObj) )
		{
			$attr = $this->attr($myObj, 'value', $ofType);
			$value = !is_null($attr) ? $attr: (string)$myObj;
		}
		return $value;
	}
	
	/* Get XML attribute && default is NULL */
	protected function attr($obj, $attrName, $debug = false){
		$hasAttr = is_object($obj->attributes()->$attrName);
		return $hasAttr === true ? (string)$obj->attributes()->$attrName: null;
	}
	
	/* MISC colspan counter */
	protected function countMaxColumns($section){
		$c = 0;
		if(is_object($section->row)){
			foreach($section->row as $row){
				$c = is_object($row->field) && count($row->field)>$c ? count($row->field): $c;
			}
		}
		return $c;
	}
		
	/* DB get */
	private function getFormData()
	{
		$q = "SELECT * FROM "._SQLPREFIX_."_cms_forms WHERE id_form = '".(int)$this->idForm."' LIMIT 1";
		$qq = Db::result($q);
		return count($qq) == 1 ? $qq[0]: false;
	}

}

?>
