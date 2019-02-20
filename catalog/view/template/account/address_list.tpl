<?php echo $header; ?>
<div class="container-page">
    <div class="breadcrumb">
        <div class="layout">
            <h1><i class="fa fa-address-book"></i> <?php echo $heading_title; ?></h1>
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
            <div class="content-page my-account my-address">
            	<?php echo $notification; ?>
				<?php echo $content_top; ?>

                <div class="widget">

                    <h6><?php echo $text_addresses; ?></h6>

                    <div class="buttons">
                        <div class="left">
                            <a href="<?php echo $insert; ?>" class="button"><i class="fa fa-file-o"></i><?php echo $button_new; ?></a>
                            <a href="<?php echo $back; ?>" class="button button_cancel"><i class="fa fa-undo"></i><?php echo $button_back; ?></a>
                        </div>
                    </div>

                    <div class="content">
						<?php if ($addresses) { ?>
						<?php foreach ($addresses as $result) { ?>
						<ul class="list-icon">
							<li>
                                <?php if ($result['default']) { ?>
                                <h4><span class="gold-text"><i class="fa fa-star"></i></span> <?php echo $text_default; ?></h4>
                                <?php } ?>
                                <?php echo $result['address']; ?>
                            </li>
							<li>
								<div class="linksdivs">
                                    <a href="<?php echo $result['update']; ?>" class="button smaller"><i class="fa fa-pencil"></i><?php echo $button_edit; ?></a>
                                </div>
								<div class="linksdivs">
                                    <a href="<?php echo $result['delete']; ?>" class="button button_alt smaller"><i class="fa fa-trash"></i><?php echo $button_delete; ?></a>
                                </div>
							</li>
						</ul>
						<?php } ?>
						<?php } else { ?>
						<div class="information">
							<p><?php echo $text_no_addresses; ?></p>
							<span class="icon"><i class="fa fa-info-circle"></i></span>
						</div>
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
