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
            <div class="content-page">
            	<?php echo $content_top; ?>
                <div class="widget">
					<h6><?php echo isset($heading_sub_title) ? $heading_sub_title : $heading_title; ?></h6>
                    <div class="content global-messages">
						<?php echo $text_message; ?>
					</div>
                    <div class="buttons">
						<?php if (isset($account) && isset($text_my_account)) { ?>
                        <div class="left"><a href="<?php echo $account; ?>" class="button"><i class="fa fa-user"></i><?php echo $text_my_account; ?></a></div>
                        <?php } ?>
                        <div class="right"><a href="<?php echo $continue; ?>" class="button button_alt"><i class="fa fa-home"></i><?php echo $button_continue; ?></a></div>
                    </div>
                </div>
                <?php echo $content_bottom; ?>
            </div>
        </div>
        <?php echo $column_right; ?>
    </div>
</div>
<?php echo $footer; ?>
