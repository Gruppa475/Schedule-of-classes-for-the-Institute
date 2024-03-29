<?php
/**
 * @package	AcyMailing for Joomla!
 * @version	5.10.10
 * @author	acyba.com
 * @copyright	(C) 2009-2019 ACYBA S.A.R.L. All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

defined('_JEXEC') or die('Restricted access');
?><?php
if(class_exists('plgSystemAcymailingClassMail', false)) return;
class plgSystemAcymailingClassMail extends JPlugin {
	public function __construct(&$subject, $config = array()) {
		$file = rtrim(JPATH_SITE,DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_acymailing'.DIRECTORY_SEPARATOR.'inc'.DIRECTORY_SEPARATOR.'joomlanotification'.DIRECTORY_SEPARATOR.'mail.php';
		try{
			if(file_exists($file)) require_once($file);
		}catch(Exception $e) {
 			echo "Could not load Acymailing JMail class at $file,<br />please disable the acymailingclassmail plugin (Override Joomla mailing system plugin)";
 		}
		parent::__construct($subject, $config);
	}

	public function onBeforeMailPrepare(&$mail, &$mailer, &$do){
		if(empty($mail->mail_name)) return false;
		require_once(rtrim(JPATH_ADMINISTRATOR,DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_acymailing'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'helper.php');
		$alias_email = 'hikashop-'.str_replace('_', '-', $mail->mail_name);
		$mailClass = acymailing_get('class.mail');
		$overrideEmail = $mailClass->get($alias_email);
		if(empty($overrideEmail) || $overrideEmail->published == 0) return false;

		$acymailer = acymailing_get('helper.acymailer');
		$acymailer->checkConfirmField = false;
		$acymailer->checkEnabled = false;
		$acymailer->checkAccept = false;
		$acymailer->autoAddUser = true;
		$acymailer->report = false;
		$acymailer->trackEmail = true;

		foreach($mail->data as $dataName => $dataValue){
			if(is_string($dataValue) || is_numeric($dataValue)){
				$acymailer->addParam($dataName, $dataValue);
			} else{
				if(!empty($dataValue)){
					foreach($dataValue as $dataSName => $dataSValue){
						if(is_string($dataSValue) || is_numeric($dataSValue)){
							$acymailer->addParam($dataName.'_'.$dataSName, $dataSValue);
						}else{
							if (!empty($dataSValue)){
								foreach($dataSValue as $dataSSName => $dataSSValue){
									if(is_string($dataSSValue) || is_numeric($dataSSValue)) $acymailer->addParam($dataName.'_'.$dataSName.'_'.$dataSSName, $dataSSValue);
								}
							}
						}
					}
				}
			}
		}

		if(!empty($mail->data->email)){
			$receiver = $mail->data->email;
		}elseif(!empty($mail->data->customer->email)){
			$receiver = $mail->data->customer->email;
		}else{
			return false;
		}

		$statusSend = $acymailer->sendOne($overrideEmail->mailid, $receiver);
		if(!$statusSend) acymailing_enqueueMessage(nl2br($acymailer->reportMessage), 'error');
		$mail->mail_success = $statusSend;

		$do = false;
		return $statusSend;
	}
}

