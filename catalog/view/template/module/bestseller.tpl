<?php if ($products) { ?>
<?php if ($position == 'content_top' || $position == 'content_bottom') { ?>
<div class="widget-module widget-popular">
    <div class="grid-items-container">
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
<div class="widget widget-popular">
	<h6><?php echo $heading_title; ?></h6>
    <div id="runpopularscroll" class="vertical-scroll-box runitemsscroll widget-list">
        <?php echo $products; ?>
    </div>
    <div class="buttons">
    	<div class="center">
    		<a class="button button_alt button_more" href="<?php echo $more; ?>"><i class="fa fa-th-list"></i><?php echo $text_more; ?></a>
    	</div>
    </div>
</div>
<?php } ?>
<?php } ?>
