<?php echo $header; ?>
<div class="container-page anonpost-form-page">
    <div class="breadcrumb">
        <div class="layout">
            <h1><a href="<?php echo $page; ?>"><i class="fa fa-pencil"></i><?php echo $heading_title; ?></a></h1>
            <div class="links">
                <?php foreach ($breadcrumbs as $breadcrumb) { ?>
                <?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
                <?php } ?>
            </div>
        </div>
    </div>
    <div class="layout layout-left-minus-right">

        <div class="container-left">
            <div class="content-page anonpost-listing">
            	<?php echo $notification; ?>

            	<?php echo $content_top; ?>

                <div class="widget">

					<h6><?php echo $heading_sub_title; ?></h6>

                    <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form" class="account-product-form">
    					<div class="content">

                            <p class="text-center text-larger"><span class="help"><?php echo $text_anonpost_intro; ?></span></p>

                            <?php if ($success) { ?>
                            <div class="success">
                                <p><?php echo $success; ?></p>
                                <span class="close"><i class="fa fa-times"></i></span>
                                <span class="icon"><i class="fa fa-check-circle"></i></span>
                            </div>
                            <?php } ?>

                            <?php if ($warning) { ?>
                            <div class="warning">
                                <p><?php echo $warning; ?></p>
                                <span class="close"><i class="fa fa-times"></i></span>
                                <span class="icon"><i class="fa fa-exclamation-triangle"></i></span>
                            </div>
                            <?php } ?>

                            <div class="formbox">
                                <p class="form-label">
                                    <strong><?php echo $entry_link; ?></strong>
                                    <i class="fa fa-question-circle float-right grey-text" title="<?php echo $entry_link . $help_required; ?>" data-content="<?php echo $help_link; ?>" data-placement="left" rel="popover" data-container="body" data-trigger="hover"></i>
                                </p>
                                <input type="text" name="link" value="<?php echo isset($link) ? $link : ''; ?>" placeholder="http://" required="required" />
                                <?php if ($error_link) { ?><span class="error"><i class="fa fa-exclamation-triangle"></i><?php echo $error_link; ?></span><?php } ?>
                            </div>

							<?php if ($this->config->get('member_tab_links')) { ?>
							<?php if ($this->config->get('member_data_field_category')) { ?>

                            <div class="formbox<?php echo (!empty($category_name)) ? ' success' : ''; ?>">
                                <p class="form-label">
                                    <strong><?php echo $entry_category; ?></strong>
                                    <i class="fa fa-question-circle float-right grey-text" title="<?php echo $help_category_alt . $help_required; ?>" data-placement="left" data-container="body" rel="tooltip"></i>
                                </p>
    							<input type="text" name="category_name" value="<?php echo (!empty($category_name) ? $category_name : ''); ?>" placeholder="<?php echo $entry_category; ?>" required="required" />
                                <div class="hidden">
                                    <p class="or">--- OR ---</p>
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
                                </div>
								<?php if ($error_category) { ?><span class="error"><i class="fa fa-exclamation-triangle"></i><?php echo $error_category; ?></span><?php } ?>
							</div>

							<div class="formbox sub-category-wrapper hidden"<?php if (!$sub_categories) { ?> style="display:none;"<?php } ?>>
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

							<div class="formbox third-category-wrapper hidden"<?php if (!$third_categories) { ?> style="display:none;"<?php } ?>>
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
                                    <strong><?php echo $entry_size; ?></strong>
                                    <i class="fa fa-question-circle float-right grey-text" title="<?php echo $help_size . $help_required; ?>" data-placement="left" data-container="body" rel="tooltip"></i>
                                </p>
								<input type="text" name="size" value="<?php echo $size; ?>" placeholder="<?php echo $entry_size; ?>" required="required" />
								<?php if ($error_size) { ?><span class="error"><i class="fa fa-exclamation-triangle"></i><?php echo $error_size; ?></span><?php } ?>
							</div>

                            <div class="formbox<?php if (!$error_image && !$error_image_url && !$error_image_file) { ?> hidden<?php } ?>">
                                <p class="form-label">
                                    <strong><?php echo $entry_image; ?></strong>
                                    <i class="fa fa-question-circle float-right grey-text" title="<?php echo $entry_image . $help_required; ?>" data-content="<?php echo $help_image; ?>" data-placement="left" rel="popover" data-container="body" data-trigger="hover"></i>
                                </p>
                                <div class="image image-border">
                                    <img src="<?php echo $thumb; ?>" alt="" id="thumb" class="thumb" style="max-width:205px;" /><br />
                                    <input type="hidden" name="image" value="<?php echo $image; ?>" id="image" />
                                    <a class="button button-upload" id="button-upload" title="<?php echo $button_upload; ?>" rel="tooltip" data-container="body"><i class="fa fa-upload"></i><?php echo $button_upload; ?></a>
                                    <a class="button button_clear button_alt" data-target="" title="<?php echo $button_clear; ?>" rel="tooltip" data-container="body"><i class="fa fa-times"></i><?php echo $button_clear; ?></a>
                                    <div class="progress-bar"></div>
                                </div>
                                <?php if ($error_image) { ?><span class="error"><i class="fa fa-exclamation-triangle"></i><?php echo $error_image; ?></span><?php } ?>

                                <?php if ($admin) { ?>
                                <p class="or">--- OR ---</p>
                                <p class="form-label">
                                    <strong><?php echo $entry_image_url; ?></strong>
                                    <i class="fa fa-question-circle float-right grey-text" title="<?php echo $entry_image_url . $help_optional; ?>" data-placement="left" data-container="body" rel="tooltip"></i>
                                </p>
                                <input type="text" name="image_url" value="<?php echo isset($image_url) ? $image_url : ''; ?>" placeholder="http://" />
                                <p class="or">--- OR ---</p>
                                <div class="image image-border">
                                    <input type="file" name="image_file" id="image-file" accept="image/png,image/jpeg" />
                                    <?php if ($error_image_file) { ?><span class="error"><i class="fa fa-exclamation-triangle"></i><?php echo $error_image_file; ?></span><?php } ?>
                                </div>
                                <?php } ?>

                                <?php if ($error_image_url) { ?><span class="error"><i class="fa fa-exclamation-triangle"></i><?php echo $error_image_url; ?></span><?php } ?>
                            </div>

                            <div class="formbox">
                                <span class="label">
                                    <a id="button-more-options" class="button-more-options"><i class="fa fa-plus-circle"></i><?php echo $text_more_options; ?></a>
                                </span>
                            </div>

                            <div class="grid-1" id="container-more-options" data-show="<?php echo $display_more_options ? 'true' : 'false'; ?>">
    							<div class="formbox">
    							  <p class="form-label">
                                      <strong><?php echo $entry_year; ?></strong>
                                      <i class="fa fa-question-circle float-right grey-text" title="<?php echo $help_year . $help_optional; ?>" data-placement="left" data-container="body" rel="tooltip"></i>
                                  </p>
    							  <input type="text" name="year" value="<?php echo $year; ?>" placeholder="<?php echo $entry_year; ?>" />
    							  <?php if ($error_year) { ?><span class="error"><i class="fa fa-exclamation-triangle"></i><?php echo $error_year; ?></span><?php } ?>
    							</div>

                                <div class="formbox">
    								<p class="form-label">
                                        <strong><?php echo $entry_condition; ?></strong>
                                        <i class="fa fa-question-circle float-right grey-text" title="<?php echo $help_condition . $help_optional; ?>" data-placement="left" data-container="body" rel="tooltip"></i>
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
    							</div>

                                <div class="formbox formbox-price">
                                  <p class="form-label">
                                      <strong><?php echo $entry_value; ?></strong>
                                      <i class="fa fa-question-circle float-right grey-text" title="<?php echo $help_value; ?>" data-content="<?php echo $help_value_more; ?>" data-placement="left" data-container="body" rel="popover" data-trigger="hover"></i>
                                  </p>
                                  <input type="text" name="price" value="<?php echo $price; ?>" placeholder="<?php if ($currency['symbol_left']) { ?><?php echo $currency['symbol_left']; ?>&nbsp;<?php } ?><?php echo $entry_value . ' (' . $currency['code'] . ')'; ?><?php if ($currency['symbol_right']) { ?>&nbsp;<?php echo $currency['symbol_right']; ?><?php } ?>" size="6" />
                                  <?php if ($error_price) { ?><span class="error"><i class="fa fa-exclamation-triangle"></i><?php echo $error_price; ?></span><?php } ?>
                                </div>

        						<div id="languages" class="htabs hidden">
        							<?php foreach ($languages as $language) { ?>
        							<a href="#language<?php echo $language['language_id']; ?>"><img src="image/flags/<?php echo $language['image']; ?>" title="<?php echo $language['name']; ?>" /> <?php echo $language['name']; ?></a>
        							<?php } ?>
        						</div>

        						<?php foreach ($languages as $language) { ?>
        						<div id="language<?php echo $language['language_id']; ?>">
                                    <?php if ($admin) { ?>
        							<div class="formbox">
        								<p class="form-label">
                                            <strong><?php echo $entry_name; ?></strong>
                                            <i class="fa fa-question-circle float-right grey-text" title="<?php echo $entry_name . $help_optional; ?>" data-content="<?php echo $help_name; ?>" data-placement="left" data-container="body" rel="popover" data-trigger="hover"></i>
                                        </p>
        								<input type="text" name="product_description[<?php echo $language['language_id']; ?>][name]" value="<?php echo isset($product_description[$language['language_id']]['name']) ? $product_description[$language['language_id']]['name'] : ''; ?>" placeholder="<?php echo $entry_name; ?>" />
                                        <?php if (isset($error_name[$language['language_id']])) { ?><span class="error"><i class="fa fa-exclamation-triangle"></i><?php echo $error_name[$language['language_id']]; ?></span><?php } ?>
        							</div>

        							<div class="formbox">
        								<p class="form-label">
                                            <strong><?php echo $entry_description; ?></strong>
                                            <i class="fa fa-question-circle float-right grey-text" title="<?php echo $entry_description . $help_optional; ?>" data-content="<?php echo $help_description; ?>" data-placement="left" data-container="body" rel="popover" data-trigger="hover"></i>
                                        </p>
                                        <textarea name="product_description[<?php echo $language['language_id']; ?>][description]" cols="68" rows="6" id="description<?php echo $language['language_id']; ?>"><?php echo isset($product_description[$language['language_id']]['description']) ? $product_description[$language['language_id']]['description'] : ''; ?></textarea>
                                        <?php if (isset($error_description[$language['language_id']])) { ?><span class="error"><i class="fa fa-exclamation-triangle"></i><?php echo $error_description[$language['language_id']]; ?></span><?php } ?>
        							</div>
                                    <?php } ?>
        							<div class="formbox"<?php echo (!$this->config->get('member_data_field_tags') ? ' style="display:none;"' : '') ?>>
        								<p class="form-label">
                                            <strong><?php echo $entry_tag; ?></strong>
                                            <i class="fa fa-question-circle float-right grey-text" title="<?php echo $entry_tag . $help_optional; ?>" data-content="<?php echo $help_tag; ?>" data-placement="left" data-container="body" rel="popover" data-trigger="hover"></i>
                                        </p>
        								<input type="text" name="product_description[<?php echo $language['language_id']; ?>][tag]" value="<?php echo isset($product_description[$language['language_id']]['tag']) ? $product_description[$language['language_id']]['tag'] : ''; ?>" placeholder="<?php echo $entry_tag; ?>" />
                                        <?php if (isset($error_tag[$language['language_id']])) { ?><span class="error"><i class="fa fa-exclamation-triangle"></i><?php echo $error_tag[$language['language_id']]; ?></span><?php } ?>
        							</div>
        						</div>
        						<?php } ?>
                            </div><!-- #container-more-options-->

                            <?php if ($admin) { ?>
                            <div class="clearafter">
                                <div class="grid-2">
                                    <div class="formbox">
                                        <p class="form-label">
                                            <strong><?php echo $entry_approved; ?></strong>
                                        </p>
                                        <select name="approved">
                      					  <?php if ($approved) { ?>
                      					  <option value="1" selected="selected"><?php echo $text_yes; ?></option>
                      					  <option value="0"><?php echo $text_no; ?></option>
                      					  <?php } else { ?>
                      					  <option value="1"><?php echo $text_yes; ?></option>
                      					  <option value="0" selected="selected"><?php echo $text_no; ?></option>
                      					  <?php } ?>
                      					</select>
                                    </div>
                                </div>
                                <div class="grid-2">
                                    <div class="formbox">
                                        <p class="form-label">
                                            <strong><?php echo $entry_status; ?></strong>
                                        </p>
                                        <select name="status">
                      					  <?php if ($status) { ?>
                      					  <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                      					  <option value="0"><?php echo $text_disabled; ?></option>
                      					  <?php } else { ?>
                      					  <option value="1"><?php echo $text_enabled; ?></option>
                      					  <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                      					  <?php } ?>
                      					</select>
                                    </div>
                                </div>
                            </div>
                            <?php } ?>

                            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>" />
					        <input id="anonpost-submit" type="submit" value="<?php echo $button_post; ?>" class="button hidden" />

    					</div>

    					<div class="buttons">
                            <p class="text-center text-larger">
                                <?php if (!$logged) { ?>
                                <span class="help"><?php echo $text_anonpost_footer; ?></span><br />
                                <?php } ?>
                                <span class="help"><?php echo $text_all_fields_required; ?></span>
                            </p>
                            <?php if ($captcha_enabled) { ?>
                            <div class="center">
                                <p class="hidden">
                                    <label><?php echo $entry_captcha; ?><i class="fa fa-question-circle float-right grey-text" title="<?php echo $entry_captcha . $help_required; ?>" data-placement="left" data-container="body" rel="tooltip"></i></label>
                                </p>
                                <div id="anonpost-g-recaptcha" class="recaptcha-box"></div>
                                <p>
                                    <?php if ($error_captcha) { ?><span class="error"><i class="fa fa-exclamation-triangle"></i><?php echo $error_captcha; ?></span><br /><?php } ?>
                                    <span class="help"><i class="fa fa-info-circle"></i><?php echo $help_unauthorized; ?></span>
                                </p>
                            </div>
                            <?php } ?>
    						<div class="center">
                                <label for="anonpost-submit" class="button button-submit button_highlight fullwidth bigger"><i class="fa fa-save"></i> <?php echo $button_post; ?></label>
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
            		<input type="hidden" name="text_hide_options" value="<?php echo $text_hide_options; ?>" />
            		<input type="hidden" name="text_more_options" value="<?php echo $text_more_options; ?>" />
                    <input type="hidden" name="manufacturers_all" value="<?php echo htmlspecialchars(json_encode($manufacturers), ENT_COMPAT); ?>" />
                    <input type="hidden" name="categories_complete" value="<?php echo $categories_complete; ?>" />

                </div>
                <?php echo $content_bottom; ?>
            </div>
        </div>

    </div>
</div>
<script type="text/javascript"><!--
var productNoImage = '<?php echo $no_image; ?>';
var imageWidth = <?php echo $this->config->get('config_image_product_width'); ?>;
var imageHeight = <?php echo $this->config->get('config_image_product_height'); ?>;
var textWait = '<?php echo $text_wait; ?>';
var textYes = '<?php echo $text_yes; ?>';
var textNo = '<?php echo $text_no; ?>';
var textNone = '<?php echo $text_none; ?>';
var buttonClear = '<?php echo $button_clear; ?>';
var buttonRemove = '<?php echo $button_remove; ?>';
var buttonUpload = '<?php echo $button_upload; ?>';
var entryRequired = '<?php echo $entry_required; ?>';
var csrfToken = '<?php echo $csrf_token; ?>';
var languages = [];
<?php foreach ($languages as $language) { ?>
languages.push({
    id: <?php echo $language['language_id']; ?>,
    image: '<?php echo $language['image']; ?>',
    name: '<?php echo $language['name']; ?>'
});
<?php } ?>
//--></script>
<?php echo $footer; ?>
