<?php
class ControllerInformationSitemap extends Controller {
	public function index() {
		$language = array_merge(
			$this->load->language('information/sitemap'),
			$this->load->language('common/footer')
		);

		$this->document->setTitle($this->language->get('heading_title'));
		$this->document->setDescription(sprintf($this->language->get('meta_description'), $this->config->get('config_name')));
		$this->document->setKeywords($this->language->get('meta_keyword'));

		$this->data = $this->cache->get('sitemap.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id'));

		if ($this->data === false) {
			$this->data = $language;

			$this->addBreadcrumb($this->language->get('text_home'), $this->url->link('common/home'));
			$this->addBreadcrumb($this->language->get('heading_title'), $this->url->link('information/sitemap'));

			$this->data['breadcrumbs'] = $this->getBreadcrumbs();

			$this->load->model('catalog/category');
			$this->load->model('catalog/product');

			// CATEGORIES
			$categories = array();

			// TO-DO: use categories_complete
			// $categories_complete = $this->model_catalog_category->getAllCategoriesComplete();
			//
			// $all_category_data[] = array(
			// 	'category_id' 				=> $result['category_id'],
			// 	'parent_id'   				=> $result['parent_id'],
			// 	'name'        				=> $result['name'],
			// 	'path'        				=> $result['path'],
			// 	'path_name'	  				=> $result['path_name'],
			// 	'image'  	  				=> $result['image'],
			// 	'children'	  				=> $category_children,
			// 	'manufacturers'				=> $category_manufacturers,
			// 	'product_count' 			=> $result['product_count'],
			// 	'sort_order'  				=> $result['sort_order'],
			// 	'sort_order_path'			=> $result['sort_order_path'],
			// 	'sort_order_path_display'	=> $result['sort_order_path_display'],
			// 	'status'  	  				=> $result['status']
			// );

			$categories_1 = $this->model_catalog_category->getCategories(0);

			foreach ($categories_1 as $category_1) {
				$level_2_data = array();

				$categories_2 = $this->model_catalog_category->getCategories($category_1['category_id']);

				foreach ($categories_2 as $category_2) {
					$level_3_data = array();

					$categories_3 = $this->model_catalog_category->getCategories($category_2['category_id']);

					foreach ($categories_3 as $category_3) {
						$level_3_data[] = array(
							'name' => $category_3['name'],
							'href' => $this->url->link('product/category', 'path=' . $category_1['category_id'] . '_' . $category_2['category_id'] . '_' . $category_3['category_id'])
						);
					}

					$level_2_data[] = array(
						'name'     => $category_2['name'],
						'children' => $level_3_data,
						'href'     => $this->url->link('product/category', 'path=' . $category_1['category_id'] . '_' . $category_2['category_id'])
					);
				}

				$categories[] = array(
					'name'     => $category_1['name'],
					'children' => $level_2_data,
					'href'     => $this->url->link('product/category', 'path=' . $category_1['category_id'])
				);
			}

			$this->data['categories'] = $categories;

			// BROWSE
			$this->data['listings'] = $this->url->link('product/allproducts');
			$this->data['category'] = $this->url->link('product/allcategories');
			$this->data['manufacturer'] = $this->url->link('product/manufacturer');
			$this->data['member'] = $this->url->link('product/member');
			$this->data['featured'] = $this->url->link('product/featured');
			$this->data['special'] = $this->url->link('product/special');
			$this->data['search'] = $this->url->link('product/search');
			$this->data['compare'] = $this->url->link('product/compare');

			// ACCOUNT SERVICES
			$this->data['register'] = $this->url->link('account/register', '', 'SSL');
			$this->data['login'] = $this->url->link('account/login', '', 'SSL');
			$this->data['logout'] = $this->url->link('account/logout', '', 'SSL');
			$this->data['forgotten'] = $this->url->link('account/forgotten', '', 'SSL');
			$this->data['post'] = $this->url->link('account/product/insert', '', 'SSL');

			// MY ACCOUNT
			$this->data['account'] = $this->url->link('account/account', '', 'SSL');
			$this->data['edit'] = $this->url->link('account/edit', '', 'SSL');
			$this->data['profile'] = $this->customer->isLogged() && $this->customer->validateMembership() ? $this->customer->getProfileUrl() : $this->url->link('account/member', '', 'SSL');
			$this->data['order'] = $this->url->link('account/order', '', 'SSL');
			$this->data['wishlist'] = $this->url->link('account/wishlist', '', 'SSL');
			$this->data['newsletter'] = $this->url->link('account/newsletter', '', 'SSL');
			$this->data['myproducts'] = $this->url->link('account/product', '', 'SSL');
			$this->data['address'] = $this->url->link('account/address', '', 'SSL');
			$this->data['history'] = $this->url->link('account/order', '', 'SSL');
			$this->data['download'] = $this->url->link('account/download', '', 'SSL');
			$this->data['sales'] = $this->url->link('account/sales', '', 'SSL');
			$this->data['review'] = $this->url->link('account/review', '', 'SSL');
			$this->data['question'] = $this->url->link('account/question', '', 'SSL');
			$this->data['password'] = $this->url->link('account/password', '', 'SSL');
			$this->data['voucher'] = $this->url->link('account/voucher', '', 'SSL');
			$this->data['return'] = $this->url->link('account/return/insert', '', 'SSL');
			$this->data['affiliate'] = $this->url->link('affiliate/account', '', 'SSL');

			// CART
			$this->data['cart'] = $this->url->link('checkout/cart', '', 'SSL');
			$this->data['checkout'] = $this->url->link('checkout/checkout', '', 'SSL');

			// INFORMATION
			$this->data['location'] = $this->url->link('information/location');
			$this->data['contact'] = $this->url->link('information/contact');
			$this->data['sitemap'] = $this->url->link('information/sitemap');

			$this->load->model('catalog/information');

			$this->data['informations'] = array();

			foreach ($this->model_catalog_information->getInformations() as $result) {
				$this->data['informations'][] = array(
					'title' => $result['title'],
					'href'  => $this->url->link('information/information', 'information_id=' . $result['information_id'])
				);
			}

			// OTHER
			$this->data['home'] = $this->url->link('common/home');
			$this->data['email'] = $this->config->get('config_email');
			$this->data['text_footer_contact'] = sprintf($this->language->get('text_footer_contact'), $this->url->link('information/contact', 'contact_id=0'));

			$this->cache->set('sitemap.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id'), $this->data, 60 * 60 * 24 * 30); // 1 month cache expiration
		}

		$this->template = '/template/information/sitemap.tpl';

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
?>
