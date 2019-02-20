<form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="popup-login-form" class="popup-form">
    <div class="widget">
        <h6><?php echo $text_login; ?></h6>
        <div class="content">
            <div class="social-login">
                <a id="fb-auth" class="button button-fb-login bigger"><i class="fa fa-facebook-square"></i><?php echo $button_login_facebook; ?></a>
                <input type="hidden" id="csrf-token" name="csrf_token" value="<?php echo $csrf_token; ?>" />
                <p class="login-or"><?php echo $text_or; ?></p>
            </div>
            <div class="popup-notification"></div>
            <div class="formbox">
                <p class="form-label">
                    <strong><?php echo $entry_login_email; ?></strong>
                    <i class="fa fa-question-circle float-right grey-text" title="<?php echo $help_login_email . $help_required; ?>" data-placement="left" rel="tooltip"></i>
                </p>
                <input type="text" name="email" placeholder="<?php echo $entry_login_email; ?>" value="" required="required">
            </div>
            <div class="formbox">
                <p class="form-label">
                    <strong><?php echo $entry_login_password; ?></strong>
                    <i class="fa fa-question-circle float-right grey-text" title="<?php echo $help_login_password . $help_required; ?>" data-placement="left" rel="tooltip"></i>
                </p>
                <input type="password" name="password" placeholder="<?php echo $entry_login_password; ?>" value="" required="required">
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
                <input id="popup-login-form-submit" type="submit" value="<?php echo $button_login; ?>" class="button hidden" />
                <input type="hidden" name="redirect" value="<?php echo $redirect; ?>" />
                <label for="popup-login-form-submit" class="button button-submit button-login button_highlight bigger fullwidth"><i class="fa fa-sign-in"></i> <?php echo $button_login; ?></label>
                <a class="button bigger fullwidth" href="<?php echo $register; ?>"><i class="fa fa-group"></i><?php echo $button_register; ?></a>
                <span class="terms"><a href="<?php echo $account_forgotten_password; ?>" class="gray-text"><i class="fa fa-question"></i><?php echo $button_forgot_password; ?></a></span>
            </div>
        </div>
    </div>
</form>
