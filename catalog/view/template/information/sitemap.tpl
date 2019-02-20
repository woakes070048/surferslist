<?php echo $header; ?>
<div class="container-page sitemap-page">
    <div class="breadcrumb">
        <div class="layout">
            <h1><a href="<?php echo $sitemap; ?>"><i class="fa fa-sitemap"></i><?php echo $heading_title; ?></a></h1>
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
            <div class="content-page">
            	<?php echo $content_top; ?>
				<?php echo $notification; ?>
                <div class="global-page">
                    <p class="text-center text-larger"><?php echo $text_intro; ?></p>
                    <div class="widget widget-ac-container">
                        <div class="widget-ac">
                            <h6><i class="fa fa-sitemap"></i>&emsp;<?php echo $text_category; ?><span><i class="fa fa-chevron-down icon-ac-down"></i><i class="fa fa-chevron-up icon-ac-up"></i></span></h6>
                            <div class="ac-content">
                                <?php foreach ($categories as $category_1) { ?>
                                <ul class="list-icon list-icon-top list-icon">
                                    <li class="list-icon-title"><strong><a href="<?php echo $category_1['href']; ?>"><?php echo $category_1['name']; ?></strong></a>
                                        <?php if ($category_1['children']) { ?>
                                        <ul>
                                            <?php foreach ($category_1['children'] as $category_2) { ?>
                                            <li>&emsp; <a href="<?php echo $category_2['href']; ?>"><?php echo $category_2['name']; ?></a>
                                                <?php if ($category_2['children']) { ?>
                                                <ul>
                                                    <?php foreach ($category_2['children'] as $category_3) { ?>
                                                    <li>&emsp; &emsp; <a href="<?php echo $category_3['href']; ?>"><?php echo $category_3['name']; ?></a></li>
                                                    <?php } ?>
                                                </ul>
                                            <?php } ?>
                                            </li>
                                            <?php } ?>
                                        </ul>
                                        <?php } ?>
                                    </li>
                                </ul>
                            <?php } ?>
                            </div>
                        </div>
                        <div class="widget-ac">
                            <h6><i class="fa fa-th-list"></i>&emsp;<?php echo $heading_product; ?><span><i class="fa fa-chevron-down icon-ac-down"></i><i class="fa fa-chevron-up icon-ac-up"></i></span></h6>
                            <div class="ac-content">
                                <ul class="list-icon list-icon-top list-icon">
                                    <li>&emsp; <a href="<?php echo $listings; ?>"><?php echo $text_listings; ?></a></li>
                                    <li>&emsp; <a href="<?php echo $category; ?>"><?php echo $text_categories; ?></a></li>
                                    <li>&emsp; <a href="<?php echo $manufacturer; ?>"><?php echo $text_manufacturers; ?></a></li>
                                    <li>&emsp; <a href="<?php echo $member; ?>"><?php echo $text_members; ?></a></li>
                                    <li>&emsp; <a href="<?php echo $featured; ?>"><?php echo $text_featured; ?></a></li>
                                    <li>&emsp; <a href="<?php echo $special; ?>"><?php echo $text_special; ?></a></li>
                                    <li>&emsp; <a href="<?php echo $search; ?>"><?php echo $text_search; ?></a></li>
                                </ul>
                            </div>
                        </div>
                        <div class="widget-ac">
                            <h6><i class="fa fa-info"></i>&emsp;<?php echo $heading_information; ?><span><i class="fa fa-chevron-down icon-ac-down"></i><i class="fa fa-chevron-up icon-ac-up"></i></span></h6>
                            <div class="ac-content">
                                <ul class="list-icon list-icon-top list-icon">
                                    <?php foreach ($informations as $information) { ?>
                                    <li>&emsp; <a href="<?php echo $information['href']; ?>"><?php echo $information['title']; ?></a></li>
                                    <?php } ?>
                                    <li>&emsp; <a href="<?php echo $sitemap; ?>"><?php echo $text_sitemap; ?></a></li>
                                </ul>
                            </div>
                        </div>
                        <div class="widget-ac">
                            <h6><i class="fa fa-group"></i>&emsp;<?php echo $text_service; ?><span><i class="fa fa-chevron-down icon-ac-down"></i><i class="fa fa-chevron-up icon-ac-up"></i></span></h6>
                            <div class="ac-content">
                                <ul class="list-icon list-icon-top list-icon">
                                    <li>&emsp; <a href="<?php echo $register; ?>"><?php echo $text_register; ?></a></li>
                                    <li>&emsp; <a href="<?php echo $login; ?>"><?php echo $text_login; ?></a></li>
                                    <li>&emsp; <a href="<?php echo $post; ?>"><?php echo $text_post; ?></a></li>
                                    <li>&emsp; <a href="<?php echo $contact; ?>"><?php echo $text_contact_profile; ?></a></li>
                                    <li>&emsp; <a href="<?php echo $compare; ?>"><?php echo $text_compare; ?></a></li>
                                    <!--<li>&emsp; <a href="<?php echo $return; ?>"><?php echo $text_return; ?></a></li>-->
                                    <li>&emsp; <a href="<?php echo $forgotten; ?>"><?php echo $text_forgotten; ?></a></li>
                                    <li>&emsp; <a href="<?php echo $logout; ?>"><?php echo $text_logout; ?></a></li>
                                </ul>
                            </div>
                        </div>
                        <div class="widget-ac">
                            <h6><i class="fa fa-user"></i>&emsp;<?php echo $text_account; ?><span><i class="fa fa-chevron-down icon-ac-down"></i><i class="fa fa-chevron-up icon-ac-up"></i></span></h6>
                            <div class="ac-content">
                                <ul class="list-icon list-icon-top list-icon">
                                    <li>&emsp; <a href="<?php echo $account; ?>"><?php echo $text_account; ?></a></li>
                                    <li>&emsp; <a href="<?php echo $profile; ?>"><?php echo $text_profile; ?></a></li>
                                    <li>&emsp; <a href="<?php echo $edit; ?>"><?php echo $text_edit; ?></a></li>
                                    <li>&emsp; <a href="<?php echo $address; ?>"><?php echo $text_address; ?></a></li>
                                    <li>&emsp; <a href="<?php echo $myproducts; ?>"><?php echo $text_products; ?></a></li>
                                    <li>&emsp; <a href="<?php echo $sales; ?>"><?php echo $text_sales; ?></a></li>
                                    <li>&emsp; <a href="<?php echo $review; ?>"><?php echo $text_review; ?></a></li>
                                    <li>&emsp; <a href="<?php echo $question; ?>"><?php echo $text_question; ?></a></li>
                                    <li>&emsp; <a href="<?php echo $wishlist; ?>"><?php echo $text_wishlist; ?></a></li>
                                    <li>&emsp; <a href="<?php echo $order; ?>"><?php echo $text_order; ?></a></li>
                                    <!--<li>&emsp; <a href="<?php echo $newsletter; ?>"><?php echo $text_newsletter; ?></a></li>-->
                                    <li>&emsp; <a href="<?php echo $password; ?>"><?php echo $text_password; ?></a></li>
                                </ul>
                            </div>
                        </div>
                        <div class="widget-ac">
                            <h6><i class="fa fa-shopping-cart"></i>&emsp;<?php echo $text_cart; ?><span><i class="fa fa-chevron-down icon-ac-down"></i><i class="fa fa-chevron-up icon-ac-up"></i></span></h6>
                            <div class="ac-content">
                                <ul class="list-icon list-icon-top list-icon clearafter">
                                    <li class="list-icon-title grid-2"><i class="fa fa-shopping-cart"></i> <a href="<?php echo $cart; ?>"><strong><?php echo $text_cart; ?></strong></a></li>
                                    <li class="list-icon-title grid-2"><i class="fa fa-credit-card"></i> <a href="<?php echo $checkout; ?>"><strong><?php echo $text_checkout; ?></strong></a></li>
                                </ul>
                            </div>
                        </div>
                        <div class="widget-ac">
                            <h6><i class="fa fa-globe"></i>&emsp;<?php echo $text_location; ?><span><i class="fa fa-chevron-down icon-ac-down"></i><i class="fa fa-chevron-up icon-ac-up"></i></span></h6>
                            <div class="ac-content">
                                <ul class="list-icon list-icon-top list-icon clearafter">
                                    <li class="list-icon-title grid-1"><i class="fa fa-globe"></i> <a href="<?php echo $location; ?>"><strong><?php echo $text_location_set; ?></strong></a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <p class="text-center"><?php echo $text_footer_contact; ?></p>
                </div>
                <?php echo $content_bottom; ?>
            </div>
        </div>
        <?php echo $column_right; ?>
    </div>
</div>
<?php echo $footer; ?>
