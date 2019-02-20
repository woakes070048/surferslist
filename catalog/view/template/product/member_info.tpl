<?php echo $header; ?>
<main class="container-page member-page">
    <header class="breadcrumb">
        <div class="layout">
            <h1><a href="<?php echo $action; ?>"><i class="fa fa-user"></i><?php echo $heading_title; ?></a></h1>
            <div class="links">
                <?php foreach ($breadcrumbs as $breadcrumb) { ?>
                <?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
                <?php } ?>
            </div>
        </div>
    </header>
    <div class="layout">
		<?php if (!empty($member_name)) { ?>
        <section id="user-profile" class="container-top">
			<div class="content-page member-profile clearafter">
                <?php echo $notification; ?>
				<?php if ($member_banner) { ?>
				<div class="grid-1 profile-banner-section hidden-small">
					<div class="widget">
						<div class="content widget-no-heading">
							<div class="image image-border">
								<img src="<?php echo $member_banner; ?>" title="<?php echo $member_name; ?>" alt="<?php echo $member_name; ?>" />
							</div>
						</div>
					</div>
				</div>
				<?php } ?>
				<div class="grid-4 profile-image-section">
					<div class="widget">
                        <div class="content widget-no-heading member-image">
                            <div class="image image-no-border">
                                <?php if ($member_image) { ?>
                                <a class="lightbox" href="<?php echo $member_image; ?>"><img src="<?php echo $member_thumb; ?>" title="<?php echo $member_name; ?>" alt="<?php echo $member_name; ?>" /></a>
                                <?php } else { ?>
                                <img src="<?php echo $member_thumb; ?>" title="<?php echo $member_name; ?>" alt="<?php echo $member_name; ?>" />
                                <?php } ?>
                            </div>
                            <?php if ($review_status) { ?>
                            <div class="review">
                                <div class="rating">
                                    <div class="rating-stars view-star<?php echo $rating; ?>" title="<?php echo $help_member_rating; ?>" rel="tooltip" data-container="body">
                                        <i class="fa fa-star icon_star star-color color1"></i><i class="fa fa-star icon_star star-color color2"></i><i class="fa fa-star icon_star star-color color3"></i><i class="fa fa-star icon_star star-color color4"></i><i class="fa fa-star icon_star star-color color5"></i><i class="fa fa-star icon_star star-dark dark1"></i><i class="fa fa-star icon_star star-dark dark2"></i><i class="fa fa-star icon_star star-dark dark3"></i><i class="fa fa-star icon_star star-dark dark4"></i><i class="fa fa-star icon_star star-dark dark5"></i>
                                    </div>
                                </div>
                            </div>
                            <?php } ?>
                        </div>
                        <div class="share">
                            <div class="sharethis-inline-share-buttons"></div>
                        </div>
                    </div>
                </div>
                <div class="grid-8 profile-details-section">
                    <div class="widget">
                        <div id="tabs" class="widget-top-nav">
                            <a href="#tab-description"><i class="fa fa-info-circle"></i><strong><?php echo $tab_description; ?></strong></a>
                            <a href="#tab-discussion"><i class="fa fa-comment"></i><strong><?php echo $tab_discussion; ?></strong></a>
                            <?php if ($review_status) { ?><a href="#tab-review"><i class="fa fa-star"></i><strong><?php echo $tab_review; ?></strong></a><?php } ?>
                        </div>
						<div id="tab-description">
                            <div class="content tab-content">
                                <ul class="global-attribute">
                                    <?php if ($text_products) { ?>
                                    <li><b><?php echo $text_member_listings; ?></b><i><a href="#member-listings" id="member-listings-jump"><?php echo $text_products; ?></a></i></li>
                                    <?php } ?>
                                    <li class="hidden"><b><?php echo $text_member_type; ?></b><i><?php echo $member_type; ?></i></li>
                                    <li><b><?php echo $text_member_location; ?></b><i><?php echo $member_location; ?>, <a href="<?php echo $member_location_href; ?>"><?php echo $member_location_zone . ', ' . $member_location_country; ?></a></i></li>
                                    <?php if ($member_socials) { ?>
                                    <?php if ($text_member_custom_field_01 && !empty($member_custom_field_01)) { ?>
                                    <li><b><?php echo $text_member_custom_field_01; ?>:</b><i>
                                        <a href="<?php echo $member_custom_field_01; ?>" target="_blank"><?php echo $member_custom_field_01; ?></a>
                                    </i></li>
                                    <?php } ?>
                                    <li><b><?php echo $text_member_socials; ?></b><i>
                                        <div class="socials">
        								<?php if ($text_member_custom_field_02 && !empty($member_custom_field_02)) { ?><a href="<?php echo $member_custom_field_02; ?>" target="_blank"><i class="fa fa-twitter" title="<?php echo $text_member_custom_field_02; ?>" rel="tooltip"></i></a><?php } ?>
        								<?php if ($text_member_custom_field_03 && !empty($member_custom_field_03)) { ?><a href="<?php echo $member_custom_field_03; ?>" target="_blank"><i class="fa fa-facebook" title="<?php echo $text_member_custom_field_03; ?>" rel="tooltip"></i></a><?php } ?>
                                        <?php if ($text_member_custom_field_05 && !empty($member_custom_field_05)) { ?><a href="<?php echo $member_custom_field_05; ?>" target="_blank"><i class="fa fa-instagram" title="<?php echo $text_member_custom_field_05; ?>" rel="tooltip"></i></a><?php } ?>
        								<?php if ($text_member_custom_field_04 && !empty($member_custom_field_04)) { ?><a href="<?php echo $member_custom_field_04; ?>" target="_blank"><i class="fa fa-pinterest" title="<?php echo $text_member_custom_field_04; ?>" rel="tooltip"></i></a><?php } ?>
        								<?php if ($text_member_custom_field_06 && !empty($member_custom_field_06)) { ?><a href="<?php echo $member_custom_field_06; ?>" target="_blank"><i class="fa fa-link" title="<?php echo $text_member_custom_field_06; ?>" rel="tooltip"></i></a><?php } ?>
            							</div>
                                    </i></li>
                                    <?php } ?>
                                    <li class="hidden"><b><?php echo $text_member_date_added; ?></b><i><?php echo $member_date_added; ?></i></li>
                                </ul>
                                <div class="member-description">
                                    <p><?php echo $member_description; ?></p>
                                </div>
                                <?php if ($member_tags) { ?>
                                <div class="tags"><?php foreach ($member_tags as $member_tag) {?><a href="<?php echo $member_tag['href']; ?>">#<?php echo $member_tag['tag']; ?></a><?php } ?></div>
                                <?php } ?>
                            </div>
                            <div class="buttons">
                                <div class="left">
                                    <?php if ($customer_id) { ?>
                                    <a href="<?php echo $contact_member; ?>" class="button" title="<?php echo $button_contact . ' ' . $text_member_profile; ?>" rel="tooltip" data-container="body"><i class="fa fa-envelope"></i><?php echo $button_contact; ?></a>
                                    <?php } else { ?>
                                    <span class="terms">
                                        <a href="<?php echo $contact_member; ?>" id="claim-profile" title="<?php echo $text_claim; ?>" data-content="<?php echo $help_claim; ?>" data-placement="top" rel="popover" data-trigger="hover"><i class="fa fa-dot-circle-o"></i><?php echo $text_claim; ?></a>
                                    </span>
                                    <?php } ?>
                                </div>
                                <div class="right">
                                    <a id="scrolldiscuss" class="links button button_alt button_discuss" title="<?php echo $text_discuss . ' ' . $text_member_profile; ?>" rel="tooltip" data-container="body"><i class="fa fa-comment"></i><?php echo $text_discuss; ?></a>
                                    <a id="scrollreview" class="links button button_alt button_write" title="<?php echo $text_write . ' ' . $text_member_profile; ?>" rel="tooltip" data-container="body"><i class="fa fa-pencil"></i><?php echo $text_write; ?></a>
                                </div>
                            </div>
						</div>
                        <?php if ($discussion_status) { ?>
						<div id="tab-discussion">
							<div id="discussion"></div>

                            <div class="content">
                                <h3 id="discussion-title"><?php echo $text_discuss; ?></h3>
                                <?php if ($help_discussion) { ?>
                                <p class="text-center"><span class="help"><i class="fa fa-info-circle"></i><?php echo $help_discussion; ?></span></p>
                                <?php } ?>
                                <?php if ($discussion_unauthorized) { ?>
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
                                    <div class="formbox" title="<?php echo $entry_discussion; ?>" rel="tooltip">
                                        <p class="form-label"><strong><?php echo $entry_discussion; ?></strong> <span class="required">*</span></p>
                                        <textarea name="text" cols="40" rows="8" style="width: 98%;" placeholder="<?php echo $entry_discussion; ?>"></textarea>
                                        <span class="help"><i class="fa fa-code"></i><?php echo $text_note; ?></span>
                                    </div>
                                </div>
                                <?php if ($discussion_unauthorized) { ?>
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
                                    <a id="button-discussion" class="button"><i class="fa fa-pencil"></i><?php echo $button_submit_discussion; ?></a>
                                </div>
                            </div>
						</div>
						<?php } ?>
						<?php if ($review_status) { ?>
						<div id="tab-review">
							<div id="review"></div>
							<?php if (!$review_unauthorized) { ?>
							<div class="content">
								<h3 id="review-title" class="sub-section-title"><?php echo $text_write; ?></h3>
								<div class="formboxcolumn">
									<div class="formbox">
										<p class="form-label"><strong><?php echo $entry_rating; ?></strong> <span class="required">*</span></p>
										<div id="formboxrating">
											<div class="stars-content">
												<input type="radio" id="rate5" value="5" name="rating" class="hidden-star">
												<label for="rate5" class="visible-star"><i class="fa fa-star" rel="tooltip" data-container="body" data-placement="top" data-original-title="<?php echo $entry_good; ?>"></i></label>
												<input type="radio" id="rate4" value="4" name="rating" class="hidden-star">
												<label for="rate4" class="visible-star"><i class="fa fa-star"></i></label>
												<input type="radio" id="rate3" value="3" name="rating" class="hidden-star">
												<label for="rate3" class="visible-star"><i class="fa fa-star"></i></label>
												<input type="radio" id="rate2" value="2" name="rating" class="hidden-star">
												<label for="rate2" class="visible-star"><i class="fa fa-star"></i></label>
												<input type="radio" id="rate1" value="1" name="rating" class="hidden-star">
												<label for="rate1" class="visible-star"><i class="fa fa-star" rel="tooltip" data-container="body" data-placement="top" data-original-title="<?php echo $entry_bad; ?>"></i></label>
											</div>
										</div>
									</div>
									<div class="formbox hidden">
										<p class="form-label"><strong><?php echo $entry_name; ?></strong> <span class="required">*</span></p>
										<input type="text" name="name" value="<?php echo $profile_name; ?>" placeholder="<?php echo $profile_name; ?>" readonly="readonly" />
									</div>
								</div>
								<div class="formboxcolumn">
									<div class="formbox" title="<?php echo $entry_review; ?>" rel="tooltip" data-container="body">
										<p class="form-label"><strong><?php echo $entry_review; ?></strong> <span class="required">*</span></p>
										<textarea name="text" cols="40" rows="8" style="width: 98%;" placeholder="<?php echo $entry_review; ?>"></textarea>
										<span class="help"><i class="fa fa-code"></i><?php echo $text_note; ?></span>
									</div>
								</div>
							</div>
							<div class="buttons">
								<div class="left"><a id="button-review" class="button"><i class="fa fa-pencil"></i><?php echo $button_submit; ?></a></div>
							</div>
							<?php } else { ?>
							<div class="content">
								<h3 id="review-title" class="sub-section-title"><?php echo $text_write; ?></h3>
								<div class="warning">
									<p><?php echo $review_unauthorized; ?></p>
									<span class="icon"><i class="fa fa-exclamation-triangle"></i></span>
								</div>
							</div>
							<?php } ?>
						</div>
						<?php } ?>
                    </div>
                </div>
                <input type="hidden" name="profile_id" value="<?php echo $profile_id; ?>" />
			</div>
        </section>
        <?php } else { ?>
        <div class="global-page">
            <div class="information">
                <p><?php echo $text_error; ?></p>
                <span class="icon"><i class="fa fa-info-circle"></i></span>
            </div>
            <div class="buttons">
				<div class="left"><a href="<?php echo $back; ?>" class="button"><?php echo $button_back; ?></a></div>
				<div class="right"><a href="<?php echo $continue; ?>" class="button button_alt"><?php echo $button_continue; ?></a></div>
            </div>
        </div>
        <?php } ?>

        <?php if ($text_products) { ?>
        <section id="member-listings" class="container-bottom profile-listings-section">
            <aside id="sidebar" class="sidebar container-left">
                <?php echo $column_left; ?>
                <?php require_once('filter.inc.php'); ?>
                <?php echo $column_right; ?>
            </aside>
            <div class="container-center">
                <div class="content-page">
                    <?php echo $content_top; ?>
                    <?php require_once('products.inc.php'); ?>
                    <?php echo $content_bottom; ?>
                </div>
            </div>
        </section>
        <?php } ?>

    </div>
</main>
<script type="text/javascript"><!--
var text_wait = '<?php echo $text_wait; ?>';
//--></script>
<?php echo $footer; ?>
