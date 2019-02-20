<?php echo $header; ?>
<div class="container-page account-question-page">
    <div class="breadcrumb">
        <div class="layout">
            <h1><i class="fa fa-comment"></i> <?php echo $heading_title; ?></h1>
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
            <div class="content-page my-account my-question">
            	<?php echo $notification; ?>
            	<?php echo $content_top; ?>

                <div class="widget">

					<h6><?php echo $heading_sub_title_form; ?></h6>

					<div class="buttons">
						<div class="left">
							<a id="form-submit" class="button button-submit button_save"><i class="fa fa-check"></i><?php echo $button_update; ?></a>
							<a href="<?php echo $cancel; ?>" class="button button_cancel"><i class="fa fa-ban"></i><?php echo $button_cancel; ?></a>
						</div>
					</div>

					<div class="content">
					  <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form" class="account-question-form">
						<table class="form">
                          <?php if ($product) { ?>
						  <tr>
							<td><?php echo $entry_product; ?></td>
							<td><input type="text" name="product" value="<?php echo $product; ?>" disabled="disabled" /></td>
						  </tr>
                          <?php } ?>
						  <tr>
							<td><?php echo $entry_member; ?></td>
							<td><input type="text" name="member" value="<?php echo $member; ?>" disabled="disabled" /></td>
						  </tr>
						  <tr>
							<td><?php echo $entry_text; ?></td>
							<td><textarea name="text" cols="60" rows="8"><?php echo $text; ?></textarea>
							  <?php if ($error_text) { ?>
							  <span class="error"><?php echo $error_text; ?></span>
							  <?php } ?></td>
						  </tr>
						  <tr>
							<td><?php echo $entry_status; ?></td>
							<td><select name="status">
								<?php if ($status) { ?>
								<option value="1" selected="selected"><?php echo $text_enabled; ?></option>
								<option value="0"><?php echo $text_disabled; ?></option>
								<?php } else { ?>
								<option value="1"><?php echo $text_enabled; ?></option>
								<option value="0" selected="selected"><?php echo $text_disabled; ?></option>
								<?php } ?>
							  </select></td>
						  </tr>
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
