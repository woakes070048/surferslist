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
            <div class="content-page my-account my-returns">
            	<?php echo $content_top; ?>
                <div class="global-page">
                    <?php if ($returns) { ?>
                    <?php foreach ($returns as $return) { ?>
                    <ul class="list-icon">
                        <li><strong><?php echo $text_return_id; ?></strong> #<?php echo $return['return_id']; ?></li>
                        <li><strong><?php echo $text_status; ?></strong> <?php echo $return['status']; ?></li>
                        <li><strong><?php echo $text_date_added; ?></strong> <?php echo $return['date_added']; ?></li>
                        <li><strong><?php echo $text_order_id; ?></strong> <?php echo $return['order_id']; ?></li>
                        <li><strong><?php echo $text_customer; ?></strong> <?php echo $return['name']; ?></li>
                        <li>
                            <div class="linksdivs">
                                <a href="<?php echo $return['href']; ?>" class="button smaller"><i class="fa fa-print"></i><?php echo $button_view; ?></a>
                            </div>
                        </li>
                    </ul>
                    <?php } ?>
                    <div class="pagination"><?php echo $pagination; ?></div>
                    <?php } else { ?>
                    <div class="information">
                        <p><?php echo $text_empty; ?></p>
                        <span class="icon"><i class="fa fa-info-circle"></i></span>
                    </div>
                    <?php } ?>
                    <div class="buttons">
                        <div class="right"><a href="<?php echo $continue; ?>" class="button button_alt"><?php echo $button_continue; ?></a></div>
                    </div>
                </div>
                <?php echo $content_bottom; ?>
            </div>
        </div>
        <?php echo $column_right; ?>
    </div>
</div>
<?php echo $footer; ?>
