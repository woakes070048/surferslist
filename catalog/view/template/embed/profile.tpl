<?php echo $header; ?>
<main class="container-page embed-profile-page"<?php if ($nobackground) { ?>style="background:transparent;"<?php } ?>>
    <header class="breadcrumb">
        <?php if ($showheader) { ?>
        <div class="layout">
            <h1><a class="logo" href="<?php echo $config_url; ?>" target="_blank" title="<?php echo $config_name; ?>">
                <img src="<?php echo $config_icon; ?>" alt="<?php echo $config_name; ?>" width="28" height="28" /></a> <?php echo $heading_title; ?>
            </h1>
            <div class="links">
                <a href="<?php echo $config_url; ?>" target="_blank"><?php echo $config_name; ?></a>
                <?php foreach ($breadcrumbs as $breadcrumb) { ?>
                <?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
                <?php } ?>
            </div>
        </div>
        <?php } ?>
    </header>
    <div class="layout">
		<?php if ($products) { ?>
        <section id="embed-profile">
            <?php if (!$nosidebar) { ?>
            <aside id="sidebar" class="sidebar container-right">
                <?php echo $refine; ?>
            </aside>
            <?php } ?>
            <div class="container-center">
                <div class="content-page">

                    <?php echo $products; ?>

                    <?php if (isset($pagination)) { ?>
                    <div class="pagination"><?php echo $pagination; ?></div>
                    <?php } ?>

                    <?php if (!$hidefooter) { ?>
                    <p class="powered text-center">
                        <?php if ($logo) { ?>
                        <a class="logo" href="<?php echo $config_url; ?>" target="_blank" title="<?php echo $text_powered; ?>">
                            <img src="<?php echo $logo; ?>" alt="<?php echo $text_powered; ?>" width="140" height="60" />
                        </a>
                        <?php } else { ?>
                        <a href="<?php echo $config_url; ?>" target="_blank"><?php echo $text_powered; ?></a>
                        <?php } ?>
                    </p>
                    <?php } ?>
                    <div class="footer">
                        <a class="top show-to-top" id="top"><i class="fa fa-arrow-up"></i></a>
                    </div>
                </div>
            </div>
        </section>
        <?php } else { ?>
        <?php if (!$nosidebar) { ?>
        <aside id="sidebar" class="sidebar container-right">
            <?php echo $refine; ?>
        </aside>
        <?php } ?>
        <div class="container-center">
            <div class="global-page">
                <div class="information">
                    <p><?php echo $text_no_listings; ?></p>
                    <span class="icon"><i class="fa fa-info-circle"></i></span>
                </div>
            </div>
        </div>
        <?php } ?>
    </div>
</main>
<script type="text/javascript"><!--
var member_id = <?php echo $profile_id; ?>;
var textWait = '<?php echo $text_wait; ?>';
//--></script>
<?php echo $footer; ?>
