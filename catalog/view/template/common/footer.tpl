<footer id="page-footer">
    <div class="container-footer">
        <div class="layout">
            <div class="footer">
                <nav class="middle-footer footer-column1111">
    				<div class="column">
    					<h6><?php echo $heading_product; ?></h6>
    					<ul>
                            <li><a href="<?php echo $product; ?>"><?php echo $text_product; ?></a></li>
                            <li><a href="<?php echo $category; ?>"><?php echo $text_category; ?></a></li>
                            <li><a href="<?php echo $manufacturer; ?>"><?php echo $text_manufacturer; ?></a></li>
                            <li><a href="<?php echo $member; ?>"><?php echo $text_member; ?></a></li>
                            <li><a href="<?php echo $search; ?>"><?php echo $text_search; ?></a></li>
                            <li class="hidden"><a href="<?php echo $featured; ?>"><?php echo $text_featured; ?></a></li>
                            <li class="hidden"><a href="<?php echo $special; ?>"><?php echo $text_special; ?></a></li>
    					</ul>
                    </div><div class="column">
    					<h6><?php echo $heading_account; ?></h6>
    					<ul>
                            <?php if (!$logged) { ?>
    						<li><a href="<?php echo $login; ?>"><?php echo $text_login; ?></a></li>
                            <li><a href="<?php echo $register; ?>"><?php echo $text_register; ?></a></li>
                            <li><a href="<?php echo $post; ?>"><?php echo $text_post; ?></a></li>
                            <li><a href="<?php echo $compare; ?>"><?php echo $text_compare; ?></a></li>
    						<li><a href="<?php echo $forgotten; ?>"><?php echo $text_forgotten; ?></a></li>
                            <?php } else { ?>
    						<li><a href="<?php echo $account; ?>"><?php echo $text_account; ?></a></li>
                            <li><a href="<?php echo $profile; ?>"><?php echo $text_profile; ?></a></li>
                            <li><a href="<?php echo $products; ?>"><?php echo $text_products; ?></a></li>
    						<li><a href="<?php echo $wishlist; ?>"><?php echo $text_wishlist; ?></a></li>
                            <li><a href="<?php echo $logout; ?>"><?php echo $text_logout; ?></a></li>
                            <?php } ?>
    					</ul>
                    </div><div class="column">
                        <h6><?php echo $heading_information; ?></h6>
                        <ul>
                            <?php foreach ($informations as $information) { ?>
                            <li><a href="<?php echo $information['href']; ?>"><?php echo $information['title']; ?></a></li>
                            <?php } ?>
                            <li><a href="<?php echo $contact; ?>"><?php echo $text_contact; ?></a></li>
                        </ul>
                    </div><div class="column">
                        <h6>
                            <a href="<?php echo $about; ?>">
                                <img srcset="<?php echo $server . $logo_img['logo_regular_1x']; ?>,
                                        <?php echo $server . $logo_img['logo_regular_2x']; ?> 2x"
                                    src="<?php echo $server . $logo_img['logo_regular_2x']; ?>"
                                    alt="<?php echo $text_logo_footer; ?>"
                                    width="220"
                                    height="63"
                                    class="logo-regular" />
                            </a>
                        </h6>
                        <p class="text-larger"><a href="<?php echo $product; ?>"><i><?php echo $text_slogan; ?></i></a></p>
                        <p class="text-smaller"><a href="<?php echo $sitemap; ?>"><?php echo $text_sitemap; ?></a>
                        <?php foreach ($informations_extra as $information_extra) { ?>
                        &nbsp;|&nbsp;<a href="<?php echo $information_extra['href']; ?>"><?php echo $information_extra['title']; ?></a>
                        <?php } ?></p>
                        <?php if ($social_links) { ?>
                        <p>
                            <?php foreach ($social_links as $key => $value) { ?>
                                <?php if ($value['show']) { ?>
                                <a href="<?php echo $value['url']; ?>" class="contact_icons" target="_blank">
                                    <span class="social <?php echo $key; ?>"><i class="fa <?php echo $value['fa-icon']; ?>"></i></span>
                                </a>
                                <?php } ?>
                            <?php } ?>
                        </p>
                        <?php } ?>
                    </div>
                </nav>
                <a class="top" id="top"><i class="fa fa-arrow-up"></i></a>
            </div>
        </div>
    </div>
    <div class="container-footer-bottom">
        <div class="layout">
            <div class="footer">
                <div class="bottom-footer">
                    <p class="powered">
                        <br />
                        <a href="mailto:<?php echo $contact_email; ?>"><?php echo $text_powered; ?></a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</footer>
<?php if (!$logged) { ?>
<div style="display:none;">
	<div id="login-popup"></div>
</div>
<div style="display:none;">
	<div id="register-popup"></div>
</div>
<?php } ?>
<div style="display:none;">
	<div id="contact-popup"></div>
</div>
<!-- JS -->
<script type="text/javascript"><!--
var fbAppID = '<?php echo $fb_app_id; ?>'; // for FB login only
//--></script>
<?php if (!$minify) { ?>
<script type="text/javascript" src="<?php echo $server; ?>catalog/view/root/javascript/jquery-3.2.1.min.js"></script>
<script type="text/javascript" src="<?php echo $server; ?>catalog/view/root/cookie/src/js.cookie.js"></script>
<script type="text/javascript" src="<?php echo $server; ?>catalog/view/root/javascript/tabs.js"></script>
<script type="text/javascript" src="<?php echo $server; ?>catalog/view/root/icheck/icheck.min.js"></script>
<script type="text/javascript" src="<?php echo $server; ?>catalog/view/root/bootstrap/js/bootstrap.min.js"></script>
<script type="text/javascript" src="<?php echo $server; ?>catalog/view/root/colorbox/js/jquery.colorbox-min.js"></script>
<script type="text/javascript" src="<?php echo $server; ?>catalog/view/root/javascript/imagesloaded.pkgd.min.js"></script>
<script type="text/javascript" src="<?php echo $server; ?>catalog/view/root/javascript/masonry.pkgd.min.js"></script>
<script type="text/javascript" src="<?php echo $server; ?>catalog/view/root/javascript/main.js"></script>
<script type="text/javascript" src="<?php echo $server; ?>catalog/view/root/javascript/listings.js"></script>
<script type="text/javascript" src="<?php echo $server; ?>catalog/view/root/javascript/profile.js"></script>
<script type="text/javascript" src="<?php echo $server; ?>catalog/view/root/javascript/listing.js"></script>
<script type="text/javascript" src="<?php echo $server; ?>catalog/view/root/javascript/module.js"></script>
<?php } else { ?>
<script type="text/javascript" src="<?php echo $server . 'catalog/view/' . $js_min; ?>"></script>
<?php } ?>
<?php foreach ($scripts as $script) { ?>
<script type="text/javascript" src="<?php echo $server . $script . $fingerprint; ?>"></script>
<?php } ?>
<?php if ($social_buttons) { ?>
<script type="text/javascript" src="https://platform-api.sharethis.com/js/sharethis.js#property=59bda5f0d022ae0011cd87f0&product=inline-share-buttons"></script>
<?php } ?>
<script type="text/javascript" src="https://www.google.com/recaptcha/api.js?onload=onloadReCaptcha&render=explicit" async defer></script>
</body>
</html>
