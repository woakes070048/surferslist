<div id="filterwidget" class="widget widget-filter">
	<h6><?php echo $heading_title; ?></h6>
    <div class="content">
    <?php foreach ($filter_groups as $filter_group) { ?><div class="formbox">
        <p><strong id="filter-group<?php echo $filter_group['filter_group_id']; ?>"><?php echo $filter_group['name']; ?></strong></p>
        <div>
            <?php foreach ($filter_group['filter'] as $filter) { ?>
                <?php if (in_array($filter['filter_id'], $filter_category)) { ?>
                <span class="label label-checked"><input type="checkbox" value="<?php echo $filter['filter_id']; ?>" id="filter<?php echo $filter['filter_id']; ?>" checked="checked" /><label for="filter<?php echo $filter['filter_id']; ?>"><?php echo $filter['name']; ?></label></span>
                <?php } else { ?>
                <span class="label"><input type="checkbox" value="<?php echo $filter['filter_id']; ?>" id="filter<?php echo $filter['filter_id']; ?>" /><label for="filter<?php echo $filter['filter_id']; ?>"><?php echo $filter['name']; ?></label></span>
                <?php } ?>
            <?php } ?>
        </div>
    </div><?php } ?>
    </div>
    <div class="buttons">
    	<div class="left"><a id="button-filter" href="<?php echo $action; ?>" class="button"><i class="fa fa-filter"></i><?php echo $button_filter; ?></a></div>
    </div>
</div>
