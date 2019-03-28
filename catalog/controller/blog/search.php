<?php
class ControllerBlogSearch extends Controller {
    public function index() {
        $this->data = array_merge(
            $this->load->language('blog/common'),
            $this->load->language('blog/search')
        );

        $this->load->model('blog/article');

        if (isset($this->request->get['search'])) {
            $search = $this->request->get['search'];
        } else if (isset($this->request->get['s'])) {
            $search = $this->request->get['s'];
        } else {
            $search = '';
        }

        $tag = isset($this->request->get['tag']) ? $this->request->get['tag'] : '';
		$filter_description = isset($this->request->get['description']) ? $this->request->get['description'] : true;
        $blog_category_id = isset($this->request->get['blog_category_id']) ? (int)$this->request->get['blog_category_id'] : 0;
        $author = isset($this->request->get['author']) ? (int)$this->request->get['author'] : 0;
		$sort = isset($this->request->get['sort']) ? $this->request->get['sort'] : 'a.sort_order';
		$order = isset($this->request->get['order']) ? $this->request->get['order'] : 'DESC';
		$page = isset($this->request->get['page']) ? (int)$this->request->get['page'] : 1;
		$limit = (isset($this->request->get['limit']) && $this->request->get['limit'] <= $this->config->get('blog_catalog_limit') * 4) ? (int)$this->request->get['limit'] : $this->config->get('blog_catalog_limit');

        $heading_title = $this->language->get('heading_blog_search');
        $meta_description = $this->language->get('meta_description');
        $meta_keyword = $this->language->get('meta_keyword');

        $implode = array();

        if ($search) {
            $search = strip_non_alphanumeric_encode($search, false, ' -_"');
            $implode[] = sprintf($this->language->get('text_search_keyword'), $search);
        }

        if ($tag) {
            $tag = strip_non_alphanumeric($tag, true);
            $implode[] = sprintf($this->language->get('text_search_tag'), $tag);
        }

        if ($author) {
            $this->load->model('catalog/member');
            $member_info = $this->model_catalog_member->getMember($author);

            if ($member_info) {
                $implode[] = sprintf($this->language->get('text_search_author'), strip_tags_decode($member_info['member_account_name']));
            }
        }

        if ($blog_category_id) {
            $this->load->model('blog/category');
            $category_info = $this->model_blog_category->getBlogCategory($blog_category_id);

            if ($category_info) {
                $implode[] = sprintf($this->language->get('text_search_category'), strip_tags_decode($category_info['name']));
            }
        }

        if ($implode) {
            $heading_title .= ': ' . implode(', ', $implode);
        }

        $query_params = array(
			'search',
			'tag',
			'description',
			'author',
			'blog_category_id',
			'blog_article_id',
			'sort',
			'order',
			'limit',
            'page'
		);

		$this->setQueryParams($query_params);

		$url = $this->getQueryString();

		$this->addBreadcrumb($this->language->get('text_home'), $this->url->link('common/home'));
        $this->addBreadcrumb($this->language->get('text_blog'), $this->url->link('blog/home'));
        $this->addBreadcrumb($this->language->get('text_search'), $this->url->link('blog/search', $url));

        $this->data['heading_title'] = $heading_title;

        $this->data['articles'] = array();
        $article_total = 0;
        $data = array();

        if (!$search && !$tag && !$author && !$blog_category_id && !isset($this->request->get['sort'])) {
            // $this->redirect($this->url->link('blog/home'));
        } else {
            $data = array(
                'filter_name'               => $search,
                'filter_tag'                => $tag,
                'filter_description'        => $filter_description,
                'filter_category_id'        => $blog_category_id,
                'filter_sub_category'       => true,
                'filter_member_account_id'  => $author,
                'sort'                      => $sort,
                'order'                     => $order,
                'start'                     => ($page - 1) * $limit,
                'limit'                     => $limit,
            );

            $article_total = $this->model_blog_article->getTotalBlogArticles($data);

            $max_pages = $limit > 0 ? ceil($article_total / $limit) : 1;

            if ($page <= 0 || $limit <= 0 || ($max_pages > 0 && $page > $max_pages)) {
                $this->redirect($this->url->link('error/not_found'));
            }

    		$this->data['articles'] = $article_total ? $this->getChild('blog/data/list', $this->model_blog_article->getBlogArticles($data)) : array();
        }

        $this->data['sidebar'] = $this->getChild('blog/sidebar', array(
            'query_params'  => $query_params,
            'route'         => 'blog/search',
            'path'          => '',
            'filter'        => $data
        ));

        $this->data['empty'] = $this->getChild('blog/empty', array(
            'route'         => 'blog/search',
            'path'          => ''
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

        $this->data['tag'] = $tag;
        $this->data['author'] = $author;
        $this->data['blog_category_id'] = $blog_category_id;
        $this->data['sort'] = $sort;
        $this->data['order'] = $order;
        $this->data['limit'] = $limit;

        $this->data['page'] = $this->url->link('blog/search');
        $this->data['url'] = $url;

        $this->document->addStyle('catalog/view/root/stylesheet/blog.css');

        $this->template = 'template/blog/search.tpl';

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
