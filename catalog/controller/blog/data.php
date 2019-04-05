<?php
class ControllerBlogData extends Controller {
	private $cache_expires = 60 * 60 * 24 * 30; // 1 month

	protected function index() {
        return false;
    }

    protected function info($data) {
		$this->getData($data, true);

		$this->setOutput($data);
	}

	protected function complete($data) {
		$this->getData($data, false);

		$this->setOutput($data);
	}

	protected function list($articles) {
        if (empty($articles)) {
            return array();
        }

		$customer_group_id = $this->customer->isLogged() ? $this->customer->getCustomerGroupId() : $this->config->get('config_customer_group_id');

		$this->load->language('blog/common');

		$this->load->model('tool/image');

		$this->data['text_author'] = $this->language->get('text_author');
		$this->data['text_viewed'] = $this->language->get('text_viewed');
		$this->data['text_published'] = $this->language->get('text_published');
		$this->data['text_category'] = $this->language->get('text_category');
		$this->data['text_loading'] = $this->language->get('text_loading');
		$this->data['button_read_more'] = $this->language->get('button_read_more');

		$this->data['display_views'] = false;

		$article_data = array();

		foreach ($articles as $article) {
			$this->getMinData($article, $customer_group_id);
			$article_data[$article['blog_article_id']] = $article;
		}

		$this->data['articles'] = $article_data;

		$this->template = 'template/blog/articles.tpl';

		$this->render();
	}

    protected function getData(&$data, $min = true) {
		if (!$data) {
			return array();
		}

		$this->load->language('blog/common');

		$this->load->model('tool/image');

		$customer_group_id = $this->customer->isLogged() ? $this->customer->getCustomerGroupId() : $this->config->get('config_customer_group_id');

		if ($min) {
			$this->getMinData($data, $customer_group_id);
		} else {
			$this->getMoreData($data, $customer_group_id);
		}
	}

    protected function getMinData(&$data, $customer_group_id, $cache = true) {
        $article_data = !$cache ? false : $this->cache->get('blog_' . (int)$data['blog_article_id'] . '.data.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id') . '.' . (int)$customer_group_id);

        if ($article_data === false) {
			$limit_char = $this->config->get('blog_limit_char') ?: 150;

            $short_description = remove_links(preg_replace('/\s+/', ' ', strip_tags_decode($data['meta_description'])));

            if (utf8_strlen($short_description) > $limit_char) {
                $short_description = utf8_substr($short_description, 0, $limit_char) . $this->language->get('text_ellipses');
            }

			$categories = array();

			$article_categories = $this->model_blog_article->getArticleCategories($data['blog_article_id']);

			foreach ($article_categories as $category) {
				$categories[] = array(
					'blog_category_id'	=> $category['blog_category_id'],
					'path_blog'			=> $category['path_blog'],
					'name'        		=> $category['name'],
					'image'				=> $category['image'],
					// 'article_count'		=> $category['article_count'],
					'href'        		=> $this->url->link('blog/category', 'path_blog=' . $category['path_blog'])
				);
			}

			$article_data = array(
				'blog_article_id'   => $data['blog_article_id'],
				'thumb'             => $data['image'] ? $this->model_tool_image->resize($data['image'], $this->config->get('blog_image_article_width'), $this->config->get('blog_image_article_height'), 'autocrop') : false,
				'name'              => html_entity_decode($data['name'], ENT_QUOTES, 'UTF-8'),
				'date_published'    => date($this->language->get('date_format_long'), strtotime($data['date_available'])),
				'day'               => date('d', strtotime($data['date_available'])),
				'month'             => date('M', strtotime($data['date_available'])),
				'year'              => date('Y', strtotime($data['date_available'])),
				'member_id'       	=> $data['member_id'],
				'author_name'       => html_entity_decode($data['author_name'], ENT_QUOTES, 'UTF-8'),
				'author_search'     => $this->url->link('blog/search', 'author=' . $data['member_id']),
				'author_href'       => $this->url->link('product/member/info', 'member_id=' . $data['member_id']),
				'short_description' => $short_description,
				'categories'		=> $categories,
				'viewed'            => $data['viewed'],
				'href'              => $this->url->link('blog/article', 'blog_article_id=' . $data['blog_article_id'])
			);

			if ($cache) {
				$this->cache->set('blog_' . (int)$data['blog_article_id'] . '.data.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id') . '.' . (int)$customer_group_id, $article_data, $this->cache_expires);
			}
        }

		$data = array_merge($data, $article_data);

		return $article_data;
    }

	protected function getMoreData(&$data, $customer_group_id, $cache = true) {
		$article_data = !$cache ? false : $this->cache->get('blog_' . (int)$data['blog_article_id'] . '.data.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id') . '.' . (int)$customer_group_id);

		if ($article_data === false) {
			$article_data = $this->getMinData($data, $customer_group_id, false);
		}

		if (!isset($article_data['popup'])) {
			// keyword tags
			$tags = array();

            if ($data['tag']) {
                $article_tags = explode(',', $data['tag']);

                foreach ($article_tags as $tag) {
                    $tags[] = array(
                        'tag' => strtolower(trim($tag)),
                        'href' => $this->url->link('blog/search', 'tag=' . strtolower(trim($tag)))
                    );
                }
            }

			// images
            $images = array();

            $article_images = $this->model_blog_article->getArticleImage($data['blog_article_id']);

            foreach ($article_images as $article_image) {
				$image = $this->model_tool_image->resize($article_image['image'], $this->config->get('blog_image_additional_width'), $this->config->get('blog_image_additional_height'), 'autocrop');
				$popup = $this->model_tool_image->resize($article_image['image'], $this->config->get('blog_image_popup_width'), $this->config->get('blog_image_popup_height'), 'fw');

                $images[] = array(
                    'image' => $image,
                    'popup' => $popup
                );
            }

			if ($data['image'] && is_file(DIR_IMAGE . $data['image'])) {
				$image = $this->model_tool_image->resize($data['image'], $this->config->get('blog_image_thumb_width'), $this->config->get('blog_image_thumb_height'), 'autocrop');
				$popup = $this->model_tool_image->resize($data['image'], $this->config->get('blog_image_popup_width'), $this->config->get('blog_image_popup_height'), 'fw');
			} else {
				$image = false;
				$popup = false;
			}

			if ($data['author_image'] && is_file(DIR_IMAGE . $data['author_image'])) {
				$author_image = $this->model_tool_image->resize($data['author_image'], 40, 40, 'autocrop');
			} else {
				$author_image = false;
			}

			// related articles
            $related = array();

            $related_articles = $this->model_blog_article->getRelatedArticle($data['blog_article_id']);

            $limit_char = 50;

            foreach ($related_articles as $related_article) {
                $related_info = $this->model_blog_article->getBlogArticle($related_article['related_id']);

                if ($related_info) {
                    $related_image = $this->model_tool_image->resize($related_info['image'], $this->config->get('blog_image_related_width'), $this->config->get('blog_image_related_height'), 'autocrop');

					$short_desciption = remove_links(preg_replace('/\s+/', ' ', strip_tags_decode($related_info['meta_description'])));

		            if (utf8_strlen($short_desciption) > $limit_char) {
		                $short_desciption = utf8_substr($short_desciption, 0, $limit_char) . $this->language->get('text_ellipses');
		            }

                    $related[] = array(
                        'blog_article_id'	=> $related_info['blog_article_id'],
                        'thumb' 			=> $related_image,
                        'name' 				=> $related_info['name'],
                        'short_description' => $short_desciption,
                        'href' 				=> $this->url->link('blog/article', 'blog_article_id=' . $related_info['blog_article_id'])
                    );
                }
            }

			// append more data
			$article_data = array_merge($article_data, array(
				'image'				=> $image,
				'popup'				=> $popup,
				'author_image'		=> $author_image,
				'description'		=> html_entity_decode($data['description'], ENT_QUOTES, 'UTF-8'),
				'meta_description'  => sprintf($this->language->get('meta_description_prefix_author'), $data['meta_description'], $data['author_name']),
				'meta_keyword'		=> $data['meta_keyword'],
				'tags'				=> $tags,
				'article_images'	=> $images,
				'related_articles'	=> $related,
				'date_added'        => date($this->language->get('date_format_long'), strtotime($data['date_added'])),
				'date_modified'     => date($this->language->get('date_format_long'), strtotime($data['date_modified']))
			));

			// update cache with appended data
			if ($cache) {
				$this->cache->set('blog_' . (int)$data['blog_article_id'] . '.data.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id') . '.' . (int)$customer_group_id, $article_data, $this->cache_expires);
			}
		}

		$data = array_merge($data, $article_data);

		return $article_data;
	}

}
