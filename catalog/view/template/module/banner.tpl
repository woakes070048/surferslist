<?php if (count($banners) > 1) { ?>
<div id="banner<?php echo $module; ?>" class="widget widget-banner hidden-medium">
    <div class="banner" style="width:<?php echo $width . 'px'; ?>;height:<?php echo $height . 'px'; ?>">
        <?php foreach ($banners as $banner) { ?>
        <?php if ($banner['link']) { ?>
        <div><a href="<?php echo $banner['link']; ?>"><img src="<?php echo $banner['image']; ?>" alt="" /></a></div>
        <?php } else { ?>
        <div><img src="<?php echo $banner['image']; ?>" alt="" /></div>
        <?php } ?>
        <?php } ?>
    </div>
    <input type="hidden" name="cycle_options" value="<?php echo htmlspecialchars(json_encode($options), ENT_COMPAT); ?>" />
</div>
<?php } else { ?>
<div class="widget hidden-medium">
    <div class="banner" style="width:<?php echo $width . 'px'; ?>;height:<?php echo $height . 'px'; ?>">
        <div class="image image-no-border">
            <a href="<?php echo $banners[0]['link']; ?>"><img src="<?php echo $banners[0]['image']; ?>" alt="<?php echo $banners[0]['title']; ?>" /></a>
        </div>
    </div>
</div>
<?php } ?>
