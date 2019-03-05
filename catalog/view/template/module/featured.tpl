<?php if ($products) { ?>
<?php if ($position == 'content_top' || $position == 'content_bottom') { ?>
<div class="widget-module widget-featured">
    <div class="filterscat">
    	<div class="grid global-grid-item">
    		<div class="grid-sizer"></div>
            <?php echo $products; ?>
    	</div>
    	<div class="buttons">
    		<div class="center">
    			<a class="button button_highlight button_more bigger load-more icon" href="<?php echo $more; ?>"><i class="fa fa-chevron-down"></i><?php echo $text_more; ?></a>
    		</div>
    	</div>
    </div>
</div>
<?php } ?>
<?php if ($position == 'column_left' || $position == 'column_right') { ?>
<div id="featured-listings" class="widget widget-slideshow hidden-medium">
	<h6><?php echo $heading_title; ?></h6>
    <div class="content slideshow">
    	<div class="loader"></div>
    	<div class="slideshow-carousel" style="width:<?php echo $image_width . 'px'; ?>;height:<?php echo $image_height . 'px'; ?>">
            <?php echo $products; ?>
        </div>
        <input type="hidden" name="slideshow_options" value="<?php echo $slider_options; ?>" />
    </div>
    <div class="buttons">
    	<div class="center">
    		<a class="button button_alt button_more" href="<?php echo $more; ?>"><i class="fa fa-tags"></i><?php echo $text_view_all; ?></a>
    	</div>
    </div>
</div>
<?php } ?>
<?php } ?>
