<article class="featured-listing item slideshow-item">
    <div class="image image-no-border" title="<?php echo $product['name']; ?>" rel="tooltip" data-container="body">
        <?php if ($product['href']) { ?>
        <a href="<?php echo $product['href']; ?>"><img src="<?php echo $product['thumb']; ?>" alt="<?php echo $product['name']; ?>" /></a>
        <?php } else { ?>
        <img src="<?php echo $product['thumb']; ?>" alt="<?php echo $product['name']; ?>" />
        <?php } ?>
        <span class="description">
            <a href="<?php echo $product['href']; ?>" title="<?php echo $product['description']; ?>">
            <?php if ($product['year'] != '0000') { ?>
                <?php echo $product['year']; ?>&nbsp;
            <?php } ?>
            <?php if ($product['manufacturer_id'] > 1) { ?>
                <?php echo $product['manufacturer']; ?>&nbsp;
            <?php } ?>
            <?php echo $product['model']; ?>&nbsp;<?php echo $product['size']; ?>
            <?php if ($product['price'] && $product['type_id'] >= 0 ) { ?>
            <span class="price">
                <?php if (!$product['special']) { ?>
                <span class="price-top"><?php echo $product['price']; ?></span>
                <?php } else { ?>
                <span class="price-old"><?php echo $product['price']; ?></span>
                <span class="price-new"><?php echo $product['special']; ?></span>
                <i class="badges sale-badges" rel="tooltip" data-placement="top" data-original-title="<?php echo $text_save; ?> <?php echo $product['savebadges']; ?>">-<?php echo $product['salebadges']; ?>&#37;</i>
                <?php } ?>
                <?php if ($product['tax']) { ?>
                <span class="price-tax"><?php echo $text_tax; ?> <?php echo $product['tax']; ?></span>
                <?php } ?>
            </span>
            <?php } ?>
            </a>
        </span>
    </div>
</article>
