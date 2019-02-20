<?php echo $header; ?>
<div class="container-page">
    <div class="breadcrumb">
        <div class="layout">
            <h1><?php echo $heading_title; ?></h1>
            <div class="links">
                <?php foreach ($breadcrumbs as $breadcrumb) { ?>
                <?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
                <?php } ?>
            </div>
        </div>
    </div>
    <div class="layout layout-left-minus-right">
        <div class="content-page verification-page">
        <div class="container-left">
				
			<div class="widget account-verification">
			
				<h6><?php echo $text_account_verification; ?></h6>
					
				<div class="content">
					<div class="warning">
						<p><?php echo $warning; ?></p>
						<span class="icon"><i class="fa fa-exclamation-triangle"></i></span>
					</div>
						
					<p><?php echo $text_try_again; ?></p>
				</div>
			  
			</div>
 
		</div>
        </div>
    </div>
</div>
<?php echo $footer; ?>