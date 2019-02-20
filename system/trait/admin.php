<?php
trait Admin {
	protected $admin_user;

	protected function isAdmin() {
		if (!isset($this->admin_user)) {
			$this->setAdmin($this->registry);
		}

		return $this->admin_user;
	}

	protected function setAdmin($registry) {
		if (!is_object($registry) || get_class($registry) !== 'Registry' || $this->config->get('config_admin_mode_enabled') == false) {
			$this->admin_user = false;
			return;
		}

		$this->load->library('user');

		$this->user = new User($registry);

		if ($this->user->isLogged()) {
			$this->admin_user = true;
		} else {
			$this->admin_user = false;
		}
	}
}
?>
