<?php echo $header; ?>
<div class="container-page cart-page">
    <div class="breadcrumb">
        <div class="layout">
            <h1><a href="<?php echo $action; ?>"><?php echo $heading_title; ?><?php if ($weight) { ?> / <?php echo $weight; ?><?php } ?></a></h1>
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
                <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data">
                <div class="global-page">
                    <?php if ($attention) { ?>
                    <div class="attention">
                        <p><?php echo $attention; ?></p>
                        <span class="close"><i class="fa fa-times"></i></span>
                        <span class="icon"><i class="fa fa-bullhorn"></i></span>
                    </div>
                    <?php } ?>
                    <?php if ($success) { ?>
                    <div class="success">
                        <p><?php echo $success; ?></p>
                        <span class="close"><i class="fa fa-times"></i></span>
                        <span class="icon"><i class="fa fa-check"></i></span>
                    </div>
                    <?php } ?>
                    <?php if ($error_warning) { ?>
                    <div class="warning">
                        <p><?php echo $error_warning; ?></p>
                        <span class="close"><i class="fa fa-times"></i></span>
                        <span class="icon"><i class="fa fa-exclamation-triangle"></i></span>
                    </div>
                    <?php } ?>
                    <div class="shopping-cart shopping-cart-page">
                        <?php foreach ($products as $product) { ?>
                        <?php if(!empty($product['recurring'])): ?>
                            <div class="recurring"><i class="fa fa-repeat recurring_icon"></i> <strong><?php echo $text_recurring_item ?></strong> <?php echo $product['profile_description'] ?></div>
                        <?php endif; ?>
                        <div class="itemcart">
                            <div class="image">
                                <div class="position">
                                	<a class="remove" href="<?php echo $product['remove']; ?>" rel="tooltip" data-placement="top" data-original-title="<?php echo $button_remove; ?>"><i class="fa fa-trash"></i></a>
                                    <?php if ($product['thumb']) { ?>
                                    <a href="<?php echo $product['href']; ?>"><img src="<?php echo $product['thumb']; ?>" alt="<?php echo $product['name']; ?>" /></a>
                                    <?php } ?>
                                </div>
                            </div>
                            <div class="name">
                                <p><a href="<?php echo $product['href']; ?>"><?php echo $product['name']; ?></a><?php if (!$product['stock']) { ?> <b class="stock">***</b><?php } ?></p>
                                <span>- <?php echo $column_model; ?>: <?php echo $product['model']; ?></span>
								<?php if ($product['manufacturer']) { ?>
								<span>- <?php echo $column_manufacturer; ?>: <a href="<?php echo $product['manufacturer_href']; ?>"><?php echo $product['manufacturer']; ?></a></span>
								<?php } ?>
								<?php if ($product['member']) { ?>
								<span>- <?php echo $column_member; ?>: <a href="<?php echo $product['member_href']; ?>"><?php echo $product['member']; ?></a></span>
								<?php } ?>
                                <?php if (false && $product['reward']) { ?>
                                	<span>- <?php echo $product['reward']; ?></span>
                                <?php } ?>
                                <?php foreach ($product['option'] as $option) { ?>
                                    <span>- <?php echo $option['name']; ?> <?php echo $option['value']; ?></span>
                                <?php } ?>
                                <?php if(!empty($product['recurring'])): ?>
                                    <span>- <?php echo $text_payment_profile ?>: <?php echo $product['profile_name'] ?></span>
                                <?php endif; ?>
                            </div>
                            <?php if ($product['available'] > 1) { ?>
                            <div class="edit-quantity">
                                <input type="text" class="input-quantity" name="quantity[<?php echo $product['key']; ?>]" value="<?php echo $product['quantity']; ?>" />
                                <span class="image-edit">
                                	<i class="fa fa-refresh" rel="tooltip" data-placement="top" data-original-title="<?php echo $button_update; ?>"></i>
                                    <input type="submit" class="image-update" />
                                </span>
                            </div>
                            <div class="total">
                                <?php echo $product['total']; ?><br />
                                <span><?php echo $column_price; ?>: <?php echo $product['price']; ?></span>
                            </div>
                            <?php } else { ?>
							<div class="edit-quantity">
								<input type="hidden" name="quantity[<?php echo $product['key']; ?>]" value="<?php echo $product['quantity']; ?>" />
							</div>
                            <div class="total">
                                <?php echo $product['total']; ?>
                            </div>
							<?php } ?>
                        </div>
                        <?php } ?>
                        <?php foreach ($vouchers as $vouchers) { ?>
                        <div class="itemcart">
                            <div class="image">
                                <div class="position">
                                	<a class="remove" href="<?php echo $vouchers['remove']; ?>" rel="tooltip" data-placement="top" data-original-title="<?php echo $button_remove; ?>"><i class="fa fa-trash"></i></a>
                                    <img src="catalog/view/image/cart/vouchercart-100x100.jpg" alt="" />
                                </div>
                            </div>
                            <div class="name">
                                <p><?php echo $vouchers['description']; ?></p>
                            </div>
                            <div class="edit-quantity">
                            	<input type="text" class="input-quantity input-disabled" name="" value="1" disabled="disabled" />
                            </div>
                            <div class="total">
                                <?php echo $vouchers['amount']; ?>
                            </div>
                        </div>
                        <?php } ?>
                    </div>
                </div>
                </form>
                <?php if ($coupon_status || $voucher_status || $reward_status || $shipping_status) { ?>
                <div class="global-page-left">
                    <div class="global-page">
                        <h6><?php echo $text_next; ?></h6>
                        <p class="hidden"><?php echo $text_next_choice; ?></p>
                        <div class="formbox">
                            <?php if ($coupon_status) { ?>
                                <span class="label">
                                    <?php if ($next == 'coupon') { ?>
                                    <input type="radio" name="next" value="coupon" id="use_coupon" checked="checked" />
                                    <?php } else { ?>
                                    <input type="radio" name="next" value="coupon" id="use_coupon" />
                                    <?php } ?>
                                    <label for="use_coupon"><?php echo $text_use_coupon; ?></label>
                                </span>
                            <?php } ?>
                            <?php if ($voucher_status) { ?>
                                <span class="label">
                                    <?php if ($next == 'voucher') { ?>
                                    <input type="radio" name="next" value="voucher" id="use_voucher" checked="checked" />
                                    <?php } else { ?>
                                    <input type="radio" name="next" value="voucher" id="use_voucher" />
                                    <?php } ?>
                                    <label for="use_voucher"><?php echo $text_use_voucher; ?></label>
                                </span>
                            <?php } ?>
                            <?php if ($reward_status) { ?>
                                <span class="label">
                                    <?php if ($next == 'reward') { ?>
                                    <input type="radio" name="next" value="reward" id="use_reward" checked="checked" />
                                    <?php } else { ?>
                                    <input type="radio" name="next" value="reward" id="use_reward" />
                                    <?php } ?>
                                    <label for="use_reward"><?php echo $text_use_reward; ?></label>
                                </span>
                            <?php } ?>
                            <?php if ($shipping_status) { ?>
                                <span class="label">
                                    <?php if ($next == 'shipping') { ?>
                                    <input type="radio" name="next" value="shipping" id="shipping_estimate" checked="checked" />
                                    <?php } else { ?>
                                    <input type="radio" name="next" value="shipping" id="shipping_estimate" />
                                    <?php } ?>
                                    <label for="shipping_estimate"><?php echo $text_shipping_estimate; ?></label>
                                </span>
                            <?php } ?>
                        </div>
                    </div>
                </div>
                <?php } ?>
                <div class="global-page-right">
                    <?php if ($coupon_status || $voucher_status || $reward_status || $shipping_status) { ?>
                    <div class="cart-module"> <!-- start cart module -->
                        <div id="coupon" style="display: <?php echo ($next == 'coupon' ? 'block' : 'none'); ?>;">
                            <div class="widget">
                                <h6><?php echo $entry_coupon; ?></h6>
                                <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data">
                                <div class="content">
                                    <div class="formbox">
                                        <input type="text" name="coupon" value="<?php echo $coupon; ?>" />
                                        <input type="hidden" name="next" value="coupon" />
                                    </div>
                                </div>
                                <div class="buttons">
                                    <div class="left">
                                        <input type="submit" value="<?php echo $button_coupon; ?>" class="button" />
                                    </div>
                                </div>
                                </form>
                            </div>
                        </div>
                        <div id="voucher" style="display: <?php echo ($next == 'voucher' ? 'block' : 'none'); ?>;">
                            <div class="widget">
                                <h6><?php echo $entry_voucher; ?></h6>
                                <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data">
                                <div class="content">
                                    <div class="formbox">
                                        <input type="text" name="voucher" value="<?php echo $voucher; ?>" />
                                        <input type="hidden" name="next" value="voucher" />
                                    </div>
                                </div>
                                <div class="buttons">
                                    <div class="left">
                                        <input type="submit" value="<?php echo $button_voucher; ?>" class="button" />
                                    </div>
                                </div>
                                </form>
                            </div>
                        </div>
                        <div id="reward" style="display: <?php echo ($next == 'reward' ? 'block' : 'none'); ?>;">
                            <div class="widget">
                                <h6><?php echo $entry_reward; ?></h6>
                                <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data">
                                <div class="content">
                                    <div class="formbox">
                                        <input type="text" name="reward" value="<?php echo $reward; ?>" />
                                        <input type="hidden" name="next" value="reward" />
                                    </div>
                                </div>
                                <div class="buttons">
                                    <div class="left">
                                        <input type="submit" value="<?php echo $button_reward; ?>" class="button" />
                                    </div>
                                </div>
                                </form>
                            </div>
                        </div>
                        <div id="shipping" style="display: <?php echo ($next == 'shipping' ? 'block' : 'none'); ?>;">
                            <div class="widget">
                                <h6><?php echo $text_shipping_detail; ?></h6>
                                <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data">
                                <div class="content">
                                    <div class="formbox">
                                        <p><span class="required">*</span> <strong><?php echo $entry_country; ?></strong></p>
                                        <select name="country_id">
                                            <option value=""><?php echo $text_select_country; ?></option>
                                            <?php foreach ($countries as $country) { ?>
                                            <?php if ($country['country_id'] == $country_id) { ?>
                                            <option value="<?php echo $country['country_id']; ?>" selected="selected"><?php echo $country['name']; ?></option>
                                            <?php } else { ?>
                                            <option value="<?php echo $country['country_id']; ?>"><?php echo $country['name']; ?></option>
                                            <?php } ?>
                                            <?php } ?>
                                        </select>
                                    </div><div class="formbox">
                                        <p><span class="required">*</span> <strong><?php echo $entry_zone; ?></strong></p>
                                        <select name="zone_id">
                        					<?php if ($zones) { ?>
                        	                <option value=""><?php echo $text_select_zone; ?></option>
                        	                <?php foreach ($zones as $zone) { ?>
                        	                <?php if ($zone['zone_id'] == $zone_id) { ?>
                        	                <option value="<?php echo $zone['zone_id']; ?>" selected="selected"><?php echo $zone['name']; ?></option>
                        	                <?php } else { ?>
                        	                <option value="<?php echo $zone['zone_id']; ?>"><?php echo $zone['name']; ?></option>
                        	                <?php } ?>
                        	                <?php } ?>
                        	                <?php } ?>
                                        </select>
                                    </div><div class="formbox">
                                        <p><span id="postcode-required" class="required">*</span> <strong><?php echo $entry_postcode; ?></strong></p>
                                        <input type="text" name="postcode" value="<?php echo $postcode; ?>" />
                                    </div>
                                </div>
                                <div class="buttons">
                                    <div class="left">
                                        <input id="button-quote" type="submit" value="<?php echo $button_quote; ?>" class="button hidden" />
                                        <label for="button-quote" class="button"><i class="fa fa-plane"></i> <?php echo $button_quote; ?></label>
                                    </div>
                                </div>
                                </form>
                            </div>
                        </div>
                    </div> <!-- end cart module -->
                    <?php } ?>
                    <div class="widget">
                        <div class="shopping-cart shopping-cart-page">
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
                        <div class="buttons">
                            <div class="left">
								<a href="<?php echo $continue; ?>" class="button button_home"><i class="fa fa-th-list"></i><?php echo $button_shopping; ?></a>
								<a href="<?php echo $search; ?>" class="button button_alt button_search"><i class="fa fa-search"></i><?php echo $button_search; ?></a>
							</div>
                            <div class="right">
								<a href="<?php echo $checkout; ?>" class="button button_highlight"><i class="fa fa-credit-card"></i><?php echo $button_checkout; ?></a>
							</div>
                        </div>
                    </div>
                </div>
                <?php echo $content_bottom; ?>
            </div>
        </div>
        <?php echo $column_right; ?>
    </div>
</div>
<script type="text/javascript"><!--
var submitShippingUrl = '<?php echo $action; ?>';
var shippingMethod = '<?php echo $shipping_method; ?>';
var textShippingMethod = '<?php echo $text_shipping_method; ?>';
var buttonShipping = '<?php echo $button_shipping; ?>';
var textSelect = '<?php echo $text_select; ?>';
var textNone = '<?php echo $text_none; ?>';
var zoneId = '<?php echo $zone_id; ?>';
//--></script>
<?php echo $footer; ?>
