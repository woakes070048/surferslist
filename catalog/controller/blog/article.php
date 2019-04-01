<?php
class ControllerBlogArticle extends Controller {
    private $error = array();

    public function index() {
        $this->data = array_merge(
            $this->load->language('blog/common'),
            $this->load->language('blog/article')
        );

        $blog_article_id = isset($this->request->get['blog_article_id']) ? (int)$this->request->get['blog_article_id'] : 0;

        if (isset($this->request->get['preview_article']) && isset($this->session->data['customer_token']) && $this->request->get['preview_article'] == $this->session->data['customer_token']) {
			$preview_article = true;
		} else {
			$preview_article = false;
		}

        $this->load->model('blog/category');
        $this->load->model('blog/article');

        $blog_article_info = $this->model_blog_article->getBlogArticle($blog_article_id, $preview_article);

        $query_params = array(
			'search',
			'tag',
			'description',
			'author',
			'blog_category_id',
			'sort',
			'order',
			'limit',
            'page'
		);

		$this->setQueryParams($query_params);

		$url = $this->getQueryString();

		$this->addBreadcrumb($this->language->get('text_home'), $this->url->link('common/home'));
        $this->addBreadcrumb($this->language->get('text_blog'), $this->url->link('blog/home'));

        if (isset($this->request->get['path_blog'])) {
            $path_blog = '';

            $parts = explode('_', (string)$this->request->get['path_blog']);

            $blog_category_id = (int)array_pop($parts);

            foreach ($parts as $path_blog_id) {
                if (!$path_blog) {
                    $path_blog = $path_blog_id;
                } else {
                    $path_blog .= '_' . $path_blog_id;
                }

                $blog_category_info = $this->model_blog_category->getBlogCategory($path_blog_id);

                if ($blog_category_info) {
                    $this->addBreadcrumb($blog_category_info['name'], $this->url->link('blog/category', 'path_blog=' . $path_blog));
                }
            }

            $blog_category_info = $this->model_blog_category->getBlogCategory($blog_category_id);

            if ($blog_category_info) {
                $this->addBreadcrumb($blog_category_info['name'], $this->url->link('blog/category', 'path_blog=' . $this->request->get['path_blog']));
            }
        }

        if (isset($this->request->get['search']) || isset($this->request->get['tag']) || isset($this->request->get['author'])) {
            $this->addBreadcrumb($this->language->get('text_search'), $this->url->link('blog/search', $url));
        }

        if (($blog_article_info && $blog_article_info['status'] == '1') || ($blog_article_info && $preview_article)) {
            $this->addBreadcrumb($blog_article_info['name'], $this->url->link('blog/article', 'blog_article_id=' . $blog_article_id) . $url);

            $this->load->model('catalog/product');
            $this->load->model('tool/image');

            $heading_title = $blog_article_info['name'];

            if ($preview_article) {
				$heading_title = $this->language->get('button_preview') . ': ' . $heading_title;
			}

            $article_data = $this->getChild('blog/data/complete', $blog_article_info);

			$this->data = array_merge($this->data, $article_data);

            $this->data['heading_title'] = $heading_title;

            $this->data['related_products'] = $this->getChild('product/data/list', array(
    			'products' => $this->model_blog_article->getRelatedProduct($blog_article_id),
    			'more' => ''
            ));

            $this->data['sidebar'] = $this->getChild('blog/sidebar', array(
                'query_params'  => $query_params,
                'route'         => 'blog/article',
                'path'          => '',
                'filter'        => array()
            ));

            // prev/next
			$article_prev = array();
			$article_next = array();
			$filter_category_id = 0;
			$filter_member_account_id = 0;
			$max_prevnext_length = 35;
			$nav_cols = 1;
			$more_url = '';
			$more_title = '';
			$prev_url = '';
			$prev_title = '';
			$next_url = '';
			$next_title = '';
			$back_url = '';
			$back_title = $this->language->get('text_back');
			$url = '';

			$category_info = end($article_data['categories']);

			if (!$preview_article && $category_info) {
				$filter_category_id = $category_info['blog_category_id'];
				$url .= '&path_blog=' . $category_info['path_blog'];

				$more_url = $this->url->link('blog/category', $url);
				$more_title = utf8_strlen($category_info['name']) > $max_prevnext_length ? utf8_substr($category_info['name'], 0, $max_prevnext_length) . $this->language->get('text_ellipses') : $category_info['name'];
			} else if ($article_data['member_id']) {
				$filter_member_account_id = $article_data['member_id'];

				$more_url = $this->url->link('blog/search', 'author=' . $article_data['member_id'] . $url);
				$more_title = utf8_strlen($article_data['author_name']) > $max_prevnext_length ? utf8_substr($article_data['author_name'], 0, $max_prevnext_length) . $this->language->get('text_ellipses') : $article_data['author_name'];
			} else {
				$more_url = $this->url->link('blog/home');
				$more_title = $this->language->get('heading_blog_home');
			}

			$sort = $this->getQueryParam('sort') ?: 'a.sort_order';
			$sort_order = $this->getQueryParam('order') ?: 'DESC';

			$articles = !$preview_article ? $this->model_blog_article->getBlogArticlesIndexes(array(
				// 'filter_category_id'        => $filter_category_id,
				// 'filter_sub_category'       => true,
				// 'filter_member_account_id'  => $filter_member_account_id,
				'sort'                      => $sort,
				'order'                     => $sort_order
			), true) : array();

			// get the index of this listing
			$article_index = key(array_filter($articles, function ($item) use ($article_data) {
				return $item['blog_article_id'] === $article_data['blog_article_id'];
			}));

			if ($article_index !== null && array_key_exists($article_index, $articles)) {
				reset($articles);

				// set array pointer to index position
				while (key($articles) !== $article_index) {
					next($articles);
				}

				// one step fwd, two back
				if ($sort_order == 'ASC') {
					$article_prev = isset($articles[$article_index - 1]) ? $articles[$article_index - 1] : false;
					$article_next = isset($articles[$article_index + 1]) ? $articles[$article_index + 1] : false;
				} else {
					$article_prev = isset($articles[$article_index + 1]) ? $articles[$article_index + 1] : false;
					$article_next = isset($articles[$article_index - 1]) ? $articles[$article_index - 1] : false;
				}
			}

			if ($article_prev) {
				$prev_url = $this->url->link('blog/article', 'blog_article_id=' .  $article_prev['blog_article_id'] . $url);
				$prev_title = utf8_strlen($article_prev['name']) > $max_prevnext_length ? utf8_substr($article_prev['name'], 0, $max_prevnext_length) . $this->language->get('text_ellipses') : $article_prev['name'];
				$nav_cols++;
			}

			if ($article_next) {
				$next_url = $this->url->link('blog/article', 'blog_article_id=' .  $article_next['blog_article_id'] . $url);
				$next_title = utf8_strlen($article_next['name']) > $max_prevnext_length ? utf8_substr($article_next['name'], 0, $max_prevnext_length) . $this->language->get('text_ellipses') : $article_next['name'];
				$nav_cols++;
			}

			// back/previous page
			if (isset($this->request->server['HTTP_REFERER'])
				&& ($this->request->checkReferer($this->config->get('config_url')) || $this->request->checkReferer($this->config->get('config_ssl')))
				&& (!$article_prev || strpos($this->request->server['HTTP_REFERER'], $prev_url) === false)
				&& (!$article_next || strpos($this->request->server['HTTP_REFERER'], $next_url) === false)) {
				$back_url = $this->request->server['HTTP_REFERER'];
			}

			if ($back_url) {
				$this->session->data['back_url'] = $back_url;
			}

			if ($preview_article) {
				$url_add = $url ? '&' : '?';
				$prev_url .= $article_prev ? $url_add . 'preview_article=' . $this->request->get['preview_article'] : '';
				$next_url .= $article_next ? $url_add . 'preview_article=' . $this->request->get['preview_article'] : '';
			}

			$this->data['more_url'] = $more_url;
			$this->data['more_title'] = $more_title;
			$this->data['prev_url'] = $prev_url;
			$this->data['prev_title'] = $prev_title;
			$this->data['next_url'] = $next_url;
			$this->data['next_title'] = $next_title;
			$this->data['back_url'] = $back_url;
			$this->data['back_title'] = $back_title;
			$this->data['nav_cols'] = $nav_cols;

			$this->data['preview_mode'] = $preview_article;

			// update view count
			if (!$preview_article) {
                $this->model_blog_article->updateViewed($blog_article_id);
            }

            $this->document->setTitle($heading_title);
            $this->document->setDescription(($article_data['meta_description']));
            $this->document->setKeywords($article_data['meta_keyword']);
			$this->document->setUrl($this->url->link('blog/article', 'blog_article_id=' . $blog_article_id));

			$image_info = $this->model_tool_image->getFileInfo($this->data['thumb']);

			if ($image_info) {
				$this->document->setImage($this->data['thumb'], $image_info['mime'], $image_info[0], $image_info[1]);
			}

			$this->document->addLink($this->url->link('blog/article', 'blog_article_id=' . $blog_article_id), 'canonical');

            $this->data['page'] = $this->url->link('blog/article', 'blog_article_id=' . $blog_article_id);
            $this->data['blog_article_id'] = $blog_article_id;

            $this->document->addStyle('catalog/view/root/stylesheet/blog.css');

            $this->template = 'template/blog/article.tpl';
        } else {
            $this->addBreadcrumb($this->language->get('text_error'), $this->url->link('blog/article', 'blog_article_id=' . $blog_article_id . $url));

            $this->document->setTitle($this->language->get('text_error'));

            $this->data['heading_title'] = $this->language->get('text_error');
            $this->data['text_error'] = $this->language->get('text_error');
            $this->data['button_continue'] = $this->language->get('button_continue');

            $this->data['continue'] = $this->url->link('blog/home');

            $this->response->addHeader($this->request->server['SERVER_PROTOCOL'] . '/1.1 404 Not Found');

            $this->template = 'template/error/not_found.tpl';
        }

        $this->data['breadcrumbs'] = $this->getBreadcrumbs();

        $this->children = array(
            'common/notification',
            'common/column_left',
            'common/column_right',
            'common/content_top',
            'common/content_bottom',
            'common/footer',
            'common/header'
        );

        $this->response->setOutput($this->render());
    }
}
