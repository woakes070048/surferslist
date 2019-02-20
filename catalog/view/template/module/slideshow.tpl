<div id="slider<?php echo $module; ?>" class="slideshow widget widget-slideshow">
	<div class="loader"></div>
	<div class="slideshow-carousel">
        <?php foreach ($banners as $banner) { ?>
			<?php if ($banner['link']) { ?>
			<article class="item slideshow-item">
				<a href="<?php echo $banner['link']; ?>">
					<header class="slideshow-heading">
						<h1 class="slideshow-title slideshow-title-position-<?php echo $banner['position']; ?>"><?php echo $banner['title']; ?></h1>
					</header>
	                <img data-lazy="<?php echo $banner['image']; ?>"
	                    alt="<?php echo $banner['title']; ?>"
	                    title="<?php echo $banner['title']; ?>" />
	            </a>
			</article>
			<?php } else { ?>
			<div class="item slideshow-item">
				<div class="slideshow-heading">
					<h2 class="slideshow-title slideshow-title-position-<?php echo $banner['position']; ?>"><?php echo $banner['title']; ?></h2>
				</div>
                <img data-lazy="<?php echo $banner['image']; ?>"
                    alt="<?php echo $banner['title']; ?>"
                    title="<?php echo $banner['title']; ?>" />
	        </div>
			<?php } ?>
        <?php } ?>
    </div>
    <input type="hidden" name="slideshow_options" value="<?php echo htmlspecialchars(json_encode($options), ENT_COMPAT); ?>" />
</div>
