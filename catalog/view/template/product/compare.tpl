<?php echo $header; ?>
<main class="container-page compare-page">
    <header class="breadcrumb">
        <div class="layout">
            <h1><a href="<?php echo $reset; ?>"><i class="fa fa-copy"></i><?php echo $heading_title; ?></a></h1>
            <div class="links">
                <?php foreach ($breadcrumbs as $breadcrumb) { ?>
                <?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
                <?php } ?>
            </div>
        </div>
    </header>
    <div class="layout">
        <?php echo $column_left; ?>
        <section class="container-center">
            <div class="content-page">
                <?php echo $notification; ?>
                <?php echo $content_top; ?>

                <div class="global-page">

                    <?php if ($success) { ?>
                    <div class="success">
                        <p><?php echo $success; ?></p>
                        <span class="close"><i class="fa fa-times"></i></span>
                        <span class="icon"><i class="fa fa-check"></i></span>
                    </div>
                    <?php } ?>

                    <?php if ($products) { ?>
                      <table class="compare-info">
                        <thead class="hidden-medium">
                          <tr>
                            <td></td>
                            <td class="compare-product" colspan="<?php echo count($products); ?>"><?php echo $text_product; ?></td>
                          </tr>
                        </thead>
                        <tbody>
                          <tr>
                            <td><?php echo $text_name; ?></td>
                            <?php foreach ($products as $product) { ?>
                            <td class="name"><a href="<?php echo $products[$product['product_id']]['href']; ?>"><?php echo $products[$product['product_id']]['name']; ?></a></td>
                            <?php } ?>
                          </tr>
                          <tr class="thumb">
                            <td><?php echo $text_image; ?></td>
                            <?php foreach ($products as $product) { ?>
                            <td><?php if ($products[$product['product_id']]['thumb']) { ?>
                                <div class="image image-border">
                                    <a href="<?php echo $products[$product['product_id']]['href']; ?>">
                                        <img src="<?php echo $products[$product['product_id']]['thumb']; ?>" alt="<?php echo $products[$product['product_id']]['name']; ?>" />
                                    </a>
                                </div>
                              <?php } ?></td>
                            <?php } ?>
                          </tr>
                          <tr>
                            <td><?php echo $text_manufacturer; ?></td>
                            <?php foreach ($products as $product) { ?>
                            <td><a href="<?php echo $products[$product['product_id']]['manufacturer_href']; ?>"><img src="<?php echo $products[$product['product_id']]['manufacturer_image']; ?>" alt="<?php echo $products[$product['product_id']]['manufacturer']; ?>" /></a><br />
                            <a href="<?php echo $products[$product['product_id']]['manufacturer_href']; ?>"><?php echo $products[$product['product_id']]['manufacturer']; ?></a></td>
                            <?php } ?>
                          </tr>
                          <tr>
                            <td><?php echo $text_model; ?></td>
                            <?php foreach ($products as $product) { ?>
                            <td><?php echo $products[$product['product_id']]['model']; ?></td>
                            <?php } ?>
                          </tr>
                          <tr>
                            <td><?php echo $text_size; ?></td>
                            <?php foreach ($products as $product) { ?>
                            <td><?php echo $products[$product['product_id']]['size']; ?></td>
                            <?php } ?>
                          </tr>
                          <tr>
                            <td><?php echo $text_year; ?></td>
                            <?php foreach ($products as $product) { ?>
                            <td><?php echo $products[$product['product_id']]['year'] != '0000' ? $products[$product['product_id']]['year'] : $text_unknown; ?></td>
                            <?php } ?>
                          </tr>
                          <tr>
                            <td><?php echo $text_member; ?></td>
                            <?php foreach ($products as $product) { ?>
                            <?php if ($products[$product['product_id']]['member']) { ?>
                            <td><a href="<?php echo $products[$product['product_id']]['member']['href']; ?>"><?php echo $products[$product['product_id']]['member']['name']; ?></a></td>
                            <?php } else { ?>
                            <td></td>
                            <?php } ?>
                            <?php } ?>
                          </tr>
                          <tr>
                            <td><?php echo $text_location; ?></td>
                            <?php foreach ($products as $product) { ?>
                            <?php if ($products[$product['product_id']]['location']) { ?>
                            <td><?php echo $products[$product['product_id']]['location']; ?> - <a href="<?php echo $products[$product['product_id']]['location_href']; ?>"><?php echo $products[$product['product_id']]['location_zone'] . ', ' . $products[$product['product_id']]['location_country']; ?></a></td>
                            <?php } else { ?>
                            <td></td>
                            <?php } ?>
                            <?php } ?>
                          </tr>
                          <tr>
                            <td><?php echo $text_type; ?></td>
                            <?php foreach ($products as $product) { ?>
                            <td><?php echo $products[$product['product_id']]['type']; ?></td>
                            <?php } ?>
                          </tr>
                          <tr>
                            <td><?php echo $text_price; ?></td>
                            <?php foreach ($products as $product) { ?>
                            <td><div class="price">
                                <?php if ($products[$product['product_id']]['price'] && $products[$product['product_id']]['type_id'] >= 0) { ?>
                                <div class="all-price">
                                    <?php if (!$products[$product['product_id']]['special']) { ?>
                                    <?php echo $products[$product['product_id']]['price']; ?>
                                    <?php } else { ?>
                                    <span class="price-old"><span class="span1">&nbsp;</span><span class="span2">&nbsp;</span> <?php echo $products[$product['product_id']]['price']; ?></span>
                                    <?php echo $products[$product['product_id']]['special']; ?>
                                        <i class="badges sale-badges">-<?php echo $product['salebadges']; ?>&#37;</i>
                                    <?php } ?>
                                </div>
                              <?php } else if ($products[$product['product_id']]['price'] != $text_free && $products[$product['product_id']]['type_id'] < 0) { ?>
                              <?php echo $products[$product['product_id']]['price']; ?>
                              <?php } ?></div></td>
                            <?php } ?>
                          </tr>
                        </tbody>
                        <?php foreach ($attribute_groups as $attribute_group) { ?>
                        <thead>
                          <tr>
                            <td class="compare-attribute" colspan="<?php echo count($products) + 1; ?>"><?php echo $attribute_group['name']; ?></td>
                          </tr>
                        </thead>
                        <?php foreach ($attribute_group['attribute'] as $key => $attribute) { ?>
                        <tbody>
                          <tr>
                            <td><?php echo $attribute['name']; ?></td>
                            <?php foreach ($products as $product) { ?>
                            <?php if (isset($products[$product['product_id']]['attribute'][$key])) { ?>
                            <td><?php echo $products[$product['product_id']]['attribute'][$key]; ?></td>
                            <?php } else { ?>
                            <td></td>
                            <?php } ?>
                            <?php } ?>
                          </tr>
                        </tbody>
                        <?php } ?>
                        <?php } ?>
                        <tr>
                          <td></td>
                          <?php foreach ($products as $product) { ?>
                          <td>
              					<?php if ($product['type_id'] < 0) { ?>
              					<a href="<?php echo $product['href']; ?>" class="button button_alt" rel="tooltip" data-placement="top" data-original-title="<?php echo $text_view; ?>">
              						<i class="fa fa-info-circle"></i><?php echo $text_view; ?>
              					</a>
                                <?php } else if ($product['price'] && $product['type_id'] > 0) { ?>
                                <a onclick="addToCart('<?php echo $product['product_id']; ?>');" class="button button_highlight" rel="tooltip" data-placement="top" data-original-title="<?php echo $button_cart; ?>">
                                    <i class="fa fa-shopping-cart"></i><?php echo $button_cart; ?>
                                </a>
                                <?php } else if ($product['member']) { ?>
    						    <a href="<?php echo $product['member']['contact']; ?>" class="button button_contact" rel="tooltip" data-placement="top" data-original-title="<?php echo $button_contact; ?>">
                                    <i class="fa fa-envelope"></i> <?php echo $button_contact; ?>
                                </a>
    						    <?php } ?>
                                <a href="<?php echo $product['remove']; ?>" class="button b_solo_icon button_remove" data-original-title="<?php echo $button_remove; ?>" data-placement="top" rel="tooltip"><i class="fa fa-trash"></i></a>
                          </td>
                          <?php } ?>
                        </tr>
                      </table>

                    <?php } else { ?>

                    <div class="information">
                        <p><?php echo $text_empty; ?></p>
                        <span class="icon"><i class="fa fa-info-circle"></i></span>
                    </div>

                    <?php } ?>

    				<div class="buttons">
    					<div class="left"><a href="<?php echo $search; ?>" class="button button_search"><i class="fa fa-search"></i><?php echo $button_search; ?></a></div>
    					<div class="right"><a href="<?php echo $continue; ?>" class="button button_alt button_home"><i class="fa fa-home"></i><?php echo $button_continue; ?></a></div>
    				</div>

                </div>
                <?php echo $content_bottom; ?>
            </div>
        </section>
        <?php echo $column_right; ?>
    </div>
</main>
<?php echo $footer; ?>
