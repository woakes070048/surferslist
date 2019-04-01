<?php echo $header; ?>
<main class="container-page product-retired-page">
    <header class="breadcrumb">
        <div class="layout">
            <h1><?php echo $heading_title; ?></h1>
            <div class="links">
                <?php foreach ($breadcrumbs as $breadcrumb) { ?>
                <?php if ($breadcrumb['href']) { ?>
                <?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
                <?php } else { ?>
                <?php echo $breadcrumb['separator']; ?><?php echo $breadcrumb['text']; ?>
                <?php } ?>
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
                    <div class="global-messages">
						<div class="warning">
							<p><?php echo $text_error; ?> <?php echo $text_search; ?></p>
							<span class="icon"><i class="fa fa-info-circle"></i></span>
						</div>
					</div>

                    <div class="buttons">
                        <div class="left"><a href="<?php echo $continue; ?>" class="button button_home"><i class="fa fa-undo"></i><?php echo $button_continue; ?></a></div>
                        <div class="right"><a href="<?php echo $search; ?>" class="button button_search button_highlight"><i class="fa fa-search"></i><?php echo $button_search; ?></a></div>
                    </div>
                </div>

                <?php if ($categories || $manufacturer || $member) { ?>
                <div class="">
                    <ul id="listings" class="brands-list">
                        <?php foreach ($categories as $category) { ?>
                        <li class="listing-item grid-item" data-filter-class='["category"]'>
                            <a href="<?php echo $category['href']; ?>">
                                <img src="<?php echo $category['thumb']; ?>" alt="<?php echo $category['name']; ?>" />
                                <h2><?php echo $category['name']; ?></h2>
                            </a>
                        </li>
                        <?php } ?>
                        <?php if ($manufacturer) { ?>
                        <li class="listing-item grid-item" data-filter-class='["manufacturer"]'>
                            <a href="<?php echo $manufacturer['href']; ?>">
                                <img src="<?php echo $manufacturer['thumb']; ?>" alt="<?php echo $manufacturer['name']; ?>" />
                                <h2><?php echo $manufacturer['name']; ?></h2>
                            </a>
                        </li>
                        <?php } ?>
                        <?php if ($member) { ?>
                        <li class="listing-item grid-item" data-filter-class='["member"]'>
                            <a href="<?php echo $member['href']; ?>">
                                <img src="<?php echo $member['thumb']; ?>" alt="<?php echo $member['name']; ?>" />
                                <h2><?php echo $member['name']; ?></h2>
                            </a>
                        </li>
                        <?php } ?>
                    </ul>
                </div>
                <?php } ?>

                <?php echo $content_bottom; ?>
            </div>
        </section>
        <?php echo $column_right; ?>
    </div>
</main>
<?php echo $footer; ?>
