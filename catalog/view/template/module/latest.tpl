<?php if ($products) { ?>
<?php if ($position == 'content_top' || $position == 'content_bottom') { ?>
<div class="widget-module widget-latest">
    <?php require('listings.inc.php'); ?>
</div>
<?php } ?>
<?php if ($position == 'column_left' || $position == 'column_right') { ?>
<div class="widget widget-latest">
	<h6><?php echo $heading_title; ?></h6>
    <div id="runlatestscroll" class="vertical-scroll-box runitemsscroll widget-list">
        <?php foreach ($products as $product) { ?>
            <?php require('listing_mini.inc.php'); ?>
        <?php } ?>
    </div>
</div>
<?php } ?>
<?php } ?>
