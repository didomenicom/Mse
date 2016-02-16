<?php
/**
 * Mse - PHP development framework for web applications
 * @author Mike Di Domenico
 * @copyright 2008 - 2016 Mike Di Domenico
 * @license https://opensource.org/licenses/MIT
 */
defined("Access") or die("Direct Access Not Allowed");

ImportFile("includes/mailer/class.phpmailer.php");

class Mailer extends PHPMailer {
	private $toAddress = array();
	private $ccAddress = array();
	private $bccAddress = array();
	private $sendFunction = "";
	
	public function Mailer($setupSMTP = 1){
		global $Config;
		Log::info("Configuring Mailer");
		
		// Enable HTML sending
		parent::IsHTML(true);
		
		// Determine type of sending
		switch($Config->getSystemVar("email_Type")){
			case "SMTP":
				parent::IsSMTP();
				$this->Host				= $Config->getSystemVar("email_SMTPHost");
				$this->Port				= $Config->getSystemVar("email_SMTPPort");
				$this->SMTPAuth			= $Config->getSystemVar("email_SMTPAuthentication");		// Enable SMTP authentication
				$this->SMTPKeepAlive	= $Config->getSystemVar("email_SMTPKeepAlive");			// SMTP connection will not close after each email sent
				$this->Username			= $Config->getSystemVar("email_SMTPUsername");
				$this->Password 		= $Config->getSystemVar("email_SMTPPassword");
				$this->SMTPSecure 		= $Config->getSystemVar("email_SMTPSecure");
				break;
			case "mail":
				parent::IsMail();
				break;
			case "sendMail":
				parent::IsSendmail();
				break;
			case "qMail":
				parent::IsQmail();
				break;
		}
		
		// Add the sender information
		parent::SetFrom($Config->getSystemVar("email_FromEmail"), $Config->getSystemVar("email_FromName"));
		parent::AddReplyTo($Config->getSystemVar("email_ReplyToEmail"), $Config->getSystemVar("email_ReplyToName"));
			
		Log::info("Mailer Configured");
	}
	
	public function Send($log = 1){
		global $db;
		Log::info("Sending Email");
		
		// Handle Alt Body
		$this->AltBody = strip_tags(str_replace('<br />', '\r', $this->Body)); // Text Body
		
		// Send it
		$result = parent::Send(); // 1 = Success
		
		// Log it
		if($log == 1){
			@$db->insert("INSERT INTO email_log (sendFunction, toName, bcc, cc, subject, body, altBody, attachmentCount, sendTime, success) VALUES (" . 
				"'" . addslashes(self::getSendFunction()) . "', " . 
				"'" . addslashes(self::getTo()) . "', " . 
				"'" . addslashes(self::getBcc()) . "', " . 
				"'" . addslashes(self::getCc()) . "', " . 
				"'" . addslashes(self::getSubject()) . "', " . 
				"'" . addslashes(self::getBody()) . "', " . 
				"'" . addslashes(self::getAltBody()) . "'," . 
				"'" . addslashes(self::getAttachmentCount()) . "', " . // TODO: Save attachments
				"'" . addslashes(self::getSendTime()) . "', " . 
				"'" . addslashes($result) . "')");
		}
		
		// Handle email failure
		// TODO: Implement
		
		Log::info("Email Sent");
		
		return $result;
	}
	
	public function setSendFunction($function){
		$this->sendFunction = $function;
	}
	
	public function Body($body){
		parent::MsgHTML($body);
	}
	
	public function AltBody($body){
		// Do nothing
	}
	
	public function AddAddress($email, $name = ""){
		if($email && $name){
			if(parent::ValidateAddress($email)){
				$result = parent::AddAddress($email, $name);
				if($result == true){
					$this->toAddress[count($this->toAddress)] = array($email, $name);
					return 1;
				}
			}
		}
		
		return false;
	}
	
	public function AddBCC($email, $name = ""){
		if($email && $name){
			if(parent::ValidateAddress($email)){
				$result = parent::AddBCC($email, $name);
				if($result == true){
					$this->bccAddress[count($this->bccAddress)] = array($email, $name);
					return 1;
				}
			}
		}
		
		return false;
	}
	
	public function AddCC($email, $name = ""){
		if($email && $name){
			if(parent::ValidateAddress($email)){
				$result = parent::AddCC($email, $name);
				if($result == true){
					$this->ccAddress[count($this->ccAddress)] = array($email, $name);
					return 1;
				}
			}
		}
		
		return false;
	}
	
	/** 
	 * MSE Specific calls
	 */
	public function setTo($inputName, $inputEmail){
		return self::AddAddress($inputEmail, $inputName);
	}
	
	public function setSubject($inputSubject){
		$this->Subject = $inputSubject;
	}
	
	public function setMessage($inputMessage){
		return self::Body($inputMessage);
	}
	/**
	 * Priavte logging functions
	 */
	private function getSendFunction(){
		return ($this->sendFunction);
	}
	
	private function getTo(){
		$output = "";
		
		for($i = 0; $i < count($this->toAddress); $i++){
			$output .= ($this->toAddress[$i][1] . " <" . $this->toAddress[$i][0] . ">||");
		}
		
		if(substr($output, (strlen($output) - 2), strlen($output)) === "||"){
			$output = substr($output, 0, (strlen($output) - 2));
		}
		
		return $output;
	}
	
	private function getCc(){
		$output = "";
		
		for($i = 0; $i < count($this->ccAddress); $i++){
			$output .= ($this->ccAddress[$i][1] . " <" . $this->ccAddress[$i][0] . ">||");
		}
		
		if(substr($output, (strlen($output) - 2), strlen($output)) === "||"){
			$output = substr($output, 0, (strlen($output) - 2));
		}
		
		return $output;
	}
	
	private function getBcc(){
		$output = "";
		
		for($i = 0; $i < count($this->bccAddress); $i++){
			$output .= ($this->bccAddress[$i][1] . " <" . $this->bccAddress[$i][0] . ">||");
		}
		
		if(substr($output, (strlen($output) - 2), strlen($output)) === "||"){
			$output = substr($output, 0, (strlen($output) - 2));
		}
		
		return $output;
	}
	
	private function getSubject(){
		return ($this->Subject);
	}
	
	private function getBody(){
		return ($this->Body);
	}
	
	private function getAltBody(){
		return ($this->AltBody);
	}
	
	private function getAttachmentCount(){
		return 0; // TODO: Implement
	}
	
	private function getSendTime(){
		// TODO: Handle Timezones
		return time();
	}
	
	public function setHost($inputValue){
		if(isset($inputValue)){
			$this->Host = $inputValue;
		}
	}
	
	public function setPort($inputValue){
		if(isset($inputValue) && $inputValue > 0){
			$this->Port = $inputValue;
		}
	}
	
	public function setSMTPAuth($inputValue){
		if(isset($inputValue) && ($inputValue == true || $inputValue == false)){
			$this->SMTPAuth = $inputValue;
		}
	}
	
	public function setUsername($inputValue){
		if(isset($inputValue)){
			$this->Username = $inputValue;
		}
	}
	
	public function setPassword($inputValue){
		if(isset($inputValue)){
			$this->Password = $inputValue;
		}
	}
	
	public function setSMTPSecure($inputValue){
		if(isset($inputValue)){
			$this->SMTPSecure = $inputValue;
		}
	}
	
	public function setFromInfo($inputEmail, $inputName){
		if(isset($inputEmail) && isset($inputName)){
			parent::SetFrom($inputEmail, $inputName);
		}
	}
	
	public function setReplyTo($inputEmail, $inputName){
		if(isset($inputEmail) && isset($inputName)){
			parent::AddReplyTo($inputEmail, $inputName);
		}
	}
	
	public function ClearAllRecipients(){
		parent::ClearAllRecipients();
		$this->toAddress = array();
		$this->ccAddress = array();
		$this->bccAddress = array();
	}
	
	public function ClearAddresses(){
		parent::ClearAddresses();
		$this->toAddress = array();
	}
	
	public function ClearCCs(){
		parent::ClearCCs();
		$this->ccAddress = array();
	}
	
	public function ClearBCCs(){
		parent::ClearBCCs();
		$this->bccAddress = array();
	}
}

?>
