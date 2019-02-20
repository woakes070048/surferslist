<?php
$price = false;
$special = false;
$salebadges = false;
$savebadges = false;
$tax = false;

if (!isset($image_crop)) {
    $image_crop = 'autocrop';
}

$image = $result['image']
    ? $this->model_tool_image->resize($result['image'], $image_width, $image_height, $image_crop)
    : $this->model_tool_image->resize('no_image.jpg', $this->config->get('config_image_product_width'), $this->config->get('config_image_product_height'), $image_crop);

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

$description = remove_links(preg_replace('/\s+/', ' ', strip_tags_decode($result['description'])));

$this->data['products'][$result['product_id']] = array(
    'href'              => $this->url->link('product/product', 'product_id=' . $result['product_id'], 'SSL'),
    'product_id'        => $result['product_id'],
    'manufacturer_id'   => $result['manufacturer_id'],
    'manufacturer'      => $result['manufacturer'],
    'customer_id'       => $result['customer_id'],
    'member_id'         => $result['member_id'],
    'type_id'           => $result['type_id'],
    'type'              => $result['type_id'] == 1 ? $this->language->get('text_buy_now') : ($result['type_id'] == 0 ? $this->language->get('text_classified') : $this->language->get('text_shared')),
    'name'              => $result['name'],
    'model'             => $result['model'],
    'size'              => $result['size'],
    'year'              => $result['year'],
    'thumb'             => $image,
    'description'       => utf8_strlen($description) > 80 ? utf8_substr($description, 0, 80) . $this->language->get('text_ellipses') : $description,
    'quantity'          => $result['quantity'],
    'quickview'         => $this->url->link('product/quickview', 'listing_id=' . $result['product_id'], 'SSL'),
    'price'             => $price,
    'special'           => $special,
    'salebadges'        => $salebadges,
    'savebadges'        => $savebadges,
    'featured'          => $result['featured'],
    'tax'               => $tax,
    'date_added'        => $result['date_added'],
    // 'date_modified'     => $result['date_modified'],
    // 'date_available'    => $result['date_available'],
    // 'date_expiration'   => $result['date_expiration'],
    'sort_order'        => $result['sort_order']
    // , 'viewed'            => $result['viewed']
);
