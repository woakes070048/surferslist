<?php echo $header; ?>
<div class="container-page product-download-form-page">
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
    <div class="layout">
        <?php echo $column_left; ?>
        <div class="container-center">
            <div class="content-page my-account my-download">
            	<?php echo $content_top; ?>
                <div class="global-page">

					<div class="buttons"><div class="right"><a id="form-submit" class="button button-submit button_save"><?php echo $button_save; ?></a> <a href="<?php echo $cancel; ?>" class="button"><?php echo $button_cancel; ?></a></div></div>

					<?php if ($error_warning) { ?>
					<div class="warning">
						<p><?php echo $error_warning; ?></p>
						<span class="close"><i class="fa fa-times"></i></span>
						<span class="icon"><i class="fa fa-exclamation-triangle"></i></span>
					</div>
					<?php } ?>

                    <div class="content">
                      <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form" class="product-download-form">
                        <table class="form">
                          <tr>
                            <td><span class="required">*</span> <?php echo $entry_name; ?></td>
                            <td><?php foreach ($languages as $language) { ?>
                              <input type="text" name="download_description[<?php echo $language['language_id']; ?>][name]" value="<?php echo isset($download_description[$language['language_id']]) ? $download_description[$language['language_id']]['name'] : ''; ?>" />
                              <img src="image/flags/<?php echo $language['image']; ?>" title="<?php echo $language['name']; ?>" /><br />
                              <?php if (isset($error_name[$language['language_id']])) { ?>
                              <span class="error"><?php echo $error_name[$language['language_id']]; ?></span><br />
                              <?php } ?>
                              <?php } ?></td>
                          </tr>
                          <tr>
                            <td><?php echo $entry_filename; ?></td>
                            <td><input type="text" name="filename" value="<?php echo $filename; ?>" /> <a id="button-upload" class="button"><?php echo $button_upload; ?></a>
                              <?php if ($error_filename) { ?>
                              <span class="error"><?php echo $error_filename; ?></span>
                              <?php } ?></td>
                          </tr>
                          <tr>
                            <td><?php echo $entry_mask; ?></td>
                            <td><input type="text" name="mask" value="<?php echo $mask; ?>" />
                              <?php if ($error_mask) { ?>
                              <span class="error"><?php echo $error_mask; ?></span>
                              <?php } ?></td>
                          </tr>
                          <tr>
                            <td><?php echo $entry_remaining; ?></td>
                            <td><input type="text" name="remaining" value="<?php echo $remaining; ?>" size="6" /></td>
                          </tr>
                          <?php if ($download_id) { ?>
                          <tr>
                            <td><?php echo $entry_update; ?></td>
                            <td><?php if ($update) { ?>
                              <input type="checkbox" name="update" value="1" checked="checked" />
                              <?php } else { ?>
                              <input type="checkbox" name="update" value="1" />
                              <?php } ?></td>
                          </tr>
                          <?php } ?>
                        </table>
                      </form>
                    </div>

                </div>
                <?php echo $content_bottom; ?>
            </div>
        </div>
        <?php echo $column_right; ?>
    </div>
</div>
<?php echo $footer; ?>
