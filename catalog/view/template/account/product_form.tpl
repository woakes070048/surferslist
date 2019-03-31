<?php echo $header; ?>
<div class="container-page account-product-form-page">
    <div class="breadcrumb">
        <div class="layout">
            <h1><i class="fa fa-th-list"></i> <?php echo $heading_title; ?></h1>
            <div class="links">
                <?php foreach ($breadcrumbs as $breadcrumb) { ?>
                <?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
                <?php } ?>
            </div>
        </div>
    </div>
    <div class="layout layout-left-minus-right">
        <!-- <?php // echo $column_left; ?> -->
        <div class="container-left">
            <div class="content-page my-account my-listing">
            	<?php echo $notification; ?>

            	<?php echo $content_top; ?>

                <div class="widget">

					<h6><?php echo $heading_sub_title; ?></h6>

					<form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form" class="account-product-form">
                        <div class="content">
                            <p class="text-center text-larger"><span class="help"><?php echo $text_post_intro; ?></span></p>
                        </div>

                        <?php if ($error) { ?>
                        <div id="tab-error" class="post-section-container">
                            <h2 class="post-section-heading"><i class="fa fa-exclamation-triangle"></i>&nbsp; &nbsp;<?php echo $heading_error; ?><span class="toggle-icon"><i class="fa fa-chevron-down icon-ac-down"></i><i class="fa fa-chevron-up icon-ac-up"></i></span></h2>

                            <div class="post-section-content content">
                                <div class="warning">
                                    <p class="error">
                                    <?php if ($error_warning) { ?><span><i class="fa fa-exclamation-triangle"></i><?php echo $error_warning; ?></span><?php } ?>
                                    <?php foreach ($languages as $language) { ?>
                                        <?php if (isset($error_name[$language['language_id']])) { ?><span><i class="fa fa-exclamation-triangle"></i><?php echo $error_name[$language['language_id']]; ?> <a class="trigger-click" data-target="tab-general"><?php echo $text_fix; ?></a></span><?php } ?>
                                        <?php if (isset($error_description[$language['language_id']])) { ?><span><i class="fa fa-exclamation-triangle"></i><?php echo $error_description[$language['language_id']]; ?> <a class="trigger-click" data-target="tab-general"><?php echo $text_fix; ?></a></span><?php } ?>
                                    <?php } ?>
                                    <?php if ($error_category) { ?><span><i class="fa fa-exclamation-triangle"></i><?php echo $error_category; ?> <a class="trigger-click" data-target="tab-data"><?php echo $text_fix; ?></a></span><?php } ?>
                                    <?php if ($error_category_sub) { ?><span><i class="fa fa-exclamation-triangle"></i><?php echo $error_category_sub; ?> <a class="trigger-click" data-target="tab-data"><?php echo $text_fix; ?></a></span><?php } ?>
                                    <?php if ($error_manufacturer) { ?><span><i class="fa fa-exclamation-triangle"></i><?php echo $error_manufacturer; ?> <a class="trigger-click" data-target="tab-data"><?php echo $text_fix; ?></a></span><?php } ?>
                                    <?php if ($error_model) { ?><span><i class="fa fa-exclamation-triangle"></i><?php echo $error_model; ?> <a class="trigger-click" data-target="tab-data"><?php echo $text_fix; ?></a></span><?php } ?>
                                    <?php if ($error_size) { ?><span><i class="fa fa-exclamation-triangle"></i><?php echo $error_size; ?> <a class="trigger-click" data-target="tab-data"><?php echo $text_fix; ?></a></span><?php } ?>
                                    <?php if ($error_year) { ?><span><i class="fa fa-exclamation-triangle"></i><?php echo $error_year; ?> <a class="trigger-click" data-target="tab-data"><?php echo $text_fix; ?></a></span><?php } ?>
                                    <?php if ($error_condition) { ?><span><i class="fa fa-exclamation-triangle"></i><?php echo $error_condition; ?> <a class="trigger-click" data-target="tab-data"><?php echo $text_fix; ?></a></span><?php } ?>
                                    <?php if ($error_country) { ?><span><i class="fa fa-exclamation-triangle"></i><?php echo $error_country; ?> <a class="trigger-click" data-target="tab-data"><?php echo $text_fix; ?></a></span><?php } ?>
                                    <?php if ($error_zone) { ?><span><i class="fa fa-exclamation-triangle"></i><?php echo $error_zone; ?> <a class="trigger-click" data-target="tab-data"><?php echo $text_fix; ?></a></span><?php } ?>
                                    <?php if ($error_location) { ?><span><i class="fa fa-exclamation-triangle"></i><?php echo $error_location; ?> <a class="trigger-click" data-target="tab-data"><?php echo $text_fix; ?></a></span><?php } ?>
                                    <?php if ($error_image) { ?><span><i class="fa fa-exclamation-triangle"></i><?php echo $error_image; ?> <a class="trigger-click" data-target="tab-image"><?php echo $text_fix; ?></a></span><?php } ?>
                                    <?php if ($error_images) { ?><span><i class="fa fa-exclamation-triangle"></i><?php echo $error_images; ?> <a class="trigger-click" data-target="tab-image"><?php echo $text_fix; ?></a></span><?php } ?>
                                    <?php if ($error_for_sale) { ?><span><i class="fa fa-exclamation-triangle"></i><?php echo $error_for_sale; ?> <a class="trigger-click" data-target="tab-sell"><?php echo $text_fix; ?></a></span><?php } ?>
                                    <?php if ($error_value) { ?><span><i class="fa fa-exclamation-triangle"></i><?php echo $error_value; ?> <a class="trigger-click" data-target="tab-sell"><?php echo $text_fix; ?></a></span><?php } ?>
                                    <?php if ($error_type) { ?><span><i class="fa fa-exclamation-triangle"></i><?php echo $error_type; ?> <a class="trigger-click" data-target="tab-sell"><?php echo $text_fix; ?></a></span><?php } ?>
                                    <?php if ($error_price) { ?><span><i class="fa fa-exclamation-triangle"></i><?php echo $error_price; ?> <a class="trigger-click" data-target="tab-sell"><?php echo $text_fix; ?></a></span><?php } ?>
                                    <?php if ($error_price_discount) { ?><span><i class="fa fa-exclamation-triangle"></i><?php echo $error_price_discount; ?> <a class="trigger-click" data-target="tab-sell"><?php echo $text_fix; ?></a></span><?php } ?>
                                    <?php if ($error_price_special && $error_price_special != $error_price_discount) { ?><span><i class="fa fa-exclamation-triangle"></i><?php echo $error_price_special; ?> <a class="trigger-click" data-target="tab-sell"><?php echo $text_fix; ?></a></span><?php } ?>
                                    <?php if ($permissions['inventory_enabled']) { ?>
                                    <?php if ($error_quantity) { ?><span><i class="fa fa-exclamation-triangle"></i><?php echo $error_quantity; ?> <a class="trigger-click" data-target="tab-sell"><?php echo $text_fix; ?></a></span><?php } ?>
                                    <?php if ($error_minimum) { ?><span><i class="fa fa-exclamation-triangle"></i><?php echo $error_minimum; ?> <a class="trigger-click" data-target="tab-sell"><?php echo $text_fix; ?></a></span><?php } ?>
                                    <?php if ($error_part_number) { ?><span><i class="fa fa-exclamation-triangle"></i><?php echo $error_part_number; ?> <a class="trigger-click" data-target="tab-sell"><?php echo $text_fix; ?></a></span><?php } ?>
                                    <?php } ?>
                                    <?php if ($this->config->get('member_data_field_shipping')) { ?>
                                    <?php if ($error_shipping) { ?><span><i class="fa fa-exclamation-triangle"></i><?php echo $error_shipping; ?> <a class="trigger-click" data-target="tab-sell"><?php echo $text_fix; ?></a></span><?php } ?>
                                    <?php } ?>
                                    <?php if ($this->config->get('member_data_field_dimensions')) { ?>
                                    <?php if ($error_dimensions) { ?><span><i class="fa fa-exclamation-triangle"></i><?php echo $error_dimensions; ?> <a class="trigger-click" data-target="tab-sell"><?php echo $text_fix; ?></a></span><?php } ?>
                                    <?php if ($error_weight) { ?><span><i class="fa fa-exclamation-triangle"></i><?php echo $error_weight; ?> <a class="trigger-click" data-target="tab-sell"><?php echo $text_fix; ?></a></span><?php } ?>
                                    <?php } ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                        <?php } ?>

						<div id="tab-general" class="post-section-container">
                            <h2 class="post-section-heading"><i class="fa fa-info-circle"></i>&nbsp; &nbsp;<?php echo $tab_general; ?><span class="toggle-icon"><i class="fa fa-chevron-down icon-ac-down"></i><i class="fa fa-chevron-up icon-ac-up"></i></span></h2>

                            <div class="post-section-content content">
								<h3 class="title"><?php echo $heading_description; ?></h3>

								<div id="languages" class="htabs">
									<?php foreach ($languages as $language) { ?>
									<a href="#language<?php echo $language['language_id']; ?>"><img src="image/flags/<?php echo $language['image']; ?>" title="<?php echo $language['name']; ?>" /> <?php echo $language['name']; ?></a>
									<?php } ?>
								</div>

								<?php foreach ($languages as $language) { ?>
								<div id="language<?php echo $language['language_id']; ?>">
									<div class="formbox">
										<p class="form-label">
											<strong><?php echo $entry_name; ?></strong>
                                            <i class="fa fa-question-circle float-right grey-text" title="<?php echo $entry_name . $help_required; ?>" data-content="<?php echo $help_name; ?>" data-placement="left" rel="popover" data-container="body" data-trigger="hover"></i>
										</p>
										<input type="text" name="product_description[<?php echo $language['language_id']; ?>][name]" value="<?php echo isset($product_description[$language['language_id']]['name']) ? $product_description[$language['language_id']]['name'] : ''; ?>" placeholder="<?php echo $entry_name; ?>" required="required" />
										<?php if (isset($error_name[$language['language_id']])) { ?><span class="error"><i class="fa fa-exclamation-triangle"></i><?php echo $error_name[$language['language_id']]; ?></span><?php } ?>
									</div>

                                    <div class="formbox">
										<p class="form-label">
											<strong><?php echo $entry_description; ?></strong>
                                            <i class="fa fa-question-circle float-right grey-text" title="<?php echo $entry_description . $help_required; ?>" data-content="<?php echo $help_description; ?>" data-placement="left" rel="popover" data-container="body" data-trigger="hover"></i>
										</p>
										<textarea name="product_description[<?php echo $language['language_id']; ?>][description]" cols="25" rows="3" id="description<?php echo $language['language_id']; ?>" placeholder="<?php echo $entry_description; ?>"><?php echo isset($product_description[$language['language_id']]['description']) ? $product_description[$language['language_id']]['description'] : ''; ?></textarea>
										<?php if (isset($error_description[$language['language_id']])) { ?><span class="error"><i class="fa fa-exclamation-triangle"></i><?php echo $error_description[$language['language_id']]; ?></span><?php } ?>
									</div>

									<h3 class="title hidden"><?php echo $heading_tag; ?></h3>

									<div class="formbox"<?php echo (!$this->config->get('member_data_field_tags') ? ' style="display:none;"' : '') ?>>
										<p class="form-label">
											<strong><?php echo $entry_tag . $help_optional; ?></strong>
                                            <i class="fa fa-question-circle float-right grey-text" title="<?php echo $entry_tag . $help_required; ?>" data-content="<?php echo $help_tag; ?>" data-placement="left" rel="popover" data-container="body" data-trigger="hover"></i>
										</p>
										<input type="text" name="product_description[<?php echo $language['language_id']; ?>][tag]" value="<?php echo isset($product_description[$language['language_id']]['tag']) ? $product_description[$language['language_id']]['tag'] : ''; ?>" placeholder="<?php echo $entry_tag; ?>" />
									</div>
								</div>
								<?php } ?>

    							<div class="buttons buttons-middle">
    								<div class="center">
                                        <div class="grid-1">
                                            <a href="<?php echo $cancel; ?>" class="button button_alt button_cancel hidden"><i class="fa fa-ban"></i><?php echo $button_cancel; ?></a>
                                        </div>
                                        <div class="grid-1">
                                            <a class="button button_next button_highlight bigger fullwidth trigger-click" data-target="tab-data" title="<?php echo $text_next . ': ' . $tab_data; ?>" rel="tooltip" data-container="body"><i class="fa fa-arrow-down"></i><?php echo $text_next; ?></a>
                                        </div>
    								</div>
    							</div>

                            </div>

						</div><!-- tab-general -->

						<div id="tab-data" class="post-section-container">
                            <h2 class="post-section-heading"><i class="fa fa-list-ul"></i>&nbsp; &nbsp;<?php echo $tab_data; ?><span class="toggle-icon"><i class="fa fa-chevron-down icon-ac-down"></i><i class="fa fa-chevron-up icon-ac-up"></i></span></h2>

                            <div class="post-section-content content">
								<?php if ($this->config->get('member_tab_links')) { ?>
								<h3 class="title"><?php echo $heading_category; ?></h3>

								<?php if ($this->config->get('member_data_field_category')) { ?>
								<div class="formbox">
									<p class="form-label">
                                        <strong><?php echo $entry_category; ?></strong>
                                        <i class="fa fa-question-circle float-right grey-text" title="<?php echo $help_category . $help_required; ?>" data-placement="left" data-container="body" rel="tooltip"></i>
                                    </p>
									<select name="category_id">
									  <option value="0"<?php if (!$category_id) { ?> selected="selected"<?php } ?>><?php echo $text_select_category; ?></option>
									  <?php foreach ($categories as $category) { ?>
									  <?php if ($category['category_id'] == $category_id) { ?>
									  <option value="<?php echo $category['category_id']; ?>" selected="selected"><?php echo $category['name']; ?></option>
									  <?php } else { ?>
									  <option value="<?php echo $category['category_id']; ?>"><?php echo $category['name']; ?></option>
									  <?php } ?>
									  <?php } ?>
									</select>
									<?php if ($error_category) { ?><span class="error"><i class="fa fa-exclamation-triangle"></i><?php echo $error_category; ?></span><?php } ?>
								</div>

                                <div class="formbox sub-category-wrapper"<?php if (!$sub_categories) { ?> style="display:none;"<?php } ?>>
									<p class="form-label">
										<strong><?php echo $entry_category_sub; ?></strong>
                                        <i class="fa fa-question-circle float-right grey-text" title="<?php echo $help_category_sub . $help_required; ?>" data-placement="left" data-container="body" rel="tooltip"></i>
									</p>
									<select name="sub_category_id"<?php if (!$sub_categories) {?> disabled="disabled"<?php } ?>>
									  <option value="0"<?php if (!$sub_category_id) { ?> selected="selected"<?php } ?>><?php echo $text_select_category_sub; ?></option>
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
									<?php if ($error_category_sub) { ?><span class="error"><i class="fa fa-exclamation-triangle"></i><?php echo $error_category_sub; ?></span><?php } ?>
								</div>

                                <div class="formbox third-category-wrapper"<?php if (!$third_categories) { ?> style="display:none;"<?php } ?>>
									<p class="form-label">
										<strong><?php echo $entry_category_third . $help_optional; ?></strong>
                                        <i class="fa fa-question-circle float-right grey-text" title="<?php echo $help_category_third . $help_optional; ?>" data-placement="left" data-container="body" rel="tooltip"></i>
									</p>
									<select name="third_category_id"<?php if (!$third_categories) {?> disabled="disabled"<?php } ?>>
									  <option value="0"<?php if (!$third_category_id) { ?> selected="selected"<?php } ?>><?php echo $text_select_category_sub; ?></option>
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
								<?php } ?>

								<?php if ($this->config->get('member_data_field_manufacturer')) { ?>
								<h3 class="title"><?php echo $heading_manufacturer; ?></h3>

                                <div class="formbox">
									<p class="form-label">
                                        <strong><?php echo $entry_manufacturer; ?></strong>
                                        <i class="fa fa-question-circle float-right grey-text" title="<?php echo $help_manufacturer . $help_required; ?>" data-placement="left" data-container="body" rel="tooltip"></i>
                                        <img src="<?php echo $manufacturer_thumb; ?>" alt="<?php echo $manufacturer_name; ?>" id="manufacturer_thumb" class="thumb" />
                                    </p>
									<select name="manufacturer_id"<?php if (!$manufacturers) {?> disabled="disabled"<?php } ?>>
									  <option value="0" selected="selected"><?php echo $text_select_manufacturer; ?></option>
									  <option value="1"<?php if ($manufacturer_id == 1) { ?> selected="selected"<?php } ?>>-- Other Brand --</option>
									  <?php if ($manufacturers) { ?>
									  <?php foreach ($manufacturers as $manufacturer) { ?>
									  <?php if ($manufacturer['id'] == 0) continue; ?>
									  <?php if ($manufacturer['id'] == $manufacturer_id) { ?>
									  <option value="<?php echo $manufacturer['id']; ?>" selected="selected"><?php echo $manufacturer['name']; ?></option>
									  <?php } else { ?>
									  <option value="<?php echo $manufacturer['id']; ?>"><?php echo $manufacturer['name']; ?></option>
									  <?php } ?>
									  <?php } ?>
									  <?php } ?>
									</select>
									<?php if ($error_manufacturer) { ?><span class="error"><i class="fa fa-exclamation-triangle"></i><?php echo $error_manufacturer; ?></span><?php } ?>
								</div>
								<?php } ?>
								<?php } ?>

								<?php if ($this->config->get('member_data_field_model')) { ?>
								<div class="formbox">
									<p class="form-label">
										<strong><?php echo $entry_model; ?></strong>
                                        <i class="fa fa-question-circle float-right grey-text" title="<?php echo $help_model . $help_required; ?>" data-placement="left" data-container="body" rel="tooltip"></i>
									</p>
									<input type="text" name="model" value="<?php echo $model; ?>" placeholder="<?php echo $entry_model; ?>" required="required" />
									<?php if ($error_model) { ?><span class="error"><i class="fa fa-exclamation-triangle"></i><?php echo $error_model; ?></span><?php } ?>
								</div>
								<?php } ?>

								<div class="formbox">
								  <p class="form-label">
								  	<strong><?php echo $entry_year . $help_optional; ?></strong>
                                    <i class="fa fa-question-circle float-right grey-text" title="<?php echo $help_year . $help_optional; ?>" data-placement="left" data-container="body" rel="tooltip"></i>
							  	  </p>
								  <input type="text" name="year" value="<?php echo $year; ?>" placeholder="<?php echo $entry_year; ?>" />
								  <?php if ($error_year) { ?><span class="error"><i class="fa fa-exclamation-triangle"></i><?php echo $error_year; ?></span><?php } ?>
								</div>

								<div class="formbox">
									<p class="form-label">
										<strong><?php echo $entry_size; ?></strong>
                                        <i class="fa fa-question-circle float-right grey-text" title="<?php echo $help_size . $help_required; ?>" data-placement="left" data-container="body" rel="tooltip"></i>
									</p>
									<input type="text" name="size" value="<?php echo $size; ?>" placeholder="<?php echo $entry_size; ?>" required="required" />
									<?php if ($error_size) { ?><span class="error"><i class="fa fa-exclamation-triangle"></i><?php echo $error_size; ?></span><?php } ?>
								</div>

								<div class="formbox">
									<p class="form-label">
										<strong><?php echo $entry_condition; ?></strong>
                                        <i class="fa fa-question-circle float-right grey-text" title="<?php echo $help_condition . $help_required; ?>" data-placement="left" data-container="body" rel="tooltip"></i>
									</p>
									<select name="condition_id">
									  <option value="0" selected="selected"><?php echo $text_select_condition; ?></option>
									  <?php foreach ($conditions as $condition) { ?>
									  <?php if ($condition['filter_id'] == $condition_id) { ?>
									  <option value="<?php echo $condition['filter_id']; ?>" selected="selected"><?php echo $condition['name']; ?></option>
									  <?php } else { ?>
									  <option value="<?php echo $condition['filter_id']; ?>"><?php echo $condition['name']; ?></option>
									  <?php } ?>
									  <?php } ?>
									</select>
									<?php if ($error_condition) { ?><span class="error"><i class="fa fa-exclamation-triangle"></i><?php echo $error_condition; ?></span><?php } ?>
								</div>

                                <?php if ($this->config->get('member_data_field_location')) { ?>
								<h3 class="title"><?php echo $heading_location; ?></h3>

                                <div class="formbox">
									<p class="form-label">
										<strong><?php echo $entry_country; ?></strong>
                                        <i class="fa fa-question-circle float-right grey-text" title="<?php echo $help_country . $help_required; ?>" data-placement="left" data-container="body" rel="tooltip"></i>
									</p>
									<select name="country_id" required="required">
									  <option value=""><?php echo $text_select_country; ?></option>
									  <?php foreach ($countries as $country) { ?>
									  <?php if ($country['country_id'] == $country_id) { ?>
									  <option value="<?php echo $country['country_id']; ?>" selected="selected"><?php echo $country['name']; ?></option>
									  <?php } else { ?>
									  <option value="<?php echo $country['country_id']; ?>"><?php echo $country['name']; ?></option>
									  <?php } ?>
									  <?php } ?>
									</select>
									<?php if ($error_country) { ?><span class="error"><i class="fa fa-exclamation-triangle"></i><?php echo $error_country; ?></span><?php } ?>
								</div>

                                <div class="formbox">
									<p class="form-label">
										<strong><?php echo $entry_zone; ?></strong>
                                        <i class="fa fa-question-circle float-right grey-text" title="<?php echo $help_zone . $help_required; ?>" data-placement="left" data-container="body" rel="tooltip"></i>
									</p>
									<select name="zone_id" placeholder="<?php echo $entry_zone; ?>" required="required"<?php if (!$zones) {?> disabled="disabled"<?php } ?>>
        								<option value="0"><?php echo $text_select_zone; ?></option>
        								<?php foreach ($zones as $zone) { ?>
        								<?php if ($zone['zone_id'] == $zone_id) { ?>
        								<option value="<?php echo $zone['zone_id']; ?>" selected="selected"><?php echo $zone['name']; ?></option>
        								<?php } else { ?>
        								<option value="<?php echo $zone['zone_id']; ?>"><?php echo $zone['name']; ?></option>
        								<?php } ?>
        								<?php } ?>
									</select>
									<?php if ($error_zone) { ?><span class="error"><i class="fa fa-exclamation-triangle"></i><?php echo $error_zone; ?></span><?php } ?>
								</div>

                                <div class="formbox">
									<p class="form-label">
										<strong><?php echo $entry_location; ?></strong>
                                        <i class="fa fa-question-circle float-right grey-text" title="<?php echo $help_location . $help_required; ?>" data-placement="left" data-container="body" rel="tooltip"></i>
									</p>
									<input type="text" name="location" value="<?php echo $location; ?>" placeholder="<?php echo $entry_location; ?>" required="required" />
									<?php if ($error_location) { ?><span class="error"><i class="fa fa-exclamation-triangle"></i><?php echo $error_location; ?></span><?php } ?>
								</div>
                                <?php } ?>

    							<div class="buttons buttons-middle">
    								<div class="center">
                                        <div class="grid-1">
        									<a class="button button_back button_alt hidden trigger-click" data-target="tab-general" title="<?php echo $text_previous . ': ' . $tab_general; ?>" rel="tooltip" data-container="body"><i class="fa fa-arrow-up"></i><?php echo $text_previous; ?></a>
                                        </div>
                                        <div class="grid-1">
        									<a class="button button_next button_highlight bigger fullwidth trigger-click" data-target="tab-image" title="<?php echo $text_next . ': ' . $tab_image; ?>" rel="tooltip" data-container="body"><i class="fa fa-arrow-down"></i><?php echo $text_next; ?></a>
                                        </div>
    								</div>
    							</div>
                            </div>

						</div> <!-- details -->

						<div id="tab-image" class="post-section-container">
                            <h2 class="post-section-heading"><i class="fa fa-camera"></i>&nbsp; &nbsp;<?php echo $tab_image; ?><span class="toggle-icon"><i class="fa fa-chevron-down icon-ac-down"></i><i class="fa fa-chevron-up icon-ac-up"></i></span></h2>

                            <div class="post-section-content content">
								<h3 class="title"><?php echo $heading_image; ?></h3>

								<div class="formbox">
									<p class="form-label">
										<strong><?php echo $entry_image; ?></strong>
                                        <i class="fa fa-question-circle float-right grey-text" title="<?php echo $entry_image . $help_required; ?>" data-content="<?php echo $help_image; ?>" data-placement="left" rel="popover" data-container="body" data-trigger="hover"></i>
									</p>
									<div class="image image-border">
										<?php if ($featured) { ?><i class="fa fa-tag icon promo"></i><?php } ?>
										<img src="<?php echo $thumb; ?>" alt="" id="thumb" class="thumb" /><br />
										<input type="hidden" name="image" value="<?php echo $image; ?>" id="image" />
										<?php if ($permissions['inventory_enabled']) { ?><a data-image-row="" class="button button_images button_alt upload-images" title="<?php echo $text_browse; ?>" rel="tooltip" data-container="body"><i class="fa fa-file"></i><?php echo $text_browse; ?></a><?php } ?>
										<a class="button button-upload" id="button-upload" title="<?php echo $button_upload; ?>" rel="tooltip" data-container="body"><i class="fa fa-upload"></i><?php echo $button_upload; ?></a>
										<a class="button button_clear button_alt" data-target="" title="<?php echo $button_clear; ?>" rel="tooltip" data-container="body"><i class="fa fa-times"></i><?php echo $button_clear; ?></a>
									</div>
									<?php if ($error_image) { ?><span class="error"><i class="fa fa-exclamation-triangle"></i><?php echo $error_image; ?></span><?php } ?>
								</div>

                                <h3 class="title"><?php echo $heading_images; ?></h3>

    							<?php $image_row = 0; ?>
    							<?php foreach ($product_images as $product_image) { ?>
								<div id="image-row<?php echo $image_row; ?>" class="image-row">
									<div class="image image-border">
										<img src="<?php echo $product_image['thumb']; ?>" alt="" id="thumb<?php echo $image_row; ?>" class="thumb" /><br />
										<input type="hidden" name="product_image[<?php echo $image_row; ?>][image]" value="<?php echo $product_image['image']; ?>" id="image<?php echo $image_row; ?>" /><input type="hidden" name="product_image[<?php echo $image_row; ?>][sort_order]" value="<?php echo $image_row; ?>" />
										<?php if ($permissions['inventory_enabled']) { ?><a data-image-row="<?php echo $image_row; ?>" class="button button_images button_alt upload-images"><i class="fa fa-camera"></i><?php echo $text_browse; ?></a> <?php } ?><a class="button button-upload" id="button-upload<?php echo $image_row; ?>"><i class="fa fa-upload"></i><?php echo $button_upload; ?></a> <a class="button button_clear button_alt" data-target="<?php echo $image_row; ?>" title="<?php echo $button_clear; ?>" rel="tooltip" data-container="body"><i class="fa fa-times"></i><?php echo $button_clear; ?></a> <a onclick="removeImage(<?php echo $image_row; ?>)" class="button button_trash button_remove" title="<?php echo $button_remove; ?>" rel="tooltip" data-container="body"><i class="fa fa-trash"></i><?php echo $button_remove; ?></a>
									</div>
								</div>
    							<?php $image_row++; ?>
    							<?php } ?>

    							<div class="image-row-footer">
    								<div class="buttons buttons-middle">
    									<div class="left">
    										<a id="add-image" class="button"><i class="fa fa-plus"></i><?php echo $button_add_image; ?></a>
    									</div>
    								</div>
    							</div>

                                <div class="buttons buttons-middle">
    								<div class="center">
                                        <div class="grid-1">
        									<a class="button button_back button_alt hidden trigger-click" data-target="tab-data" title="<?php echo $text_previous . ': ' . $tab_data; ?>" rel="tooltip" data-container="body"><i class="fa fa-arrow-up"></i><?php echo $text_previous; ?></a>
                                        </div>
                                        <div class="grid-1">
        									<a class="button button_next button_highlight bigger fullwidth trigger-click" data-target="tab-sell" title="<?php echo $text_next . ': ' . $tab_sell; ?>" rel="tooltip" data-container="body"><i class="fa fa-arrow-down"></i><?php echo $text_next; ?></a>
                                        </div>
    								</div>
    							</div>
                            </div>

						</div><!-- tab-image -->

						<div id="tab-sell" class="post-section-container">
                            <h2 class="post-section-heading"><i class="fa fa-dollar"></i>&nbsp; &nbsp;<?php echo $tab_sell; ?><span class="toggle-icon"><i class="fa fa-chevron-down icon-ac-down"></i><i class="fa fa-chevron-up icon-ac-up"></i></span></h2>

                            <div class="post-section-content content">
                                <div id="offer-for-sale">
                                    <div class="formbox formbox-sale">
                                        <h3 class="title">
                                            <?php echo $heading_sale; ?>
                                            <i class="fa fa-question-circle float-right grey-text" title="<?php echo $help_for_sale; ?>" data-placement="left" data-container="body" rel="tooltip"></i>
                                        </h3>
                                        <?php if ($for_sale) { ?>
                                        <span class="label"><input type="radio" name="for_sale" value="1" id="sale-yes" checked="checked" /><label for="sale-yes"><?php echo $text_yes; ?></label></span>
                                        <span class="label"><input type="radio" name="for_sale" value="0" id="sale-no" /><label for="sale-no"><?php echo $text_no; ?></label></span>
                                        <?php } else { ?>
                                        <span class="label"><input type="radio" name="for_sale" value="1" id="sale-yes" /><label for="sale-yes"><?php echo $text_yes; ?></label></span>
                                        <span class="label"><input type="radio" name="for_sale" value="0" id="sale-no" checked="checked" /><label for="sale-no"><?php echo $text_no; ?></label></span>
                                        <?php } ?>
                                        <?php if ($error_for_sale) { ?><span class="error"><i class="fa fa-exclamation-triangle"></i><?php echo $error_for_sale; ?></span><?php } ?>
                                    </div>
                                </div>

                                <div id="listing-value-section">

                                    <div class="formbox formbox-price">
                                        <p class="form-label">
                                      	    <strong><?php echo $entry_value . $help_optional; ?></strong>
                                            <i class="fa fa-question-circle float-right grey-text" title="<?php echo $entry_value . $help_optional; ?>" data-content="<?php echo $help_value; ?>" data-placement="left" rel="popover" data-container="body" data-trigger="hover"></i>
                                  	    </p>
                                        <input type="text" name="value" value="<?php echo $value; ?>" placeholder="<?php if ($currency['symbol_left']) { ?><?php echo $currency['symbol_left']; ?>&nbsp;<?php } ?><?php echo $entry_value . ' (' . $currency['code'] . ')'; ?><?php if ($currency['symbol_right']) { ?>&nbsp;<?php echo $currency['symbol_right']; ?><?php } ?>" size="6" />
                                        <p class="help"><?php echo $help_value_more; ?></p>
                                        <?php if ($error_value) { ?><span class="error"><i class="fa fa-exclamation-triangle"></i><?php echo $error_value; ?></span><?php } ?>
                                    </div>

                                </div>

                                <div id="for-sale-section">

                                    <div id="listing-price">
                                        <div class="formbox formbox-type">
                                            <p class="form-label">
                                                <strong><?php echo $entry_type; ?></strong>
                                                <i class="fa fa-question-circle float-right grey-text" title="<?php echo $entry_type . $help_required; ?>" data-content="<?php echo $help_type; ?>" data-placement="left" rel="popover" data-container="body" data-trigger="hover"></i>
                                            </p>
                                            <select name="type">
                                              <?php if ($type) { ?>
                                              <option value="1" selected="selected"><?php echo $text_buy_now; ?></option>
                                              <option value="0"><?php echo $text_classified; ?></option>
                                              <?php } else { ?>
                                              <option value="1"><?php echo $text_buy_now; ?></option>
                                              <option value="0" selected="selected"><?php echo $text_classified; ?></option>
                                              <?php } ?>
                                            </select>
                                            <p class="help"><?php echo $help_type_more; ?></p>
                                            <?php if ($error_type) { ?><span class="error"><i class="fa fa-exclamation-triangle"></i><?php echo $error_type; ?></span><?php } ?>
                                        </div>

                                        <h4 class="title hidden"><?php echo $heading_price; ?></h4>

                                        <div class="formbox formbox-price">
                                            <p class="form-label">
                                              <strong><?php echo $entry_price; ?></strong>
                                              <i class="fa fa-question-circle float-right grey-text" title="<?php echo $entry_price . $help_required; ?>" data-content="<?php echo $help_price; ?>" data-placement="left" rel="popover" data-container="body" data-trigger="hover"></i>
                                      	    </p>
                                            <input type="text" name="price" value="<?php echo $price; ?>" placeholder="<?php if ($currency['symbol_left']) { ?><?php echo $currency['symbol_left']; ?>&nbsp;<?php } ?><?php echo $entry_price . ' (' . $currency['code'] . ')'; ?><?php if ($currency['symbol_right']) { ?>&nbsp;<?php echo $currency['symbol_right']; ?><?php } ?>" size="6" />
                                            <?php if ($error_price) { ?><span class="error"><i class="fa fa-exclamation-triangle"></i><?php echo $error_price; ?></span><?php } ?>
                                        </div>

                                        <?php if ($this->config->get('member_tab_special') && $permissions['special_enabled']) { ?>
                                        <h4 class="sub-title"><?php echo $heading_special . ' / ' . $heading_discount ?> <i class="fa fa-certificate highlight"></i></h4>

                                        <div class="formbox formbox-price">
                                          <p class="form-label">
                                          	<strong><?php echo $entry_special; ?></strong>
                                            <i class="fa fa-question-circle float-right grey-text" title="<?php echo $entry_special . $help_optional; ?>" data-content="<?php echo $help_special; ?>" data-placement="left" rel="popover" data-container="body" data-trigger="hover"></i>
                                          </p>
                                          <input type="text" name="price_special" value="<?php echo $price_special; ?>" placeholder="<?php if ($currency['symbol_left']) { ?><?php echo $currency['symbol_left']; ?>&nbsp;<?php } ?><?php echo $entry_special . ' (' . $currency['code'] . ')'; ?><?php if ($currency['symbol_right']) { ?>&nbsp;<?php echo $currency['symbol_right']; ?><?php } ?>" size="6" />
                                          <?php if ($error_price_special) { ?><span class="error"><i class="fa fa-exclamation-triangle"></i><?php echo $error_price_special; ?></span><?php } ?>
                                        </div>
                                        <?php } ?>

                                        <?php if ($this->config->get('member_tab_discount') && $permissions['discount_enabled']) { ?>
                                        <h4 class="title hidden"><?php echo $heading_discount ?> <i class="fa fa-certificate highlight"></i></h4>

                                        <div class="formbox formbox-price">
                                          <p class="form-label">
                                          	<strong><?php echo $entry_discount; ?></strong>
                                            <i class="fa fa-question-circle float-right grey-text" title="<?php echo $entry_discount . $help_optional; ?>" data-content="<?php echo $help_discount; ?>" data-placement="left" rel="popover" data-container="body" data-trigger="hover"></i>
                                          </p>
                                          <input type="text" name="price_discount" value="<?php echo $price_discount; ?>" placeholder="<?php if ($currency['symbol_left']) { ?><?php echo $currency['symbol_left']; ?>&nbsp;<?php } ?><?php echo $entry_discount . ' (' . $currency['code'] . ')'; ?><?php if ($currency['symbol_right']) { ?>&nbsp;<?php echo $currency['symbol_right']; ?><?php } ?>" size="6" />
                                          <?php if ($error_price_discount) { ?><span class="error"><i class="fa fa-exclamation-triangle"></i><?php echo $error_price_discount; ?></span><?php } ?>
                                        </div>

                                        <div class="formbox formbox-type">
                                          <p class="form-label">
                                          	<strong><?php echo $entry_discount_quantity; ?></strong>
                                            <i class="fa fa-question-circle float-right grey-text" title="<?php echo $entry_discount_quantity; ?>" data-content="<?php echo $help_discount_quantity; ?>" data-placement="left" rel="popover" data-container="body" data-trigger="hover"></i>
                                          </p>
                                          <input type="text" name="discount_quantity" value="<?php echo $discount_quantity; ?>" placeholder="<?php echo $entry_discount_quantity; ?>" size="6" />
                                        </div>
                                        <?php } ?>

                                        <?php if ($permissions['inventory_enabled']) { ?>
                                        <div id="buy-now-options"><!-- START Buy-Now options -->

                                        <?php if ($this->config->get('member_data_field_quantity')) { ?>
                                        <h4 class="sub-title"><?php echo $heading_inventory; ?> <i class="fa fa-certificate highlight"></i></h4>

                                        <div class="formbox">
                                          <p class="form-label">
                                          	<strong><?php echo $entry_quantity; ?></strong>
                                            <i class="fa fa-question-circle float-right grey-text" title="<?php echo $help_quantity . $help_required; ?>" data-placement="left" data-container="body" rel="tooltip"></i>
                                      	  </p>
                                          <input type="text" name="quantity" value="<?php echo $quantity >= 0 ? $quantity : ''; ?>" placeholder="<?php echo $entry_quantity; ?>" />
                                          <?php if ($error_quantity) { ?><span class="error"><i class="fa fa-exclamation-triangle"></i><?php echo $error_quantity; ?></span><?php } ?>
                                        </div>

                                        <div class="formbox">
                                          <p class="form-label">
                                          	<strong><?php echo $entry_minimum; ?></strong>
                                            <i class="fa fa-question-circle float-right grey-text" title="<?php echo $help_minimum . $help_required; ?>" data-placement="left" data-container="body" rel="tooltip"></i>
                                          </p>
                                          <input type="text" name="minimum" value="<?php echo $minimum; ?>" placeholder="<?php echo $entry_minimum; ?>" />
                                          <?php if ($error_minimum) { ?><span class="error"><i class="fa fa-exclamation-triangle"></i><?php echo $error_minimum; ?></span><?php } ?>
                                        </div>

                                        <div class="formbox formbox-shipping">
                                            <p class="form-label">
                                                <strong><?php echo $entry_subtract; ?></strong>
                                            </p>
                                            <i class="fa fa-question-circle float-right grey-text" title="<?php echo $help_subtract; ?>" data-placement="left" data-container="body" rel="tooltip"></i>
                                            <?php if ($subtract) { ?>
                                            <span class="label"><input type="radio" name="subtract" value="1" id="subtract-yes" checked="checked" /><label for="subtract-yes"><?php echo $text_yes; ?></label></span>
                                            <span class="label"><input type="radio" name="subtract" value="0" id="subtract-no" /><label for="subtract-no"><?php echo $text_no; ?></label></span>
                                            <?php } else { ?>
                                            <span class="label"><input type="radio" name="subtract" value="1" id="subtract-yes" /><label for="subtract-yes"><?php echo $text_yes; ?></label></span>
                                            <span class="label"><input type="radio" name="subtract" value="0" id="subtract-no" checked="checked" /><label for="subtract-no"><?php echo $text_no; ?></label></span>
                                            <?php } ?>
                                        </div>

                                        <?php if ($this->config->get('member_data_field_stock')) { ?>
                                        <div class="formbox">
                                            <p class="form-label">
                                            	<strong><?php echo $entry_stock_status; ?></strong>
                                                <i class="fa fa-question-circle float-right grey-text" title="<?php echo $help_stock_status; ?>" data-placement="left" data-container="body" rel="tooltip"></i>
                                        	</p>
                                            <select name="stock_status_id" required="required">
                                              <?php foreach ($stock_statuses as $stock_status) { ?>
                                              <?php if ($stock_status['stock_status_id'] == $stock_status_id) { ?>
                                              <option value="<?php echo $stock_status['stock_status_id']; ?>" selected="selected"><?php echo $stock_status['name']; ?></option>
                                              <?php } else { ?>
                                              <option value="<?php echo $stock_status['stock_status_id']; ?>"><?php echo $stock_status['name']; ?></option>
                                              <?php } ?>
                                              <?php } ?>
                                            </select>
                                        </div>
                                        <?php } ?>
                                        <?php } ?>

        								<?php if ($permissions['tax_enabled'] && $this->config->get('member_data_field_tax')) { ?>
        								<div class="formbox">
        									<p class="form-label">
                                                <strong><?php echo $entry_tax_class; ?></strong>
                                                <i class="fa fa-question-circle float-right grey-text" title="<?php echo $help_tax_class; ?>" data-placement="left" data-container="body" rel="tooltip"></i>
                                            </p>
        									<select name="tax_class_id">
        									  <option value="0"><?php echo $text_none; ?></option>
        									  <?php foreach ($tax_classes as $tax_class) { ?>
        									  <?php if ($tax_class['tax_class_id'] == $tax_class_id) { ?>
        									  <option value="<?php echo $tax_class['tax_class_id']; ?>" selected="selected"><?php echo $tax_class['title']; ?></option>
        									  <?php } else { ?>
        									  <option value="<?php echo $tax_class['tax_class_id']; ?>"><?php echo $tax_class['title']; ?></option>
        									  <?php } ?>
        									  <?php } ?>
        									</select>
        								</div>
        								<?php } ?>

                                        </div><!-- END Buy-Now options -->
                                        <?php } ?>

                                    </div>

                                    <div id="part-numbers">
                                        <?php if ($this->config->get('member_data_field_part_numbers') && $permissions['inventory_enabled']) { ?>
                                        <h3 class="title"><?php echo $heading_part_numbers; ?> <i class="fa fa-certificate highlight"></i></h3>
                                        <?php if ($error_part_number) { ?><div class="formbox"><span class="error"><i class="fa fa-exclamation-triangle"></i><?php echo $error_part_number; ?></span></div><?php } ?>

                                        <div class="formbox">
                                          <p class="form-label">
                                              <strong><?php echo $entry_mpn; ?></strong>
                                              <i class="fa fa-question-circle float-right grey-text" title="<?php echo $help_mpn . $help_optional; ?>" data-placement="left" data-container="body" rel="tooltip"></i>
                                          </p>
                                          <input type="text" name="mpn" value="<?php echo $mpn; ?>" placeholder="<?php echo $entry_mpn; ?>" />
                                        </div>

                                        <div class="formbox">
                                          <p class="form-label">
                                              <strong><?php echo $entry_sku; ?></strong>
                                              <i class="fa fa-question-circle float-right grey-text" title="<?php echo $help_sku . $help_optional; ?>" data-placement="left" data-container="body" rel="tooltip"></i>
                                          </p>
                                          <input type="text" name="sku" value="<?php echo $sku; ?>" placeholder="<?php echo $entry_sku; ?>" />
                                        </div>

                                        <div class="formbox">
                                          <p class="form-label">
                                              <strong><?php echo $entry_upc; ?></strong>
                                              <i class="fa fa-question-circle float-right grey-text" title="<?php echo $help_upc . $help_optional; ?>" data-placement="left" data-container="body" rel="tooltip"></i>
                                          </p>
                                          <input type="text" name="upc" value="<?php echo $upc; ?>" placeholder="<?php echo $entry_upc; ?>" />
                                        </div>

                                        <div class="formbox">
                                          <p class="form-label">
                                              <strong><?php echo $entry_ean; ?></strong>
                                              <i class="fa fa-question-circle float-right grey-text" title="<?php echo $help_ean . $help_optional; ?>" data-placement="left" data-container="body" rel="tooltip"></i>
                                          </p>
                                          <input type="text" name="ean" value="<?php echo $ean; ?>" placeholder="<?php echo $entry_ean; ?>" />
                                        </div>

                                        <div class="formbox">
                                          <p class="form-label">
                                              <strong><?php echo $entry_jan; ?></strong>
                                              <i class="fa fa-question-circle float-right grey-text" title="<?php echo $help_jan . $help_optional; ?>" data-placement="left" data-container="body" rel="tooltip"></i>
                                          </p>
                                          <input type="text" name="jan" value="<?php echo $jan; ?>" placeholder="<?php echo $entry_jan; ?>" />
                                        </div>
                                        <?php } ?>
                                    </div>
                                </div>

    							<?php if ($this->config->get('member_data_field_shipping')) { ?>
                                <?php $shipping_rate_all = !empty($product_shipping['0']) ? $product_shipping['0'] : array('first' => '', 'additional' => ''); ?>
                                <div id="offer-shipping">
                                    <div class="formbox formbox-shipping">
                                        <h3 class="title">
                                            <?php echo $heading_shipping; ?>
                                            <i class="fa fa-question-circle float-right grey-text" title="<?php echo $help_shipping; ?>" data-placement="left" data-container="body" rel="tooltip"></i>
                                        </h3>
                                        <?php if ($shipping) { ?>
                                        <span class="label"><input type="radio" name="shipping" value="1" id="shipping-yes" checked="checked" /><label for="shipping-yes"><?php echo $text_yes; ?></label></span>
                                        <span class="label"><input type="radio" name="shipping" value="0" id="shipping-no" /><label for="shipping-no"><?php echo $text_no; ?></label></span>
                                        <?php } else { ?>
                                        <span class="label"><input type="radio" name="shipping" value="1" id="shipping-yes" /><label for="shipping-yes"><?php echo $text_yes; ?></label></span>
                                        <span class="label"><input type="radio" name="shipping" value="0" id="shipping-no" checked="checked" /><label for="shipping-no"><?php echo $text_no; ?></label></span>
                                        <?php } ?>
                                    </div>
                                </div>

                                <div id="shipping-section">

                                    <h4 class="sub-title">
                                        <?php echo $product_shipping_rates ? $heading_shipping_rates : $entry_shipping_custom; ?>
                                        <i class="fa fa-question-circle float-right grey-text" title="<?php echo $help_shipping_cost . $help_optional; ?>" data-placement="left" data-container="body" rel="tooltip"></i>
                                    </h4>

									<div id="shipping-custom" class="formbox">
                                        <?php if ($product_shipping_rates) { ?>
                                        <p>
                                            <span class="help"><?php echo $help_shipping_custom; ?></span>
                                            <?php if ($error_shipping) { ?><span class="error"><i class="fa fa-exclamation-triangle"></i><?php echo $error_shipping; ?></span><?php } ?>
                                        </p>
										<table class="form">
											<thead>
                                                <tr>
                                                    <th class="left"><?php echo $column_geo_zone; ?>&nbsp;<small><?php echo $help_shipping_list; ?></small></th>
                                                    <th class="left"><?php echo $column_fee; ?></th>
                                                </tr>
											</thead>
											<tbody>
                                                <tr>
                                                    <th class="left">
                                                        <a class="button button_alt smaller" id="shipping-apply-all">
                                                            <i class="fa fa-bolt"></i> <?php echo $text_apply_all; ?>
                                                        </a>
                                                    </th>
                                                    <th class="left" colspan="2">
                                                        <input type="text"
                                                            name="product_shipping[0][first]"
                                                            value="<?php echo (($shipping_rate_all['first'] > 0 || $shipping_rate_all['first'] == '0' || $shipping_rate_all['first'] == '0.00') ? number_format((float)$shipping_rate_all['first'], 2, '.', '') : ''); ?>"
                                                            size="4"
                                                            id="apply_all" />
                                                        <input type="hidden" name="product_shipping[0][geo_zone_id]" value="0" />
                                                    </th>
                                                </tr>
                                                <tr>
                                                    <th></th>
                                                    <th class="left"><?php echo $help_shipping_first; ?></th>
                                                    <th class="left"><?php echo $help_shipping_additional; ?></th>
                                                </tr>
                                            <?php $x = 1; ?>
											<?php foreach ($geo_zones as $geo_zone) { ?>
												<?php $geo_zone_rates = !empty($product_shipping[$geo_zone['geo_zone_id']]) ? $product_shipping[$geo_zone['geo_zone_id']] : array('first' => '', 'additional' => ''); ?>
												<tr>
													<td class="left">
                                                        <span>
                                                            <?php echo $geo_zone['name']; ?>
                                                            <i class="fa fa-question-circle float-right grey-text" title="<?php echo $geo_zone['description']; ?>" data-placement="top" data-container="body" rel="tooltip"></i>
                                                        </span>
                                                        <input type="hidden" name="product_shipping[<?php echo $x; ?>][geo_zone_id]" value="<?php echo $geo_zone['geo_zone_id']; ?>" />
                                                    </td>
													<td class="left">
														<input type="text"
                                                            name="product_shipping[<?php echo $x; ?>][first]"
                                                            value="<?php echo (($geo_zone_rates['first'] > 0 || $geo_zone_rates['first'] == '0' || $geo_zone_rates['first'] == '0.00') ? number_format((float)$geo_zone_rates['first'], 2, '.', '') : ''); ?>"
                                                            size="4" />
													</td>
													<td class="left">
														<input type="text"
                                                            name="product_shipping[<?php echo $x; ?>][additional]"
                                                            value="<?php echo (($geo_zone_rates['additional'] > 0 || $geo_zone_rates['additional'] == '0' || $geo_zone_rates['additional'] == '0.00') ? number_format((float)$geo_zone_rates['additional'], 2, '.', '') : ''); ?>"
                                                            size="4" />
													</td>
												</tr>
											<?php $x++; ?>
											<?php } ?>
											</tbody>
										</table>
                                        <?php } else { ?>
                                        <div class="formbox">
                                          <p class="form-label">
                                              <?php echo $column_fee; ?>
                                              <i class="fa fa-question-circle float-right grey-text" title="<?php echo $entry_shipping_custom . $help_optional; ?>" data-content="<?php echo $help_shipping_custom; ?>" data-placement="left" rel="popover" data-container="body" data-trigger="hover"></i>
                                          </p>
                                          <input type="text"
                                              name="product_shipping[0][first]"
                                              value="<?php echo (($shipping_rate_all['first'] > 0 || $shipping_rate_all['first'] == '0' || $shipping_rate_all['first'] == '0.00') ? number_format((float)$shipping_rate_all['first'], 2, '.', '') : ''); ?>"
                                              size="4"
                                              title="<?php echo $entry_shipping_custom; ?>" />
                                          <input type="hidden" name="product_shipping[0][geo_zone_id]" value="0" />
                                        </div>
                                        <?php } ?>
									</div>

                                    <?php if ($this->config->get('member_data_field_dimensions') || $this->config->get('member_data_field_weight')) { ?>
    								<?php if ($this->config->get('member_data_field_dimensions')) { ?>
    								<h4 class="sub-title"><?php echo $heading_dimension; ?></h4>
    								<?php if ($error_dimensions) { ?><p class="error"><i class="fa fa-info-circle"></i> <?php echo $error_dimensions; ?></p><?php } ?>

                                    <div class="formbox">
    								  <p class="form-label">
                                          <strong><?php echo $entry_length_class; ?></strong>
                                          <i class="fa fa-question-circle float-right grey-text" title="<?php echo $help_length_class; ?>" data-placement="left" data-container="body" rel="tooltip"></i>
                                      </p>
    								  <select name="length_class_id">
    									  <option value="0"><?php echo $text_select_dimension; ?></option>
    									  <?php foreach ($length_classes as $length_class) { ?>
    									  <?php if ($length_class['length_class_id'] == $length_class_id) { ?>
    									  <option value="<?php echo $length_class['length_class_id']; ?>" selected="selected"><?php echo $length_class['title']; ?></option>
    									  <?php } else { ?>
    									  <option value="<?php echo $length_class['length_class_id']; ?>"><?php echo $length_class['title']; ?></option>
    									  <?php } ?>
    									  <?php } ?>
    									</select>
    								</div>

    								<div class="formbox">
    								  <p class="form-label">
                                          <strong><?php echo $entry_length; ?></strong>
                                          <i class="fa fa-question-circle float-right grey-text" title="<?php echo $help_length; ?>" data-placement="left" data-container="body" rel="tooltip"></i>
                                      </p>
    									<input type="text" name="length" value="<?php echo $length; ?>" size="4" placeholder="<?php echo $entry_length; ?>" />
    								</div>

                                    <div class="formbox">
    								  <p class="form-label">
                                          <strong><?php echo $entry_width; ?></strong>
                                          <i class="fa fa-question-circle float-right grey-text" title="<?php echo $help_width; ?>" data-placement="left" data-container="body" rel="tooltip"></i>
                                      </p>
    									<input type="text" name="width" value="<?php echo $width; ?>" size="4" placeholder="<?php echo $entry_width; ?>" />
    								</div>

                                    <div class="formbox">
    								  <p class="form-label">
                                          <strong><?php echo $entry_height; ?></strong>
                                          <i class="fa fa-question-circle float-right grey-text" title="<?php echo $help_height; ?>" data-placement="left" data-container="body" rel="tooltip"></i>
                                      </p>
    									<input type="text" name="height" value="<?php echo $height; ?>" size="4" placeholder="<?php echo $entry_height; ?>" />
    								</div>
    								<?php } ?>

    								<?php if ($this->config->get('member_data_field_weight')) { ?>
    								<h4 class="sub-title"><?php echo $heading_weight; ?></h4>
    								<?php if ($error_weight) { ?><p class="error"><i class="fa fa-info-circle"></i> <?php echo $error_weight; ?></p><?php } ?>

                                    <div class="formbox">
    								  <p class="form-label">
                                          <strong><?php echo $entry_weight_class; ?></strong>
                                          <i class="fa fa-question-circle float-right grey-text" title="<?php echo $help_weight_class; ?>" data-placement="left" data-container="body" rel="tooltip"></i>
                                      </p>
    								  <select name="weight_class_id">
    									  <option value="0"><?php echo $text_select_weight; ?></option>
    									  <?php foreach ($weight_classes as $weight_class) { ?>
    									  <?php if ($weight_class['weight_class_id'] == $weight_class_id) { ?>
    									  <option value="<?php echo $weight_class['weight_class_id']; ?>" selected="selected"><?php echo $weight_class['title']; ?></option>
    									  <?php } else { ?>
    									  <option value="<?php echo $weight_class['weight_class_id']; ?>"><?php echo $weight_class['title']; ?></option>
    									  <?php } ?>
    									  <?php } ?>
    									</select>
    								</div>

                                    <div class="formbox">
    								  <p class="form-label">
                                          <strong><?php echo $entry_weight; ?></strong>
                                          <i class="fa fa-question-circle float-right grey-text" title="<?php echo $help_weight; ?>" data-placement="left" data-container="body" rel="tooltip"></i>
                                      </p>
    								  <input type="text" name="weight" value="<?php echo $weight; ?>" placeholder="<?php echo $entry_weight; ?>" />
    								</div>
    								<?php } ?>

                                    <?php } ?>

                                </div>
                                <?php } ?>

    							<div class="buttons buttons-middle hidden">
    								<div class="center">
    									<a class="button button_back button_alt hidden trigger-click" data-target="tab-image" title="<?php echo $text_previous . ': ' . $tab_image; ?>" rel="tooltip" data-container="body"><i class="fa fa-arrow-up"></i><?php echo $text_previous; ?></a>
    								</div>
    							</div>
                            </div>

						</div> <!-- sale -->

						<?php if ($permissions['download_enabled'] && $this->config->get('member_tab_download')) { ?>
						<div id="tab-download" class="post-section-container">
                            <h2 class="post-section-heading"><?php echo $tab_download; ?><span class="toggle-icon"><i class="fa fa-chevron-down icon-ac-down"></i><i class="fa fa-chevron-up icon-ac-up"></i></span></h2>

                            <div class="post-section-content content">
    						  <table id="product-download" class="list">
    							<thead>
    							  <tr>
    							<td class="left"><?php echo $entry_download_name; ?></td>
    							<td class="left"><?php echo $entry_download_filename; ?></td>
    							<td></td>
    							  </tr>
    							</thead>
    							<?php $product_download_row = 0; ?>
    							<?php foreach ($product_download as $product_download) { ?>
    							<tbody id="product-download-row<?php echo $product_download_row; ?>" class="product-download-row" data-row="<?php echo $product_download_row; ?>">
    							  <tr>
    								<td class="left">
    									<input type="text" name="product_download[<?php echo $product_download_row; ?>][name]" value="<?php echo $product_download['name']; ?>" id="file<?php echo $product_download_row; ?>" />
    									<input type="hidden" name="product_download[<?php echo $product_download_row; ?>][download_id]" value="<?php echo $product_download['download_id']; ?>">
    								</td>
    								<td class="left">
    									<input type="text" readonly="readonly" size="40" name="product_download[<?php echo $product_download_row; ?>][mask]" value="<?php echo $product_download['mask']; ?>"  id="mask<?php echo $product_download_row; ?>" /> <a id="file-upload<?php echo $product_download_row; ?>" class="button"><?php echo $button_upload_digital_download; ?></a>
    									<input type="hidden" name="product_download[<?php echo $product_download_row; ?>][filename]" value="<?php echo $product_download['filename']; ?>"  id="filename<?php echo $product_download_row; ?>" />
    									<input type="hidden" name="product_download[<?php echo $product_download_row; ?>][remaining]" value="<?php echo $product_download['remaining']; ?>"  id="remaining<?php echo $product_download_row; ?>" />
    								</td>
    								<td class="right"><a class="button button_remove"><?php echo $button_remove; ?></a></td>
    								</tr>
    							</tbody>
    							<?php $product_download_row++; ?>
    							<?php } ?>
    							<tfoot>
    							  <tr>
    							<td colspan="2"></td>
    							<td class="right"><a onclick="addDownload()" class="button"><?php echo $button_add_digital_download; ?></a></td>
    							  </tr>
    							</tfoot>
    						  </table>
                          </div>

                        </div><!-- tab-download -->
						<?php } ?>

						<?php if ($permissions['attribute_enabled'] && $this->config->get('member_tab_attribute')) { ?>
						<div id="tab-attribute" class="post-section-container">
                            <h2 class="post-section-heading"><?php echo $tab_attribute; ?><span class="toggle-icon"><i class="fa fa-chevron-down icon-ac-down"></i><i class="fa fa-chevron-up icon-ac-up"></i></span></h2>

                            <div class="post-section-content content">
    						  <table id="attribute" class="list">
    							<thead>
    							  <tr>
    								<td class="left"><?php echo $entry_attribute; ?></td>
    								<td class="left"><?php echo $entry_text; ?></td>
    								<td></td>
    							  </tr>
    							</thead>
    							<?php $attribute_row = 0; ?>
    							<?php foreach ($product_attributes as $product_attribute) { ?>
    							<tbody id="attribute-row<?php echo $attribute_row; ?>" class="attribute-row" data-row="<?php echo $attribute_row; ?>">
    							  <tr>
    								<td class="left"><input type="text" name="product_attribute[<?php echo $attribute_row; ?>][name]" value="<?php echo $product_attribute['name']; ?>" />
    								  <input type="hidden" name="product_attribute[<?php echo $attribute_row; ?>][attribute_id]" value="<?php echo $product_attribute['attribute_id']; ?>" /></td>
    								<td class="left"><?php foreach ($languages as $language) { ?>
    								  <textarea name="product_attribute[<?php echo $attribute_row; ?>][product_attribute_description][<?php echo $language['language_id']; ?>][text]" cols="40" rows="5"><?php echo isset($product_attribute['product_attribute_description'][$language['language_id']]) ? $product_attribute['product_attribute_description'][$language['language_id']]['text'] : ''; ?></textarea>
    								  <img src="image/flags/<?php echo $language['image']; ?>" title="<?php echo $language['name']; ?>" align="top" /><br />
    								  <?php } ?></td>
    								<td class="left"><a class="button button_remove"><?php echo $button_remove; ?></a></td>
    							  </tr>
    							</tbody>
    							<?php $attribute_row++; ?>
    							<?php } ?>
    							<tfoot>
    							  <tr>
    								<td colspan="2"><small><?php echo $help_attribute_list; ?></small></td>
    								<td class="left"><a onclick="addAttribute();" class="button"><?php echo $button_add_attribute; ?></a></td>
    							  </tr>
    							</tfoot>
    						  </table>
                          </div>

                        </div><!-- tab-attribute -->
						<?php } ?>

						<?php if ($permissions['option_enabled'] && $this->config->get('member_tab_option')) { ?>
						<div id="tab-option" class="post-section-container">
                            <h2 class="post-section-heading"><?php echo $tab_option; ?><span class="toggle-icon"><i class="fa fa-chevron-down icon-ac-down"></i><i class="fa fa-chevron-up icon-ac-up"></i></span></h2>

                            <div class="post-section-content content">
    						  <div id="vtab-option" class="vtabs">
    							<?php $option_row = 0; ?>
    							<?php foreach ($product_options as $product_option) { ?>
    							<a href="#tab-option-<?php echo $option_row; ?>" id="option-<?php echo $option_row; ?>"><?php echo $product_option['name']; ?>&nbsp;<i class="fa fa-times-circle" alt="<?php echo $button_remove; ?>" class="icon_remove" data-row="<?php echo $option_row; ?>"></i></a>
    							<?php $option_row++; ?>
    							<?php } ?>
    							<span id="option-add">
    							<input name="option" value="" style="width: 130px;" />
    							&nbsp;<i id="option-add-button" class="fa fa-plus-square" alt="<?php echo $button_add_option; ?>" title="<?php echo $button_add_option; ?>"></i></span></div>
    						  <?php $option_row = 0; ?>
    						  <?php $option_value_row = 0; ?>
    						  <?php foreach ($product_options as $product_option) { ?>
    						  <div id="tab-option-<?php echo $option_row; ?>" class="vtabs-content option-row" data-row="<?php echo $option_row; ?>">
    							<input type="hidden" name="product_option[<?php echo $option_row; ?>][product_option_id]" value="<?php echo $product_option['product_option_id']; ?>" />
    							<input type="hidden" name="product_option[<?php echo $option_row; ?>][name]" value="<?php echo $product_option['name']; ?>" />
    							<input type="hidden" name="product_option[<?php echo $option_row; ?>][option_id]" value="<?php echo $product_option['option_id']; ?>" />
    							<input type="hidden" name="product_option[<?php echo $option_row; ?>][type]" value="<?php echo $product_option['type']; ?>" />
    							<table class="form">
    							  <tr>
    								<td><?php echo $entry_required; ?></td>
    								<td><select name="product_option[<?php echo $option_row; ?>][required]">
    									<?php if ($product_option['required']) { ?>
    									<option value="1" selected="selected"><?php echo $text_yes; ?></option>
    									<option value="0"><?php echo $text_no; ?></option>
    									<?php } else { ?>
    									<option value="1"><?php echo $text_yes; ?></option>
    									<option value="0" selected="selected"><?php echo $text_no; ?></option>
    									<?php } ?>
    								  </select></td>
    							  </tr>
    							  <?php if ($product_option['type'] == 'text') { ?>
    							  <tr>
    								<td><?php echo $entry_option_value; ?></td>
    								<td><input type="text" name="product_option[<?php echo $option_row; ?>][option_value]" value="<?php echo $product_option['option_value']; ?>" /></td>
    							  </tr>
    							  <?php } ?>
    							  <?php if ($product_option['type'] == 'textarea') { ?>
    							  <tr>
    								<td><?php echo $entry_option_value; ?></td>
    								<td><textarea name="product_option[<?php echo $option_row; ?>][option_value]" cols="40" rows="5"><?php echo $product_option['option_value']; ?></textarea></td>
    							  </tr>
    							  <?php } ?>
    							  <?php if ($product_option['type'] == 'file') { ?>
    							  <tr style="display: none;">
    								<td><?php echo $entry_option_value; ?></td>
    								<td><input type="text" name="product_option[<?php echo $option_row; ?>][option_value]" value="<?php echo $product_option['option_value']; ?>" /></td>
    							  </tr>
    							  <?php } ?>
    							  <?php if ($product_option['type'] == 'date') { ?>
    							  <tr>
    								<td><?php echo $entry_option_value; ?></td>
    								<td><input type="text" name="product_option[<?php echo $option_row; ?>][option_value]" value="<?php echo $product_option['option_value']; ?>" class="date" /></td>
    							  </tr>
    							  <?php } ?>
    							  <?php if ($product_option['type'] == 'datetime') { ?>
    							  <tr>
    								<td><?php echo $entry_option_value; ?></td>
    								<td><input type="text" name="product_option[<?php echo $option_row; ?>][option_value]" value="<?php echo $product_option['option_value']; ?>" class="datetime" /></td>
    							  </tr>
    							  <?php } ?>
    							  <?php if ($product_option['type'] == 'time') { ?>
    							  <tr>
    								<td><?php echo $entry_option_value; ?></td>
    								<td><input type="text" name="product_option[<?php echo $option_row; ?>][option_value]" value="<?php echo $product_option['option_value']; ?>" class="time" /></td>
    							  </tr>
    							  <?php } ?>
    							</table>
    							<?php if ($product_option['type'] == 'select' || $product_option['type'] == 'radio' || $product_option['type'] == 'checkbox' || $product_option['type'] == 'image') { ?>
    							<table id="option-value<?php echo $option_row; ?>" class="list">
    							  <thead>
    								<tr>
    								  <td class="left"><?php echo $entry_option_value; ?></td>
    								  <td class="right"><?php echo $entry_quantity; ?></td>
    								  <td class="left"><?php echo $entry_subtract; ?></td>
    								  <td class="right"><?php echo $entry_price; ?></td>
    								  <td class="right"><?php echo $entry_option_points; ?></td>
    								  <td class="right"><?php echo $entry_weight; ?></td>
    								  <td></td>
    								</tr>
    							  </thead>
    							  <?php foreach ($product_option['product_option_value'] as $product_option_value) { ?>
    							  <tbody id="option-value-row<?php echo $option_value_row; ?>" class="option-value-row" data-row="<?php echo $option_value_row; ?>">
    								<tr>
    								  <td class="left"><select name="product_option[<?php echo $option_row; ?>][product_option_value][<?php echo $option_value_row; ?>][option_value_id]">
    									  <?php if (isset($option_values[$product_option['option_id']])) { ?>
    									  <?php foreach ($option_values[$product_option['option_id']] as $option_value) { ?>
    									  <?php if ($option_value['option_value_id'] == $product_option_value['option_value_id']) { ?>
    									  <option value="<?php echo $option_value['option_value_id']; ?>" selected="selected"><?php echo $option_value['name']; ?></option>
    									  <?php } else { ?>
    									  <option value="<?php echo $option_value['option_value_id']; ?>"><?php echo $option_value['name']; ?></option>
    									  <?php } ?>
    									  <?php } ?>
    									  <?php } ?>
    									</select>
    									<input type="hidden" name="product_option[<?php echo $option_row; ?>][product_option_value][<?php echo $option_value_row; ?>][product_option_value_id]" value="<?php echo $product_option_value['product_option_value_id']; ?>" /></td>
    								  <td class="right"><input type="text" name="product_option[<?php echo $option_row; ?>][product_option_value][<?php echo $option_value_row; ?>][quantity]" value="<?php echo $product_option_value['quantity']; ?>" size="3" /></td>
    								  <td class="left"><select name="product_option[<?php echo $option_row; ?>][product_option_value][<?php echo $option_value_row; ?>][subtract]">
    									  <?php if ($product_option_value['subtract']) { ?>
    									  <option value="1" selected="selected"><?php echo $text_yes; ?></option>
    									  <option value="0"><?php echo $text_no; ?></option>
    									  <?php } else { ?>
    									  <option value="1"><?php echo $text_yes; ?></option>
    									  <option value="0" selected="selected"><?php echo $text_no; ?></option>
    									  <?php } ?>
    									</select></td>
    								  <td class="right"><select name="product_option[<?php echo $option_row; ?>][product_option_value][<?php echo $option_value_row; ?>][price_prefix]">
    									  <?php if ($product_option_value['price_prefix'] == '+') { ?>
    									  <option value="+" selected="selected">+</option>
    									  <?php } else { ?>
    									  <option value="+">+</option>
    									  <?php } ?>
    									  <?php if ($product_option_value['price_prefix'] == '-') { ?>
    									  <option value="-" selected="selected">-</option>
    									  <?php } else { ?>
    									  <option value="-">-</option>
    									  <?php } ?>
    									</select>
    									<input type="text" name="product_option[<?php echo $option_row; ?>][product_option_value][<?php echo $option_value_row; ?>][price]" value="<?php echo $product_option_value['price']; ?>" size="5" /></td>
    								  <td class="right"><select name="product_option[<?php echo $option_row; ?>][product_option_value][<?php echo $option_value_row; ?>][points_prefix]">
    									  <?php if ($product_option_value['points_prefix'] == '+') { ?>
    									  <option value="+" selected="selected">+</option>
    									  <?php } else { ?>
    									  <option value="+">+</option>
    									  <?php } ?>
    									  <?php if ($product_option_value['points_prefix'] == '-') { ?>
    									  <option value="-" selected="selected">-</option>
    									  <?php } else { ?>
    									  <option value="-">-</option>
    									  <?php } ?>
    									</select>
    									<input type="text" name="product_option[<?php echo $option_row; ?>][product_option_value][<?php echo $option_value_row; ?>][points]" value="<?php echo $product_option_value['points']; ?>" size="5" /></td>
    								  <td class="right"><select name="product_option[<?php echo $option_row; ?>][product_option_value][<?php echo $option_value_row; ?>][weight_prefix]">
    									  <?php if ($product_option_value['weight_prefix'] == '+') { ?>
    									  <option value="+" selected="selected">+</option>
    									  <?php } else { ?>
    									  <option value="+">+</option>
    									  <?php } ?>
    									  <?php if ($product_option_value['weight_prefix'] == '-') { ?>
    									  <option value="-" selected="selected">-</option>
    									  <?php } else { ?>
    									  <option value="-">-</option>
    									  <?php } ?>
    									</select>
    									<input type="text" name="product_option[<?php echo $option_row; ?>][product_option_value][<?php echo $option_value_row; ?>][weight]" value="<?php echo $product_option_value['weight']; ?>" size="5" /></td>
    								  <td class="left"><a class="button button_remove"><?php echo $button_remove; ?></a></td>
    								</tr>
    							  </tbody>
    							  <?php $option_value_row++; ?>
    							  <?php } ?>
    							  <tfoot>
    								<tr>
    								  <td colspan="6"><small><?php echo $help_options_list; ?></small></td>
    								  <td class="left"><a onclick="addOptionValue('<?php echo $option_row; ?>');" class="button"><?php echo $button_add_option_value; ?></a></td>
    								</tr>
    							  </tfoot>
    							</table>
    							<select id="option-values<?php echo $option_row; ?>" style="display: none;">
    							  <?php if (isset($option_values[$product_option['option_id']])) { ?>
    							  <?php foreach ($option_values[$product_option['option_id']] as $option_value) { ?>
    							  <option value="<?php echo $option_value['option_value_id']; ?>"><?php echo $option_value['name']; ?></option>
    							  <?php } ?>
    							  <?php } ?>
    							</select>
    							<?php } ?>
    						  </div>
    						  <?php $option_row++; ?>
    						  <?php } ?>
                            </div>

						</div>
						<?php } ?>

						<input id="account-post-form-submit" type="submit" value="<?php echo $button_save; ?>" class="button hidden" />

    					<div class="buttons">
                            <h3 class="title hidden"><?php echo $entry_status; ?></h3>
                            <p class="text-center text-larger"><span class="help"><?php echo $help_status; ?></span></p>
                            <div class="center">
                                <div class="enable-disable-buttons">
                                    <div class="grid-2">
                                        <a class="button bigger <?php echo ($status) ? 'button_yes' : 'button_cancel'; ?> fullwidth" data-value="1"><i class="fa fa-eye"></i><?php echo $button_enable; ?></a>
                                    </div>
                                    <div class="grid-2">
                                        <a class="button bigger <?php echo (!$status) ? 'button_no' : 'button_cancel'; ?> fullwidth" data-value="0"><i class="fa fa-eye-slash"></i><?php echo $button_disable; ?></a>
                                    </div>
                                    <input type="hidden" value="<?php echo $status; ?>" id="status" name="status" />
                                </div>
                            </div>
                        </div>

                        <div class="buttons">
                            <p class="text-center text-larger"><span class="help"><?php echo !$permissions['inventory_enabled'] ? $text_post_footer_expire : $text_post_footer; ?></span></p>
    						<div class="center">
                                <label for="account-post-form-submit" class="button button-submit button_highlight bigger fullwidth"><i class="fa fa-save"></i> <?php echo $button_save; ?></label>
    							<?php if ($permissions['inventory_enabled']) { ?>
                                <span class="terms highlight"><i class="fa fa-certificate highlight"></i><?php echo $text_premium_field; ?></span>&emsp;
                                <?php } ?>
    							<span class="terms"><a class="colorbox" href="<?php echo $help; ?>"><i class="fa fa-question"></i><?php echo $text_help; ?></a></span>&emsp;
                                <span class="terms"><a href="<?php echo $cancel; ?>"><span class="help"><i class="fa fa-ban"></i><?php echo $button_cancel; ?></span></a></span>
                            </div>
    					</div>

					</form>

                    <input type="hidden" name="text_select_manufacturer" value="<?php echo $text_select_manufacturer; ?>" />
                    <input type="hidden" name="text_manufacturer_other" value="<?php echo $text_manufacturer_other; ?>" />
                    <input type="hidden" name="text_select_category" value="<?php echo $text_select_category; ?>" />
                    <input type="hidden" name="text_select_category_sub" value="<?php echo $text_select_category_sub; ?>" />
                    <input type="hidden" name="text_select_category_count" value="<?php echo $text_select_category_count; ?>" />
                    <input type="hidden" name="text_select_category_sub_count" value="<?php echo $text_select_category_sub_count; ?>" />
                    <input type="hidden" name="text_select_manufacturer_count" value="<?php echo $text_select_manufacturer_count; ?>" />
                    <input type="hidden" name="text_select_zone" value="<?php echo $text_select_zone; ?>" />
                    <input type="hidden" name="manufacturers_all" value="<?php echo htmlspecialchars(json_encode($manufacturers), ENT_COMPAT); ?>" />
                    <input type="hidden" name="categories_complete" value="<?php echo $categories_complete; ?>" />

				</div>
                <?php echo $content_bottom; ?>
            </div>
        </div>
        <!-- <?php // echo $column_right; ?> -->
    </div>
</div>
<script type="text/javascript"><!--
// var listingId = '<?php echo $product_id; ?>';
var productNoImage = '<?php echo $no_image; ?>';
var errorMaxImages = '<?php echo sprintf($error_max_images, $this->config->get('member_image_max_number')); ?>';
var image_row = <?php echo $image_row; ?>;
var imageMax = <?php echo $this->config->get('member_image_max_number'); ?>;
var imageWidth = <?php echo $this->config->get('config_image_product_width'); ?>;
var imageHeight = <?php echo $this->config->get('config_image_product_height'); ?>;
var product_download_row = <?php echo ($permissions['download_enabled'] && $this->config->get('member_tab_download')) ? $product_download_row : 0; ?>;
var attribute_row = <?php echo ($permissions['attribute_enabled'] && $this->config->get('member_tab_attribute')) ? $attribute_row : 0; ?>;
var option_row = <?php echo ($permissions['option_enabled'] && $this->config->get('member_tab_option')) ? $option_row : 0; ?>;
var option_value_row = <?php echo ($permissions['option_enabled'] && $this->config->get('member_tab_option')) ? $option_value_row : 0; ?>;
var textWait = '<?php echo $text_wait; ?>';
var textYes = '<?php echo $text_yes; ?>';
var textNo = '<?php echo $text_no; ?>';
var textNone = '<?php echo $text_none; ?>';
var textBrowse = '<?php echo $text_browse; ?>';
var textImageManager = '<?php echo $text_image_manager; ?>';
var buttonClear = '<?php echo $button_clear; ?>';
var buttonRemove = '<?php echo $button_remove; ?>';
var buttonUpload = '<?php echo $button_upload; ?>';
var buttonUploadDigitalDownload = '<?php echo $button_upload_digital_download; ?>';
var buttonAddOptionValue = '<?php echo $button_add_option_value; ?>';
var entryQuantity = '<?php echo $entry_quantity; ?>';
var entryRequired = '<?php echo $entry_required; ?>';
var entryOptionValue = '<?php echo $entry_option_value; ?>';
var entrySubtract = '<?php echo $entry_subtract; ?>';
var entryPrice = '<?php echo $entry_price; ?>';
var entryOptionPoints = '<?php echo $entry_option_points; ?>';
var entryWeight = '<?php echo $entry_weight; ?>';
var entryQuantity = '<?php echo $entry_quantity; ?>';
var languages = [];
<?php foreach ($languages as $language) { ?>
languages.push({
    id: <?php echo $language['language_id']; ?>,
    image: '<?php echo $language['image']; ?>',
    name: '<?php echo $language['name']; ?>'
});
<?php } ?>
<?php if ($permissions['inventory_enabled']) { ?>
var permissionsInventoryEnabled = true;
<?php } ?>
//--></script>
<?php echo $footer; ?>
