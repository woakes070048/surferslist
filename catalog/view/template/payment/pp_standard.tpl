<?php if ($testmode) { ?>
<div class="content">
	<div class="warning">
		<p><?php echo $text_testmode; ?></p>
		<span class="close"><i class="fa fa-times"></i></span>
		<span class="icon"><i class="fa fa-exclamation-triangle"></i></span>
	</div>
</div>
<?php } ?>
<form action="<?php echo $action; ?>" method="post">
  <input type="hidden" name="cmd" value="_cart" />
  <input type="hidden" name="upload" value="1" />
  <input type="hidden" name="business" value="<?php echo $business; ?>" />
  <?php $i = 1; ?>
  <?php foreach ($products as $product) { ?>
  <input type="hidden" name="item_name_<?php echo $i; ?>" value="<?php echo $product['name']; ?>" />
  <input type="hidden" name="item_number_<?php echo $i; ?>" value="<?php echo $product['model']; ?>" />
  <input type="hidden" name="amount_<?php echo $i; ?>" value="<?php echo $product['price']; ?>" />
  <?php if ($product['shipping']) { ?>
  <input type="hidden" name="shipping_<?php echo $i; ?>" value="<?php echo $product['shipping']; ?>" />
  <input type="hidden" name="shipping2_<?php echo $i; ?>" value="<?php echo $product['shipping']; ?>" />
  <?php } ?>
  <?php if ($product['commission']) { ?>
  <input type="hidden" name="handling_<?php echo $i; ?>" value="<?php echo $product['commission']; ?>" />
  <?php } ?>
  <input type="hidden" name="quantity_<?php echo $i; ?>" value="<?php echo $product['quantity']; ?>" />
  <input type="hidden" name="weight_<?php echo $i; ?>" value="<?php echo $product['weight']; ?>" />
  <?php $j = 0; ?>
  <?php foreach ($product['option'] as $option) { ?>
  <input type="hidden" name="on<?php echo $j; ?>_<?php echo $i; ?>" value="<?php echo $option['name']; ?>" />
  <input type="hidden" name="os<?php echo $j; ?>_<?php echo $i; ?>" value="<?php echo $option['value']; ?>" />
  <?php $j++; ?>
  <?php } ?>
  <?php $i++; ?>
  <?php } ?>
  <?php if ($discount_amount_cart) { ?>
  <input type="hidden" name="discount_amount_cart" value="<?php echo $discount_amount_cart; ?>" />
  <?php } ?>
  <input type="hidden" name="currency_code" value="<?php echo $currency_code; ?>" />
  <input type="hidden" name="first_name" value="<?php echo $first_name; ?>" />
  <input type="hidden" name="last_name" value="<?php echo $last_name; ?>" />
  <input type="hidden" name="address1" value="<?php echo $address1; ?>" />
  <input type="hidden" name="address2" value="<?php echo $address2; ?>" />
  <input type="hidden" name="city" value="<?php echo $city; ?>" />
  <input type="hidden" name="state" value="<?php echo $state; ?>" />
  <input type="hidden" name="zip" value="<?php echo $zip; ?>" />
  <input type="hidden" name="country" value="<?php echo $country; ?>" />
  <input type="hidden" name="address_override" value="1" /> <!-- use address entered instead of address stored with PayPal -->
  <!--<input type="hidden" name="reqconfirmshipping" value="1" />--> <!-- require buyers shipping address be a confirmed address -->
  <input type="hidden" name="no_shipping" value="<?php echo $no_shipping; ?>" /> <!-- hide shipping address fields on PayPal pages -->
  <input type="hidden" name="email" value="<?php echo $email; ?>" />
  <input type="hidden" name="invoice" value="<?php echo $invoice; ?>" />
  <input type="hidden" name="lc" value="<?php echo $lc; ?>" />
  <input type="hidden" name="rm" value="2" />
  <input type="hidden" name="no_note" value="1" />
  <input type="hidden" name="charset" value="utf-8" />
  <input type="hidden" name="return" value="<?php echo $return; ?>" />
  <input type="hidden" name="notify_url" value="<?php echo $notify_url; ?>" />
  <input type="hidden" name="cancel_return" value="<?php echo $cancel_return; ?>" />
  <input type="hidden" name="paymentaction" value="<?php echo $paymentaction; ?>" />
  <input type="hidden" name="custom" value="<?php echo $custom; ?>" />
  <div class="buttons">
    <div class="center">
      <input type="submit" value="<?php echo $button_confirm . ' &raquo;'; ?>" class="button fullwidth button_highlight bigger" style="margin-bottom:10px;" />
	  <p><span class="help"><?php echo $help_paypal; ?></span></p>
    </div>
  </div>
</form>
