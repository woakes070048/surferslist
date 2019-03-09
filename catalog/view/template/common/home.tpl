<?php echo $header; ?>
<main class="container-page home-page">
    <?php if ($content_top) { ?>
    <div class="container-top">
        <?php echo $content_top; ?>
    </div>
    <?php } ?>
    <div class="layout">
        <?php echo $column_right; ?>
        <?php echo $column_left; ?>
        <section class="container-center">
            <div class="content-page">
                <?php echo $notification; ?>
            </div>
        </section>
    </div>
    <div class="container-bottom">
        <?php echo $content_bottom; ?>

        <div class="widget widget-search">
        	<form action="<?php echo $search; ?>" method="post" enctype="multipart/form-data" id="searchwidget">
        		<h1><a href="<?php echo $product_random; ?>"><?php echo $heading_search; ?></a></h1>

        		<div class="content clearafter">

        			<div class="grid-1 clearafter">
        				<div class="grid-8">
        					<div class="grid-2">
        						<div class="formbox">
        							<p class="form-label show-small hidden"><strong><?php echo $entry_search; ?></strong></p>
                                    <input type="text" name="search" value="" placeholder="<?php echo $help_search; ?>" />
        						</div>
        					</div>

        					<div class="grid-2">
        						<div class="formbox">
        							<p class="form-label show-small hidden"><strong><?php echo $heading_category; ?></strong></p>
        							<select name="category_id" class="no-cascade" title="<?php echo $help_category; ?>" rel="tooltip">
                                        <option value="0" selected="selected"><?php echo $text_category; ?></option>
                                        <?php foreach ($categories as $category) { ?>
                                        <option value="<?php echo $category['category_id']; ?>" data-url="<?php echo $category['url']; ?>"><?php echo $category['name']; ?></option>
                                        <?php } ?>
        							</select>
        						</div>
        					</div>
        				</div>

        				<div class="grid-4 searchwidget-buttons hidden-medium">
        					<div class="formbox">
        						<input id="button-search" type="submit" value="<?php echo $button_search; ?>" class="button hidden" />
        						<label for="button-search" class="button button-submit button_highlight"><i class="fa fa-search"></i><?php echo $button_search; ?></label>
        					</div>
        				</div>
        			</div>

                    <div class="grid-1 searchwidget-buttons show-medium">
                        <div class="formbox">
        					<label for="button-search" class="button button-submit button_highlight"><i class="fa fa-search"></i><?php echo $button_search; ?></label>
        				</div>
        			</div>

                    <div class="grid-1 clearafter">
                        <div class="grid-8">
                            <span class="formbox label">
                                <input type="checkbox" name="forsale" value="1" id="forsale" /> <label for="forsale" title="<?php echo $help_forsale; ?>" rel="tooltip" data-placement="right"><em><?php echo $text_forsale; ?></em></label>
                                <a href="<?php echo $search . '?more=true'; ?>" class="button-more-options float-right" title="<?php echo $help_more_options; ?>" rel="tooltip" data-placement="left">
                                    <i class="fa fa-plus-circle"></i><?php echo $text_more_options; ?>
                                </a>
                            </span>
                        </div>

                        <div class="grid-4 ">
                            <span class="label float-right hidden-medium">
                                <a href="<?php echo $about; ?>" title="<?php echo $help_about; ?>" rel="tooltip" data-placement="left">
                                    <i class="fa fa-question-circle-o"></i>
                                </a>
                            </span>
                        </div>
                    </div>

                </div>
                
                <input type="hidden" name="country_id" value="<?php echo $country_id; ?>" />
                <input type="hidden" name="zone_id" value="<?php echo $zone_id; ?>" />
                <input type="hidden" name="location" value="<?php echo $location; ?>" />
        	</form>
        </div>

        <div class="home-post">
            <div class="content">
                <div class="buttons">
                    <div class="center">
                        <a class="button button-post button_secondary bigger" href="<?php echo $post; ?>" title="<?php echo $help_post; ?>" rel="tooltip" data-container="body" data-placement="top">
                            <i class="fa fa-pencil"></i><?php echo $button_post; ?>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
<?php echo $footer; ?>
