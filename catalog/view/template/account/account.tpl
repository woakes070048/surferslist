<?php echo $header; ?>
<div class="container-page">
    <div class="breadcrumb">
        <div class="layout">
            <h1><i class="fa fa-user-circle"></i> <?php echo $heading_title; ?></h1>
            <div class="links">
                <?php foreach ($breadcrumbs as $breadcrumb) { ?>
                <?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
                <?php } ?>
            </div>
        </div>
    </div>
    <div class="layout">
        <?php echo $column_left; ?>
        <div class="container-center">
            <div class="content-page my-account my-profile">
				<?php echo $notification; ?>
				<?php echo $content_top; ?>

				<div class="widget">

				<?php if ($activated) { ?>
					<h6><?php echo $text_member_profile; ?></h6>

					<?php if (!$enabled) { ?>

					<div class="content">
						<div class="warning">
							<p><?php echo $text_not_enabled; ?></p>
							<span class="icon"><i class="fa fa-exclamation-triangle"></i></span>
						</div>
					</div>

					<?php } else { ?>

					<div class="content member-profile clearafter">
						<div class="grid-3">
                            <div class="member-image">
                                <div class="image image-border" title="<?php echo $help_member_image; ?>" rel="tooltip">
    								<img src="<?php echo $member_thumb; ?>" title="<?php echo $member_name; ?>" alt="<?php echo $member_name; ?>" />
    							</div>

                                <div class="review">
                                    <div class="rating">
                                        <div class="rating-stars view-star<?php echo $member_rating; ?>" title="<?php echo $help_member_rating; ?>" rel="tooltip">
                                            <i class="fa fa-star icon_star star-color color1"></i><i class="fa fa-star icon_star star-color color2"></i><i class="fa fa-star icon_star star-color color3"></i><i class="fa fa-star icon_star star-color color4"></i><i class="fa fa-star icon_star star-color color5"></i><i class="fa fa-star icon_star star-dark dark1"></i><i class="fa fa-star icon_star star-dark dark2"></i><i class="fa fa-star icon_star star-dark dark3"></i><i class="fa fa-star icon_star star-dark dark4"></i><i class="fa fa-star icon_star star-dark dark5"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>

							<div class="text-center hidden">
								<p class="text-smaller"><?php echo $text_member_date_added; ?><br /><em><?php echo $member_date_added; ?></em></p>
							</div>
						</div>

						<div class="grid-6">
							<h2 class="member-name hidden"><?php echo $member_name; ?></h2>

							<ul class="global-attribute">
                                <li><b><?php echo $text_member_name; ?></b><i><?php echo $member_name; ?></i></li>
								<li><b><?php echo $text_member_profile_page; ?></b><i><a href="<?php echo $member_page; ?>"><?php echo $member_page; ?></a></i></li>
								<li><b><?php echo $text_member_group; ?></b><i><?php echo $member_group; ?></i></li>
                                <li><b><?php echo $text_products; ?></b><i><a href="<?php echo $product; ?>"><?php echo $member_total_products . ' ' . $text_products; ?></a></i></li>
                                <li><b><?php echo $text_member_paypal; ?></b><i><?php echo $member_paypal; ?></i></li>
								<li><b><?php echo $text_member_location; ?></b><i><?php echo $member_location; ?></i></li>
								<?php if ($member_tags) { ?><li><b><?php echo $text_member_tags; ?></b><i><?php for ($i = 0; $i < count($member_tags); $i++) { ?><a href="<?php echo $member_tags[$i]['href']; ?>">#<?php echo $member_tags[$i]['tag']; ?></a>&emsp;<?php } ?></i></li><?php } ?>
                                <?php if ($member_socials) { ?><li><b><?php echo $text_member_socials; ?></b><i><div class="socials">
                                <?php if ($text_member_custom_field_01 && !empty($member_custom_field_01)) { ?><a href="<?php echo $member_custom_field_01; ?>" target="_blank"><i class="fa fa-globe" title="<?php echo $text_member_custom_field_01; ?>" rel="tooltip"></i></a><?php } ?>
                                <?php if ($text_member_custom_field_02 && !empty($member_custom_field_02)) { ?><a href="<?php echo $member_custom_field_02; ?>" target="_blank"><i class="fa fa-twitter-square" title="<?php echo $text_member_custom_field_02; ?>" rel="tooltip"></i></a><?php } ?>
                                <?php if ($text_member_custom_field_03 && !empty($member_custom_field_03)) { ?><a href="<?php echo $member_custom_field_03; ?>" target="_blank"><i class="fa fa-facebook-square" title="<?php echo $text_member_custom_field_03; ?>" rel="tooltip"></i></a><?php } ?>
                                <?php if ($text_member_custom_field_04 && !empty($member_custom_field_04)) { ?><a href="<?php echo $member_custom_field_04; ?>" target="_blank"><i class="fa fa-pinterest-square" title="<?php echo $text_member_custom_field_04; ?>" rel="tooltip"></i></a><?php } ?>
                                <?php if ($text_member_custom_field_05 && !empty($member_custom_field_05)) { ?><a href="<?php echo $member_custom_field_05; ?>" target="_blank"><i class="fa fa-google-plus-square" title="<?php echo $text_member_custom_field_05; ?>" rel="tooltip"></i></a><?php } ?>
                                <?php if ($text_member_custom_field_06 && !empty($member_custom_field_06)) { ?><a href="<?php echo $member_custom_field_06; ?>" target="_blank"><i class="fa fa-link" title="<?php echo $text_member_custom_field_06; ?>" rel="tooltip"></i></a><?php } ?>
                                </div></i></li><?php } ?>
                                <li><b><?php echo $text_member_date_added; ?></b><i><?php echo $member_date_added; ?></i></li>
							</ul>

						</div>
					</div>

					<div class="buttons">
						<div class="left">
							<a href="<?php echo $member; ?>" class="button"><i class="fa fa-pencil"></i><?php echo $text_member_edit; ?></a>
							<a href="<?php echo $product; ?>" class="button button_alt"><i class="fa fa-th-list"></i><?php echo $text_products_edit; ?></a>
						</div>
						<div class="right">
							<a href="<?php echo $member_page; ?>" class="button button_highlight"><i class="fa fa-user"></i><?php echo $text_member_view; ?></a>
						</div>
					</div>

					<?php } ?>

				<?php } else { ?>

					<h6><?php echo $text_member_profile; ?></h6>

					<div class="content">
						<div class="warning">
							<p><?php echo $text_not_activated; ?></p>
							<span class="icon"><i class="fa fa-exclamation-triangle"></i></span>
						</div>
					</div>

					<div class="buttons">
						<div class="left join-now">
							<a href="<?php echo $member; ?>" class="button bigger"><i class="fa fa-user"></i><?php echo $text_activate; ?></a>
						</div>
					</div>

				<?php } ?>

				</div>

				<div class="widget">

					<h6><?php echo $text_account_info; ?></h6>

					<div class="content clearafter">
						<div class="grid-2 personal-info">
							<h3 class="sub-section-title"><?php echo $text_personal; ?></h3>

							<ul class="global-attribute">
								<li><b><?php echo $text_name; ?></b><i><?php echo $firstname; ?>&nbsp;<?php echo $lastname; ?></i></li>
								<li><b><?php echo $text_email; ?></b><i><?php echo $email; ?><?php if ($activated && $enabled) { ?>&nbsp;<span class="help"><a href="<?php echo $notify; ?>" title="<?php echo $help_notify; ?>" rel="tooltip"><?php echo $text_notify; ?></a></span><?php } ?></i></li>
								<li><b><?php echo $text_address; ?></b><i><?php if ($address) { ?><?php echo $address; ?><?php } else { ?><a href="<?php echo $address_add; ?>" title="<?php echo $help_member_address_add; ?>" rel="tooltip"><?php echo $text_address_add; ?></a><?php } ?></i></li>
								<li><b><?php echo $text_telephone; ?></b><i><?php echo $telephone ? $telephone : '<span class="help text-smaller">' . $help_phone . ' ' . '<a href="' . $edit . '">' . $button_add . ' ' . $text_telephone . ' &raquo;</a></span>'; ?></i></li>
							</ul>

							<p class="help"><i class="fa fa-info-circle"></i> <?php echo $help_personal_info; ?></p>
						</div>

						<div class="grid-2 account-info">
							<h3 class="sub-section-title"><?php echo $text_account_activity; ?></h3>

							<ul class="global-attribute">
								<?php if ($activated && $enabled) { ?>
								<li><b title="<?php echo $help_member_listings; ?>" rel="tooltip"><?php echo $text_my_products; ?></b><i><a href="<?php echo $product; ?>"><?php echo $member_total_products . ' ' . $text_products; ?></a></i></li>
								<?php } ?>
                                <li><b title="<?php echo $help_member_questions; ?>" rel="tooltip"><?php echo $text_my_questions; ?></b><i><a href="<?php echo $questions; ?>"><?php echo $member_total_questions . ' ' . $text_questions; ?></a></i></li>
                                <?php if ($activated && $enabled) { ?>
                                <li><b title="<?php echo $help_member_reviews; ?>" rel="tooltip"><?php echo $text_my_reviews; ?></b><i><a href="<?php echo $reviews; ?>"><?php echo $member_total_reviews . ' ' . $text_reviews; ?></a></i></li>
                                <?php } ?>
								<li><b title="<?php echo $help_member_saved_listings; ?>" rel="tooltip"><?php echo $text_my_wishlist; ?></b><i><a href="<?php echo $wishlist; ?>"><?php echo $account_total_wishlist . ' ' . $text_wishlist; ?></a></i></li>
								<li><b title="<?php echo $help_member_order_history; ?>" rel="tooltip"><?php echo $text_my_orders; ?></b><i><a href="<?php echo $order; ?>"><?php echo $account_total_orders . ' ' . $text_orders; ?></a></i></li>
                                <?php if ($activated && $enabled) { ?>
                                <li><b title="<?php echo $help_member_sales; ?>" rel="tooltip"><?php echo $text_my_sales; ?></b><i><a href="<?php echo $sales; ?>"><?php echo $member_total_sales . ' ' . $text_sales; ?></a></i></li>
                                <?php } ?>
							</ul>
						</div>
					</div>

					<div class="buttons clearafter">
						<div class="grid-2">
							<div class="left">
								<a href="<?php echo $edit; ?>" class="button"><i class="fa fa-pencil"></i><?php echo $text_personal_edit; ?></a>
								<a href="<?php echo $addresses; ?>" class="button button_alt"><i class="fa fa-address-book-o"></i><?php echo $member_total_addresses . ' ' . $text_addresses; ?></a>
							</div>
						</div>
						<div class="grid-2">
							<div class="left">
								<a href="<?php echo $password; ?>" class="button"><i class="fa fa-key"></i><?php echo $text_password; ?></a>
								<a href="<?php echo $logout; ?>" class="button button_alt"><i class="fa fa-sign-out"></i><?php echo $text_logout; ?></a>
							</div>
						</div>
					</div>

				</div>

                <?php echo $content_bottom; ?>
            </div>
        </div>
        <?php echo $column_right; ?>
    </div>
</div>
<?php echo $footer; ?>
