<div class="layout-left-minus-right">
	<div id="login" class="container-left">
		<div class="widget">
			<h6><?php echo $text_returning_customer; ?></h6>
			<div class="content">
				<div class="social-login">
					<a id="fb-auth" class="button button-fb-login bigger"><i class="fa fa-facebook-square"></i><?php echo $button_login_facebook; ?></a>
					<input type="hidden" id="csrf-token" name="csrf_token" value="<?php echo $csrf_token; ?>" />
					<p class="login-or"><?php echo $text_or; ?></p>
				</div>
				<div id="show-login-warning"></div>
				<div class="formbox">
					<p class="form-label"><strong><?php echo $entry_email; ?></strong></p>
					<input type="text" name="email" placeholder="<?php echo $entry_email; ?>" value="" />
				</div>
				<div class="formbox">
					<p class="form-label"><strong><?php echo $entry_password; ?></strong></p>
					<input type="password" name="password" placeholder="<?php echo $entry_password; ?>" value="" />
				</div>
			</div>
			<div class="buttons">
				<div class="left" title="<?php echo $text_i_am_returning_customer; ?>" rel="tooltip">
					<a id="button-login" class="button button_highlight bigger"><i class="fa fa-sign-in"></i><?php echo $button_login; ?></a>
				</div>
				<div class="right" title="<?php echo $text_not_a_member; ?>" rel="tooltip">
					<a class="button bigger" id="button-account"><i class="fa fa-group"></i><?php echo $button_register; ?></a>
				</div>
				<div class="left">
					<span class="terms"><a href="<?php echo $forgotten; ?>" class="gray-text"><i class="fa fa-question"></i><?php echo $text_forgotten; ?></a></span>
				</div>
			</div>
		</div>
	</div>
</div>
