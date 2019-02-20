<?php echo $header; ?>
<div class="container-page address-form-page">
    <div class="breadcrumb">
        <div class="layout">
            <h1><i class="fa fa-address-book"></i> <?php echo $heading_title; ?></h1>
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
            <div class="content-page my-account my-address">
            	<?php echo $notification; ?>
				<?php echo $content_top; ?>

                <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form" class="address-form">
                <div class="widget">

                	<h6><?php echo $text_address; ?></h6>

                    <div class="buttons">
                        <div class="left">
                            <input id="account-address-form-submit" type="submit" value="<?php echo $button_save; ?>" class="button hidden" />
                            <label for="account-address-form-submit" class="button button-submit button_save"><i class="fa fa-save"></i> <?php echo $button_save; ?></label>
                            <a href="<?php echo $back; ?>" class="button button_cancel"><i class="fa fa-ban"></i><?php echo $button_cancel; ?></a>
                        </div>
                    </div>

                	<div class="content">
                        <div class="formbox"<?php if ($hide_default) { ?>style="display:none;"<?php } ?>>
                            <p class="form-label form-label-inline">
                                <strong><span class="gold-text"><i class="fa fa-star"></i></span> <?php echo $entry_default; ?></strong>
                                <i class="fa fa-question-circle float-right grey-text" title="<?php echo $entry_default; ?>" data-placement="left" data-container="body" rel="tooltip"></i>
                                <?php if ($default) { ?>
                                <span class="label label-radio"><input type="radio" name="default" value="1" id="default-yes" checked="checked" /><label for="default-yes"><?php echo $text_yes; ?></label></span>
                                <span class="label label-radio"><input type="radio" name="default" value="0" id="default-no" /><label for="default-no"><?php echo $text_no; ?></label></span>
                                <?php } else { ?>
                                <span class="label label-radio"><input type="radio" name="default" value="1" id="default-yes" /><label for="default-yes"><?php echo $text_yes; ?></label></span>
                                <span class="label label-radio"><input type="radio" name="default" value="0" id="default-no"checked="checked" /><label for="default-no"><?php echo $text_no; ?></label></span>
                                <?php } ?>
                            </p>
                        </div>
                        <div class="formbox">
                            <p class="form-label">
                                <strong><?php echo $entry_firstname; ?></strong>
                                <i class="fa fa-question-circle float-right grey-text" title="<?php echo $entry_firstname . $help_required; ?>" data-placement="left" data-container="body" rel="tooltip"></i>
                            </p>
                            <input type="text" name="firstname" value="<?php echo $firstname; ?>" placeholder="<?php echo $entry_firstname; ?>" />
                            <?php if ($error_firstname) { ?><span class="error"><i class="fa fa-exclamation-triangle"></i><?php echo $error_firstname; ?></span><?php } ?>
                        </div>
                        <div class="formbox">
                            <p class="form-label">
                                <strong><?php echo $entry_lastname; ?></strong>
                                <i class="fa fa-question-circle float-right grey-text" title="<?php echo $entry_lastname . $help_required; ?>" data-placement="left" data-container="body" rel="tooltip"></i>
                            </p>
                            <input type="text" name="lastname" value="<?php echo $lastname; ?>" placeholder="<?php echo $entry_lastname; ?>" />
                            <?php if ($error_lastname) { ?><span class="error"><i class="fa fa-exclamation-triangle"></i><?php echo $error_lastname; ?></span><?php } ?>
                        </div>
                        <div class="formbox">
                            <p class="form-label">
                                <strong><?php echo $entry_company . $help_optional; ?></strong>
                                <i class="fa fa-question-circle float-right grey-text" title="<?php echo $entry_company . $help_optional; ?>" data-placement="left" data-container="body" rel="tooltip"></i>
                            </p>
                            <input type="text" name="company" value="<?php echo $company; ?>" placeholder="<?php echo $entry_company; ?>" />
                        </div>
                        <?php if ($company_id_display) { ?>
                        <div class="formbox">
                            <p class="form-label">
                                <strong><?php echo $entry_company_id; ?></strong>
                                <i class="fa fa-question-circle float-right grey-text" title="<?php echo $entry_company_id; ?>" data-placement="left" data-container="body" rel="tooltip"></i>
                            </p>
                            <input type="text" name="company_id" value="<?php echo $company_id; ?>" placeholder="<?php echo $entry_company_id; ?>" />
                            <?php if ($error_company_id) { ?><span class="error"><i class="fa fa-exclamation-triangle"></i><?php echo $error_company_id; ?></span><?php } ?>
                        </div>
                        <?php } ?>
                        <?php if ($tax_id_display) { ?>
                        <div class="formbox">
                            <p class="form-label">
                                <strong><?php echo $entry_tax_id; ?></strong>
                                <i class="fa fa-question-circle float-right grey-text" title="<?php echo $entry_tax_id; ?>" data-placement="left" data-container="body" rel="tooltip"></i>
                            </p>
                            <input type="text" name="tax_id" value="<?php echo $tax_id; ?>" placeholder="<?php echo $entry_tax_id; ?>" />
                            <?php if ($error_tax_id) { ?><span class="error"><i class="fa fa-exclamation-triangle"></i><?php echo $error_tax_id; ?></span><?php } ?>
                        </div>
                        <?php } ?>
                        <div class="formbox">
                            <p class="form-label">
                                <strong><?php echo $entry_address_1; ?></strong>
                                <i class="fa fa-question-circle float-right grey-text" title="<?php echo $entry_address_1 . $help_required; ?>" data-placement="left" data-container="body" rel="tooltip"></i>
                            </p>
                            <input type="text" name="address_1" value="<?php echo $address_1; ?>" placeholder="<?php echo $entry_address_1; ?>" />
                            <?php if ($error_address_1) { ?><span class="error"><i class="fa fa-exclamation-triangle"></i><?php echo $error_address_1; ?></span><?php } ?>
                        </div>
                        <div class="formbox">
                            <p class="form-label">
                                <strong><?php echo $entry_address_2 . $help_optional; ?></strong>
                                <i class="fa fa-question-circle float-right grey-text" title="<?php echo $entry_address_2 . $help_optional; ?>" data-placement="left" data-container="body" rel="tooltip"></i>
                            </p>
                            <input type="text" name="address_2" value="<?php echo $address_2; ?>" placeholder="<?php echo $entry_address_2; ?>" />
                        </div>
                        <div class="formbox">
                            <p class="form-label">
                                <strong><?php echo $entry_city; ?></strong>
                                <i class="fa fa-question-circle float-right grey-text" title="<?php echo $entry_city . $help_required; ?>" data-placement="left" data-container="body" rel="tooltip"></i>
                            </p>
                            <input type="text" name="city" value="<?php echo $city; ?>" placeholder="<?php echo $entry_city; ?>" />
                            <?php if ($error_city) { ?><span class="error"><i class="fa fa-exclamation-triangle"></i><?php echo $error_city; ?></span><?php } ?>
                        </div>
                        <div class="formbox">
                            <p class="form-label">
                                <strong><?php echo $entry_country; ?></strong>
                                <i class="fa fa-question-circle float-right grey-text" title="<?php echo $entry_country . $help_required; ?>" data-placement="left" data-container="body" rel="tooltip"></i>
                            </p>
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
                            <?php if ($error_country) { ?><span class="error"><i class="fa fa-exclamation-triangle"></i><?php echo $error_country; ?></span><?php } ?>
                        </div>
                        <div class="formbox">
                            <p class="form-label">
                                <strong><?php echo $entry_zone; ?></strong>
                                <i class="fa fa-question-circle float-right grey-text" title="<?php echo $entry_zone . $help_required; ?>" data-placement="left" data-container="body" rel="tooltip"></i>
                            </p>
                            <select name="zone_id">
                            </select>
                            <?php if ($error_zone) { ?><span class="error"><i class="fa fa-exclamation-triangle"></i><?php echo $error_zone; ?></span><?php } ?>
                        </div>
                        <div class="formbox">
                            <p class="form-label">
                                <strong><?php echo $entry_postcode; ?></strong> <span id="postcode-required" class="required hidden">*</span>
                                <i class="fa fa-question-circle float-right grey-text" title="<?php echo $entry_postcode; ?>" data-placement="left" data-container="body" rel="tooltip"></i>
                            </p>
                            <input type="text" name="postcode" value="<?php echo $postcode; ?>" placeholder="<?php echo $entry_postcode; ?>" />
                            <?php if ($error_postcode) { ?><span class="error"><i class="fa fa-exclamation-triangle"></i><?php echo $error_postcode; ?></span><?php } ?>
                        </div>
                    </div><!-- .content -->

					</div>
                </form>
                <?php echo $content_bottom; ?>
            </div>
        </div>
        <?php echo $column_right; ?>
    </div>
</div>
<script type="text/javascript"><!--
var textSelect = '<?php echo $text_select; ?>';
var textNone = '<?php echo $text_none; ?>';
var addressZoneId = '<?php echo $zone_id; ?>';
//--></script>
<?php echo $footer; ?>
