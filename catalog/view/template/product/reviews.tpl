<?php echo $header; ?>
<main class="container-page reviews-page">
    <header class="breadcrumb">
        <div class="layout">
            <h1><?php echo $heading_title; ?></h1>
            <div class="links">
                <?php foreach ($breadcrumbs as $breadcrumb) { ?>
                <?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
                <?php } ?>
            </div>
        </div>
    </header>
    <div class="layout">
        <?php echo $column_left; ?>
        <?php echo $column_right; ?>
        <section class="container-center">
            <div class="content-page">
            	<?php echo $notification; ?>
                <?php echo $content_top; ?>
                <div class="global-page">
                    <?php if ($reviews) { ?>
                    <?php foreach ($reviews as $review) { ?>
                    <div class="review-mini">
                        <div class="info">
                            <?php if ($review['prod_thumb']) { ?>
                            <a class="image" href="<?php echo $review['prod_href']; ?>"><img src="<?php echo $review['prod_thumb']; ?>" alt="<?php echo $review['prod_name']; ?>" /></a>
                            <?php } ?>
                            <div class="item">
                                <h3><a href="<?php echo $review['prod_href']; ?>"><?php echo $review['prod_name']; ?></a></h3>
                                <?php if ($review['rating']) { ?>
                                <div class="rating">
                                    <div class="rating-stars view-star<?php echo $review['rating']; ?>">
                                        <i class="fa fa-star icon_star star-color color1"></i>
                                        <i class="fa fa-star icon_star star-color color2"></i>
                                        <i class="fa fa-star icon_star star-color color3"></i>
                                        <i class="fa fa-star icon_star star-color color4"></i>
                                        <i class="fa fa-star icon_star star-color color5"></i>
                                        <i class="fa fa-star icon_star star-dark dark1"></i>
                                        <i class="fa fa-star icon_star star-dark dark2"></i>
                                        <i class="fa fa-star icon_star star-dark dark3"></i>
                                        <i class="fa fa-star icon_star star-dark dark4"></i>
                                        <i class="fa fa-star icon_star star-dark dark5"></i>
                                    </div>
                                </div>
                                <?php } ?>
                            </div>
                        </div>
                        <div class="description">
                            <div class="text"><p><?php echo $review['description']; ?></p></div>
                            <div class="author"><a href="<?php echo $review['prod_href']; ?>"><?php echo $review["author"]; ?></a> <span class="added"><?php echo $review["date_added"]; ?></span></div>
                        </div>
                    </div>
                    <?php } ?>
                    <div class="pagination"><?php echo $pagination; ?></div>
                    <?php } else { ?>
                    <div class="information">
                        <p><?php echo $text_empty; ?></p>
                        <span class="icon"><i class="fa fa-info-circle"></i></span>
                    </div>
                    <?php } ?>
                </div>
                <?php echo $content_bottom; ?>
            </div>
        </section>
    </div>
</main>
<?php echo $footer; ?>
