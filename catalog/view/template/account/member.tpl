<?php echo $header; ?>
<div class="container-page">
    <div class="breadcrumb">
        <div class="layout">
            <h1><i class="fa fa-user"></i> <?php echo $heading_title; ?></h1>
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
            <div class="content-page my-account my-profile">
            	<?php echo $notification; ?>
				<?php echo $content_top; ?>

				<form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form" class="member-profile-form">

					<div class="widget widget-ac-container">

                        <h6><?php echo $text_profile_info; ?></h6>

						<div class="buttons">
							<div class="left">
								<input id="account-member-form-submit" type="submit" value="<?php echo $button_save; ?>" class="button hidden" />
								<?php if (!$activated) { ?>
								<label for="account-member-form-submit" class="button button-submit button_highlight" title="<?php echo $button_save_activate; ?>" rel="tooltip" data-container="body"><i class="fa fa-save"></i> <?php echo $button_save_activate; ?></label>
								<?php } else { ?>
								<label for="account-member-form-submit" class="button button-submit button_save" title="<?php echo $button_save; ?>" rel="tooltip" data-container="body"><i class="fa fa-save"></i> <?php echo $button_save; ?></label>
								<?php } ?>
								<a href="<?php echo $back; ?>" class="button button_cancel" title="<?php echo $button_cancel; ?>" rel="tooltip" data-container="body"><i class="fa fa-ban"></i><?php echo $button_cancel; ?></a>
							</div>
							<div class="right">
								<?php if ($permissions && ($permissions['banner_enabled'] || $permissions['url_alias_enabled'])) { ?><span class="terms highlight"><i class="fa fa-certificate highlight"></i><?php echo $text_premium_field; ?></span>&nbsp; &nbsp;<?php } ?>
								<span class="terms"><a class="colorbox" href="<?php echo $help; ?>" title="<?php echo $text_member_profile_info; ?>" rel="tooltip" data-container="body"><i class="fa fa-question"></i><?php echo $text_help; ?></a></span>
							</div>
						</div>

                        <div class="content clearafter">

							<div class="grid-3 text-center">
								<div class="formbox">
									<p class="form-label">
                                        <strong><?php echo $entry_member_account_image; ?></strong>
                                        <i class="fa fa-question-circle float-right grey-text" title="<?php echo $entry_member_account_image . $help_optional; ?>" data-content="<?php echo $help_member_account_image; ?>" data-placement="left" rel="popover" data-container="body" data-trigger="hover"></i>
                                    </p>
									<div class="image image-border"><img src="<?php echo $member_account_image_thumb; ?>" alt="" id="member_account_image_thumb" class="thumb" /><br /><br />
									<input type="hidden" name="member_account_image" value="<?php echo $member_account_image; ?>" id="member_account_image" />
									<?php if ($permissions && $permissions['inventory_enabled']) { ?><a onclick="image_upload('member_account_image', 'member_account_image_thumb');" id="member_account_image_browse" class="button button_images button_alt hidden-small" title="<?php echo $text_browse; ?>" rel="tooltip" data-container="body"><i class="fa fa-file"></i><?php echo $text_browse; ?></a><?php } ?>
									<a id="button-upload" class="button"><i class="fa fa-upload"></i><?php echo $button_upload; ?></a>&nbsp;&nbsp;<a id="button-clear-image" class="button button_clear button_alt"><i class="fa fa-times"></i><?php echo $button_clear; ?></a></div>
									<?php if ($error_member_account_image) { ?><span class="error"><i class="fa fa-exclamation-triangle"></i><?php echo $error_member_account_image; ?></span><?php } ?>
								</div>
							</div>

							<div class="grid-6">
								<div class="formbox">
									<p class="form-label">
                                        <strong><?php echo $entry_member_account_name; ?></strong>
                                        <i class="fa fa-question-circle float-right grey-text" title="<?php echo $entry_member_account_name . $help_required; ?>" data-content="<?php echo $help_member_account_name; ?>" data-placement="left" rel="popover" data-container="body" data-trigger="hover"></i>
                                    </p>
									<input type="text" name="member_account_name" value="<?php echo $member_account_name; ?>" placeholder="<?php echo $entry_member_account_name; ?>" required="required" />
									<?php if ($error_member_account_name) { ?><span class="error"><i class="fa fa-exclamation-triangle"></i><?php echo $error_member_account_name; ?></span><?php } ?>
								</div>

								<div class="formbox">
									<p class="form-label">
                                        <strong><?php echo $entry_member_account_description; ?></strong>
                                        <i class="fa fa-question-circle float-right grey-text" title="<?php echo $entry_member_account_description . $help_optional; ?>" data-content="<?php echo $help_member_account_description; ?>" data-placement="left" rel="popover" data-container="body" data-trigger="hover"></i>
                                    </p>
									<textarea name="member_account_description" cols="25" rows="3" placeholder="<?php echo $entry_member_account_description; ?>"><?php echo $member_account_description; ?></textarea>
									<?php if ($error_member_account_description) { ?><span class="error"><i class="fa fa-exclamation-triangle"></i><?php echo $error_member_account_description; ?></span><?php } ?>
								</div>

								<?php if ($permissions && $permissions['sort_enabled']) { ?>
								<div class="formbox">
									<p class="form-label">
                                        <strong><?php echo $entry_member_tag; ?></strong>
                                        <i class="fa fa-question-circle float-right grey-text" title="<?php echo $entry_member_tag . $help_optional; ?>" data-content="<?php echo $help_member_tag; ?>" data-placement="left" rel="popover" data-container="body" data-trigger="hover"></i>
                                    </p>
									<input type="text" name="member_tag" value="<?php echo $member_tag; ?>" placeholder="<?php echo $entry_member_tag; ?>" />
									<?php if ($error_member_tag) { ?><span class="error"><i class="fa fa-exclamation-triangle"></i><?php echo $error_member_tag; ?></span><?php } ?>
								</div>
								<?php } else { ?>
								<div class="formbox">
									<p class="form-label">
                                        <strong><?php echo $entry_member_activity; ?></strong>
                                        <i class="fa fa-question-circle float-right grey-text" title="<?php echo $entry_member_activity . $help_optional; ?>" data-content="<?php echo $help_member_activity; ?>" data-placement="left" rel="popover" data-container="body" data-trigger="hover"></i>
                                    </p>
									<?php foreach ($member_activities as $key => $value) { ?>
									<span class="label label-activity grid-4"><input type="checkbox" name="member_activity[]" value="<?php echo $key; ?>" id="activity-<?php echo $key; ?>" <?php if (in_array($key, $member_activity)) { ?>checked="checked"<?php } ?>><label for="activity-<?php echo $key; ?>"><?php echo $value; ?></label></span>
									<?php } ?>
								</div>
								<?php } ?>
							</div>

                        </div>

                        <div class="widget-ac">
                            <h6 class="ac-title sub-section-title
                            <?php if (!$activated
                                || $error_member_url_alias
                                || $error_customer_group
                                || $error_member_group
                                || $error_member_paypal_account
                                || $jump_to_paypal
                                || $error_member_country
                                || $error_member_zone
                                || $error_member_city) { ?> widget-ac-active<?php } ?>">
                                <?php echo $text_member_details; ?>
                                <span><i class="fa fa-chevron-down icon-ac-down"></i><i class="fa fa-chevron-up icon-ac-up"></i></span>
                            </h6>

							<div class="ac-content content">

                                <?php if ($permissions && $permissions['url_alias_enabled']) { ?>
                                <div class="formbox">
                                    <p class="form-label">
                                        <strong><?php echo $entry_member_url_alias; ?></strong>
                                        <i class="fa fa-certificate highlight"></i>
                                        <i class="fa fa-question-circle float-right grey-text" title="<?php echo $entry_member_url_alias . $help_required; ?>" data-content="<?php echo $help_member_url_alias; ?>" data-placement="left" rel="popover" data-container="body" data-trigger="hover"></i>
                                    </p>
                                    <input type="text" name="member_url_alias" value="<?php echo $member_url_alias; ?>" placeholder="<?php echo $entry_member_url_alias; ?>" required="required" />
                                    <?php if ($error_member_url_alias) { ?><span class="error"><i class="fa fa-exclamation-triangle"></i><?php echo $error_member_url_alias; ?></span><?php } ?>
                                </div>
                                <?php } ?>

								<div class="formbox">
									<p class="form-label">
                                        <?php echo $entry_customer_group; ?>
                                        <i class="fa fa-question-circle float-right grey-text" title="<?php echo $help_customer_group . $help_required; ?>" data-placement="left" data-container="body" rel="tooltip"></i>
                                    </p>
									<select name="customer_group_id">
										<option value=""><?php echo $text_select_customer_group; ?></option>
										<?php foreach ($customer_groups as $customer_group) { ?>
										<?php if ($customer_group['customer_group_id'] == $customer_group_id) { ?>
										<option value="<?php echo $customer_group['customer_group_id']; ?>" selected="selected"><?php echo $customer_group['name']; ?></option>
										<?php } else { ?>
										<option value="<?php echo $customer_group['customer_group_id']; ?>"><?php echo $customer_group['name']; ?></option>
										<?php } ?>
										<?php } ?>
									</select>
								  <?php if ($error_customer_group) { ?><span class="error"><i class="fa fa-exclamation-triangle"></i><?php echo $error_customer_group; ?></span><?php } ?>
								</div>

								<div class="formbox hidden">
									<p class="form-label">
                                        <?php echo $entry_member_group; ?>
                                        <i class="fa fa-question-circle float-right grey-text" title="<?php echo $help_member_group . $help_required; ?>" data-placement="left" data-container="body" rel="tooltip"></i>
                                    </p>
									<select name="member_group_id" disabled="disabled">
										<?php foreach ($member_groups as $member_group) { ?>
										<?php if ($member_group['member_group_id'] == $member_group_id) { ?>
										<option value="<?php echo $member_group['member_group_id']; ?>" selected="selected"><?php echo $member_group['member_group_name']; ?></option>
										<?php } else { ?>
										<option value="<?php echo $member_group['member_group_id']; ?>"><?php echo $member_group['member_group_name']; ?></option>
										<?php } ?>
										<?php } ?>
									</select>
								  <?php if ($error_member_group) { ?><span class="error"><i class="fa fa-exclamation-triangle"></i><?php echo $error_member_group; ?></span><?php } ?>
								</div>

								<?php if ($this->config->get('member_member_paypal')) { ?>
								<div class="formbox" id="jump_to_paypal">
									<p class="form-label">
                                        <strong><?php echo $entry_member_paypal_account; ?></strong>
                                        <i class="fa fa-question-circle float-right grey-text" title="<?php echo ($this->config->get('member_member_paypal_require')) ? $entry_member_paypal_account . $help_required : $entry_member_paypal_account . $help_optional; ?>" data-content="<?php echo $help_member_paypal_account; ?>" data-placement="left" rel="popover" data-container="body" data-trigger="hover"></i>
                                    </p>
									<input type="text" name="member_paypal_account" value="<?php echo $member_paypal_account; ?>" placeholder="<?php echo $entry_member_paypal_account; ?>"  <?php if ($this->config->get('member_member_paypal_require')) { ?>required="required" <?php } ?>/>
									<?php if ($text_no_paypal) { ?><span class="help green-text text-larger"><i class="fa fa-info-circle"></i><?php echo $text_no_paypal; ?></span><?php } ?>
                                    <?php if ($error_member_paypal_account) { ?><span class="error"><i class="fa fa-exclamation-triangle"></i><?php echo $error_member_paypal_account; ?></span><?php } ?>
								</div>
								<?php } ?>

								<div class="formbox">
									<p class="form-label">
                                        <strong><?php echo $entry_member_country; ?></strong>
                                        <i class="fa fa-question-circle float-right grey-text" title="<?php echo $help_member_country . $help_required; ?>" data-placement="left" data-container="body" rel="tooltip"></i>
                                    </p>
									<select name="member_country_id" required="required">
									  <option value=""><?php echo $text_select_country; ?></option>
									  <?php foreach ($countries as $country) { ?>
									  <?php if ($country['country_id'] == $member_country_id) { ?>
									  <option value="<?php echo $country['country_id']; ?>" selected="selected"><?php echo $country['name']; ?></option>
									  <?php } else { ?>
									  <option value="<?php echo $country['country_id']; ?>"><?php echo $country['name']; ?></option>
									  <?php } ?>
									  <?php } ?>
									</select>
									<?php if ($error_member_country) { ?><span class="error"><i class="fa fa-exclamation-triangle"></i><?php echo $error_member_country; ?></span><?php } ?>
								</div>

								<div class="formbox">
									<p class="form-label">
                                        <strong><?php echo $entry_member_zone; ?></strong>
                                        <i class="fa fa-question-circle float-right grey-text" title="<?php echo $help_member_zone . $help_required; ?>" data-placement="left" data-container="body" rel="tooltip"></i>
                                    </p>
									<select name="member_zone_id" placeholder="<?php echo $entry_member_zone; ?>" required="required">
									</select>
									<?php if ($error_member_zone) { ?><span class="error"><i class="fa fa-exclamation-triangle"></i><?php echo $error_member_zone; ?></span><?php } ?>
								</div>

								<div class="formbox">
									<p class="form-label">
                                        <strong><?php echo $entry_member_city; ?></strong>
                                        <i class="fa fa-question-circle float-right grey-text" title="<?php echo $help_member_city . $help_required; ?>" data-placement="left" data-container="body" rel="tooltip"></i>
                                    </p>
									<input type="text" name="member_city" value="<?php echo $member_city; ?>" placeholder="<?php echo $entry_member_city; ?>" required="required" />
									<?php if ($error_member_city) { ?><span class="error"><i class="fa fa-exclamation-triangle"></i><?php echo $error_member_city; ?></span><?php } ?>
								</div>
							</div>
                        </div>

                        <div class="widget-ac"<?php if (!$member_custom_fields && !error_member_custom_fields) echo ' style="display:none;"'; ?>>
                            <h6 class="ac-title sub-section-title<?php if (!$activated || $error_member_custom_fields) echo ' widget-ac-active'; ?>"><?php echo $text_socials; ?><span><i class="fa fa-chevron-down icon-ac-down"></i><i class="fa fa-chevron-up icon-ac-up"></i></span></h6>

                            <div class="ac-content">

								<div class="member-socials content">
                                    <p class="text-center">
                                        <span class="help"><i class="fa fa-info-circle"></i><?php echo $help_member_socials; ?></span>
                                    </p>
									<?php if ($error_member_custom_fields) { ?>
									<p class="text-center"><span class="error"><i class="fa fa-exclamation-triangle"></i><?php echo $error_member_custom_fields; ?></span></p>
									<?php } ?>

									<div class="formbox"<?php if (!$entry_member_custom_field_01) echo ' style="display:none;"'; ?>>
										<p class="form-label">
                                            <strong><?php echo $entry_member_custom_field_01; ?></strong>
                                            <i class="fa fa-question-circle float-right grey-text" title="<?php echo $help_member_custom_field_01 . $help_optional; ?>" data-placement="left" data-container="body" rel="tooltip"></i>
                                        </p>
										<span class="social website"><i class="fa fa-globe"></i></span> <input type="text" name="member_custom_field_01" value="<?php echo $member_custom_field_01; ?>" placeholder="<?php echo $entry_member_custom_field_01; ?>" />
									</div>
									<div class="formbox"<?php if (!$entry_member_custom_field_02) echo ' style="display:none;"'; ?>>
										<p class="form-label">
                                            <strong><?php echo $entry_member_custom_field_02; ?></strong>
                                            <i class="fa fa-question-circle float-right grey-text" title="<?php echo $help_member_custom_field_02 . $help_optional; ?>" data-placement="left" data-container="body" rel="tooltip"></i>
                                        </p>
										<span class="social twitter"><i class="fa fa-twitter"></i></span> <input type="text" name="member_custom_field_02" value="<?php echo $member_custom_field_02; ?>" placeholder="<?php echo $entry_member_custom_field_02; ?>" />
									</div>
									<div class="formbox"<?php if (!$entry_member_custom_field_03) echo ' style="display:none;"'; ?>>
										<p class="form-label">
                                            <strong><?php echo $entry_member_custom_field_03; ?></strong>
                                            <i class="fa fa-question-circle float-right grey-text" title="<?php echo $help_member_custom_field_03 . $help_optional; ?>" data-placement="left" data-container="body" rel="tooltip"></i>
                                        </p>
										<span class="social facebook"><i class="fa fa-facebook"></i></span> <input type="text" name="member_custom_field_03" value="<?php echo $member_custom_field_03; ?>" placeholder="<?php echo $entry_member_custom_field_03; ?>" />
									</div>
									<div class="formbox"<?php if (!$entry_member_custom_field_05) echo ' style="display:none;"'; ?>>
										<p class="form-label">
                                            <strong><?php echo $entry_member_custom_field_05; ?></strong>
                                            <i class="fa fa-question-circle float-right grey-text" title="<?php echo $help_member_custom_field_05 . $help_optional; ?>" data-placement="left" data-container="body" rel="tooltip"></i>
                                        </p>
										<span class="social instagram"><i class="fa fa-instagram"></i></span> <input type="text" name="member_custom_field_05" value="<?php echo $member_custom_field_05; ?>" placeholder="<?php echo $entry_member_custom_field_05; ?>" />
									</div>
									<div class="formbox"<?php if (!$entry_member_custom_field_04) echo ' style="display:none;"'; ?>>
										<p class="form-label">
                                            <strong><?php echo $entry_member_custom_field_04; ?></strong>
                                            <i class="fa fa-question-circle float-right grey-text" title="<?php echo $help_member_custom_field_04 . $help_optional; ?>" data-placement="left" data-container="body" rel="tooltip"></i>
                                        </p>
										<span class="social pinterest"><i class="fa fa-pinterest"></i></span> <input type="text" name="member_custom_field_04" value="<?php echo $member_custom_field_04; ?>" placeholder="<?php echo $entry_member_custom_field_04; ?>" />
									</div>
								</div>
							</div>

						</div>

                        <?php if ($permissions && $permissions['banner_enabled']) { ?>
						<div class="widget-ac"<?php if ($error_member_account_banner) echo ' widget-ac-active'; ?>>
							<h6 class="ac-title sub-section-title"><?php echo $entry_member_account_banner; ?> <i class="fa fa-certificate highlight"></i><span><i class="fa fa-chevron-down icon-ac-down"></i><i class="fa fa-chevron-up icon-ac-up"></i></span></h6>

                            <div class="ac-content content">
								<div class="formbox">
									<p class="form-label">
                                        <strong><?php echo $entry_member_account_banner; ?></strong>
                                        <i class="fa fa-question-circle float-right grey-text" title="<?php echo $entry_member_account_banner . $help_optional; ?>" data-content="<?php echo $help_member_account_banner; ?>" data-placement="left" rel="popover" data-container="body" data-trigger="hover"></i>
                                    </p>
									<div class="image image-border"><img src="<?php echo $member_account_banner_thumb; ?>" alt="" id="member_account_banner_thumb" class="thumb" /><br /><br />
									<input type="hidden" name="member_account_banner" value="<?php echo $member_account_banner; ?>" id="member_account_banner" />
									<?php if ($permissions && $permissions['inventory_enabled']) { ?><a onclick="image_upload('member_account_banner', 'member_account_banner_thumb');" id="member_account_banner_browse" class="button button_images button_alt hidden-small" title="<?php echo $text_browse; ?>" rel="tooltip" data-container="body"><i class="fa fa-file"></i><?php echo $text_browse; ?></a><?php } ?>
									<a id="button-upload-banner" class="button"><i class="fa fa-upload"></i><?php echo $button_upload; ?></a>&nbsp;&nbsp;<a id="button-clear-banner" class="button button_clear button_alt"><i class="fa fa-times"></i><?php echo $button_clear; ?></a></div>
									<?php if ($error_member_account_banner) { ?><span class="error"><i class="fa fa-exclamation-triangle"></i><?php echo $error_member_account_banner; ?></span><?php } ?>
								</div>
							</div>
						</div>
						<?php } ?>

						<?php if ($permissions && $permissions['banner_enabled'] && $entry_member_custom_field_06) { ?>
						<div class="widget-ac"<?php if ($error_embed_settings_bool || $error_embed_settings_hex) echo ' widget-ac-active'; ?>>
							<h6 class="ac-title sub-section-title"><?php echo $entry_member_account_embed; ?> <i class="fa fa-certificate highlight"></i><span><i class="fa fa-chevron-down icon-ac-down"></i><i class="fa fa-chevron-up icon-ac-up"></i></span></h6>

                            <div class="ac-content content clearafter">
                                <div class="grid-1">
                                    <div class="formbox">
                                        <p><?php echo $help_member_account_embed; ?></p>
                                        <input type="hidden" name="member_custom_field_06" value="<?php echo $embed_code; ?>" />
                                    </div>
                                </div>
                                <div class="grid-2">
                                    <h4>
                                        <?php echo $entry_embed_settings_bool; ?>
                                        <i class="fa fa-question-circle float-right grey-text" title="<?php echo $entry_embed_settings_bool . $help_optional; ?>" data-content="<?php echo $help_embed_settings_bool; ?>" data-placement="left" rel="popover" data-container="body" data-trigger="hover"></i>
                                    </h4>
                                    <?php if ($error_embed_settings_bool) { ?>
                                    <p><span class="error"><i class="fa fa-exclamation-triangle"></i><?php echo $error_embed_settings_bool; ?></span></p>
                                    <?php } ?>
                                    <?php foreach ($embed_settings_bool as $bool_setting) { ?>
                                    <div class="formbox embed-settings-bool">
                                        <p><label for="embed-settings-<?php echo $bool_setting['key']; ?>"><?php echo $bool_setting['label']; ?></label></p>
                                        <div class="enable-disable-buttons">
                                            <a class="grid-2 button bigger <?php echo ($bool_setting['value'] == 'false') ? 'button_no' : 'button_cancel'; ?>" data-value="false"<?php if ($bool_setting['key'] == 'customcolor') { ?> data-trigger="embed-settings-hex"<?php } ?>><i class="fa fa-times"></i><?php echo $text_no; ?></a>
                                            <a class="grid-2 button bigger <?php echo ($bool_setting['value'] == 'true') ? 'button_yes' : 'button_cancel'; ?>" data-value="true"<?php if ($bool_setting['key'] == 'customcolor') { ?> data-trigger="embed-settings-hex"<?php } ?>><i class="fa fa-check"></i><?php echo $text_yes; ?></a>
                                            <input type="hidden" value="<?php echo $bool_setting['value']; ?>" id="embed-settings-<?php echo $bool_setting['key']; ?>" name="embed_settings_bool[<?php echo $bool_setting['key']; ?>]" />
                                        </div>
                                    </div>
                                    <?php } ?>
                                </div>
                                <div class="grid-2">
                                    <h4>
                                        <?php echo $entry_embed_settings_hex; ?>
                                        <i class="fa fa-question-circle float-right grey-text" title="<?php echo $entry_embed_settings_hex . $help_optional; ?>" data-content="<?php echo $help_embed_settings_hex; ?>" data-placement="left" rel="popover" data-container="body" data-trigger="hover"></i>
                                    </h4>
                                    <?php if ($error_embed_settings_hex) { ?>
                                    <p><span class="error"><i class="fa fa-exclamation-triangle"></i><?php echo $error_embed_settings_hex; ?></span></p>
                                    <?php } ?>
                                    <div id="embed-settings-hex">
                                        <?php foreach ($embed_settings_hex as $hex_setting) { ?>
                                        <div class="grid-2">
                                            <div class="formbox embed-settings-hex">
                                                <p><label for="embed-settings-<?php echo $hex_setting['key']; ?>"><?php echo $hex_setting['label']; ?></label></p>
                                                <input type="text" value="<?php echo $hex_setting['value']; ?>" name="embed_settings_hex[<?php echo $hex_setting['key']; ?>]" style="background-color:#<?php echo $hex_setting['value']; ?>;border-color:#<?php echo $hex_setting['value']; ?>;" />
                                            </div>
                                        </div>
                                        <?php } ?>
                                    </div>
                                </div>
                                <div class="grid-1">
                                    <div class="formbox">
                                        <p>
                                            <label for="embed-profile-url"><?php echo $entry_embed_iframe_code; ?></label>
                                            <i class="fa fa-question-circle float-right grey-text" title="<?php echo $entry_embed_iframe_code . $help_optional; ?>" data-content="<?php echo $help_embed_iframe_code; ?>" data-placement="left" rel="popover" data-container="body" data-trigger="hover"></i>
                                        </p>
                                        <pre><code><?php echo $embed_iframe_code; ?></code></pre>
                                        <span class="help"><i class="fa fa-info-circle"></i><?php echo $help_embed_iframe_code; ?> <br class="show-very-small" /><a href="<?php echo $embed_url; ?>" target="_blank"><i class="fa fa-external-link"></i><?php echo $text_preview_embed; ?></a></span>
                                    </div>
                                </div>
							</div>
						</div>
						<?php } ?>

					</div>

				</form>

                <?php echo $content_bottom; ?>
            </div>
        </div>
        <?php echo $column_right; ?>
    </div>
</div>
<script type="text/javascript"><!--
var textWait = '<?php echo $text_wait; ?>';
var textSelectZone = '<?php echo $text_select_zone; ?>';
var textNone = '<?php echo $text_none; ?>';
var memberZoneId = '<?php echo $member_zone_id; ?>';
var memberNoImage = '<?php echo $no_image; ?>';
<?php if ($permissions && $permissions['banner_enabled']) { ?>
var memberNoBanner = '<?php echo $no_banner; ?>';
<?php } ?>
<?php if ($permissions && $permissions['inventory_enabled']) { ?>
var textImageManager = '<?php echo $text_image_manager; ?>';
var imageMax = <?php echo $this->config->get('member_image_max_number'); ?>;
<?php } ?>
//--></script>
<?php echo $footer; ?>
