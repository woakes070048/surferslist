<?php
class User {
	private $user_id;
	private $username;
	private $permission = array();

	public function __construct($registry) {
		$this->db = $registry->get('db');
		$this->request = $registry->get('request');
		$this->session = $registry->get('session');

		if (isset($this->session->data['user_id'])) {
			$user_query = $this->db->query("
				SELECT u.user_id
				, u.username
				, u.ip
				, ug.permission
				FROM " . DB_PREFIX . "user u
				LEFT JOIN " . DB_PREFIX . "user_group ug ON u.user_group_id = ug.user_group_id
				WHERE u.user_id = '" . (int)$this->session->data['user_id'] . "'
				AND u.status = '1'
			");

			if ($user_query->num_rows) {
				$this->user_id = $user_query->row['user_id'];
				$this->username = $user_query->row['username'];

				if ($user_query->row['ip'] != $this->request->server['REMOTE_ADDR']) {
					$this->db->query("
						UPDATE " . DB_PREFIX . "user
						SET ip = '" . $this->db->escape($this->request->server['REMOTE_ADDR']) . "'
						WHERE user_id = '" . (int)$this->session->data['user_id'] . "'
					");
				}

				$permissions = unserialize($user_query->row['permission']);

				if (is_array($permissions)) {
					foreach ($permissions as $key => $value) {
						$this->permission[$key] = $value;
					}
				}
			} else {
				$this->logout();
			}
		}
	}

	public function login($username, $password, $override = false) {
		$sql = "
			SELECT u.user_id
			, u.username
			, u.password
			, ug.permission
			FROM " . DB_PREFIX . "user u
			LEFT JOIN " . DB_PREFIX . "user_group ug ON u.user_group_id = ug.user_group_id
			WHERE u.username = '" . $this->db->escape($username) . "'
			AND u.status = '1'
		";

		$user_query = $this->db->query($sql);

		if (!$user_query->num_rows) {
			return false;
		} else {
			$authenticated = $override ? true : false;

			if (password_verify($password, $user_query->row['password'])) {
				$authenticated = true;
			}

			if (!$authenticated) {
				return false;
			}

			// Regenerate session id
			$this->session->regenerateId();

			$this->session->data['user_id'] = $user_query->row['user_id'];

			$this->user_id = $user_query->row['user_id'];
			$this->username = $user_query->row['username'];

			$permissions = unserialize($user_query->row['permission']);

			if (is_array($permissions)) {
				foreach ($permissions as $key => $value) {
					$this->permission[$key] = $value;
				}
			}

			return true;
		}
	}

	public function logout() {
		unset($this->session->data['user_id']);

		$this->user_id = '';
		$this->username = '';

		session_destroy();
	}

	public function hasPermission($key, $value) {
		if (isset($this->permission[$key])) {
			return in_array($value, $this->permission[$key]);
		} else {
			return false;
		}
	}

	public function isLogged() {
		return $this->user_id;
	}

	public function getId() {
		return $this->user_id;
	}

	public function getUserName() {
		return $this->username;
	}

}
?>
