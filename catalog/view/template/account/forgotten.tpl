<?php echo $header; ?>
<div class="container-page">
    <div class="breadcrumb">
        <div class="layout">
            <h1><?php echo $heading_title; ?></h1>
            <div class="links">
                <?php foreach ($breadcrumbs as $breadcrumb) { ?>
                <?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
                <?php } ?>
            </div>
        </div>
    </div>
    <div class="layout layout-left-minus-right">
		<div class="content-page my-account my-password">
            <div class="container-left">
                <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form" class="password-forgotten">

                <div class="widget">
                    <h6><?php echo $text_forgotten; ?></h6>

                    <div class="content">
                        <p><?php echo $text_reset; ?></p>

                        <?php if ($error_warning) { ?>
						<div class="warning">
							<p><?php echo $error_warning; ?></p>
							<span class="icon"><i class="fa fa-exclamation-triangle"></i></span>
						</div>
						<?php } ?>

                        <?php if ($name_required) { ?>
                        <div class="formbox">
                            <p class="form-label">
                                <strong><?php echo $entry_name; ?></strong>
                                <i class="fa fa-question-circle float-right grey-text" title="<?php echo $entry_name . $help_required; ?>" data-content="<?php echo $help_name; ?>" data-placement="left" rel="popover" data-container="body" data-trigger="hover"></i>
                            </p>
                            <input type="text" name="name" placeholder="<?php echo $entry_name; ?>" value="<?php echo $name; ?>" required="required" />
							<?php if ($error_name) { ?><span class="error"><i class="fa fa-exclamation-triangle"></i><?php echo $error_name; ?></span><?php } ?>
                        </div>
                        <?php } ?>

                        <div class="formbox"">
                            <p class="form-label">
                                <strong><?php echo $entry_email; ?></strong>
                                <i class="fa fa-question-circle float-right grey-text" title="<?php echo $help_email . $help_required; ?>" data-placement="left" data-container="body" rel="tooltip"></i>
                            </p>
                            <input type="text" name="email" placeholder="<?php echo $entry_email; ?>" value="<?php echo $email; ?>" required="required" />
							<?php if ($error_email) { ?><span class="error"><i class="fa fa-exclamation-triangle"></i><?php echo $error_email; ?></span><?php } ?>
                        </div>

                        <?php if ($captcha_enabled) { ?>
                        <div class="formbox">
                            <p class="form-label">
                                <strong><?php echo $entry_captcha; ?></strong>
                                <i class="fa fa-question-circle float-right grey-text" title="<?php echo $help_captcha . $help_required; ?>" data-placement="left" data-container="body" rel="tooltip"></i>
                            </p>
                            <div id="anonpost-g-recaptcha" class="recaptcha-box"></div>
                            <span class="help text-center"><i class="fa fa-info-circle"></i><?php echo $help_captcha; ?></span>
                            <?php if ($error_captcha) { ?><span class="error"><i class="fa fa-exclamation-triangle"></i><?php echo $error_captcha; ?></span><?php } ?>
                        </div>
                        <?php } ?>
                    </div>

                    <div class="buttons">
                        <div class="left">
                            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>" />
                            <input id="account-forgotten-form-submit" type="submit" value="<?php echo $button_continue; ?>" class="button hidden" />
                            <label for="account-forgotten-form-submit" class="button button-submit"><i class="fa fa-check"></i> <?php echo $button_continue; ?></label>
                        </div>
                        <div class="right">
                            <a href="<?php echo $cancel; ?>" class="button button_alt"><i class="fa fa-ban"></i><?php echo $button_cancel; ?></a>
                        </div>
                    </div>

                </div>

                </form>
            </div>
        </div>
    </div>
</div>
<?php echo $footer; ?>
