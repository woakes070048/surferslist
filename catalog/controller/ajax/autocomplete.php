<?php
class ControllerAjaxAutocomplete extends Controller {

    public function product() {
		if ((!isset($this->request->get['filter_name']) && !isset($this->request->get['filter_model']) && !isset($this->request->get['filter_category_id']))
            || !$this->customer->validateLogin()
            || !$this->customer->validateProfile()) {
            return false;
        }

        $json = array();

		$this->load->model('account/product');

        $data_filters = array('filter_name', 'filter_model', 'filter_category_id', 'filter_sub_category');

        foreach ($data_filters as $data_filter) {
            ${$data_filter} = isset($this->request->get[$data_filter]) ? $this->request->get[$data_filter] : '';
        }

		$data = array(
			'customer_id'         => $this->customer->getId(),
			'filter_name'         => $filter_name,
			'filter_model'        => $filter_model,
			'filter_category_id'  => $filter_category_id,
			'filter_sub_category' => $filter_sub_category,
			'start'               => 0,
			'limit'               => isset($this->request->get['limit']) ? $this->request->get['limit'] : 15
		);

		$results = $this->model_account_product->getProducts($data);

		foreach ($results as $result) {
			// $option_data = array();
            //
			// $product_options = $this->model_account_product->getProductOptions($result['product_id']);
            //
			// foreach ($product_options as $product_option) {
			// 	if ($product_option['type'] == 'select' || $product_option['type'] == 'radio' || $product_option['type'] == 'checkbox' || $product_option['type'] == 'image') {
			// 		$option_value_data = array();
            //
			// 		foreach ($product_option['product_option_value'] as $product_option_value) {
			// 			$option_value_data[] = array(
			// 				'product_option_value_id' => $product_option_value['product_option_value_id'],
			// 				'option_value_id'         => $product_option_value['option_value_id'],
			// 				'name'                    => $product_option_value['name'],
			// 				'price'                   => (float)$product_option_value['price'] ? $this->currency->format($product_option_value['price'], $this->config->get('config_currency')) : false,
			// 				'price_prefix'            => $product_option_value['price_prefix']
			// 			);
			// 		}
            //
			// 		$option_data[] = array(
			// 			'product_option_id' => $product_option['product_option_id'],
			// 			'option_id'         => $product_option['option_id'],
			// 			'name'              => $product_option['name'],
			// 			'type'              => $product_option['type'],
			// 			'option_value'      => $option_value_data,
			// 			'required'          => $product_option['required']
			// 		);
			// 	} else {
			// 		$option_data[] = array(
			// 			'product_option_id' => $product_option['product_option_id'],
			// 			'option_id'         => $product_option['option_id'],
			// 			'name'              => $product_option['name'],
			// 			'type'              => $product_option['type'],
			// 			'option_value'      => $product_option['option_value'],
			// 			'required'          => $product_option['required']
			// 		);
			// 	}
			// }

			$json[] = array(
				'product_id' => $result['product_id'],
				'name'       => strip_tags_decode(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8')),
				'model'      => $result['model'],
				// 'option'     => $option_data,
				'price'      => $result['price']
			);
		}

		$this->response->setOutput(json_encode($json));
	}

    public function member() {
		if (!isset($this->request->get['member_name'])
            && !isset($this->request->get['customer_name'])
            && !isset($this->request->get['member_id'])) {
            return false;
        }

		$json = array();

        $member_name = isset($this->request->get['member_name']) ? $this->request->get['member_name'] : '';
        $customer_name = isset($this->request->get['customer_name']) ? $this->request->get['customer_name'] : '';
        $member_id = isset($this->request->get['member_id']) ? (int)$this->request->get['member_id'] : 0;
        $limit = isset($this->request->get['limit']) ? (int)$this->request->get['limit'] : 15;

		$data = array(
			'filter_member_account_name'	=> $member_name,
			'filter_customer_name'			=> $customer_name,
			'filter_member_id'				=> $member_id,
			'start'							=> 0,
			'limit'							=> $limit
		);

		$this->load->model('catalog/member');
		$this->load->model('tool/image');

		$results = $this->model_catalog_member->getMembers($data);

		foreach ($results as $result) {
			if (!$this->admin && $result['customer_id'] == 0) continue; // filter out profiles without a linked account

			$member_image = ($result['member_account_image']) ? $result['member_account_image'] : 'no_image.jpg';

			$json[] = array(
				'member_id'				=> $result['member_account_id'],
				'member_image'			=> $this->model_tool_image->resize($member_image, $this->config->get('config_image_category_width'), $this->config->get('config_image_category_height')),
				'member_name' 			=> strip_tags_decode(html_entity_decode($result['member_account_name'], ENT_QUOTES, 'UTF-8')),
				'fullname'				=> $result['fullname']
			);
		}

		$this->response->setOutput(json_encode($json));
	}

    public function attribute() {
		if (!isset($this->request->get['filter_name'])
            || !$this->customer->validateLogin()
            || !$this->customer->validateProfile()
            || !$this->customer->getMemberPermission('attribute_enabled')
            || !$this->config->get('member_tab_attribute')) {
            return false;
        }

        $json = array();

		$this->load->model('account/product');

		$data = array(
			'filter_name' => $this->request->get['filter_name'],
			'start'       => 0,
			'limit'       => isset($this->request->get['limit']) ? $this->request->get['limit'] : 15
		);

		$results = $this->model_account_product->getAttributes($data);

		foreach ($results as $result) {
			$json[] = array(
				'attribute_id'    => $result['attribute_id'],
				'name'            => strip_tags_decode(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8')),
				'attribute_group' => $result['attribute_group']
			);
		}

		$sort_order = array();

		foreach ($json as $key => $value) {
			$sort_order[$key] = $value['name'];
		}

		array_multisort($sort_order, SORT_ASC, $json);

		$this->response->setOutput(json_encode($json));
	}

	public function option() {
        if (!isset($this->request->get['filter_name'])
            || !$this->customer->validateLogin()
            || !$this->customer->validateProfile()
            || !$this->customer->getMemberPermission('option_enabled')
            || !$this->config->get('member_status')
            || !$this->config->get('member_tab_option')) {
            return false;
        }

        $json = array();

        $filter_name = $this->request->get['filter_name'];

		$this->load->language('account/product');
		$this->load->model('account/product');
		$this->load->model('tool/image');

		$data = array(
			'filter_name' => $filter_name,
			'start'       => 0,
			'limit'       => isset($this->request->get['limit']) ? $this->request->get['limit'] : 15
		);

		$options = $this->model_account_product->getOptions($data);

		foreach ($options as $option) {
			$option_value_data = array();

			if ($option['type'] == 'select' || $option['type'] == 'radio' || $option['type'] == 'checkbox' || $option['type'] == 'image') {
				$option_values = $this->model_account_product->getOptionValues($option['option_id']);

				foreach ($option_values as $option_value) {
					$image = $this->model_tool_image->resize($option_value['image'], 50, 50);

					$option_value_data[] = array(
						'option_value_id' => $option_value['option_value_id'],
						'name'            => html_entity_decode($option_value['name'], ENT_QUOTES, 'UTF-8'),
						'image'           => $image
					);
				}

				$sort_order = array();

				foreach ($option_value_data as $key => $value) {
					$sort_order[$key] = $value['name'];
				}

				array_multisort($sort_order, SORT_ASC, $option_value_data);
			}

			$type = '';

			if ($option['type'] == 'select' || $option['type'] == 'radio' || $option['type'] == 'checkbox' || $option['type'] == 'image') {
				$type = $this->language->get('text_choose');
			}

			if ($option['type'] == 'text' || $option['type'] == 'textarea') {
				$type = $this->language->get('text_input');
			}

			if ($option['type'] == 'file') {
				$type = $this->language->get('text_file');
			}

			if ($option['type'] == 'date' || $option['type'] == 'datetime' || $option['type'] == 'time') {
				$type = $this->language->get('text_date');
			}

			$json[] = array(
				'option_id'    => $option['option_id'],
				'name'         => strip_tags_decode(html_entity_decode($option['name'], ENT_QUOTES, 'UTF-8')),
				'category'     => $type,
				'type'         => $option['type'],
				'option_value' => $option_value_data
			);
		}

		$sort_order = array();

		foreach ($json as $key => $value) {
			$sort_order[$key] = $value['name'];
		}

		array_multisort($sort_order, SORT_ASC, $json);

		$this->response->setOutput(json_encode($json));
	}

}
