<?php
class ControllerBlogCategory extends Controller {
    public function index() {
        $this->data = $this->load->language('blog/common');

        $this->load->model('blog/category');
        $this->load->model('blog/article');
        $this->load->model('tool/image');

        if (isset($this->request->get['search'])) {
            $search = $this->request->get['search'];
        } else if (isset($this->request->get['s'])) {
            $search = $this->request->get['s'];
        } else {
            $search = '';
        }

        $filter_description = isset($this->request->get['description']) ? $this->request->get['description'] : true;
        $author = isset($this->request->get['author']) ? (int)$this->request->get['author'] : 0;
        $sort = isset($this->request->get['sort']) ? $this->request->get['sort'] : 'a.date_available';
        $order = isset($this->request->get['order']) ? $this->request->get['order'] : 'DESC';
        $page = isset($this->request->get['page']) ? (int)$this->request->get['page'] : 1;
        $limit = (isset($this->request->get['limit']) && $this->request->get['limit'] <= $this->config->get('blog_catalog_limit') * 4) ? (int)$this->request->get['limit'] : $this->config->get('blog_catalog_limit');

        $query_params = array(
			'search',
            'description',
            'blog_category_id',
			'author',
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

            $parts = explode('_', (string) $this->request->get['path_blog']);

            $blog_category_id = (int)array_pop($parts);

            foreach ($parts as $path_blog_id) {
                if (!$path_blog) {
                    $path_blog = (int)$path_blog_id;
                } else {
                    $path_blog .= '_' . (int)$path_blog_id;
                }

                $blog_category_info = $this->model_blog_category->getBlogCategory($path_blog_id);

                if ($blog_category_info) {
                    $this->addBreadcrumb($blog_category_info['name'], $this->url->link('blog/category', 'path_blog=' . $path_blog . $url));
                }
            }
        } else {
            $blog_category_id = 0;
        }

        $blog_category_info = $this->model_blog_category->getBlogCategory($blog_category_id);

        if ($blog_category_info) {
            $heading_title = $this->language->get('heading_blog_category') . html_entity_decode($blog_category_info['name'], ENT_QUOTES, 'UTF-8');
            $meta_description = $blog_category_info['meta_description'];
            $meta_keyword = $blog_category_info['meta_keyword'];

            $this->addBreadcrumb($blog_category_info['name'], $this->url->link('blog/category', 'path_blog=' . $this->request->get['path_blog'] . $url));

            $thumb = $blog_category_info['image'] ? $this->model_tool_image->resize($blog_category_info['image'], $this->config->get('blog_image_thumb_width'), $this->config->get('blog_image_thumb_height')) : false;

            $this->data['description'] = html_entity_decode($blog_category_info['description'], ENT_QUOTES, 'UTF-8');

            $data = array(
                'filter_name'               => $search,
    			'filter_category_id' 		=> $blog_category_id,
                'filter_sub_category'       => true,
                'filter_description'        => $filter_description,
    			'filter_member_account_id'  => $author,
    			'sort'               		=> $sort,
    			'order'              		=> $order,
    			'start'              		=> ($page - 1) * $limit,
    			'limit'              		=> $limit
            );

            $article_total = $this->model_blog_article->getTotalBlogArticles($data);

            $max_pages = $limit > 0 ? ceil($article_total / $limit) : 1;

            if ($page <= 0 || $limit <= 0 || ($max_pages > 0 && $page > $max_pages)) {
                $this->redirect($this->url->link('error/not_found'));
            }

    		$this->data['articles'] = $article_total ? $this->getChild('blog/data/list', $this->model_blog_article->getBlogArticles($data)) : array();

            $this->data['sidebar'] = $this->getChild('blog/sidebar', array(
                'query_params'  => $query_params,
                'route'         => 'blog/search',
                'path'          => 'blog_category_id=' . $blog_category_id,
                'filter'        => $data
            ));

            $this->data['empty'] = $this->getChild('blog/empty', array(
                'route'         => 'blog/category',
                'path'          => 'path_blog=' . $this->request->get['path_blog']
            ));

    		$this->data['pagination'] = $this->getPagination($article_total, $page, $limit, 'blog/category', 'path_blog=' . $this->request->get['path_blog'], $url);

    		if ($page > 1) {
    			$heading_title .= ' - ' . sprintf($this->language->get('text_page_of'), $page, $max_pages);
    			$meta_description = strip_tags_decode(substr($this->data['pagination'], strpos($this->data['pagination'], '<div class="results'))) . ' - ' . $meta_description;
    			$meta_keyword .= ', ' . strtolower($this->language->get('text_page')) . ' ' . $page;
    		}

    		$this->document->setTitle($heading_title);
    		$this->document->setDescription($meta_description);
    		$this->document->setKeywords($meta_keyword);

			$image_info = $this->model_tool_image->getFileInfo($thumb);

			if ($image_info) {
				$this->document->setImage($thumb, $image_info['mime'], $image_info[0], $image_info[1]);
			}

            $this->data['heading_title'] = $heading_title;
            $this->data['author'] = $author;
            $this->data['blog_category_id'] = $blog_category_id;
            $this->data['sort'] = $sort;
            $this->data['order'] = $order;
            $this->data['limit'] = $limit;

            $this->data['page'] = $this->url->link('blog/category', 'path_blog=' . $this->request->get['path_blog']);
    		$this->data['url'] = $url;

            $this->document->addStyle('catalog/view/root/stylesheet/blog.css');
            //$this->document->addScript('catalog/view/root/javascript/blog.js');

            $this->template = 'template/blog/category.tpl';
        } else {
            $this->addBreadcrumb($this->language->get('text_error'), $this->url->link('blog/category', $url));

            $this->document->setTitle($this->language->get('text_error'));

            $this->data['heading_title'] = $this->language->get('text_error');

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
