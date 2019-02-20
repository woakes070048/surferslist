<div class="widget widget-categories widget-ac-container hidden-medium">
    <div class="widget-ac">
        <h6 class="widget-ac-active"><?php echo $heading_title; ?><span><i class="fa fa-chevron-down icon-ac-down"></i><i class="fa fa-chevron-up icon-ac-up"></i></span></h6>
        <div class="ac-content">
            <ul class="widget-list">
                <?php foreach ($categories as $category_1) { ?>
                <li<?php if ($category_1['children']) { ?> class="hbox has-menu"<?php } ?>>
                    <?php if ($category_1['id'] == $category_id) { ?>
                    <a class="active<?php if ($category_1['children']) { ?> active-arrow<?php } ?>" href="<?php echo $category_1['href']; ?>">
                        <?php if ($category_1['image']) { ?>
                        <span class="category-icon icon-<?php echo $category_1['alt']; ?>"></span>
                        <?php } ?>
                        <?php echo $category_1['name']; ?>
                    </a>
                    <?php if ($category_1['children']) { ?>
                    <ul class="widget-list-child">
                        <?php foreach ($category_1['children'] as $category_2) { ?>
                        <li<?php if ($category_2['children']) { ?> class="hbox has-menu"<?php } ?>>
                            <a href="<?php echo $category_2['href']; ?>"
                                title="<?php echo $category_2['alt']; ?>"
                                class="<?php if ($category_2['children']) { ?>heading<?php } ?><?php if ($category_2['id'] == $child_id) { ?> active-child<?php } ?>">
                                <?php echo $category_2['name']; ?>
                            </a>
                            <?php if ($category_2['children']) { ?>
                            <ul class="widget-list-child list-children">
                                <?php foreach ($category_2['children'] as $category_3) { ?>
                                <li><a href="<?php echo $category_3['href']; ?>" title="<?php echo $category_3['alt']; ?>"><?php echo $category_3['name']; ?></a></li>
                                <?php } ?>
                            </ul>
                            <?php } ?>
                            <?php } ?>
                    </ul>
                    <?php } ?>
                    <?php } else { ?>
                    <?php if ($category_1['children']) { ?>
                    <a href="<?php echo $category_1['href']; ?>" class="heading">
                        <?php if ($category_1['image']) { ?>
                        <span class="category-icon icon-<?php echo $category_1['alt']; ?>"></span>
                        <?php } ?>
                        <?php echo $category_1['name']; ?>
                    </a>
                    <ul class="widget-list-child list-children">
                        <?php foreach ($category_1['children'] as $category_2) { ?>
                        <li<?php if ($category_2['children']) { ?> class="hbox has-menu"<?php } ?>>
                            <a href="<?php echo $category_2['href']; ?>"
                                title="<?php echo $category_2['alt']; ?>"
                                class="<?php if ($category_2['children']) { ?>heading<?php } ?><?php if ($category_2['id'] == $child_id) { ?> active-child<?php } ?>">
                                <?php echo $category_2['name']; ?>
                            </a>
                            <?php if ($category_2['children']) { ?>
                            <ul class="widget-list-child list-children">
                                <?php foreach ($category_2['children'] as $category_3) { ?>
                                <li><a href="<?php echo $category_3['href']; ?>" title="<?php echo $category_3['alt']; ?>"><?php echo $category_3['name']; ?></a></li>
                                <?php } ?>
                            </ul>
                            <?php } ?>
                        </li>
                        <?php } ?>
                    </ul>
                    <?php } else { ?>
                    <a href="<?php echo $category_1['href']; ?>">
                        <?php if ($category_1['image']) { ?>
                        <span class="category-icon icon-<?php echo $category_1['alt']; ?>"></span>
                        <?php } ?>
                        <?php echo $category_1['name']; ?>
                    </a>
                    <?php } ?>
                    <?php } ?>
                    </li>
                    <?php } ?>
            </ul>
        </div>
    </div>
</div>
