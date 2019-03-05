<div class="progress-bar"></div>
<div class="information loading">
	<p><?php echo $text_loading; ?></p>
	<span class="icon"><i class="fa fa-spin fa-circle-o-notch icon-spin"></i></span>
</div>
<div class="grid global-grid-item">
	<div class="grid-sizer"></div>
	<?php foreach ($products as $product) { ?>
	<article id="listing-<?php echo $product['product_id']; ?>"
		data-filter-class='["<?php if ($product["price"] && $product["special"]) { ?>sale<?php } else { ?>category<?php } ?>"]'
		class="grid-item itemcat<?php if ($product['compare']) { ?> grid-item-compare<?php } ?><?php if ($product['price'] && $product['featured']) { ?> grid-item-promo<?php } else if ($product["price"] && $product["special"]) { ?> grid-item-sale<?php } ?>"
		style="visibility: hidden;">
		<div class="image">
			<a href="<?php echo $product['href']; ?>" title="<?php echo $product['name']; ?>">
				<img src="<?php echo $product['thumb']; ?>" alt="<?php echo $product['name']; ?>" />
			</a>
			<span class="description">
				<a href="<?php echo $product['href']; ?>"><?php echo $product['description']; ?></a>
			</span>
		</div>
		<div class="quickview">
			<a class="button button_quickview smaller" href="<?php echo $product['quickview']; ?>" rel="quickview">
				<i class="fa fa-eye"></i> <?php echo $button_quickview; ?>
			</a>
		</div>
		<div class="pannel">
			<div class="info">
				<header>
					<h3><a href="<?php echo $product['href']; ?>" title="<?php echo $product['name']; ?>"><?php echo $product['name']; ?></a></h3>
				</header>
				<?php if ($product['price'] && $product['type_id'] >= 0) { ?>
				<div class="price">
					<?php if (!$product['special']) { ?>
					<span class="price-top"><?php echo $product['price']; ?></span>
					<?php } else { ?>
					<span class="price-old"><?php echo $product['price']; ?></span>
					<span class="price-new"><?php echo $product['special']; ?></span>
					<i class="badges sale-badges" rel="tooltip" data-placement="top" data-original-title="<?php echo $text_save; ?> <?php echo $product['savebadges']; ?>">-<?php echo $product['salebadges']; ?>&#37;</i>
					<?php } ?>
					<?php if ($product['tax']) { ?>
					<span class="price-tax"><?php echo $text_tax; ?> <?php echo $product['tax']; ?></span>
					<?php } ?>
				</div>
				<?php } ?>
			</div>

			<footer class="add-to add-to111">
				<a onclick="addToWishList('<?php echo $product['product_id']; ?>');" rel="tooltip" data-placement="top" data-original-title="<?php echo $button_wishlist; ?>">
					<i class="fa fa-save"></i>
				</a>
				<?php if ($product['type_id'] < 0 || $product['customer_id'] == 0) { ?>
				<a href="<?php echo $product['href']; ?>" rel="tooltip" data-placement="top" data-original-title="<?php echo $button_learn_more; ?>">
					<i class="fa fa-info-circle"></i>
				</a>
				<?php } else if ($product['price'] && $product['type_id'] > 0) { ?>
				<a onclick="addToCart('<?php echo $product['product_id']; ?>');" rel="tooltip" data-placement="top" data-original-title="<?php echo $button_cart; ?>">
					<i class="fa fa-shopping-cart"></i>
				</a>
				<?php } else { ?>
				<a onclick="messageProfile('<?php echo $product['customer_id']; ?>','<?php echo $product['product_id']; ?>');" rel="tooltip" data-placement="top" data-original-title="<?php echo $button_contact; ?>">
					<i class="fa fa-envelope"></i>
				</a>
				<?php } ?>
				<?php if ($product['compare']) { ?>
				<a onclick="removeFromCompare('<?php echo $product['product_id']; ?>');" rel="tooltip" data-placement="top" data-original-title="<?php echo $button_compare; ?>">
					<i class="fa fa-copy"></i>
				</a>
				<?php } else { ?>
				<a onclick="addToCompare('<?php echo $product['product_id']; ?>');" rel="tooltip" data-placement="top" data-original-title="<?php echo $button_compare; ?>">
					<i class="fa fa-copy"></i>
				</a>
				<?php } ?>
			</footer>
		</div>
	</article>
	<?php } ?>
</div>
