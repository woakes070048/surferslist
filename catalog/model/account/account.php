<?php
class ModelAccountAccount extends Model {

	public function getTotalProducts($data = array()) {
		$query = $this->db->query("
			SELECT COUNT(DISTINCT p.product_id) AS total
			FROM " . DB_PREFIX . "product p
			LEFT JOIN " . DB_PREFIX . "product_member pm ON (p.product_id = pm.product_id)
			LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id)
			WHERE pm.customer_id = '" . (int)$this->customer->getId() . "'
			AND p.date_available <= NOW()
			AND p.date_expiration >= NOW()
			AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "'
		");

		return $query->row['total'];
	}

	public function getTotalSales() {
		$query = $this->db->query("
			SELECT COUNT(DISTINCT o.order_id) AS total
			FROM `" . DB_PREFIX . "order` o
			LEFT JOIN `" . DB_PREFIX . "order_product` op ON (o.order_id = op.order_id)
			WHERE op.member_customer_id = '" . (int)$this->customer->getId(). "'
			AND o.order_status_id > '0'
		");

		return $query->row['total'];
	}

	public function getTotalSalesProductsBySalesId($order_id) {
		$query = $this->db->query("
			SELECT DISTINCT COUNT(op.order_product_id) AS total
			FROM `" . DB_PREFIX . "order_product` op
			WHERE op.order_id = '" . (int)$order_id . "'
			AND op.member_customer_id = '" . (int)$this->customer->getId(). "'
		");

		return $query->row['total'];
	}

	// Profile Ratings
	public function getTotalReviews() {
		$query = $this->db->query("SELECT COUNT(review_id) AS total FROM " . DB_PREFIX . "review WHERE customer_id = '" . (int)$this->customer->getId() . "'");
		return $query->row['total'];
	}

	public function getTotalReviewsAwaitingApproval() {
		$query = $this->db->query("SELECT COUNT(review_id) AS total FROM " . DB_PREFIX . "review WHERE status = '0' AND customer_id = '" . (int)$this->customer->getId() . "'");
		return $query->row['total'];
	}

	// Discussions
	public function getTotalQuestions() {
		$query = $this->db->query("SELECT COUNT(question_id) AS total FROM " . DB_PREFIX . "question WHERE customer_id = '" . (int)$this->customer->getId() . "'");
		return $query->row['total'];
	}

	public function getTotalQuestionsAwaitingApproval() {
		$query = $this->db->query("SELECT COUNT(question_id) AS total FROM " . DB_PREFIX . "question WHERE status = '0' AND customer_id = '" . (int)$this->customer->getId() . "'");
		return $query->row['total'];
	}

	// Orders
	public function getTotalOrders() {
		$query = $this->db->query("SELECT COUNT(order_id) AS total FROM " . DB_PREFIX . "order WHERE customer_id = '" . (int)$this->customer->getId() . "' AND order_status_id > '0'");
		return $query->row['total'];
	}

	public function getTotalOrderProductsByOrderId($order_id) {
		$query = $this->db->query("SELECT COUNT(order_product_id) AS total FROM " . DB_PREFIX . "order_product WHERE order_id = '" . (int)$order_id . "'");
		return $query->row['total'];
	}

	public function getTotalOrderVouchersByOrderId($order_id) {
		$query = $this->db->query("SELECT COUNT(order_voucher_id) AS total FROM " . DB_PREFIX . "order_voucher WHERE order_id = '" . (int)$order_id . "'");
		return $query->row['total'];
	}

	public function getTotalOrderTotals($order_id) {
		$query = $this->db->query("SELECT SUM(value) AS total FROM " . DB_PREFIX . "order_total WHERE order_id = '" . (int)$order_id . "' ORDER BY sort_order");
		return $query->rows;
	}

	public function getTotalOrderHistories($order_id) {
		$query = $this->db->query("
			SELECT COUNT(order_history_id) AS total
			FROM " . DB_PREFIX . "order_history oh
			LEFT JOIN " . DB_PREFIX . "order_status os ON oh.order_status_id = os.order_status_id
			WHERE oh.order_id = '" . (int)$order_id . "'
			AND oh.notify = '1'
			AND os.language_id = '" . (int)$this->config->get('config_language_id') . "'
			ORDER BY oh.date_added
		");

		return $query->rows;
	}

}

