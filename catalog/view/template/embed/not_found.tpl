<?php echo $header; ?>
<div class="container-page embed-not-found">
    <div class="breadcrumb">
        <div class="layout">
            <h1><?php echo $heading_title; ?></h1>
            <div class="links">
                <a href="<?php echo $config_url; ?>" target="_blank"><?php echo $config_name; ?></a>
                <?php foreach ($breadcrumbs as $breadcrumb) { ?>
                <?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
                <?php } ?>
            </div>
        </div>
    </div>
    <div class="layout">
        <div class="container-center">
            <div class="content-page">
                <div class="global-page">
					<div class="warning">
						<p><?php echo $text_error; ?></p>
						<span class="icon"><i class="fa fa-exclamation-triangle"></i></span>
					</div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php echo $footer; ?>
