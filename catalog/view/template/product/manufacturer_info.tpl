<?php echo $header; ?>
<main class="container-page manufacturer-page">
    <header class="breadcrumb">
        <div class="layout">
            <h1><a href="<?php echo $action; ?>"><i class="fa fa-ticket"></i><?php echo $heading_title; ?></a></h1>
            <div class="links">
                <?php foreach ($breadcrumbs as $breadcrumb) { ?>
                <?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
                <?php } ?>
            </div>
        </div>
    </header>
    <div class="layout">
        <section id="brand-listings" class="container-bottom">
            <aside id="sidebar" class="sidebar container-left">
                <?php echo $column_left; ?>
                <?php if (!empty($brand)) { ?>
                <div id="brand-profile" class="widget hidden-medium">
                    <h6><?php echo $heading_brand; ?></h6>
                    <div class="content brand-profile">
                        <div class="image image-border" title="<?php echo $brand['name']; ?>" rel="tooltip" data-container="body">
                            <?php if ($brand['href']) { ?>
                            <a href="<?php echo $brand['href']; ?>" target="_blank"><img src="<?php echo $brand['image']; ?>" title="<?php echo $brand['name']; ?>" alt="<?php echo $brand['name']; ?>" /></a>
                            <?php } else { ?>
                            <img src="<?php echo $brand['image']; ?>" title="<?php echo $brand['name']; ?>" alt="<?php echo $brand['name']; ?>" />
                            <?php } ?>
                        </div>

                        <h2 class="brand-name hidden">
                            <a href="<?php echo $brand['href']; ?>" target="_blank"><?php echo $brand['name']; ?></a>
                        </h2>

                        <ul class="global-attribute hidden">
                            <?php if ($brand['href']) { ?>
                            <li><i title="<?php echo $text_manufacturer_website; ?>"><a href="<?php echo $brand['href']; ?>" target="_blank"><?php echo $brand['href']; ?></a></i></li>
                            <?php } ?>
                            <?php if ($brand['description']) { ?>
                            <li><i title="<?php echo $text_manufacturer_description; ?>"><?php echo $brand['description']; ?></i></li>
                            <?php } ?>
                        </ul>

                        <?php if ($manufacturers_active) { ?>
                        <div class="filter manufacturer-filter" title="<?php echo $help_select_manufacturer; ?>" rel="tooltip" data-container="body">
                          <label for="manufacturer-filter" class="hidden"><?php echo $text_select_manufacturer; ?></label>
                          <select name="manufacturer-filter" id="manufacturer-filter" onchange="location = this.value;">
                            <?php foreach ($manufacturers_active as $manufacturer_active) { ?>
                            <?php if ($manufacturer_active['id'] == $manufacturer_id) { ?>
                            <option value="<?php echo $manufacturer_active['href']; ?>" data-filter="<?php echo $manufacturer_active['id']; ?>" selected="selected"><?php echo $manufacturer_active['name']; ?></option>
                            <?php } else { ?>
                            <option value="<?php echo $manufacturer_active['href']; ?>" data-filter="<?php echo $manufacturer_active['id']; ?>"><?php echo $manufacturer_active['name']; ?></option>
                            <?php } ?>
                            <?php } ?>
                          </select>
                        </div>
                        <?php } ?>
                    </div>
                    <div class="buttons">
                        <div class="center">
                            <a href="<?php echo $brand['href']; ?>" target="_blank" class="button button_alt"><i class="fa fa-link"></i><?php echo $text_manufacturer_website; ?></a>
                        </div>
                    </div>
                </div>
                <?php } ?>
                <?php echo $refine; ?>
                <?php echo $column_right; ?>
            </aside>
            <div class="container-center">
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
            </div>
        </section>
    </div>
</main>
<?php echo $footer; ?>
