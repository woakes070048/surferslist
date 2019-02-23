<?php
class ControllerCommonSeoUrl extends Controller {
	private $cache = array();  // query (id=#) => keyword, and also full urls
	private $category_path = array();  // keyword path (a/b/c) => category id path (1_2_3)
	private $products = array();  // product_id => array(route, path, manufacturer_id, product_id)

	public function __construct($registry) {
		parent::__construct($registry);

		$this->load->model('tool/seo_url');

		$this->cache = array_merge(
			$this->getEmptyRoutes(),
			$this->model_tool_seo_url->getUrlAlias('manufacturer'),
			$this->model_tool_seo_url->getUrlAlias('member')
		);

		if ($this->config->get('config_route_cache')) {
			if (is_array($this->config->get('config_route_keywords'))) {
				$this->cache = array_merge($this->config->get('config_route_keywords'), $this->cache);
			}

			if (is_array($this->config->get('config_route_category_path'))) {
				$this->category_path = $this->config->get('config_route_category_path');
			}
		}
	}

	// parses an SEO-friendly url, resets GET params, and forwards to appropriate route
	public function index() {
		// Add rewrite to url class
		if ($this->config->get('config_seo_url')) {
			$this->url->addRewrite($this);
		}

		// Rewrite and redirect any products that are not already in seo-friendly format
		if (preg_match('~^/index\.php\?~', $this->request->server['REQUEST_URI']) && isset($this->request->get['route']) && $this->request->get['route'] == 'product/product' && isset($this->request->get['product_id'])) {
			unset($this->request->get['route']);

			// rewrites product url to seo-friendly version with full path that should include categories and brand
			$product_url = $this->url->link('product/product', http_build_query($this->request->get));

			// redirect only if rewrite was successful (no query string params)
			$product_url_parts = parse_url($product_url);

			if (empty($product_url_parts['query'])) {
				$this->redirect($product_url, 301);
			}
		}

		// Decode the SEO URL to find route to forward request
		// (.htaccess directive "RewriteRule ^([^?]*) index.php?_route_=$1 [L,QSA]")
		if (isset($this->request->get['_route_'])) {
			$route = $this->request->get['_route_'];

			// routes set by config are memory-cached
			// check category paths first
			if (array_key_exists($route, $this->category_path)) {
				$this->request->get['route'] = 'product/category';
				$this->request->get['path'] = $this->category_path[$route];
			} else if (array_key_exists(substr($route, 0, strrpos($route, '/')), $this->category_path)) {
				$this->request->get['route'] = 'product/category';
				$this->request->get['path'] = $this->category_path[substr($route, 0, strrpos($route, '/'))];
				$query = $this->getQuery(substr($route, strrpos($route, '/') + 1));
				$url = is_array($query) ? explode('=', $query[0]) : explode('=', $query);
				if ($url[0] == 'manufacturer_id') {
					$this->request->get['manufacturer_id'] = $url[1];
				}
			} else if (in_array($route, $this->cache, true) && !is_numeric($route)) {
				$query = array_search($route, $this->cache, true);
				// $this->log->write('route: ' . $route . ' | query: ' . $query);
				$this->setRequest($query, true);
			}

			// not a memory-cached route
			if (!isset($this->request->get['route'])) {
				$parts = explode('/', $route);

				// remove any empty arrays from trailing /
				if (utf8_strlen(end($parts)) == 0) {
					array_pop($parts);
				}

				$first_part = reset($parts);
				$last_part = end($parts);

				// last keyword in all product listing urls starts with "listing-"
				if (strpos($last_part, 'listing-') === 0) {
					$query = $this->getQuery($last_part);

					if (!$query) {
						$product_retired_data = $this->model_tool_seo_url->getProductRetired($last_part);

						if (!$product_retired_data) {
							$this->request->get['route'] = 'error/not_found';
						} else {
							$this->request->get['product_id'] = $product_retired_data['product_id'];
							$this->request->get['customer_id'] = $product_retired_data['member_customer_id'];
							$this->request->get['manufacturer_id'] = $product_retired_data['manufacturer_id'];
							// to-do: add path to product_retired table (ideal) upon retirement, or join select query above to product_to_category table (slower)
							// $this->request->get['path'] = $product_retired_data['path'];
							$this->request->get['route'] = 'error/product_retired';
						}
					} else {
						$this->setRequest((is_array($query) ? $query[0] : $query), true);

						// remove from $parts array so this keyword isn't looked up again in the foreach loop that iterates over $parts below
						array_pop($parts);
					}
				}

				// not a product listing, break apart and loop through each keyword
				if (!isset($this->request->get['route']) || $this->request->get['route'] == 'product/product') {
					foreach ($parts as $part) {
						$query = $this->getQuery($part);

						// duplicate keywords found
						// identify the product using manufacturer and category using path
						// (fallback: categories are cached above and listing keywords are unique)
						if (is_array($query) && count($query) > 1) {
							if (!empty($this->request->get['path'])) {
								$path_parts = explode('_', $this->request->get['path']);
								$parent_id = array_pop($path_parts);

								foreach ($query as $value) {
									$query_parts = explode('=', $value);

									if (count($query_parts) == 2 && $query_parts[0] == 'category_id') {
										if ($this->model_tool_seo_url->categoryHasParent($query_parts[1], $parent_id)) {
											$query = $value;
											break;
										}
									}
								}
							} else if (!empty($this->request->get['manufacturer_id'])) {
								foreach ($query as $value) {
									$query_parts = explode('=', $value);

									if (count($query_parts) == 2 && $query_parts[0] == 'product_id') {
										if ($this->model_tool_seo_url->productHasManufacturer($query_parts[1], $this->request->get['manufacturer_id'])) {
											$query = $value;
											break;
										}
									}
								}
							} else {
								$query = $query[0];
							}
						}

						if ($query) {
							$this->setRequest($query, false);
						} else {
							$this->request->get['route'] = 'error/not_found';
							break;
						}
					}
				}
			}

			if (!isset($this->request->get['route'])) {
				if (isset($this->request->get['product_id'])) {
					$this->request->get['route'] = 'product/product';
				} elseif (isset($this->request->get['path'])) {
					$this->request->get['route'] = 'product/category';
				} elseif (isset($this->request->get['member_id'])) {
					$this->request->get['route'] = 'product/member/info';
				} elseif (isset($this->request->get['manufacturer_id'])) {
					$this->request->get['route'] = 'product/manufacturer/info';
				} elseif (isset($this->request->get['information_id'])) {
					$this->request->get['route'] = 'information/information';
				} elseif (isset($this->request->get['forum_post_id'])) {
					$this->request->get['route'] = 'forum/read';
				} elseif (isset($this->request->get['forum_path'])) {
					$this->request->get['route'] = 'forum/forum_category';
				} elseif (isset($this->request->get['blog_article_id'])) {
                    $this->request->get['route'] = 'blog/blog_article';
				} elseif (isset($this->request->get['path_blog'])) {
                    $this->request->get['route'] = 'blog/blog_category';
				} else {
					// check `url_alias` db table one more time again for a keyword with the entire path which a matching route, as a last-ditch effort (e.g. quickviews)
					$query = $this->getQuery($route);

					if ($query) {
						 $this->request->get['route'] = is_array($query) ? $query[0] : $query;
					}
				}
			}

			if (isset($this->request->get['route']) && (!$this->config->get('config_maintenance') || isset($this->session->data['token']))) {
				// ensure the url is complete for product listings and redirect to if not
				if ($this->request->get['route'] == 'product/product' && isset($this->request->get['product_id'])) {
					// rewrite url of product with complete path, including categories and brand
					$product_url = $this->url->link('product/product', 'product_id=' . (int)$this->request->get['product_id']); //http_build_query($temp));
					$product_url_parts = parse_url($product_url);

					// redirect to new url if it doesn't match the incoming request (i.e. request is not the copmlete url) and no tracking query param set
					if ((!empty($product_url_parts['path']) && !empty($this->request->server['REQUEST_URI']))
						&& (strtolower(urldecode($this->request->server['REQUEST_URI'])) != strtolower($product_url_parts['path']))
						&& (strpos($this->request->server['REQUEST_URI'], 'tracking=') === false)
						&& (strpos($this->request->server['REQUEST_URI'], 'preview_listing=') === false)) {
						$this->redirect($product_url, 301);
					}
				}

				return $this->forward($this->request->get['route']);
			}
		}
	}

	// returns an SEO-friendly url, given a link like http://domain.com/index.php?route=product/category&category_path=1_2_3
	public function rewrite($link) {
		if ($this->config->get('config_seo_url')) {
			$url = '';
			$data = array();

			$url_info = parse_url(str_replace('&amp;', '&', $link));
			parse_str($url_info['query'], $data);

			if (!empty($this->cache[$url_info['query']])) {
				$url = $this->cache[$url_info['query']];

				unset($data['product_id']);
				unset($data['listing_id']);
				unset($data['information_id']);
				unset($data['manufacturer_id']);
				unset($data['member_id']);
				unset($data['path']);
				// unset($data['forum_post_id']);
				// unset($data['blog_article_id']);
				// unset($data['forum_path']);
				// unset($data['path_blog']);
			}

			// force product page url rewrite to include category(ies) and manufacturer
			if (!$url && !empty($data['route']) && $data['route'] == 'product/product' && !empty($data['product_id'])) {
				$product_id = (int)$data['product_id'];

				if (!empty($this->products[$product_id])) {
					$data = $this->products[$product_id];
				} else {
					$product_data = $this->model_tool_seo_url->getProductData($data);
					$data = $product_data;
					unset($product_data['tracking']);
					$this->products[$product_id] = $product_data;
				}
				// $url .=  '/listings'; // prefix all product listings with the allproducts keyword (REMOVED)
			}

			if (!$url) {
				// $this->log->write('data: ' . json_encode($data));
				foreach ($data as $key => $value) {
					if ($value === null) {
						unset($data[$key]);
						continue;
					}

					$keyword = '';

					if (isset($data['route'])) {
						if ($key == 'path') {
							if (in_array($value, $this->category_path, true)) {
								$categoryKeyword = array_search($value, $this->category_path, true);
								$keyword .= '/' . $categoryKeyword;
							} else {
								$categories = explode('_', (string)$value);

								foreach ($categories as $category) {
									$keyword .= '/' . $this->getKeyword('category_id=' . (int)$category);
								}
							}

							unset($data[$key]);
						} else if ($data['route'] == 'product/category' && $key == 'manufacturer_id') {
							$keyword .= '/' . $this->getKeyword('manufacturer_id=' . (int)$value);
							unset($data[$key]);
						} else if (($data['route'] == 'product/product' && ($key == 'product_id' || $key == 'manufacturer_id'))
							|| ($data['route'] == 'product/manufacturer/info' && $key == 'manufacturer_id')
							|| ($data['route'] == 'information/information' && $key == 'information_id')
							|| ($data['route'] == 'product/member/info' && $key == 'member_id')
							|| ($data['route'] == 'forum/read' && $key == 'forum_post_id')) {

							$query = $key . '=' . (int)$value;

							if (array_key_exists($query, $this->cache)) {
								$keyword .= '/' . $this->cache[$query];
							} else if ((int)$value > 0) {
								$keyword .= '/' . $this->getKeyword($query);
							}

							// $this->log->write($key . ' | ' . $value . ' | ' . $keyword);

							// special check for listings not found (e.g. retired or deleted)
							if ($keyword == '/' && $data['route'] == 'product/product' && $key == 'product_id') {
								$url = false;
								break;
							}

							unset($data[$key]);
						} else if (array_key_exists($data['route'], $this->cache)) {
							if (is_numeric($this->cache[$data['route']])) {
								continue;
							}

							$keyword .= '/' . $this->cache[$data['route']];
							// $this->log->write('cache | ' . $data['route'] . ' | ' . $keyword);
							unset($data[$key]);
						} else if ($key == 'path_blog') {
							// prefix all blog category urls with blog_home keyword
							$keyword .=  '/blog';
							// $keyword .= '/' . $this->getKeyword('blog/blog_home');

							$blog_categories = explode('_', $value);

							foreach ($blog_categories as $blog_category) {
								$keyword .= '/' . $this->getKeyword('blog_category_id=' . (int)$blog_category);
							}

							unset($data[$key]);
						} else if ($data['route'] == 'blog/blog_article' && $key == 'blog_article_id') {
							// prefix all blog category urls with blog_home keyword
							// $keyword .= '/' . $this->getKeyword('blog/blog_home');

							$keyword .= '/blog/' . $this->getKeyword($key . '=' . (int)$value);
							unset($data[$key]);
						} else if ($key == 'forum_path') {
							$keyword .=  '/forum';

							$forum_categories = explode('_', $value);

							foreach ($forum_categories as $forum_category) {
								$keyword .= '/' . $this->getKeyword('forum_category_id=' . (int)$forum_category);
							}

							unset($data[$key]);
						} else {
							$keyword_temp = $this->getKeyword($data['route']);
							if ($keyword_temp && !is_numeric($keyword_temp)) {
								// $this->log->write('keyword_temp | ' . $data['route'] . ' | ' . $keyword_temp);
								$keyword .= '/' . $keyword_temp;
								unset($data[$key]);
							}
						}
					}

					$url .= $keyword;
				}

				$this->cache[$url_info['query']] = $url;
			}

			if ($url) {
				unset($data['route']);

				$query = '';

				if ($data) {
					foreach ($data as $key => $value) {
						$query .= '&' . rawurlencode((string)$key) . '=' . rawurlencode((is_array($value) ? http_build_query($value) : (string)$value));
					}

					if ($query) {
						$query = '?' . str_replace('&', '&amp;', trim($query, '&'));
					}
				}

				return $url_info['scheme'] . '://' . $url_info['host'] . (isset($url_info['port']) ? ':' . $url_info['port'] : '') . str_replace('/index.php', '', $url_info['path']) . $url . $query;
			} else {
				return $link;
			}
		} else {
			return $link;
		}
	}

	private function setRequest($query, $set_route = false) {
		$url = explode('=', $query);

		if ($set_route) {
			if ($url[0] == 'product_id') {
				$this->request->get['product_id'] = $url[1];
				$this->request->get['route'] = 'product/product';
			} else if ($url[0] == 'information_id') {
				$this->request->get['information_id'] = $url[1];
				$this->request->get['route'] = 'information/information';
			} else if ($url[0] == 'manufacturer_id') {
				$this->request->get['manufacturer_id'] = $url[1];
				$this->request->get['route'] = 'product/manufacturer/info';
			} else if ($url[0] == 'member_id') {
				$this->request->get['member_id'] = $url[1];
				$this->request->get['route'] = 'product/member/info';
			} else if ($url[0] == $query) {
				$this->request->get['route'] = $url[0];
			}
		} else {
			if ($url[0] == 'product_id') {
				$this->request->get['product_id'] = $url[1];
			}

			if ($url[0] == 'category_id') {
				if (!isset($this->request->get['path'])) {
					$this->request->get['path'] = $url[1];
				} else {
					$this->request->get['path'] .= '_' . $url[1];
				}
			}

			if ($url[0] == 'member_id') {
				$this->request->get['member_id'] = $url[1];
				$this->request->get['filter_member_id'] = $url[1];
			}

			if ($url[0] == 'manufacturer_id') {
				$this->request->get['manufacturer_id'] = $url[1];
				$this->request->get['filter_manufacturer_id'] = $url[1];
			}

			if ($url[0] == 'information_id') {
				$this->request->get['information_id'] = $url[1];
			}

			// Unavailable
			if ($url[0] == 'error/product_unavailable') {
				$this->request->get['route'] = 'error/product_unavailable';
			}

			// Blog
			if ($url[0] == 'blog_article_id') {
				$this->request->get['blog_article_id'] = $url[1];
			}

			if ($url[0] == 'blog_category_id') {
				if (!isset($this->request->get['path_blog'])) {
					$this->request->get['path_blog'] = $url[1];
				} else {
					$this->request->get['path_blog'] .= '_' . $url[1];
				}
			}

			// Forum
			if ($url[0] == 'forum_post_id') {
				$this->request->get['forum_post_id'] = $url[1];
			}

			if ($url[0] == 'forum_category_id') {
				if (!isset($this->request->get['forum_path'])) {
					$this->request->get['forum_path'] = $url[1];
				} else {
					$this->request->get['forum_path'] .= '_' . $url[1];
				}
			}
		}
	}

	private function getKeyword($query) {
		if (!empty($this->cache[$query])) {
			$keyword = $this->cache[$query];
		} else {
			if (strpos($query, 'product_id=') === 0) {
				$keyword = $this->model_tool_seo_url->getProductKeyword($query);
			} else {
				$keyword = $this->model_tool_seo_url->getKeyword($query);
			}

			if ($query && $keyword) {
				$this->cache[$query] = $keyword;
			}
		}

		return $keyword;
	}

	private function getQuery($keyword) {
		if (is_numeric($keyword)) {
			$this->request->get['page'] = (int)$keyword;
			return;
		}

		$cachedKeywords = array_flip($this->cache);

		if (array_key_exists($keyword, $cachedKeywords)) {
			$query = $cachedKeywords[$keyword];
		} else {
			if (strpos($keyword, 'listing-') === 0) {
				$query = $this->model_tool_seo_url->getProductQuery($keyword);
			} else {
				$query = $this->model_tool_seo_url->getQuery($keyword);

				// handle potential duplicates
				if (is_array($query)) {
					$query = $query[0];
				}
			}

			if ($query && $keyword) {
				$this->cache[$query] = $keyword;
			}
		}

		return $query;
	}

	private function getEmptyRoutes() {
		return array_flip(array(
		    'product/product',
		    'product/category',
		    'product/manufacturer/info',
		    'product/member/info',
		    'information/information',
		    'information/information/info'
		));
	}

}
?>
