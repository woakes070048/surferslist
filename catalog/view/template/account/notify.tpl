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
            <div class="content-page my-account-notify">
            	<?php echo $notification; ?>
				<?php echo $content_top; ?>

				<form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form" class="member-notify-form">

					<div class="widget">

                        <h6><?php echo $text_notify_info; ?></h6>

						<div class="buttons">
							<div class="left">
                                <a class="button button-submit button_save"><i class="fa fa-save"></i><?php echo $button_save; ?></a>
								<a href="<?php echo $back; ?>" class="button button_cancel"><i class="fa fa-ban"></i><?php echo $button_cancel; ?></a>
							</div>
						</div>

                        <div class="content">
                            <?php foreach ($notifications as $notification) { ?>
                            <div class="clearafter">
                                <div class="grid-3">
                                    <h3>
                                        <label for="email-notifications-<?php echo $notification['key']; ?>"><?php echo $notification['label']; ?></label>
                                        <i class="fa fa-question-circle float-right grey-text" title="<?php echo $notification['label']; ?>" data-content="<?php echo $notification['help']; ?>" data-placement="left" rel="popover" data-container="body" data-trigger="hover"></i>
                                    </h3>
                                    <p><span class="help"><?php echo $notification['help']; ?></span></p>
                                </div>
                                <div class="grid-6">
                                    <div class="formbox email-notifications">
                                        <div class="enable-disable-buttons">
                                            <a class="grid-2 button bigger <?php echo (!$notification['value']) ? 'button_no' : 'button_cancel'; ?>" data-value="0"><i class="fa fa-times"></i><?php echo $text_no; ?></a>
                                            <a class="grid-2 button bigger <?php echo ($notification['value']) ? 'button_yes' : 'button_cancel'; ?>" data-value="1"><i class="fa fa-check"></i><?php echo $text_yes; ?></a>
                                            <input type="hidden" value="<?php echo $notification['value']; ?>" id="email-notifications-<?php echo $notification['key']; ?>" name="<?php echo $notification['key']; ?>" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php } ?>
                            <hr />
                            <p class="help text-center"><?php echo $text_notify_footer; ?></p>
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
