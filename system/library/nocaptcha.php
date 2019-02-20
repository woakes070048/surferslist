<?php
class NoCaptcha {
	private $secret;
	private $response;
	private $remoteip;
	private $noCaptcha;

	public function setResponse($gRecaptchaResponse) {
		$this->response = $gRecaptchaResponse;
	}

	public function setRemoteIp($remoteIp) {
		$this->remoteip = $remoteIp;
	}

	function __construct($secret) {
		$this->secret = $secret;

		$this->noCaptcha = new \ReCaptcha\ReCaptcha($this->secret);
	}

	function verify(){
		$recaptcha = $this->noCaptcha;

		$resp = $recaptcha->verify($this->response, $this->remoteip);

		if ($resp->isSuccess()) {
			return true;
		} else {
			$errors = $resp->getErrorCodes();

			return $errors;
		}
	}
}
?>
