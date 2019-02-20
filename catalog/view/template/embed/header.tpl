<!DOCTYPE html>
<html dir="<?php echo $direction; ?>" lang="<?php echo $lang; ?>" class="no-js">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1, user-scalable=no">
<title><?php echo $title; ?></title>
<base href="<?php echo $server; ?>" />
<?php if ($description) { ?>
<meta name="description" content="<?php echo $description; ?>" />
<?php } ?>
<?php if ($keywords) { ?>
<meta name="keywords" content="<?php echo $keywords; ?>" />
<?php } ?>
<?php foreach ($links as $link) { ?>
<link href="<?php echo $link['href']; ?>" rel="<?php echo $link['rel']; ?>" />
<?php } ?>
<!-- CSS -->
<link rel="stylesheet" type="text/css" href="<?php echo $server . 'catalog/view/' . $css_min; ?>" media="all" />
<?php foreach ($styles as $style) { ?>
<link rel="<?php echo $style['rel']; ?>" type="text/css" href="<?php echo $server_cdn . $style['href']; ?>" media="<?php echo $style['media']; ?>" />
<?php } ?>
<style>
<?php if ($customcolor) { ?>
/* CUSTOM COLORS ACTIVATED */
/* Primary Color: #<?php echo $color_primary; ?> */
/* Secondary Color: #<?php echo $color_secondary; ?> */
/* Featured Color: #<?php echo $color_featured; ?> */
/* Special Color: #<?php echo $color_special; ?> */

a {
    color: #<?php echo $color_primary; ?>;
}

.button:not(.button_alt):not(.button_quickview) {
    background-color: #<?php echo $color_secondary; ?> !important;
}

#button-filter:hover {
    background-color: #<?php echo $color_featured; ?> !important;
}

.container-page .grid-item:hover .pannel .info,
.container-page .grid-item:hover .pannel:before,
.container-page .grid-item:hover .image,
.grid-item .add-to a:hover {
    background-color: #<?php echo $color_primary; ?>;
}

.container-page:not(.featured-page) .grid-item-promo:not(.module-item) .pannel,
.container-page:not(.featured-page) .grid-item-promo:not(.module-item) .pannel .info,
.container-page:not(.featured-page) .grid-item-promo:not(.module-item) .pannel:before,
.container-page:not(.featured-page) .grid-item-promo:not(.module-item) .image,
.container-page.featured-page .grid-item:hover .pannel .info,
.container-page.featured-page .grid-item:hover .pannel:before,
.container-page.featured-page .grid-item:hover .image,
.container-page .grid-item.grid-item-promo:hover .pannel .info,
.container-page .grid-item.grid-item-promo:hover .pannel:before,
.container-page .grid-item.grid-item-promo:hover .image,
.grid-item-promo .add-to a:hover {
    background-color: #<?php echo $color_featured; ?>;
}

.container-page .grid-item-sale:hover .pannel .info,
.container-page .grid-item-sale:hover .pannel:before,
.container-page .grid-item-sale:hover .image,
.grid-item-sale .add-to a:hover {
    background-color: #<?php echo $color_special; ?>;
}

.container-page .grid-item .quickview > .button:hover {
    background-color: #<?php echo $color_primary; ?>;
    border-color: #<?php echo $color_primary; ?>;
}

.container-page .grid-item-promo .quickview > .button:hover {
    background-color: #<?php echo $color_featured; ?>;
    border-color: #<?php echo $color_featured; ?>;
}

.container-page .grid-item-sale .quickview > .button:hover {
    background-color: #<?php echo $color_special; ?>;
    border-color: #<?php echo $color_special; ?>;
}

.grid-item .sale-badges {
    background-color: #<?php echo $color_special; ?>;
}

.container-page .grid-item-sale:hover .sale-badges {
    color: #<?php echo $color_special; ?>;
}

.pagination .links b {
    background-color: #<?php echo $color_secondary; ?>;
}

.pagination .links a:hover, .footer .top:hover {
    background-color: #<?php echo $color_primary; ?>;
}

.formbox .label label:hover, .formbox .label-checked label {
    color: #<?php echo $color_primary; ?>;
}

.icheckbox_minimal-custom,
.iradio_minimal-custom {
    background: url(catalog/view/root/icheck/skins/minimal/grey.png) no-repeat;
}
<?php } ?>
body {
    background-color: #f3f6f6;
}
#page-header {
    padding-top: 0;
}
.footer .top {
    bottom: 0;
}
</style>
<!-- Font Awesome -->
<script src="https://use.fontawesome.com/08241cf910.js"></script>
<!-- Google Analytics -->
<?php echo $google_analytics; ?>
</head>
<body class="embed">

<header id="page-header">
    <div class="container-header-top"></div>
    <div class="container-header"></div>
</header>
