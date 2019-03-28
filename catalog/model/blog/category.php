<?php
class ModelBlogCategory extends Model {
	private $cache_expires = 60 * 60 * 24 * 30; // 1 month cache expiration

	public function getBlogCategory($blog_category_id) {
		$category_data = $this->cache->get('blog.category.' . (int)$blog_category_id . '.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id'));

		if ($category_data === false) {
			$sql = "
				SELECT DISTINCT c.*
				, cd.*
				, GROUP_CONCAT(DISTINCT cp.path_id ORDER BY cp.level SEPARATOR '_') AS path_blog
				, (SELECT COUNT(a.blog_article_id) AS total
                    FROM " . DB_PREFIX . "blog_article a
                    INNER JOIN " . DB_PREFIX . "blog_article_to_store a2s ON (a.blog_article_id = a2s.blog_article_id)
                        AND a2s.store_id='" . (int)$this->config->get('config_store_id') . "'
					LEFT JOIN " . DB_PREFIX . "blog_article_to_category a2c ON (a.blog_article_id = a2c.blog_article_id)
					WHERE a2c.blog_category_id = c.blog_category_id
					AND a.status = '1'
					AND a.approved = '1'
					AND a.date_available <= NOW()) AS article_count
                , (SELECT keyword
                    FROM " . DB_PREFIX . "url_alias
                    WHERE query = 'blog_category_id=" . (int)$blog_category_id . "
                    LIMIT 1') AS keyword
                FROM " . DB_PREFIX . "blog_category c
				LEFT JOIN " . DB_PREFIX . "blog_category_path cp ON (c.blog_category_id = cp.blog_category_id)
                LEFT JOIN " . DB_PREFIX . "blog_category_description cd ON (c.blog_category_id = cd.blog_category_id)
                    AND cd.language_id = '" . (int)$this->config->get('config_language_id') . "'
                LEFT JOIN " . DB_PREFIX . "blog_category_to_store cs2 ON (c.blog_category_id = cs2.blog_category_id)
                    AND cs2.store_id ='" . (int)$this->config->get('config_store_id') . "'
                WHERE c.blog_category_id = '" . (int)$blog_category_id . "'
                AND c.status = 1
				GROUP BY cp.blog_category_id
			";

			$query = $this->db->query($sql);

			$category_data = $query->row;

			$this->cache->set('blog.category.' . (int)$blog_category_id . '.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id'), $category_data, $this->cache_expires);
		}

		return $category_data;
	}

	public function getBlogCategories($parent_id = 0) {
		$category_data = $this->cache->get('blog.category.children.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id') . '.' . (int)$parent_id);

		if ($category_data === false) {
			$category_data = array();

			$sql = "
				SELECT c.blog_category_id
				FROM " . DB_PREFIX . "blog_category c
				LEFT JOIN " . DB_PREFIX . "blog_category_description cd ON (c.blog_category_id = cd.blog_category_id)
					AND cd.language_id = '" . (int)$this->config->get('config_language_id') . "'
				LEFT JOIN " . DB_PREFIX . "blog_category_to_store c2s ON (c.blog_category_id = c2s.blog_category_id)
					AND c2s.store_id = '" . (int)$this->config->get('config_store_id') . "'
				WHERE c.parent_id = '" . (int)$parent_id . "'
				AND c.status = '1'
				ORDER BY c.sort_order, LCASE(cd.name)
			";

			$query = $this->db->query($sql);

			foreach ($query->rows as $result) {
				$category_data[] = $this->getBlogCategory($result['blog_category_id']);
			}

			$this->cache->set('blog.category.children.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id') . '.' . (int)$parent_id, $category_data, $this->cache_expires);
		}

		return $category_data;
	}

	public function getAllBlogCategories($data) {
		$cache = md5(http_build_query($data));
		$all_category_data = $this->cache->get('blog.category.all.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id') . '.' . $cache);

		if ($all_category_data === false) {
			$all_category_data = array();

			$sql = "
				SELECT cp.blog_category_id AS blog_category_id
				, cd2.name AS name
				, c1.status
				, c1.image
				, c1.parent_id
				, c1.sort_order
				, cp.level
				, (SELECT COUNT(a.blog_article_id) AS total
                    FROM " . DB_PREFIX . "blog_article a
                    INNER JOIN " . DB_PREFIX . "blog_article_to_store a2s ON (a.blog_article_id = a2s.blog_article_id)
                        AND a2s.store_id='" . (int)$this->config->get('config_store_id') . "'
					LEFT JOIN " . DB_PREFIX . "blog_article_to_category a2c ON (a.blog_article_id = a2c.blog_article_id)
					WHERE a2c.blog_category_id = c1.blog_category_id
					AND a.status = '1'
					AND a.approved = '1'
					AND a.date_available <= NOW()) AS article_count
				, GROUP_CONCAT(DISTINCT cp.path_id ORDER BY cp.level SEPARATOR '_') AS path_blog
				, GROUP_CONCAT(cd1.name ORDER BY cp.level SEPARATOR '" . $this->language->get('text_separator') . "') AS path_name
				, GROUP_CONCAT(c2.sort_order ORDER BY cp.level SEPARATOR '" . $this->language->get('text_separator') . "') AS sort_order_path_display
				, GROUP_CONCAT(LPAD(c2.sort_order,10,'0') ORDER BY cp.level) AS sort_order_path
				FROM " . DB_PREFIX . "blog_category_path cp
				LEFT JOIN " . DB_PREFIX . "blog_category c1 ON (cp.blog_category_id = c1.blog_category_id)
				LEFT JOIN " . DB_PREFIX . "blog_category c2 ON (cp.path_id = c2.blog_category_id)
				LEFT JOIN " . DB_PREFIX . "blog_category_description cd1 ON (cp.path_id = cd1.blog_category_id)
					AND cd1.language_id = '" . (int)$this->config->get('config_language_id') . "'
				LEFT JOIN " . DB_PREFIX . "blog_category_description cd2 ON (cp.blog_category_id = cd2.blog_category_id)
					AND cd2.language_id = '" . (int)$this->config->get('config_language_id') . "'
				WHERE 1 = 1
			";

			if (!empty($data['filter_name'])) {
				$sql .= "
					AND cd2.name LIKE '" . $this->db->escape($data['filter_name']) . "%'
				";
			}

			if (!empty($data['filter_status'])) {
				$sql .= "
					AND c1.status = '" . (int)$data['filter_status'] . "'
				";
			} else if (!empty($data['filter_complete'])) {
				$sql .= "
					AND c1.status = '1'
				";
			}

			if (!empty($data['filter_level_max'])) {
				$sql .= "
					AND cp.level <= '" . (int)$data['filter_level_max'] . "'
				";
			}

			$sql .= "
				GROUP BY cp.blog_category_id
			";

			// if (!empty($data['filter_path'])) {
			// 	$sql .= " HAVING path = '" . $this->db->escape($data['filter_path']) . "'";
			// }

			$sort_data = array(
				'name',
				'path_name',
				'sort_order',
				'sort_order_path'
			);

			if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
				$sql .= " ORDER BY " . $data['sort'];
			} else {
				$sql .= " ORDER BY name";
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

			foreach ($query->rows as $result) {
				$category_children = !empty($data['filter_complete']) ? $this->getBlogCategories($result['blog_category_id']) : array();

				$all_category_data[] = array(
	                'blog_category_id' 			=> $result['blog_category_id'],
					'parent_id'   				=> $result['parent_id'],
					'name'        				=> $result['name'],
					'path_blog'        		    => $result['path_blog'],
					'path_name'	  				=> $result['path_name'],
					'image'  	  				=> $result['image'],
					'children'	  				=> $category_children,
					'article_count' 			=> $result['article_count'],
					'sort_order'  				=> $result['sort_order'],
					'sort_order_path'			=> $result['sort_order_path'],
					'sort_order_path_display'	=> $result['sort_order_path_display'],
					'status'  	  				=> $result['status']
				);
			}

			$this->cache->set('blog.category.all.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id') . '.' . $cache, $all_category_data, $this->cache_expires);
		}

		return $all_category_data;
	}
}
