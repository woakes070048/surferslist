<div id="filterwidget" class="widget listing-filter widget-ac-container collapse-medium">
    <div class="widget-filter widget-ac">
        <h6 class="widget-ac-active"><?php echo $heading_filter_listings; ?><span><i class="fa fa-chevron-down icon-ac-down"></i><i class="fa fa-chevron-up icon-ac-up"></i></span></h6>
        <div class="ac-content">
            <div class="content">
                <?php if (!empty($parent_categories)) { ?>
                <?php foreach ($parent_categories as $parent_category_id => $parent_category) { ?>
                <div class="filter category-filter" title="<?php echo $help_category; ?>" rel="tooltip" data-container="body">
                  <label for="parent-category-<?php echo $parent_category_id; ?>-filter" class="hidden"><?php echo $text_filter_category; ?></label>
                  <select name="parent-category-<?php echo $parent_category_id; ?>-filter" id="parent-category-<?php echo $parent_category_id; ?>-filter" onchange="location = this.value;">
                    <?php foreach ($parent_category as $parent_categories_data) { ?>
                    <?php if (in_array($parent_categories_data['id'], $category_hierarchy_ids)) { ?>
                    <option value="<?php echo $parent_categories_data['href']; ?>" data-filter="<?php echo $parent_categories_data['id']; ?>" selected="selected"><?php echo $parent_categories_data['name']; ?></option>
                    <?php } else { ?>
                    <option value="<?php echo $parent_categories_data['href']; ?>" data-filter="<?php echo $parent_categories_data['id']; ?>"><?php echo $parent_categories_data['name']; ?></option>
                    <?php } ?>
                    <?php } ?>
                  </select>
                </div>
                <?php } ?>
                <?php } ?>

                <?php if (count($categories) > 1) { ?>
                <div class="filter category-filter" title="<?php echo $help_category_sub; ?>" rel="tooltip" data-container="body">
                  <label for="category-filter" class="hidden"><?php echo $text_filter_category; ?></label>
                  <select name="category-filter" id="category-filter" onchange="location = this.value;">
                    <?php foreach ($categories as $category_data) { ?>
                    <?php if ($category_data['id'] == $filter_category_id) { ?>
                    <option value="<?php echo $category_data['href']; ?>" data-filter="<?php echo $category_data['id']; ?>" selected="selected"><?php echo $category_data['name']; ?></option>
                    <?php } else { ?>
                    <option value="<?php echo $category_data['href']; ?>" data-filter="<?php echo $category_data['id']; ?>"><?php echo $category_data['name']; ?></option>
                    <?php } ?>
                    <?php } ?>
                  </select>
                </div>
                <?php } ?>

                <?php if (count($manufacturers) > 1) { ?>
                <div class="filter manufacturer-filter" title="<?php echo $help_manufacturer; ?>" rel="tooltip" data-container="body">
                  <label for="manufacturer-filter" class="hidden"><?php echo $text_filter_manufacturer; ?></label>
                  <select name="manufacturer-filter" id="manufacturer-filter" onchange="location = this.value;">
                    <?php foreach ($manufacturers as $manufacturer_data) { ?>
                    <?php if (!empty($show_all_manufacturers) || !empty($manufacturer_data['product_count']) || $manufacturer_data['id'] == $filter_manufacturer_id || $manufacturer_data['id'] == 0) { ?>
                    <?php if ($manufacturer_data['id'] == $filter_manufacturer_id) { ?>
                    <option value="<?php echo $manufacturer_data['href']; ?>" data-filter="<?php echo $manufacturer_data['id']; ?>" selected="selected"><?php echo $manufacturer_data['name']; ?></option>
                    <?php } else { ?>
                    <option value="<?php echo $manufacturer_data['href']; ?>" data-filter="<?php echo $manufacturer_data['id']; ?>"><?php echo $manufacturer_data['name']; ?></option>
                    <?php } ?>
                    <?php } ?>
                    <?php } ?>
                  </select>
                </div>
                <?php } ?>

                <div class="filter name-filter" title="<?php echo $help_name; ?>" rel="tooltip" data-container="body">
                    <label for="filter_search" class="hidden"><?php echo $text_filter_name; ?></label>
                    <input type="text" name="filter_search" value="<?php echo $filter_search; ?>" placeholder="<?php echo $text_filter_name; ?>" />
                </div>

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

                <?php if ($filter_groups && $products) { ?>
                <div class="formbox">
                    <span class="label">
                        <a id="button-more-options"><i class="fa fa-plus-circle"></i><?php echo $text_more_options; ?></a>
                    </span>
                </div>
                <div id="container-more-options" data-show="<?php echo $display_more_options ? 'true' : 'false'; ?>" class="clearafter">
                    <div class="formbox" title="<?php echo $help_type; ?>">
                        <p><strong><?php echo $heading_filter_type; ?></strong></p>
                        <?php foreach ($listing_types as $listing_type) { ?>
                        <?php if (in_array($listing_type['type_id'], $type_selected)) { ?>
                        <span class="label label-checked grid-3">
                            <input type="checkbox"
                                name="type[]"
                                value="<?php echo $listing_type['type_id']; ?>"
                                id="type-<?php echo $listing_type['type_id']; ?>"
                                checked="checked" />
                            <label for="type-<?php echo $listing_type['type_id']; ?>">
                                <?php echo $listing_type['name']; ?>
                            </label>
                        </span>
                        <?php } else { ?>
                        <span class="label grid-3">
                            <input type="checkbox"
                                name="type[]"
                                value="<?php echo $listing_type['type_id']; ?>"
                                id="type-<?php echo $listing_type['type_id']; ?>" />
                            <label for="type-<?php echo $listing_type['type_id']; ?>">
                                <?php echo $listing_type['name']; ?>
                            </label>
                        </span>
                        <?php } ?>
                        <?php } ?>
                    </div>
                    <?php foreach ($filter_groups as $filter_group) { ?>
                    <div class="formbox grid-3">
                        <p><strong id="filter-group<?php echo $filter_group['filter_group_id']; ?>"><?php echo $filter_group['name']; ?></strong></p>
                        <div>
                            <?php foreach ($filter_group['filter'] as $filter) { ?>
                                <?php if (in_array($filter['filter_id'], $filter_category)) { ?>
                                <span class="label label-checked">
                                    <input type="checkbox"
                                        name="filter-<?php echo $filter_group['name']; ?>[]"
                                        value="<?php echo $filter['filter_id']; ?>"
                                        id="filter-<?php echo $filter['filter_id']; ?>"
                                        checked="checked" />
                                    <label for="filter-<?php echo $filter['filter_id']; ?>">
                                        <?php echo $filter['name']; ?>
                                    </label>
                                </span>
                                <?php } else { ?>
                                <span class="label">
                                    <input type="checkbox"
                                        name="filter-<?php echo $filter_group['name']; ?>[]"
                                        value="<?php echo $filter['filter_id']; ?>"
                                        id="filter<?php echo $filter['filter_id']; ?>" />
                                    <label for="filter<?php echo $filter['filter_id']; ?>">
                                        <?php echo $filter['name']; ?>
                                    </label>
                                </span>
                                <?php } ?>
                            <?php } ?>
                        </div>
                    </div>
                    <?php } ?>
                </div>
                <?php } ?>

                <?php if ($location_page) { ?>
                <div class="formbox">
                    <span class="label">
                        <a href="<?php echo $location_page; ?>" id="button-location" title="<?php echo $text_set_location; ?>">
                            <i class="fa fa-globe"></i><?php echo $text_location; ?>
                        </a>
                    </span>
                </div>
                <?php } ?>

            </div>
            <div class="buttons">
                <div class="left hidden">
                    <div class="formbox">
                        <span class="label">
                            <input type="checkbox" name="forsale" value="1" id="forsale" <?php if ($forsale) { ?>checked="checked"<?php } ?> />
                            <label for="forsale"><em><?php echo $text_forsale; ?></em></label>
                        </span>
                    </div>
                </div>
                <div class="center">
                    <a id="button-filter" href="<?php echo $action; ?>" class="button">
                        <i class="fa fa-filter"></i> <?php echo $button_filter; ?>
                    </a>
                    <?php if ($reset) { ?>
                    <a id="button-reset" href="<?php echo $reset; ?>" class="button button_alt">
                        <i class="fa fa-refresh"></i><?php echo $button_reset; ?>
                    </a>
                    <?php } ?>
                    <?php if ($compare) { ?>
                    <div class="product-filter">
                        <a href="<?php echo $random; ?>" id="random"><i class="fa fa-random"></i><?php echo $text_random; ?></a>
                        <a class="product-compare<?php if (!empty($this->session->data['compare'])) { ?> active<?php } ?> compare-total" href="<?php echo $compare; ?>"><?php echo $text_compare; ?></a>
                    </div>
                    <?php } ?>
                </div>
            </div>
        	<input type="hidden" name="text_hide_options" value="<?php echo $text_hide_options; ?>" />
        	<input type="hidden" name="text_more_options" value="<?php echo $text_more_options; ?>" />
        </div>
    </div>
</div>
