<div class="layout-left-minus-right">
<div id="checkout-payment-method-form" class="container-left">
<div class="widget">
	<h6><?php echo $text_checkout_payment_method; ?></h6>
    <div class="content">
        <div id="show-button-payment-method"></div>
        <?php if ($error_warning) { ?>
        <div class="warning">
            <p><?php echo $error_warning; ?></p>
            <span class="icon"><i class="fa fa-exclamation-triangle"></i></span>
        </div>
        <?php } ?>
            <?php if ($payment_methods) { ?>
                <p><?php echo $text_payment_method; ?></p>
                <div class="formbox">
                    <?php foreach ($payment_methods as $payment_method) { ?>
                    <span class="label">
                    <?php if ($payment_method['code'] == $code || !$code) { ?>
                    <?php $code = $payment_method['code']; ?>
                    <input type="radio" name="payment_method" value="<?php echo $payment_method['code']; ?>" id="<?php echo $payment_method['code']; ?>" checked="checked" />
                    <?php } else { ?>
                    <input type="radio" name="payment_method" value="<?php echo $payment_method['code']; ?>" id="<?php echo $payment_method['code']; ?>" />
                    <?php } ?>
                    <label for="<?php echo $payment_method['code']; ?>"><?php echo $payment_method['title']; ?></label>
                    <?php } ?>
                    </span>
                </div>
            <?php } ?>
            <div class="formbox">
                <p><strong><?php echo $text_comments; ?></strong></p>
                <textarea name="comment"><?php echo $comment; ?></textarea>
            </div>
    </div>
    <div class="buttons">
        <div class="center">
            <a id="button-payment-method" class="button fullwidth bigger"><i class="fa fa-arrow-down"></i><?php echo $button_next; ?></a>
        </div>
    </div>
</div>
</div>
</div>
