<?php
/**
* class.mailer.php - WEEBO framework lib.
*
* THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
* IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
* FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
* AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
* LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
* OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
* SOFTWARE.
* --
*
* @package Mailer
* @author Jan Prochazka aka prochor <prochor666@gmail.com>
* @copyright 2012 Jan Prochazka aka prochor <prochor666@gmail.com>
* @license http://opensource.org/licenses/mit-license.php MIT License
* @version 1.1 (2013-03-21)
* @link 
*/

class Mailer{

public $useSmtp, $nameFrom, $mailFrom, $mailTo, $mailSubject, $mailBody, $contentType, $smtpServer, $smtpPort, $smtpUser, $smtpPassword, $adminMail;

private $smtpTimeout, $smtpLocalhost, $smtpNewLine, $mailHeaders, $sendStatus, $smtpError, $smtpLog, $attachmentSend;

public function __construct(){
	/* init default system public vars */
	$this->useSmtp = _USINGSMTP_;
	$this->smtpServer = _SMTPSERVER_;
	$this->smtpPort = _SMTPPORT_;
	$this->smtpUser = _SMTPUSER_;
	$this->smtpPassword = _SMTPPASSWORD_;
	$this->adminMail = _SYSTEMMAIL_;
	
	/* required email variables */
	$this->mailTo = null;
	$this->mailFrom = _ADMINMAIL_; // System account as default
	$this->nameFrom = _WEEBOSITETITLE_; // System name as default
	$this->mailBody = null;
	$this->mailSubject = null;
	$this->contentType = 'text/plain'; // Plain text as default ['text/plain' or 'text/html']
	$this->attachments = array(); // path/to/file
	
	/* init default system private vars */
	$this->smtpTimeout = _SMTPTIMEOUT_;
	$this->smtpNewLine = _SMTPNEWLINE_;
	$this->mailHeaders = null;
	$this->sendStatus = null;
	$this->smtpError = null;
	$this->smtpLog = array();
	$this->secure = _SMTPSECURE_;
	$this->smtpLocalhost = _SMTPLOCALHOST_;
	$this->attachmentSend = false;
}

public function getLog(){
	return $this->smtpLog;
}

public function getStatus(){
	return $this->sendStatus;
}

public function getError(){
	return $this->smtpError;
}

public function configureSmtp($data = array()){
	$this->smtpServer = array_key_exists('smtpServer', $data) ? $data['smtpServer']: $this->smtpServer;
	$this->smtpPort = array_key_exists('smtpPort', $data) ? $data['smtpPort']: $this->smtpPort;
	$this->smtpUser = array_key_exists('smtpUser', $data) ? $data['smtpUser']: $this->smtpUser;
	$this->smtpPassword = array_key_exists('smtpPassword', $data) ? $data['smtpPassword']: $this->smtpPassword;
	$this->smtpTimeout = array_key_exists('smtpTimeout', $data) ? $data['smtpTimeout']: $this->smtpTimeout;
	$this->smtpNewLine = array_key_exists('smtpNewLine', $data) ? $data['smtpNewLine']: $this->smtpNewLine;
	$this->secure = array_key_exists('secure', $data) ? $data['secure']: $this->secure;
	$this->smtpLocalhost = array_key_exists('smtpLocalhost', $data) ? $data['smtpLocalhost']: $this->smtpLocalhost;
}

public function sendMail(){

	if(
		mb_strlen($this->contentType)>0 && 
		mb_strlen($this->mailSubject)>0 && 
		mb_strlen($this->mailBody)>0 && 
		Validator::checkmail($this->mailFrom) !== false && 
		Validator::checkmail($this->mailTo) !== false
	){
		if(_ENCODESUBJECT_ === true)
		{
			$this->mailSubject ='=?UTF-8?B?'.base64_encode($this->mailSubject).'?=';
		}
		$this->nameFrom ='=?UTF-8?B?'.base64_encode($this->nameFrom).'?=';
		
		if(count($this->attachments)>0){
			//$newContentType = $this->contentType;
			$newContentType = 'multipart/mixed';
			$semiRand = strtoupper(System::hash(time()));
			$boundary = "boundary=\"==Multipart_Boundary_x".$semiRand."\"x"; 
			
			$this->mailHeaders .= "MIME-Version: 1.0" . "\r\n";
			$this->mailHeaders .= "X-Priority: 3" . "\r\n";
			$this->mailHeaders .= "X-MSMail-Priority: Normal" . "\r\n";
			$this->mailHeaders .= "X-MimeOLE: WEEBO-mailer-module(20120323)" . "\r\n";
			$this->mailHeaders .= "X-Mailer: WEEBO mailer module (20120323)" . "\r\n";
			//$this->mailHeaders .= "From: ".$this->nameFrom." <".$this->mailFrom.">" . "\r\n";
			//$this->mailHeaders .= "To: ".$this->mailTo."\r\n";
			
			$this->mailHeaders .= "Content-Type: ".$newContentType.";\n ".$boundary."\r\n\r\n";
			$this->mailHeaders .= "This is a multi-part message in MIME format.\r\n\r\n";
			$this->mailHeaders .= "--".$semiRand."\r\n";
			
			$this->mailHeaders .= "Content-Type: ".$this->contentType."; charset=UTF-8" . "; \r\n";
			$this->mailHeaders .= "Content-Transfer-Encoding: 8bit" . "\r\n\r\n";
			//$this->mailHeaders .= "Content-Transfer-Encoding: quoted-printable\r\n"; 
			$this->mailHeaders .= $this->mailBody."\r\n\r\n";
			$this->mailHeaders .= "--".$semiRand."\r\n";
			
			$attachmentMatrix = array();
			
			foreach($this->attachments as $attFile){
				if( $attachmentContent = @file_get_contents($attFile) )
				{
					$name = basename($attFile);
					$attachmentMatrix[$name] = chunk_split(base64_encode($attachmentContent)); 
					$this->sendStatus .= 'SENDING ATTACHMENT FILE: '.$attFile.'<br />';
				}else{
					$this->sendStatus .= 'ATTACHMENT FILE CORRUPT OR NOT FOUND: '.$attFile.'<br />';
				}
			}
			
			$key = 0;
			foreach($attachmentMatrix as $name => $attachmentContent){
				$this->mailHeaders .= "Content-Type: application/octet-stream;\r\n"; // use different content types here
				$this->mailHeaders .= "Content-Transfer-Encoding: base64\r\n";
				$this->mailHeaders .= "Content-Disposition: attachment; filename=\"".( count($attachmentMatrix) - 1 )."-".$key."-".$name."\"\r\n\r\n";
				$this->mailHeaders .= $attachmentContent."\r\n\r\n";
				$this->mailHeaders .= $key == ( count($attachmentMatrix) - 1 ) ? "--".$semiRand."--": "--".$semiRand."\r\n";
				$key++;
			}
			
			$this->attachmentSend = true;
			
		}else{
			$this->mailHeaders .= "MIME-Version: 1.0" . "\r\n";
			$this->mailHeaders .= "X-Priority: 3" . "\r\n";
			$this->mailHeaders .= "X-MSMail-Priority: Normal" . "\r\n";
			$this->mailHeaders .= "X-MimeOLE: WEEBO-mailer-module(20120323)" . "\r\n";
			$this->mailHeaders .= "X-Mailer: WEEBO mailer module(20120323)" . "\r\n";
			$this->mailHeaders .= "Content-Type: ".$this->contentType."; charset=UTF-8" . "; \r\n";
			$this->mailHeaders .= "Content-Transfer-Encoding: 8bit" . "\r\n";
			//$this->mailHeaders .= "From: ".$this->nameFrom." <".$this->mailFrom.">" . "\r\n";
			//$this->mailHeaders .= "To: ".$this->mailTo."\r\n";
		}
	  
		if($this->useSmtp === true){
			// using smtp auth
			$this->authSMTP();
			$this->sendStatus .= !is_null($this->smtpError) ? 'MAILER FAILED, SMTP MODE, REASON: '.$this->smtpError.'<br />' : 'MAILER OK, SMTP MODE<br />'; 
		}else{ 
			// using php mail function
			$xcode = mail($this->mailTo, $this->mailSubject, $this->mailBody, $this->mailHeaders);
			$this->sendStatus .= $xcode === false ? 'MAILER FAILED, MAIL MODE<br />' : 'MESSAGE SEND OK, MAIL MODE<br />';  
		}

	}else{
		$this->sendStatus = 'MESSAGE SEND FAILED, REASON: INCOMPLETE DATA';
	}
}

protected function authSMTP()
{
	//Connect to the host on the specified port
	if(strtolower(trim($this->secure)) == 'ssl') {
		$this->smtpServer = 'ssl://' . $this->smtpServer;
	}
	
	$smtpConnect = fsockopen($this->smtpServer, $this->smtpPort, $errno, $errstr, $this->smtpTimeout);
	$smtpResponse = fgets($smtpConnect, 515);
	
	if(empty($smtpConnect))
	{
		$this->smtpError = "Failed to connect: ".$smtpResponse;
	}else{
		$this->smtpLog['connection'] = "Connected: ".$smtpResponse;
	}

	
	fputs($smtpConnect, 'HELO ' . $this->smtpLocalhost . $this->smtpNewLine);
	$smtpResponse = fgets($smtpConnect, 515);
	$this->smtpLog['heloresponse'] = $smtpResponse;
	
	if(strtolower(trim($this->secure)) == 'tls') 
	{
		fputs($smtpConnect, 'STARTTLS' . $this->smtpNewLine);
		$smtpResponse = fgets($smtpConnect, 515);
		$this->smtpLog['starttls'] = $smtpResponse;
		stream_socket_enable_crypto($smtpConnect, true,STREAM_CRYPTO_METHOD_TLS_CLIENT);
		fputs($smtpConnect, 'HELO ' . $this->smtpLocalhost . $this->smtpNewLine);
		$smtpResponse = fgets($smtpConnect, 515);
		$this->smtpLog['tlsheloresponse'] = $smtpResponse;
	}
	
	
	if($this->smtpServer != 'localhost') {
		fputs($smtpConnect, 'AUTH LOGIN' . $this->smtpNewLine);
		$smtpResponse = fgets($smtpConnect, 515);
		$this->smtpLog['authrequest'] = $smtpResponse;
		fputs($smtpConnect, base64_encode($this->smtpUser) . $this->smtpNewLine);
		$smtpResponse = fgets($smtpConnect, 515);
		$this->smtpLog['authusername'] = $smtpResponse;
		fputs($smtpConnect, base64_encode($this->smtpPassword) . $this->smtpNewLine);
		$smtpResponse = fgets($smtpConnect, 515);
		$this->smtpLog['authpassword'] = $smtpResponse;
	}

	/*
	//Request Auth Login
	fputs($smtpConnect,"AUTH LOGIN" . $this->smtpNewLine);
	$smtpResponse = fgets($smtpConnect, 515);
	$this->smtpLog['authrequest'] = $smtpResponse;
	
	//Send username
	fputs($smtpConnect, base64_encode($username) . $this->smtpNewLine);
	$smtpResponse = fgets($smtpConnect, 515);
	$this->smtpLog['authusername'] = $smtpResponse;

	//Send password
	fputs($smtpConnect, base64_encode($password) . $this->smtpNewLine);
	$smtpResponse = fgets($smtpConnect, 515);
	$this->smtpLog['authpassword'] = $smtpResponse;
	
	//Say Hello to SMTP
	fputs($smtpConnect, "HELO $smtpServer" . $this->smtpNewLine);
	$smtpResponse = fgets($smtpConnect, 515);
	$this->smtpLog['heloresponse'] = $smtpResponse;
	*/
	
	//Email From
	fputs($smtpConnect, "MAIL FROM: <".$this->mailFrom.">" . $this->smtpNewLine);
	$smtpResponse = fgets($smtpConnect, 515);
	$this->smtpLog['mailfromresponse'] = $smtpResponse;

	//Email To
	fputs($smtpConnect, "RCPT TO: <".$this->mailTo.">" . $this->smtpNewLine);
	$smtpResponse = fgets($smtpConnect, 515);
	$this->smtpLog['mailtoresponse'] = $smtpResponse;

	//The Email
	fputs($smtpConnect, "DATA" . $this->smtpNewLine);
	$smtpResponse = fgets($smtpConnect, 515);
	$this->smtpLog['data1response'] = $smtpResponse;
	
	if($this->attachmentSend===true)
	{
		//fputs($smtpConnect, "To: ".$this->mailTo."\nFrom: ".$this->nameFrom." <".$this->mailFrom.">\nSubject: ".$this->mailSubject."\n".$this->mailHeaders."\n\n");
		fputs($smtpConnect, "From: ".$this->nameFrom." <".$this->mailFrom.">\n");
		fputs($smtpConnect, "To: ".$this->mailTo."\n");
		fputs($smtpConnect, $this->mailHeaders);
		fputs($smtpConnect, "Subject: ".$this->mailSubject."\n\n");
		
		$smtpResponse = fgets($smtpConnect, 515);
		$this->smtpLog['data2responseattachmenton'] = $smtpResponse;
	}else{
		fputs($smtpConnect, "From: ".$this->nameFrom." <".$this->mailFrom.">\n");
		fputs($smtpConnect, "To: ".$this->mailTo."\n");
		fputs($smtpConnect, $this->mailHeaders);
		fputs($smtpConnect, "Subject: ".$this->mailSubject."\n\n");
		fputs($smtpConnect, $this->mailBody."\r\n.\r\n");
		
		$smtpResponse = fgets($smtpConnect, 515);
		$this->smtpLog['data2responseattachmentoff'] = $smtpResponse;
	}
	// Say Bye bye
	fputs($smtpConnect,"QUIT\n");
	$smtpResponse = fgets($smtpConnect, 515);
	$this->smtpLog['quitresponse'] = $smtpResponse;
}


}
?>
