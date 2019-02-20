<?php if ($modules) { ?>
<?php if (!$sidebar_exists) {?><aside id="sidebar" class="sidebar container-left"><?php } ?>
    <?php foreach ($modules as $module) { ?>
    <?php echo $module; ?>
    <?php } ?>
<?php if (!$sidebar_exists) {?></aside><?php } ?>
<?php } ?>
