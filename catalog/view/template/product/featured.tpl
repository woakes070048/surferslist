<?php echo $header; ?>
<main class="container-page featured-page">
    <header class="breadcrumb">
        <div class="layout">
            <h1><a href="<?php echo $action; ?>"><i class="fa fa-tags"></i><?php echo $heading_title; ?></a></h1>
            <div class="links">
                <?php foreach ($breadcrumbs as $breadcrumb) { ?>
                <?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
                <?php } ?>
            </div>
        </div>
    </header>
    <div class="layout">
        <aside id="sidebar" class="sidebar container-left">
            <?php echo $column_left; ?>
	        <?php echo $refine; ?>
            <?php echo $column_right; ?>
        </aside>
        <section class="container-center">
            <div class="content-page">
                <?php echo $notification; ?>
                <?php echo $content_top; ?>
            	<?php echo $products; ?>
                <?php echo $content_bottom; ?>
            </div>
        </section>
    </div>
</main>
<?php echo $footer; ?>
