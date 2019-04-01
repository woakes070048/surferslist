<?php echo $header; ?>
<main class="container-page manufacturers-page">
    <header class="breadcrumb">
        <div class="layout">
            <h1><a href="<?php echo $reset; ?>"><i class="fa fa-ticket"></i><?php echo $heading_title; ?></a></h1>
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
            <div class="widget brand-filter widget-ac-container collapse-medium">
    	        <div class="widget-filter widget-ac">
    	            <h6 class="widget-ac-active"><?php echo $heading_filter; ?><span><i class="fa fa-chevron-down icon-ac-down"></i><i class="fa fa-chevron-up icon-ac-up"></i></span></h6>
                    <div class="ac-content">
                        <div class="content">
                            <div class="filter name-filter" title="<?php echo $help_name; ?>" rel="tooltip" data-container="body">
        						<label for="filter_name" class="hidden"><?php echo $text_filter_name; ?></label>
        						<input type="text" name="filter_name" value="<?php echo $filter_name; ?>" placeholder="<?php echo $text_filter_name; ?>" />
                                <input type="hidden" name="reset_url" value="<?php echo $reset; ?>" />
        					</div>
        					<?php if ($category_manufacturers) { ?>
        					<div class="filter category-filter" title="<?php echo $help_category; ?>" rel="tooltip" data-container="body">
        					  <label for="category-filter" class="hidden"><?php echo $text_filter_category; ?></label>
        					  <select id="category-filter" name="filter_category_id" onchange="filterListings();">
        						<?php foreach ($category_manufacturers as $category_manufacturer) { ?>
        						<?php if ($category_manufacturer['id'] == $filter_category_id) { ?>
        						<option value="<?php echo $category_manufacturer['id']; ?>" selected="selected"><?php echo $category_manufacturer['name']; ?></option>
        						<?php } else { ?>
        						<option value="<?php echo $category_manufacturer['id']; ?>"><?php echo $category_manufacturer['name']; ?></option>
        						<?php } ?>
        						<?php } ?>
        					  </select>
        					</div>
        					<?php } ?>
                            <?php if ($sorts) { ?>
                            <div class="sort" title="<?php echo $help_sort; ?>" rel="tooltip" data-container="body">
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
                            <div class="limit" title="<?php echo $help_limit; ?>" rel="tooltip" data-container="body">
                                <label for="limit" class="hidden"><?php echo $text_limit; ?></label>
                                <select name="limit" onchange="location = this.value;">
                                <?php foreach ($limits as $limit_option) { ?>
                                <?php if ($limit_option['value'] == $limit) { ?>
                                <option value="<?php echo $limit_option['href']; ?>" selected="selected"><?php echo $limit_option['text']; ?></option>
                                <?php } else { ?>
                                <option value="<?php echo $limit_option['href']; ?>"><?php echo $limit_option['text']; ?></option>
                                <?php } ?>
                                <?php } ?>
                              </select>
                            </div>
                        </div>
                        <div class="buttons">
                            <div class="center">
                                <a id="button-filter" onclick="filterListings();" class="button">
                                    <i class="fa fa-filter"></i> <?php echo $button_filter; ?>
                                </a>
                                <div class="product-filter">
                                    <a href="<?php echo $random; ?>" id="random"><i class="fa fa-random"></i><?php echo $text_random; ?></a>
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

                <?php if ($categories) { ?>
                <div id="grid-items-container">
                	<div class="grid global-grid-item">
                	<div class="grid-sizer"></div>
					<?php foreach ($categories as $category) { ?>
						<?php if ($category['manufacturer']) { ?>
							<?php for ($i = 0; $i < count($category['manufacturer']);) { ?>
								<?php $j = $i + ceil(count($category['manufacturer']) / 4); ?>
								<?php for (; $i < $j; $i++) { ?>
									<?php if (isset($category['manufacturer'][$i])) { ?>
                                    <article class="grid-item runmember" data-filter-class='["<?php echo $category["name"]; ?>"]'>
                                        <div class="image">
                                            <?php if ($category['manufacturer'][$i]['image']) { ?>
                                            <a href="<?php echo $category['manufacturer'][$i]['href']; ?>" title="<?php echo $category['manufacturer'][$i]['name']; ?>">
                                                <img src="<?php echo $category['manufacturer'][$i]['image']; ?>" alt="<?php echo $category['manufacturer'][$i]['name']; ?>" />
                                            </a>
                                            <?php } ?>
                            				<span class="description">
                                                <a href="<?php echo $category['manufacturer'][$i]['href']; ?>" title="<?php echo $category['manufacturer'][$i]['name']; ?>">
                                                    <?php if (!empty($category['manufacturer'][$i]['description'])) { ?>
                                					<span class="listings">
                                						<?php echo $category['manufacturer'][$i]['description']; ?>
                                					</span>
                                					<?php } ?>
                                                </a>
                            				</span>
                                        </div>
                                        <div class="pannel">
                                            <div class="info">
                                                <header>
                            						<h3><a href="<?php echo $category['manufacturer'][$i]['href']; ?>" title="<?php echo $category['manufacturer'][$i]['name']; ?>"><?php echo $category['manufacturer'][$i]['name']; ?></a></h3>
                            					</header>
                                            </div>
                                        </div>
                                    </article>
									<?php } ?>
								<?php } ?>
							<?php } ?>
						<?php } ?>
					<?php } ?>
                    </div>
                </div>

                <?php if (isset($pagination)) { ?>
                <div class="pagination"><?php echo $pagination; ?></div>
                <?php } ?>

               <?php } else { ?>

                <div class="global-page manufacturer-page">
					<div class="information">
						<p><?php echo $text_empty; ?></p>
						<span class="icon"><i class="fa fa-info-circle"></i></span>
					</div>
                	<div class="buttons">
                		<div class="left"><a href="<?php echo $back; ?>" class="button button_back"><i class="fa fa-undo"></i><?php echo $button_back; ?></a> <a href="<?php echo $reset; ?>" class="button button_alt button_reset"><i class="fa fa-refresh"></i><?php echo $button_reset; ?></a></div>
                		<div class="right"><a href="<?php echo $search; ?>" class="button button_search"><i class="fa fa-search"></i><?php echo $button_search; ?></a> <a href="<?php echo $continue; ?>" class="button button_alt button_home"><i class="fa fa-home"></i><?php echo $button_continue; ?></a></div>
                	</div>
                </div>
                <?php } ?>

                <?php echo $content_bottom; ?>
            </div>
        </section>
    </div>
</main>
<?php echo $footer; ?>
