<div id="filterwidget" class="widget articles-filter widget-ac-container collapse-medium">
    <div class="widget-filter widget-ac">
        <h6 class="widget-ac-active"><?php echo $heading_filter_articles; ?><span><i class="fa fa-chevron-down icon-ac-down"></i><i class="fa fa-chevron-up icon-ac-up"></i></span></h6>
        <div class="ac-content">
            <div class="content">
                <?php if (count($categories) > 1) { ?>
                <div class="filter category-filter">
                    <ul class="filter-listings">
                        <?php foreach ($categories as $category) { ?>
                            <li data-filter="<?php echo $category['name']; ?>">
                                <a href="<?php echo $category['href']; ?>"><?php echo $category['name']; ?></a>
                            </li>
                        <?php } ?>
                    </ul>
                </div>
                <?php } ?>

                <div class="filter name-filter" title="<?php echo $help_name; ?>" rel="tooltip" data-container="body">
                    <label for="filter_search" class="hidden"><?php echo $text_search; ?></label>
                    <input type="text" name="filter_search" value="<?php echo $filter_search; ?>" placeholder="<?php echo $text_search; ?>" />
                </div>

                <div class="filter description-filter clearafter hidden" title="<?php echo $help_description; ?>" rel="tooltip" data-container="body">
                    <?php if ($filter_description) { ?>
                    <span class="label">
                        <input type="checkbox" name="description" value="1" id="description" checked="checked" />
                        <label for="description"><?php echo $help_description; ?></label>
                    </span>
                    <?php } else { ?>
                    <span class="label">
                        <input type="checkbox" name="description" value="1" id="description" />
                        <label for="description"><?php echo $help_description; ?></label>
                    </span>
                    <?php } ?>
                </div>

                <?php if (!$hide_sort_limit) { ?>
                <div class="sort" title="<?php echo $help_sort; ?>" rel="tooltip" data-container="body">
                    <label for="sort" class="hidden"><?php echo $text_sort; ?></label>
                    <select name="sort" onchange="location = this.value;">
                        <?php foreach ($sorts as $sort_data) { ?>
                        <?php if ($sort_data['value'] == $sort . '-' . $order) { ?>
                        <option value="<?php echo $sort_data['href']; ?>" selected="selected"><?php echo $sort_data['text']; ?></option>
                        <?php } else { ?>
                        <option value="<?php echo $sort_data['href']; ?>"><?php echo $sort_data['text']; ?></option>
                        <?php } ?>
                        <?php } ?>
                    </select>
                </div>

                <div class="limit" title="<?php echo $help_limit; ?>" rel="tooltip" data-container="body">
                    <label for="limit" class="hidden"><?php echo $text_limit; ?></label>
                    <select name="limit" onchange="location = this.value;">
                        <?php foreach ($limits as $limit_data) { ?>
                        <?php if ($limit_data['value'] == $limit) { ?>
                        <option value="<?php echo $limit_data['href']; ?>" selected="selected"><?php echo $limit_data['text']; ?></option>
                        <?php } else { ?>
                        <option value="<?php echo $limit_data['href']; ?>"><?php echo $limit_data['text']; ?></option>
                        <?php } ?>
                        <?php } ?>
                    </select>
                </div>
                <?php } ?>
            </div>
            <div class="buttons">
                <div class="center">
                    <a id="button-filter" href="<?php echo $search; ?>" class="button">
                        <i class="fa fa-search"></i> <?php echo $button_search; ?>
                    </a>
                </div>
                <div class="left">
                    <div class="product-filter">
                        <?php if ($reset) { ?>
                        <a href="<?php echo $reset; ?>" class="grid-2 grey-text"><i class="fa fa-refresh"></i><?php echo $button_reset; ?></a>
                        <?php } ?>
                        <a href="<?php echo $continue; ?>" class="grid-2 grey-text"><i class="fa fa-home"></i><?php echo $heading_blog_home; ?></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
