<?php echo $header; ?>
<main class="container-page special-page">
    <header class="breadcrumb">
        <div class="layout">
            <h1><a href="<?php echo $reset; ?>"><i class="fa fa-money"></i><?php echo $heading_title; ?></a></h1>
            <div class="links">
                <?php foreach ($breadcrumbs as $breadcrumb) { ?>
                <?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
                <?php } ?>
            </div>
        </div>
    </header>
    <div class="layout">
        <aside id="sidebar" class="sidebar container-left">
            <?php echo $column_left; ?>
	        <div class="widget listing-filter widget-ac-container collapse-medium">
                <div class="widget-filter widget-ac">
	                <h6 class="widget-ac-active"><?php echo $heading_filter_listings; ?><span><i class="fa fa-chevron-down icon-ac-down"></i><i class="fa fa-chevron-up icon-ac-up"></i></span></h6>
                    <div class="ac-content">
                        <div class="content">
                            <div class="sort" title="<?php echo $help_sort; ?>" rel="tooltip" data-container="body">
        					    <label for="sort" class="hidden"><?php echo $text_sort; ?></label>
                                <select name="sort" onchange="location = this.value;">
                                    <?php foreach ($sorts as $sort_data) { ?>
                                    <?php if ($sort_data['value'] == $sort . '-' . $order) { ?>
                                    <option value="<?php echo $sort_data['href']; ?>" selected="selected"><?php echo $sort_data['text']; ?></option>
                                    <?php } else { ?>
                                    <option value="<?php echo $sort_data['href']; ?>"><?php echo $sort_data['text']; ?></option>
                                    <?php } ?>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="limit" title="<?php echo $help_limit; ?>" rel="tooltip" data-container="body">
        					    <label for="limit" class="hidden"><?php echo $text_limit; ?></label>
                                <select name="limit" onchange="location = this.value;">
                                    <?php foreach ($limits as $limit_data) { ?>
                                    <?php if ($limit_data['value'] == $limit) { ?>
                                    <option value="<?php echo $limit_data['href']; ?>" selected="selected"><?php echo $limit_data['text']; ?></option>
                                    <?php } else { ?>
                                    <option value="<?php echo $limit_data['href']; ?>"><?php echo $limit_data['text']; ?></option>
                                    <?php } ?>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="buttons">
                            <div class="center">
                                <a id="button-reset" href="<?php echo $reset; ?>" class="button">
                                    <i class="fa fa-refresh"></i> <?php echo $button_reset; ?>
                                </a>
                                <div class="product-filter">
                                    <a href="<?php echo $random; ?>" id="random"><i class="fa fa-random"></i><?php echo $text_random; ?></a>
                                    <a class="product-compare<?php if (!empty($this->session->data['compare'])) { ?> active<?php } ?>" href="<?php echo $compare; ?>"><i class="fa fa-copy"></i><span id="compare-total"><?php echo $text_compare; ?></span></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php echo $column_right; ?>
        </aside>
        <section class="container-center">
            <div class="content-page">
                <?php echo $notification; ?>
                <?php echo $content_top; ?>
                <?php require_once('products.inc.php'); ?>
                <?php echo $content_bottom; ?>
            </div>
        </section>
    </div>
</main>
<?php echo $footer; ?>
