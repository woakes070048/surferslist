<?php
class ModelCatalogQuestion extends Model {
	use Admin;

	public function addQuestion($data) {
		if (empty($data)) {
			return;
		}

		$sql = "
			INSERT INTO " . DB_PREFIX . "question
			SET product_id = '" . (int)$data['product_id'] . "'
			, member_id = '" . (int)$data['member_id'] . "'
			, customer_id = '" . ($this->customer->isLogged() ? $this->customer->getId() : ($this->isAdmin() ? (int)$this->config->get('config_customer_id') : 0)) . "'
			, parent_id = '" . (isset($data['parent_id']) ? (int)$data['parent_id'] : 0) . "'
			, text = '" . $this->db->escape($data['text']) . "'
			, status = '1'
			, date_added = NOW()
		"; // , status = '" . (int)$this->customer->isProfileEnabled() . "'

		$this->db->query($sql);

		return $this->db->countAffected() ? $this->db->getLastId() : 0;
	}

	public function getQuestionsByProductId($product_id, $start = 0, $limit = 20) {
		if ($start < 0) {
			$start = 0;
		}

		if ($limit < 1) {
			$limit = 20;
		}

		$sql = "
			SELECT q.question_id
			, q.parent_id
			, q.text
			, q.date_added
			, c.customer_id
			, CONCAT(c.firstname, ' ', c.lastname) AS author_customer_name
			, m.customer_id AS author_customer_id
			, m.member_account_id AS author_member_id
			, m.member_account_name AS author_member_name
			, m.member_account_image AS author_member_image
			FROM " . DB_PREFIX . "question q
			LEFT JOIN " . DB_PREFIX . "customer c ON (q.customer_id = c.customer_id)
			LEFT JOIN " . DB_PREFIX . "customer_member_account m ON (q.customer_id = m.customer_id)
				AND m.customer_id <> 0
			WHERE q.product_id = '" . (int)$product_id . "'
			AND q.status = '1'
			ORDER BY q.date_added DESC
			LIMIT " . (int)$start . "," . (int)$limit;

		$query = $this->db->query($sql);

		return $query->rows;
	}

	public function getTotalQuestionsByProductId($product_id) {
		$query = $this->db->query("
			SELECT COUNT(question_id) AS total
			FROM " . DB_PREFIX . "question
			WHERE product_id = '" . (int)$product_id . "'
			AND status = '1'
		");

		return $query->row['total'];
	}

	public function getQuestionsByMemberId($member_id, $start = 0, $limit = 20) {
		if ($start < 0) {
			$start = 0;
		}

		if ($limit < 1) {
			$limit = 20;
		}

		$sql = "
			SELECT q.question_id
			, q.parent_id
			, q.text
			, q.date_added
			, c.customer_id
			, CONCAT(c.firstname, ' ', c.lastname) AS author_customer_name
			, m.customer_id AS author_customer_id
			, m.member_account_id AS author_member_id
			, m.member_account_name AS author_member_name
			, m.member_account_image AS author_member_image
			FROM " . DB_PREFIX . "question q
			LEFT JOIN " . DB_PREFIX . "customer c ON (q.customer_id = c.customer_id)
			LEFT JOIN " . DB_PREFIX . "customer_member_account m ON (q.customer_id = m.customer_id)
				AND m.customer_id <> 0
			WHERE q.member_id = '" . (int)$member_id . "'
			AND q.product_id = '0'
			AND q.status = '1'
			ORDER BY q.date_added DESC
			LIMIT " . (int)$start . "," . (int)$limit;

		$query = $this->db->query($sql);

		return $query->rows;
	}

	public function getTotalQuestionsByMemberId($member_id) {
		$query = $this->db->query("
			SELECT COUNT(question_id) AS total
			FROM " . DB_PREFIX . "question
			WHERE member_id = '" . (int)$member_id . "'
			AND product_id = '0'
			AND status = '1'
		");

		return $query->row['total'];
	}

	public function getTotalQuestionsByUser($since) {
		$query = $this->db->query("
			SELECT COUNT(question_id) AS total
			, MAX(date_added) AS date_added
			FROM " . DB_PREFIX . "question
			WHERE customer_id = '" . (int)$this->customer->getId() . "'
			AND date_added >= '" . $this->db->escape($since) . "'
			AND status = '1'
		");

		return $query->row;
	}

}
