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
                                    <a class="product-compare<?php if (!empty($this->session->data['compare'])) { ?> active<?php } ?> compare-total" href="<?php echo $compare; ?>"><?php echo $text_compare; ?></a>
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

                <?php if ($products) { ?>

                <div id="grid-items-container">

            		<?php echo $products; ?>

                	<?php if ($more) { ?>
                	<div class="buttons">
                		<div class="center">
                			<a class="button button_highlight button_more bigger load-more icon" href="<?php echo $more; ?>"><i class="fa fa-chevron-down"></i><?php echo $text_more; ?></a>
                		</div>
                	</div>
                	<?php } ?>
                </div>

                <?php if (isset($pagination)) { ?>
                <div class="pagination"><?php echo $pagination; ?></div>
                <?php } ?>

                <?php } else { ?>

                <div class="global-page">
                	<div class="information">
                		<p><?php echo $text_empty; ?></p>
                		<span class="icon"><i class="fa fa-info-circle"></i></span>
                	</div>
                	<div class="buttons">
                		<div class="left">
                			<a href="<?php echo $back; ?>" class="button button_back"><i class="fa fa-undo"></i><?php echo $button_back; ?></a>
                			<?php if (!empty($url)) { ?>
                			<a href="<?php echo $reset; ?>" class="button button_alt button_reset"><i class="fa fa-refresh"></i><?php echo $button_reset; ?></a>
                			<?php } ?>
                		</div>
                		<div class="right">
                			<a href="<?php echo $search; ?>" class="button button_search"><i class="fa fa-search"></i><?php echo $button_search; ?></a>
                			<a href="<?php echo $continue; ?>" class="button button_alt button_home"><i class="fa fa-home"></i><?php echo $button_continue; ?></a>
                		</div>
                	</div>
                </div>

                <?php } ?>

                <?php echo $content_bottom; ?>
            </div>
        </section>
    </div>
</main>
<?php echo $footer; ?>
