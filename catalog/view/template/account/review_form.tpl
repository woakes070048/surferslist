<?php echo $header; ?>
<div class="container-page">
    <div class="breadcrumb">
        <div class="layout">
            <h1><i class="fa fa-star"></i> <?php echo $heading_title; ?></h1>
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
            <div class="content-page my-account my-review">
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
					  <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form">
						<table class="form">
						  <tr>
							<td><?php echo $entry_member; ?></td>
							<td><input type="text" name="member" value="<?php echo $member; ?>" disabled="disabled" /></td>
						  </tr>
						  <tr class="hidden">
							<td><?php echo $entry_order_product; ?></td>
							<td><input type="text" name="order_product" value="<?php echo $order_product; ?>" disabled="disabled" /></td>
						  </tr>
						  <tr>
							<td><?php echo $entry_rating; ?></td>
							<td><div id="formboxrating">
									<div class="stars-content">
										<input type="radio" id="rate5" value="5" name="rating" class="hidden-star" <?php if ($rating == 5) { ?>checked="checked"<?php } ?>>
										<label for="rate5" class="visible-star"><i class="fa fa-star" rel="tooltip" data-placement="top" data-original-title="<?php echo $entry_good; ?>"></i></label>
										<input type="radio" id="rate4" value="4" name="rating" class="hidden-star" <?php if ($rating == 4) { ?>checked="checked"<?php } ?>>
										<label for="rate4" class="visible-star"><i class="fa fa-star"></i></label>
										<input type="radio" id="rate3" value="3" name="rating" class="hidden-star" <?php if ($rating == 3) { ?>checked="checked"<?php } ?>>
										<label for="rate3" class="visible-star"><i class="fa fa-star"></i></label>
										<input type="radio" id="rate2" value="2" name="rating" class="hidden-star" <?php if ($rating == 2) { ?>checked="checked"<?php } ?>>
										<label for="rate2" class="visible-star"><i class="fa fa-star"></i></label>
										<input type="radio" id="rate1" value="1" name="rating" class="hidden-star" <?php if ($rating == 1) { ?>checked="checked"<?php } ?>>
										<label for="rate1" class="visible-star"><i class="fa fa-star" rel="tooltip" data-placement="top" data-original-title="<?php echo $entry_bad; ?>"></i></label>
									</div>
								</div>
							  <?php if ($error_rating) { ?>
							  <span class="error"><?php echo $error_rating; ?></span>
							  <?php } ?></td>
						  </tr>
						  <tr>
							<td><?php echo $entry_text; ?></td>
							<td><textarea name="text" cols="60" rows="8"><?php echo $text; ?></textarea>
							  <?php if ($error_review) { ?>
							  <span class="error"><?php echo $error_review; ?></span>
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
