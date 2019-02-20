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
            <div class="content-page my-account my-return">
            	<?php echo $content_top; ?>
                <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form">
                <div class="widget">
                    <h6><?php echo $text_order; ?></h6>
                    <div class="content">
                        <?php if ($error_warning) { ?>
                        <div class="warning">
                            <p><?php echo $error_warning; ?></p>
                            <span class="close"><i class="fa fa-times"></i></span>
                            <span class="icon"><i class="fa fa-exclamation-triangle"></i></span>
                        </div>
                        <?php } ?>
                        <?php echo $text_description; ?>
                        <div class="formboxcolumn">
                            <div class="formbox">
                                <p><span class="required">*</span> <strong><?php echo $entry_firstname; ?></strong></p>
                                <input type="text" name="firstname" value="<?php echo $firstname; ?>" />
                                <?php if ($error_firstname) { ?><span class="error"><i class="fa fa-exclamation-triangle"></i><?php echo $error_firstname; ?></span><?php } ?>
                            </div>
                            <div class="formbox">
                                <p><span class="required">*</span> <strong><?php echo $entry_lastname; ?></strong></p>
                                <input type="text" name="lastname" value="<?php echo $lastname; ?>" />
                                <?php if ($error_lastname) { ?><span class="error"><i class="fa fa-exclamation-triangle"></i><?php echo $error_lastname; ?></span><?php } ?>
                            </div>
                            <div class="formbox">
                                <p><span class="required">*</span> <strong><?php echo $entry_email; ?></strong></p>
                                <input type="text" name="email" value="<?php echo $email; ?>" />
                                <?php if ($error_email) { ?><span class="error"><i class="fa fa-exclamation-triangle"></i><?php echo $error_email; ?></span><?php } ?>
                            </div>
                        </div><div class="formboxcolumn">
                            <div class="formbox">
                                <p><span class="required">*</span> <strong><?php echo $entry_telephone; ?></strong></p>
                                <input type="text" name="telephone" value="<?php echo $telephone; ?>" />
                                <?php if ($error_telephone) { ?><span class="error"><i class="fa fa-exclamation-triangle"></i><?php echo $error_telephone; ?></span><?php } ?>
                            </div>
                            <div class="formbox">
                                <p><span class="required">*</span> <strong><?php echo $entry_order_id; ?></strong></p>
                                <input type="text" name="order_id" value="<?php echo $order_id; ?>" />
                                <?php if ($error_order_id) { ?><span class="error"><i class="fa fa-exclamation-triangle"></i><?php echo $error_order_id; ?></span><?php } ?>
                            </div>
                            <div class="formbox">
                                <p><strong><?php echo $entry_date_ordered; ?></strong></p>
                                <input type="text" name="date_ordered" value="<?php echo $date_ordered; ?>" class="date" />
                            </div>
                        </div>
                    </div> 
                    <h6><?php echo $text_product; ?></h6>
                    <div id="return-product" class="content">
                        <div class="formboxcolumn">
                            <div class="formbox">
                                <p><span class="required">*</span> <strong><?php echo $entry_product; ?></strong></p>
                                <input type="text" name="product" value="<?php echo $product; ?>" />
                                <?php if ($error_product) { ?><span class="error"><i class="fa fa-exclamation-triangle"></i><?php echo $error_product; ?></span><?php } ?>
                            </div>
                            <div class="formbox">
                                <p><span class="required">*</span> <strong><?php echo $entry_model; ?></strong></p>
                                <input type="text" name="model" value="<?php echo $model; ?>" />
                                <?php if ($error_model) { ?><span class="error"><i class="fa fa-exclamation-triangle"></i><?php echo $error_model; ?></span><?php } ?>
                            </div>
                            <div class="formbox">
                                <p><strong><?php echo $entry_quantity; ?></strong></p>
                                <input type="text" name="quantity" value="<?php echo $quantity; ?>" />
                            </div>
                            <div class="formbox">
                                <p><strong><?php echo $entry_opened; ?></strong></p>
                                <?php if ($opened) { ?>
                                    <span class="label"><input type="radio" name="opened" value="1" id="opened" checked="checked" /><label for="opened"><?php echo $text_yes; ?></label></span>
                                    <?php } else { ?>
                                    <span class="label"><input type="radio" name="opened" value="1" id="opened" /><label for="opened"><?php echo $text_yes; ?></label></span>
                                <?php } ?>
                                <?php if (!$opened) { ?>
                                    <span class="label"><input type="radio" name="opened" value="0" id="unopened" checked="checked" /><label for="unopened"><?php echo $text_no; ?></label></span>
                                    <?php } else { ?>
                                    <span class="label"><input type="radio" name="opened" value="0" id="unopened" /><label for="unopened"><?php echo $text_no; ?></label></span>
                                <?php } ?>
                            </div>
                        </div><div class="formboxcolumn">
                            <div class="formbox">
                                <p><strong><?php echo $entry_fault_detail; ?></strong></p>
                                <textarea name="comment"><?php echo $comment; ?></textarea>
                            </div>
                            <div class="formbox">
                                <p><span class="required">*</span> <strong><?php echo $entry_reason; ?></strong></p>
                                <?php foreach ($return_reasons as $return_reason) { ?>
                                <?php if ($return_reason['return_reason_id'] == $return_reason_id) { ?>
                                    <span class="label"><input type="radio" name="return_reason_id" value="<?php echo $return_reason['return_reason_id']; ?>" id="return-reason-id<?php echo $return_reason['return_reason_id']; ?>" checked="checked" /><label for="return-reason-id<?php echo $return_reason['return_reason_id']; ?>"><?php echo $return_reason['name']; ?></label></span>
                                    <?php } else { ?>
                                    <span class="label"><input type="radio" name="return_reason_id" value="<?php echo $return_reason['return_reason_id']; ?>" id="return-reason-id<?php echo $return_reason['return_reason_id']; ?>" /><label for="return-reason-id<?php echo $return_reason['return_reason_id']; ?>"><?php echo $return_reason['name']; ?></label></span>
                                <?php  } ?>
                                <?php  } ?>
                                <?php if ($error_reason) { ?><span class="error"><i class="fa fa-exclamation-triangle"></i><?php echo $error_reason; ?></span><?php } ?>
                            </div>
                        </div>
                        <div class="formbox">
                            <p><span class="required">*</span> <strong><?php echo $entry_captcha; ?></strong></p>
                            <input type="text" name="captcha" value="<?php echo $captcha; ?>" />
                            <img src="index.php?route=account/return/captcha" alt="" />
                            <?php if ($error_captcha) { ?><span class="error"><i class="fa fa-exclamation-triangle"></i><?php echo $error_captcha; ?></span><?php } ?>
                        </div>
                    </div> 
                    <div class="buttons">
                        <div class="left">
                            <input id="account-return-form-submit" type="submit" value="<?php echo $button_continue; ?>" class="button hidden" />
                            <label for="account-return-form-submit" class="button button-submit"><i class="fa fa-check"></i> <?php echo $button_continue; ?></label>
                            <a href="<?php echo $back; ?>" class="button button_alt"><i class="fa fa-undo"></i><?php echo $button_back; ?></a>
                        </div>
                        <?php if ($text_agree) { ?>
                        <div class="right">
                            <span class="terms"><input type="checkbox" id="agree" name="agree" value="1" <?php if ($agree) { ?>checked="checked"<?php } ?>/><label for="agree"><?php echo $text_agree; ?></label></span>
                        </div>
                        <?php } ?>
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
