<form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="popup-register-form" class="popup-form">
    <div class="widget">
        <h6><?php echo $text_register_join; ?></h6>
        <div class="content">
            <div class="social-login">
                <a id="fb-auth" class="button button-fb-login bigger"><i class="fa fa-facebook-square"></i><?php echo $button_login_facebook; ?></a>
                <input type="hidden" id="csrf-token" name="csrf_token" value="<?php echo $csrf_token; ?>" />
                <p class="login-or"><?php echo $text_or; ?></p>
                <p><?php echo $text_register_members; ?></p>
            </div>
            <div class="popup-notification"></div>
            <div class="formbox">
                <p class="form-label">
                    <strong><?php echo $entry_register_firstname; ?></strong>
                    <i class="fa fa-question-circle float-right grey-text" title="<?php echo $help_register_firstname . $help_required; ?>" data-placement="left" rel="tooltip"></i>
                </p>
                <input type="text" name="firstname" placeholder="<?php echo $entry_register_firstname; ?>" value="" required="required">
            </div>
            <div class="formbox">
                <p class="form-label">
                    <strong><?php echo $entry_register_lastname; ?></strong>
                    <i class="fa fa-question-circle float-right grey-text" title="<?php echo $help_register_lastname . $help_required; ?>" data-placement="left" rel="tooltip"></i>
                </p>
                <input type="text" name="lastname" placeholder="<?php echo $entry_register_lastname; ?>" value="" required="required">
            </div>
            <div class="formbox">
                <p class="form-label">
                    <strong><?php echo $entry_register_email; ?></strong>
                    <i class="fa fa-question-circle float-right grey-text" title="<?php echo $help_register_email . $help_required; ?>" data-placement="left" rel="tooltip"></i>
                </p>
                <input type="text" name="email" placeholder="<?php echo $entry_register_email; ?>" value="" required="required">
            </div>
            <div class="formbox">
                <p class="form-label">
                    <strong><?php echo $entry_register_password; ?></strong>
                    <i class="fa fa-question-circle float-right grey-text" title="<?php echo $help_register_password . $help_required; ?>" data-content="<?php echo $help_register_password_requirements; ?>" data-placement="left" rel="popover" data-trigger="hover"></i>
                </p>
                <input type="password" name="password" placeholder="<?php echo $entry_register_password; ?>" value="" required="required">
            </div>
            <?php if ($captcha_enabled) { ?>
            <div class="formbox">
                <p class="form-label">
                    <strong><?php echo $entry_captcha; ?></strong>
                    <i class="fa fa-question-circle float-right grey-text" title="<?php echo $help_captcha . $help_required; ?>" data-placement="left" rel="tooltip"></i>
                </p>
                <div id="register-popup-g-recaptcha" class="recaptcha-box"></div>
            </div>
            <?php } ?>
        </div>
        <div class="buttons">
            <div class="center">
                <input id="popup-register-form-submit" type="submit" value="<?php echo $button_register; ?>" class="button hidden" />
                <input type="hidden" name="redirect" value="<?php echo $redirect; ?>" />
                <label for="popup-register-form-submit" class="button button-submit button-register button_highlight bigger fullwidth"><i class="fa fa-group"></i> <?php echo $button_register; ?></label>
                <span class="terms help"><?php echo $text_register_agree; ?></span>
                <span class="terms help"><?php echo $text_register_account_already; ?></span>
            </div>
        </div>
    </div>
</form>
