<?php
class ControllerModuleRefine extends Controller {
	private $category_id = 0;
	private $config_product_count = false;
	private $display_more_options = false;
	private $show_all_manufacturers = false;
	private $anchor = '';

	protected function index($data) {
		$this->data = $this->load->language('product/common');

		$this->load->model('catalog/product');
		$this->load->model('catalog/category');
		$this->load->model('catalog/manufacturer');
		$this->load->model('catalog/filter');

        $this->config_product_count = true; // $this->config->get('config_product_count');

		$this->display_more_options = $data['display_more_options'];
		$this->show_all_manufacturers = false;

		switch ($data['route']) {
			case 'product/allproducts':
				$this->show_all_manufacturers = true;
				break;

			case 'product/category':
				$this->category_id = $data['filter']['filter_category_id'];
				break;

			case 'product/manufacturer/info':
				$this->anchor = '#brand-listings';
				break;

			case 'product/member/info':
				$this->anchor = '#member-listings';
				break;

			default:
				$this->anchor = '';
				break;
		}

        $this->setQueryParams($data['query_params']);

		// Brands/Manufacturers
        $this->data['manufacturers'] = $this->getFilterManufacturers($data);

		// Categories
        $this->data['category_hierarchy_ids'] = array();

        if ($this->category_id) {
            $this->data['category_hierarchy_ids'][] = $this->category_id;
        }

        $this->data['categories'] = $this->getFilterCategories($data);
        $this->data['parent_categories'] = $this->getFilterParentCategories($data);

		// Type, Filters
		$this->data['listing_types'] = $this->getFilterListingTypes($data);
		$this->data['filter_groups'] = $this->getFilterFilterGroups($data);

        if (isset($this->request->get['filter']) && !is_array($this->request->get['filter'])) {
            $this->data['filter_category'] = explode(',', $this->request->get['filter']);
            $this->display_more_options = true;
        } else {
            $this->data['filter_category'] = array();
        }

        // Sort, Limit
        $this->data['sorts'] = $this->getSortOptions($data);
        $this->data['limits'] = $this->getLimits($data['route'], $data['path'] . $this->getQueryString(array('limit')));

		// Selected Values
		$this->data['filter'] = $data['filter']['filter_filter'];
		$this->data['filter_search'] = $data['filter']['filter_name'];
		$this->data['filter_tag'] = isset($data['filter']['filter_tag']) ? $data['filter']['filter_tag'] : '';
		$this->data['filter_category_id'] = $data['filter']['filter_category_id'];
		$this->data['filter_manufacturer_id'] = $data['filter']['filter_manufacturer_id'];
		$this->data['filter_country_id'] = isset($data['filter']['filter_country_id']) ? $data['filter']['filter_country_id'] : '';
		$this->data['filter_zone_id'] = isset($data['filter']['filter_zone_id']) ? $data['filter']['filter_zone_id'] : '';
		$this->data['filter_location'] = isset($data['filter']['filter_location']) ? $data['filter']['filter_location'] : '';
		$this->data['type_selected'] = $data['filter']['filter_listing_type'];
		$this->data['sort'] = $data['filter']['sort'];
		$this->data['order'] = $data['filter']['order'];
		$this->data['limit'] = $data['filter']['limit'];
		$this->data['forsale'] = isset($data['forsale']) ? $data['forsale'] : '';

		$request_path = isset($this->request->server['REQUEST_URI']) ? parse_url(strtolower(urldecode($this->request->server['REQUEST_URI'])), PHP_URL_PATH) : '';

		// Links
        $this->data['action'] = str_replace('&amp;', '&', $this->url->link($data['route'], $data['path'] . $this->getQueryString(array('filter', 'type', 'search'))));;
        $this->data['location_page'] = $data['route'] !== 'embed/profile' ? $this->url->link('information/location', 'redirect_path=' . urlencode(ltrim($request_path, "/"))) : '';
		$this->data['random'] = $this->url->link($data['route'], $data['path'] . '&sort=random' . $this->getQueryString(array('sort', 'order')));
		$this->data['compare'] = $data['route'] !== 'embed/profile' ? $this->url->link('product/compare', '') : '';
		$this->data['reset'] = $this->getQueryString(array('page')) ? $this->url->link($data['route'], $data['path']) . $this->anchor : '';

		$this->data['display_more_options'] = $this->display_more_options;
        $this->data['show_all_manufacturers'] = $this->show_all_manufacturers;

        $this->data['text_compare'] = sprintf($this->language->get('text_compare'), (isset($this->session->data['compare']) ? count($this->session->data['compare']) : 0));

        $this->data['products'] = $data['products'] ? true : false;

        $this->template = 'template/module/refine.tpl';

        $this->render();
    }

	protected function getFilterListingTypes($data) {
		$listing_types_filter = array();

        $listing_types = array(
            array(
                'id' 	=> '0',
                'name'	=> $this->language->get('text_classified'),
                'sort_order' => '1'
            ),
            array(
                'id'	=> '1',
                'name'	=> $this->language->get('text_buy_now'),
                'sort_order' => '2'
            ),
            array(
                'id'	=> '-1',
                'name'	=> $this->language->get('text_shared'),
                'sort_order' => '3'
            )
        );

        foreach ($listing_types as $listing_type) {
            if ($this->config_product_count) {
                $product_total_type = count(array_filter($data['products'], function ($item) use ($listing_type) {
                    return $item['type_id'] == $listing_type['id'];
                }));
            }

            $listing_types_filter[] = array(
                'type_id' => $listing_type['id'],
                'name'    => $listing_type['name'] . ($this->config_product_count ? sprintf($this->language->get('text_product_count'), $product_total_type) : '')
            );
        }

		return $listing_types_filter;
	}

	protected function getFilterFilterGroups($data) {
        $filter_groups_filter = array();

		// display all filters all the time
        $filter_groups = $this->model_catalog_filter->getCategoryFiltersAll();

        foreach ($filter_groups as $filter_group) {
            $filter_data = array();

            foreach ($filter_group['filter'] as $filter_group_filter) {
                if ($this->config_product_count) {
                    $product_total_filter = count(array_filter($data['products'], function ($item) use ($filter_group_filter) {
                        return in_array($filter_group_filter['filter_id'], $item['filter_ids']);
                    }));
                }

                $filter_data[] = array(
                    'filter_id' => $filter_group_filter['filter_id'],
                    'name'      => $filter_group_filter['name'] . ($this->config_product_count ? sprintf($this->language->get('text_product_count'), $product_total_filter) : '')
                );
            }

            $filter_groups_filter[] = array(
                'filter_group_id' => $filter_group['filter_group_id'],
                'name'            => $filter_group['name'],
                'filter'          => $filter_data
            );
        }

		return $filter_groups_filter;
	}

    protected function getFilterManufacturers($data) {
        if (!$this->config->get('apac_products_refine_brand') || $data['route'] === 'product/manufacturer/info') {
			return array();
		}

		if (isset($data['product_manufacturers'])) {
			return $this->getFilterProductManufacturers($data);
		}

		$manufacturers_filter = array();

        $url = $this->getQueryString(array('manufacturer_id', 'filter_manufacturer_id'));

        $manufacturers_data = array(
            'filter_category_id' 		=> isset($data['category_parents'][1]['id']) ? $data['category_parents'][1]['id'] : $this->category_id,
            'include_parent_categories' => true
        );

        $manufacturers = $this->model_catalog_manufacturer->getManufacturers($manufacturers_data);

        if ($manufacturers) {
            $manufacturers_filter[0] = array(
                'id'	=> 0,
                'name'	=> $this->language->get('text_manufacturer_all'),
                'href'	=> $this->url->link($data['route'], $data['path'] . $url)
            );

            foreach ($manufacturers as $manufacturer) {
                if ($this->config_product_count) {
                    $product_total_manufacturer = count(array_filter($data['products'], function ($item) use ($manufacturer) {
                        return $manufacturer['manufacturer_id'] == $item['manufacturer_id'];
                    }));
                }

                $manufacturers_filter[$manufacturer['manufacturer_id']] = array(
                    'id'	=> $manufacturer['manufacturer_id'],
                    'name'  => $manufacturer['name'] . ($this->config_product_count ? sprintf($this->language->get('text_product_count'), $product_total_manufacturer) : ''),
                    'product_count' => $this->config_product_count ? $product_total_manufacturer : null,
                    'href'  => $data['route'] === 'product/allproducts'
                        ? $this->url->link('product/manufacturer/info', 'manufacturer_id=' . $manufacturer['manufacturer_id'] . $url)
                        : $this->url->link($data['route'], $data['path'] . ($this->category_id ? '&manufacturer_id=' : '&filter_manufacturer_id=') . $manufacturer['manufacturer_id'] . $url)
                );
            }
        }

        return $manufacturers_filter;
    }

	protected function getFilterProductManufacturers($data) {
		if (!$this->config->get('apac_products_refine_brand')) {
			return array();
		}

		$manufacturers_filter = array();

		if ($data['product_manufacturers']) {
			$url = $this->getQueryString(array('filter_manufacturer_id'));

			$manufacturers_filter[] = array(
				'id'	=> 0,
				'name'	=> $this->language->get('text_manufacturer_all'),
				'href'	=> $this->url->link($data['route'], $data['path'] . '&filter_manufacturer_id=0' . $url) . $this->anchor
			);

			foreach ($data['product_manufacturers'] as $manufacturer) {
				if ($this->config_product_count) {
					$product_total_manufacturer = $manufacturer['product_count'];

					// $product_total_manufacturer = count(array_filter($products, function ($item) use ($manufacturer) {
					// 	return $manufacturer['manufacturer_id'] == $item['manufacturer_id'];
					// }));
				}

				if (!$this->config_product_count || $product_total_manufacturer) {
					$manufacturers_filter[] = array(
						'id'	=> $manufacturer['manufacturer_id'],
						'name'  => $manufacturer['name'] . ($this->config_product_count ? sprintf($this->language->get('text_product_count'), $product_total_manufacturer) : ''),
						'product_count' => $this->config_product_count ? $product_total_manufacturer : null,
						'href'  => $this->url->link($data['route'], $data['path'] . '&filter_manufacturer_id=' . $manufacturer['manufacturer_id'] . $url) . $this->anchor
					);
				}
			}
		}

        return $manufacturers_filter;
    }

	protected function getFilterCategories($data) {
		if (!$this->config->get('apac_products_refine_category')) {
			return array();
		}

		if (isset($data['product_categories'])) {
			return $this->getFilterProductCategories($data);
		}

		$url = $this->getQueryString(array('filter_category_id', 'filter_manufacturer_id', 'manufacturer_id'));

		$categories_filter = array();

		$categories = $this->model_catalog_category->getCategories($this->category_id);

		if ($categories) {
			$categories_filter[] = array(
				'id'	=> 0,
				'name'	=> !$this->category_id ? $this->language->get('text_category_all') : $this->language->get('text_sub_categories_all'),
				'href'	=> $data['route'] === 'product/allproducts' || $this->category_id
					? $this->url->link('product/allproducts', $url)
					: $this->url->link($data['route'], $data['path'] . $url)
			);

			foreach ($categories as $category) {
				if (utf8_strpos($category['name'], $this->language->get('heading_more')) !== false) {
					$category_name = $this->language->get('heading_more');
				} else if (utf8_strpos($category['name'], $this->language->get('heading_other')) !== false) {
					$category_name = $this->language->get('heading_other');
				} else {
					$category_name = $category['name'];
				}

				switch (substr_count($category['path'], '_')) {
					case 1:
					case 2:
						$category_name = ucwords($category_name);
						break;
					case 0:
					default:
						$category_name = utf8_strtoupper($category_name);
						break;
				}

				if ($this->config_product_count) {
					// $product_total_category = $category['product_count'];
					$product_total_category = count(array_filter($data['products'], function ($item) use ($category) {
						return in_array($category['category_id'], $item['category_ids']);
					}));
				}

				$categories_filter[] = array(
					'id'    => $category['category_id'],
					'name'  => $category_name . ($this->config_product_count && !$category['top'] ? sprintf($this->language->get('text_product_count'), $product_total_category) : ''),
					'href'  => $data['route'] === 'product/allproducts' || $this->category_id
						? $this->url->link('product/category', 'path=' . $category['path'] . $url)
						: $this->url->link($data['route'], $data['path'] . '&filter_category_id=' . $category['category_id'] . $url)
				);
			}
		}

		return $categories_filter;
	}

	protected function getFilterParentCategories($data) {
		if (!$this->config->get('apac_products_refine_category') || !isset($data['category_parents'])) {
			return array();
		}

		$url = $this->getQueryString(array('filter_category_id', 'filter_manufacturer_id', 'manufacturer_id'));

		$categories_filter = array();

		foreach ($data['category_parents'] as $category_parent) {
			$this->data['category_hierarchy_ids'][] = $category_parent['id'];

			$category_parent_categories = $this->model_catalog_category->getCategories($category_parent['id']);

			if ($category_parent_categories) {
				$categories_filter[$category_parent['id']][] = array(
					'id'	=> 0,
					'name'	=> $category_parent['id'] == 0
						? $this->language->get('text_category_all')
						: $this->language->get('text_sub_categories_all'),
					'href'	=> $category_parent['id'] == 0
						? $this->url->link('product/allproducts', $url)
						: $this->url->link($data['route'], 'path=' . $category_parent['path'] . $url)
				);

				foreach ($category_parent_categories as $category_parent_category) {
					if (utf8_strpos($category_parent_category['name'], $this->language->get('heading_more')) !== false) {
						$category_name = $this->language->get('heading_more');
					} else if (utf8_strpos($category_parent_category['name'], $this->language->get('heading_other')) !== false) {
						$category_name = $this->language->get('heading_other');
					} else {
						$category_name = $category_parent_category['name'];
					}

					switch (substr_count($category_parent_category['path'], '_')) {
						case 1:
						case 2:
							$category_name = ucwords($category_name);
							break;
						case 0:
						default:
							$category_name = utf8_strtoupper($category_name);
							break;
					}

					if ($this->config_product_count) {
						// $product_total_category_parent_category = $category_parent_category['product_count'];

						$data['filter_category_id'] = $category_parent_category['category_id'];
						$product_total_category_parent_category = $this->model_catalog_product->getTotalProducts($data);

						// $products_parent_category = $this->model_catalog_product->getProductsIndexes($data);
						// $product_total_category_parent_category = count(array_filter($data['products'], function ($item) use ($category_parent_category) {
						// 	return in_array($category_parent_category['category_id'], $item['category_ids']);
						// }));
					}

					$categories_filter[$category_parent['id']][] = array(
						'id'    => $category_parent_category['category_id'],
						'name'  => $category_name . ($this->config_product_count && !$category_parent_category['top'] ? sprintf($this->language->get('text_product_count'), $product_total_category_parent_category) : ''),
						'href'  => $category_parent['id'] == 0
							? $this->url->link($data['route'], 'path=' . $category_parent_category['category_id'] . $url)
							: $this->url->link($data['route'], 'path=' . $category_parent['path'] . '_' . $category_parent_category['category_id'] . $url)
					);
				}
			}
		}

		return $categories_filter;
	}

    protected function getFilterProductCategories($data) {
		if (!$this->config->get('apac_products_refine_category')) {
			return array();
		}

        $categories_filter = array();

		$url = $this->getQueryString(array('filter_manufacturer_id', 'filter_category_id'));

		$categories_filter[] = array(
			'id' 		=> 0,
			'name'      => $this->language->get('text_category_all'),
			'href'      => $this->url->link($data['route'], $data['path'] . '&filter_category_id=0' . $url) . $this->anchor
		);

		foreach ($data['product_categories'] as $category_info) {
			if (utf8_strpos($category_info['name'], $this->language->get('heading_more')) !== false) {
				$category_name = $this->language->get('heading_more');
			} else if (utf8_strpos($category_info['name'], $this->language->get('heading_other')) !== false) {
				$category_name = $this->language->get('heading_other');
			} else {
				$category_name = $category_info['name'];
			}

			switch (substr_count($category_info['path'], '_')) {
				case 1:
					$category_name = '&emsp;' . ucwords($category_name);
					break;
				case 2:
					$category_name = '&emsp;' . '&emsp;' . ucwords($category_name);
					break;
				case 0:
				default:
					$category_name = utf8_strtoupper($category_name);
					break;
			}

			if ($this->config_product_count) {
				$product_total_category = $category_info['product_count'];

				// 	$product_total_category = count(array_filter($products, function ($item) use ($category_info) {
				// 		return in_array($category_info['category_id'], $item['category_ids']);
				// 	}));
			}

			if ((!$this->config_product_count || $product_total_category) && (substr_count($category_info['path'], '_') < 2)) {
				$categories_filter[] = array(
					'id' 		  => $category_info['category_id'],
					'name'        => $category_name . ($this->config_product_count ? sprintf($this->language->get('text_product_count'), $product_total_category) : ''),
					'product_count' => $this->config_product_count ? $product_total_category : null,
					'link'        => $this->url->link('product/category', 'path=' . $category_info['path']),
					'href'        => $this->url->link($data['route'], $data['path'] . '&filter_category_id=' . $category_info['category_id'] . $url) . $this->anchor
				);
			}
		}

        return $categories_filter;
    }

	protected function getSortOptions($data) {
		$url = $this->getQueryString(array('sort', 'order'));

		if ($this->config->get('apac_products_sort_sort_order')) {
			$this->addSort($this->language->get('text_default'), 'p.sort_order-ASC', $this->url->link($data['route'], $data['path'] . '&sort=p.sort_order&order=ASC' . $url));
		}

		if ($this->config->get('apac_products_sort_name')) {
			$this->addSort($this->language->get('text_name_asc'), 'pd.name-ASC', $this->url->link($data['route'], $data['path'] . '&sort=pd.name&order=ASC' . $url));
			$this->addSort($this->language->get('text_name_desc'), 'pd.name-DESC', $this->url->link($data['route'], $data['path'] . '&sort=pd.name&order=DESC' . $url));
		}

		if ($this->config->get('apac_products_sort_price')) {
			$this->addSort($this->language->get('text_price_asc'), 'p.price-ASC', $this->url->link($data['route'], $data['path'] . '&sort=p.price&order=ASC' . $url));
			$this->addSort($this->language->get('text_price_desc'), 'p.price-DESC', $this->url->link($data['route'], $data['path'] . '&sort=p.price&order=DESC' . $url));
		}

		if ($this->config->get('apac_products_sort_model')) {
			$this->addSort($this->language->get('text_model_asc'), 'p.model-ASC', $this->url->link($data['route'], $data['path'] . '&sort=p.model&order=ASC' . $url));
			$this->addSort($this->language->get('text_model_desc'), 'p.model-DESC', $this->url->link($data['route'], $data['path'] . '&sort=p.model&order=DESC' . $url));
		}

		if ($this->config->get('apac_products_sort_date')) {
			$this->addSort($this->language->get('text_date_asc'), 'p.date_added-ASC', $this->url->link($data['route'], $data['path'] . '&sort=p.date_added&order=ASC' . $url));
			$this->addSort($this->language->get('text_date_desc'), 'p.date_added-DESC', $this->url->link($data['route'], $data['path'] . '&sort=p.date_added&order=DESC' . $url));
		}

		$this->addSort($this->language->get('text_random'), 'random-' . $data['filter']['order'],  $this->url->link($data['route'], $data['path'] . '&sort=random' . $url));

		return $this->getSorts();
	}
}
