<?php echo $header; ?>
<main class="container-page home-page">
    <?php if ($notification) { ?>
    <div class="layout">
        <section class="container-center">
            <div class="content-page">
                <?php echo $notification; ?>
            </div>
        </section>
    </div>
    <?php } ?>
    <div class="container-top">
        <div class="content-page">
            <?php echo $content_top; ?>
            <div class="widget widget-home">
                <form action="<?php echo $post; ?>" method="post" enctype="multipart/form-data">
            		<h2><a href="<?php echo $post; ?>"><?php echo $heading_post; ?></a></h2>

            		<div class="content clearafter">

            			<div class="grid-1 clearafter">
            				<div class="grid-8">
                                <div class="formbox">
                                    <p class="form-label show-small hidden"><strong><?php echo $entry_link; ?></strong></p>
                                    <input type="text"
                                        name="link"
                                        value=""
                                        placeholder="<?php echo $text_link; ?>"
                                        title="<?php echo $entry_link; ?>"
                                        data-content="<?php echo $help_link; ?>"
                                        data-placement="bottom"
                                        data-container="body"
                                        rel="popover"
                                        data-trigger="focus" />
                                </div>
            				</div>

            				<div class="grid-4">
            					<div class="formbox">
            						<input id="button-post" type="submit" value="<?php echo $button_post; ?>" class="button hidden" />
            						<label for="button-post" class="button button-submit button_secondary"><i class="fa fa-pencil"></i><?php echo $button_post; ?></label>
            					</div>
            				</div>
            			</div>

                        <div class="grid-1 clearafter">
                            <div class="grid-8">
                                <span class="formbox label">
                                    <?php echo $text_post_alt; ?>
                                </span>
                            </div>

                            <div class="grid-4"></div>
                        </div>

                    </div>

                    <input type="hidden" name="category_name" value="" />
                    <input type="hidden" name="newlink" value="true" />
            	</form>

            	<form action="<?php echo $search; ?>" method="get" enctype="multipart/form-data">
            		<h2><a href="<?php echo $product_random; ?>"><?php echo $heading_search; ?></a></h2>

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
            							<select name="category" title="<?php echo $help_category; ?>" rel="tooltip">
                                            <option value="0" selected="selected"><?php echo $text_category; ?></option>
                                            <?php foreach ($categories as $category) { ?>
                                            <option value="<?php echo $category['category_id']; ?>" data-url="<?php echo $category['url']; ?>"><?php echo $category['name']; ?></option>
                                            <?php } ?>
            							</select>
            						</div>
            					</div>
            				</div>

            				<div class="grid-4 hidden-medium">
            					<div class="formbox">
            						<input id="button-search" type="submit" value="<?php echo $button_search; ?>" class="button hidden" />
            						<label for="button-search" class="button button-submit button_highlight"><i class="fa fa-search"></i><?php echo $button_search; ?></label>
            					</div>
            				</div>
            			</div>

                        <div class="grid-1 show-medium">
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

                            <div class="grid-4"></div>
                        </div>

                    </div>

                    <?php if ($country_id) { ?>
                    <input type="hidden" name="country" value="<?php echo $country_id; ?>" />
                    <?php } ?>
                    <?php if ($zone_id) { ?>
                    <input type="hidden" name="state" value="<?php echo $zone_id; ?>" />
                    <?php } ?>
                    <?php if ($location) { ?>
                    <input type="hidden" name="location" value="<?php echo $location; ?>" />
                    <?php } ?>
            	</form>

        	</div>
        </div>
    </div>
    <?php if ($content_bottom) { ?>
    <div class="layout">
        <?php echo $column_right; ?>
        <?php echo $column_left; ?>
        <section class="container-center">
            <div class="container-bottom">
                <?php echo $content_bottom; ?>
            </div>
        </section>
    </div>
    <?php } ?>
</main>
<?php echo $footer; ?>
