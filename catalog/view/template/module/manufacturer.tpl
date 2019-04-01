<?php if ($manufacturers) { ?>
<?php if ($position == 'content_top' || $position == 'content_bottom') { ?>
<div class="widget-module horizontal-grid">
    <div class="grid-items-container">
        <div class="grid global-grid-item">
    		<div class="grid-sizer"></div>
            <?php foreach ($manufacturers as $manufacturer) { ?>
            <article class="grid-item runmanufacturer">
                <div class="image">
                    <?php if ($manufacturer['image']) { ?>
                    <a href="<?php echo $manufacturer['href']; ?>" title="<?php echo $manufacturer['name']; ?>">
                        <img src="<?php echo $manufacturer['image']; ?>" alt="<?php echo $manufacturer['name']; ?>" />
                    </a>
                    <?php } ?>
                    <span class="description">
                        <a href="<?php echo $manufacturer['href']; ?>" title="<?php echo $manufacturer['name']; ?>">
                            <?php if ($manufacturer['product_count']) { ?>
                            <span class="listings">
                                <?php echo $manufacturer['text_products']; ?>
                            </span>
                            <?php } ?>
                        </a>
                    </span>
                </div>
                <div class="pannel">
                    <div class="info">
                        <header>
                            <h3><a href="<?php echo $manufacturer['href']; ?>" title="<?php echo $manufacturer['name']; ?>"><?php echo $manufacturer['name']; ?></a></h3>
                        </header>
                    </div>
                </div>
            </article>
            <?php } ?>
        </div>
        <div class="buttons">
        	<div class="center">
        		<a class="button button_alt button_more" href="<?php echo $more; ?>"><i class="fa fa-ticket"></i><?php echo $text_more; ?></a>
        	</div>
        </div>
    </div>
</div>
<?php } ?>
<?php if ($position == 'column_left' || $position == 'column_right') { ?>
<div id="featured-brands" class="widget widget-slideshow hidden-medium">
	<h6><?php echo $heading_title; ?></h6>
    <div class="content slideshow">
    	<div class="loader"></div>
    	<div class="slideshow-carousel" style="width:<?php echo $image_width . 'px'; ?>;height:<?php echo $image_height . 'px'; ?>">
            <?php foreach ($manufacturers as $manufacturer) { ?>
            <article class="featured-brand item slideshow-item">
                <div class="image image-no-border" title="<?php echo $manufacturer['name']; ?>" rel="tooltip" data-container="body">
                    <?php if ($manufacturer['href']) { ?>
                    <a href="<?php echo $manufacturer['href']; ?>"><img src="<?php echo $manufacturer['image']; ?>" alt="<?php echo $manufacturer['name']; ?>" /></a>
                    <?php } else { ?>
                    <img src="<?php echo $manufacturer['image']; ?>" alt="<?php echo $manufacturer['name']; ?>" />
                    <?php } ?>
                    <span class="description">
                        <a href="<?php echo $manufacturer['href']; ?>">
                            <header><h3><?php echo $manufacturer['name']; ?></h3></header>
                            <?php if ($manufacturer['product_count']) { ?>
                            <span class="listings">
                                <?php echo $manufacturer['text_products']; ?>
                            </span>
                            <?php } ?>
                        </a>
                    </span>
                </div>
            </article>
            <?php } ?>
        </div>
        <input type="hidden" name="slideshow_options" value="<?php echo $slider_options; ?>" />
    </div>
    <div class="buttons">
    	<div class="center">
    		<a class="button button_alt button_more" href="<?php echo $more; ?>"><i class="fa fa-ticket"></i><?php echo $text_more; ?></a>
    	</div>
    </div>
</div>
<?php } ?>
<?php } ?>
