<?php
/* Mail form creator 1.0 */
class formSend extends Jform{
	
	public $formStatus, $mailBody;
	
	/* CONSTRUCT */
	public function __construct(){
		parent::__construct();
		
		$this->mailBody = null;
		$this->formStatus = null;
	}
	
	public function sendForm(){
		
		$this->addEmailRecipients();
		$this->collectData();
		$this->recipients = array_merge($this->recipients, $this->copies);
		$this->recipients = array_unique($this->recipients);
		
		$mailSubject ='=?UTF-8?B?'.base64_encode($this->formData['title']).'?=';
		$nameFrom ='=?UTF-8?B?'.base64_encode(_WEEBOSITETITLE_).'?=';
		$mailBodyEncoded = $this->mailBody; 
		
		foreach($this->recipients as $rec){
			
			$mail = new PHPMailer();
			
			if(_USINGSMTP_ === true)
			{
				$mail->IsSMTP();
				
				//Set the hostname of the mail server
				$mail->Host       = _SMTPSERVER_;
				//Set the SMTP port number - 587 for authenticated TLS, a.k.a. RFC4409 SMTP submission
				$mail->Port       = _SMTPPORT_;
				//Set the encryption system to use - ssl (deprecated) or tls
				$mail->SMTPSecure = _SMTPSECURE_;
				//Whether to use SMTP authentication
				$mail->SMTPAuth   = _SMTPAUTH_;
				//Username to use for SMTP authentication - use full email address for gmail
				$mail->Username   = _SMTPUSER_;
				//Password to use for SMTP authentication
				$mail->Password   = _SMTPPASSWORD_;
			}
			//$mail->SMTPDebug  = 2;
			//Ask for HTML-friendly debug output
			//$mail->Debugoutput = 'html';
			//Set who the message is to be sent from
			$mail->SetFrom(_SYSTEMMAIL_, $nameFrom);
			//Set an alternative reply-to address
			$mail->AddReplyTo(_SYSTEMMAIL_, $nameFrom);
			//Set who the message is to be sent to
			$mail->AddAddress($rec, $rec);
			//Set the subject line
			$mail->Subject = $mailSubject;
			$mail->IsHTML(true);
			$mail->CharSet = "utf-8";
			//Read an HTML message body from an external file, convert referenced images to embedded, convert HTML into a basic plain-text alternative body
			$mail->MsgHTML('<style>.jform-section-cell { font-size: 40px; }</style>'.$mailBodyEncoded);
			//Replace the plain text body with one created manually
			$mail->AltBody = strip_tags($this->mailBody);
			//Attach files
			foreach($this->attachments as $attFile){
				if( file_exists($attFile) && is_file($attFile) )
				{
					$mail->AddAttachment($attFile);
				}
			}
			
			sleep(1);
			
			$result = $mail->Send();
		}
		
		if(!$result){
			$this->formStatus = '
					<div class="jform-status jform-error-state">
						'.$this->lng['weebo_site_mailer_not_sent'].' 
					</div>
					<div class="jform-error error-mailer-0">
						'.$this->lng['weebo_site_mailer_field_error'].': '.(string)$mail->ErrorInfo.'
					</div>
				';
		}
	}
	
	private function collectData(){
		
		if(is_object($this->formXml->jform->section) && count($this->formXml->jform->section)>0)
		{
			$this->mailBody .= '
						<div id="jform-'.$this->idForm.'">
					';
			
			$sectionKey = 0;
			foreach($this->formXml->jform->section as $section){
				
				switch($this->formDisplayType){
					case 'table':
						
						$maxFieldCount = $this->countMaxColumns($section);
						
						$this->mailBody .= '
							<table class="jform-section jform-section-'.$sectionKey.'" style="margin: 10px 0 0 0; border-collapse: collapse; border: 1px solid #888888;">
						';
						
						if(is_object($section->label))
						{
							$this->mailBody .= '
								<tr><th colspan="'.$maxFieldCount .'" style="font-weight: bold; font-size: 16px; padding: 10px; background-color: #eeeeee;">'.(string)$section->label.'</th></tr>
							';
						}
						
						if(is_object($section->description))
						{
							$this->mailBody .= '
								<tr><th colspan="'.$maxFieldCount .'" style="font-size: 14px; padding: 10px; background-color: #eeeeee;">'.(string)$section->description.' '.count($section->row).'</th></tr>
								';
						}
						
						$this->mailBody .= '
								<tbody class="jform-body">
							';
						
						$rowKey = 0;
						foreach($section->row as $row)
						{
							$fieldCount = is_object($row->field) ? count($row->field): 0;
							
							$this->mailBody .= '
									<tr class="jform-section-row jform-section-row-'.$rowKey.'">
								';
							
							if($fieldCount>0)
							{
								$fieldKey = 0;
								foreach($row->field as $field)
								{
									$fieldNamePattern = $sectionKey.'-'.$rowKey.'-'.$fieldKey;
									
									$this->mailBody .= $fieldCount == ($fieldKey+1) && $maxFieldCount > $fieldCount && ($maxFieldCount - $fieldCount)>0 ? '
										<td class="jform-section-cell jform-section-cell-'.$fieldKey.'" colspan="'.(($maxFieldCount - $fieldCount)+1).'" style="font-size: 14px; padding: 10px;">'.$this->setFieldValue('jform-cell-'.$fieldNamePattern).'</td>
									': '
										<td class="jform-section-cell jform-section-cell-'.$fieldKey.'" style="font-size: 14px; padding: 10px;">'.$this->setFieldValue('jform-cell-'.$fieldNamePattern).'</td>
									';
									
									$fieldKey++;
								}
								
							}else{
								$this->mailBody .= '
										<td class="jform-separator" colspan="'.$maxFieldCount.'" style="padding: 10px;"></td>
									';
							}
							
							$this->mailBody .= '
									</tr>
									';
							$rowKey++;
						}
						
						$this->mailBody .= '
								</tbody>
							</table>
							';
						
					break; default:
						
						$maxFieldCount = $this->countMaxColumns($section);
						
						$this->mailBody .= '
							<div class="jform-section jform-section-'.$sectionKey.'" style="margin: 10px 0 0 0; border: 1px solid #888888;">
							';
						
						if(is_object($section->label))
						{
							$this->mailBody .= '
								<div class="jform-caption" style="font-weight: bold; font-size: 16px; padding: 10px; background-color: #eeeeee;">'.(string)$section->label.'</div>
								';
						}
						
						if(is_object($section->description))
						{
							$this->mailBody .= '
								<div class="jform-description" style="font-size: 14px; padding: 10px; background-color: #eeeeee;">'.(string)$section->description.'</div>
								';
						}
						
						$this->mailBody .= '
								<div class="jform-body">
								';
						
						$rowKey = 0;
						foreach($section->row as $row)
						{
							$fieldCount = is_object($row->field) ? count($row->field): 0;
							$this->mailBody .= '
									<div class="jform-section-row jform-section-row-'.$rowKey.'">
								';
							
							if($fieldCount>0){
								$fieldKey = 0;
								foreach($row->field as $field){
									$fieldNamePattern = $sectionKey.'-'.$rowKey.'-'.$fieldKey;
									
									$this->mailBody .= '
										<div class="jform-section-cell jform-section-cell-'.$fieldKey.'" style="font-size: 14px; padding: 10px;">'.$this->setFieldValue('jform-cell-'.$fieldNamePattern).'</div>
									';
									$fieldKey++;
								}
								
							}else{
								$this->mailBody .= '
										<div class="jform-separator" style="padding: 10px;"></div>
									';
							}
							
							$this->mailBody .= '
									</div>
									';
							$rowKey++;
						}
						
						$this->mailBody .= '
								</div>
							</div>
							';
						
				}
				$sectionKey++;
			}
			$this->mailBody .= '
						</div>
					';
		}
	
	}
	
	private function setFieldValue($key){
		
		$m = array_key_exists($key, $this->fieldNames) ? '<strong>'.(string)$this->fieldNames[$key].':</strong> ': null;
		$m .= array_key_exists($key, $this->fieldValues) ? (string)$this->fieldValues[$key]: null;
		return '<div>'.$m.'</div>';
	}
	
	
	public function setFormStatus(){
		
		if(count($this->errorMessages)>0)
		{
			$this->formStatus = '
					<div class="jform-status jform-error-state">
							'.$this->lng['weebo_site_mailer_not_sent'].' 
					</div>
			';
			
			foreach( $this->errorMessages as $key => $fn ){
				$this->formStatus .= '
					<div class="jform-error error-'.$key.'">
						'.$this->lng['weebo_site_mailer_field_error'].': '.(string)$this->fieldNames[$key].': '.$fn.'
					</div>
				';
			}
		}else{
			$this->formStatus = '
					<div class="jform-status jform-ok-state">
							'.$this->lng['weebo_site_mailer_sent'].'
					</div>
			';
		}
	}
	
	private function addEmailRecipients(){
		foreach($this->formXml->jform->recipients->data as $rec){
			if(DataValidator::validateData($rec, 'mail') === true)
			{
				$this->recipients[] = (string)$rec;
			}
		}
	}
}
?>
