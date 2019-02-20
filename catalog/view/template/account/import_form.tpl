<?php echo $header; ?>
<div class="container-page anonpost-form-page">
    <div class="breadcrumb">
        <div class="layout">
            <h1><a href="<?php echo $page; ?>"><i class="fa fa-pencil"></i><?php echo $heading_title; ?></a></h1>
            <div class="links">
                <?php foreach ($breadcrumbs as $breadcrumb) { ?>
                <?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
                <?php } ?>
            </div>
        </div>
    </div>
    <div class="layout layout-left-minus-right">

        <div class="container-left">
            <div class="content-page anonpost-listing">
            	<?php echo $notification; ?>

            	<?php echo $content_top; ?>

                <div class="widget">

					<h6><?php echo $heading_sub_title; ?></h6>

                    <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form" class="account-product-form">
    					<div class="content">

                            <p class="text-center text-larger"><span class="help"><?php echo $text_import_intro; ?></span></p>

                            <?php if ($success) { ?>
                            <div class="success">
                                <p><?php echo $success; ?></p>
                                <span class="close"><i class="fa fa-times"></i></span>
                                <span class="icon"><i class="fa fa-check-circle"></i></span>
                            </div>
                            <?php } ?>

                            <?php if ($warning) { ?>
                            <div class="warning">
                                <p><?php echo $warning; ?></p>
                                <span class="close"><i class="fa fa-times"></i></span>
                                <span class="icon"><i class="fa fa-exclamation-triangle"></i></span>
                            </div>
                            <?php } ?>

                            <div class="formbox">
                                <p class="form-label"><strong><?php echo $entry_import; ?></strong></p>
                                <input type="file" name="import" id="import" required="required" />
                                <?php if ($error_import) { ?><span class="error"><i class="fa fa-exclamation-triangle"></i><?php echo $error_import; ?></span><?php } ?>
                            </div>

                            <div class="formbox">
                                <p class="form-label"><strong><?php echo $entry_max; ?></strong></p>
                                <input type="text" name="max" value="<?php echo isset($max) ? $max : ''; ?>" required="required" />
                            </div>

                            <div class="formbox">
                                <p class="form-label"><strong><?php echo $entry_profile; ?></strong></p>
                                <input type="text" name="member[member_name]" value="<?php echo (isset($member['member_name']) ? $member['member_name'] : ''); ?>" placeholder="<?php echo $help_member; ?>" />
                                <input type="hidden" name="member[member_id]" value="<?php echo (isset($member['member_id']) ? $member['member_id'] : ''); ?>" />
                                <p><span class="help"><i class="fa fa-info-circle"></i><?php echo $help_member_info; ?></a></span></p>
                            </div>

                            <div class="clearafter">
                                <div class="grid-2">
                                    <div class="formbox">
                                        <p class="form-label"><strong><?php echo $entry_approved; ?></strong></p>
                                        <select name="approved">
                      					  <?php if ($approved) { ?>
                      					  <option value="1" selected="selected"><?php echo $text_yes; ?></option>
                      					  <option value="0"><?php echo $text_no; ?></option>
                      					  <?php } else { ?>
                      					  <option value="1"><?php echo $text_yes; ?></option>
                      					  <option value="0" selected="selected"><?php echo $text_no; ?></option>
                      					  <?php } ?>
                      					</select>
                                    </div>
                                </div>
                                <div class="grid-2">
                                    <div class="formbox">
                                        <p class="form-label"><strong><?php echo $entry_status; ?></strong></p>
                                        <select name="status">
                      					  <?php if ($status) { ?>
                      					  <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                      					  <option value="0"><?php echo $text_disabled; ?></option>
                      					  <?php } else { ?>
                      					  <option value="1"><?php echo $text_enabled; ?></option>
                      					  <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                      					  <?php } ?>
                      					</select>
                                    </div>
                                </div>
                            </div>

					        <input id="import-submit" type="submit" value="<?php echo $button_import; ?>" class="button hidden" />
                            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>" />

    					</div>

    					<div class="buttons">
    						<div class="center">
                                <label for="import-submit" class="button button-submit button_highlight bigger"><i class="fa fa-upload"></i> <?php echo $button_import; ?></label>
    						</div>
    					</div>
                    </form>

                </div>
                <?php echo $content_bottom; ?>
            </div>
        </div>

    </div>
</div>
<?php echo $footer; ?>
