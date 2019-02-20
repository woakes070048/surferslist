<div class="widget widget-search<?php if ($search) { ?> search-keyword<?php } ?> widget-ac-container<?php if ($search) { ?> collapse-small<?php } ?>">
	<form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="searchwidget" class="widget-ac">
		<h6 class="widget-ac-active"><?php echo $heading_title; ?><span><i class="fa fa-chevron-down icon-ac-down"></i><i class="fa fa-chevron-up icon-ac-up"></i></span></h6>
		<div class="ac-content">
			<div class="content clearafter">
				<div class="grid-1 clearafter">
					<div class="grid-8">
						<div class="grid-2">
							<div class="formbox">
								<p class="form-label show-small hidden"><strong><?php echo $entry_search; ?></strong></p>
								<?php if ($search) { ?>
								<input type="text" name="search" value="<?php echo $search; ?>" title="<?php echo $help_search; ?>" />
								<?php } else { ?>
								<input type="text" name="search" value="" placeholder="<?php echo $entry_search; ?>" title="<?php echo $help_search; ?>" />
								<?php } ?>
								<?php if (!$is_home) { ?>
								<span class="label">
									<input type="checkbox" name="forsale" value="1" id="forsale" <?php if ($forsale) { ?>checked="checked"<?php } ?> />
									<label for="forsale"><em><?php echo $text_forsale; ?></em></label>
									<span class="float-right hidden-large">
										<input type="checkbox" name="description" value="1" id="description" <?php if ($description) { ?>checked="checked"<?php } ?> />
										<label for="description"><em><?php echo $entry_description; ?></em></label>
									</span>
								</span>
								<?php } ?>
							</div>
						</div>

						<div class="grid-2">
							<div class="formbox">
								<p class="form-label show-small hidden"><strong><?php echo $heading_category; ?></strong></p>
								<select name="category_id" title="<?php echo $help_category; ?>">
								  <option value="0"<?php if (!$category_id) { ?> selected="selected"<?php } ?>><?php echo $text_category; ?></option>
								  <?php foreach ($categories as $category) { ?>
								  <?php if ($category['category_id'] == $category_id) { ?>
								  <option value="<?php echo $category['category_id']; ?>" data-url="<?php echo $category['url']; ?>" selected="selected"><?php echo $category['name']; ?></option>
								  <?php } else { ?>
								  <option value="<?php echo $category['category_id']; ?>" data-url="<?php echo $category['url']; ?>"><?php echo $category['name']; ?></option>
								  <?php } ?>
								  <?php } ?>
								</select>

								<span class="label">
									<a id="button-location" href="<?php echo $location_page; ?>" class="button-location pull-right" rel="tooltip" data-placement="bottom" data-original-title="<?php echo $help_location_change; ?>">
										<i class="fa fa-globe"></i><?php echo $location_geo; ?>
									</a>
									<?php if (!$is_home) { ?>
									<a id="button-more-options" class="button-more-options"><i class="fa fa-plus-circle"></i><?php echo $text_more_options; ?></a>
									<?php } else { ?>
									<a href="<?php echo $search_page . '?more=true'; ?>" class="button-more-options"><i class="fa fa-plus-circle"></i><?php echo $text_more_options; ?></a>
									<?php } ?>
								</span>

							</div>
						</div>
					</div>

					<div class="grid-4 searchwidget-buttons hidden-medium">
						<div class="formbox">
							<input id="button-search" type="submit" value="<?php echo $button_search; ?>" class="button hidden" />
							<label for="button-search" class="button button-submit button_highlight" title="<?php echo $button_search; ?>"><i class="fa fa-search"></i><?php echo $button_search; ?></label>
							<?php if (!$is_home) { ?>
							<span class="label text-right">
								<?php if (!$search && !$tag && !$display_more_options) { ?>
								<a href="<?php echo $products_page; ?>" id="button-browse" title="<?php echo $button_browse; ?>"><i class="fa fa-th-list"></i><?php echo $button_browse; ?></a>
								<?php } else { ?>
								<a href="<?php echo $search_page; ?>" id="button-reset" title="<?php echo $button_reset; ?>"><i class="fa fa-refresh"></i><?php echo $button_reset; ?></a>
								<?php } ?>
							</span>
							<?php } ?>
						</div>
					</div>

				</div>

				<div class="grid-1" id="container-more-options" data-show="<?php echo $display_more_options ? 'true' : 'false'; ?>">

					<div class="grid-6 searchwidget-selects">

						<div class="searchwidget-select sub-category-wrapper">
							<div class="grid-3 hidden-small">
								<h3 class="title text-right"><?php echo $heading_category_sub; ?></h3>
							</div>

							<div class="grid-6">
								<div class="formbox">
									<p class="form-label show-small"><strong><?php echo $heading_category_sub; ?></strong></p>
									<select name="sub_category_id"<?php if (!$sub_categories) {?> disabled="disabled"<?php } ?>>
									  <option value="0"<?php if (!$sub_category_id) { ?> selected="selected"<?php } ?>><?php echo $text_category_sub; ?></option>
									  <?php if ($sub_category_id) { ?>
									  <?php foreach ($sub_categories as $sub_category) { ?>
									  <?php if ($sub_category['category_id'] == $sub_category_id) { ?>
									  <option value="<?php echo $sub_category['category_id']; ?>" selected="selected"><?php echo $sub_category['name']; ?></option>
									  <?php } else { ?>
									  <option value="<?php echo $sub_category['category_id']; ?>"><?php echo $sub_category['name']; ?></option>
									  <?php } ?>
									  <?php } ?>
									  <?php } ?>
									</select>
								</div>
							</div>
						</div>

						<div class="searchwidget-select third-category-wrapper"<?php if (!$third_categories) { ?> style="display:none;"<?php } ?>>
							<div class="grid-3 hidden-small">
								<h3 class="title text-right"><?php echo $heading_category_third; ?></h3>
							</div>

							<div class="grid-6">
								<div class="formbox">
									<p class="form-label show-small"><strong><?php echo $heading_category_third; ?></strong></p>
									<select name="third_category_id"<?php if (!$third_categories) {?> disabled="disabled"<?php } ?>>
									  <option value="0"<?php if (!$third_category_id) { ?> selected="selected"<?php } ?>><?php echo $text_category_sub; ?></option>
									  <?php if ($third_category_id) { ?>
									  <?php foreach ($third_categories as $third_category) { ?>
									  <?php if ($third_category['category_id'] == $third_category_id) { ?>
									  <option value="<?php echo $third_category['category_id']; ?>" selected="selected"><?php echo $third_category['name']; ?></option>
									  <?php } else { ?>
									  <option value="<?php echo $third_category['category_id']; ?>"><?php echo $third_category['name']; ?></option>
									  <?php } ?>
									  <?php } ?>
									  <?php } ?>
									</select>
								</div>
							</div>
						</div>

						<div class="searchwidget-select">
							<div class="grid-3 hidden-small">
								<h3 class="title text-right"><?php echo $heading_manufacturer; ?></h3>
							</div>

							<div class="grid-6">
								<div class="formbox">
									<p class="form-label show-small"><strong><?php echo $heading_manufacturer; ?></strong></p>
									<select name="manufacturer_id" title="<?php echo $help_manufacturer; ?>">
									  <option value="0"><?php echo $text_manufacturer; ?></option>
									  <?php if ($manufacturers) { ?>
									  <?php foreach ($manufacturers as $manufacturer) { ?>
									  <?php if ($manufacturer['manufacturer_id'] == $manufacturer_id) { ?>
									  <option value="<?php echo $manufacturer['manufacturer_id']; ?>" selected="selected"><?php echo $manufacturer['name']; ?></option>
									  <?php } else { ?>
									  <option value="<?php echo $manufacturer['manufacturer_id']; ?>"><?php echo $manufacturer['name']; ?></option>
									  <?php } ?>
									  <?php } ?>
									  <?php } ?>
									</select>
								</div>
							</div>
						</div>

						<div class="searchwidget-select">
							<div class="grid-3 hidden-small">
								<h3 class="title text-right"><?php echo $heading_price; ?></h3>
							</div>

							<div class="grid-6">
								<div class="formbox">
									<p class="form-label show-small"><strong><?php echo $heading_price; ?></strong></p>
									<select name="price" title="<?php echo $help_price; ?>">
										<option value="0"><?php echo $text_price; ?></option>
										<?php foreach ($prices as $price) { ?>
										<?php if ($price['filter_id'] == $price_selected) { ?>
										<option value="<?php echo $price['filter_id']; ?>" selected="selected"><?php echo $price['name']; ?></option>
										<?php } else { ?>
										<option value="<?php echo $price['filter_id']; ?>"><?php echo $price['name']; ?></option>
										<?php } ?>
										<?php } ?>
									</select>
								</div>
							</div>
						</div>

						<div class="searchwidget-select">
							<div class="grid-3 hidden-small">
								<h3 class="title text-right"><?php echo $heading_age; ?></h3>
							</div>

							<div class="grid-6">
								<div class="formbox">
									<p class="form-label show-small"><strong><?php echo $heading_age; ?></strong></p>
									<select name="age" title="<?php echo $help_age; ?>">
										<option value="0"><?php echo $text_age; ?></option>
										<?php foreach ($ages as $age) { ?>
										<?php if ($age['filter_id'] == $age_selected) { ?>
										<option value="<?php echo $age['filter_id']; ?>" selected="selected"><?php echo $age['name']; ?></option>
										<?php } else { ?>
										<option value="<?php echo $age['filter_id']; ?>"><?php echo $age['name']; ?></option>
										<?php } ?>
										<?php } ?>
									</select>
								</div>
							</div>
						</div>

						<div class="searchwidget-select">
							<div class="grid-3 hidden-small">
								<h3 class="title text-right"><?php echo $heading_country; ?></h3>
							</div>

							<div class="grid-6">
								<div class="formbox">
									<p class="form-label show-small"><strong><?php echo $heading_country; ?></strong></p>
									<select name="country_id" title="<?php echo $help_country; ?>">
										<option value="0"><?php echo $text_country; ?></option>
										<?php foreach ($countries as $country) { ?>
										<?php if ($country['country_id'] == $country_id) { ?>
										<option value="<?php echo $country['country_id']; ?>" selected="selected"><?php echo $country['name']; ?></option>
										<?php } else { ?>
										<option value="<?php echo $country['country_id']; ?>"><?php echo $country['name']; ?></option>
										<?php } ?>
										<?php } ?>
									</select>
								</div>
							</div>
						</div>

						<div class="searchwidget-select location-wrapper"<?php if (!$zones) { ?> style="display:none;"<?php } ?>>
							<div class="grid-3 hidden-small">
								<h3 class="title text-right"><?php echo $heading_zone; ?></h3>
							</div>

							<div class="grid-6">
								<div class="formbox">
									<p class="form-label show-small"><strong><?php echo $heading_zone; ?></strong></p>
									<select name="zone_id" title="<?php echo $help_zone; ?>" <?php if (!$zones) {?> disabled<?php } ?>>
										<option value="0"><?php echo $text_zone; ?></option>
										<?php foreach ($zones as $zone) { ?>
										<?php if ($zone['zone_id'] == $zone_id) { ?>
										<option value="<?php echo $zone['zone_id']; ?>" selected="selected"><?php echo $zone['name']; ?></option>
										<?php } else { ?>
										<option value="<?php echo $zone['zone_id']; ?>"><?php echo $zone['name']; ?></option>
										<?php } ?>
										<?php } ?>
									</select>
								</div>
							</div>
						</div>

						<div class="searchwidget-select location-wrapper"<?php if (!$zones) { ?> style="display:none;"<?php } ?>>
							<div class="grid-3 hidden-small">
								<h3 class="title text-right"><?php echo $heading_location; ?></h3>
							</div>

							<div class="grid-6">
								<div class="formbox">
									<p class="form-label show-small"><strong><?php echo $heading_location; ?></strong></p>
									<input type="text" name="location" value="<?php echo $location; ?>" title="<?php echo $help_location; ?>" placeholder="<?php echo !$zone_id ? $text_location_na : ''; ?>" <?php if (!$zones || !$zone_id) {?> disabled<?php } ?>/>
								</div>
							</div>
						</div>

						<div class="searchwidget-select hidden-very-small">
							<div class="grid-3 hidden-small">&nbsp;</div>

							<div class="grid-6">
								<div class="formbox">
									<span class="label-location">
										<?php if (!$session_country_id) { ?>
										<a href="<?php echo $location_page; ?>" class="button-location-set gray-text" rel="tooltip" data-placement="top" data-original-title="<?php echo $help_location_set; ?>">
											<i class="fa fa-globe"></i><?php echo $text_location_set; ?>
										</a>
										<?php } else { ?>
										<a href="<?php echo $location_remove; ?>" class="button-location-remove gray-text" rel="tooltip" data-placement="top" data-original-title="<?php echo $help_location_remove; ?>">
											<i class="fa fa-globe"></i><?php echo $text_location_remove; ?>
										</a>
										<?php } ?>
									</span>
								</div>
							</div>
						</div>

					</div>

					<div class="grid-3 searchwidget-checkboxes">
						<div class="grid-2">
							<h4 class="title"><?php echo $heading_type; ?></h4>
						</div>

						<div class="grid-2">
							<div class="formbox" title="<?php echo $help_type; ?>">
							  <?php foreach ($listing_types as $listing_type) { ?>
								<?php if (in_array($listing_type['type_id'], $type_selected)) { ?>
								<span class="label"><input type="checkbox" name="type[]" value="<?php echo $listing_type['type_id']; ?>" id="type-<?php echo $listing_type['type_id']; ?>" checked="checked" /><label for="type-<?php echo $listing_type['type_id']; ?>"><?php echo $listing_type['name']; ?></label></span>
								<?php } else { ?>
								<span class="label"><input type="checkbox" name="type[]" value="<?php echo $listing_type['type_id']; ?>" id="type-<?php echo $listing_type['type_id']; ?>" /><label for="type-<?php echo $listing_type['type_id']; ?>"><?php echo $listing_type['name']; ?></label></span>
								<?php } ?>
							  <?php } ?>
							</div>
						</div>

						<div class="grid-2">
							<h4 class="title"><?php echo $heading_member; ?></h4>
						</div>

						<div class="grid-2">
							<div class="formbox" title="<?php echo $help_member; ?>">
							  <?php foreach ($member_groups as $member_group) { ?>
								<?php if (in_array($member_group['group_id'], $member_selected)) { ?>
								<span class="label"><input type="checkbox" name="member[]" value="<?php echo $member_group['group_id']; ?>" id="member-<?php echo $member_group['group_id']; ?>" checked="checked" /><label for="member-<?php echo $member_group['group_id']; ?>"><?php echo $member_group['name']; ?></label></span>
								<?php } else { ?>
								<span class="label"><input type="checkbox" name="member[]" value="<?php echo $member_group['group_id']; ?>" id="member-<?php echo $member_group['group_id']; ?>" /><label for="member-<?php echo $member_group['group_id']; ?>"><?php echo $member_group['name']; ?></label></span>
								<?php } ?>
							  <?php } ?>
							</div>
						</div>

						<div class="grid-2">
							<h4 class="title"><?php echo $heading_condition; ?></h4>
						</div>

						<div class="grid-2">
							<div class="formbox" title="<?php echo $help_condition; ?>">
							  <?php foreach ($conditions as $listing_condition) { ?>
								<?php if (in_array($listing_condition['filter_id'], $condition)) { ?>
								<span class="label"><input type="checkbox" name="condition[]" value="<?php echo $listing_condition['filter_id']; ?>" id="condition-<?php echo $listing_condition['filter_id']; ?>" checked="checked" /><label for="condition-<?php echo $listing_condition['filter_id']; ?>"><?php echo $listing_condition['name']; ?></label></span>
								<?php } else { ?>
								<span class="label"><input type="checkbox" name="condition[]" value="<?php echo $listing_condition['filter_id']; ?>" id="condition-<?php echo $listing_condition['filter_id']; ?>" /><label for="condition-<?php echo $listing_condition['filter_id']; ?>"><?php echo $listing_condition['name']; ?></label></span>
								<?php } ?>
							  <?php } ?>
							</div>
						</div>

					</div>

				</div>

				<div class="grid-1 searchwidget-buttons show-medium">
					<?php if (!$search && !$tag && !$display_more_options) { ?>
					<div class="formbox">
						<label for="button-search" class="button button-submit button_highlight" title="<?php echo $button_search; ?>"><i class="fa fa-search"></i><?php echo $button_search; ?></label>
					</div>
					<?php } else { ?>
					<div class="grid-2">
						<div class="formbox">
							<label for="button-search" class="button button-submit button_highlight" title="<?php echo $button_search; ?>"><i class="fa fa-search"></i><?php echo $button_search; ?></label>
						</div>
					</div>
					<div class="grid-2">
						<div class="formbox">
							<a href="<?php echo $search_page; ?>" class="button button_alt" id="button-reset" title="<?php echo $button_reset; ?>"><i class="fa fa-refresh"></i><?php echo $button_reset; ?></a>
						</div>
					</div>
					<?php } ?>
				</div>

			</div>

		</div>

		<input type="hidden" name="tag" value="<?php echo $tag; ?>" />
		<input type="hidden" name="products_page" value="<?php echo $products_page; ?>" />
		<input type="hidden" name="text_hide_options" value="<?php echo $text_hide_options; ?>" />
		<input type="hidden" name="text_more_options" value="<?php echo $text_more_options; ?>" />
	</form>

	<input type="hidden" name="text_select_manufacturer" value="<?php echo $text_manufacturer; ?>" />
	<input type="hidden" name="text_manufacturer_other" value="<?php echo $text_manufacturer_other; ?>" />
	<input type="hidden" name="text_select_category" value="<?php echo $text_category; ?>" />
	<input type="hidden" name="text_select_category_sub" value="<?php echo $text_category_sub; ?>" />
	<input type="hidden" name="text_select_category_count" value="<?php echo $text_category_count; ?>" />
	<input type="hidden" name="text_select_category_sub_count" value="<?php echo $text_category_sub_count; ?>" />
	<input type="hidden" name="text_select_manufacturer_count" value="<?php echo $text_manufacturer_count; ?>" />
	<input type="hidden" name="text_select_zone" value="<?php echo $text_zone; ?>" />
	<input type="hidden" name="manufacturers_all" value="<?php echo htmlspecialchars(json_encode($manufacturers), ENT_COMPAT); ?>" />
	<input type="hidden" name="categories_complete" value="<?php echo htmlspecialchars(json_encode($categories_complete), ENT_COMPAT); ?>" />
</div>
