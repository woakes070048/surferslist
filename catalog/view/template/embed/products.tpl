<div id="filterscat">
	<div class="grid global-grid-item">
		<div class="grid-sizer"></div>
		<?php foreach ($products as $product) { ?>
		<article data-filter-class='["<?php if ($product["price"] && $product["special"]) { ?>sale<?php } else { ?>category<?php } ?>"]'
			class="grid-item itemcat<?php if ($product['price'] && $product['featured']) { ?> grid-item-promo<?php } else if ($product["price"] && $product["special"]) { ?> grid-item-sale<?php } ?>">
			<div class="image">
				<img src="<?php echo $product['thumb']; ?>" alt="<?php echo $product['name']; ?>" />
				<span class="description"  title="<?php echo $product['description_short']; ?>">
					<?php if ($product['year'] != '0000') { ?>
						<?php echo $product['year']; ?>&nbsp;
					<?php } ?>
					<?php if ($product['manufacturer_id'] != '1') { ?>
						<?php echo $product['manufacturer']; ?>&nbsp;
					<?php } ?>
					<?php echo $product['model']; ?>&nbsp;<?php echo $product['size']; ?>
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
						<h3><?php echo $product['name']; ?></h3>
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
			</div>
		</article>
		<?php } ?>
	</div>
</div>
