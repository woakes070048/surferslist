<?php echo $header; ?>
<div class="container-page">
    <div class="breadcrumb">
        <div class="layout">
            <h1><i class="fa fa-heart"></i> <?php echo $heading_title; ?></h1>
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
            <div class="content-page my-account my-wishlist">
            	<?php echo $content_top; ?>

                <div class="widget">

                	<h6><?php echo $heading_title; ?></h6>

					<div class="buttons">
						<div class="left">
                            <a href="<?php echo $continue; ?>" class="button button_alt">
                                <i class="fa fa-undo"></i><?php echo $button_continue; ?>
                            </a>
                        </div>
					</div>

					<div class="content">
						<?php if ($products) { ?>
						<?php foreach ($products as $product) { ?>
						<div id="wishlist-row<?php echo $product['product_id']; ?>" class="item-mini wishlist-item-mini">
							<?php if ($product['thumb']) { ?>
							<div class="image image-border">
                                <a href="<?php echo $product['href']; ?>">
                                    <img src="<?php echo $product['thumb']; ?>" alt="<?php echo $product['name']; ?>" />
                                </a>
                            </div>
							<?php } ?>
							<div class="info">
								<h2><a href="<?php echo $product['href']; ?>" title="<?php echo $product['name']; ?>"><?php echo $product['name']; ?></a></h2>
                                <div class="model hidden">
                                    <strong><?php echo $column_model; ?>:</strong> <?php echo $product['model']; ?>
                                </div>
								<?php if ($product['price'] && $product['quantity'] >= 0) { ?>
								<div class="price">
                                    <div class="all-price">
                                        <?php if (!$product['special']) { ?>
                                        <?php echo $product['price']; ?>
                                        <?php } else { ?>
                                        <span class="price-old"><span class="span1">&nbsp;</span><span class="span2">&nbsp;</span> <?php echo $product['price']; ?></span>
                                        <?php echo $product['special']; ?>
                                            <i class="badges sale-badges">-<?php echo $product['salebadges']; ?>&#37;</i>
                                        <?php } ?>
                                    </div>
								</div>
								<?php } ?>
    							<ul class="list-icon">
    								<li>
                                        <div class="linksdivs">
                                            <a href="<?php echo $product['href']; ?>" class="button smaller"><i class="fa fa-eye"></i> View</a>
                                        </div>
                                        <?php if ($product['price'] && $product['quantity'] > 0) { ?>
    									<div class="linksdivs">
                                            <a onclick="addToCart('<?php echo $product['product_id']; ?>');" class="button button_highlight smaller"><i class="fa fa-shopping-cart"></i><?php echo $button_cart; ?></a>
                                        </div>
                                        <?php } ?>
    									<div class="linksdivs">
                                            <a href="<?php echo $product['remove']; ?>" class="button button_trash smaller"><i class="fa fa-trash"></i><?php echo $button_remove; ?></a>
                                        </div>
    								</li>
    							</ul>
							</div>
						</div>
						<?php } ?>
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
