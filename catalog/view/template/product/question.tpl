<section id="discussions">
<?php if ($questions) { ?>
<?php foreach ($questions as $question) { ?>
<div class="review-mini">
    <div class="info">
        <div class="avatar"><?php if ($question['href']) { ?><a href="<?php echo $question['href']; ?>"><img src="<?php echo $question['image']; ?>" alt="<?php echo $question['name']; ?>" /></a><?php } else { ?><img src="<?php echo $question['image']; ?>" alt="<?php echo $question['name']; ?>" /><?php } ?></div>
        <div class="item">
            <h3><?php if ($question['href']) { ?><a href="<?php echo $question['href']; ?>"><?php echo $question['name']; ?></a><?php } else { ?><?php echo $question['name']; ?><?php } ?> <span class="added"><?php echo $question['date_added']; ?></span></h3>
        </div>
    </div>
    <div class="description">
        <div class="text"><p><?php echo $question['text']; ?></p></div>
    </div>
</div>
<?php } ?>
<div class="pagination"><?php echo $pagination; ?></div>
<?php } else { ?>
<div class="content">
    <div class="information">
        <p><?php echo $text_no_questions; ?></p>
        <span class="icon"><i class="fa fa-info-circle"></i></span>
    </div>
</div>
<?php } ?>
</section>
