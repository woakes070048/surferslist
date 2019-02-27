<?php
class ControllerFeedGoogleSitemap extends Controller {
	public function index() {
		if (!$this->config->get('google_sitemap_status')) {
			return false;
		}
		$output  = '<?xml version="1.0" encoding="UTF-8"?>';
		$output .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">';

		// Home page

		if ($this->request->isSecure()) {
			$server = $this->config->get('config_ssl');
		} else {
			$server = $this->config->get('config_url');
		}

		$output .= '<url>';
		$output .= '<loc>' . $server . '</loc>';
		$output .= '<changefreq>weekly</changefreq>';
		$output .= '<priority>1.0</priority>';

		if ($this->config->get('config_logo')) {
			$output .= '<image:image>';
			$output .= '<image:loc>' . str_replace(' ', '%20', $server . 'image/' . $this->config->get('config_logo')) . '</image:loc>';
			$output .= '<image:caption>' . $this->config->get('config_meta_description') . '</image:caption>';
			$output .= '<image:title>' . $this->config->get('config_title') . '</image:title>';
			$output .= '</image:image>';
		}

		$output .= '</url>';

		// Information Pages

		$this->load->model('catalog/information');

		$informations = $this->model_catalog_information->getInformations();

		foreach ($informations as $information) {
			$output .= '<url>';
			$output .= '<loc>' . $this->url->link('information/information', 'information_id=' . $information['information_id']) . '</loc>';
			$output .= '<changefreq>weekly</changefreq>';
			$output .= '<priority>0.5</priority>';
			$output .= '</url>';
		}

		// Manufacturers (Brands)

		$this->load->model('catalog/manufacturer');

		$manufacturers = $this->model_catalog_manufacturer->getManufacturers();

		foreach ($manufacturers as $manufacturer) {
			$output .= '<url>';
			$output .= '<loc>' . $this->url->link('product/manufacturer/info', 'manufacturer_id=' . $manufacturer['manufacturer_id']) . '</loc>';
			$output .= '<changefreq>weekly</changefreq>';
			$output .= '<priority>0.8</priority>';

			if ($manufacturer['image']) {
				$output .= '<image:image>';
				$output .= '<image:loc>' . str_replace(' ', '%20', str_replace(' ', '%20', $server . 'image/' . $manufacturer['image'])) . '</image:loc>';
				$output .= '<image:caption>' . $manufacturer['name'] . '</image:caption>';
				$output .= '<image:title>' . $manufacturer['name'] . '</image:title>';
				$output .= '</image:image>';
			}

			$output .= '</url>';
		}

		// Categories

		$this->load->model('catalog/category');

		$output .= $this->getCategories(0);

		// Products

		$this->load->model('catalog/product');
		$this->load->model('tool/image');

		$products = $this->model_catalog_product->getProducts();

		foreach ($products as $product) {
			$product_title = $product['name'];
			$product_title_parts = array('model', 'manufacturer');

			foreach ($product_title_parts as $product_title_part) {
				if ($product[$product_title_part]) {
					$product_title .=  ' - ' . $product[$product_title_part];
				}
			}

			if ($product['image']) {
				$output .= '<url>';
				$output .= '<loc>' . $this->url->link('product/product', 'product_id=' . $product['product_id']) . '</loc>';
				$output .= '<lastmod>' . date('Y-m-d\TH:i:sP', strtotime($product['date_modified'])) . '</lastmod>';
				$output .= '<changefreq>weekly</changefreq>';
				$output .= '<priority>0.9</priority>';
				$output .= '<image:image>';
				$output .= '<image:loc>' . str_replace(' ', '%20', $this->model_tool_image->resize($product['image'], $this->config->get('config_image_popup_width'), $this->config->get('config_image_popup_height'))) . '</image:loc>';
				$output .= '<image:caption>' . utf8_substr(str_replace('&nbsp', ' ', strip_tags(html_entity_decode($product['description'], ENT_QUOTES, 'UTF-8'))), 0, 100) . '...' . '</image:caption>';
				$output .= '<image:title>' . $product_title . '</image:title>';
				$output .= '</image:image>';
				$output .= '</url>';
			}
		}

		$output .= '</urlset>';

		$this->response->addHeader('Content-Type: application/xml');
		$this->response->setOutput($output);
	}

	protected function getCategories($parent_id, $current_path = '') {
		$output = '';

		$results = $this->model_catalog_category->getCategories($parent_id);

		foreach ($results as $result) {
			if (!$current_path) {
				$new_path = $result['category_id'];
			} else {
				$new_path = $current_path . '_' . $result['category_id'];
			}

			$output .= '<url>';
			$output .= '<loc>' . $this->url->link('product/category', 'path=' . $new_path) . '</loc>';
			$output .= '<lastmod>' . date('Y-m-d\TH:i:sP', strtotime($result['date_modified'])) . '</lastmod>';
			$output .= '<changefreq>weekly</changefreq>';
			$output .= '<priority>0.7</priority>';
			$output .= '</url>';

			$output .= $this->getCategories($result['category_id'], $new_path);
		}

		return $output;
	}
}
?>
