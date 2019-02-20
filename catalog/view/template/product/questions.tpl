<?php echo $header; ?>
<main class="container-page questions-page">
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
        <section class="container-center">
            <div class="content-page">
            	<?php echo $notification; ?>
                <?php echo $content_top; ?>
                <div class="global-page">
                    <?php if ($questions) { ?>
                    <?php foreach ($questions as $question) { ?>
                    <div class="question-mini">
                        <div class="info">
                            <?php if ($question['prod_thumb']) { ?>
                            <a class="image" href="<?php echo $question['prod_href']; ?>"><img src="<?php echo $question['prod_thumb']; ?>" alt="<?php echo $question['prod_name']; ?>" /></a>
                            <?php } ?>
                            <div class="item">
                                <h3><a href="<?php echo $question['prod_href']; ?>"><?php echo $question['prod_name']; ?></a></h3>
                            </div>
                        </div>
                        <div class="description">
                            <div class="text"><p><?php echo $question['description']; ?></p></div>
                            <div class="author"><a href="<?php echo $question['prod_href']; ?>"><?php echo $question["author"]; ?></a> <span class="added"><?php echo $question["date_added"]; ?></span></div>
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
        <?php echo $column_right; ?>
    </div>
</main>
<?php echo $footer; ?>
