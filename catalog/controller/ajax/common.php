<?php
class ControllerAjaxCommon extends Controller {

    public function country() {
        if (!isset($this->request->get['country_id'])) {
            return false;
        }

        $json = array();

        $country_id = (int)$this->request->get['country_id'];

        $this->load->model('localisation/country');

        $country_info = $this->model_localisation_country->getCountry($country_id);

        if ($country_info) {
            $this->load->model('localisation/zone');

            $zone_info = $this->model_localisation_zone->getZonesByCountryId($country_id);

            $json = array(
                'country_id'        => $country_info['country_id'],
                'name'              => $country_info['name'],
                'iso_code_2'        => $country_info['iso_code_2'],
                'iso_code_3'        => $country_info['iso_code_3'],
                'address_format'    => $country_info['address_format'],
                'postcode_required' => $country_info['postcode_required'],
                'zone'              => $zone_info,
                'status'            => $country_info['status']
            );
        }

        $this->response->setOutput(json_encode($json));
    }

    public function sub_category() {
		if (empty($this->request->get['category_id'])) {
            return false;
        }

        $json = array();

		$category_id = (int)$this->request->get['category_id'];

		$this->load->model('catalog/category');

		$category_info = $this->model_catalog_category->getCategories($category_id);

		if ($category_info) {
			$count = count($category_info);

			$json = array(
				'category_id'  => $category_id,
				'category'     => $category_info,
				'count'        => $count,
				'text_select'  => sprintf('Select Sub-Category (%s)', $count)
			);
		}

		$this->response->setOutput(json_encode($json));
	}

    public function manufactuer_category() {
		if (!isset($this->request->get['category_id'])) {
            return false;
        }

        $json = array();

		$category_id = (int)$this->request->get['category_id'];

		$this->load->model('catalog/manufacturer');

		$manufacturers_data = array(
			'filter_category_id' 		=> $category_id,
			'include_parent_categories' => true
		);

		$manufacturer_info = $this->model_catalog_manufacturer->getManufacturers($manufacturers_data);

		if ($manufacturer_info) {
			$count = count($manufacturer_info);

            $text_select = !empty($this->request->get['required']) ? sprintf('Select Brand (%s)', $count) : sprintf('--- Any Brand (%s) ---', $count);

			$json = array(
				'category_id'  => $category_id,
				'manufacturer' => $manufacturer_info,
				'count'        => $count,
				'text_select'  => $text_select
			);
		}

		$this->response->setOutput(json_encode($json));
	}

}
