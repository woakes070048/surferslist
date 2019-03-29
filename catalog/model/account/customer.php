<?php
class ModelAccountCustomer extends Model {
	use Contact;

	public function addCustomer($data) {
		if (empty($data)) {
			return 0;
		}

		$this->db->query("
			INSERT INTO " . DB_PREFIX . "customer
			SET store_id = '" . (int)$this->config->get('config_store_id') . "'
			, firstname = '" . $this->db->escape($data['firstname']) . "'
			, lastname = '" . $this->db->escape($data['lastname']) . "'
			, email = '" . $this->db->escape($data['email']) . "'
			, salt = '" . $this->db->escape($salt = substr(hash_rand('md5'), 0, 9)) . "'
			, password = '" . $this->db->escape(password_hash($data['password'], PASSWORD_BCRYPT)) . "'
			, newsletter = '" . (isset($data['newsletter']) ? (int)$data['newsletter'] : 1) . "'
			, customer_group_id = '" . (int)$data['customer_group_id'] . "'
			, ip = '" . $this->db->escape($this->request->server['REMOTE_ADDR']) . "'
			, status = '1'
			, member_enabled = '0'
			, approved = '" . (int)!$data['approval_required'] . "'
			, date_added = NOW()
		");

		$customer_id = $this->db->countAffected() ? $this->db->getLastId() : 0;

		if (!$customer_id) {
			return 0;
		}

		if (!empty($data['provider'])) {
			$this->addSocialLogin($customer_id, $data);
		}

		// email notification
		$this->load->language('mail/customer');

		$verification_link = $data['approval_required'] ? $this->url->link('account/verification', 'v=' . $this->setVerificationCode($customer_id) . '&u=' . (int)$customer_id, 'SSL') : '';

		$message = sprintf($this->language->get('text_welcome'), $data['firstname'] . ' ' . $data['lastname'], $this->config->get('config_name')) . "\n\n";

		if (!$data['approval_required']) {
			$message .= sprintf($this->language->get('text_login'), $this->url->link('account/login', '', 'SSL')) . "\n\n";
		} else {
			$message .= $this->language->get('text_email_verification') . "\r\n";
            $message .= $verification_link . "\n\n";
		}

		$message .= sprintf($this->language->get('text_services'), $this->config->get('config_name')) . "\n\n";
		$message .= sprintf($this->language->get('text_forgotten'), $this->url->link('account/forgotten', '', 'SSL')) . "\n\n";
		$message .= $this->language->get('text_thanks') . "\r\n";
		$message .= $this->config->get('config_name');

		// htnl email
		$template = new Template();
		$template->data['title'] = sprintf($this->language->get('text_subject'), $this->config->get('config_name'));
		$template->data['store_name'] = $this->config->get('config_name');
		$template->data['store_url'] = $this->config->get('config_url');
		$template->data['logo'] = $this->config->get('config_url') . 'logo/logo-140x60.png'; // $this->config->get('config_url') . 'image/' . $this->config->get('config_logo');
		$template->data['text_welcome'] = sprintf($this->language->get('text_welcome'), $data['firstname'] . ' ' . $data['lastname'], $this->config->get('config_name'));
		$template->data['text_next_step'] = !$data['approval_required'] ? sprintf($this->language->get('text_login'), $this->url->link('account/login', '', 'SSL')) : $this->language->get('text_email_verification');
		$template->data['text_link'] = $verification_link ?: '';
		$template->data['text_services'] = ''; // $this->language->get('text_services');
		$template->data['text_forgotten'] = sprintf($this->language->get('text_forgotten'), sprintf($this->language->get('html_link'), $this->url->link('account/forgotten', '', 'SSL')));
		$template->data['text_thanks'] = $this->language->get('text_thanks');
		$template->data['text_signature'] = $this->config->get('config_name');
		$template->data['text_footer'] = $this->language->get('text_footer');
		$html_instructions = array_map('trim', explode('- ', sprintf($this->language->get('text_services'), $this->config->get('config_name'))));
		$template->data['text_instruction'] = array_shift($html_instructions);
		$template->data['instructions'] = array_map(function($item) { return ' - ' . ucfirst($item); }, $html_instructions);

		$mail_sent = $this->sendEmail(array(
			'to' 		=> $data['email'],
			'from' 		=> $this->config->get('config_email'),
			'sender' 	=> $this->config->get('config_name'),
			'subject' 	=> sprintf($this->language->get('text_subject'), $this->config->get('config_name')),
			'message' 	=> $message,
			'reply' 	=> $this->config->get('config_email'),
			'html' 		=> $template->fetch('/template/mail/account.tpl'),
			'admin'		=> true
		));

		return $customer_id;
	}

	public function addSocialLogin($customer_id, $data) {
		$this->db->query("
			INSERT INTO " . DB_PREFIX . "customer_login_social
			SET customer_id = '" . (int)$customer_id . "'
			, user_id = '" . (isset($data['user_id']) ? $this->db->escape($data['user_id']) : '0') . "'
			, provider = '" . $this->db->escape($data['provider']) . "'
			, token = '" . (isset($data['token']) ? $this->db->escape($data['token']) : '') . "'
			, token_expires = '" . (isset($data['token_expires']) ? date('Y-m-d H:i:s', time() + (int)$data['token_expires']) : date('Y-m-d H:i:s')) . "'
			, login_count = '0'
			, date_added = NOW()
			, date_modified = NOW()
		");

		// return $this->db->countAffected() ? $this->db->getLastId() : 0;
	}

	public function updateSocialLoginToken($customer_id, $data) {
		$this->db->query("
			UPDATE " . DB_PREFIX . "customer_login_social
			SET token = '" . (isset($data['token']) ? $this->db->escape($data['token']) : '') . "'
			, token_expires = '" . (isset($data['token_expires']) ? date('Y-m-d H:i:s', time() + (int)$data['token_expires']) : date('Y-m-d H:i:s')) . "'
			, date_modified = NOW()
			WHERE customer_id = '" . (int)$customer_id . "'
			AND user_id = '" . $this->db->escape($data['user_id']) . "'
			AND provider = '" . $this->db->escape($data['provider']) . "'
		");
	}

	public function updateSocialLoginCount($customer_id, $data) {
		$this->db->query("
			UPDATE " . DB_PREFIX . "customer_login_social
			SET login_count = (login_count + 1)
			, date_modified = NOW()
			WHERE customer_id = '" . (int)$customer_id . "'
			AND user_id = '" . $this->db->escape($data['user_id']) . "'
			AND provider = '" . $this->db->escape($data['provider']) . "'
		");
	}

	public function removeSocialLogin($customer_id) {
		// DISABLE
		$this->db->query("
			UPDATE " . DB_PREFIX . "customer_login_social
			SET status = 0
			, date_modified = NOW()
			WHERE customer_id = '" . (int)$customer_id . "'
			AND user_id = '" . $this->db->escape($data['user_id']) . "'
			AND provider = '" . $this->db->escape($data['provider']) . "'
		");

		// DELETE
		// $this->db->query("
		// 	DELETE FROM " . DB_PREFIX . "customer_login_social
		// 	WHERE customer_id = '" . (int)$customer_id . "'
		// 	AND user_id = '" . $this->db->escape($data['user_id']) . "'
		// 	AND provider = '" . $this->db->escape($data['provider']) . "'
		// ");
	}

	public function editCustomer($data) {
		$this->db->query("
			UPDATE " . DB_PREFIX . "customer
			SET firstname = '" . $this->db->escape($data['firstname']) . "'
			, lastname = '" . $this->db->escape($data['lastname']) . "'
			, email = '" . $this->db->escape($data['email']) . "'
			, telephone = '" . $this->db->escape($data['telephone']) . "'
			, fax = '" . $this->db->escape($data['fax']) . "'
			WHERE customer_id = '" . (int)$this->customer->getId() . "'
		");
	}

	public function editPassword($email, $password) {
		$this->db->query("
			UPDATE " . DB_PREFIX . "customer
			SET salt = '" . $this->db->escape($salt = substr(hash_rand('md5'), 0, 9)) . "'
			, password = '" . $this->db->escape(password_hash($password, PASSWORD_BCRYPT)) . "'
			WHERE LOWER(email) = '" . $this->db->escape(utf8_strtolower($email)) . "'
		");
	}

	public function editToken($email, $token) {
		$this->db->query("
			UPDATE " . DB_PREFIX . "customer
			SET token = '" . $this->db->escape($token) . "'
			WHERE LOWER(email) = '" . $this->db->escape(utf8_strtolower($email)) . "'
		");
	}

	public function editNewsletter($newsletter) {
		$this->db->query("
			UPDATE " . DB_PREFIX . "customer
			SET newsletter = '" . (int)$newsletter . "'
			WHERE customer_id = '" . (int)$this->customer->getId() . "'
		");
	}

	public function getCustomer($customer_id) {
		$query = $this->db->query("
			SELECT *
			FROM " . DB_PREFIX . "customer c
			LEFT JOIN " . DB_PREFIX . "customer_member_account m ON c.customer_id = m.customer_id
				AND m.customer_id <> 0
			WHERE c.customer_id = '" . (int)$customer_id . "'
		");

		return $query->row;
	}

	public function getCustomerByEmail($email) {
		$query = $this->db->query("
			SELECT *
			FROM " . DB_PREFIX . "customer
			WHERE LOWER(email) = '" . $this->db->escape(utf8_strtolower($email)) . "'
		");

		return $query->row;
	}

	public function getCustomerSocialByEmail($email, $provider = '') {
		$query = $this->db->query("
			SELECT c.customer_id
			, c.email
			, c.status AS account_status
			, c.approved
			, cls.provider
			, cls.user_id
			, cls.provider
			, cls.token AS social_token
			, cls.token_expires
			, cls.status AS social_status
			FROM " . DB_PREFIX . "customer c
			LEFT JOIN " . DB_PREFIX . "customer_login_social cls ON c.customer_id = cls.customer_id
			WHERE LOWER(c.email) = '" . $this->db->escape(utf8_strtolower($email)) . "'
			AND (LOWER(cls.provider) = '" . $this->db->escape(utf8_strtolower($provider)) . "' OR cls.provider = '' OR cls.provider IS NULL)
			LIMIT 1
		");

		return $query->row;
	}

	public function getCustomerByToken($token) {
		$query = $this->db->query("
			SELECT customer_id
			, email
			, status
			, approved
			FROM " . DB_PREFIX . "customer
			WHERE token = '" . $this->db->escape($token) . "'
			AND token != ''
		");

		if ($query->num_rows) {
			$this->db->query("
				UPDATE " . DB_PREFIX . "customer
				SET token = ''
			");
		}

		return $query->row;
	}

	public function getCustomers($data = array()) {
		$sql = "
			SELECT *
			, CONCAT(c.firstname, ' ', c.lastname) AS name
			, cg.name AS customer_group
			FROM " . DB_PREFIX . "customer c
			LEFT JOIN " . DB_PREFIX . "customer_group_description cg ON (c.customer_group_id = cg.customer_group_id)
		";

		$implode = array();

		if (isset($data['filter_name']) && !is_null($data['filter_name'])) {
			$implode[] = "LCASE(CONCAT(c.firstname, ' ', c.lastname)) LIKE '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "%'";
		}

		if (isset($data['filter_email']) && !is_null($data['filter_email'])) {
			$implode[] = "LCASE(c.email) = '" . $this->db->escape(utf8_strtolower($data['filter_email'])) . "'";
		}

		if (isset($data['filter_customer_group_id']) && !is_null($data['filter_customer_group_id'])) {
			$implode[] = "cg.customer_group_id = '" . $this->db->escape($data['filter_customer_group_id']) . "'";
		}

		if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
			$implode[] = "c.status = '" . (int)$data['filter_status'] . "'";
		}

		if (isset($data['filter_approved']) && !is_null($data['filter_approved'])) {
			$implode[] = "c.approved = '" . (int)$data['filter_approved'] . "'";
		}

		if (isset($data['filter_ip']) && !is_null($data['filter_ip'])) {
			$implode[] = "c.customer_id IN (SELECT customer_id FROM " . DB_PREFIX . "customer_ip WHERE ip = '" . $this->db->escape($data['filter_ip']) . "')";
		}

		if (isset($data['filter_date_added']) && !is_null($data['filter_date_added'])) {
			$implode[] = "DATE(c.date_added) = DATE('" . $this->db->escape($data['filter_date_added']) . "')";
		}

		if ($implode) {
			$sql .= " WHERE " . implode(" AND ", $implode);
		}

		$sort_data = array(
			'name',
			'c.email',
			'customer_group',
			'c.status',
			'c.ip',
			'c.date_added'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY name";
		}

		if (isset($data['order']) && ($data['order'] == 'DESC')) {
			$sql .= " DESC";
		} else {
			$sql .= " ASC";
		}

		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}

		$query = $this->db->query($sql);

		return $query->rows;
	}

	public function getTotalCustomersByEmail($email) {
		$query = $this->db->query("
			SELECT COUNT(customer_id) AS total
			FROM " . DB_PREFIX . "customer
			WHERE LOWER(email) = '" . $this->db->escape(utf8_strtolower($email)) . "'
		");

		return $query->row['total'];
	}

	public function getTotalCustomersByEmailAndName($email, $name) {
		$sql = "
			SELECT COUNT(customer_id) AS total
			FROM " . DB_PREFIX . "customer
			WHERE LOWER(email) = '" . $this->db->escape(utf8_strtolower($email)) . "'
		";

		if ($name) {
			"
				AND (
					LOWER(firstname) LIKE '" . $this->db->escape(utf8_strtolower($name)) . "%'
					OR LOWER(lastname) LIKE '" . $this->db->escape(utf8_strtolower($name)) . "%'
				)
			";
		}

		$query = $this->db->query($sql);

		return $query->row['total'];
	}

	public function getIps($customer_id) {
		$query = $this->db->query("
			SELECT *
			FROM " . DB_PREFIX . "customer_ip
			WHERE customer_id = '" . (int)$customer_id . "'
		");

		return $query->rows;
	}

	public function isBanIp($ip) {
		$query = $this->db->query("
			SELECT *
			FROM " . DB_PREFIX . "customer_ban_ip
			WHERE ip = '" . $this->db->escape($ip) . "'
		");

		return $query->num_rows;
	}

	public function setVerificationCode($customer_id) {
		list($usec, $sec) = explode(' ', microtime());
		srand((float) $sec + ((float) $usec * 100000));

		$verification_code = md5($customer_id . ':' . rand());

		$this->db->query("
			DELETE FROM " . DB_PREFIX . "customer_verification
			WHERE customer_id = '".(int)$customer_id."'
		");

		$this->db->query("
			INSERT INTO " . DB_PREFIX . "customer_verification
			SET customer_id = '".(int)$customer_id."'
			, verification_code = '" . $this->db->escape($verification_code) . "'
		");

		return $verification_code;
	}

	public function getVerificationCode($customer_id) {
		$query = $this->db->query("
			SELECT DISTINCT c.email
			, cv.verification_code
			FROM " . DB_PREFIX . "customer_verification cv
			LEFT JOIN " . DB_PREFIX . "customer c ON cv.customer_id = c.customer_id
			WHERE cv.customer_id='" . $customer_id . "'
		");

		return $query->row;
	}

	public function deleteVerificationCode($customer_id) {
		$this->db->query("
			UPDATE " . DB_PREFIX . "customer
			SET approved = '1'
			WHERE customer_id='" . $customer_id . "'
		");

		$this->db->query("
			DELETE FROM " . DB_PREFIX . "customer_verification
			WHERE customer_id='" . $customer_id . "'
		");
	}

	public function addLoginAttempt($email = "") {
		$query = $this->db->query("
			SELECT *
			FROM " . DB_PREFIX . "customer_login
			WHERE email = '" . $this->db->escape(utf8_strtolower((string)$email)) . "'
			AND ip = '" . $this->db->escape($this->request->server['REMOTE_ADDR']) . "'
		");

		if (!$query->num_rows) {
			$this->db->query("
				INSERT INTO " . DB_PREFIX . "customer_login
				SET email = '" . $this->db->escape(utf8_strtolower((string)$email)) . "'
				, ip = '" . $this->db->escape($this->request->server['REMOTE_ADDR']) . "'
				, total = 1
				, date_added = '" . $this->db->escape(date('Y-m-d H:i:s')) . "'
				, date_modified = '" . $this->db->escape(date('Y-m-d H:i:s')) . "'
			");
		} else {
			$this->db->query("
				UPDATE " . DB_PREFIX . "customer_login
				SET total = (total + 1)
				, date_modified = '" . $this->db->escape(date('Y-m-d H:i:s')) . "'
				WHERE customer_login_id = '" . (int)$query->row['customer_login_id'] . "'
			");
		}
	}

	public function getLoginAttempts($email) {
		if ($email) {
			$query = $this->db->query("
				SELECT *
				FROM " . DB_PREFIX . "customer_login
				WHERE email = '" . $this->db->escape(utf8_strtolower($email)) . "'
				ORDER BY date_modified DESC
				LIMIT 1
			");
		}

		// get total login attempts by host in the last hour (to protect against brute-force email guessing)
		if (!$email || !$query->num_rows) {
			$query = $this->db->query("
				SELECT SUM(total) AS total
				, MAX(date_modified) AS date_modified
				FROM " . DB_PREFIX . "customer_login
				WHERE date_modified >= '" . $this->db->escape(date('Y-m-d H:i:s', strtotime('-1 hour'))) . "'
				GROUP BY ip
				HAVING ip = '" . $this->db->escape($this->request->server['REMOTE_ADDR']) . "'
			");
		}

		return $query->row;
	}

	public function deleteLoginAttempts($email) {
		$this->db->query("
			DELETE FROM " . DB_PREFIX . "customer_login
			WHERE email = '" . $this->db->escape(utf8_strtolower($email)) . "'
		");
	}
}

