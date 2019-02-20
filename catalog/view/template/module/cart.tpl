<div id="cart" class="hbox">
	<div class="heading">
        <span class="span-icon"><i class="fa fa-shopping-cart<?php if ($products || $vouchers) { ?> highlight<?php } ?>" title="<?php echo $text_items; ?>"></i><?php echo $heading_title; ?></span>
    </div>
    <div class="content">
        <div class="toptitle">
            <h6><?php echo $heading_title; ?></h6>
            <h5 id="cart-total"><?php echo $text_items; ?></h5>
        </div>
        <div class="shopping-cart">
            <?php if ($products || $vouchers) { ?>
        	<?php foreach ($products as $product) { ?>
            <div class="itemcart">
                <div class="image">
                	<div class="position">
                        <?php if ($product['thumb']) { ?>
                        <a href="<?php echo $product['href']; ?>"><img src="<?php echo $product['thumb']; ?>" alt="<?php echo $product['name']; ?>" /></a>
                        <?php } ?>
                        <?php if ($product['available'] > 1) { ?>
                        <span class="quantity"><?php echo $product['quantity']; ?>x</span>
                        <?php } ?>
                    </div>
                </div>
                <div class="name">
                	<p><a href="<?php echo $product['href']; ?>"><?php echo $product['name']; ?></a></p>
                    <?php foreach ($product['option'] as $option) { ?>
                        <span>- <?php echo $option['name']; ?> <?php echo $option['value']; ?></span>
                    <?php } ?>
                    <?php if (!empty($product['recurring'])): ?>
                    	<span>- <?php echo $text_payment_profile ?> <?php echo $product['profile']; ?></span>
                    <?php endif; ?>
                </div>
                <div class="total">
                	<div class="position">
                        <?php echo $product['total']; ?>
                        <a data-cart-key="<?php echo $product['key']; ?>" title="<?php echo $button_remove; ?>" class="cart-remove"><i class="fa fa-times"></i></a>
                    </div>
                </div>
            </div>
            <?php } ?>
            <?php foreach ($vouchers as $voucher) { ?>
            <div class="itemcart">
                <div class="image">
                	<div class="position">
                        <img src="catalog/view/image/cart/vouchercart-70x70.jpg" alt="" />
                        <span class="quantity">1x</span>
                    </div>
                </div>
                <div class="name">
                	<p><?php echo $voucher['description']; ?></p>
                </div>
                <div class="total">
                	<div class="position">
                        <?php echo $voucher['amount']; ?>
                        <a data-cart-key="<?php echo $voucher['key']; ?>" title="<?php echo $button_remove; ?>" class="cart-remove"><i class="fa fa-times"></i></a>
                    </div>
                </div>
            </div>
            <?php } ?>
            <div class="mini-cart-total">
                <table>
                    <?php foreach ($totals as $total) { ?>
                    <tr>
                        <td class="totaltitle"><?php echo $total['title']; ?>:</td>
                        <td class="totaltext"><?php echo $total['text']; ?></td>
                    </tr>
                    <?php } ?>
                </table>
            </div>
            <div class="buttons">
            	<div class="left">
                    <a href="<?php echo $cart; ?>" class="button"><i class="fa fa-shopping-cart"></i><?php echo $text_cart; ?></a>
                </div>
            	<div class="right">
                    <a href="<?php echo $checkout; ?>" class="button button_highlight"><i class="fa fa-credit-card"></i><?php echo $text_checkout; ?></a>
                </div>
            </div>
            <?php } else { ?>
            <p class="empty"><?php echo $text_empty; ?></p>
            <?php } ?>
            <?php if ($cart_info) { ?>
            <div class="cart-info">
                <img class="image" src="<?php echo $cart_info['icon']; ?>" alt="" />
                <div>
                    <p class="line1"><?php echo $cart_info['line1']; ?></p>
					<p class="line2"><?php echo $cart_info['line2']; ?></p>
					<p class="line3"><?php echo $cart_info['line3']; ?></p>
                </div>
            </div>
            <?php } ?>
        </div>
    </div>
</div>
