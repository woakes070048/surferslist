<?php
class ModelDesignBanner extends Model {
	private $cache_expires = 60 * 60 * 24 * 7; // 1 week

	public function getBanner($banner_id) {
		$banner_image_data = $this->cache->get('banner.image.' . (int)$banner_id . '.' . (int)$this->config->get('config_language_id'));

		if ($banner_image_data === false) {
			$query = $this->db->query("
				SELECT *
				FROM " . DB_PREFIX . "banner_image bi
				LEFT JOIN " . DB_PREFIX . "banner_image_description bid ON (bi.banner_image_id  = bid.banner_image_id)
				WHERE bi.banner_id = '" . (int)$banner_id . "'
				AND bid.language_id = '" . (int)$this->config->get('config_language_id') . "'
				ORDER BY sort_order ASC, title ASC
			");

			$banner_image_data = $query->rows;

			$this->cache->set('banner.image.' . (int)$banner_id . '.' . (int)$this->config->get('config_language_id'), $banner_image_data, $this->cache_expires);
		}

		return $banner_image_data;
	}

}
?>
