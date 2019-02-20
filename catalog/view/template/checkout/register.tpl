<div class="layout-left-minus-right">
	<div id="checkout-registration-form" class="container-left">
	<div class="widget">
	<h6><?php echo $text_join; ?></h6>
	<div class="content">
		<div id="show-button-register"></div>
		<p><?php echo $text_members; ?></p>
		<div class="formbox" title="<?php echo $help_firstname; ?>" rel="tooltip" data-container="body">
			<p class="form-label"><strong><?php echo $entry_firstname; ?></strong></p>
			<input type="text" name="firstname" placeholder="<?php echo $entry_firstname; ?>" value="" />
		</div>
		<div class="formbox" title="<?php echo $help_lastname; ?>" rel="tooltip" data-container="body">
			<p class="form-label"><strong><?php echo $entry_lastname; ?></strong></p>
			<input type="text" name="lastname" placeholder="<?php echo $entry_lastname; ?>" value="" />
		</div>
		<div class="formbox" title="<?php echo $help_email; ?>" rel="tooltip" data-container="body">
			<p class="form-label"><strong><?php echo $entry_email; ?></strong></p>
			<input type="text" name="email" placeholder="<?php echo $entry_email; ?>" value="" />
		</div>
		<div class="formbox" title="<?php echo $help_password; ?>" data-content="<?php echo $error_password; ?>" data-placement="top" rel="popover" data-trigger="hover">
			<p class="form-label"><strong><?php echo $entry_password; ?></strong></p>
			<input type="password" name="password" placeholder="<?php echo $entry_password; ?>" value="" />
		</div>
		<div class="formbox" title="<?php echo $help_captcha; ?>" rel="tooltip" data-container="body">
			<p class="form-label"><strong><?php echo $entry_captcha; ?></strong></p>
			<div id="register-g-recaptcha" class="recaptcha-box"></div>
		</div>
	</div>
    <div class="buttons">
        <div class="left">
            <input type="button" value="<?php echo $button_continue; ?>" id="button-register" class="button hidden" />
            <a id="button-register" class="button button_highlight bigger"><i class="fa fa-group"></i><?php echo $button_continue; ?></a>
        </div>
		<div class="left">
			<span class="terms help"><?php echo $text_agree; ?></span>
		</div>
    </div>
</div>
</div>
</div>
