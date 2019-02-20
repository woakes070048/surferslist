<?php
class ModelCatalogReview extends Model {
	use Contact;

	public function addReview($member_id, $data) {
		$this->db->query("
			INSERT INTO " . DB_PREFIX . "review
			SET member_id = '" . (int)$member_id . "'
			, customer_id = '" . (int)$this->customer->getId() . "'
			, order_product_id = '" . (isset($data['order_product_id']) ? (int)$data['order_product_id'] : 0) . "'
			, text = '" . $this->db->escape($data['text']) . "'
			, rating = '" . (int)$data['rating'] . "'
			, date_added = NOW()
		");

		// email notification
		if ($this->db->countAffected()) {
			$this->load->language('mail/member');
			$this->load->model('catalog/member');

			$bcc = array();

			$member_info = $this->model_catalog_member->getMember($member_id);

			if ($this->config->get('member_email_customers')) {
				// bcc profile owner
				if (!empty($member_info['email'])) {
					$bcc[] = $member_info['email'];
				}

				// copy user submitting the review, unless profile activated and email notification disabled
				if (!$this->customer->hasProfile() || $this->customer->getEmailNotifySetting('email_review')) {
					$bcc[] = $this->customer->getEmail();
				}
			}

			$mail_sent = $this->sendEmail(array(
				'to' 		=> $this->config->get('config_email'),
				'from' 		=> $this->config->get('config_email_noreply'),
				'sender' 	=> $this->config->get('config_name'),
				'subject' 	=> sprintf(
					$this->language->get('text_review_mail_subject'),
					$this->config->get('config_name'),
					$member_info['member_account_name']
				),
				'message' 	=> sprintf(
					$this->language->get('text_review_mail'),
					$member_info['member_account_name'],
					$this->url->link('product/member/info', 'member_id=' . (int)$member_id, 'SSL'),
					$this->customer->getProfileName(),
					$this->customer->getProfileUrl(),
					(int)$data['rating'],
					strip_tags_decode(html_entity_decode($data['text'], ENT_QUOTES, 'UTF-8'))
				),
				'bcc' 		=> $bcc,
				'reply' 	=> $this->config->get('config_email_noreply'),
				'admin'		=> true
			));
        }

		$this->cache->delete('member');
	}

	public function getReviewsByMemberId($member_id, $start = 0, $limit = 20) {
		if ($start < 0) {
			$start = 0;
		}

		if ($limit < 1) {
			$limit = 20;
		}

		$query = $this->db->query("
			SELECT r.review_id
			, r.rating
			, r.text
			, m.member_account_id AS author_member_id
			, m.customer_id AS author_customer_id
			, m.member_account_name AS author_member_name
			, m.member_account_image AS author_member_image
			, r.date_added
			FROM " . DB_PREFIX . "review r
			LEFT JOIN " . DB_PREFIX . "customer_member_account m ON (r.customer_id = m.customer_id)
				AND m.customer_id <> 0
			WHERE r.member_id = '" . (int)$member_id . "'
			AND r.status = '1'
			ORDER BY r.date_added DESC
			LIMIT " . (int)$start . "," . (int)$limit
		);

		return $query->rows;
	}

	public function getTotalReviewsByMemberId($member_id) {
		$query = $this->db->query("
			SELECT COUNT(review_id) AS total
			FROM " . DB_PREFIX . "review
			WHERE member_id = '" . (int)$member_id . "'
			AND status = '1'
		");

		return $query->row['total'];
	}

	public function hasSubmittedReviewForMemberId($member_id) {
		$query = $this->db->query("
			SELECT COUNT(review_id) AS total
			FROM " . DB_PREFIX . "review
			WHERE member_id = '" . (int)$member_id . "'
			AND customer_id = '" . (int)$this->customer->getId() . "'
		");

		return $query->row['total'] ? true : false;
	}

}
?>
