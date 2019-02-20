<div id="quickViewContent">
	<div class="container-center">
		<div class="content-page">
			<div class="left-item">
				<div class="thumb<?php if ($featured) { ?> promo<?php } ?>">
					<?php if ($price && $type_id >= 0 && $special) { ?><span class="sale-badges">-<?php echo $salebadges; ?>&#37;</span><?php } ?>
					<?php if ($featured) { ?><i class="icon icon-tag promo text-larger"></i><?php } ?>
					<a href="<?php echo $product_href; ?>" target="_blank"><img src="<?php echo $thumb; ?>" alt="<?php echo $heading_title; ?>" /></a>
				</div>
			</div>
			<div class="right-item">
				<div class="product-info">
					<ul class="global-attribute">
						<li><b><?php echo $text_name; ?></b> <i><h1><?php echo $heading_title; ?></h1></i></li>
						<?php if ($price && $type_id >= 0) { ?>
						<li class="price"><b><?php echo $text_price; ?></b>
							<i><div class="all-price">
								<?php if (!$special) { ?>
								<?php echo $price; ?>
								<?php } else { ?>
								<span class="price-old"><span class="span1">&nbsp;</span><span class="span2">&nbsp;</span> <?php echo $price; ?></span>
								<?php echo $special; ?>
								<?php } ?>
							</div>
							<?php if ($tax) { ?>
							<span class="others"><?php echo $text_tax; ?> <?php echo $tax; ?></span>
							<?php } ?>
							<?php if ($points) { ?>
							<span class="others"><?php echo $text_points; ?> <?php echo $points; ?></span>
							<?php } ?>
							<?php if ($discounts) { ?>
							<span class="others">
							<?php foreach ($discounts as $discount) { ?>
							<?php echo sprintf($text_discount, $discount['quantity'], $discount['price']); ?><br />
							<?php } ?>
							</span>
							<?php } ?></i></li>
						<?php } ?>
						<?php if (false && $price && $type_id >= 0) { ?>
						<li><b><?php echo $text_type; ?></b> <i><?php echo $type; ?></i></li>
						<?php } ?>
						<?php if ($manufacturer) { ?>
						<?php if ($manufacturer_image) { ?>
						<li class="manufacturer-logo"><b><?php echo $text_manufacturer; ?></b> <i><a href="<?php echo $manufacturer_href; ?>"><img src="<?php echo $manufacturer_image; ?>" alt="<?php echo $manufacturer; ?>" rel="tooltip" data-placement="top" data-original-title="<?php echo $manufacturer; ?>" /></a></i></li>
						<?php } else { ?>
						<li><b><?php echo $text_manufacturer; ?></b> <i><a href="<?php echo $manufacturer_href; ?>"><?php echo $manufacturer; ?></a></i></li>
						<?php } ?>
						<?php } ?>
						<li><b><?php echo $text_model; ?></b> <i><?php echo $model; ?></i></li>
						<li><b><?php echo $text_size; ?></b> <i><?php echo $size; ?></i></li>
						<li><b><?php echo $text_year; ?></b> <i><?php echo $year; ?></i></li>
						<?php if ($condition) { ?>
						<li><b><?php echo $text_condition; ?></b> <i><?php echo $condition; ?></i></li>
						<?php } ?>
						<?php if ($price != $text_free && $type_id < 0) { ?>
						<li><b><?php echo $text_value; ?></b> <i><?php echo $price; ?></i></li>
						<?php } ?>
						<?php if ($categories) { ?>
						<li><b><?php echo $text_category; ?></b><i>
							<?php for ($i = 0; $i < count($categories); $i++) { ?>
								<?php if ($i < (count($categories) - 1)) { ?>
								<a href="<?php echo $categories[$i]['href']; ?>"><?php echo $categories[$i]['name']; ?></a>,
								<?php } else { ?>
								<a href="<?php echo $categories[$i]['href']; ?>"><?php echo $categories[$i]['name']; ?></a>
								<?php } ?>
							<?php } ?></i></li>
						<?php } ?>
						<?php if (false && ($this->config->get('member_status')) && $member) { ?>
						<li><b><?php echo $text_member; ?></b>
							<i><?php if ($member['image']) { ?><a href="<?php echo $member['href']; ?>"><img src="<?php echo $member['image']; ?>" title="<?php echo $member['name']; ?>" alt="<?php echo $member['name']; ?>" /></a>&nbsp;<?php } ?>
							<a href="<?php echo $member['href']; ?>"><?php echo $member['name']; ?></a>&nbsp;
							<div class="rating-stars view-star<?php echo $member['rating']; ?>">
								<i class="fa fa-star icon_star star-color color1"></i>
								<i class="fa fa-star icon_star star-color color2"></i>
								<i class="fa fa-star icon_star star-color color3"></i>
								<i class="fa fa-star icon_star star-color color4"></i>
								<i class="fa fa-star icon_star star-color color5"></i>
								<i class="fa fa-star icon_star star-dark dark1"></i>
								<i class="fa fa-star icon_star star-dark dark2"></i>
								<i class="fa fa-star icon_star star-dark dark3"></i>
								<i class="fa fa-star icon_star star-dark dark4"></i>
								<i class="fa fa-star icon_star star-dark dark5"></i>
							</div></i></li>
						<?php } ?>
						<li><b><?php echo $text_location; ?></b> <i><?php echo !empty($location) ? $location . ' - ' : ''; ?><?php echo $location_zone . ', ' . $location_country; ?></i></li>
						<?php if ($price && $type_id >= 0) { ?>
						<li><b><?php echo $text_shipping; ?></b> <i><?php echo $shipping; ?></i></li>
						<?php } ?>
						<?php if ($tags) { ?>
						<li><b><?php echo $text_tags; ?></b> <i><?php for ($i = 0; $i < count($tags); $i++) { ?><?php if ($i < (count($tags) - 1)) { ?><a href="<?php echo $tags[$i]['href']; ?>"><?php echo $tags[$i]['tag']; ?></a>, <?php } else { ?><a href="<?php echo $tags[$i]['href']; ?>"><?php echo $tags[$i]['tag']; ?></a><?php } ?><?php } ?></i></li>
						<?php } ?>
						<?php if (false && $reward) { ?>
						<li><b><?php echo $text_reward; ?></b> <i><?php echo $reward; ?></i></li>
						<?php } ?>
					</ul>
					<div class="item-description">
						<?php echo $description; ?>
					</div>
				</div>
				<div class="buttons buttons-middle">
					<div class="center">
						<a href="<?php echo $product_href; ?>" target="_blank" class="button button_alt bolder" rel="tooltip" data-placement="top" data-original-title="<?php echo $button_view . ' ' . $text_product; ?>">
							<i class="fa fa-eye"></i><?php echo $button_view . ' ' . $text_product; ?>
						</a>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
