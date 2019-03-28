<?php
class ControllerBlogSidebar extends Controller {
	private $config_article_count = false;

	protected function index($data) {
		$this->data = $this->load->language('blog/common');

		$this->load->model('blog/category');
		$this->load->model('blog/article');

        $this->config_article_count = false; // $this->config->get('blog_article_count');
		$hide_sort_limit = false;

		if ($data['route'] === 'blog/article') {
			$hide_sort_limit = true;
		}

        $this->setQueryParams($data['query_params']);

		// Categories
        $this->data['categories'] = $this->getFilterCategories($data);

        // Sort, Limit
        $this->data['sorts'] = !$hide_sort_limit ? $this->getSortOptions($data) : array();
        $this->data['limits'] = !$hide_sort_limit ? $this->getLimits($data['route'], $data['path'] . $this->getQueryString(array('limit')), $this->config->get('blog_catalog_limit')) : array();
		$this->data['hide_sort_limit'] = $hide_sort_limit;

		// Selected Values
		$filter_search = '';
		$filter_description = '';
		$sort = '';
		$order = '';
		$limit = '';

		if (!empty($data['filter'])) {
			$filter_search = $data['filter']['filter_name'];
			$filter_description = $data['filter']['filter_description'];
			$sort = $data['filter']['sort'];
			$order = $data['filter']['order'];
			$limit = $data['filter']['limit'];
		}

		$this->data['filter_search'] = $filter_search;
		$this->data['filter_description'] = $filter_description;
		$this->data['sort'] = $sort;
		$this->data['order'] = $order;
		$this->data['limit'] = $limit;

		// Links
        $this->data['action'] = str_replace('&amp;', '&', $this->url->link($data['route'], $data['path'] . $this->getQueryString(array('description', 'search'))));
		$this->data['reset'] = $this->getQueryString(array('page')) ? $this->url->link($data['route'], $data['path']) : '';
		$this->data['continue'] = $this->url->link('blog/home');
		$this->data['search'] = $this->url->link('blog/search');

        $this->template = 'template/blog/sidebar.tpl';

        $this->render();
    }

	protected function getFilterCategories($data) {
        $blog_categories = array();

		$categories = $this->model_blog_category->getAllBlogCategories(array(
			'filter_status'		 => 1,
			'sort'               => 'sort_order_path',
			'order'              => 'ASC',
			'start'              => 0,
			'limit'              => 999
		));

		foreach ($categories as $category) {
			if ($this->config_article_count) {
				$article_total = $this->model_blog_article->getTotalBlogArticles(array(
    				'filter_category_id' => $category['blog_category_id']
    			));
			}

			$blog_categories[] = array(
				'name'  => utf8_strtoupper($category['name']) . ($this->config_article_count ? ' (' . $article_total . ')' : ''),
				// 'href'  => $this->url->link($data['route'], $data['path'] . '&filter_category_id=' . $category['blog_category_id']),
				'href'  => $this->url->link('blog/category', 'path_blog=' . $category['path_blog'])
				// 'thumb' => $category['image'] ? $this->model_tool_image->resize($category['image'], $this->config->get('blog_image_additional_width'), $this->config->get('blog_image_additional_height')) : false
			);
		}

		return $blog_categories;
	}

	protected function getSortOptions($data) {
		$url = $this->getQueryString(array('sort', 'order'));

		$this->addSort($this->language->get('text_default'), 'a.sort_order-ASC', $this->url->link($data['route'], $data['path'] . '&sort=a.sort_order&order=ASC' . $url));
        $this->addSort($this->language->get('text_name_asc'), 'ad.name-ASC', $this->url->link($data['route'], $data['path'] . '&sort=ad.name&order=ASC' . $url));
        $this->addSort($this->language->get('text_name_desc'), 'ad.name-DESC', $this->url->link($data['route'], $data['path'] . '&sort=ad.name&order=DESC' . $url));
        $this->addSort($this->language->get('text_date_asc'), 'a.date_available-ASC', $this->url->link($data['route'], $data['path'] . '&sort=a.date_available&order=ASC' . $url));
        $this->addSort($this->language->get('text_date_desc'), 'a.date_available-DESC', $this->url->link($data['route'], $data['path'] . '&sort=a.date_available&order=DESC' . $url));

		return $this->getSorts();
	}
}
