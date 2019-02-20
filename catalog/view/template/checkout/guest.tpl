<div class="widget">
    <h6><?php echo $text_your_details; ?></h6>
    <div class="content">
    	<div id="show-button-guest"></div>
        <div class="formboxcolumn">
            <div class="formbox">
                <p class="form-label"><strong><?php echo $entry_firstname; ?></strong></p>
                <input type="text" name="firstname" value="" />
            </div>
            <div class="formbox">
                <p class="form-label"><strong><?php echo $entry_lastname; ?></strong></p>
                <input type="text" name="lastname" value="" />
            </div>
            <div class="formbox">
                <p class="form-label"><strong><?php echo $entry_email; ?></strong></p>
                <input type="text" name="email" value="" />
            </div>
        </div><div class="formboxcolumn">
            <div class="formbox">
                <p class="form-label"><strong><?php echo $entry_telephone; ?></strong></p>
                <input type="text" name="telephone" value="" />
                <span class="help"><?php echo $help_phone; ?></span>
            </div>
            <div class="formbox hidden">
                <p class="form-label"><strong><?php echo $entry_fax; ?></strong></p>
                <input type="text" name="fax" value="" />
            </div>
        </div>
    </div>
    <h6><?php echo $text_your_address; ?></h6>
    <div class="content">
        <div class="formboxcolumn">
            <div class="formbox">
                <p><strong><?php echo $entry_company; ?></strong></p>
                <input type="text" name="company" value="" />
            </div>
            <div class="formbox" style="display: <?php echo (count($customer_groups) > 1 ? 'inline-block' : 'none'); ?>;">
                <p><strong><?php echo $entry_customer_group; ?></strong></p>
                <?php foreach ($customer_groups as $customer_group) { ?>
                <?php if ($customer_group['customer_group_id'] == $customer_group_id) { ?>
                <span class="label">
                    <input type="radio" name="customer_group_id" value="<?php echo $customer_group['customer_group_id']; ?>" id="customer_group_id<?php echo $customer_group['customer_group_id']; ?>" checked="checked" />
                    <label for="customer_group_id<?php echo $customer_group['customer_group_id']; ?>"><?php echo $customer_group['name']; ?></label>
                </span>
                <?php } else { ?>
                <span class="label">
                    <input type="radio" name="customer_group_id" value="<?php echo $customer_group['customer_group_id']; ?>" id="customer_group_id<?php echo $customer_group['customer_group_id']; ?>" />
                    <label for="customer_group_id<?php echo $customer_group['customer_group_id']; ?>"><?php echo $customer_group['name']; ?></label>
                </span>
                <?php } ?>
                <?php } ?>
            </div>
            <div id="company-id-display" class="formbox">
                <p><span id="company-id-required" class="required">*</span> <strong><?php echo $entry_company_id; ?></strong></p>
                <input type="text" name="company_id" value="" />
            </div>
            <div id="tax-id-display" class="formbox">
                <p><span id="tax-id-required" class="required">*</span> <strong><?php echo $entry_tax_id; ?></strong></p>
                <input type="text" name="tax_id" value="" />
            </div>
            <div class="formbox">
                <p><strong><?php echo $entry_address_1; ?></strong></p>
                <input type="text" name="address_1" value="" />
            </div>
            <div class="formbox">
                <p><strong><?php echo $entry_address_2; ?></strong></p>
                <input type="text" name="address_2" value="" />
            </div>
        </div><div class="formboxcolumn">
            <div class="formbox">
                <p><strong><?php echo $entry_city; ?></strong></p>
                <input type="text" name="city" value="" />
            </div>
            <div class="formbox">
                <p><span id="payment-postcode-required" class="required">*</span> <strong><?php echo $entry_postcode; ?></strong></p>
                <input type="text" name="postcode" value="" />
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
    <?php if ($shipping_required) { ?>
    <div class="content content-checkbox">
        <div class="formbox">
            <span class="label"><input type="checkbox" name="shipping_address" value="1" id="shipping" checked="checked" /><label for="shipping"><?php echo $entry_shipping; ?></label></span>
        </div>
    </div>
    <?php } ?>
    <div class="buttons">
        <div class="center">
            <input type="button" value="<?php echo $button_continue; ?>" id="button-guest" class="button fullwidth bigger" />
        </div>
    </div>
</div>
<script type="text/javascript"><!--
var customer_group = [];

<?php foreach ($customer_groups as $customer_group) { ?>
customer_group[<?php echo $customer_group['customer_group_id']; ?>] = [];
customer_group[<?php echo $customer_group['customer_group_id']; ?>]['company_id_display'] = '<?php echo $customer_group['company_id_display']; ?>';
customer_group[<?php echo $customer_group['customer_group_id']; ?>]['company_id_required'] = '<?php echo $customer_group['company_id_required']; ?>';
customer_group[<?php echo $customer_group['customer_group_id']; ?>]['tax_id_display'] = '<?php echo $customer_group['tax_id_display']; ?>';
customer_group[<?php echo $customer_group['customer_group_id']; ?>]['tax_id_required'] = '<?php echo $customer_group['tax_id_required']; ?>';
<?php } ?>
//--></script>
