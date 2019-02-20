<div class="widget">
    <div class="content widget-no-title">
        <div id="show-button-guest-shipping"></div>
        <div class="formboxcolumn">
            <div class="formbox">
                <p><strong><?php echo $entry_firstname; ?></strong></p>
                <input type="text" name="firstname" value="<?php echo $firstname; ?>" />
            </div>
            <div class="formbox">
                <p><strong><?php echo $entry_lastname; ?></strong></p>
                <input type="text" name="lastname" value="<?php echo $lastname; ?>" />
            </div>
            <div class="formbox">
                <p><strong><?php echo $entry_company; ?></strong></p>
                <input type="text" name="company" value="<?php echo $company; ?>" />
            </div>
            <div class="formbox">
                <p><strong><?php echo $entry_address_1; ?></strong></p>
                <input type="text" name="address_1" value="<?php echo $address_1; ?>" />
            </div>
            <div class="formbox">
                <p><strong><?php echo $entry_address_2; ?></strong></p>
                <input type="text" name="address_2" value="<?php echo $address_2; ?>" />
            </div>
        </div><div class="formboxcolumn">
            <div class="formbox">
                <p><strong><?php echo $entry_city; ?></strong></p>
                <input type="text" name="city" value="<?php echo $city; ?>" />
            </div>
            <div class="formbox">
                <p><strong><?php echo $entry_postcode; ?></strong> <span id="shipping-postcode-required"></span></p>
                <input type="text" name="postcode" value="<?php echo $postcode; ?>" />
            </div>
            <div class="formbox">
                <p><strong><?php echo $entry_country; ?></strong></p>
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
            <div class="formbox">
                <p><strong><?php echo $entry_zone; ?></strong></p>
                <select name="zone_id">
                </select>
            </div>
        </div>
    </div>
    <div class="buttons">
        <div class="center">
            <input type="button" value="<?php echo $button_continue; ?>" id="button-guest-shipping" class="button fullwidth bigger" />
        </div>
    </div>
</div>
<script type="text/javascript"><!--
var textSelect = '<?php echo $text_select; ?>';
var textNone = '<?php echo $text_none; ?>';
var zoneId = '<?php echo $zone_id; ?>';
//--></script>
