<?php
class ModelAccountQuestion extends Model {
	public function editQuestion($question_id, $data) {
		$this->db->query("
			UPDATE " . DB_PREFIX . "question
			SET text = '" . $this->db->escape($data['text']) . "'
			, status = '" . (int)$data['status'] . "'
			, date_modified = NOW()
			WHERE question_id = '" . (int)$question_id . "'
			AND customer_id = '" . (int)$this->customer->getId() . "'
		");
		// if ($this->db->countAffected() > 0) $this->cache->delete('question');
	}

	public function deleteQuestion($question_id) {
		$sql = sprintf('
			SELECT		`q`.*
			FROM		`%1$squestion` `q`
			WHERE		`q`.`question_id` = %2$s
			AND			`q`.`customer_id` = %3$s
			LIMIT 1
			',
			DB_PREFIX,
			(int)$question_id,
			(int)$this->customer->getId()
		);

		$query = $this->db->query($sql);

		if ($query->num_rows) {
			$data = $query->row;

			$this->db->query("
				INSERT INTO " . DB_PREFIX . "question_retired
				SET question_id = '" . (int)$question_id . "'
				, parent_id = '" . (int)$data['parent_id'] . "'
				, customer_id = '" . (int)$this->customer->getId() . "'
				, member_id = '" . (int)$data['member_id'] . "'
				, product_id = '" . (int)$data['product_id'] . "'
				, text = '" . $this->db->escape($data['text']) . "'
				, status = '" . (int)$data['status'] . "'
				, date_added = '" . $this->db->escape($data['date_added']) . "'
				, date_modified = '" . $this->db->escape($data['date_modified']) . "'
				, date_retired = NOW()
			");

			if ($this->db->countAffected() > 0) {
				$this->db->query("
					DELETE FROM " . DB_PREFIX . "question
					WHERE question_id = '" . (int)$question_id . "'
					AND customer_id = '" . (int)$this->customer->getId() . "
				'");
				// $this->cache->delete('question');
			}
		}
	}

	public function getQuestion($question_id) {
		$query = $this->db->query("
			SELECT DISTINCT q.*
			, m1.member_account_name AS member
			, pd.name AS product
			FROM " . DB_PREFIX . "question q
			LEFT JOIN " . DB_PREFIX . "customer_member_account m1 ON (q.member_id = m1.member_account_id)
			LEFT JOIN " . DB_PREFIX . "product_description pd ON (q.product_id = pd.product_id)
				AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "'
			WHERE q.question_id = '" . (int)$question_id . "'
			AND q.customer_id = '" . (int)$this->customer->getId() . "'
		");

		return $query->row;
	}

	public function enableQuestion($question_id) {
		$this->db->query("
			UPDATE " . DB_PREFIX . "question
			SET status = '1'
			WHERE question_id = '" . (int)$question_id . "'
			AND customer_id = '" . (int)$this->customer->getId() . "'
		");
		// if ($this->db->countAffected() > 0) $this->cache->delete('question');
	}

	public function disableQuestion($question_id) {
		$this->db->query("
			UPDATE " . DB_PREFIX . "question
			SET status = '0'
			WHERE question_id = '" . (int)$question_id . "'
			AND customer_id = '" . (int)$this->customer->getId() . "'
		");
		// if ($this->db->countAffected() > 0) $this->cache->delete('question');
	}

	public function getQuestions($data = array()) {
		$sql = "
			SELECT q.*
			, m1.member_account_name AS member
			, pd.name AS product
			FROM " . DB_PREFIX . "question q
			LEFT JOIN " . DB_PREFIX . "customer_member_account m1 ON (q.member_id = m1.member_account_id)
			LEFT JOIN " . DB_PREFIX . "product_description pd ON (q.product_id = pd.product_id)
			WHERE q.customer_id = '" . (int)$this->customer->getId() . "'
		";

		$sort_data = array(
			'location',
			'm1.member_account_name',
			'pd.name',
			'q.status',
			'q.date_added'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= $data['sort'] != 'location' ? " ORDER BY " . $data['sort'] : " ORDER BY COALESCE(pd.name, m1.member_account_name, '')";
		} else {
			$sql .= " ORDER BY q.date_added";
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

	public function getTotalQuestions($status = '') {
		$sql = "
			SELECT COUNT(question_id) AS total
			FROM " . DB_PREFIX . "question
			WHERE customer_id = '" . (int)$this->customer->getId() . "'
		";

		if ($status !== '' && !is_null($status)) {
			$sql .= "
				AND status = '" . (int)$status . "'
			";
		}

		$query = $this->db->query($sql);

		return $query->row['total'];
	}

}

