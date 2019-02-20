<?php echo $header; ?>
<div class="container-page">
    <div class="breadcrumb">
        <div class="layout">
            <h1><i class="fa fa-list-alt"></i> <?php echo $heading_title; ?></h1>
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
            <div class="content-page my-account my-orders">
            	<?php echo $content_top; ?>

            	<div class="widget">

					<h6><?php echo $heading_sub_title; ?></h6>

					<div class="buttons">
						<div class="left"><a href="<?php echo $continue; ?>" class="button button_alt"><i class="fa fa-undo"></i><?php echo $button_continue; ?></a></div>
					</div>

					<div class="content clearafter">
						<?php if ($orders) { ?>
						<?php foreach ($orders as $order) { ?>
						<div class="grid-3">
							<ul class="list-icon">
								<li><strong><?php echo $text_order_id; ?>:</strong>  #<?php echo $order['order_id']; ?></li>
								<li><strong><?php echo $text_status; ?>:</strong>  <?php echo $order['status']; ?></li>
								<li><strong><?php echo $text_date_added; ?>:</strong>  <?php echo $order['date_added']; ?></li>
								<li><strong><?php echo $text_products; ?>:</strong>  <?php echo $order['products']; ?></li>
								<li class="hidden"><strong><?php echo $text_member; ?>:</strong>  <a href="<?php echo $order['member_href']; ?>"><?php echo $order['member']; ?></a></li>
								<li><strong><?php echo $text_total; ?>:</strong>  <?php echo $order['total']; ?></li>
                                <li>
                                    <div class="linksdivs">
                                        <a href="<?php echo $order['href']; ?>" class="button smaller"><i class="fa fa-eye"></i><?php echo $button_view; ?></a>
                                    </div>
                                </li>
							</ul>
						</div>
						<?php } ?>
						<div class="pagination"><?php echo $pagination; ?></div>
						<?php } else { ?>
						<div class="information">
							<p><?php echo $text_empty; ?></p>
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
