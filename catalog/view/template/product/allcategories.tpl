<?php echo $header; ?>
<main class="container-page categories-page">
    <header class="breadcrumb">
        <div class="layout">
            <h1><a href="<?php echo $reset; ?>"><i class="fa fa-sitemap"></i><?php echo $heading_title; ?></a></h1>
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
	        <div class="widget categories-filter widget-ac-container collapse-medium">
                <div class="widget-filter widget-ac">
                    <h6 class="widget-ac-active"><?php echo $heading_filter_categories; ?><span><i class="fa fa-chevron-down icon-ac-down"></i><i class="fa fa-chevron-up icon-ac-up"></i></span></h6>
                    <div class="ac-content">
                        <div class="content">
        					<?php if ($indexes) { ?>
                            <div class="filter category-filter" title="<?php echo $help_category; ?>" rel="tooltip" data-container="body">
            					<ul class="filter-listings">
            						<?php foreach ($indexes as $index) { ?>
                                        <li data-filter="<?php echo $index['name']; ?>"><?php echo $index['name']; ?></li>
                                    <?php } ?>
            					</ul>
                            </div>
        					<?php } ?>
                            <?php if ($sorts) { ?>
                            <div class="sort fullwidth" title="<?php echo $help_sort; ?>" rel="tooltip" data-container="body">
                                <label for="sort" class="hidden"><?php echo $text_sort; ?></label>
                                <select name="sort" onchange="location = this.value;">
                                    <?php foreach ($sorts as $sort_option) { ?>
                                    <?php if ($sort_option['value'] == $sort . '-' . $order) { ?>
                                    <option value="<?php echo $sort_option['href']; ?>" selected="selected"><?php echo $sort_option['text']; ?></option>
                                    <?php } else { ?>
                                    <option value="<?php echo $sort_option['href']; ?>"><?php echo $sort_option['text']; ?></option>
                                    <?php } ?>
                                    <?php } ?>
                                </select>
                            </div>
                            <?php } ?>
                        </div>
                        <div class="buttons">
                            <div class="center">
                                <a href="<?php echo $reset; ?>" class="button button_alt button_reset"><i class="fa fa-refresh"></i><?php echo $button_reset; ?></a>
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

				<?php if ($indexes) { ?>
				<div class="listing-items-container">
                	<div class="progress-bar"></div>
                	<div class="information loading">
                		<p><?php echo $text_loading; ?></p>
                		<span class="icon"><i class="fa fa-spin fa-circle-o-notch icon-spin"></i></span>
                	</div>
					<ul class="listing-items brands-list">
						<?php foreach ($indexes as $index) { ?>
							<?php if ($index['name']) { ?>
								<?php for ($i = 0; $i < count($index['category']);) { ?>
									<?php $j = $i + ceil(count($index['category']) / 4); ?>
									<?php for (; $i < $j; $i++) { ?>
										<?php if (isset($index['category'][$i])) { ?>
										<li class="listing-item grid-item" data-filter-class='["<?php echo $index['name']; ?>"]'>
											<a href="<?php echo $index['category'][$i]['href']; ?>">
                                                <span class="category-icon icon-<?php echo $index['category'][$i]['icon']; ?>"></span>
												<h2><?php echo $index['category'][$i]['name']; ?></h2>
											</a>
										</li>
										<?php } ?>
									<?php } ?>
								<?php } ?>
							<?php } ?>
						<?php } ?>
					</ul>
				</div>
                <?php } else if ($categories) { ?>
                <div class="global-page">
					<div class="product-grid category-grid">
						<?php foreach ($categories as $category) { ?>
						<div>
							<?php if ($this->config->get('apac_categories_display_images') && $category['thumb']) { ?>
							<div class="image">
                                <a href="<?php echo $category['href']; ?>">
                                    <span class="category-icon icon-<?php echo $category['icon']; ?>"></span>
                                </a>
                            </div>
							<?php } ?>
							<div class="name">
                                <a href="<?php echo $category['href']; ?>"><?php echo $category['name']; ?></a>
                            </div>
						</div>
						<?php } ?>
					</div>
				</div>
				<?php } ?>

                <?php echo $content_bottom; ?>
            </div>
        </section>
    </div>
</main>
<?php echo $footer; ?>
