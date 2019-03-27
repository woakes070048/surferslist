<?php
class ModelBlogArticle extends Model {
	private $cache_expires = 60 * 60 * 24 * 30; // 1 month cache expiration

    public function getBlogArticle($blog_article_id, $preview = false) {
		if (empty($blog_article_id)) return array();

		$customer_group_id = $this->customer->isLogged() ? $this->customer->getCustomerGroupId() : $this->config->get('config_customer_group_id');

		$article_data = $this->cache->get('blog_' . (int)$blog_article_id . '.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id') . '.' . (int)$customer_group_id);

		if ($preview || $article_data === false) {
            $sql = "
                SELECT DISTINCT a.*
                , ad.*
                , cma.member_account_name
				, cma.member_account_image
                , (SELECT keyword
                    FROM " . DB_PREFIX . "url_alias
                    WHERE query = 'blog_article_id=" . (int)$blog_article_id . "
                    LIMIT 1') AS keyword
                FROM " . DB_PREFIX . "blog_article a
                LEFT JOIN " . DB_PREFIX . "blog_article_description ad ON (a.blog_article_id = ad.blog_article_id)
                    AND ad.language_id = '" . (int)$this->config->get('config_language_id') . "'
                LEFT JOIN " . DB_PREFIX . "blog_article_to_store a2s ON (a.blog_article_id = a2s.blog_article_id)
                    AND a2s.store_id='" . (int)$this->config->get('config_store_id') . "'
                LEFT JOIN " . DB_PREFIX . "customer_member_account cma ON (a.member_id = cma.member_account_id)
                WHERE a.blog_article_id = '" . (int)$blog_article_id . "'
            ";

            if (!$preview) {
				$sql .= "
					AND a.status = '1'
					AND a.approved = '1'
					AND a.date_available <= NOW()
				";
			}

            $query = $this->db->query($sql);

            if ($query->num_rows) {
                $article_data = array(
					'blog_article_id'  => $query->row['blog_article_id'],
					'keyword'		   => $query->row['keyword'],
                    'member_id'		   => $query->row['member_id'],
                    'author_name'      => $query->row['member_account_name'],
                    'author_image'     => $query->row['member_account_image'],
                    'image'		       => $query->row['image'],
					'date_available'   => $query->row['date_available'],
					'date_added'       => $query->row['date_added'],
					'date_modified'    => $query->row['date_modified'],
                    'sort_order'	   => $query->row['sort_order'],
                    'viewed'	       => $query->row['viewed'],
					'name'             => $query->row['name'],
					'description'      => $query->row['description'],
					'meta_description' => $query->row['meta_description'],
					'meta_keyword'     => $query->row['meta_keyword'],
					'tag'              => $query->row['tag']
                );
            } else {
				$article_data = array();
			}

			if (!$preview) {
				$this->cache->set('blog_' . (int)$blog_article_id . '.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id') . '.' . (int)$customer_group_id, $article_data, $this->cache_expires);
			}
		}

        return $article_data;
    }

    public function getBlogArticles($data = array(), $cache_results = true) {
		$search_log = array();
		$cache = md5(http_build_query($data));
		$customer_group_id = $this->customer->isLogged() ? $this->customer->getCustomerGroupId() : $this->config->get('config_customer_group_id');

		$article_data = $cache_results ? $this->cache->get('blog.articles.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id') . '.' . (int)$customer_group_id . '.' . $cache) : false;

		if ($article_data === false) {
			$article_data = array();

			$sql = "
				SELECT a.blog_article_id
			";

			$this->generateGetBlogArticlesJoins($sql, $data);
			$this->generateGetBlogArticlesConditions($sql, $data, $search_log);

			$sql .= "
				GROUP BY a.blog_article_id
			";

			$this->generateGetBlogArticlesSortOrderSql($sql, $data);

			if (isset($data['start']) || isset($data['limit'])) {
				if ($data['start'] < 0) {
					$data['start'] = 0;
				}

				if ($data['limit'] < 1) {
					$data['limit'] = $this->config->get('blog_catalog_limit') ?: 30;
				}

				$sql .= "
					LIMIT " . (int)$data['start'] . "," . (int)$data['limit'] . "
				";
			}

			$query = $this->db->query($sql);

			foreach ($query->rows as $result) {
				$article_data[$result['blog_article_id']] = $this->getBlogArticle($result['blog_article_id']);
			}

			if ($cache_results) {
				$this->cache->set('blog.articles.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id') . '.' . (int)$customer_group_id . '.' . $cache, $article_data, $this->cache_expires);
			}
		}

		// search logging
		if (isset($data['is_search']) && $data['is_search'] === true && $search_log) {
			$search_log[] = 'SEARCH RESULTS: ' . count($article_data);
			$search_log[] = 'SEARCH PARAMS: ' . json_encode(array_filter($data, function ($item) { return $item !== "" && $item !== []; }));

			if ($search_log && ($this->request->checkReferer($this->config->get('config_url')) || $this->request->checkReferer($this->config->get('config_ssl')))) {
				$search_log_file = new Log('search_blog.log');
				$search_log_file->write(array_reduce($search_log, function ($carry, $item) { return $carry . $item . ' | '; }, ''));
				// $search_log_file->write(preg_replace('/[^\S\n]/', ' ', $sql));
			}
		}

		return $article_data;
	}

    public function getTotalBlogArticles($data = array()) {
		$cache = md5(http_build_query($data));

		$article_total = $this->cache->get('blog.total.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id') . '.' . $cache);

		if ($article_total === false) {
			$sql = "
				SELECT COUNT(DISTINCT a.blog_article_id) AS total
			";

			$this->generateGetBlogArticlesJoins($sql, $data);
			$this->generateGetBlogArticlesConditions($sql, $data, $search_log);

			$query = $this->db->query($sql);

			$article_total = $query->row['total'];

			$this->cache->set('blog.total.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id') . '.' . $cache, $article_total, $this->cache_expires);
		}

		return $article_total;
	}

    public function getBlogArticlesIndexes($data, $cache_results = true) {
		if (empty($data)) return array();

		unset($data['start']);
		unset($data['limit']);

		if (!isset($data['sort'])) {
			$data['sort'] = 'a.date_available';
		}

		if (!isset($data['order'])) {
			$data['order'] = (($data['sort'] == 'a.date_available') ? 'DESC' : 'ASC');
		}

		if (!isset($data['filter_sub_category'])) {
			$data['filter_sub_category'] = true;
		}

		$cache = md5(http_build_query($data));

		$blog_indexes = !$cache_results ? false : $this->cache->get('blog.indexes.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id') . '.' . $cache);

		if ($blog_indexes === false) {
			$blog_indexes = array();

			$sql = "
				SELECT a.blog_article_id
				, a.member_id
				, ad.name
				, cma.member_group_id
				, cmg.customer_group_id
				, GROUP_CONCAT(DISTINCT a2c.blog_category_id) AS category_ids
				FROM " . DB_PREFIX . "blog_category_path cp
				LEFT JOIN " . DB_PREFIX . "blog_article_to_category a2c ON (cp.blog_category_id = a2c.blog_category_id)
                LEFT JOIN " . DB_PREFIX . "blog_article a ON (a2c.blog_article_id = a.blog_article_id)
                LEFT JOIN " . DB_PREFIX . "blog_article_description ad ON (a.blog_article_id = ad.blog_article_id)
                    AND ad.language_id = '" . (int)$this->config->get('config_language_id') . "'
                LEFT JOIN " . DB_PREFIX . "blog_article_to_store a2s ON (a.blog_article_id = a2s.blog_article_id)
                    AND a2s.store_id='" . (int)$this->config->get('config_store_id') . "'
                LEFT JOIN " . DB_PREFIX . "customer_member_account cma ON (a.member_id = cma.member_account_id)
				LEFT JOIN " . DB_PREFIX . "customer_member_group cmg ON (cma.member_group_id = cmg.member_group_id)
			";

			$this->generateGetBlogArticlesConditions($sql, $data);

			$sql .= "
				GROUP BY a.blog_article_id
			";

			$this->generateGetBlogArticlesSortOrderSql($sql, $data);

			$query = $this->db->query($sql);

			// $this->log->write(json_encode($data) . "\r\n" . "num_rows: {$query->num_rows}" . "\r\n" . $sql);

			foreach ($query->rows as $result) {
				$blog_indexes[] = array(
					'blog_article_id'  => $result['blog_article_id'],
					'name'             => $result['name'],
					'member_id' 	   => $result['member_id'],
					'member_group_id'  => $result['member_group_id'],
					'customer_group_id' => $result['customer_group_id'],
					'category_ids'	   => explode(',', $result['category_ids']),
					'filter_ids'	   => explode(',', $result['filter_ids'])
				);
			}

			if ($cache_results) {
				$this->cache->set('blog.indexes.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id') . '.' . $cache, $blog_indexes, $this->cache_expires);
			}
		}

		return $blog_indexes;
	}

    private function generateGetBlogArticlesJoins(&$sql, $data) {
		if (!empty($data['filter_category_id']) || !empty($data['filter_name'])) {
			if (!empty($data['filter_sub_category']) || !empty($data['filter_name'])) {
				$sql .= "
					FROM " . DB_PREFIX . "blog_category_path cp
					LEFT JOIN " . DB_PREFIX . "blog_article_to_category a2c ON (cp.blog_category_id = a2c.blog_category_id)
				";
			} else {
				$sql .= "
					FROM " . DB_PREFIX . "blog_article_to_category a2c
				";
			}

            $sql .= "
                LEFT JOIN " . DB_PREFIX . "blog_article a ON (a2c.blog_article_id = a.blog_article_id)
            ";
		} else {
			$sql .= "
				FROM " . DB_PREFIX . "blog_article a
			";
		}

		$sql .= "
			LEFT JOIN " . DB_PREFIX . "blog_article_description ad ON (a.blog_article_id = ad.blog_article_id)
				AND ad.language_id = '" . (int)$this->config->get('config_language_id') . "'
		";

		if (!empty($data['filter_member_group']) || !empty($data['filter_name'])) {
			$sql .= "
				LEFT JOIN " . DB_PREFIX . "customer_member_account cma ON (a.member_id = cma.member_account_id)
			";

			if (!empty($data['filter_member_group'])) {
				$sql .= "
					LEFT JOIN " . DB_PREFIX . "customer_member_group cmg ON (cma.member_group_id = cmg.member_group_id)
				";
			}
		}

		$sql .= "
			LEFT JOIN " . DB_PREFIX . "blog_article_to_store a2s ON (a.blog_article_id = a2s.blog_article_id)
			  	AND a2s.store_id = '" . (int)$this->config->get('config_store_id') . "'
		";
	}

	private function generateGetBlogArticlesConditions(&$sql, $data, &$search_log = null) {
		$sql .= "
			WHERE a.status = '1'
			AND a.approved = '1'
			AND a.date_available <= NOW()
		";

		if (!empty($data['filter_category_id'])) {
			if (!empty($data['filter_sub_category'])) {
				$sql .= "
					AND cp.path_id = '" . (int)$data['filter_category_id'] . "'
				";
			} else {
				$sql .= "
					AND a2c.blog_category_id = '" . (int)$data['filter_category_id'] . "'
				";
			}
		}

		if (!empty($data['filter_articles']) && !is_array($data['filter_articles'])) {
			$implode = array();
			$filter_articles = explode(',', $data['filter_articles']);

			foreach ($filter_articles as $blog_article_id) {
				$implode[] = (int)$blog_article_id;
			}

			$sql .= "
				AND a.blog_article_id NOT IN (" . implode(',', $implode) . ")
			";
		}

		// search
		if (!empty($data['filter_name']) || !empty($data['filter_tag'])) {
			$keywords = array();
			$keywords_quoted = array();
			$implode_title = array();
			$filter_phrase = '';
			$filter_tag = '';

			// cleanup search keywords and phrase
			if (!empty($data['filter_name']) && !is_array($data['filter_name'])) {
				$filter_phrase =  utf8_strtolower(strip_tags_decode($data['filter_name'])); // utf8_strtolower(htmlspecialchars_decode($data['filter_name'], ENT_NOQUOTES));

				if ($filter_phrase) {
					if ($search_log !== null) $search_log[] = "SEARCH PHRASE: `{$filter_phrase}`";

					// check for double quote phrases to treat as single keyword
					if (preg_match('/"([^"]+)"/', htmlspecialchars_decode($filter_phrase, ENT_QUOTES), $matches, PREG_OFFSET_CAPTURE)) {
						// add a keyword for each double-quoted phrase found
						foreach ($matches as $match) {
							$double_quote_keyword = $match[0];

							if (!in_array($double_quote_keyword, $keywords_quoted)) {
								$keywords_quoted[] = $double_quote_keyword;

								if ($search_log !== null) $search_log[] = "SEARCH QUOTED: \"{$double_quote_keyword}\"";
							}
						}

						// strip out all the double-quoted keywords and double quotes
						$filter_phrase = trim(str_replace(array_merge($keywords_quoted, array('&quot;', '"')), '', $filter_phrase));
						// !important: $filter_phrase could end up empty string here
					}

					$keywords = $keywords_quoted;

					if ($filter_phrase) {
						// add a keyword (without double quotes) for the entire search phrase
						$keywords = array_merge($keywords, array($filter_phrase));

						// contains space
						if (strpos($filter_phrase, ' ') !== false) {
							// add a keyword for each word in the search phrase
							$keywords = array_merge($keywords, explode(' ', $filter_phrase));
						}
					}

					if ($search_log !== null && $keywords) $search_log[] = "SEARCH FOR KEYWORD(S): " . json_encode($keywords) . "";
				}
			}

			// tag (should be single word)
			if (!empty($data['filter_tag']) && !is_array($data['filter_tag'])) {
				$filter_tag = utf8_strtolower(strip_non_alphanumeric($data['filter_tag'], true));

				if ($search_log !== null && $filter_tag) $search_log[] = "SEARCH TAG: `{$filter_tag}`";
			}

			// include tag selected with search
			if ($filter_tag && $keywords) {
				$sql .= "
					AND LCASE(ad.tag) LIKE '%" . $this->db->escape($filter_tag) . "%'
				";
			}

			// start large `AND` grouping for filter_name and filter_tag
			if ($keywords || $filter_tag) {
				$sql .= "
					AND (
				";
			}

			// tag without search
			if ($filter_tag && !$keywords && !$filter_phrase) {
				$sql .= "
					LCASE(ad.tag) LIKE '%" . $this->db->escape($filter_tag) . "%'
				";
			}

			if ($keywords) {
				foreach ($keywords as $keyword) {
					$keyword_singular = (utf8_substr($keyword, -1) == 's' && utf8_substr($keyword, -3) != 'ies')
						? utf8_substr($keyword, 0, -1)
						: $keyword;

					if (strpos($filter_phrase, ' ') === false || $keyword != $filter_phrase) {
						$implode_title[] = "
							LCASE(ad.name) LIKE '%" . $this->db->escape($keyword_singular) . "%'
						";
					}
				}
			}

			if ($implode_title) {
				$sql .= "
					(" . implode(" OR ", $implode_title) . ")
				";
			}

			if ($filter_phrase) {
				$sql .= "
					OR REPLACE(LCASE(cma.member_account_name), ' ', '') LIKE '%" . $this->db->escape(str_replace(' ', '', $filter_phrase)) . "%'
				";
			}

			if ($filter_phrase && !empty($data['filter_description'])) {
				$sql .= "
					OR LCASE(ad.description) LIKE '%" . $this->db->escape($filter_phrase) . "%'
				";
			}

			// end large `AND` grouping for filter_name and filter_tag
			if ($keywords || $filter_tag) {
				$sql .= "
					)
				";
			}
		}

		if (!empty($data['filter_member_account_id'])) {
			$sql .= "
				AND a.member_id = '" . (int)$data['filter_member_account_id'] . "'
			";
		} else if (isset($data['filter_member_account_id']) && is_null($data['filter_member_account_id'])) {
			$sql .= "
				AND a.member_id IS NULL
			";
		} else if (isset($data['filter_member_exists'])) {
			$sql .= "
				AND a.member_id IS NOT NULL
			";
		}

		if (!empty($data['filter_member_group']) && is_array($data['filter_member_group'])) {
			$sql .= "
				AND cmg.customer_group_id IN (" . implode(',', $data['filter_member_group']) . ")
			";
		}
	}

	private function generateGetBlogArticlesSortOrderSql(&$sql, $data) {
		$sort_data = array(
			'ad.name',
			'a.sort_order',
			'a.date_available'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			switch ($data['sort']) {
				case 'a.sort_order':
					$sql .= "
						ORDER BY YEAR(a.date_available) DESC
						, IF(cmg.customer_group_id = 0, 0, 1) DESC
						, a.date_available DESC
					";
					break;

				case 'ad.name':
					$sql .= "
						ORDER BY LCASE(" . $data['sort'] . ")
					";
					break;

				default:
					$sql .= "
						ORDER BY " . $data['sort'] . "
					";
					break;

			}
		} else {
			$sql .= "
				ORDER BY a.sort_order
			";
		}

		if (!isset($data['sort']) || ($data['sort'] != 'a.sort_order')) {
			if (isset($data['order']) && ($data['order'] == 'DESC')) {
				$sql .= "
					DESC, LCASE(ad.name) DESC
				";
			} else {
				$sql .= "
					ASC, LCASE(ad.name) ASC
				";
			}
		}
	}

    public function getRelatedProduct($blog_article_id) {
        if (empty($blog_article_id)) return array();

        $this->load->model('catalog/product');

        $product_data = array();

        $query = $this->db->query("
            SELECT *
            FROM " . DB_PREFIX . "blog_article_to_product a2p
            LEFT JOIN " . DB_PREFIX . "product p ON (a2p.product_id = p.product_id)
            LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id)
                AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "'
            WHERE a2p.blog_article_id = '" . (int)$blog_article_id . "'
            AND p.status = '1'
            AND p.member_approved = '1'
            AND p.date_available <= NOW()
            AND p.date_expiration >= NOW()
        ");

        foreach ($query->rows as $result) {
            if (!array_key_exists($result['product_id'], $product_data)) {
                $product_data[$result['product_id']] = $this->model_catalog_product->getProduct($result['product_id']);
            }
        }

        return $product_data;
    }

    public function getRelatedProductIds($blog_article_id) {
        if (empty($blog_article_id)) return array();

        $query = $this->db->query("
            SELECT product_id
            FROM " . DB_PREFIX . "blog_article_to_product
            WHERE blog_article_id = '" . (int)$blog_article_id . "'
        ");

        return $query->rows;
    }

    public function getRelatedArticle($blog_article_id) {
        $query = $this->db->query("
            SELECT related_id
            FROM " . DB_PREFIX . "blog_article_related
            WHERE blog_article_id = '" . (int)$blog_article_id . "'
        ");

        return $query->rows;
    }

    public function getArticleImage($blog_article_id) {
        $query = $this->db->query("
            SELECT *
            FROM " . DB_PREFIX . "blog_article_image
            WHERE blog_article_id = '" . (int)$blog_article_id . "'
            ORDER BY sort_order ASC
        ");

        return $query->rows;
    }

    public function getNameArticle($blog_article_id) {
        $query = $this->db->query("
            SELECT name
            FROM " . DB_PREFIX . "blog_article_description
            WHERE blog_article_id = '" . (int)$blog_article_id . "'
            AND language_id ='" . (int)$this->config->get('config_language_id') . "'
        ");

        return $query->row['name'];
    }

    public function updateViewed($blog_article_id) {
        $this->db->query("
            UPDATE " . DB_PREFIX . "blog_article
            SET viewed = (viewed + 1)
            WHERE blog_article_id = '" . (int)$blog_article_id . "'
        ");
    }
}
