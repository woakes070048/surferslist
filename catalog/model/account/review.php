<?php
class ModelAccountReview extends Model {
	public function editReview($review_id, $data) {
		$this->db->query("
			UPDATE " . DB_PREFIX . "review
			SET text = '" . $this->db->escape(strip_tags_decode($data['text'])) . "'
			, rating = '" . (int)$data['rating'] . "'
			, status = '" . (int)$data['status'] . "'
			, date_modified = NOW()
			WHERE review_id = '" . (int)$review_id . "'
			AND customer_id = '" . (int)$this->customer->getId() . "'
		");
	}

	public function deleteReview($review_id) {
		$this->db->query("
			DELETE FROM " . DB_PREFIX . "review
			WHERE review_id = '" . (int)$review_id . "'
			AND customer_id = '" . (int)$this->customer->getId() . "'
		");
	}

	public function getReview($review_id) {
		$query = $this->db->query("
			SELECT DISTINCT r.*
			, m1.member_account_name AS member
			, COALESCE(op.name, '-- NA --') AS order_product
			FROM " . DB_PREFIX . "review r
			LEFT JOIN " . DB_PREFIX . "customer_member_account m1 ON (r.member_id = m1.member_account_id)
			LEFT JOIN " . DB_PREFIX . "order_product op ON (r.order_product_id = op.product_id)
			WHERE r.review_id = '" . (int)$review_id . "'
			AND r.customer_id = '" . (int)$this->customer->getId() . "'
		");

		return $query->row;
	}

	public function enableReview($review_id) {
		$this->db->query("
			UPDATE " . DB_PREFIX . "review
			SET status = '1'
			WHERE review_id = '" . (int)$review_id . "'
			AND customer_id = '" . (int)$this->customer->getId() . "'
		");
	}

	public function disableReview($review_id) {
		$this->db->query("
			UPDATE " . DB_PREFIX . "review
			SET status = '0'
			WHERE review_id = '" . (int)$review_id . "'
			AND customer_id = '" . (int)$this->customer->getId() . "'
		");
	}

	public function getReviews($data = array()) {
		$sql = "
			SELECT r.review_id
			, r.member_id
			, r.customer_id
			, r.order_product_id
			, r.rating
			, r.text
			, r.status
			, r.date_added
			, m1.member_account_name AS member
			, COALESCE(op.name, '-- NA --') AS order_product
			FROM " . DB_PREFIX . "review r
			LEFT JOIN " . DB_PREFIX . "customer_member_account m1 ON (r.member_id = m1.member_account_id)
			LEFT JOIN " . DB_PREFIX . "order_product op ON (r.order_product_id = op.product_id)
			WHERE r.customer_id = '" . (int)$this->customer->getId() . "'
		";

		$sort_data = array(
			'm1.member_account_name',
			'op.name',
			'r.status',
			'r.date_added'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY r.date_added";
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

	public function getTotalReviews($status = '') {
		$sql = "
			SELECT COUNT(review_id) AS total
			FROM " . DB_PREFIX . "review
			WHERE customer_id = '" . (int)$this->customer->getId() . "'
		";

		if (!empty($status)) {
			$sql .= "
				AND status = '" . (int)$status . "'
			";
		}

		$query = $this->db->query($sql);

		return $query->row['total'];
	}

}
?>
