<?php echo $header; ?>
<?php if ($full_page) { ?>
    <div class="maintenance-page">
        <div class="maintenance-container">
            <div class="widget widget-no-heading">
                <div class="content">
					<div style="padding:45px 20px;">
						<?php if ($logo) { ?>
							<img src="<?php echo $logo; ?>" alt="" />
						<?php } ?>
                    </div>
                    <?php echo $message; ?>
                </div>
            </div>
        </div>
    </div>
    </body>
    </html>
<?php } else { ?>
    <div class="container-page no-breadcrumb">
        <div class="layout">
            <div class="container-center">
                <div class="content-page">
                    <div class="global-page">
                        <div style="padding:100px 0;"><?php echo $message; ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php echo $footer; ?>
<?php } ?>
