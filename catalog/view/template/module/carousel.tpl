<?php if (count($banners) > 1) { ?>
<div class="widget widget-carousel hidden-medium" data-limit="<?php echo $limit; ?>" data-scroll="<?php echo $scroll; ?>">
    <div id="carousel<?php echo $module; ?>">
        <ul class="jcarousel-skin-custom slides">
            <?php foreach ($banners as $banner) { ?>
            <li><a href="<?php echo $banner['link']; ?>"><img src="<?php echo $banner['image']; ?>" alt="<?php echo $banner['title']; ?>" /></a></li>
            <?php } ?>
        </ul>
    </div>
</div>
<?php } else { ?>
<div class="widget hidden-medium">
    <div class="image image-no-border">
        <a href="<?php echo $banners[0]['link']; ?>"><img src="<?php echo $banners[0]['image']; ?>" alt="<?php echo $banners[0]['title']; ?>" /></a>
    </div>
</div>
<?php } ?>
