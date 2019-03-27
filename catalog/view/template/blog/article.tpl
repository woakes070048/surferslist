<?php echo $header; ?>
<main class="container-page blog-page blog-article-page" itemscope itemtype="http://schema.org/Article">
    <header class="breadcrumb">
        <div class="layout">
            <h1><a href="<?php echo $page; ?>" itemprop="url"><i class="fa fa-rss"></i><span itemprop="name"><?php echo $heading_title; ?></span></a></h1>
            <div class="links">
                <?php foreach ($breadcrumbs as $breadcrumb) { ?>
                <?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
                <?php } ?>
            </div>
        </div>
    </header>
    <div class="layout">
        <aside id="sidebar" class="sidebar container-right">
            <?php echo $column_left; ?>
            <?php echo $sidebar; ?>
            <?php echo $column_right; ?>
        </aside>
        <section class="container-center">
            <div class="container-top">
                <div class="content-page">
                	<?php echo $content_top; ?>
    				<?php echo $notification; ?>

                    <div class="global-page">
                        <div class="article-body" itemprop="articleBody">
                            <div class="article-heading">
                                <div class="article-date">
                                    <span class="month"><?php echo $month; ?></span>
                                    <span class="day"><?php echo $day; ?></span>
                                    <span class="year"><?php echo $year; ?></span>
                                    <span class="published hidden" itemprop="dateCreated"><?php echo $date_published; ?></span>
                                    <span class="modified hidden" itemprop="dateModified"><?php echo $date_modified; ?></span>
                                </div>
                                <div class="article-stats">
                                    <?php if ($author_image) { ?>
                                    <span class="avatar">
                                        <a href="<?php echo $author_href; ?>">
                                            <img src="<?php echo $author_image; ?>" alt="<?php echo $author_name; ?>" />
                                        </a>
                                    </span>
                                    <?php } ?>
                                    <span class="author"><i class="fa fa-user"></i><?php echo $text_author; ?> <a href="<?php echo $author_search; ?>" ><span itemprop="author"><?php echo $author_name; ?></span></a></span>
                                    <?php if ($categories) { ?>
                                    <span class="categories"><i class="fa fa-sitemap"></i><?php echo $text_category; ?>
                                        <?php for ($i = 0; $i < count($categories); $i++) { ?>
                                        <?php if ($i < (count($categories) - 1)) { ?>
                                        <a href="<?php echo $categories[$i]['href']; ?>" itemprop="articleSection"><?php echo $categories[$i]['name']; ?></a>,
                                        <?php } else { ?>
                                        <a href="<?php echo $categories[$i]['href']; ?>" itemprop="articleSection"><?php echo $categories[$i]['name']; ?></a>
                                        <?php } ?>
                                        <?php } ?></span>
                                    <?php } ?>
                                </div>
                            </div>

                            <?php if ($image) { ?>
                            <div class="article-image-wrapper clearafter">
                                <div class="grid-6 offset-1">
                    				<div class="article-image image image-border">
                                        <a href="<?php echo $popup; ?>" class="lightbox" rel="article-images" data-small-image="<?php echo $thumb; ?>">
                                            <img src="<?php echo $image; ?>" itemprop="image" />
                                        </a>
                    				</div>
                    			</div>
                            </div>
                			<?php } ?>

                            <?php if ($article_images) { ?>
                            <div class="article-images images">
                                <?php foreach ($article_images as $article_image) { ?>
                                <div class="article-image image image-border">
                                    <a href="<?php echo $article_image['popup']; ?>" class="lightbox" rel="article-images" data-small-image="<?php echo $article_image['image']; ?>">
                                        <img src="<?php echo $article_image['image']; ?>" alt="<?php echo $heading_title; ?>" />
                                    </a>
                                </div>
                                <?php } ?>
                            </div>
                            <?php } ?>

                            <div class="article-description">
                                <?php echo $description; ?>
                            </div>

                            <div class="tags" itemprop="keywords">
                                <h4 class="hidden"><i class="fa fa-tags"></i><?php echo $text_tags; ?>: </h4>
                                <?php foreach ($tags as $tag) { ?>
                                <a class="label label-default" href="<?php echo $tag['href']; ?>"><?php echo $tag['tag']; ?></a>
                                <?php } ?>
                            </div>
                        </div>

                        <div class="share">
                            <div class="sharethis-inline-share-buttons"></div>
                        </div>

                        <input type="hidden" name="blog_article_id" value="<?php echo $blog_article_id; ?>" />
                    </div>

                    <?php if ($related_products) { ?>
                    <div id="article-related">
                        <h2><?php echo $text_related_product; ?></h2>

                        <section id="related-listings" class="widget-module">
                            <div id="filterscat">
                                <?php echo $related_products; ?>
                            </div>
                        </section>
                    </div>
                    <?php } ?>

                    <?php if ($related_articles) { ?>
                    <div id="article-related" class="box-blog">
                        <h2><?php echo $text_related_article; ?></h2>

                        <section id="related-articles" class="widget-module">
                            <div class="grid global-grid-item">
                            	<div class="grid-sizer"></div>
                                <?php foreach ($related_articles as $related) { ?>
                                <article id="article-<?php echo $related['blog_article_id']; ?>" class="grid-item itemcat" data-filter-class="article">
                                    <?php if ($related['thumb']) { ?>
                                    <div class="image" >
                                        <a href="<?php echo $related['href']; ?>">
                                            <img src="<?php echo $related['thumb']; ?>" />
                                        </a>
                                    </div>
                                    <?php } ?>
                                    <div class="article-name">
                                        <a href="<?php echo $related['href']; ?>"><?php echo $related['name']; ?></a>
                                    </div>
                                    <div class="article-description">
                                        <?php echo $related['description']; ?>
                                        <a class="button button_inverse button_dark smaller" href="<?php echo $related['href']; ?>"><?php echo $text_read_more; ?></a>
                                    </div>
                                </article>
                                <?php } ?>
                            </div>
                        </section>
                    </div>
                    <?php } ?>
                </div>

                <div class="container-bottom">
                    <?php echo $content_bottom; ?>
                </div>
            </div>
        </section>
    </div>
</main>
<?php echo $footer; ?>
