<?php echo $header; ?>
<div class="container-page">
    <div class="breadcrumb">
        <div class="layout">
            <h1><a href="<?php echo $page; ?>"><i class="fa fa-group"></i><?php echo $heading_title; ?></a></h1>
            <div class="links">
                <?php foreach ($breadcrumbs as $breadcrumb) { ?>
                <?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
                <?php } ?>
            </div>
        </div>
    </div>
    <div class="layout layout-left-minus-right">
        <div class="content-page register-page">
            <div class="container-left">
                <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form">
                <div class="widget">
                    <h6><?php echo $text_join; ?></h6>
                    <div class="content" id="registration-form">
                        <div class="social-login">
                            <a id="fb-auth" class="button button-fb-login bigger"><i class="fa fa-facebook-square"></i><?php echo $button_login_facebook; ?></a>
                            <input type="hidden" id="csrf-token" name="csrf_token" value="<?php echo $csrf_token; ?>" />
                            <p class="login-or"><?php echo $text_or; ?></p>
                            <p><?php echo $text_members; ?></p>
                        </div>
                        <?php if ($error_warning) { ?>
                        <div class="warning">
                            <p><?php echo $error_warning; ?></p>
                            <span class="close"><i class="fa fa-times"></i></span>
                            <span class="icon"><i class="fa fa-exclamation-triangle"></i></span>
                        </div>
                        <?php } ?>
                        <div class="formbox">
                            <p class="form-label">
                                <strong><?php echo $entry_firstname; ?></strong>
                                <i class="fa fa-question-circle float-right grey-text" title="<?php echo $help_firstname . $help_required; ?>" data-placement="left" data-container="body" rel="tooltip"></i>
                            </p>
                            <input type="text" name="firstname" placeholder="<?php echo $entry_firstname; ?>" value="<?php echo $firstname; ?>" required="required" />
                            <?php if ($error_firstname) { ?><span class="error"><i class="fa fa-exclamation-triangle"></i><?php echo $error_firstname; ?></span><?php } ?>
                        </div>
                        <div class="formbox">
                            <p class="form-label">
                                <strong><?php echo $entry_lastname; ?></strong>
                                <i class="fa fa-question-circle float-right grey-text" title="<?php echo $help_lastname . $help_required; ?>" data-placement="left" data-container="body" rel="tooltip"></i>
                            </p>
                            <input type="text" name="lastname" placeholder="<?php echo $entry_lastname; ?>" value="<?php echo $lastname; ?>" required="required" />
                            <?php if ($error_lastname) { ?><span class="error"><i class="fa fa-exclamation-triangle"></i><?php echo $error_lastname; ?></span><?php } ?>
                        </div>
                        <div class="formbox">
                            <p class="form-label">
                                <strong><?php echo $entry_email; ?></strong>
                                <i class="fa fa-question-circle float-right grey-text" title="<?php echo $help_email . $help_required; ?>" data-placement="left" data-container="body" rel="tooltip"></i>
                            </p>
                            <input type="text" name="email" placeholder="<?php echo $entry_email; ?>" value="<?php echo $email; ?>" required="required" />
                            <?php if ($error_email) { ?><span class="error"><i class="fa fa-exclamation-triangle"></i><?php echo $error_email; ?></span><?php } ?>
                        </div>
                        <div class="formbox">
                            <p class="form-label">
                                <strong><?php echo $entry_password; ?></strong>
                                <i class="fa fa-question-circle float-right grey-text" title="<?php echo $help_password . $help_required; ?>" data-content="<?php echo $help_password_requirements; ?>" data-placement="left" rel="popover" data-container="body" data-trigger="hover"></i>
                            </p>
                            <input type="password" name="password" placeholder="<?php echo $entry_password; ?>" value="<?php echo $password; ?>" required="required" />
                            <?php if ($error_password) { ?><span class="error"><i class="fa fa-exclamation-triangle"></i><?php echo $error_password; ?></span><?php } ?>
                        </div>
                        <?php if ($captcha_enabled) { ?>
                        <div class="formbox">
							<p class="form-label">
                                <strong><?php echo $entry_captcha; ?></strong>
                                <i class="fa fa-question-circle float-right grey-text" title="<?php echo $help_captcha . $help_required; ?>" data-placement="left" data-container="body" rel="tooltip"></i>
                            </p>
							<div id="register-g-recaptcha" class="recaptcha-box"></div>
							<?php if ($error_captcha) { ?><span class="error"><i class="fa fa-exclamation-triangle"></i><?php echo $error_captcha; ?></span><?php } ?>
						</div>
                        <?php } ?>
                    </div>
                    <div class="buttons">
                        <div class="center">
                            <input id="account-register-form-submit" type="submit" value="<?php echo $button_continue; ?>" class="button hidden" />
                            <label for="account-register-form-submit" class="button button_highlight button-submit bigger fullwidth"><i class="fa fa-group"></i> <?php echo $button_continue; ?></label>
                            <span class="terms help"><?php echo $text_agree; ?></span>
                            <span class="terms help"><?php echo $text_account_already; ?></span>
                        </div>
                    </div>
                </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php echo $footer; ?>
