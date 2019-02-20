<?php if (!isset($redirect)) { ?>
<div class="layout-left-minus-right">
<div id="checkout-confirm-form" class="container-left">
<div class="widget">
<h6><?php echo $text_checkout_confirm; ?></h6>
<div class="content checkout-product">
    <div class="shopping-cart shopping-cart-page">
        <?php foreach ($products as $product) { ?>  
            <?php if(!empty($product['recurring'])): ?>
                <div class="recurring"><i class="fa fa-repeat recurring_icon"></i> <strong><?php echo $text_recurring_item ?></strong> <?php echo $product['profile_description'] ?></div>
            <?php endif; ?>
            <div class="itemcart">
				<div class="image">
					<div class="position">
						<?php if ($product['thumb']) { ?>
						<a href="<?php echo $product['href']; ?>"><img src="<?php echo $product['thumb']; ?>" alt="<?php echo $product['name']; ?>" /></a>
						<?php } ?>
					</div>
				</div>
                <div class="name">
                    <p><a href="<?php echo $product['href']; ?>"><?php echo $product['name']; ?></a></p>
                    <span>- <?php echo $column_model; ?>: <?php echo $product['model']; ?></span>
                    <?php if ($product['manufacturer']) { ?>
					<span>- <?php echo $column_manufacturer; ?>: <a href="<?php echo $product['manufacturer_href']; ?>"><?php echo $product['manufacturer']; ?></a></span>
					<?php } ?>
                    <?php if ($product['member']) { ?>
					<span>- <?php echo $column_member; ?>: <a href="<?php echo $product['member_href']; ?>"><?php echo $product['member']; ?></a></span>
					<?php } ?>
                    <?php foreach ($product['option'] as $option) { ?>
                        <span>- <?php echo $option['name']; ?> <?php echo $option['value']; ?></span>
                    <?php } ?>
                    <?php if(!empty($product['recurring'])): ?>
                        <span>- <?php echo $text_payment_profile ?>: <?php echo $product['profile_name'] ?></span>
                    <?php endif; ?>
                </div>
                <?php if ($product['available'] > 1) { ?>
                <div class="edit-quantity"><input type="text" class="input-quantity input-disabled" name="" value="<?php echo $product['quantity']; ?>" disabled="disabled" /></div>
                <div class="total">
                    <?php echo $product['total']; ?><br />
                    <span><?php echo $column_price; ?>: <?php echo $product['price']; ?></span>
                </div>
                <?php } else { ?>
					<div class="edit-quantity hidden"></div>
					<div class="total">
						<?php echo $product['total']; ?>
					</div>
				 <?php } ?>
            </div>
        <?php } ?>
        
        <?php foreach ($vouchers as $voucher) { ?>
            <div class="itemcart">
				<div class="image">&nbsp;</div>
                <div class="name"><p><?php echo $voucher['description']; ?></p></div>
                <div class="edit-quantity"><input type="text" class="input-quantity input-disabled" name="" value="1" disabled="disabled" /></div>
                <div class="total"><?php echo $voucher['amount']; ?></div>
            </div>
        <?php } ?>
        <div class="mini-cart-total cart-total">
            <table>
                <?php foreach ($totals as $total) { ?>
                <tr>
                    <td class="totaltitle"><?php echo $total['title']; ?>:</td>
                    <td class="totaltext"><?php echo $total['text']; ?></td>
                </tr>
                <?php } ?>
            </table>
        </div>
    </div>
</div>
    <div class="payment"><?php echo $payment; ?></div>
</div>
</div>
</div>
<?php } else { ?>
<script type="text/javascript"><!--
location = '<?php echo $redirect; ?>';
//--></script> 
<?php } ?>