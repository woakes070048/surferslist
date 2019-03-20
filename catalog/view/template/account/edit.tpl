<?php echo $header; ?>
<div class="container-page">
    <div class="breadcrumb">
        <div class="layout">
            <h1><i class="fa fa-address-card"></i> <?php echo $heading_title; ?></h1>
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
            <div class="content-page my-account">
            	<?php echo $notification; ?>
				<?php echo $content_top; ?>

				<form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form" class="account-edit">
					<div class="widget">

						<h6><?php echo $text_your_details; ?></h6>

						<div class="buttons">
							<div class="left">
								<input id="account-edit-form-submit" type="submit" value="<?php echo $button_save; ?>" class="button hidden" />
                                <label for="account-edit-form-submit" class="button button-submit button_save"><i class="fa fa-save"></i> <?php echo $button_save; ?></label>
								<a href="<?php echo $back; ?>" class="button button_cancel"><i class="fa fa-ban"></i><?php echo $button_cancel; ?></a>
							</div>
							<div class="right hidden-small">
								<a href="<?php echo $addresses; ?>" class="button button_alt"><i class="fa fa-address-book-o"></i><?php echo $text_edit . ' ' . $text_addresses; ?></a>
							</div>
						</div>

						<div class="content">
							<div class="formbox">
								<p class="form-label">
                                    <strong><?php echo $entry_firstname; ?></strong>
                                    <i class="fa fa-question-circle float-right grey-text" title="<?php echo $entry_firstname . $help_required; ?>" data-placement="left" data-container="body" rel="tooltip"></i>
                                </p>
								<input type="text" name="firstname" value="<?php echo $firstname; ?>"  placeholder="<?php echo $entry_firstname; ?>" />
								<?php if ($error_firstname) { ?><span class="error"><i class="fa fa-exclamation-triangle"></i><?php echo $error_firstname; ?></span><?php } ?>
							</div>
                            <div class="formbox">
								<p class="form-label">
                                    <strong><?php echo $entry_lastname; ?></strong>
                                    <i class="fa fa-question-circle float-right grey-text" title="<?php echo $entry_lastname . $help_required; ?>" data-placement="left" data-container="body" rel="tooltip"></i>
                                </p>
								<input type="text" name="lastname" value="<?php echo $lastname; ?>"  placeholder="<?php echo $entry_lastname; ?>" />
								<?php if ($error_lastname) { ?><span class="error"><i class="fa fa-exclamation-triangle"></i><?php echo $error_lastname; ?></span><?php } ?>
							</div>
                            <div class="formbox">
								<p class="form-label">
                                    <strong><?php echo $entry_email; ?></strong>
                                    <i class="fa fa-question-circle float-right grey-text" title="<?php echo $entry_email . $help_required; ?>" data-placement="left" data-container="body" rel="tooltip"></i>
                                </p>
								<input type="text" name="email" value="<?php echo $email; ?>"  placeholder="<?php echo $entry_email; ?>" />
								<?php if ($error_email) { ?><span class="error"><i class="fa fa-exclamation-triangle"></i><?php echo $error_email; ?></span><?php } ?>
							</div>
                            <div class="formbox">
								<p class="form-label">
                                    <strong><?php echo $entry_telephone . $help_optional; ?></strong>
                                    <i class="fa fa-question-circle float-right grey-text" title="<?php echo $entry_telephone . $help_optional; ?>" data-placement="left" data-container="body" rel="tooltip"></i>
                                </p>
								<input type="text" name="telephone" value="<?php echo $telephone; ?>"  placeholder="<?php echo $entry_telephone; ?>" />
                                <span class="help"><?php echo $help_phone; ?></span>
								<?php if ($error_telephone) { ?><span class="error"><i class="fa fa-exclamation-triangle"></i><?php echo $error_telephone; ?></span><?php } ?>
							</div>
                            <div class="formbox">
								<p class="form-label">
                                    <strong><?php echo $entry_fax . $help_optional; ?></strong>
                                    <i class="fa fa-question-circle float-right grey-text" title="<?php echo $entry_fax . $help_optional; ?>" data-placement="left" data-container="body" rel="tooltip"></i>
                                </p>
								<input type="text" name="fax" value="<?php echo $fax; ?>"  placeholder="<?php echo $entry_fax; ?>" />
							</div>
						</div>

					</div>

				</form>

                <?php echo $content_bottom; ?>
            </div>
        </div>
        <?php echo $column_right; ?>
    </div>
</div>
<?php echo $footer; ?>
