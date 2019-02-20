<div class="widget">
	<h6><?php echo $heading_title; ?></h6>
    <div id="runebayscroll" class="vertical-scroll-box runitemsscroll">
        <?php foreach ($products as $product) { ?>
        <div class="item-mini">
            <div class="image">
                <?php if ($product['thumb']) { ?>
                <a href="<?php echo $product['href']; ?>"><img src="<?php echo $product['thumb']; ?>" alt="<?php echo $product['name']; ?>" /></a>
                <?php } ?>
            </div>
            <div class="info">
                <h3><a href="<?php echo $product['href']; ?>" title="<?php echo $product['name']; ?>"><?php echo $product['name']; ?></a></h3>
                <div class="price">
                    <?php echo $product['price']; ?>
                </div>
            </div>
            <img src="<?php echo $tracking_pixel; ?>" height="0" width="0" />
        </div>
        <?php } ?>
    </div>
</div>
