<?php echo $header; ?>
<div class="container-page contact-page">
    <div class="breadcrumb">
        <div class="layout">
            <h1><a href="<?php echo $page; ?>"><i class="fa fa-envelope"></i><?php echo $heading_title; ?></a></h1>
            <div class="links">
                <?php foreach ($breadcrumbs as $breadcrumb) { ?>
                <?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
                <?php } ?>
            </div>
        </div>
    </div>
    <div class="layout layout-left-minus-right">
        <!-- <?php // echo $column_left; ?> -->

        <div class="container-left">
            <div class="content-page">
            	<?php echo $content_top; ?>
				<?php echo $notification; ?>

                <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form">

                <div class="widget">
                    <h6><?php echo $text_contact; ?></h6>

                    <div class="content clearafter">
						<p class="text-larger"><?php echo $text_intro; ?></p>

                        <?php if ($warning) { ?>
                        <div class="warning">
                            <p><?php echo $warning; ?></p>
                            <span class="close"><i class="fa fa-times"></i></span>
                            <span class="icon"><i class="fa fa-exclamation-triangle"></i></span>
                        </div>
                        <?php } ?>

                        <div class="formbox<?php echo (isset($member['member_name']) && isset($member['member_id'])) ? ' success' : ''; ?>">
                            <p class="form-label">
                                <strong><?php echo $entry_member; ?></strong>
                                <i class="fa fa-question-circle float-right grey-text" title="<?php echo $help_member . $help_required; ?>" data-content="<?php echo $help_member_info; ?>" data-placement="left" rel="popover" data-container="body" data-trigger="hover"></i>
                            </p>
							<input type="text" name="member[member_name]" value="<?php echo (isset($member['member_name']) ? $member['member_name'] : ''); ?>" placeholder="<?php echo $entry_member; ?>" required="required" />
							<input type="hidden" name="member[member_id]" value="<?php echo (isset($member['member_id']) ? $member['member_id'] : ''); ?>" />
							<span class="label help"><i class="fa fa-info-circle"></i><?php echo $help_admin; ?>&nbsp;&nbsp;&nbsp;<a href="<?php echo $members; ?>" class="pull-right" title="<?php echo $text_members; ?>"><i class="fa fa-group"></i><?php echo $text_members; ?></a></span>
                            <?php if ($error_member) { ?><span class="error"><i class="fa fa-exclamation-triangle"></i><?php echo $error_member; ?></span><?php } ?>
                        </div>

                        <div class="formbox">
                            <p class="form-label">
                                <strong><?php echo $entry_name; ?></strong>
                                <i class="fa fa-question-circle float-right grey-text" title="<?php echo $help_name . $help_required; ?>" data-placement="left" rel="tooltip"></i>
                            </p>
                            <input type="text" name="name" value="<?php echo $name; ?>" placeholder="<?php echo $entry_name; ?>" required="required" />
                            <?php if ($error_name) { ?><span class="error"><i class="fa fa-exclamation-triangle"></i><?php echo $error_name; ?></span><?php } ?>
                        </div>

                        <div class="formbox">
                            <p class="form-label">
                                <strong><?php echo $entry_email; ?></strong>
                                <i class="fa fa-question-circle float-right grey-text" title="<?php echo $help_email . $help_required; ?>" data-placement="left" rel="tooltip"></i>
                            </p>
                            <input type="text" name="email" value="<?php echo $email; ?>" placeholder="<?php echo $entry_email; ?>" required="required" />
                            <?php if ($error_email) { ?><span class="error"><i class="fa fa-exclamation-triangle"></i><?php echo $error_email; ?></span><?php } ?>
                        </div>

                        <div class="formbox">
							<p class="form-label">
                                <strong><?php echo $entry_message; ?></strong>
                                <i class="fa fa-question-circle float-right grey-text" title="<?php echo $help_message . $help_required; ?>" data-placement="left" rel="tooltip"></i>
                            </p>
							<textarea name="message" placeholder="<?php echo $entry_message; ?>" cols="40" rows="13"><?php echo $message; ?></textarea>
							<?php if ($error_message) { ?><span class="error"><i class="fa fa-exclamation-triangle"></i><?php echo $error_message; ?></span><?php } ?>
						</div>

                        <?php if ($captcha_enabled) { ?>
						<div class="formbox">
							<p class="form-label">
                                <strong><?php echo $entry_captcha; ?></strong>
                                <i class="fa fa-question-circle float-right grey-text" title="<?php echo $help_captcha . $help_required; ?>" data-placement="left" rel="tooltip"></i>
                            </p>
							<div id="contact-g-recaptcha" class="recaptcha-box"></div>
                            <span class="help text-center"><i class="fa fa-info-circle"></i><?php echo $help_unauthorized; ?></span>
							<?php if ($error_captcha) { ?><span class="error"><i class="fa fa-exclamation-triangle"></i><?php echo $error_captcha; ?></span><?php } ?>
						</div>
                        <?php } ?>
                    </div>

                    <div class="buttons">
                        <div class="center">
                            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>" />
                            <input id="contact-submit" type="submit" value="<?php echo $button_submit; ?>" class="button hidden" />
							<label for="contact-submit" class="button button-submit button_highlight bigger fullwidth"><i class="fa fa-envelope"></i> <?php echo $button_submit; ?></label>
                            <span class="terms help"><i class="fa fa-question-circle"></i><?php echo $text_footer; ?></span>
						</div>
                    </div>
                </div>

                </form>

                <?php if ($contact_deails) { ?>
                <div class="widget">
                    <h6><?php echo $text_location; ?></h6>

                    <div class="content">
                        <?php if ($contact_address) { ?>
                        <ul class="list-icon list-icon-top list-icon3">
                            <li class="list-icon-title"><i class="fa fa-map-marker"></i> <strong><?php echo $text_address; ?></strong></li>
                            <li><?php echo $contact_store; ?><br /><?php echo $contact_address; ?></li>
                        </ul>
                        <?php } ?>
                        <?php if ($contact_telephone) { ?>
                        <ul class="list-icon list-icon-top list-icon3">
                            <li class="list-icon-title"><i class="fa fa-phone"></i> <strong><?php echo $text_telephone; ?></strong></li>
                            <li><?php echo $contact_telephone; ?></li>
                        </ul>
                        <?php } ?>
                        <?php if ($contact_email) { ?>
                        <ul class="list-icon list-icon-top list-icon3">
                            <li class="list-icon-title"><i class="fa fa-envelope"></i> <strong><?php echo $text_email; ?></strong></li>
                            <li><?php echo $contact_email; ?></li>
                        </ul>
                        <?php } ?>
                        <?php if ($contact_fax) { ?>
                        <ul class="list-icon list-icon-top list-icon3">
                            <li class="list-icon-title"><i class="fa fa-print"></i> <strong><?php echo $text_fax; ?></strong></li>
                            <li><?php echo $contact_fax; ?></li>
                        </ul>
                        <?php } ?>
                    </div>
                </div>
                <?php } ?>

                <?php echo $content_bottom; ?>

            </div>
        </div>
        <!-- <?php // echo $column_right; ?> -->
    </div>
</div>
<?php echo $footer; ?>
