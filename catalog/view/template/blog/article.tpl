<?php echo $header; ?>
<main class="container-page blog-page blog-article-page">
    <header class="breadcrumb">
        <div class="layout">
            <h1><a href="<?php echo $page; ?>"><i class="fa fa-rss"></i><?php echo $heading_title; ?></a></h1>
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
                        <div class="article-info" itemscope itemtype="http://schema.org/Article">
                            <div class="post-section">
                                <div class="title-box">
                                    <div class="post-data">
                                        <strong><i class="fa fa-user"></i></strong> <?php echo $text_author; ?> <a href="<?php echo $author_search; ?>" ><span itemprop="author"><?php echo $author_name; ?></span></a>
                                        | <srong><i class="fa fa-calendar"></i></srong> <?php echo $text_published; ?> <span itemprop="dateCreated"><?php echo $published; ?></span>
                                        <?php if ($published != $date_modified) { ?>
                                        | <srong><i class="fa fa-calendar"></i></srong> <?php echo $text_modified; ?> <span itemprop="dateModified"><?php echo $date_modified; ?></span>
                                        <?php } ?>
                                        | <strong><i class="fa fa-eye"></i></strong> <?php echo $text_viewed; ?> <?php echo $viewed; ?>
                                        <a href="<?php echo $page;?>" itemprop="url" title="<?php echo $heading_title; ?>"></a>
                                    </div>
                                </div>
                                <div class="description">
                                    <?php echo $description; ?>
                                </div>
                            </div>
                        </div>

                        <?php if ($article_images) { ?>
                        <div class="images article-images">
                            <?php foreach ($article_images as $article_image) { ?>
                            <a href="<?php echo $article_image['popup']; ?>" class="zoom-gallery lightbox" rel="article-images" data-small-image="<?php echo $article_image['image']; ?>">
                                <img src="<?php echo $article_image['image']; ?>" alt="<?php echo $heading_title; ?>" />
                            </a>
                            <?php } ?>
                        </div>
                        <?php } ?>

                        <div class="share">
                            <div class="sharethis-inline-share-buttons"></div>
                        </div>

                        <div class="tags">
                            <h4><i class="fa fa-tags"></i><?php echo $text_tags; ?>: </h4>
                            <?php foreach ($tags as $tag) { ?>
                            <a class="label label-default" href="<?php echo $tag['href']; ?>"><?php echo $tag['tag']; ?></a>
                            <?php } ?>
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
                            <div class="box-blog-product">
                                <?php foreach ($related_articles as $related) { ?>
                                <div class="grid-4">
                                    <?php if ($related['thumb']) { ?>
                                    <div class="image" ><a href="<?php echo $related['href']; ?>"><img src="<?php echo $related['thumb']; ?>" /></a></div>
                                    <?php } ?>
                                    <div class="name"><a href="<?php echo $related['href']; ?>"><?php echo $related['name']; ?></a></div>
                                    <div class="description">
                                        <?php echo $related['description']; ?>
                                        <a class="readmore" href="<?php echo $related['href']; ?>"><?php echo $text_read_more; ?></a>
                                    </div>
                                </div>
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
