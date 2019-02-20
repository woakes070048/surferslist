<?php
class ModelAccountNotify extends Model {
	public function getMemberNotifications() {
		$query = $this->db->query("
            SELECT email_contact
            , email_discuss
            , email_post
            , email_review
            , email_flag
			FROM " . DB_PREFIX . "customer_notify
			WHERE customer_id = '" . (int)$this->customer->getId() . "'
        ");

		return $query->row;
	}

	public function getMemberNotificationsByMemberId($member_id) {
		$query = $this->db->query("
            SELECT email_contact
            , email_discuss
            , email_post
            , email_review
            , email_flag
			FROM " . DB_PREFIX . "customer_notify
			WHERE customer_id = '" . (int)$member_id . "'
        ");

		return $query->row;
	}

    public function editMemberNotifications($data) {
        $this->db->query("
            UPDATE " . DB_PREFIX . "customer_notify
            SET email_contact = '" . (int)$data['email_contact'] . "'
            , email_post = '" . (int)$data['email_post'] . "'
            , email_discuss = '" . (int)$data['email_discuss'] . "'
            , email_review = '" . (int)$data['email_review'] . "'
            , email_flag = '" . (int)$data['email_flag'] . "'
            , date_modified = NOW()
            WHERE customer_id = '" . (int)$this->customer->getId() . "'
        ");
    }
}
