<div class="widget widget-something">
	<div class="content">
        <a href="<?php echo $payment_url; ?>" title="PayPal Express Checkout">
            <?php if($is_mobile == true) { ?>
                <img src="catalog/view/image/module/paypal_express_mobile.png" alt="PayPal Express Checkout" title="PayPal Express Checkout" />
            <?php }else{ ?>
                <img src="https://www.paypalobjects.com/en_GB/i/btn/btn_xpressCheckout.gif" alt="PayPal Express Checkout" />
            <?php } ?>
        </a>
    </div>
</div>
