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
            <div class="content-page my-account my-newsletter">
            	<?php echo $content_top; ?>
                <div class="global-page">
                	<h6><?php echo $heading_title; ?></h6>
                    <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form">
                        <div class="formbox">
                            <p><strong><?php echo $entry_newsletter; ?></strong></p>
                            <?php if ($newsletter) { ?>
                                <span class="label"><input type="radio" name="newsletter" value="1" checked="checked" /><?php echo $text_yes; ?></span>
                                <span class="label"><input type="radio" name="newsletter" value="0" /><?php echo $text_no; ?></span>
                                <?php } else { ?>
                                <span class="label"><input type="radio" name="newsletter" value="1" /><?php echo $text_yes; ?></span>
                                <span class="label"><input type="radio" name="newsletter" value="0" checked="checked" /><?php echo $text_no; ?></span>
                            <?php } ?>
                        </div>
                        <div class="buttons">
                            <div class="left">
                                <input id="account-newsletter-form-submit" type="submit" value="<?php echo $button_continue; ?>" class="button" />
                                <label for="account-newsletter-form-submit" href="<?php echo $back; ?>" class="button button_alt"><?php echo $button_back; ?></label>
                            </div>
                        </div>
                    </form>
                </div>
                <?php echo $content_bottom; ?>
            </div>
        </div>
        <?php echo $column_right; ?>
    </div>
</div>
<?php echo $footer; ?>
