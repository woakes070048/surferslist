<?php
class ControllerBlogArticle extends Controller {
    private $error = array();

    public function index() {
        $this->data = array_merge(
            $this->load->language('blog/common'),
            $this->load->language('blog/article')
        );

        $this->load->model('blog/category');
        $this->load->model('blog/article');

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

        $blog_article_id = isset($this->request->get['blog_article_id']) ? (int)$this->request->get['blog_article_id'] : 0;

        $blog_article_info = $this->model_blog_article->getBlogArticle($blog_article_id);

        if ($blog_article_info) {
            $this->addBreadcrumb($blog_article_info['name'], $this->url->link('blog/article', 'blog_article_id=' . $this->request->get['blog_article_id']) . $url);

            $this->load->model('catalog/product');
            $this->load->model('tool/image');

            $heading_title = $this->language->get('heading_blog_article') . html_entity_decode($blog_article_info['name'], ENT_QUOTES, 'UTF-8');

            $article_data = $this->getChild('blog/data/complete', $blog_article_info);

			$this->data = array_merge($this->data, $article_data);

            $this->data['heading_title'] = $heading_title;

            $this->data['related_products'] = $this->getChild('product/data/list', $this->model_blog_article->getRelatedProduct($blog_article_id));

            $data = array(
                'filter_name'               => '',
                'filter_description'        => true,
                'sort'                      => 'a.date_available',
                'order'                     => 'DESC',
                'limit'                     => $this->config->get('blog_catalog_limit'),
            );

            $this->data['sidebar'] = $this->getChild('blog/sidebar', array(
                'query_params' => $query_params,
                'route' => 'blog/search',
                'path' => '',
                'filter' => $data
            ));

            $this->document->setTitle($heading_title);
            $this->document->setDescription(($blog_article_info['meta_description'] ?: $article_data['short_description']));
            $this->document->setKeywords($blog_article_info['meta_keyword']);
			$this->document->setUrl($this->url->link('blog/article', 'blog_article_id=' . $this->request->get['blog_article_id']));

			$image_info = $this->model_tool_image->getFileInfo($this->data['thumb']);

			if ($image_info) {
				$this->document->setImage($this->data['thumb'], $image_info['mime'], $image_info[0], $image_info[1]);
			}

			$this->document->addLink($this->url->link('blog/article', 'blog_article_id=' . $this->request->get['blog_article_id']), 'canonical');

            $this->data['page'] = $this->url->link('blog/article', 'blog_article_id=' . $blog_article_info['blog_article_id']);
            $this->data['blog_article_id'] = $blog_article_id;

            $this->document->addStyle('catalog/view/root/stylesheet/blog.css');
            //$this->document->addScript('catalog/view/root/javascript/blog.js');

            $this->model_blog_article->updateViewed($this->request->get['blog_article_id']);

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
