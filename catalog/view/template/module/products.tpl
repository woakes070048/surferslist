<?php if ($position == 'column_left' || $position == 'column_right') { ?>

<?php foreach ($products as $product) { ?>
	<article class="featured-listing item slideshow-item">
	    <div class="image image-no-border" title="<?php echo $product['name']; ?>" rel="tooltip" data-container="body">
	        <?php if ($product['href']) { ?>
	        <a href="<?php echo $product['href']; ?>"><img src="<?php echo $product['thumb_alt']; ?>" alt="<?php echo $product['name']; ?>" /></a>
	        <?php } else { ?>
	        <img src="<?php echo $product['thumb_alt']; ?>" alt="<?php echo $product['name']; ?>" />
	        <?php } ?>
	        <span class="description">
	            <a href="<?php echo $product['href']; ?>" title="<?php echo $product['description_short']; ?>">
	            <?php if ($product['year'] != '0000') { ?>
	                <?php echo $product['year']; ?>&nbsp;
	            <?php } ?>
	            <?php if ($product['manufacturer_id'] > 1) { ?>
	                <?php echo $product['manufacturer']; ?>&nbsp;
	            <?php } ?>
	            <?php echo $product['model']; ?>&nbsp;<?php echo $product['size']; ?>
	            <?php if ($product['price'] && $product['type_id'] >= 0 ) { ?>
	            <span class="price">
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
	            </span>
	            <?php } ?>
	            </a>
	        </span>
	    </div>
	</article>
<?php } ?>

<?php } ?>

<?php if ($position == 'content_top' || $position == 'content_bottom') { ?>

<?php foreach ($products as $product) { ?>
<article data-filter-class='["<?php if ($product["price"] && $product["special"]) { ?>sale<?php } else { ?>category<?php } ?>"]'
	class="grid-item itemcat module-item<?php if ($product['price'] && $product['featured']) { ?> grid-item-promo<?php } else if ($product["price"] && $product["special"]) { ?> grid-item-sale<?php } ?>">
	<div class="image">
		<a href="<?php echo $product['href']; ?>" title="<?php echo $product['name']; ?>">
			<img src="<?php echo $product['thumb']; ?>" alt="<?php echo $product['name']; ?>" />
		</a>
		<span class="description">
			<a href="<?php echo $product['href']; ?>" title="<?php echo $product['name']; ?>">
			<?php if ($product['year'] != '0000') { ?>
				<?php echo $product['year']; ?>&nbsp;
			<?php } ?>
			<?php if ($product['manufacturer_id'] > 1) { ?>
				<?php echo $product['manufacturer']; ?>&nbsp;
			<?php } ?>
			<?php echo $product['model']; ?>&nbsp;<?php echo $product['size']; ?>
			</a>
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
			<?php if ($product['price'] && $product['quantity'] >= 0) { ?>
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
			<?php if ($product['quantity'] < 0 || $product['customer_id'] == 0) { ?>
			<a href="<?php echo $product['href']; ?>" rel="tooltip" data-placement="top" data-original-title="<?php echo $text_view; ?>">
				<i class="fa fa-info-circle"></i>
			</a>
			<?php } else if ($product['price'] && $product['quantity'] > 0) { ?>
			<a onclick="addToCart('<?php echo $product['product_id']; ?>');" rel="tooltip" data-placement="top" data-original-title="<?php echo $button_cart; ?>">
				<i class="fa fa-shopping-cart"></i>
			</a>
			<?php } else { ?>
			<a onclick="messageProfile('<?php echo $product['customer_id']; ?>','<?php echo $product['product_id']; ?>');" rel="tooltip" data-placement="top" data-original-title="<?php echo $button_contact; ?>">
				<i class="fa fa-envelope"></i>
			</a>
			<?php } ?>
			<a onclick="addToCompare('<?php echo $product['product_id']; ?>');" rel="tooltip" data-placement="top" data-original-title="<?php echo $button_compare; ?>">
				<i class="fa fa-copy"></i>
			</a>
		</footer>
	</div>
</article>
<?php } ?>

<?php } ?>
