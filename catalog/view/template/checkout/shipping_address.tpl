<div class="layout-left-minus-right">
<div id="checkout-shipping-address-form" class="container-left">
<div class="widget">
	<h6><?php echo $text_checkout_shipping_address; ?></h6>
    <div class="content">
    	<div id="show-button-shipping-address"></div>
        <?php if ($addresses) { ?>
            <div class="formbox">
            	<span class="label"><input type="radio" name="shipping_address" value="existing" id="shipping-address-existing" checked="checked" /><label for="shipping-address-existing"><?php echo $text_address_existing; ?></label></span>
            </div>
            <div id="shipping-existing">
                <div class="formbox">
                    <select name="address_id">
                        <?php foreach ($addresses as $address) { ?>
                        <?php if ($address['address_id'] == $address_id) { ?>
                        <option value="<?php echo $address['address_id']; ?>" selected="selected"><?php echo $address['firstname']; ?> <?php echo $address['lastname']; ?>, <?php echo $address['address_1']; ?>, <?php echo $address['city']; ?>, <?php echo $address['zone']; ?>, <?php echo $address['country']; ?></option>
                        <?php } else { ?>
                        <option value="<?php echo $address['address_id']; ?>"><?php echo $address['firstname']; ?> <?php echo $address['lastname']; ?>, <?php echo $address['address_1']; ?>, <?php echo $address['city']; ?>, <?php echo $address['zone']; ?>, <?php echo $address['country']; ?></option>
                        <?php } ?>
                        <?php } ?>
                    </select>
                </div>
            </div>
            <div class="formbox">
            	<span class="label"><input type="radio" name="shipping_address" value="new" id="shipping-address-new" /><label for="shipping-address-new"><?php echo $text_address_new; ?></label></span>
            </div>
        <?php } else { ?>
		<input type="hidden" name="shipping_address" value="new" />
		<?php } ?>
        	<div id="shipping-new" style="display: <?php echo ($addresses ? 'none' : 'block'); ?>;">
                    <div class="formbox" title="<?php echo $help_firstname; ?>" rel="tooltip">
                        <p class="form-label"><strong><?php echo $entry_firstname; ?></strong></p>
                        <input type="text" name="firstname" placeholder="<?php echo $entry_firstname; ?>" value="<?php echo $firstname; ?>" />
                    </div>
                    <div class="formbox" title="<?php echo $help_lastname; ?>" rel="tooltip">
                        <p class="form-label"><strong><?php echo $entry_lastname; ?></strong></p>
                        <input type="text" name="lastname" placeholder="<?php echo $entry_lastname; ?>" value="<?php echo $lastname; ?>" />
                    </div>
                    <div class="formbox hidden">
                        <p class="form-label"><strong><?php echo $entry_company; ?></strong></p>
                        <input type="text" name="company" placeholder="<?php echo $entry_company; ?>" value="" />
                    </div>
                    <div class="formbox" title="<?php echo $help_country; ?>" rel="tooltip">
                        <p class="form-label"><strong><?php echo $entry_country; ?></strong></p>
                        <select name="country_id">
                            <option value=""><?php echo $text_select; ?></option>
                            <?php foreach ($countries as $country) { ?>
                            <?php if ($country['country_id'] == $country_id) { ?>
                            <option value="<?php echo $country['country_id']; ?>" selected="selected"><?php echo $country['name']; ?></option>
                            <?php } else { ?>
                            <option value="<?php echo $country['country_id']; ?>"><?php echo $country['name']; ?></option>
                            <?php } ?>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="formbox" title="<?php echo $help_zone; ?>" rel="tooltip">
                        <p class="form-label"><strong><?php echo $entry_zone; ?></strong></p>
                        <select name="zone_id">
                        </select>
                    </div>
                    <div class="formbox" title="<?php echo $help_address_1; ?>" rel="tooltip">
                        <p class="form-label"><strong><?php echo $entry_address_1; ?></strong></p>
                        <input type="text" name="address_1" placeholder="<?php echo $entry_address_1; ?>" value="" />
                    </div>
                    <div class="formbox" title="<?php echo $help_address_2; ?>" rel="tooltip">
                        <p class="form-label"><strong><?php echo $entry_address_2; ?></strong></p>
                        <input type="text" name="address_2" placeholder="<?php echo $entry_address_2; ?>" value="" />
                    </div>
                    <div class="formbox" title="<?php echo $help_city; ?>" rel="tooltip">
                        <p class="form-label"><strong><?php echo $entry_city; ?></strong></p>
                        <input type="text" name="city" placeholder="<?php echo $entry_city; ?>" value="<?php echo $city; ?>" />
                    </div>
                    <div class="formbox" title="<?php echo $help_postcode; ?>" rel="tooltip">
                        <p class="form-label"><strong><?php echo $entry_postcode; ?></strong> <span id="shipping-postcode-required"></span></p>
                        <input type="text" name="postcode" placeholder="<?php echo $entry_postcode; ?>" value="" />
                    </div>
            </div>
    </div>
    <div class="buttons">
        <div class="center">
            <a id="button-shipping-address" class="button fullwidth bigger"><i class="fa fa-arrow-down"></i><?php echo $button_next; ?></a>
        </div>
    </div>
</div>
</div>
</div>
<script type="text/javascript"><!--
var textSelect = '<?php echo $text_select; ?>';
var textNone = '<?php echo $text_none; ?>';
var shippingZoneId = '<?php echo $zone_id; ?>';
//--></script>
