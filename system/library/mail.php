<?php
class Mail {
	protected $to;
	protected $cc;
	protected $bcc;
	protected $from;
	protected $sender;
	protected $replyto;
	protected $readreceipt;
	protected $subject;
	protected $text;
	protected $html;
	protected $attachments = array();
	protected $api_key;
	public $protocol = 'smtp'; // mail or smtp
	public $parameter;
	public $hostname;
	public $username;
	public $password;
	public $port = 25;
	public $timeout = 5;

	public function __construct($api_key = '') {
		$this->api_key = $api_key;
	}

	public function setTo($to) {
		$this->to = $to;
	}

	public function setCC($cc) {
		$this->cc = $cc;
	}

	public function setBCC($bcc) {
		$this->bcc = $bcc;
	}

	public function setFrom($from) {
		$this->from = $from;
	}

	public function setSender($sender) {
		$this->sender = $sender;
	}

	public function setReplyTo($replyto) {
		$this->replyto = $replyto;
	}

	public function setReadReceipt($readreceipt) {
		$this->readreceipt = $readreceipt;
	}

	public function setSubject($subject) {
		$this->subject = $subject;
	}

	public function setText($text) {
		$this->text = $text;
	}

	public function setHtml($html) {
		$this->html = $html;
	}

	public function addAttachment($filename) {
		$this->attachments[] = $filename;
	}

	public function toString() {
		$email_to_string = '';

		$email_parts = array(
			'subject'	=> $this->subject,
			'to'		=> $this->to,
			'from'		=> $this->from,
			'replyto'	=> $this->replyto,
			'cc'		=> !empty($this->cc) ? implode(',', $this->cc) : '',
			'bcc'		=> !empty($this->bcc) ? implode(',', $this->bcc) : '',
			'sender'	=> $this->sender,
			'text'		=> !empty($this->text) ? '<<' . trim(preg_replace('/\r|\n|\s+/', ' ', $this->text)) . '>>': 'n/a',
			'html'		=> !empty($this->html) ? '<<<' . trim(preg_replace('/\r|\n|\s+/', ' ', $this->html)) . '>>>' : 'n/a'
		);

		foreach ($email_parts as $key => $value) {
			$email_to_string .= strtoupper($key) . ':' . $value . ' | ';
		}

		return rtrim($email_to_string, ' | ');
	}

	public function send() {
		if ($this->validate()) {
			if (class_exists('SendGrid') && $this->api_key) {
				return $this->sendSendGrid();
			} else if (class_exists('PHPMailer')) {
				return $this->sendPHPMailer();
			} else {
				trigger_error('Error: no email service configured!');
				return false;
				// exit();
			}
		}
	}

	public function sendSendGrid() {
		$sendgrid = new SendGrid($this->api_key);

		$email = new SendGrid\Email();

		$email->addTo($this->to);

		if ($this->cc) {
			$email->addCc($this->cc);
		}

		if ($this->bcc) {
			$email->addBcc($this->bcc);
		}

		$email->setFrom($this->from);
		$email->setFromName($this->sender);

		if ($this->replyto) {
			$email->setReplyTo($this->replyto);
		}

		$email->setSubject($this->subject);

		if (!$this->html) {
			$email->setText($this->text);
		} else {
			$email->setHtml($this->html);

			if ($this->text) {
				$email->setText($this->text);
			} else {
				$email->setText('This is a HTML email and your email client software does not support HTML email!');
			}
		}

		foreach ($this->attachments as $attachment) {
			if (file_exists($attachment)) {
				$email->addAttachment($attachment);
			}
		}

		$email->addHeader('X-Sent-Using', 'SendGrid-API');
		$email->addHeader('X-Transport', 'web');

		try {
			$sendgrid->send($email);
			return true;
		} catch(\SendGrid\Exception $e) {
			$sendgrid_error_msg = $e->getCode() . "\n";

			foreach($e->getErrors() as $er) {
				$sendgrid_error_msg .= $er;
			}

			trigger_error('ERROR: email not sent! DETAILS: ' . $sendgrid_error_msg);
			return false;
			//exit();
		}
	}

	public function sendPHPMailer() {
		$mail = new PHPMailer;
		$mail->CharSet = "UTF-8";

		if (is_array($this->to)) {
			foreach ($this->to as $toTmp){
				$mail->addAddress($toTmp);
			}
		} else {
			$mail->addAddress($this->to);
		}

		if ($this->cc) {
			if (is_array($this->cc)) {
				foreach ($this->cc as $ccTmp){
					$mail->addCC($ccTmp);
				}
			} else {
				$mail->addCC($this->cc);
			}
		}

		if ($this->bcc) {
			if (is_array($this->bcc)) {
				foreach ($this->bcc as $bccTmp){
					$mail->addBCC($bccTmp);
				}
			} else {
				$mail->addBCC($this->bcc);
			}
		}

		if ($this->replyto) {
			$mail->addReplyTo($this->replyto);
		} else {
			$mail->addReplyTo($this->from, $this->sender);
		}

		if(!empty($this->readreceipt)) {
			$mail->ConfirmReadingTo = $this->readreceipt;
		}

		// debug
		$mail->SMTPDebug = 4;
		$mail->Debugoutput = 'error_log';  // 'html'

		$mail->Subject = $this->subject;
		$mail->setFrom($this->from, $this->sender);

		if (!$this->html) {
			$mail->Body = $this->text;
		} else {
			$mail->msgHTML($this->html);

			if ($this->text) {
				$mail->AltBody = $this->text;
			} else {
				$mail->AltBody = 'This is a HTML email and your email client software does not support HTML email!';
			}
		}

		foreach ($this->attachments as $attachment) {
			if (file_exists($attachment['file'])) {
				$mail->addAttachment($attachment['file']);
			}
		}

		// if ($this->protocol == 'smtp') {
		// 	$mail->isSMTP();
		// 	$mail->SMTPAuth = true;
		// 	// $mail->AuthType = 'LOGIN'; // 'XOAUTH2';
		// 	// $mail->oauthUserEmail = OAUTH_USER_EMAIL;
        //
		// 	if (strpos(HTTP_SERVER, 'test') !== false) {
		// 		$mail->oauthClientId = OAUTH_CLIENT_ID_TEST;
		// 		$mail->oauthClientSecret = OAUTH_CLIENT_SECRET_TEST;
		// 		$mail->oauthRefreshToken = OAUTH_REFRESH_TOKEN_TEST;
		// 	} else {
		// 		$mail->oauthClientId = OAUTH_CLIENT_ID;
		// 		$mail->oauthClientSecret = OAUTH_CLIENT_SECRET;
		// 		$mail->oauthRefreshToken = OAUTH_REFRESH_TOKEN;
		// 	}
        //
		// 	$mail->Host = $this->hostname;
		// 	$mail->Port = $this->port;
        //
		// 	if ($this->port == '587'){
		// 		$mail->SMTPAuth = true;
		// 		$mail->SMTPSecure = 'tls';
		// 	} else if ($this->port == '465') {
		// 		$mail->SMTPAuth = true;
		// 		$mail->SMTPSecure = 'ssl';
		// 	}
        //
		// 	if (!empty($this->username)  && !empty($this->password)) {
		// 		$mail->Username = $this->username;
		// 		$mail->Password = $this->password;
		// 	}
		// }

		if (!$mail->send()) {
			trigger_error('Error: mail was not sent! Details: ' . $mail->ErrorInfo);
			// exit();
			return false;
		} else {
			return true;
		}
	}

	private function validate() {
		$error = array();

		if (!$this->to) {
			$error[] = 'Error: E-Mail to required!';
		}

		if (!$this->from) {
			$error[] = 'Error: E-Mail from required!';
		}

		if (!$this->sender) {
			$error[] = 'Error: E-Mail sender required!';
		}

		if (!$this->subject) {
			$error[] = 'Error: E-Mail subject required!';
		}

		if ((!$this->text) && (!$this->html)) {
			$error[] = 'Error: E-Mail message required!';
		}

		if ($error) {
			trigger_error(implode(' | ', $error));
			return false;
			// exit();
		} else {
			return true;
		}
	}
}
