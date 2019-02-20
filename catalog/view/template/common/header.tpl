<!DOCTYPE html>
<html dir="<?php echo $direction; ?>" lang="<?php echo $lang; ?>" class="no-js">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1, user-scalable=no" />
<title><?php echo $title; ?></title>
<base href="<?php echo $server; ?>" />
<?php foreach ($dns_prefetch as $domain) { ?>
<link rel="dns-prefetch" href="<?php echo $domain; ?>" />
<?php } ?>
<?php if ($description) { ?>
<meta name="description" content="<?php echo $description; ?>" />
<?php } ?>
<?php if ($keywords) { ?>
<meta name="keywords" content="<?php echo $keywords; ?>" />
<?php } ?>
<?php if ($app) { ?>
<meta name="theme-color" content="#0d8f91" />
<meta name="apple-mobile-web-app-title" content="<?php echo $name; ?>" />
<meta name="apple-mobile-web-app-capable" content="yes" />
<meta name="apple-mobile-web-app-status-bar-style" content="black" />
<meta name="msapplication-TileColor" content="#ffffff" />
<meta name="msapplication-TileImage" content="<?php echo $server; ?>ms-icon-144x144.png" />
<?php } ?>
<?php if ($open_graph && isset($open_graph['url'])) { ?>
<meta property="og:site_name" content="<?php echo $name; ?>" />
<meta property="og:title" content="<?php echo $open_graph['title']; ?>" />
<?php if (isset($open_graph['description'])) { ?>
<meta property="og:description" content="<?php echo $open_graph['description']; ?>" />
<?php } ?>
<meta property="og:type" content="<?php echo $open_graph['type']; ?>" />
<meta property="og:url" content="<?php echo $open_graph['url']; ?>" />
<?php if (isset($open_graph['image'])) { ?>
<meta property="og:image" content="<?php echo $open_graph ['image']; ?>" />
<?php } ?>
<?php if (isset($open_graph['image_type'])) { ?>
<meta property="og:image:type" content="<?php echo $open_graph['image_type']; ?>" />
<?php } ?>
<?php if (isset($open_graph['image_width'])) { ?>
<meta property="og:image:width" content="<?php echo $open_graph['image_width']; ?>" />
<?php } ?>
<?php if (isset($open_graph['image_height'])) { ?>
<meta property="og:image:height" content="<?php echo $open_graph['image_height']; ?>" />
<?php } ?>
<?php } ?>
<?php if ($favicon) { ?>
<link href="<?php echo $server . $favicon; ?>" rel="icon" />
<?php } ?>
<?php if ($ascii_art) { ?>
<!--
<?php echo $ascii_art; ?>
-->
<?php } ?>
<link rel="alternate" href="<?php echo $alternate; ?>" hreflang="<?php echo $lang; ?>" />
<?php if ($app) { ?>
<link rel="apple-touch-startup-image" media="(device-width: 375px) and (device-height: 812px) and (-webkit-device-pixel-ratio: 3)" href="<?php echo $server; ?>apple-launch-1125x2436.png"><!-- iPhone X (1125px x 2436px) -->
<link rel="apple-touch-startup-image" media="(device-width: 375px) and (device-height: 667px) and (-webkit-device-pixel-ratio: 2)" href="<?php echo $server; ?>apple-launch-750x1334.png"><!-- iPhone 8, 7, 6s, 6 (750px x 1334px) -->
<link rel="apple-touch-startup-image" media="(device-width: 414px) and (device-height: 736px) and (-webkit-device-pixel-ratio: 3)" href="<?php echo $server; ?>apple-launch-1242x2208.png"><!-- iPhone 8 Plus, 7 Plus, 6s Plus, 6 Plus (1242px x 2208px) -->
<link rel="apple-touch-startup-image" media="(device-width: 320px) and (device-height: 568px) and (-webkit-device-pixel-ratio: 2)" href="<?php echo $server; ?>apple-launch-640x1136.png"><!-- iPhone 5 (640px x 1136px) -->
<link rel="apple-touch-startup-image" media="(device-width: 768px) and (device-height: 1024px) and (-webkit-device-pixel-ratio: 2)" href="<?php echo $server; ?>apple-launch-1536x2048.png"><!-- iPad Mini, Air (1536px x 2048px) -->
<link rel="apple-touch-startup-image" media="(device-width: 834px) and (device-height: 1112px) and (-webkit-device-pixel-ratio: 2)" href="<?php echo $server; ?>apple-launch-1668x2224.png"><!-- iPad Pro 10.5" (1668px x 2224px) -->
<link rel="apple-touch-startup-image" media="(device-width: 1024px) and (device-height: 1366px) and (-webkit-device-pixel-ratio: 2)" href="<?php echo $server; ?>apple-launch-2048x2732.png"><!-- iPad Pro 12.9" (2048px x 2732px) -->
<link rel="apple-touch-icon" href="<?php echo $server; ?>apple-icon.png?v=2019" />
<link rel="apple-touch-icon" sizes="57x57" href="<?php echo $server; ?>apple-icon-57x57.png?v=2019" />
<link rel="apple-touch-icon" sizes="60x60" href="<?php echo $server; ?>apple-icon-60x60.png?v=2019" />
<link rel="apple-touch-icon" sizes="72x72" href="<?php echo $server; ?>apple-icon-72x72.png?v=2019" />
<link rel="apple-touch-icon" sizes="76x76" href="<?php echo $server; ?>apple-icon-76x76.png?v=2019" />
<link rel="apple-touch-icon" sizes="114x114" href="<?php echo $server; ?>apple-icon-114x114.png?v=2019" />
<link rel="apple-touch-icon" sizes="120x120" href="<?php echo $server; ?>apple-icon-120x120.png?v=2019" />
<link rel="apple-touch-icon" sizes="144x144" href="<?php echo $server; ?>apple-icon-144x144.png?v=2019" />
<link rel="apple-touch-icon" sizes="152x152" href="<?php echo $server; ?>apple-icon-152x152.png?v=2019" />
<link rel="apple-touch-icon" sizes="180x180" href="<?php echo $server; ?>apple-icon-180x180.png?v=2019" />
<link rel="icon" type="image/png" sizes="192x192"  href="<?php echo $server; ?>android-icon-192x192.png" />
<link rel="icon" type="image/png" sizes="32x32" href="<?php echo $server; ?>favicon-32x32.png" />
<link rel="icon" type="image/png" sizes="96x96" href="<?php echo $server; ?>favicon-96x96.png" />
<link rel="icon" type="image/png" sizes="16x16" href="<?php echo $server; ?>favicon-16x16.png" />
<link rel="apple-touch-startup-image" href="<?php echo $server; ?>apple-icon.png?v=2019" />
<link rel="manifest" href="<?php echo $server; ?>manifest.json" />
<?php } ?>
<?php foreach ($links as $link) { ?>
<link href="<?php echo $link['href']; ?>" rel="<?php echo $link['rel']; ?>" />
<?php } ?>
<!-- CSS -->
<link href="https://fonts.googleapis.com/css?family=Lato|Ubuntu" rel="stylesheet">
<?php if (!$minify) { ?>
<link rel="stylesheet" type="text/css" href="<?php echo $server; ?>catalog/view/root/stylesheet/normalize.min.css" media="all" /><!-- 2.3kB -->
<?php if ($direction == 'rtl' || $direction == 'RTL') { ?>
<link rel="stylesheet" type="text/css" href="<?php echo $server; ?>catalog/view/root/stylesheet/stylesheet-rtl.css" media="all" /><!-- 12.7kB -->
<link rel="stylesheet" type="text/css" href="<?php echo $server; ?>catalog/view/root/stylesheet/stylesheet.css" media="all" /><!-- 176.2kB -->
<?php } else { ?>
<link rel="stylesheet" type="text/css" href="<?php echo $server; ?>catalog/view/root/stylesheet/stylesheet-ltr.css" media="all" /><!-- 12.0kB -->
<link rel="stylesheet" type="text/css" href="<?php echo $server; ?>catalog/view/root/stylesheet/stylesheet.css" media="all" /><!-- 176.2kB -->
<?php } ?>
<link rel="stylesheet" type="text/css" href="<?php echo $server; ?>catalog/view/root/font-awesome/css/font-awesome.min.css" media="all" /><!-- 22.1kB -->
<link rel="stylesheet" type="text/css" href="<?php echo $server; ?>catalog/view/root/bootstrap/css/bootstrap.min.css" media="all" /><!-- 4.8kB -->
<link rel="stylesheet" type="text/css" href="<?php echo $server; ?>catalog/view/root/colorbox/css/colorbox.css" media="all" /><!-- 3.0kB -->
<link rel="stylesheet" type="text/css" href="<?php echo $server; ?>catalog/view/root/slick/slick.css" media="all" /> <!-- 1.7kB -->
<link rel="stylesheet" type="text/css" href="<?php echo $server; ?>catalog/view/root/icheck/skins/minimal/custom.css" media="all" /><!-- 1.6kB -->
<?php } else { ?>
<link rel="stylesheet" type="text/css" href="<?php echo $server . 'catalog/view/' . $css_min; ?>" media="all" />
<?php } ?>
<?php foreach ($styles as $style) { ?>
<link rel="<?php echo $style['rel']; ?>" type="text/css" href="<?php echo $server . $style['href']; ?>" media="<?php echo $style['media']; ?>" />
<?php } ?>
<!-- Misc/Temp -->
<?php if ($minify) { ?>
<!-- Font Awesome -->
<script src="https://use.fontawesome.com/08241cf910.js"></script>
<?php } ?>
<!-- Google Analytics -->
<?php echo $google_analytics; ?>
</head>
<body class="<?php echo $page; ?>" <?php if ($app) { ?>ontouchstart=""<?php } ?>>
<div id="notification">
<?php if (isset($error)) { ?>
<div class="bg">
    <span class="close"><i class="fa fa-times-circle-o"></i></span>
    <div class="widget">
        <h6><i class="fa fa-exclamation-triangle"></i></h6>
        <p><?php echo $error ?></p>
    </div>
</div>
<?php } ?>
</div>
<div class="panelbody layout">
    <span id="closedbody"><i class="fa fa-times-circle-o"></i><?php echo $button_menu; ?></span>
    <div class="runpanelbody">
        <ul class="panelcol-2">
    		<?php foreach ($categories as $category) { ?>
    		<li><a href="<?php echo $category['href']; ?>"><?php if ($category['image']) { ?><span class="category-icon icon-<?php echo $category['alt']; ?>"></span><?php } ?><?php echo $category['name']; ?></a></li>
    		<?php } ?>
    		<li><a href="<?php echo $manufacturer; ?>"><i class="fa fa-ticket"></i><?php echo $text_product_manufacturer; ?></a></li>
    		<li><a href="<?php echo $member; ?>"><i class="fa fa-group"></i><?php echo $text_product_member; ?></a></li>
            <li><a href="<?php echo $product_search; ?>"><i class="fa fa-search"></i><?php echo $text_search; ?></a></li>
        </ul>
        <ul class="panelcol-2">
            <?php if (!$logged) { ?>
            <li><a href="<?php echo $login; ?>"><i class="fa fa-sign-in"></i><?php echo $text_login; ?></a></li>
            <li><a href="<?php echo $register; ?>"><i class="fa fa-group"></i><?php echo $text_register; ?></a></li>
            <?php } else { ?>
            <li><a href="<?php echo $account; ?>"><i class="fa fa-user-circle"></i><?php echo $text_account; ?></a></li>
            <li><a href="<?php echo $listings; ?>"><i class="fa fa-th-list"></i><?php echo $text_listings; ?></a></li>
            <?php } ?>
            <li><a href="<?php echo $post_link; ?>"><i class="fa fa-pencil"></i><?php echo $heading_post; ?></a></li>
            <li><a href="<?php echo $contact; ?>"><i class="fa fa-envelope"></i><?php echo $text_contact; ?></a></li>
            <li><a href="<?php echo $shopping_cart; ?>"><i class="fa fa-shopping-cart"></i><?php echo $text_shopping_cart; ?></a></li>
            <li><a href="<?php echo $about; ?>"><i class="fa fa-info-circle"></i><?php echo $text_about; ?></a></li>
            <li><a href="<?php echo $faq; ?>"><i class="fa fa-question-circle"></i><?php echo $text_faq; ?></a></li>
            <?php if ($logged) { ?>
            <li><a href="<?php echo $logout; ?>"><i class="fa fa-sign-out"></i><?php echo $text_logout; ?></a></li>
            <?php } else { ?>
            <li><a href="<?php echo $location; ?>"><i class="fa fa-globe"></i><?php echo $text_location; ?></a></li>
            <?php } ?>
        </ul>
        <div class="panelcol-1">
            <p class="text-empty">
                <?php if ($location_code) { ?>
                <?php echo $text_location; ?>: <a href="<?php echo $location; ?>"><span class="text-uppercase"><?php echo $location_geo; ?></span></a>
                <?php } else { ?>
                <a href="<?php echo $location; ?>"><span class="help"><i class="fa fa-globe"></i> <?php echo $text_location_set; ?></span></a>
                <?php } ?>
            </p>
        </div>
        <div class="panelfooter">
            <a href="<?php echo $home; ?>" title="<?php echo $name; ?>">
                <img srcset="<?php echo $server . $logo_img['logo_small_1x']; ?>,
                        <?php echo $server . $logo_img['logo_small_2x']; ?> 2x"
                    src="<?php echo $server . $logo_img['logo_small_2x']; ?>"
                    alt="<?php echo $name; ?>"
                    width="140"
                    height="60" />
            </a>
        </div>
    </div>
</div>
<header id="page-header">
    <div class="container-header-top">
    	<div class="layout">
    		<div class="header">
                <div class="top-header">
                	<div class="left-top-header">
                        <div id="hboxaccount" class="hbox<?php if ($logged) { ?> has-menu<?php } ?>">
        					<?php if (!$logged) { ?>
                            <span class="mini-info">
                                <a href="<?php echo $login; ?>"><i class="fa fa-sign-in" title="<?php echo $text_login; ?>"></i><?php echo $text_login; ?></a>
                            </span>
                            <?php } else { ?>
                            <div class="heading">
                                <span class="span-title mini-info"><i class="fa fa-user-circle-o" title="<?php echo $heading_account; ?>"></i><?php echo $heading_account; ?></span>
                            </div>
                            <div class="content">
                                <ul class="list">
        							<li><a href="<?php echo $account; ?>"><i class="fa fa-user-circle"></i><?php echo $text_account; ?></a></li>
        							<?php if (!$activated) { ?>
        							<li><a href="<?php echo $activate; ?>" class="highlight"><i class="fa fa-user highlight"></i><?php echo $text_activate_profile; ?></a></li>
                                    <?php } else { ?>
                                    <li><a href="<?php echo $listings; ?>" id="listings-total"><i class="fa fa-th-list"></i><?php echo $text_listings; ?></a></li>
        							<li><a href="<?php echo $profile; ?>"><i class="fa fa-user"></i><?php echo $text_profile; ?></a></li>
        							<?php } ?>
                                    <li><a href="<?php echo $wishlist; ?>" id="wishlist-total"><i class="fa fa-heart"></i><?php echo $text_wishlist; ?></a></li>
        							<li><a href="<?php echo $logout; ?>"><i class="fa fa-sign-out"></i><?php echo $text_logout; ?></a></li>
                                </ul>
                            </div>
                            <?php } ?>
                        </div>
                        <?php if (!$logged) { ?>
                        <div id="hboxregister" class="hbox">
                            <span class="mini-info">
                                <a href="<?php echo $register; ?>"><i class="fa fa-group" title="<?php echo $text_register; ?>"></i><?php echo $text_register; ?></a>
                            </span>
                        </div>
                        <?php } ?>
                        <div id="hboxlocation" class="hbox<?php if ($location_code) { ?> has-menu<?php } ?>">
                            <?php if (!$location_code) { ?>
        					<span class="mini-info">
        						<a href="<?php echo $location; ?>"><i class="fa fa-globe gray-text" title="<?php echo $text_location_set; ?>"></i><?php echo $text_location; ?></a>
        					</span>
                            <?php } else { ?>
                            <div class="heading">
                                <span class="span-title mini-info"><i class="fa fa-globe" title="<?php echo $location_code; ?>"></i><?php echo $location_code; ?></span>
                            </div>
                            <div class="content">
                                <ul class="list">
                                    <li><span class="dark-text"><i class="fa fa-globe gold-text"></i><?php echo $location_geo; ?></span></li>
                                    <li><a href="<?php echo $location; ?>"><i class="fa fa-pencil grey-text"></i><?php echo $text_location_change; ?></a></li>
        							<li><a href="<?php echo $location_remove; ?>" class="red-text"><i class="fa fa-times-circle red-text"></i><?php echo $text_location_remove; ?></a></li>
                                </ul>
                            </div>
                            <?php } ?>
                        </div>
                        <?php echo $language; ?>
                        <?php echo $currency; ?>
                    </div>
                    <div class="right-top-header">
                        <?php echo $cart; ?>
                        <div id="hboxsearch" class="hbox">
            				<span class="mini-info">
            					<a href="<?php echo $product_search; ?>" id="search-link"><i class="fa fa-search" title="<?php echo $text_search; ?>"></i><?php echo $text_search; ?></a>
            				</span>
                        </div>
                        <div id="hboxinformation" class="hbox<?php if ($informations) { ?> has-menu<?php } ?>">
                            <?php if (!$informations) { ?>
                            <span class="mini-info">
                                <a href="<?php echo $about; ?>"><i class="fa fa-info-circle" title="<?php echo $text_about; ?>"></i><?php echo $text_about; ?></a>
                            </span>
                            <?php } else { ?>
                            <div class="heading">
                                <span class="span-title mini-info"><i class="fa fa-info-circle" title="<?php echo $text_about; ?>"></i><?php echo $text_about; ?></span>
                            </div>
                            <div class="content">
                                <ul class="list">
                                    <?php foreach ($informations as $information) { ?>
                                    <li><a href="<?php echo $information['href']; ?>"><i class="fa fa-info-circle"></i><?php echo $information['title']; ?></a></li>
                                    <?php } ?>
                                </ul>
                            </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
    		</div>
    	</div>
    </div>
    <div class="container-header">
        <div class="layout">
            <div id="header" class="header">
                <div class="middle-header">
                    <div id="logo">
                        <a href="<?php echo $home; ?>">
                            <img srcset="<?php echo $server . $logo_img['logo_regular_1x']; ?>,
                                    <?php echo $server . $logo_img['logo_regular_2x']; ?> 2x"
                                src="<?php echo $server . $logo_img['logo_regular_2x']; ?>"
                                alt="<?php echo $name; ?>"
                                width="220"
                                height="63"
                                class="logo-regular" />
                            <img srcset="<?php echo $server . $logo_img['logo_small_1x']; ?>,
                                    <?php echo $server . $logo_img['logo_small_2x']; ?> 2x"
                                src="<?php echo $server . $logo_img['logo_small_2x']; ?>"
                                alt="<?php echo $name; ?>"
                                width="140"
                                height="60"
                                class="logo-small" />
                        </a>
                    </div>
                	<nav id="menu" class="menu menu-floating-fff">
                        <div class="menu-inner clearafter">
                        	<ul class="menu-nav">
                                <li class="topcat hbox has-menu">
            						<span class="button button_browse bolder heading"><?php echo $text_product_browse; ?></span>
                                    <ul class="menuchildren skinchildren">
                                        <?php if ($categories) { ?>
                                        <?php foreach ($categories as $category) { ?>
                                        <li class="menucategory">
                                            <a href="<?php echo $category['href']; ?>">
                                                <?php if ($category['image']) { ?>
                                                <span class="category-icon icon-<?php echo $category['alt']; ?>"></span>
                                                <?php } ?>
                                                <?php echo $category['name']; ?>
                                            </a>
                                        </li>
                                        <?php } ?>
                                        <?php } else { ?>
                                        <li><a href="<?php echo $product_search; ?>"><i class="fa fa-search"></i><?php echo $text_product_search; ?></a></li>
                                        <li><a href="<?php echo $product; ?>"><i class="fa fa-th-list"></i><?php echo $text_product_listings; ?></a><ul class="subchildren skinchildren hidden"></ul></li>
                                        <li><a href="<?php echo $manufacturer; ?>"><i class="fa fa-ticket"></i><?php echo $text_product_manufacturer; ?></a></li>
                                        <li><a href="<?php echo $member; ?>"><i class="fa fa-group"></i><?php echo $text_product_member; ?></a></li>
                                        <li class="hidden"><a href="<?php echo $featured; ?>"><i class="fa fa-tag"></i><?php echo $text_product_featured; ?></a></li>
                                        <?php } ?>
                                    </ul>
                               </li>
                               <li class="topcat">
            						<a href="<?php echo $product_search. '&more=true'; ?>" class="button button_alt bolder menu-nav-search"><?php echo $text_search; ?></a>
                                    <?php if (false && $categories) { ?>
                                    <ul class="menuchildren skinchildren">
                                        <?php foreach ($categories as $category) { ?>
                                        <li class="menucategory">
                                            <a href="<?php echo $product_search . '?s=&category=' . $category['id'] . '&more=true'; ?>">
                                                <?php if ($category['image']) { ?>
                                                <span class="category-icon icon-<?php echo $category['alt']; ?>"></span>
                                                <?php } ?>
                                                <?php echo $category['name']; ?>
                                            </a>
                                        </li>
                                        <?php } ?>
                                    </ul>
                                    <?php } ?>
                               </li>
                               <li class="topcat">
            						<a href="<?php echo $post_link; ?>" class="button button_post bolder"><?php echo $heading_post; ?></a>
                               </li>
                        	</ul>
                        </div>
                    </nav>
                    <div id="search" class="hidden">
                        <div class="search-inner">
                            <span class="button-search"><i class="fa fa-search"></i></span>
                            <span class="button-search-close"><i class="fa fa-times-circle"></i></span>
                            <input type="text" name="search" placeholder="<?php echo $text_search; ?>" value="<?php echo $search; ?>" />
                        </div>
                    </div>
                    <div id="account-actions" class="<?php echo $logged ? 'logged-in' : 'logged-out'; ?>">
                        <?php if ($logged) { ?>
                        <a href="<?php echo $logout; ?>" class="button button_primary button_inverse"><i class="fa fa-sign-out" title="<?php echo $text_logout; ?>"></i><?php echo $text_logout; ?></a>
                        <a href="<?php echo $account; ?>" class="button button_primary"><i class="fa fa-user-circle" title="<?php echo $text_account; ?>"></i><?php echo $text_account; ?></a>
                        <?php } else { ?>
                        <a href="<?php echo $register; ?>" class="button button_primary button_inverse"><i class="fa fa-group" title="<?php echo $text_register; ?>"></i><?php echo $text_register; ?></a>
                        <a href="<?php echo $login; ?>" class="button button_primary"><i class="fa fa-sign-in" title="<?php echo $text_login; ?>"></i><?php echo $text_login; ?></a>
                        <?php } ?>
                    </div>
                    <div id="responsive-menu">
        				<div class="responsive-menu-inner">
        					<div class="left">
                                <a id="mobile-menu" class="button"><i class="fa fa-bars"></i><?php echo $button_menu; ?></a>
                            </div>
        				</div>
        			</div>
                </div>
            </div>
        </div>
    </div>
</header>
