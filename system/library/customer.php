<?php
class Customer {
	private $customer_id;
	private $firstname;
	private $lastname;
	private $email;
	private $telephone;
	private $fax;
	private $newsletter;
	private $customer_group_id;
	private $address_id;
	private $member_enabled;
	private $member_activated;
	private $member_city;
	private $member_zone_id;
	private $member_country_id;
	private $member_paypal;
	private $member_directory_images;
	private $member_directory_downloads;
	private $member_max_products;
	private $member_commission_rate;
	private $member_account_id;
	private $member_account_name;
	private $member_account_url;
	private $member_group_id;
	private $member_group_name;
	private $permissions = array();
	private $email_notify = array();

	public function __construct($registry) {
		$this->config = $registry->get('config');
		$this->db = $registry->get('db');
		$this->request = $registry->get('request');
		$this->session = $registry->get('session');
		$this->language = $registry->get('language');
		$this->url = $registry->get('url');

		if (isset($this->session->data['customer_id'])) {
			$customer_query = $this->db->query("
				SELECT c.customer_id
				, c.firstname
				, c.lastname
				, c.email
				, c.telephone
				, c.fax
				, c.newsletter
				, c.customer_group_id
				, c.address_id
				, c.approved
				, c.member_enabled
				, c.date_added
				, m.member_account_id
				, m.member_account_name
				, m.member_city
				, m.member_zone_id
				, m.member_country_id
				, m.member_paypal_account
				, m.member_directory_images
				, m.member_directory_downloads
				, m.member_max_products
				, m.member_commission_rate
				, m.member_group_id
				, cmg.member_group_name
				, cn.email_contact
				, cn.email_post
				, cn.email_discuss
				, cn.email_review
				, cn.email_flag
				, cip.customer_ip_id
				FROM " . DB_PREFIX . "customer c
				LEFT JOIN " . DB_PREFIX . "customer_member_account m ON c.customer_id = m.customer_id
				LEFT JOIN " . DB_PREFIX . "customer_notify cn ON (c.customer_id = cn.customer_id)
				LEFT JOIN " . DB_PREFIX . "customer_member_group cmg ON (m.member_group_id = cmg.member_group_id)
				LEFT JOIN " . DB_PREFIX . "customer_ip cip ON (c.customer_id = cip.customer_id)
					AND (cip.ip = '" . $this->db->escape($this->request->server['REMOTE_ADDR']) . "')
				WHERE c.customer_id = '" . (int)$this->session->data['customer_id'] . "'
				AND c.status = '1'
			");

			if ($customer_query->num_rows) {
				$this->setProperties($customer_query->row);

				// update cart and wishlist
				$this->db->query("
					UPDATE " . DB_PREFIX . "customer
					SET cart = '" . $this->db->escape(isset($this->session->data['cart']) ? serialize($this->session->data['cart']) : '') . "'
					, wishlist = '" . $this->db->escape(isset($this->session->data['wishlist']) ? serialize($this->session->data['wishlist']) : '') . "'
					, ip = '" . $this->db->escape($this->request->server['REMOTE_ADDR']) . "'
					WHERE customer_id = '" . (int)$this->customer_id . "'
				");

				// record new IP login
				if (!$customer_query->row['customer_ip_id']) {
					$this->db->query("
						INSERT INTO " . DB_PREFIX . "customer_ip
						SET customer_id = '" . (int)$this->session->data['customer_id'] . "'
						, ip = '" . $this->db->escape($this->request->server['REMOTE_ADDR']) . "'
						, date_added = NOW()
					");
				}
			} else {
				$this->logout();
			}
		}
	}

	public function login($email, $password, $override = false) {
		$sql = "
			SELECT c.customer_id
			, c.firstname
			, c.lastname
			, c.email
			, c.telephone
			, c.fax
			, c.newsletter
			, c.customer_group_id
			, c.address_id
			, c.approved
			, c.member_enabled
			, c.date_added
			, c.cart
			, c.wishlist
			, c.password
			, c.salt
			, m.member_account_id
			, m.member_account_name
			, m.member_city
			, m.member_zone_id
			, m.member_country_id
			, m.member_paypal_account
			, m.member_directory_images
			, m.member_directory_downloads
			, m.member_max_products
			, m.member_commission_rate
			, m.member_group_id
			, cmg.member_group_name
			, cn.email_contact
			, cn.email_post
			, cn.email_discuss
			, cn.email_review
			, cn.email_flag
			FROM " . DB_PREFIX . "customer c
			LEFT JOIN " . DB_PREFIX . "customer_member_account m ON (c.customer_id = m.customer_id)
			LEFT JOIN " . DB_PREFIX . "customer_notify cn ON (c.customer_id = cn.customer_id)
			LEFT JOIN " . DB_PREFIX . "customer_member_group cmg ON (m.member_group_id = cmg.member_group_id)
			WHERE LOWER(c.email) = '" . $this->db->escape(utf8_strtolower($email)) . "'
			AND c.status = '1'
		";

		if (!$override) {
			$sql .= "
				AND c.approved = '1'
			";
		}

		$customer_query = $this->db->query($sql);

		if (!$customer_query->num_rows) {
			return false;
		} else {
			$authenticated = $override ? true : false;

			if ($customer_query->row['password'] == sha1($customer_query->row['salt'] . sha1($customer_query->row['salt'] . sha1($password)))) {
				$authenticated = true;

				// BCRYPT migration
				$this->db->query("
					UPDATE " . DB_PREFIX . "customer
					SET salt = '" . $this->db->escape($salt = substr(hash_rand('md5'), 0, 9)) . "'
					, password = '" . $this->db->escape(password_hash($password, PASSWORD_BCRYPT)) . "'
					WHERE LOWER(email) = '" . $this->db->escape(utf8_strtolower($email)) . "'
				");
			}

			if (password_verify($password, $customer_query->row['password'])) {
				$authenticated = true;
			}

			if (!$authenticated) {
				return false;
			}

			// Create customer login cookie if HTTPS
			if ($this->config->get('config_secure')) {
				if ($this->request->isSecure()) {
					// Create a cookie and restrict it to HTTPS pages
					$this->session->data['customer_cookie'] = hash_rand('md5');

					setcookie('customer', $this->session->data['customer_cookie'], 0, '/', '', true, true);
				} else {
					return false;
				}
			}

			// Regenerate session id
			$this->session->regenerateId();

			// Token used to protect account functions against CSRF
			$this->setToken();

			$this->session->data['customer_id'] = $customer_query->row['customer_id'];
			$this->session->data['customer_login_time'] = time();

			if ($customer_query->row['cart'] && is_string($customer_query->row['cart'])) {
				$cart = unserialize($customer_query->row['cart']);

				foreach ($cart as $key => $value) {
					if (!array_key_exists($key, $this->session->data['cart'])) {
						$this->session->data['cart'][$key] = $value;
					} else {
						$this->session->data['cart'][$key] += $value;
					}
				}
			}

			if ($customer_query->row['wishlist'] && is_string($customer_query->row['wishlist'])) {
				if (!isset($this->session->data['wishlist'])) {
					$this->session->data['wishlist'] = array();
				}

				$wishlist = unserialize($customer_query->row['wishlist']);

				foreach ($wishlist as $product_id) {
					if (!in_array($product_id, $this->session->data['wishlist'])) {
						$this->session->data['wishlist'][] = $product_id;
					}
				}
			}

			$this->setProperties($customer_query->row);

			$this->db->query("
				UPDATE " . DB_PREFIX . "customer
				SET ip = '" . $this->db->escape($this->request->server['REMOTE_ADDR']) . "'
				WHERE customer_id = '" . (int)$this->customer_id . "'
			");

			return true;
		}
	}

	public function logout() {
		$this->db->query("
			UPDATE " . DB_PREFIX . "customer
			SET cart = '" . $this->db->escape(isset($this->session->data['cart']) ? serialize($this->session->data['cart']) : '') . "'
			, wishlist = '" . $this->db->escape(isset($this->session->data['wishlist']) ? serialize($this->session->data['wishlist']) : '') . "'
			WHERE customer_id = '" . (int)$this->customer_id . "'
		");

		$this->session->data['cart'] = array();

		unset($this->session->data['customer_id']);
		unset($this->session->data['customer_cookie']);
		unset($this->session->data['customer_token']);
		unset($this->session->data['customer_login_time']);
		unset($this->session->data['wishlist']);

		$this->removeUnusedImages($this->config->get('member_image_orphan_max_age'));
		$this->setProperties();
	}

	protected function setProperties($data = array()) {
		if ($data) {
			$this->customer_id = isset($data['customer_id']) ? $data['customer_id'] : '';
			$this->firstname = isset($data['firstname']) ? $data['firstname'] : '';
			$this->lastname = isset($data['lastname']) ? $data['lastname'] : '';
			$this->email = isset($data['email']) ? $data['email'] : '';
			$this->telephone = isset($data['telephone']) ? $data['telephone'] : '';
			$this->fax = isset($data['fax']) ? $data['fax'] : '';
			$this->newsletter = isset($data['newsletter']) ? $data['newsletter'] : '';
			$this->customer_group_id = isset($data['customer_group_id']) ? $data['customer_group_id'] : '';
			$this->address_id = isset($data['address_id']) ? $data['address_id'] : '';
			$this->approved = isset($data['approved']) ? $data['approved'] : 0;
			$this->member_enabled = $this->config->get('member_status') && isset($data['member_enabled']) ? $data['member_enabled'] : 0;
			$this->member_activated = !empty($data['member_account_id']) ? $data['member_account_id'] : 0;
			$this->member_city = isset($data['member_city']) ? $data['member_city'] : '';
			$this->member_zone_id = isset($data['member_zone_id']) ? $data['member_zone_id'] : '';
			$this->member_country_id = isset($data['member_country_id']) ? $data['member_country_id'] : '';
			$this->member_paypal = isset($data['member_paypal_account']) ? $data['member_paypal_account'] : '';
			$this->member_directory_images = isset($data['member_directory_images']) ? $data['member_directory_images'] : '';
			$this->member_directory_downloads = isset($data['member_directory_downloads']) ? $data['member_directory_downloads'] : '';
			$this->member_max_products = isset($data['member_max_products']) ? $data['member_max_products'] : '';
			$this->member_commission_rate = isset($data['member_commission_rate']) ? $data['member_commission_rate'] : '';
			$this->member_account_id = isset($data['member_account_id']) ? $data['member_account_id'] : '';
			$this->member_account_name = isset($data['member_account_name']) ? $data['member_account_name'] : '';
			$this->member_account_url = null;
			$this->member_group_id = isset($data['member_group_id']) ? $data['member_group_id'] : '';
			$this->member_group_name = isset($data['member_group_name']) ? $data['member_group_name'] : '';
			$this->email_notify['email_contact'] = isset($data['email_contact']) ? $data['email_contact'] : '';
			$this->email_notify['email_post'] = isset($data['email_post']) ? $data['email_post'] : '';
			$this->email_notify['email_discuss'] = isset($data['email_discuss']) ? $data['email_discuss'] : '';
			$this->email_notify['email_review'] = isset($data['email_review']) ? $data['email_review'] : '';
			$this->email_notify['email_flag'] = isset($data['email_flag']) ? $data['email_flag'] : '';
		} else {
			$this->customer_id = '';
			$this->firstname = '';
			$this->lastname = '';
			$this->email = '';
			$this->telephone = '';
			$this->fax = '';
			$this->newsletter = '';
			$this->customer_group_id = '';
			$this->address_id = '';
			$this->approved = 0;
			$this->member_enabled = 0;
			$this->member_activated = 0;
			$this->member_city = '';
			$this->member_zone_id = '';
			$this->member_country_id = '';
			$this->member_paypal = '';
			$this->member_directory_images = '';
			$this->member_directory_downloads = '';
			$this->member_max_products = '';
			$this->member_commission_rate = '';
			$this->member_account_id = 0;
			$this->member_account_name = '';
			$this->member_account_url = '';
			$this->member_group_id = '';
			$this->member_group_name = '';
			$this->email_notify['email_contact'] = '';
			$this->email_notify['email_post'] = '';
			$this->email_notify['email_discuss'] = '';
			$this->email_notify['email_review'] = '';
			$this->email_notify['email_flag'] = '';
		}

		$this->setMemberPermissions();
	}

	public function isLogged() {
		return $this->customer_id;
	}

	public function getId() {
		return $this->customer_id;
	}

	public function getFirstName() {
		return $this->firstname;
	}

	public function getLastName() {
		return $this->lastname;
	}

	public function getProfileId() {
		return $this->member_account_id;
	}

	public function getProfileName() {
		return $this->member_account_name;
	}

	public function getProfileUrl() {
		if (is_null($this->member_account_url)) {
			$this->setProfileUrl();
		}

		return $this->member_account_url;
	}

	private function setProfileUrl() {
		$this->member_account_url = $this->url->link('product/member/info', 'member_id=' . $this->member_account_id, 'SSL');
	}

	public function getEmail() {
		return $this->email;
	}

	public function getTelephone() {
		return $this->telephone;
	}

	public function getFax() {
		return $this->fax;
	}

	public function getNewsletter() {
		return $this->newsletter;
	}

	public function getCustomerGroupId() {
		return $this->customer_group_id;
	}

	public function getMemberGroupId() {
		return $this->member_group_id;
	}

	public function getAddressId() {
		return $this->address_id;
	}

	public function isApproved() {
		return $this->approved;
	}

	public function isProfileEnabled() {
		return $this->member_enabled;
	}

	public function hasProfile() {
		return $this->member_activated;
	}

	public function getMemberCity() {
		return $this->member_city;
	}

	public function getMemberZoneId() {
		return $this->member_zone_id;
	}

	public function getMemberCountryId() {
		return $this->member_country_id;
	}

	public function getMemberPayPal() {
		return $this->member_paypal;
	}

	public function getMemberImagesDirectory() {
		return $this->member_directory_images;
	}

	public function getMemberImagesInUse() {
		$images = array();

		if (!$this->hasProfile() || !$this->isProfileEnabled()) {
			return array();
		}

		$sql = "(SELECT image FROM " . DB_PREFIX . "product WHERE member_customer_id = '" . (int)$this->getId() . "' AND date_expiration >= NOW())
				UNION DISTINCT
				(SELECT pi.image FROM " . DB_PREFIX . "product_image pi INNER JOIN " . DB_PREFIX . "product p ON pi.product_id = p.product_id WHERE p.member_customer_id = '" . (int)$this->getId() . "' AND p.date_expiration >= NOW())
				UNION DISTINCT
				(SELECT member_account_image AS image FROM " . DB_PREFIX . "customer_member_account WHERE customer_id = '" . (int)$this->getId() . "')
				UNION DISTINCT
				(SELECT member_account_banner AS image FROM " . DB_PREFIX . "customer_member_account WHERE customer_id = '" . (int)$this->getId() . "')";

		$query = $this->db->query($sql);

		foreach ($query->rows as $result) {
			$images[] = $result['image'];
		}

		return $images;
	}

	public function getMemberImages($directory = '', $recursive = true) {
		$images = array();

		// quit if member image dir is not defined or is not below default member image dir
		if (!$this->getMemberImagesDirectory() || strpos($this->getMemberImagesDirectory(), $this->config->get('member_image_upload_directory')) !== 0) {
			return array();
		}

		$path = DIR_IMAGE . 'data/' . $this->getMemberImagesDirectory() . trim($directory);

		if ($recursive) {
			$images = glob_recursive(rtrim($path, '/') . '/*', GLOB_NOSORT);
		} else {
			$images = glob(rtrim($path, '/') . '/*');;
		}

		return $images;
	}

	public function getMemberImageOrphans($paths = array()) {
		$image_ophans = array();

		// quit if member image dir is not defined or is not below default member image dir
		if (!$this->getMemberImagesDirectory() || strpos($this->getMemberImagesDirectory(), $this->config->get('member_image_upload_directory')) !== 0) {
			return array();
		}

		// get an array of all member images in use by the member (e.g. "data/member/m/member-name/listing-001.jpg")
		$images_active = $this->getMemberImagesInUse();

		// get all paths inside member image upload directory
		$paths_check = !$paths ? $this->getMemberImages() : $paths;

		foreach ($paths_check as $path) {
			// get partial path (e.g. "member/m/member-name", "member/m/member-name/listings/category/listing-001.jpg")
			$sub_path = utf8_substr($path, strlen(DIR_IMAGE . 'data/'));

			// skip if path does not belong to this member
			if (strpos($sub_path, $this->getMemberImagesDirectory()) !== 0) continue;

			// skip if a dir or if a part of a path of an image in use
			if (is_dir($path) || preg_grep("/^" . preg_quote('data/' . $sub_path, '/') . ".*/", $images_active)) continue;

			// path is a file that is not in use
			if (is_file($path)) {
				$image_ophans[] = $path;
			}
		}

		return $image_ophans;
	}

	private function removeUnusedImages($max_age = 0) {
		$image_ophans = $this->getMemberImageOrphans();

		if (!$image_ophans) {
			return;
		}

		$delete = true;

		$log = new Log('images_debug.log');

		foreach ($image_ophans as $path) {
			if (!is_file($path)) {
				continue;
			}

			$delete = true;

			$sub_path = utf8_substr($path, strlen(DIR_IMAGE . 'data/'));

			$log->write('ORPHANED image: ' . $sub_path);

			if ($max_age !== 0) {
				$file_last_modified = filemtime($path);
				$file_age = $file_last_modified ? time() - $file_last_modified : 0;

				// keep orphaned image if less than max age
				if ($file_age <= $max_age) {
					$delete = false;
				}
			}

			if ($delete) {
				@unlink($path);

				$log->write('AUTO DELETED image: ' . $sub_path);
			}
		}
	}

	public function getMemberDownloadsDirectory() {
		return $this->member_directory_downloads;
	}

	public function getMemberMaxProducts() {
		return $this->member_max_products;
	}

	public function getMemberCommissionRate() {
		return $this->member_commission_rate;
	}

	public function getBalance() {
		if ((int)$this->customer_id <= 0) return null;

		$query = $this->db->query("
			SELECT SUM(amount) AS total
			FROM " . DB_PREFIX . "customer_transaction
			WHERE customer_id = '" . (int)$this->customer_id . "'
		");

		return $query->row['total'];
	}

	public function getRewardPoints() {
		if ((int)$this->customer_id <= 0) return null;

		$query = $this->db->query("
			SELECT SUM(points) AS total
			FROM " . DB_PREFIX . "customer_reward
			WHERE customer_id = '" . (int)$this->customer_id . "'
		");

		return $query->row['total'];
	}

  	private function setMemberPermissions() {
		if ((int)$this->customer_id <= 0) {
			$this->permissions = array();
			return;
		}

		$query = $this->db->query("
			SELECT *
			FROM " . DB_PREFIX . "customer_member_group cmg
			WHERE cmg.member_group_id = '" . (int)$this->member_group_id . "'
		");

		$this->permissions = $query->row;
	}

	public function getMemberPermissions() {
		return $this->permissions;
	}

	public function getMemberPermission($permission) {
		return isset($this->permissions[$permission]) ? $this->permissions[$permission] : 0;
	}

	public function getEmailNotifySettings() {
		return $this->email_notify;
	}

	public function getEmailNotifySetting($setting) {
		return isset($this->email_notify[$setting]) ? $this->email_notify[$setting] : 0;
	}

  	public function getTotalOrdersWithMember($member_id = 0) {
		if ((int)$this->customer_id <= 0) return null;

		$query = $this->db->query("
			SELECT COUNT(DISTINCT o.order_id) AS total
			FROM " . DB_PREFIX . "order o
			LEFT JOIN " . DB_PREFIX . "order_product op ON (o.order_id = op.order_id)
			WHERE o.customer_id = '" . (int)$this->customer_id . "'
			AND op.member_customer_id = '" . (int)$member_id . "'
		");

		return $query->row['total'];
  	}

	public function isSecure() {
		if (!$this->config->get('config_secure')
			|| ($this->request->isSecure()
				&& isset($this->request->cookie['customer'])
				&& isset($this->session->data['customer_cookie'])
				&& $this->request->cookie['customer'] == $this->session->data['customer_cookie']
		)) {
			return true;
		} else {
			return false;
		}
	}

	public function setToken() {
		$this->session->data['customer_token'] = hash_rand('md5');
		$this->session->data['customer_login_time'] = time();
	}

	public function loginExpired($age = 3600) {
		if (isset($this->session->data['customer_login_time']) && (time() - $this->session->data['customer_login_time'] < $age)) {
			return false;
		} else {
			return true;
		}
	}

  	public function validateToken() {
		if (!isset($this->request->get['customer_token'])
			|| !isset($this->session->data['customer_token'])
			|| $this->request->get['customer_token'] != $this->session->data['customer_token']) {
			$this->logout();
			$this->session->data['warning'] = $this->language->get('error_invalid_token');
			return false;
		} else {
			return true;
		}
	}

  	public function validateLogin() {
    	if (!$this->isLogged()) {
			$this->session->data['warning'] = $this->language->get('error_logged');
			return false;
		} else if ($this->loginExpired()) {
			$this->logout();
			$this->session->data['warning'] = $this->language->get('error_login_expired');
			return false;
		} else if (!$this->isSecure()) {
			$this->logout();
			$this->session->data['warning'] = $this->language->get('error_not_secure');
			return false;
		} else {
			return true;
		}
  	}

  	public function validateMembership() {
    	if (!$this->config->get('member_status') || !$this->config->get('member_product_manager')) {
			$this->session->data['warning'] = $this->language->get('error_membership');
			return false;
		} else {
			return true;
		}
	}

  	public function validateProfile() {
		$this->validateMembership();

    	if (!$this->hasProfile()) {
			$this->session->data['warning'] = sprintf($this->language->get('error_member_activate'), $this->url->link('account/member', '', 'SSL'));
			return false;
		} else if (!$this->isProfileEnabled()) {
			$this->session->data['warning'] = sprintf($this->language->get('error_member_disabled'), $this->url->link('information/contact', '', 'SSL'));
			return false;
		} else {
			return true;
		}
  	}

}
