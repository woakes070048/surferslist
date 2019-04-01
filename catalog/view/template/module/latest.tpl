<?php if ($products) { ?>
<?php if ($position == 'content_top' || $position == 'content_bottom') { ?>
<div class="widget-module widget-latest">
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
<div class="widget widget-latest">
	<h6><?php echo $heading_title; ?></h6>
    <div id="runlatestscroll" class="vertical-scroll-box runitemsscroll widget-list">
        <?php echo $products; ?>
    </div>
</div>
<?php } ?>
<?php } ?>
