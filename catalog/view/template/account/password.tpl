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
    <div class="layout">
        <?php echo $column_left; ?>
        <div class="container-center">
            <div class="content-page my-account my-password">
            	<?php echo $content_top; ?>

                <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form" class="password-form">

                <div class="widget">

					<h6><?php echo $text_change; ?></h6>

					<div class="buttons">
						<div class="left">
							<a id="form-submit" class="button button_save"><i class="fa fa-save"></i><?php echo $button_continue; ?></a>
							<a href="<?php echo $back; ?>" class="button button_alt"><i class="fa fa-ban"></i><?php echo $button_back; ?></a>
						</div>
					</div>

                    <div class="content">
                        <div class="formbox">
                            <p class="form-label">
                                <strong><?php echo $entry_password; ?></strong>
                                <i class="fa fa-question-circle float-right grey-text" title="<?php echo $help_password . $help_required; ?>" data-content="<?php echo $help_password_requirements; ?>" data-placement="left" rel="popover" data-container="body" data-trigger="hover"></i>
                            </p>
                            <input type="password" name="password" value="<?php echo $password; ?>" />
                            <?php if ($error_password) { ?><span class="error"><i class="fa fa-exclamation-triangle"></i><?php echo $error_password; ?></span><?php } ?>
                        </div>
                        <div class="formbox">
                            <p class="form-label">
                                <strong><?php echo $entry_confirm; ?></strong>
                                <i class="fa fa-question-circle float-right grey-text" title="<?php echo $help_password_confirm . $help_required; ?>" data-placement="left" data-container="body" rel="tooltip"></i>
                            </p>
                            <input type="password" name="confirm" value="<?php echo $confirm; ?>" />
                            <?php if ($error_confirm) { ?><span class="error"><i class="fa fa-exclamation-triangle"></i><?php echo $error_confirm; ?></span><?php } ?>
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
