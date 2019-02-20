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
            <div class="content-page my-account my-downloads">
            	<?php echo $content_top; ?>
				<?php // echo $notification; ?>

                <div class="global-page">
                    <?php foreach ($downloads as $download) { ?>
                    <ul class="list-icon">
                        <li><strong><?php echo $text_order; ?></strong> <?php echo $download['order_id']; ?></li>
                        <li><strong><?php echo $text_size; ?></strong> <?php echo $download['size']; ?></li>
                        <li><strong><?php echo $text_name; ?></strong> <?php echo $download['name']; ?></li>
                        <li><strong><?php echo $text_date_added; ?></strong> <?php echo $download['date_added']; ?></li>
                        <li><strong><?php echo $text_remaining; ?></strong> <?php echo $download['remaining']; ?></li>
                        <?php if ($download['remaining'] > 0) { ?>
                        <li>
                            <div class="linksdivs">
                                <a href="<?php echo $download['href']; ?>"><i class="fa fa-download-alt"></i><?php echo $button_download; ?></a>
                            </div>
                        </li>
                        <?php } ?>
                    </ul>
                    <?php } ?>
                    <div class="pagination"><?php echo $pagination; ?></div>
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
