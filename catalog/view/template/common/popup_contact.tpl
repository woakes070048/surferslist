<form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="popup-contact-form" class="popup-form">
    <div class="widget">
        <h6><?php echo $text_contact_form; ?></h6>
        <div class="content">
            <div class="information">
                <p class="text-larger">
                    <span id="popup-contact-form-member" class="bolder"><?php echo (isset($member['member_name']) ? $member['member_name'] : '&nbsp;'); ?></span>
                </p>
                <span class="icon"><i class="fa fa-envelope"></i></span>
            </div>
            <div class="formbox success hidden">
                <input type="hidden" name="member[member_name]" value="<?php echo (isset($member['member_name']) ? $member['member_name'] : ''); ?>" placeholder="<?php echo $entry_contact_member; ?>" readonly="readonly" />
                <input type="hidden" name="member[member_id]" value="<?php echo (isset($member['member_id']) ? $member['member_id'] : ''); ?>" />
            </div>
            <div class="formbox">
                <p class="form-label">
                    <strong><?php echo $entry_contact_name; ?></strong>
                    <i class="fa fa-question-circle float-right grey-text" title="<?php echo $help_contact_name . $help_required; ?>" data-placement="left" rel="tooltip"></i>
                </p>
                <input type="text" name="name" value="<?php echo $name; ?>" placeholder="<?php echo $entry_contact_name; ?>" required="required" />
            </div>
            <div class="formbox">
                <p class="form-label">
                    <strong><?php echo $entry_contact_email; ?></strong>
                    <i class="fa fa-question-circle float-right grey-text" title="<?php echo $help_contact_email . $help_required; ?>" data-placement="left" rel="tooltip"></i>
                </p>
                <input type="text" name="email" value="<?php echo $email; ?>" placeholder="<?php echo $entry_contact_email; ?>" required="required" />
            </div>
            <div class="formbox">
                <p class="form-label">
                    <strong><?php echo $entry_contact_message; ?></strong>
                    <i class="fa fa-question-circle float-right grey-text" title="<?php echo $help_contact_message . $help_required; ?>" data-placement="left" rel="tooltip"></i>
                </p>
                <textarea name="message" placeholder="<?php echo $entry_contact_message; ?>" cols="40" rows="13" style="height:155px;"><?php echo $message; ?></textarea>
            </div>
            <?php if ($captcha_enabled) { ?>
            <div class="formbox">
                <p class="form-label">
                    <strong><?php echo $entry_captcha; ?></strong>
                    <i class="fa fa-question-circle float-right grey-text" title="<?php echo $help_captcha . $help_required; ?>" data-placement="left" rel="tooltip"></i>
                </p>
                <div id="contact-popup-g-recaptcha" class="recaptcha-box"></div>
                <span class="help text-center"><i class="fa fa-info-circle"></i><?php echo $help_unauthorized; ?></span>
            </div>
            <?php } ?>
        </div>
        <div class="buttons">
            <div class="center">
                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>" />
                <input id="popup-contact-form-submit" type="submit" value="<?php echo $button_contact_submit; ?>" class="button hidden" />
                <label for="popup-contact-form-submit" class="button button-submit button-contact button_highlight bigger fullwidth"><i class="fa fa-envelope"></i> <?php echo $button_contact_submit; ?></label>
                <span class="terms help"><i class="fa fa-question-circle"></i><?php echo $text_contact_form_footer; ?></span>
            </div>
        </div>
    </div>
</form>
