<?php echo $header; ?>
<div class="container-page">
    <div class="breadcrumb">
        <div class="layout">
            <h1><?php echo $heading_title; ?></h1>
            <div class="links">
                <?php foreach ($breadcrumbs as $breadcrumb) { ?>
                <?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
                <?php } ?>
            </div>
        </div>
    </div>
    <div class="layout layout-left-plus-right">
        <div class="container-center">
            <div class="content-page">
                <?php echo $content_top; ?>
                <div class="checkout">
                    <div id="checkout">
                        <div class="checkout-heading"><?php echo $text_checkout_option; ?></div>
                        <div class="checkout-content"></div>
                    </div>
                    <?php if (!$logged) { ?>
                    <div id="payment-address">
                        <div class="checkout-heading"><span><?php echo $text_checkout_account; ?></span></div>
                        <div class="checkout-content"></div>
                    </div>
                    <?php } else { ?>
                    <div id="payment-address">
                        <div class="checkout-heading"><span><?php echo $text_checkout_payment_address; ?></span></div>
                        <div class="checkout-content"></div>
                    </div>
                    <?php } ?>
                    <?php if ($shipping_required) { ?>
                    <div id="shipping-address">
                        <div class="checkout-heading"><?php echo $text_checkout_shipping_address; ?></div>
                        <div class="checkout-content"></div>
                    </div>
                    <div id="shipping-method">
                        <div class="checkout-heading"><?php echo $text_checkout_shipping_method; ?></div>
                        <div class="checkout-content"></div>
                    </div>
                    <?php } ?>
                    <div id="payment-method">
                        <div class="checkout-heading"><?php echo $text_checkout_payment_method; ?></div>
                        <div class="checkout-content"></div>
                    </div>
                    <div id="confirm">
                        <div class="checkout-heading"><?php echo $text_checkout_confirm; ?></div>
                        <div class="checkout-content"></div>
                    </div>
                </div>
                <?php echo $content_bottom; ?>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript"><!--
var textModify = '<?php echo $text_modify; ?>';
var textSelect = '<?php echo $text_select; ?>';
var textSelectZone = '<?php echo $text_select_zone; ?>';
var textSelectCountry = '<?php echo $text_select_country; ?>';
var textNone = '<?php echo $text_none; ?>';
var textCheckoutPaymentAddress = '<?php echo $text_checkout_payment_address; ?>';
var shippingRequired = <?php echo $shipping_required ? 'true' : 'false'; ?>;
<?php if (false && isset($quickConfirm) && $quickConfirm == true) {  // (quickConfirm temp disabled) ?>
var quickConfirm = true;
<?php } ?>
<?php if (!$logged) { ?>
var logged = false;
<?php } else { ?>
var logged = true;
<?php } ?>
//--></script>
<?php echo $footer; ?>
