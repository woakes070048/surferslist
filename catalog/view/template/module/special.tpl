<?php if ($products) { ?>
<?php if ($position == 'content_top' || $position == 'content_bottom') { ?>
<div class="widget-module widget-special">
    <?php require('listings.inc.php'); ?>
</div>
<?php } ?>
<?php if ($position == 'column_left' || $position == 'column_right') { ?>
<div class="widget widget-special">
	<h6><?php echo $heading_title; ?></h6>
    <div id="runspecialscroll" class="vertical-scroll-box runitemsscroll widget-list">
        <?php foreach ($products as $product) { ?>
            <?php require('listing_mini.inc.php'); ?>
        <?php } ?>
    </div>
    <div class="buttons">
    	<div class="center">
    		<a class="button button_alt button_more" href="<?php echo $more; ?>"><i class="fa fa-th-list"></i><?php echo $text_more; ?></a>
    	</div>
    </div>
</div>
<?php } ?>
<?php } ?>
