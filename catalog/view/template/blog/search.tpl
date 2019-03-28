<?php echo $header; ?>
<main class="container-page blog-page blog-search-page">
    <header class="breadcrumb">
        <div class="layout">
            <h1><a href="<?php echo $page; ?>"><i class="fa fa-search"></i><?php echo $heading_title; ?></a></h1>
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
            <div class="content-page">
                <?php echo $notification; ?>
                <?php echo $content_top; ?>

                <?php if ($articles) { ?>

                <?php echo $articles; ?>

                <?php if (isset($pagination)) { ?>
                <div class="pagination"><?php echo $pagination; ?></div>
                <?php } ?>

                <?php } else { ?>

                <?php echo $empty; ?>

                <?php } ?>

                <?php echo $content_bottom; ?>
            </div>
        </section>
    </div>
</main>
<?php echo $footer; ?>
