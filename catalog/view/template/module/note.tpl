<div class="widget">
    <?php if ($note_box_heading) { ?>
    <h6><?php echo $note_box_heading; ?></h6>
    <?php } ?>
    <div class="content <?php if ($note_box_heading) { ?>modules-html<?php } else { ?>modules-html widget-no-heading<?php } ?>">
    	<div class="note">
            <?php if ($note_image && $image_location == 'top') { ?>
            <div class="note-image">
                <img src="image/<?php echo $note_image; ?>" alt="<?php echo $note_box_heading; ?>" />
            </div>
            <?php } ?>
            <?php if ($note_content) { ?>
            <div class="note-content">
                <?php if ($note_image && ($image_location == 'left' || $image_location == 'right')) { ?>
                <div class="note-image-<?php echo $image_location; ?>">
                    <img src="image/<?php echo $note_image; ?>" alt="<?php echo $note_box_heading; ?>" />
                </div>
                <?php } ?>
                <?php echo $note_content; ?>
            </div>
            <?php } ?>
            <?php if ($note_image && $image_location == 'bottom') { ?>
            <div class="note-image">
                <img src="image/<?php echo $note_image; ?>" alt="<?php echo $note_box_heading; ?>" />
            </div>
            <?php } ?>
        </div>
    </div>
    <?php if ($note_button) { ?>
    <div class="buttons">
        <div class="<?php echo $button_position; ?>">
            <a class="button" href="<?php echo $note_url; ?>"><?php echo $note_button; ?></a>
        </div>
    </div>
    <?php } ?>
</div>
