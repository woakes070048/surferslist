<?php echo $header; ?>
<div class="container-page information-page">
    <div class="breadcrumb">
        <div class="layout">
            <h1><a href="<?php echo $page; ?>"><i class="fa fa-info-circle"></i><?php echo $heading_title; ?></a></h1>
            <div class="links">
                <?php foreach ($breadcrumbs as $breadcrumb) { ?>
                <?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
                <?php } ?>
            </div>
        </div>
    </div>
    <div class="layout">
        <?php echo $column_left; ?>
        <div class="container-center">
            <div class="content-page">
            	<?php echo $content_top; ?>
				<?php echo $notification; ?>
                <div class="global-page">
                    <?php echo $description; ?>
                </div>
                <?php echo $content_bottom; ?>
            </div>
        </div>
        <?php echo $column_right; ?>
    </div>
</div>
<?php echo $footer; ?>
