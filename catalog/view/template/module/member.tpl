<?php if ($members) { ?>
<?php if ($position == 'content_top' || $position == 'content_bottom') { ?>
    <div class="widget-module horizontal-grid">
        <div class="grid-items-container">
            <div class="grid global-grid-item">
    		<div class="grid-sizer"></div>
            <?php foreach ($members as $member) { ?>
                <article class="grid-item runmember">
                <div class="image">
                    <?php if ($member['image']) { ?>
                    <a href="<?php echo $member['href']; ?>" title="<?php echo $member['name']; ?>">
                        <img src="<?php echo $member['image']; ?>" alt="<?php echo $member['name']; ?>" />
                    </a>
                    <?php } ?>
                    <span class="description">
                        <a href="<?php echo $member['href']; ?>" title="<?php echo $member['name']; ?>">
                            <?php if ($product_count) { ?>
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
        <div class="buttons">
        	<div class="center">
        		<a class="button button_alt button_more" href="<?php echo $more; ?>"><i class="fa fa-group"></i><?php echo $text_more; ?></a>
        	</div>
        </div>
    </div>
</div>
<?php } ?>
<?php if ($position == 'column_left' || $position == 'column_right') { ?>
<div id="featured-profiles" class="widget widget-slideshow hidden-medium">
	<h6><?php echo $heading_title; ?></h6>
    <div class="content slideshow">
    	<div class="loader"></div>
    	<div class="slideshow-carousel" style="width:<?php echo $image_width . 'px'; ?>;height:<?php echo $image_height . 'px'; ?>">
            <?php foreach ($members as $member) { ?>
            <article class="featured-profile item slideshow-item">
                <div class="image image-no-border" title="<?php echo $member['name']; ?>" rel="tooltip" data-container="body">
                    <?php if ($member['href']) { ?>
                    <a href="<?php echo $member['href']; ?>"><img src="<?php echo $member['image']; ?>" alt="<?php echo $member['name']; ?>" /></a>
                    <?php } else { ?>
                    <img src="<?php echo $member['image']; ?>" alt="<?php echo $brand['name']; ?>" />
                    <?php } ?>
                    <span class="description">
                        <a href="<?php echo $member['href']; ?>">
                            <header><h3><?php echo $member['name']; ?></h3></header>
                            <?php if ($product_count) { ?>
                            <span class="listings">
                                <?php echo $member['text_products']; ?>
                            </span>
                            <?php } ?><?php if (isset($member['rating'])) { ?>
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
            </article>
            <?php } ?>
        </div>
        <input type="hidden" name="slideshow_options" value="<?php echo $slider_options; ?>" />
    </div>
    <div class="buttons">
    	<div class="center">
    		<a class="button button_alt button_more" href="<?php echo $more; ?>"><i class="fa fa-group"></i><?php echo $text_more; ?></a>
    	</div>
    </div>
</div>
<?php } ?>
<?php } ?>
