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
            <div class="content-page my-account my-certificate">
            	<?php echo $content_top; ?>
                <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data">
                <div class="widget">
                    <h6><?php echo $heading_title; ?></h6>
                    <div class="content">
                        <?php if ($error_warning) { ?>
                        <div class="warning">
                            <p><?php echo $error_warning; ?></p>
                            <span class="close"><i class="fa fa-times"></i></span>
                            <span class="icon"><i class="fa fa-exclamation-triangle"></i></span>
                        </div>
                        <?php } ?>
                        <p><?php echo $text_description; ?></p>
                        <div class="formboxcolumn">
                            <div class="formbox">
                                <p><span class="required">*</span> <strong><?php echo $entry_from_name; ?></strong></p>
                                <input type="text" name="from_name" value="<?php echo $from_name; ?>" />
                                <?php if ($error_from_name) { ?><span class="error"><i class="fa fa-exclamation-triangle"></i><?php echo $error_from_name; ?></span><?php } ?>
                            </div>
                            <div class="formbox">
                                <p><span class="required">*</span> <strong><?php echo $entry_from_email; ?></strong></p>
                                <input type="text" name="from_email" value="<?php echo $from_email; ?>" />
                                <?php if ($error_from_email) { ?><span class="error"><i class="fa fa-exclamation-triangle"></i><?php echo $error_from_email; ?></span><?php } ?>
                            </div>
                            <div class="formbox">
                                <p><span class="required">*</span> <strong><?php echo $entry_amount; ?></strong></p>
                                <input type="text" name="amount" value="<?php echo $amount; ?>" />
                                <?php if ($error_amount) { ?><span class="error"><i class="fa fa-exclamation-triangle"></i><?php echo $error_amount; ?></span><?php } ?>
                            </div>
                            <div class="formbox">
                                <p><span class="required">*</span> <strong><?php echo $entry_theme; ?></strong></p>
                                <?php foreach ($voucher_themes as $voucher_theme) { ?>
                                <?php if ($voucher_theme['voucher_theme_id'] == $voucher_theme_id) { ?>
                                    <span class="label"><input type="radio" name="voucher_theme_id" value="<?php echo $voucher_theme['voucher_theme_id']; ?>" id="voucher-<?php echo $voucher_theme['voucher_theme_id']; ?>" checked="checked" /><label for="voucher-<?php echo $voucher_theme['voucher_theme_id']; ?>"><?php echo $voucher_theme['name']; ?></label></span>
                                    <?php } else { ?>
                                    <span class="label"><input type="radio" name="voucher_theme_id" value="<?php echo $voucher_theme['voucher_theme_id']; ?>" id="voucher-<?php echo $voucher_theme['voucher_theme_id']; ?>" /><label for="voucher-<?php echo $voucher_theme['voucher_theme_id']; ?>"><?php echo $voucher_theme['name']; ?></label></span>
                                <?php } ?>
                                <?php } ?>
                                <?php if ($error_theme) { ?><span class="error"><i class="fa fa-exclamation-triangle"></i><?php echo $error_theme; ?></span><?php } ?>
                            </div>
                        </div><div class="formboxcolumn">
                            <div class="formbox">
                                <p><span class="required">*</span> <strong><?php echo $entry_to_name; ?></strong></p>
                                <input type="text" name="to_name" value="<?php echo $to_name; ?>" />
                                <?php if ($error_to_name) { ?><span class="error"><i class="fa fa-exclamation-triangle"></i><?php echo $error_to_name; ?></span><?php } ?>
                            </div>
                            <div class="formbox">
                                <p><span class="required">*</span> <strong><?php echo $entry_to_email; ?></strong></p>
                                <input type="text" name="to_email" value="<?php echo $to_email; ?>" />
                                <?php if ($error_to_email) { ?><span class="error"><i class="fa fa-exclamation-triangle"></i><?php echo $error_to_email; ?></span><?php } ?>
                            </div>
                            <div class="formbox">
                                <p><strong><?php echo $entry_message; ?></strong></p>
                                <textarea name="message"><?php echo $message; ?></textarea>
                            </div>
                        </div>
                    </div> 
                    <div class="buttons">
                        <div class="left">
                            <input id="account-voucher-form-submit" type="submit" value="<?php echo $button_continue; ?>" class="button hidden" />
                            <label for="account-voucher-form-submit" class="button button-submit"><i class="fa fa-check"></i> <?php echo $button_continue; ?></label>
                        </div>
                        <div class="right">
                            <span class="terms"><input type="checkbox" id="agree" name="agree" value="1" <?php if ($agree) { ?>checked="checked"<?php } ?>/><label for="agree"><?php echo $text_agree; ?></label></span>
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
