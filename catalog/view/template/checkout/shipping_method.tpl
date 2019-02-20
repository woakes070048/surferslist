<div class="layout-left-minus-right">
<div id="checkout-shipping-method-form" class="container-left">
<div class="widget">
	<h6><?php echo $text_checkout_shipping_method; ?></h6>
    <div class="content">
        <div id="show-button-shipping-method"></div>
        <?php if ($error_warning) { ?>
        <div class="warning">
            <p><?php echo $error_warning; ?></p>
            <span class="icon"><i class="fa fa-exclamation-triangle"></i></span>
        </div>
        <?php } ?>
        <?php if ($shipping_methods) { ?>
        <p><?php echo $text_shipping_method; ?></p>
        <?php foreach ($shipping_methods as $shipping_method) { ?>
        <div class="formbox">
            <p><b><?php echo $shipping_method['title']; ?></b></p>
            <?php if (!$shipping_method['error']) { ?>
                <?php foreach ($shipping_method['quote'] as $quote) { ?>
                <span class="label">
                    <?php if ($quote['code'] == $code || !$code) { ?>
                    <?php $code = $quote['code']; ?>
                    <input type="radio" name="shipping_method" value="<?php echo $quote['code']; ?>" id="<?php echo $quote['code']; ?>" checked="checked" />
                    <?php } else { ?>
                    <input type="radio" name="shipping_method" value="<?php echo $quote['code']; ?>" id="<?php echo $quote['code']; ?>" />
                    <?php } ?>
                    <label for="<?php echo $quote['code']; ?>"><?php echo $quote['title']; ?></label>
                    <label class="labelprice" for="<?php echo $quote['code']; ?>"><?php echo $quote['text']; ?></label>
                </span>
                <?php } ?>
            <?php } else { ?>
                <span class="error"><i class="fa fa-exclamation-triangle"></i><?php echo $shipping_method['error']; ?></span>
            <?php } ?>
        </div>
        <?php } ?>
        <?php } ?>
		<?php if ($this->config->get('insurance_status')) { ?>
        <div class="formbox">
			<p><b><?php echo $text_insurance; ?></b></p>
			<span class="label">
				<input type="checkbox" name="insurance" id="insurance" value="1" <?php echo $insurance ? 'checked="checked"' : '' ?>/>
				<input type="hidden" name="shipping_insurance" value="<?php if (isset($insurance_value)) { ?><?php echo $insurance_value; ?><?php } ?>" />
				<label for="insurance"><?php echo $entry_insurance; ?></label>
				<label class="labelprice" for="insurance"><?php if (isset($insurance_fee)) { ?><?php echo $insurance_fee; ?><?php } ?></label>
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
            <a id="button-shipping-method" class="button fullwidth bigger"><i class="fa fa-arrow-down"></i><?php echo $button_next; ?></a>
        </div>
    </div>
</div>
</div>
</div>
