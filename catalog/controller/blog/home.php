<?php
class ControllerBlogHome extends Controller {
    public function index() {
        $this->data = $this->load->language('blog/common');

        $this->load->model('setting/setting');

        $blog_setting = $this->model_setting_setting->getSetting('blog', $this->config->get('config_store_id'));

        if (!$blog_setting) {
			$this->redirect($this->url->link('common/home'));
        }

        $this->load->model('blog/article');

        $name = $blog_setting['blog_name'][$this->config->get('config_language_id')];
        $heading_title = $blog_setting['blog_title'][$this->config->get('config_language_id')];
        $meta_description = $blog_setting['blog_meta_description'][$this->config->get('config_language_id')];
        $meta_keyword = $blog_setting['blog_meta_keyword'][$this->config->get('config_language_id')];

        if (isset($this->request->get['search'])) {
            $search = $this->request->get['search'];
        } else if (isset($this->request->get['s'])) {
            $search = $this->request->get['s'];
        } else {
            $search = '';
        }

        $filter_description = isset($this->request->get['description']) ? $this->request->get['description'] : true;
        $sort = isset($this->request->get['sort']) ? $this->request->get['sort'] : 'a.date_available';
        $order = isset($this->request->get['order']) ? $this->request->get['order'] : 'DESC';
        $page = isset($this->request->get['page']) ? (int)$this->request->get['page'] : 1;
        $limit = (isset($this->request->get['limit']) && $this->request->get['limit'] <= $this->config->get('blog_catalog_limit') * 4) ? (int)$this->request->get['limit'] : $this->config->get('blog_catalog_limit');

        $query_params = array(
			'search',
            'description',
			'sort',
			'order',
			'limit',
            'page'
		);

		$this->setQueryParams($query_params);

		$url = $this->getQueryString();

		$this->addBreadcrumb($this->language->get('text_home'), $this->url->link('common/home'));
        $this->addBreadcrumb($this->language->get('text_blog'), $this->url->link('blog/home'));

        $data = array(
            'filter_name'           => $search,
            'filter_description'    => $filter_description,
            'sort'                  => $sort,
            'order'                 => $order,
            'start'                 => ($page - 1) * $limit,
            'limit'                 => $limit
        );

        $article_total = $this->model_blog_article->getTotalBlogArticles($data);

        $max_pages = $limit > 0 ? ceil($article_total / $limit) : 1;

        if ($page <= 0 || $limit <= 0 || ($max_pages > 0 && $page > $max_pages)) {
            $this->redirect($this->url->link('error/not_found'));
        }

		$this->data['articles'] = $article_total ? $this->getChild('blog/data/list', $this->model_blog_article->getBlogArticles($data)) : array();

        $this->data['sidebar'] = $this->getChild('blog/sidebar', array(
            'query_params' => $query_params,
            'route' => 'blog/search',
            'path' => '',
            'filter' => $data
        ));

        $this->data['empty'] = $this->getChild('blog/empty', array(
            'route' => 'blog/home',
            'path' => ''
        ));

		$this->data['pagination'] = $this->getPagination($article_total, $page, $limit, 'blog/search', '', $url);

		if ($page > 1) {
			$heading_title .= ' - ' . sprintf($this->language->get('text_page_of'), $page, $max_pages);
			$meta_description = strip_tags_decode(substr($this->data['pagination'], strpos($this->data['pagination'], '<div class="results'))) . ' - ' . $meta_description;
			$meta_keyword .= ', ' . strtolower($this->language->get('text_page')) . ' ' . $page;
		}

        $this->document->setTitle($heading_title);
        $this->document->setDescription($meta_description);
        $this->document->setKeywords($meta_keyword);

        $this->data['heading_title'] = $heading_title;
        $this->data['sort'] = $sort;
        $this->data['order'] = $order;
        $this->data['limit'] = $limit;

        $this->data['page'] = $this->url->link('blog/home');
        $this->data['url'] = $url;

        $this->document->addStyle('catalog/view/root/stylesheet/blog.css');
        //$this->document->addScript('catalog/view/root/javascript/blog.js');
        //$this->document->addScript('catalog/view/root/wookmark/wookmark.min.js');

        $this->template = 'template/blog/home.tpl';

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
