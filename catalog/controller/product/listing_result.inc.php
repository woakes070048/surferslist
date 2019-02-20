<?php
if (!isset($customer_group_id)) {
    $customer_group_id = $this->customer->isLogged() ? $this->customer->getCustomerGroupId() : $this->config->get('config_customer_group_id');
}

$result_product_data = $this->cache->get('product_' . (int)$result['product_id'] . '.min.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id') . '.' . (int)$customer_group_id);

if ($result_product_data === false) {
    $thumb = $this->model_tool_image->resize($result['image'], $this->config->get('config_image_product_width'), $this->config->get('config_image_product_height'), 'fw');
    $image = $this->model_tool_image->resize($result['image'], $this->config->get('config_image_thumb_width'), $this->config->get('config_image_thumb_height'), 'fw');

    $result_description = remove_links(preg_replace('/\s+/', ' ', strip_tags_decode($result['description'])));

    if (!$result_description) {
        if ($result['year'] != '0000') {
            $result_description .= $result['year'] . ' ';
        }

        if ($result['manufacturer_id'] > 1) {
            $result_description .= $result['manufacturer'] . ' ';
        }

        $result_description .= $result['model'];

        if (utf8_strpos(trim($result['size']), ' ') === false && utf8_strlen($result['size']) < 10) {
            $result_description .= ' ' . $result['size'];
        }
    } else if (utf8_strlen($result_description) > 80) {
        $result_description = utf8_substr($result_description, 0, 80) . $this->language->get('text_ellipses');
    }

    $result_product_data = array(
        'href'              => $this->url->link('product/product', 'product_id=' . $result['product_id'], 'SSL'),
        'product_id'        => $result['product_id'],
        'customer_id'       => $result['customer_id'],
        'member_id'         => $result['member_id'],
        'manufacturer_id'   => $result['manufacturer_id'],
        'manufacturer'      => $result['manufacturer'],
        'path'              => $result['path'],
        'type_id'           => $result['type_id'],
    	'type'              => $result['type_id'] == 1 ? $this->language->get('text_buy_now') : ($result['type_id'] == 0 ? $this->language->get('text_classified') : $this->language->get('text_shared')),
        'name'              => $result['name'],
        'model'             => $result['model'],
        'size'              => $result['size'],
        'year'              => $result['year'],
        'thumb'             => $thumb,
        'image'             => $image,
        'description'       => $result_description,
        'quantity'          => $result['quantity'],
        'quickview'         => $this->url->link('product/quickview', 'listing_id=' . $result['product_id'], 'SSL'),
    	'location'          => isset($result['location']) ? $result['location'] : '',
        'zone_id'           => isset($result['zone_id']) ? $result['zone_id'] : '',
    	'location_zone'     => !empty($location_zone) ? $location_zone['name'] : '',
        'country_id'        => isset($result['country_id']) ? $result['country_id'] : '',
    	'location_country'  => !empty($location_country) ? $location_country['iso_code_3'] : '',
        'featured'          => $result['featured']
    );
    // 'manufacturer_image' => !empty($result['manufacturer_image']) && $result['manufacturer_id'] != 1 ? $this->model_tool_image->resize($result['manufacturer_image'], 100, 40, "fh") : false,
    // 'manufacturer_href' => $this->url->link('product/manufacturer/info', 'manufacturer_id=' . $result['manufacturer_id'], 'SSL'),
    // 'member'            => isset($result['member']) ? $result['member'] : '',
    // 'member_href'       => $this->url->link('product/member/info', 'member_id=' . $result['member_id'], 'SSL'),
    // 'member_contact'    => $this->url->link('information/contact', 'contact_id=' . $result['customer_id'], 'SSL'),
    // 'date_added'        => $result['date_added'],
    // 'date_modified'     => $result['date_modified'],
    // 'sort_order'        => $result['sort_order'],
    // 'viewed'            => $result['viewed'],
    // 'attribute'         => isset($attribute_data) ? $attribute_data : array(),
    // 'weight'            => isset($result['weight']) && isset($result['weight_class_id']) ? $this->weight->format($result['weight'], $result['weight_class_id']) : '',
    // 'length'            => isset($result['length']) && isset($result['length_class_id']) ? $this->length->format($result['length'], $result['length_class_id']) : '',
    // 'width'             => isset($result['width']) && isset($result['length_class_id']) ? $this->length->format($result['width'], $result['length_class_id']) : '',
    // 'height'            => isset($result['height']) && isset($result['length_class_id']) ? $this->length->format($result['height'], $result['length_class_id']) : '',
    // 'location_href'     => isset($result['country_id']) && isset($result['zone_id']) ? $this->url->link('product/search', 'country=' . $result['country_id'] . '&state=' . $result['zone_id'], 'SSL') : ''

    $this->cache->set('product_' . (int)$result['product_id'] . '.min.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id') . '.' . (int)$customer_group_id, $result_product_data);
}

// add-in non-cached listing data
$price = false;
$special = false;
$salebadges = false;
$savebadges = false;
$tax = false;

if (!$this->config->get('config_customer_price') || $this->customer->isLogged()) {
    $price = $result['price'] != 0
        ? $this->currency->format($this->tax->calculate($result['price'], $result['tax_class_id'], $this->config->get('config_tax')))
        : $this->language->get('text_free');
}

if ((float)$result['special']) {
    $special = $this->currency->format($this->tax->calculate($result['special'], $result['tax_class_id'], $this->config->get('config_tax')));
    $salebadges = round((($result['price'] - $result['special']) / $result['price']) * 100, 0);
    $savebadges = $this->currency->format(($this->tax->calculate($result['price'], $result['tax_class_id'], $this->config->get('config_tax'))) - ($this->tax->calculate($result['special'], $result['tax_class_id'], $this->config->get('config_tax'))));
}

if ($this->config->get('config_tax')) {
    $tax = $this->currency->format((float)$result['special'] ? $result['special'] : $result['price']);
}

$result_product_data = array_merge($result_product_data, array(
    'price'             => $price,
    'price_value'       => $result['price'],
    'special'           => $special,
    'special_value'     => $result['special'],
    'salebadges'        => $salebadges,
    'savebadges'        => $savebadges,
    'tax'               => $tax,
    'compare'           => isset($this->session->data['compare']) && in_array($result['product_id'], $this->session->data['compare']) ? true : false
));

$this->data['products'][] = $result_product_data;
