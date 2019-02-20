<?php
class Session {
	public $data = array();

	public function __construct() {
		if (!session_id()) {
			/*
			ini_set('session.use_only_cookies', 'On');
			ini_set('session.use_trans_sid', 'Off');
			ini_set('session.cookie_httponly', 'On');
			*/
			
			// session_save_path(dirname($_SERVER['DOCUMENT_ROOT']).'/public_html/tmp');
			session_set_cookie_params(0, '/');
			session_start();
		}

		$this->data =& $_SESSION;
	}

	function getId() {
		return session_id();
	}

	public function regenerateId() {
		session_regenerate_id(true);
	}
}
?>