<?php echo $header; ?>
<main class="container-page featured-page">
    <header class="breadcrumb">
        <div class="layout">
            <h1><a href="<?php echo $reset; ?>"><i class="fa fa-tags"></i><?php echo $heading_title; ?></a></h1>
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

                <?php if ($products) { ?>

                <div id="grid-items-container">

            		<?php echo $products; ?>
                    
                </div>

                <?php if (isset($pagination)) { ?>
                <div class="pagination"><?php echo $pagination; ?></div>
                <?php } ?>

                <?php } else { ?>

                <div class="global-page">
                	<div class="information">
                		<p><?php echo $text_empty; ?></p>
                		<span class="icon"><i class="fa fa-info-circle"></i></span>
                	</div>
                	<div class="buttons">
                		<div class="left">
                			<a href="<?php echo $back; ?>" class="button button_back"><i class="fa fa-undo"></i><?php echo $button_back; ?></a>
                			<?php if (!empty($url)) { ?>
                			<a href="<?php echo $reset; ?>" class="button button_alt button_reset"><i class="fa fa-refresh"></i><?php echo $button_reset; ?></a>
                			<?php } ?>
                		</div>
                		<div class="right">
                			<a href="<?php echo $search; ?>" class="button button_search"><i class="fa fa-search"></i><?php echo $button_search; ?></a>
                			<a href="<?php echo $continue; ?>" class="button button_alt button_home"><i class="fa fa-home"></i><?php echo $button_continue; ?></a>
                		</div>
                	</div>
                </div>

                <?php } ?>

                <?php echo $content_bottom; ?>
            </div>
        </section>
    </div>
</main>
<?php echo $footer; ?>
