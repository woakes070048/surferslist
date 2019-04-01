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
        <?php if ($content_top) { ?>
        <div class="container-top">
            <?php echo $content_top; ?>
        </div>
        <?php } ?>
        <aside id="sidebar" class="sidebar container-right">
            <?php echo $column_left; ?>
            <?php echo $sidebar; ?>
            <?php echo $column_right; ?>
        </aside>
        <section class="container-center">
            <div class="content-page">
                <?php echo $notification; ?>

                <div class="widget article-body" itemprop="articleBody">
                    <div class="content">
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
                                <?php if ($member_id) { ?>
                                <span class="author"><i class="fa fa-user"></i><?php echo $text_author; ?> <a href="<?php echo $author_search; ?>" ><span itemprop="author"><?php echo $author_name; ?></span></a></span>
                                <?php } ?>
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
                            <h2 class="hidden"><?php echo $text_image_gallery; ?></h2>
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

                        <input type="hidden" name="blog_article_id" value="<?php echo $blog_article_id; ?>" />
                    </div>

                    <div class="buttons-footer">
                        <div class="share">
                            <div class="sharethis-inline-share-buttons"></div>
                        </div>

                        <div class="prevnext full">
                            <ul class="pager column<?php echo $nav_cols; ?>">
                                <?php if ($prev_url) { ?>
                                <li class="prev"><a href="<?php echo $prev_url; ?>" rel="tooltip" title="<?php echo $prev_title;?>"><i class="fa fa-chevron-circle-left"></i><span class="name hidden"><?php echo $prev_title;?></span><span class="dir"><?php echo $text_prev; ?></span></a></li>
                                <?php } ?>
                                <?php if ($back_url) { ?>
                                <li class="back"><a href="<?php echo $back_url; ?>" rel="tooltip" title="<?php echo $button_back;?>"><i class="fa fa-undo"></i><span class="dir"><?php echo $button_back; ?></span></a></li>
                                <?php } else { ?>
                                <li class="more"><a href="<?php echo $more_url; ?>" rel="tooltip" title="<?php echo $more_title;?>"><i class="fa fa-th"></i><span class="name hidden"><?php echo $more_title;?></span><span class="dir"><?php echo $text_more;?></span></a></li>
                                <?php } ?>
                                <?php if ($next_url) { ?>
                                <li class="next"><a href="<?php echo $next_url; ?>" rel="tooltip" title="<?php echo $next_title;?>"><i class="fa fa-chevron-circle-right"></i><span class="name hidden"><?php echo $next_title;?></span><span class="dir"><?php echo $text_next; ?></span></a></li>
                                <?php } ?>
                            </ul>
                        </div>
                    </div>
                </div>

                <?php if ($related_articles) { ?>
                <div id="article-related" class="box-blog">
                    <h2><?php echo $text_related_article; ?></h2>

                    <section id="related-articles" class="widget-module">
                        <div class="grid-items-container">
                            <div class="grid global-grid-item">
                                <div class="grid-sizer"></div>
                                <?php foreach ($related_articles as $related) { ?>
                                <article id="article-<?php echo $related['blog_article_id']; ?>" class="grid-item itemcat" data-filter-class="article">
                                    <?php if ($related['thumb']) { ?>
                                    <div class="image">
                                        <a href="<?php echo $related['href']; ?>" title="<?php echo $related['name']; ?>">
                                            <img src="<?php echo $related['thumb']; ?>" alt="<?php echo $related['name']; ?>" />
                                        </a>
                                        <span class="description">
                                            <a href="<?php echo $related['href']; ?>"><?php echo $related['short_description']; ?></a>
                                        </span>
                                    </div>
                                    <?php } ?>
                                    <div class="pannel">
                                        <div class="info">
                                            <header>
                                                <h3><a href="<?php echo $related['href']; ?>" title="<?php echo $related['name']; ?>"><?php echo $related['name']; ?></a></h3>
                                            </header>
                                        </div>
                                        <footer class="add-to add-to1">
                                            <a href="<?php echo $related['href']; ?>" class="button button-read-more">
                                                <?php echo $button_read_more; ?>
                                            </a>
                                        </footer>
                                    </div>
                                </article>
                                <?php } ?>
                            </div>
                        </div>
                    </section>
                </div>
                <?php } ?>

                <?php if ($related_products) { ?>
                <div id="article-related">
                    <h2><?php echo $text_related_product; ?></h2>

                    <section id="related-listings" class="widget-module">
                        <div id="grid-items-container">
                            <?php echo $related_products; ?>
                        </div>
                    </section>
                </div>
                <?php } ?>
            </div>
        </section>
        <?php if ($content_bottom) { ?>
        <div class="container-bottom">
            <?php echo $content_bottom; ?>
        </div>
        <?php } ?>
    </div>
</main>
<?php echo $footer; ?>
