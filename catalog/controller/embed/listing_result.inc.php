<?php
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

$thumb = $this->model_tool_image->resize($result['image'], $this->config->get('config_image_product_width'), $this->config->get('config_image_product_height'), 'fw');
$image = $this->model_tool_image->resize($result['image'], $this->config->get('config_image_thumb_width'), $this->config->get('config_image_thumb_height'), 'autocrop');

$this->data['products'][$result['product_id']] = array(
    // 'href'              => $this->url->link('embed/listing', 'listing_id=' . $result['product_id'] . $url, 'SSL'),
    'product_id'        => $result['product_id'],
    'customer_id'       => $result['customer_id'],
    'member_id'         => $result['member_id'],
    'member'            => isset($result['member']) ? $result['member'] : '',
	// 'member_href'       => $this->url->link('embed/profile', 'profile_id=' . $result['member_id'], 'SSL'),
	// 'member_contact'    => $this->url->link('information/contact', 'contact_id=' . $result['customer_id'], 'SSL'),
    'manufacturer_id'   => $result['manufacturer_id'],
    'manufacturer'      => $result['manufacturer'],
    // 'manufacturer_image' => $this->model_tool_image->resize($result['manufacturer_image'], 100, 40, "fh"),
    // 'manufacturer_href' => $this->url->link('embed/profile', 'filter_manufacturer_id=' . $result['manufacturer_id'], 'SSL'),
    'type_id'           => $result['type_id'],
    'type'              => $result['type_id'] == 1 ? $this->language->get('text_buy_now') : ($result['type_id'] == 0 ? $this->language->get('text_classified') : $this->language->get('text_shared')),
    'name'              => $result['name'],
    'model'             => $result['model'],
    'size'              => $result['size'],
    'year'              => $result['year'],
    'thumb'             => $thumb,
    'image'             => $image,
    'description'       => utf8_substr(remove_links(preg_replace('/\s+/', ' ', strip_tags_decode($result['description']))), 0, 80) . $this->language->get('text_ellipses'),
    'quantity'          => $result['quantity'],
    'quickview'         => $this->url->link('embed/quickview', 'listing_id=' . $result['product_id'] . $url, 'SSL'),
    'price'             => $price,
    'price_value'       => $result['price'],
    'special'           => $special,
    'special_value'     => $result['special'],
    'salebadges'        => $salebadges,
    'savebadges'        => $savebadges,
    'featured'          => $result['featured'],
    'tax'               => $tax,
    // 'date_added'        => $result['date_added'],
    // 'date_modified'     => $result['date_modified'],
    // 'date_available'    => $result['date_available'],
    // 'date_expiration'   => $result['date_expiration'],
    // 'sort_order'        => $result['sort_order'],
    // 'viewed'            => $result['viewed'],
    // 'attribute'         => isset($attribute_data) ? $attribute_data : array(),
    // 'weight'            => $this->weight->format($result['weight'], $result['weight_class_id']),
	// 'length'            => $this->length->format($result['length'], $result['length_class_id']),
	// 'width'             => $this->length->format($result['width'], $result['length_class_id']),
	// 'height'            => $this->length->format($result['height'], $result['length_class_id']),
	'location'          => isset($result['location']) ? $result['location'] : '',
	'location_zone'     => isset($location_zone) ? $location_zone['name'] : '',
	'location_country'  => isset($location_country) ? $location_country['iso_code_3'] : ''
);
