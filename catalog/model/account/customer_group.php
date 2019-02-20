<?php
class ModelAccountCustomerGroup extends Model {
	private $cache_expires = 60 * 60 * 24 * 30; // 1 month

	public function getCustomerGroup($customer_group_id) {
		$customer_group_data = $this->cache->get('customer.group.' . (int)$customer_group_id . '.' . (int)$this->config->get('config_language_id'));

		if ($customer_group_data === false) {
			$query = $this->db->query("
				SELECT DISTINCT *
				FROM " . DB_PREFIX . "customer_group cg
				LEFT JOIN " . DB_PREFIX . "customer_group_description cgd ON (cg.customer_group_id = cgd.customer_group_id)
				WHERE cg.customer_group_id = '" . (int)$customer_group_id . "'
				AND cgd.language_id = '" . (int)$this->config->get('config_language_id') . "'
			");

			$customer_group_data = $query->row;

			$this->cache->set('customer.group.' . (int)$customer_group_id . '.' . (int)$this->config->get('config_language_id'), $customer_group_data, $this->cache_expires);
		}

		return $customer_group_data;
	}

	public function getCustomerGroups() {
		$customer_groups_data = $this->cache->get('customer.groups.' . (int)$this->config->get('config_language_id'));

		if ($customer_groups_data === false) {
			$query = $this->db->query("
				SELECT *
				, cg.customer_group_id
				FROM " . DB_PREFIX . "customer_group cg
				LEFT JOIN " . DB_PREFIX . "customer_group_description cgd ON (cg.customer_group_id = cgd.customer_group_id)
				WHERE cgd.language_id = '" . (int)$this->config->get('config_language_id') . "'
				ORDER BY cg.sort_order ASC, cgd.name ASC
			");

			$customer_groups_data = $query->rows;

			$this->cache->set('customer.groups.' . (int)$this->config->get('config_language_id'), $customer_groups_data, $this->cache_expires);
		}

		return $customer_groups_data;
	}

	public function getCustomerMemberGroup($member_group_id) {
		$customer_member_group_data = $this->cache->get('customer.member.group.' . (int)$member_group_id);

		if ($customer_member_group_data === false) {
			$query = $this->db->query("
				SELECT *
				FROM " . DB_PREFIX . "customer_member_group cmg
				WHERE cmg.member_group_id = '" . (int)$member_group_id . "'
			");

			$customer_member_group_data = $query->row;

			$this->cache->set('customer.member.group.' . (int)$member_group_id, $customer_member_group_data, $this->cache_expires);
		}

		return $customer_member_group_data;
	}

	public function getCustomerMemberGroups($customer_group_id = 0, $data = array()) {
		$cache = md5(http_build_query($data));

		$customer_member_groups_data = $this->cache->get('customer.member.group.' . (int)$customer_group_id . '.' . $cache);

		if ($customer_member_groups_data === false) {
			$sql = "
				SELECT *
				FROM " . DB_PREFIX . "customer_member_group cmg
			";

			if ($customer_group_id) {
				$sql .= "
					WHERE cmg.customer_group_id = '" . (int)$customer_group_id . "'
				";

				if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
					$sql .= "
						AND cmg.status = '" . (int)$data['filter_status'] . "'
					";
				}
			} else if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
				$sql .= "
					WHERE cmg.status = '" . (int)$data['filter_status'] . "'
				";
			}

			$query = $this->db->query($sql);

			$customer_member_groups_data = $query->rows;

			$this->cache->set('customer.member.group.' . (int)$customer_group_id . $cache, $customer_member_groups_data, $this->cache_expires);
		}

		return $customer_member_groups_data;
	}

	public function getDefaultMemberGroupId($customer_group_id) {
		$query = $this->db->query("
			SELECT DISTINCT member_group_default_id
			FROM " . DB_PREFIX . "customer_group cg
			WHERE cg.customer_group_id = '" . (int)$customer_group_id . "'
		");

		return $query->row['member_group_default_id'];
	}

	public function getCustomerGroupIdByMemberGroupId($member_group_id) {
		$query = $this->db->query("
			SELECT DISTINCT customer_group_id
			FROM " . DB_PREFIX . "customer_member_group cmg
			WHERE cmg.member_group_id = '" . (int)$member_group_id . "'
		");

		return $query->row['customer_group_id'];
	}
}
?>
