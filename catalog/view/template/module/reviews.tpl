<div class="widget">
	<?php if ($header) { ?><h6><?php echo $header; ?></h6><?php } ?>
    <?php foreach ($reviews as $review) { ?>
    <div class="review-mini">
        <?php if ($review['product_id']) { ?>
        <div class="info">
            <?php if ($review['prod_thumb']) { ?>
            <a class="image" href="<?php echo $review['prod_href']; ?>"><img src="<?php echo $review['prod_thumb']; ?>" alt="<?php echo $review['prod_name']; ?>" /></a>
            <?php } ?>
            <div class="item">
                <h3><a href="<?php echo $review['prod_href']; ?>"><?php echo $review['prod_name']; ?></a></h3>
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
            </div>
        </div>
        <?php } ?>
        <div class="description">
            <div class="text"><p><?php echo $review['description']?></p></div>
            <div class="author"><a href="<?php echo $review['href']?>"><?php echo $review['author']?></a> <span class="added"><?php echo $review["date_added"];?></span></div>
        </div>
    </div>
    <?php } ?>
    <div class="buttons">
        <div class="left">
            <a href="<?php echo $link_all_reviews; ?>" class="button"><i class="fa fa-file-text-o"></i><?php echo $text_all_reviews;?></a>
        </div>
    </div>
</div>
