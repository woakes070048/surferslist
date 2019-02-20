<?php echo $header; ?>
<main class="container-page search-page">
    <header class="breadcrumb">
        <div class="layout">
            <h1><a href="<?php echo $search; ?>"><i class="fa fa-search"></i><?php echo $heading_title; ?></a></h1>
            <div class="links">
                <?php foreach ($breadcrumbs as $breadcrumb) { ?>
                <?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
                <?php } ?>
            </div>
        </div>
    </header>
    <div class="layout">
        <?php echo $column_left; ?>
        <section class="container-center">
            <div class="content-page">
                <?php echo $notification; ?>
                <?php echo $content_top; ?>

				<?php if (!$no_search) { ?>

                <div class="global-page">
                    <div class="product-filter">
                        <div class="sort" title="<?php echo $help_sort; ?>" rel="tooltip" data-container="body">
						    <label for="sort" class="hidden"><?php echo $text_sort; ?></label>
                            <select name="sort" onchange="location = this.value;">
                                <?php foreach ($sorts as $sorts) { ?>
                                <?php if ($sorts['value'] == $sort . '-' . $order) { ?>
                                <option value="<?php echo $sorts['href']; ?>" selected="selected"><?php echo $sorts['text']; ?></option>
                                <?php } else { ?>
                                <option value="<?php echo $sorts['href']; ?>"><?php echo $sorts['text']; ?></option>
                                <?php } ?>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="limit" title="<?php echo $help_limit; ?>" rel="tooltip" data-container="body">
						    <label for="limit" class="hidden"><?php echo $text_limit; ?></label>
                            <select name="limit" onchange="location = this.value;">
                                <?php foreach ($limits as $limits) { ?>
                                <?php if ($limits['value'] == $limit) { ?>
                                <option value="<?php echo $limits['href']; ?>" selected="selected"><?php echo $limits['text']; ?></option>
                                <?php } else { ?>
                                <option value="<?php echo $limits['href']; ?>"><?php echo $limits['text']; ?></option>
                                <?php } ?>
                                <?php } ?>
                            </select>
                        </div>
                        <a href="<?php echo $random; ?>" id="random" class="text-grey" title="<?php echo $help_random; ?>" rel="tooltip" data-container="body"><i class="fa fa-random"></i><?php echo $text_random; ?></a>
                        <a class="product-compare<?php if (!empty($this->session->data['compare'])) { ?> active<?php } ?>" title="<?php echo $button_compare; ?>" rel="tooltip" href="<?php echo $compare; ?>"><i class="fa fa-copy"></i><span id="compare-total"><?php echo $text_compare; ?></span></a>
                    </div>
                </div>

				<?php require_once('products.inc.php'); ?>

				<?php } else { ?>

				<div class="widget">
                    <div class="content">
    					<div class="information">
    						<p><?php echo $text_complete; ?></p>
    						<span class="icon"><i class="fa fa-info-circle"></i></span>
    					</div>
                    </div>
					<div class="buttons">
						<div class="left">
                            <a href="<?php echo $back; ?>" class="button button_back"><i class="fa fa-th-list"></i><?php echo $button_browse; ?></a>
                        </div>
						<div class="right">
                            <a href="<?php echo $continue; ?>" class="button button_alt button_home"><i class="fa fa-home"></i><?php echo $button_continue; ?></a>
                        </div>
					</div>
				</div>

				<?php } ?>

                <?php echo $content_bottom; ?>
            </div>
        </section>
        <?php echo $column_right; ?>
    </div>
</main>
<?php echo $footer; ?>
