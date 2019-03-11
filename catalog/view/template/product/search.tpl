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
                        <a class="product-compare<?php if (!empty($this->session->data['compare'])) { ?> active<?php } ?> compare-total" title="<?php echo $button_compare; ?>" rel="tooltip" href="<?php echo $compare; ?>"><?php echo $text_compare; ?></a>
                    </div>
                </div>

                <?php if ($products) { ?>

                <div id="filterscat">

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

                	<div class="parameters">
                		<h3><?php echo $heading_params; ?></h3>
                		<ul class="global-attribute">
                			<?php foreach ($params as $param) { ?>
                			<li><b><?php echo $param['name']; ?>:</b><i class="param-value" data-field="<?php echo $param['field']; ?>" data-value="<?php echo $param['value']; ?>"></i></li>
                			<?php } ?>
                		</ul>
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
