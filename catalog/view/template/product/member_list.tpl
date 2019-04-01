<?php echo $header; ?>
<main class="container-page members-page">
    <header class="breadcrumb">
        <div class="layout">
            <h1><a href="<?php echo $reset; ?>"><i class="fa fa-group"></i><?php echo $heading_title; ?></a></h1>
            <div class="links">
                <?php foreach ($breadcrumbs as $breadcrumb) { ?>
                <?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
                <?php } ?>
            </div>
        </div>
    </header>
    <div class="layout">
        <aside id="sidebar" class="sidebar container-left">
            <?php echo $column_left; ?>
            <div class="widget profile-filter widget-ac-container collapse-medium">
    	        <div class="widget-filter widget-ac">
    	            <h6 class="widget-ac-active"><?php echo $heading_filter; ?><span><i class="fa fa-chevron-down icon-ac-down"></i><i class="fa fa-chevron-up icon-ac-up"></i></span></h6>
                    <div class="ac-content">
                        <div class="content">
                            <div class="filter name-filter" title="<?php echo $help_name; ?>" rel="tooltip" data-container="body">
                                <label for="filter_name" class="hidden"><?php echo $text_filter_name; ?></label>
                                <input type="text" name="filter_name" value="<?php echo $filter_name; ?>" placeholder="<?php echo $text_filter_name; ?>" />
                                <input type="hidden" name="reset_url" value="<?php echo $reset; ?>" />
                            </div>
                            <?php if ($countries) { ?>
                            <div class="filter country-filter" title="<?php echo $help_country; ?>" rel="tooltip" data-container="body">
                              <label for="filter_country_id" class="hidden"><?php echo $text_filter_country; ?></label>
                              <select name="filter_country_id" onchange="filterListings();">
                                <?php foreach ($countries as $country) { ?>
                                <?php if ($country['id'] == $filter_country_id) { ?>
                                <option value="<?php echo $country['id']; ?>" selected="selected"><?php echo $country['name']; ?></option>
                                <?php } else { ?>
                                <option value="<?php echo $country['id']; ?>"><?php echo $country['name']; ?></option>
                                <?php } ?>
                                <?php } ?>
                              </select>
                            </div>
                            <?php } ?>
                            <div class="filter zone-filter" title="<?php echo $help_zone; ?>" rel="tooltip" data-container="body">
                              <label for="filter_zone_id" class="hidden"><?php echo $text_filter_zone; ?></label>
                              <select name="filter_zone_id" onchange="filterListings();"<?php if (!$zones || !$filter_country_id) { ?> disabled="disabled"<?php } ?>>
                                <?php foreach ($zones as $zone) { ?>
                                <?php if ($zone['id'] == $filter_zone_id) { ?>
                                <option value="<?php echo $zone['id']; ?>" selected="selected"><?php echo $zone['name']; ?></option>
                                <?php } else { ?>
                                <option value="<?php echo $zone['id']; ?>"><?php echo $zone['name']; ?></option>
                                <?php } ?>
                                <?php } ?>
                              </select>
                            </div>
                            <div class="filter member-filter clearafter" title="<?php echo $help_member; ?>">
							  <?php foreach ($member_groups as $member_group) { ?>
								<?php if (in_array($member_group['group_id'], $filter_member)) { ?>
								<span class="label grid-2">
                                    <input type="checkbox" name="filter_member[]" value="<?php echo $member_group['group_id']; ?>" id="member-<?php echo $member_group['group_id']; ?>" checked="checked" />
                                    <label for="member-<?php echo $member_group['group_id']; ?>"><?php echo $member_group['name']; ?></label>
                                </span>
								<?php } else { ?>
								<span class="label grid-2">
                                    <input type="checkbox" name="filter_member[]" value="<?php echo $member_group['group_id']; ?>" id="member-<?php echo $member_group['group_id']; ?>" />
                                    <label for="member-<?php echo $member_group['group_id']; ?>"><?php echo $member_group['name']; ?></label>
                                </span>
								<?php } ?>
							  <?php } ?>
							</div>
                            <?php if ($sorts) { ?>
                            <div class="sort" title="<?php echo $help_sort; ?>" rel="tooltip" data-container="body">
                                <label for="sort" class="hidden"><?php echo $text_sort; ?></label>
                                <select name="sort" onchange="location = this.value;">
                                    <?php foreach ($sorts as $sort_option) { ?>
                                    <?php if ($sort_option['value'] == $sort . '-' . $order) { ?>
                                    <option value="<?php echo $sort_option['href']; ?>" selected="selected"><?php echo $sort_option['text']; ?></option>
                                    <?php } else { ?>
                                    <option value="<?php echo $sort_option['href']; ?>"><?php echo $sort_option['text']; ?></option>
                                    <?php } ?>
                                    <?php } ?>
                                </select>
                            </div>
                            <?php } ?>
                            <div class="limit" title="<?php echo $help_limit; ?>" rel="tooltip" data-container="body">
                                <label for="limit" class="hidden"><?php echo $text_limit; ?></label>
                                <select name="limit" onchange="location = this.value;">
                                <?php foreach ($limits as $limit_option) { ?>
                                <?php if ($limit_option['value'] == $limit) { ?>
                                <option value="<?php echo $limit_option['href']; ?>" selected="selected"><?php echo $limit_option['text']; ?></option>
                                <?php } else { ?>
                                <option value="<?php echo $limit_option['href']; ?>"><?php echo $limit_option['text']; ?></option>
                                <?php } ?>
                                <?php } ?>
                              </select>
                            </div>
                            <div class="formbox">
                                <span class="label">
                                    <a href="<?php echo $location_page; ?>" id="button-location" title="<?php echo $text_set_location; ?>"><i class="fa fa-globe"></i>&nbsp;<?php echo $text_location; ?></a>
                                </span>
                            </div>
                        </div>
                        <div class="buttons">
                            <div class="center">
                                <a id="button-filter" onclick="filterListings();" class="button">
                                    <i class="fa fa-filter"></i> <?php echo $button_filter; ?>
                                </a>
                                <div class="product-filter">
                                    <a href="<?php echo $reset; ?>" class="float-left grey-text"><i class="fa fa-refresh"></i><?php echo $button_reset; ?></a>
                                    <a href="<?php echo $random; ?>" id="random"><i class="fa fa-random"></i><?php echo $text_random; ?></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php echo $column_right; ?>
        </aside>
        <section class="container-center">
            <div class="content-page">
                <?php echo $notification; ?>
                <?php echo $content_top; ?>

                <?php if ($members) { ?>
                <div id="grid-items-container">
                	<div class="grid global-grid-item">
                	<div class="grid-sizer"></div>
                    <?php foreach ($members as $member) { ?>
                    <article class="grid-item runmember" data-filter-class='["<?php echo $member['key']; ?>"]'>
                        <div class="image">
                            <?php if ($member['image']) { ?>
                            <a href="<?php echo $member['href']; ?>" title="<?php echo $member['name']; ?>">
                                <img src="<?php echo $member['image']; ?>" alt="<?php echo $member['name']; ?>" />
                            </a>
                            <?php } ?>
            				<span class="description">
                                <a href="<?php echo $member['href']; ?>" title="<?php echo $member['name']; ?>">
                					<?php if ($member['text_products']) { ?>
                					<span class="listings">
                						<?php echo $member['text_products']; ?>
                					</span>
                					<?php } ?><?php if (!$custom_fields && isset($member['rating'])) { ?>
                                    <span class="rating">
                                        <span class="rating-stars view-star<?php echo $member['rating']; ?>">
                                            <i class="fa fa-star icon_star star-color color1"></i>
                                            <i class="fa fa-star icon_star star-color color2"></i>
                                            <i class="fa fa-star icon_star star-color color3"></i>
                                            <i class="fa fa-star icon_star star-color color4"></i>
                                            <i class="fa fa-star icon_star star-color color5"></i>
                                            <i class="fa fa-star icon_star star-dark dark1"></i>
                                            <i class="fa fa-star icon_star star-dark dark2"></i>
                                            <i class="fa fa-star icon_star star-dark dark3"></i>
                                            <i class="fa fa-star icon_star star-dark dark4"></i>
                                            <i class="fa fa-star icon_star star-dark dark5"></i>
                                        </span>
                                    </span>
                                    <?php } ?>
                                </a>
            				</span>
                        </div>
                        <div class="pannel">
                            <div class="info">
                                <header>
            						<h3><a href="<?php echo $member['href']; ?>" title="<?php echo $member['name']; ?>"><?php echo $member['name']; ?></a></h3>
                    					<?php if ($custom_fields && isset($member['rating'])) { ?>
                    					<span class="rating" title="<?php echo $member['help_member_rating']; ?>" rel="tooltip" data-container="body">
                    						<span class="rating-stars view-star<?php echo $member['rating']; ?>">
                    							<i class="fa fa-star icon_star star-color color1"></i>
                    							<i class="fa fa-star icon_star star-color color2"></i>
                    							<i class="fa fa-star icon_star star-color color3"></i>
                    							<i class="fa fa-star icon_star star-color color4"></i>
                    							<i class="fa fa-star icon_star star-color color5"></i>
                    							<i class="fa fa-star icon_star star-dark dark1"></i>
                    							<i class="fa fa-star icon_star star-dark dark2"></i>
                    							<i class="fa fa-star icon_star star-dark dark3"></i>
                    							<i class="fa fa-star icon_star star-dark dark4"></i>
                    							<i class="fa fa-star icon_star star-dark dark5"></i>
                    						</span>
                    					</span>
                    					<?php } ?>
            					</header>
            					<?php if ($custom_fields) { ?>
            					<footer class="member-socials">
            						<a href="<?php echo $member['member_custom_field_01']; ?>" target="_blank" <?php if (!$entry_member_custom_field_01 || empty($member['member_custom_field_01'])) echo 'style="display:none;"'; ?>><span class="website social" title="<?php echo $entry_member_custom_field_01; ?>" rel="tooltip" data-container="body"><i class="fa fa-globe"></i></span></a>
            						<a href="<?php echo $member['member_custom_field_02']; ?>" target="_blank" <?php if (!$entry_member_custom_field_02 || empty($member['member_custom_field_02'])) echo 'style="display:none;"'; ?>><span class="twitter social" title="<?php echo $entry_member_custom_field_02; ?>" rel="tooltip" data-container="body"><i class="fa fa-twitter"></i></span></a>
            						<a href="<?php echo $member['member_custom_field_03']; ?>" target="_blank" <?php if (!$entry_member_custom_field_03 || empty($member['member_custom_field_03'])) echo 'style="display:none;"'; ?>><span class="facebook social" title="<?php echo $entry_member_custom_field_03; ?>" rel="tooltip" data-container="body"><i class="fa fa-facebook"></i></span></a>
                                    <a href="<?php echo $member['member_custom_field_05']; ?>" target="_blank" <?php if (!$entry_member_custom_field_05 || empty($member['member_custom_field_05'])) echo 'style="display:none;"'; ?>><span class="instagram social" title="<?php echo $entry_member_custom_field_05; ?>" rel="tooltip" data-container="body"><i class="fa fa-instagram"></i></span></a>
            						<a href="<?php echo $member['member_custom_field_04']; ?>" target="_blank" <?php if (!$entry_member_custom_field_04 || empty($member['member_custom_field_04'])) echo 'style="display:none;"'; ?>><span class="pinterest social" title="<?php echo $entry_member_custom_field_04; ?>" rel="tooltip" data-container="body"><i class="fa fa-pinterest"></i></span></a>
            						<a href="<?php echo $member['member_custom_field_06']; ?>" target="_blank" <?php if (!$entry_member_custom_field_06 || empty($member['member_custom_field_06'])) echo 'style="display:none;"'; ?>><span class="other social" title="<?php echo $entry_member_custom_field_06; ?>" rel="tooltip" data-container="body"><i class="fa fa-link"></i></span></a>
            					</footer>
            					<?php } ?>
                            </div>
                        </div>
                    </article>
                    <?php } ?>
                    </div>
                </div>

                <?php if (isset($pagination)) { ?>
                <div class="pagination"><?php echo $pagination; ?></div>
                <?php } ?>

				<?php } else { ?>

				<div class="global-page">
                    <div class="information">
                        <p><?php echo $text_empty_members; ?></p>
                        <span class="icon"><i class="fa fa-info-circle"></i></span>
                    </div>
                	<div class="buttons">
                		<div class="left"><a href="<?php echo $back; ?>" class="button button_back"><i class="fa fa-undo"></i><?php echo $button_back; ?></a> <a href="<?php echo $reset; ?>" class="button button_alt button_reset"><i class="fa fa-refresh"></i><?php echo $button_reset; ?></a></div>
                		<div class="right"><a href="<?php echo $search; ?>" class="button button_search"><i class="fa fa-search"></i><?php echo $button_search; ?></a> <a href="<?php echo $continue; ?>" class="button button_alt button_home"><i class="fa fa-home"></i><?php echo $button_continue; ?></a></div>
                	</div>
                </div>
                <?php } ?>

                <?php echo $content_bottom; ?>
            </div>
        </section>
    </div>
</main>
<?php echo $footer; ?>
