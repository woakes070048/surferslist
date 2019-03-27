<div class="global-page">
    <div class="information">
        <p><?php echo $text_empty; ?></p>
        <span class="icon"><i class="fa fa-info-circle"></i></span>
    </div>
    <div class="buttons">
        <div class="left">
            <a href="<?php echo $back; ?>" class="button button_back"><i class="fa fa-undo"></i><?php echo $button_back; ?></a>
            <?php if ($reset) { ?>
            <a href="<?php echo $reset; ?>" class="button button_alt button_reset"><i class="fa fa-refresh"></i><?php echo $button_reset; ?></a>
            <?php } ?>
        </div>
        <div class="right">
            <a href="<?php echo $continue; ?>" class="button button_alt button_home"><i class="fa fa-home"></i><?php echo $text_blog; ?></a>
        </div>
    </div>
</div>
