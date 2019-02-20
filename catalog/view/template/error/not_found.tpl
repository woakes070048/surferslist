<?php echo $header; ?>
<div class="container-page">
    <div class="breadcrumb">
        <div class="layout">
            <?php if (!empty($action)) { ?>
            <h1><a href="<?php echo $action; ?>"><?php echo $heading_title; ?></a></h1>
            <?php } else { ?>
            <h1><?php echo $heading_title; ?></h1>
            <?php } ?>
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
                <?php echo isset($notification) ? $notification : ''; ?>

                <div class="global-page">
                    <div class="global-messages">
						<div class="warning">
							<p><?php echo $text_error; ?></p>
							<span class="icon"><i class="fa fa-exclamation-triangle"></i></span>
						</div>
					</div>

                    <div class="buttons">
                        <div class="left"><a href="<?php echo $continue; ?>" class="button button_home"><i class="fa fa-undo"></i><?php echo $button_continue; ?></a></div>
                        <?php if (isset($search)) { ?>
                        <div class="right"><a href="<?php echo $search; ?>" class="button button_search button_highlight"><i class="fa fa-search"></i><?php echo $button_search; ?></a></div>
                        <?php } ?>
                    </div>
                </div>

                <?php echo $content_bottom; ?>
            </div>
        </div>
        <?php echo $column_right; ?>
    </div>
</div>
<?php echo $footer; ?>
