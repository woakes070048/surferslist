<section id="profile-ratings">
<?php if ($reviews) { ?>
<?php foreach ($reviews as $review) { ?>
<div class="review-mini">
    <div class="info">
        <div class="avatar"><a href="<?php echo $review['href']; ?>"><img src="<?php echo $review['image']; ?>" alt="<?php echo $review['name']; ?>" /></a></div>
        <div class="item">
            <h3><a href="<?php echo $review['href']; ?>"><?php echo $review['name']; ?></a> <span class="added"><?php echo $review['date_added']; ?></span></h3>
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
    <div class="description">
        <div class="text"><p><?php echo $review['text']; ?></p></div>
    </div>
</div>
<?php } ?>
<div class="pagination"><?php echo $pagination; ?></div>
<?php } else { ?>
<div class="content">
    <div class="information">
        <p><?php echo $text_no_reviews; ?></p>
        <span class="icon"><i class="fa fa-info-circle"></i></span>
    </div>
</div>
<?php } ?>
</section>
