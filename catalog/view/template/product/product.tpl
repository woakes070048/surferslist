<?php echo $header; ?>
<main class="container-page product-page">
    <header class="breadcrumb">
        <div class="layout">
            <h1>
                <a href="<?php echo $page_canonical; ?>">
                    <?php if ($category_icon) { ?><span class="category-icon icon-<?php echo $category_icon; ?>"></span><?php } ?><?php echo $heading_title; ?>
                </a>
            </h1>
            <div class="links">
                <?php foreach ($breadcrumbs as $breadcrumb) { ?>
                <?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
                <?php } ?>
            </div>
        </div>
    </header>
    <div class="layout">
        <section class="container-center">
            <div class="container-top">
                <?php echo $content_top; ?>
                <div class="content-page">
                    <?php echo $notification; ?>
                    <div class="left-item">
    					<div class="prevnext full">
    						<ul class="pager column<?php echo $nav_cols; ?>">
    							<?php if ($prev_url) { ?>
    							<li class="prev"><a href="<?php echo $prev_url; ?>" rel="tooltip" title="<?php echo $prev_title;?>"><i class="fa fa-chevron-circle-left"></i><span class="name hidden"><?php echo $prev_title;?></span><span class="dir"><?php echo $text_prev; ?></span></a></li>
    							<?php } ?>
    							<?php if ($back_url) { ?>
    							<li class="back"><a href="<?php echo $back_url; ?>" rel="tooltip" title="<?php echo $button_back;?>"><i class="fa fa-undo"></i><span class="dir"><?php echo $button_back; ?></span></a></li>
                                <?php } else { ?>
    							<li class="more"><a href="<?php echo $more_url; ?>" rel="tooltip" title="<?php echo $more_title;?>"><i class="fa fa-th"></i><span class="name hidden"><?php echo $more_title;?></span><span class="dir"><?php echo $text_more;?></span></a></li>
                                <?php } ?>
    							<?php if ($next_url) { ?>
    							<li class="next"><a href="<?php echo $next_url; ?>" rel="tooltip" title="<?php echo $next_title;?>"><i class="fa fa-chevron-circle-right"></i><span class="name hidden"><?php echo $next_title;?></span><span class="dir"><?php echo $text_next; ?></span></a></li>
    							<?php } ?>
    						</ul>
    					</div>
    					<?php if ($image_small || $images) { ?>
    					<?php if ($image_small) { ?>
    					<div class="thumb">
    						<?php if ($price && $type_id >= 0 && $special) { ?>
                            <span class="sale-badges">-<?php echo $salebadges; ?>&#37;</span>
                            <?php } ?>
    						<?php if ($featured) { ?>
                            <i class="icon icon-tag promo text-larger"></i>
                            <?php } ?>
    						<a href="<?php echo $image_large; ?>" class="zoom" id="image">
                                <img src="<?php echo $image_small; ?>" alt="<?php echo $heading_title; ?>" id="image-small" />
                            </a>
    						<a href="<?php echo $image_large; ?>" class="lightbox hidden-medium"<?php if ($images) { ?> rel="listing-images"<?php } ?> title="<?php echo $heading_title; ?>">
                                <span><i class="fa fa-search-plus"></i></span>
                            </a>
    					</div>
    					<?php } ?>
    					<?php if ($images) { ?>
    					<div class="images">
    						<?php if ($image_small) { ?>
    							<a href="<?php echo $image_large; ?>" class="zoom-gallery lightbox" rel="listing-images" data-small-image="<?php echo $image_small; ?>">
                                    <img src="<?php echo $image_thumb; ?>" alt="<?php echo $heading_title; ?>" />
                                </a>
    						<?php } ?>
    						<?php foreach ($images as $image) { ?>
                                <?php if ($image['small'] != $image_small) { ?>
    							<a href="<?php echo $image['large']; ?>" class="zoom-gallery lightbox" rel="listing-images" data-small-image="<?php echo $image['small']; ?>">
                                    <img src="<?php echo $image['thumb']; ?>" alt="<?php echo $heading_title; ?>" />
                                </a>
                                <?php } ?>
    						<?php } ?>
    					</div>
    					<?php } ?>
    					<?php } ?>
                        <div class="share">
                            <div class="sharethis-inline-share-buttons"></div>
                        </div>
                    </div>
                    <div class="right-item">
                        <div class="widget">
                            <div id="tabs" class="widget-top-nav">
                                <a href="#tab-description"><i class="fa fa-info-circle"></i><strong><?php echo $tab_description; ?></strong></a>
                                <?php if ($attribute_groups) { ?>
                                <a href="#tab-attribute"><i class="fa fa-reorder"></i><strong><?php echo $tab_attribute; ?></strong></a>
                                <?php } ?>
                                <?php if ($question_status) { ?>
                                <a href="#tab-question"><i class="fa fa-comment"></i><strong><?php echo $tab_question; ?></strong></a>
                            <?php } ?>
                                <?php if ($learn_more) { ?>
                                <a href="#tab-learn-more"><i class="fa fa-question-circle"></i><strong><?php echo $tab_learn_more; ?></strong></a>
                                <?php } ?>
                            </div>
                            <div id="tab-description">
                                <div class="actions">
                                    <div class="addto clearafter">
                                        <div class="float-left">
                                            <a id="shortlink" class="links button_shortlink" title="<?php echo $text_shortlink_help; ?>" data-content="<?php echo $page_shortlink; ?>" data-placement="bottom" rel="popover" data-trigger="click"><i class="fa fa-link"></i><?php echo $text_shortlink; ?></a>
                                            <a onclick="addToWishList('<?php echo $product_id; ?>');" class="links button_wishlist" title="<?php echo $button_wishlist; ?>" rel="tooltip" data-container="body"><i class="fa fa-save"></i><?php echo $button_wishlist; ?></a>
                        					<?php if ($compare) { ?>
                                            <a onclick="removeFromCompare('<?php echo $product_id; ?>');" class="links button_compare" title="<?php echo $button_compare; ?>" rel="tooltip" data-container="body"><i class="fa fa-copy"></i><?php echo $button_compare; ?></a>
                        					<?php } else { ?>
                                            <a onclick="addToCompare('<?php echo $product_id; ?>');" class="links button_compare" title="<?php echo $button_compare; ?>" rel="tooltip" data-container="body"><i class="fa fa-copy"></i><?php echo $button_compare; ?></a>
                                            <?php } ?>
                                        </div>
                                        <?php if ($member_customer_id) { ?>
                                        <div class="float-right">
                                            <a id="flaglisting" onclick="flagListing('<?php echo $product_id; ?>','<?php echo $text_flag_confirm; ?>')" class="links button_flag" title="<?php echo $text_flag_help; ?>" rel="tooltip" data-container="body"><i class="fa fa-flag"></i><?php echo $text_flag; ?></a>
                                        </div>
                                        <?php } ?>
                                    </div>
                                </div>
                                <div class="content clearafter">
                                    <?php if ($price && $type_id >= 0) { ?>
                                    <div class="price">
                                    	<div class="all-price">
                                            <?php echo $text_price; ?>
                                            <?php if (!$special) { ?>
                                            <?php echo $price; ?>
                                            <?php } else { ?>
                                            <span class="price-old"><span class="span1">&nbsp;</span><span class="span2">&nbsp;</span> <?php echo $price; ?></span>
                                            <?php echo $special; ?>
                                            <?php } ?>
                                        </div>
                                        <span class="others"><i><?php echo $type; ?></i></span>
                                        <?php if ($tax) { ?>
                                        <span class="others"><?php echo $text_tax; ?> <?php echo $tax; ?></span>
                                        <?php } ?>
                                        <?php if ($points) { ?>
                                        <span class="others"><?php echo $text_points; ?> <?php echo $points; ?></span>
                                        <?php } ?>
                                        <?php if ($discounts) { ?>
                                        <span class="others">
                                        <?php foreach ($discounts as $discount) { ?>
                                        <?php echo sprintf($text_discount, $discount['quantity'], $discount['price']); ?><br />
                                        <?php } ?>
                                        </span>
                                        <?php } ?>
                                    </div>
                                    <?php } ?>
                                    <div class="grid-1">
    									<ul class="global-attribute">
    										<?php if ($manufacturer) { ?>
    										<?php if ($manufacturer_image) { ?>
    										<li class="manufacturer-logo"><b><?php echo $text_manufacturer; ?></b> <i><a href="<?php echo $manufacturer_href; ?>"><img src="<?php echo $manufacturer_image; ?>" alt="<?php echo $manufacturer; ?>" rel="tooltip" data-placement="top" data-original-title="<?php echo $manufacturer; ?>" /></a></i></li>
    										<?php } else { ?>
    										<li><b><?php echo $text_manufacturer; ?></b> <i><a href="<?php echo $manufacturer_href; ?>"><?php echo $manufacturer; ?></a></i></li>
    										<?php } ?>
    										<?php } ?>
    										<li><b><?php echo $text_model; ?></b> <i><?php echo $model; ?></i></li>
    										<li><b><?php echo $text_size; ?></b> <i><?php echo $size; ?></i></li>
                                            <?php if ($year) { ?>
    										<li><b><?php echo $text_year; ?></b> <i><?php echo $year; ?></i></li>
                                            <?php } ?>
    										<?php if ($condition) { ?>
    										<li><b><?php echo $text_condition; ?></b> <i><?php echo $condition; ?></i></li>
    										<?php } ?>
                                            <?php if ($price != $text_free && $type_id < 0) { ?>
    										<li><b><?php echo $text_value; ?></b> <i><?php echo $price; ?></i></li>
    										<?php } ?>
    										<?php if ($categories) { ?>
    										<li><b><?php echo $text_category; ?></b><i>
    											<?php for ($i = 0; $i < count($categories); $i++) { ?>
    												<?php if ($i < (count($categories) - 1)) { ?>
    												<a href="<?php echo $categories[$i]['href']; ?>"><?php echo $categories[$i]['name']; ?></a>,
    												<?php } else { ?>
    												<a href="<?php echo $categories[$i]['href']; ?>"><?php echo $categories[$i]['name']; ?></a>
    												<?php } ?>
    											<?php } ?></i></li>
    										<?php } ?>
                                            <?php if (($this->config->get('member_status')) && $member) { ?>
                                            <li><b><?php echo $text_member; ?></b>
                                                <i><a href="<?php echo $member['href']; ?>">
                                                    <?php if ($member['image']) { ?><img src="<?php echo $member['image']; ?>" title="<?php echo $member['name']; ?>" alt="<?php echo $member['name']; ?>" /><?php } ?>
                                                    <?php echo $member['name']; ?>
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
                                                </a>
                                            </i></li>
                    						<?php } ?>
                                            <?php if ($location || ($location_zone && $location_country)) { ?>
                                            <li><b><?php echo $text_location; ?></b> <i><?php echo $location ? $location . ' - ' : ''; ?><a href="<?php echo $location_href; ?>"><?php echo $location_zone . ', ' . $location_country; ?></a></i></li>
                                            <?php } ?>
                                            <?php if ($shipping_display && $price && $type_id >= 0) { ?>
    										<li><b><?php echo $text_shipping; ?></b> <i><?php echo $shipping; ?></i></li>
    										<?php } ?>
    										<?php if ($shipping_display_dimensions) { ?>
    										<li><b><?php echo $text_dimensions; ?></b> <i><?php echo $length; ?> x <?php echo $width; ?> x <?php echo $height; ?></i></li>
    										<li><b><?php echo $text_weight; ?></b> <i><?php echo $weight; ?></i></li>
    										<?php } ?>
                                            <?php if ($learn_more) { ?>
                                            <li><b><?php echo $text_learn_more; ?></b> <i><a href="<?php echo $learn_more; ?>" target="_blank"><?php echo $learn_more; ?></a></i></li>
                                            <?php } ?>
    									</ul>
                                	</div>
                                </div>
                                <?php if ($description) { ?>
                                <div class="item-description">
                                    <p><?php echo $description; ?></p>
                                </div>
                                <?php } ?>
                                <?php if ($tags) { ?>
                                <div class="tags"><?php foreach ($tags as $tag) { ?><a href="<?php echo $tag['href']; ?>">#<?php echo $tag['tag']; ?></a><?php } ?></div>
                                <?php } ?>
                                <?php if ($options) { ?>
                                <h6 id="text-options" class="text-options-before"><?php echo $text_option; ?></h6>
                                <div class="options">
                                    <?php foreach ($options as $option) { ?>
                                        <?php if ($option['type'] == 'select') { ?>
                                        <div class="formbox">
                                            <p><?php if ($option['required']) { ?><span class="required">*</span> <?php } ?><strong><?php echo $option['name']; ?>:</strong></p>
                                            <select name="option[<?php echo $option['product_option_id']; ?>]">
                                                <option value=""><?php echo $text_select; ?></option>
                                                <?php foreach ($option['option_value'] as $option_value) { ?>
                                                <option value="<?php echo $option_value['product_option_value_id']; ?>"><?php echo $option_value['name']; ?>
                                                <?php if ($option_value['price']) { ?>
                                                (<?php echo $option_value['price_prefix']; ?><?php echo $option_value['price']; ?>)
                                                <?php } ?>
                                                </option>
                                                <?php } ?>
                                            </select>
                                            <span id="option-<?php echo $option['product_option_id']; ?>"></span>
                                        </div>
                                        <?php } ?>
                                        <?php if ($option['type'] == 'radio') { ?>
                                        <div class="formbox">
                                            <p><?php if ($option['required']) { ?><span class="required">*</span> <?php } ?><strong><?php echo $option['name']; ?>:</strong></p>
                                            <?php foreach ($option['option_value'] as $option_value) { ?>
                                            <span class="label"><input type="radio" name="option[<?php echo $option['product_option_id']; ?>]" value="<?php echo $option_value['product_option_value_id']; ?>" id="option-value-<?php echo $option_value['product_option_value_id']; ?>" /><label for="option-value-<?php echo $option_value['product_option_value_id']; ?>"><?php echo $option_value['name']; ?> <?php if ($option_value['price']) { ?>(<?php echo $option_value['price_prefix']; ?><?php echo $option_value['price']; ?>)<?php } ?></label></span>
                                            <?php } ?>
                                            <span id="option-<?php echo $option['product_option_id']; ?>"></span>
                                        </div>
                                        <?php } ?>
                                        <?php if ($option['type'] == 'checkbox') { ?>
                                        <div class="formbox">
                                            <p><?php if ($option['required']) { ?><span class="required">*</span> <?php } ?><strong><?php echo $option['name']; ?>:</strong></p>
                                            <?php foreach ($option['option_value'] as $option_value) { ?><span class="label"><input type="checkbox" name="option[<?php echo $option['product_option_id']; ?>][]" value="<?php echo $option_value['product_option_value_id']; ?>" id="option-value-<?php echo $option_value['product_option_value_id']; ?>" /><label for="option-value-<?php echo $option_value['product_option_value_id']; ?>"><?php echo $option_value['name']; ?> <?php if ($option_value['price']) { ?>(<?php echo $option_value['price_prefix']; ?><?php echo $option_value['price']; ?>)<?php } ?></label></span><?php } ?>
                                            <span id="option-<?php echo $option['product_option_id']; ?>"></span>
                                        </div>
                                        <?php } ?>
                                        <?php if ($option['type'] == 'image') { ?>
                                        <div class="formbox">
                                            <p><?php if ($option['required']) { ?><span class="required">*</span> <?php } ?><strong><?php echo $option['name']; ?>:</strong></p>
                                            <table class="option-image">
                                                <?php foreach ($option['option_value'] as $option_value) { ?>
                                                <tr>
                                                <td style="width: 1px;"><input type="radio" name="option[<?php echo $option['product_option_id']; ?>]" value="<?php echo $option_value['product_option_value_id']; ?>" id="option-value-<?php echo $option_value['product_option_value_id']; ?>" /></td>
                                                <td style="width: 1px;"><label for="option-value-<?php echo $option_value['product_option_value_id']; ?>"><img src="<?php echo $option_value['image']; ?>" alt="<?php echo $option_value['name'] . ($option_value['price'] ? ' ' . $option_value['price_prefix'] . $option_value['price'] : ''); ?>" /></label></td>
                                                <td><label for="option-value-<?php echo $option_value['product_option_value_id']; ?>"><?php echo $option_value['name']; ?>
                                                <?php if ($option_value['price']) { ?>
                                                (<?php echo $option_value['price_prefix']; ?><?php echo $option_value['price']; ?>)
                                                <?php } ?>
                                                </label></td>
                                                </tr>
                                                <?php } ?>
                                            </table>
                                            <span id="option-<?php echo $option['product_option_id']; ?>"></span>
                                        </div>
                                        <?php } ?>
                                        <?php if ($option['type'] == 'text') { ?>
                                        <div class="formbox">
                                            <p><?php if ($option['required']) { ?><span class="required">*</span> <?php } ?><strong><?php echo $option['name']; ?>:</strong></p>
                                            <input type="text" name="option[<?php echo $option['product_option_id']; ?>]" value="<?php echo $option['option_value']; ?>" />
                                            <span id="option-<?php echo $option['product_option_id']; ?>"></span>
                                        </div>
                                        <?php } ?>
                                        <?php if ($option['type'] == 'textarea') { ?>
                                        <div class="formbox">
                                            <p><?php if ($option['required']) { ?><span class="required">*</span> <?php } ?><strong><?php echo $option['name']; ?>:</strong></p>
                                            <textarea name="option[<?php echo $option['product_option_id']; ?>]"><?php echo $option['option_value']; ?></textarea>
                                            <span id="option-<?php echo $option['product_option_id']; ?>"></span>
                                        </div>
                                        <?php } ?>
                                        <?php if ($option['type'] == 'file') { ?>
                                        <div class="formbox">
                                            <p><?php if ($option['required']) { ?><span class="required">*</span> <?php } ?><strong><?php echo $option['name']; ?>:</strong></p>
                                            <input type="button" value="<?php echo $button_upload; ?>" id="button-option-<?php echo $option['product_option_id']; ?>" class="button">
                                            <input type="hidden" name="option[<?php echo $option['product_option_id']; ?>]" value="" />
                                            <span id="option-<?php echo $option['product_option_id']; ?>"></span>
                                        </div>
                                        <?php } ?>
                                        <?php if ($option['type'] == 'date') { ?>
                                        <div class="formbox">
                                            <p><?php if ($option['required']) { ?><span class="required">*</span> <?php } ?><strong><?php echo $option['name']; ?>:</strong></p>
                                            <input type="text" name="option[<?php echo $option['product_option_id']; ?>]" value="<?php echo $option['option_value']; ?>" class="date" />
                                            <span id="option-<?php echo $option['product_option_id']; ?>"></span>
                                        </div>
                                        <?php } ?>
                                        <?php if ($option['type'] == 'datetime') { ?>
                                        <div class="formbox">
                                            <p><?php if ($option['required']) { ?><span class="required">*</span> <?php } ?><strong><?php echo $option['name']; ?>:</strong></p>
                                            <input type="text" name="option[<?php echo $option['product_option_id']; ?>]" value="<?php echo $option['option_value']; ?>" class="datetime" />
                                            <span id="option-<?php echo $option['product_option_id']; ?>"></span>
                                        </div>
                                        <?php } ?>
                                        <?php if ($option['type'] == 'time') { ?>
                                        <div class="formbox">
                                            <p><?php if ($option['required']) { ?><span class="required">*</span> <?php } ?><strong><?php echo $option['name']; ?>:</strong></p>
                                            <input type="text" name="option[<?php echo $option['product_option_id']; ?>]" value="<?php echo $option['option_value']; ?>" class="time" />
                                            <span id="option-<?php echo $option['product_option_id']; ?>"></span>
                                        </div>
                                        <?php } ?>
                                    <?php } ?>
                                </div>
                                <?php } ?>
                                <div id="buttons-item" class="buttons">
                                    <div class="center">
                                        <?php if ($member_customer_id) { ?>
                                        <a href="contact?contact_id=<?php echo $member_customer_id; ?>&listing_id=<?php echo $product_id; ?>" class="button button_primary bigger"><i class="fa fa-envelope"></i><?php echo $button_contact; ?></a>
                                        <?php } ?>
                                        <?php if ($question_status) { ?>
                                        <a id="scroll-to-discussion" class="button button_secondary bigger"><i class="fa fa-comment"></i><?php echo $text_discuss; ?></a>
                                        <?php } ?>
                                        <?php if ($type_id == 1) { ?>
                                        <?php if ($quantity > 1 && $member_customer_id) { ?>
                                        <br class="show-xlarge" />
                                        <?php } ?>
                                        <a id="button-cart" class="button button_highlight bigger"><i class="fa fa-shopping-cart"></i><?php echo $button_cart; ?></a>
    									<?php if ($quantity > 1) { ?>
                                    	<div class="quantity-box">
                                            <a class="quantityplus" onclick="quantityMore();"><i class="fa fa-plus"></i></a>
                                            <a class="quantityminus" onclick="quantityLess();"><i class="fa fa-minus"></i></a>
                                            <input type="text" class="quantity" id="quantity" name="quantity" value="<?php echo $minimum; ?>" />
                                        </div>
                                        <?php } ?>
                                        <input type="hidden" name="product_id" value="<?php echo $product_id; ?>" />
                                        <?php if ($minimum > 1) { ?>
                                        <div class="top"><span class="info"><i class="fa fa-info-circle"></i><?php echo $text_minimum; ?></span></div>
                                        <?php } ?>
                                        <?php } ?>
                                        <?php if ($learn_more) { ?>
                                        <a href="<?php echo $learn_more; ?>" target="_blank" class="button button_alt bigger" title="<?php echo $help_button_learn_more; ?>" rel="tooltip" data-container="body"><i class="fa fa-external-link-square"></i><?php echo $button_learn_more; ?></a>
                                        <?php } ?>
                                        <?php if ($footnote) { ?>
                                        <p class="footnote stock"><i><?php echo $footnote; ?></i></p>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                            <?php if ($attribute_groups) { ?>
                            <div id="tab-attribute">
                                <div class="content">
                                    <?php foreach ($attribute_groups as $attribute_group) { ?>
                                    <ul class="global-attribute">
                                        <li class="name"><strong><?php echo $attribute_group['name']; ?></strong></li>
                                        <?php foreach ($attribute_group['attribute'] as $attribute) { ?>
                                        <li><b><?php echo $attribute['name']; ?></b><i><?php echo $attribute['text']; ?></i></li>
                                        <?php } ?>
                                    </ul>
                                    <?php } ?>
                                </div>
                            </div>
                            <?php } ?>
                            <?php if ($question_status) { ?>
    						<div id="tab-question">
    							<div id="question"></div>

    							<div class="content">
    								<h3 id="question-title"><?php echo $text_discuss; ?></h3>
                                    <?php if ($help_discussion) { ?>
    								<p class="text-center"><span class="help"><i class="fa fa-info-circle"></i><?php echo $help_discussion; ?></span></p>
                                    <?php } ?>
                                    <?php if ($question_unauthorized) { ?>
    								<div class="formboxcolumn">
    									<div class="formbox">
    										<p class="form-label"><strong><?php echo $entry_name; ?></strong> <span class="required">*</span></p>
    										<input type="text" name="name" value="" placeholder="<?php echo $entry_name; ?>" required="required" />
    									</div>
    									<div class="formbox">
    										<p class="form-label"><strong><?php echo $entry_email; ?></strong> <span class="required">*</span></p>
    										<input type="text" name="email" value="" placeholder="<?php echo $entry_email; ?>" required="required" />
    									</div>
    								</div>
                                    <?php } ?>
    								<div class="formboxcolumn">
    									<div class="formbox" title="<?php echo $entry_question; ?>" rel="tooltip">
    										<p class="form-label"><strong><?php echo $entry_question; ?></strong> <span class="required">*</span></p>
    										<textarea name="text" cols="40" rows="8" style="width: 98%;" placeholder="<?php echo $entry_question; ?>"></textarea>
    										<span class="help"><i class="fa fa-code"></i><?php echo $text_note; ?></span>
    									</div>
    								</div>
                                    <?php if ($question_unauthorized) { ?>
                                    <div class="formboxcolumn">
                                        <div class="formbox">
                                            <p class="form-label"><strong><?php echo $entry_captcha; ?></strong></p>
                                            <div id="discussion-g-recaptcha" class="recaptcha-box"></div>
                                            <span class="help"><i class="fa fa-info-circle"></i><?php echo $help_discussion; ?></span>
                                        </div>
                                    </div>
                                    <?php } ?>
    							</div>
    							<div class="buttons">
    								<div class="left">
    									<a id="button-question" class="button"><i class="fa fa-pencil"></i><?php echo $button_submit; ?></a>
    								</div>
    							</div>
    						</div>
                            <?php } ?>
                            <?php if ($learn_more) { ?>
                            <div id="tab-learn-more">
    							<div class="content">
                                    <?php if (!$member_customer_id) { ?>
                                    <div class="information">
                                        <p><?php echo $footnote; ?></p>
                                        <span class="icon"><i class="fa fa-question-circle"></i></span>
                                    </div>
                                    <?php } ?>
    								<div class="text-empty">
                                        <p><a href="<?php echo $learn_more; ?>" target="_blank" class="button button_highlight bigger" title="<?php echo $help_button_learn_more; ?>" rel="tooltip" data-container="body"><i class="fa fa-external-link-square"></i><?php echo $button_learn_more; ?></a></p>
                                    </div>
                                    <div class="item-description">
                                        <h4 class="text-center"><span class="help"><a href="<?php echo $learn_more; ?>" target="_blank"><?php echo $learn_more; ?></a></span></h4>
                                    </div>
                                </div>
                            </div>
    					    <?php } ?>
                        </div>
                    </div>
                    <input type="hidden" name="listing_id" value="<?php echo $product_id; ?>" />
                </div>
            </div>
            <div class="container-bottom">
                <?php if ($products) { ?>
                <h2><?php echo $heading_related; ?></h2>
                <?php echo $column_left; ?>
                <?php echo $column_right; ?>
                <section class="container-center">
                    <div class="content-page">
                        <div id="related-listings" class="widget-module">
                            <?php include('products.inc.php'); ?>
                        </div>
                    </div>
                </section>
                <?php } ?>
                <?php echo $content_bottom; ?>
            </div>
        </section>
    </div>
</main>
<script type="text/javascript"><!--
var option_ids_file_type = <?php echo json_encode($option_ids_file_type); ?>;
var option_timepicker = <?php echo json_encode($option_timepicker); ?>;
var textWait = '<?php echo $text_wait; ?>';
var date_added = '<?php echo $date_added; ?>';
var date_modified = '<?php echo $date_modified; ?>';
//--></script>
<?php echo $footer; ?>
