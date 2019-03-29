<?php
class ModelAccountTransaction extends Model {
	public function getTransactions($data = array()) {
		$sql = "
			SELECT *
			FROM " . DB_PREFIX . "customer_transaction
			WHERE customer_id = '" . (int)$this->customer->getId() . "'
		";

		$sort_data = array(
			'amount',
			'description',
			'date_added'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY date_added";
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

	public function getTotalTransactions() {
		$query = $this->db->query("
			SELECT COUNT(customer_transaction_id) AS total
			FROM " . DB_PREFIX . "customer_transaction
			WHERE customer_id = '" . (int)$this->customer->getId() . "'
		");

		return $query->row['total'];
	}

	public function getTotalAmount() {
		$query = $this->db->query("
			SELECT SUM(amount) AS total
			FROM `" . DB_PREFIX . "customer_transaction`
			WHERE customer_id = '" . (int)$this->customer->getId() . "'
			GROUP BY customer_id
		");

		return $query->num_rows ? $query->row['total'] : 0;
	}
}

