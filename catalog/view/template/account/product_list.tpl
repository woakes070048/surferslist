<?php echo $header; ?>
<div class="container-page account-product-list-page">
    <div class="breadcrumb">
        <div class="layout">
            <h1><i class="fa fa-th-list"></i> <?php echo $heading_title; ?></h1>
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
            <div class="content-page my-account my-listings">
            	<?php echo $notification; ?>
				<?php echo $content_top; ?>

                <div class="widget">

					<h6><?php echo $heading_sub_title; ?></h6>

					<div class="buttons">
						<div class="left">
						  <a href="<?php echo $insert; ?>" class="button button_new button_highlight"><i class="fa fa-file-image-o"></i><?php echo $button_new; ?></a>
                          <?php if ($products) { ?>
                          <a id="copy-listing" href="<?php echo $copy; ?>" class="button button_copy"><i class="fa fa-copy"></i><?php echo $button_copy; ?></a>
                          <?php } ?>
                          <?php if ($permissions['inventory_enabled']) { ?>
                          <a id="manage-images" class="button button_primary button_images"><i class="fa fa-camera"></i><?php echo $text_image_manager; ?></a>
                          <?php } ?>
                          <a href="<?php echo $continue; ?>" class="button button_alt button_back"><i class="fa fa-undo"></i><?php echo $button_back; ?></a>
						</div>
                        <?php if ($products) { ?>
						<div class="right">
  						  <a id="delete-listings" href="<?php echo $delete; ?>" class="button button_trash submit-form" title="<?php echo $help_delete; ?>" rel="tooltip" data-container="body"><i class="fa fa-trash"></i><?php echo $button_delete; ?></a>
                          <?php if (!$permissions['inventory_enabled']) { ?>
  						  <a id="renew-listings" href="<?php echo $renew; ?>" class="button button_renew submit-form" title="<?php echo $help_renew; ?>" rel="tooltip" data-container="body"><i class="fa fa-refresh"></i><?php echo $button_renew; ?></a>
  						  <?php } ?>
                          <a id="disable-listings" href="<?php echo $disable; ?>" class="button button_no submit-form" title="<?php echo $help_disable; ?>" rel="tooltip" data-container="body"><i class="fa fa-eye-slash"></i><?php echo $button_disable; ?></a>
                          <a id="enable-listings" href="<?php echo $enable; ?>" class="button button_yes submit-form" title="<?php echo $help_enable; ?>" rel="tooltip" data-container="body"><i class="fa fa-eye"></i><?php echo $button_enable; ?></a>
                          <a id="transfer-listing" href="<?php echo $transfer; ?>" class="button button_alt button_transfer" title="<?php echo $help_transfer; ?>" rel="tooltip" data-container="body"><i class="fa fa-exchange"></i><?php echo $button_transfer; ?></a>
						</div>
                        <?php } ?>
					</div>

					<div class="content">
						<form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form" class="account-product-list-form">

						<table class="list bordered-bottom">
						  <thead>
							<tr>
							  <td class="center" width="130"></td>
							  <td class="left">
                                <?php if ($sort == 'name') { ?>
								<a href="<?php echo $sort_name; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_name; ?></a>
								<?php } else { ?>
								<a href="<?php echo $sort_name; ?>"><?php echo $column_name; ?></a>
								<?php } ?></td>
							  <td class="hidden-medium">
                                <?php if ($sort == 'type') { ?>
								<a href="<?php echo $sort_type; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_type; ?></a>
								<?php } else { ?>
								<a href="<?php echo $sort_type; ?>"><?php echo $column_type; ?></a>
								<?php } ?></td>
							  <td class="hidden-small">
                                <?php if ($sort == 'status') { ?>
								<a href="<?php echo $sort_status; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_status; ?></a>
								<?php } else { ?>
								<a href="<?php echo $sort_status; ?>"><?php echo $column_status; ?></a>
								<?php } ?></td>
							  <?php if (!$permissions['inventory_enabled']) { ?>
							  <td class="hidden-large">
                                <?php if ($sort == 'expires') { ?>
								<a href="<?php echo $sort_date_expiration; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_date_expiration; ?></a>
								<?php } else { ?>
								<a href="<?php echo $sort_date_expiration; ?>"><?php echo $column_date_expiration; ?></a>
								<?php } ?></td>
							  <?php } ?>
                              <td class="show-small">&nbsp;</td>
							  <td class="center"></td>
							</tr>
						  </thead>
						  <tbody>
							<tr class="filter">
							  <td class="center">
                                  <?php if ($products) { ?>
                                  <a id="filter-product-list" class="button button_alt"><i class="fa fa-filter"></i><?php echo $button_filter; ?></a>
                                  <?php } ?>
                              </td>
							  <td class="left hidden-small">
                                  <input type="text" name="filter_name" value="<?php echo $filter_name; ?>" placeholder="<?php echo $help_filter_name; ?>" title="<?php echo $help_filter_name; ?>" rel="tooltip" />
                              </td>
							  <td class="left show-small" colspan="2">
                                  <input type="text" name="filter_name" value="<?php echo $filter_name; ?>" placeholder="<?php echo $help_filter_name; ?>" title="<?php echo $help_filter_name; ?>" rel="tooltip" />
                              </td>
							  <td class="hidden-medium">
                                  <select name="filter_type" title="<?php echo $help_filter_type; ?>" rel="tooltip">
    								  <option value="*"></option>
    								  <?php if ($filter_type == '-1') { ?>
    								  <option value="-1" selected="selected"><?php echo $text_shared; ?></option>
    								  <?php } else { ?>
    								  <option value="-1"><?php echo $text_shared; ?></option>
    								  <?php } ?>
    								  <?php if ($filter_type == '0') { ?>
    								  <option value="0" selected="selected"><?php echo $text_classified; ?></option>
    								  <?php } else { ?>
    								  <option value="0"><?php echo $text_classified; ?></option>
    								  <?php } ?>
    								  <?php if ($filter_type == '1') { ?>
    								  <option value="1" selected="selected"><?php echo $text_buy_now; ?></option>
    								  <?php } else { ?>
    								  <option value="1"><?php echo $text_buy_now; ?></option>
    								  <?php } ?>
								  </select>
                              </td>
							  <td class="hidden-small">
                                  <select name="filter_status" title="<?php echo $help_filter_status; ?>" rel="tooltip">
    								  <option value="*"></option>
    								  <?php if ($filter_status == '1') { ?>
    								  <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
    								  <?php } else { ?>
    								  <option value="1"><?php echo $text_enabled; ?></option>
    								  <?php } ?>
    								  <?php if ($filter_status == '2') { ?>
    								  <option value="2" selected="selected"><?php echo $text_approval; ?></option>
    								  <?php } else { ?>
    								  <option value="2"><?php echo $text_approval; ?></option>
    								  <?php } ?>
    								  <?php if (!is_null($filter_status) && !$filter_status) { ?>
    								  <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
    								  <?php } else { ?>
    								  <option value="0"><?php echo $text_disabled; ?></option>
    								  <?php } ?>
							      </select></td>
                              <?php if (!$permissions['inventory_enabled']) { ?>
							  <td class="hidden-large">&nbsp;</td>
                              <?php } ?>
							  <td class="right">
                                  <?php if ($products) { ?>
                                  <input type="checkbox" id="check-select-all" title="<?php echo $text_select_all; ?>" rel="tooltip" data-container="body" />
                                  <?php } ?>
                              </td>
							</tr> <!-- end filter -->

							<?php foreach ($products as $product) { ?>
							<tr>
							  <td class="center">
                                  <div class="image image-border">
                                      <?php if ($product['featured']) { ?><i class="fa fa-tag icon promo"></i><?php } ?>
                                      <img src="<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>" />
                                  </div>
                              </td>
							  <td class="left">
                                  <h4>
                                      <?php echo $product['name']; ?>
                                      <?php if ($permissions['inventory_enabled'] && $product['quantity'] > 1) { ?>
                                      <span class="listing-quantity grey-text text-smaller">(<?php echo $product['quantity']; ?>)</span>
                                      <?php } ?>
                                      <?php if ($product['type'] == $text_buy_now || $product['type'] == $text_classified) { ?>
                                      <span class="listing-value">
                                        <br />
                                        <?php if ($product['special']) { ?>
                                        <span class="green-text text-smaller"><?php echo $product['special']; ?></span>
        								<span class="grey-text text-small" style="text-decoration: line-through;"><?php echo $product['price']; ?></span>
        								<?php } else { ?>
        								<span class="green-text text-smaller"><?php echo $product['price']; ?></span>
        								<?php } ?>
                                      </span>
                                      <?php } ?>
                                  </h4>
                                  <div class="listing-buttons">
                                      <div class="left">
                                          <a href="<?php echo $product['edit']; ?>" class="button button_highlight"><i class="fa fa-pencil"></i><?php echo $text_edit; ?></a>
          								  <a href="<?php echo $product['quickview']; ?>" rel="quickview" class="button button_inverse button_alt button_quickview"><i class="fa fa-eye"></i><?php echo $button_preview; ?></a>
                                      </div>
                                  </div>
							  </td>
							  <td class="hidden-medium">
                                <?php if ($product['type'] == $text_classified) { ?>
								<span class="listing-type classified-ad"><?php echo $product['type']; ?></span>
								<?php } else if ($product['type'] == $text_buy_now) { ?>
								<span class="listing-type buy-now-ad"><?php echo $product['type']; ?></span>
                                <?php } else if ($product['type'] == $text_shared) { ?>
                                <span class="listing-type not-for-sale grey-text"><i><?php echo $product['type']; ?></i></span>
                                <?php } else { ?>
								<span class="listing-type"><?php echo $product['type']; ?></span>
								<?php } ?></td>
                              <?php if ($product['status'] == $text_approval) { ?>
							  <td class="not-approved">
                                  <span class="listing-status"><i class="fa fa-clock-o"></i><span class="hidden-very-small"><?php echo $product['status']; ?></span>
                              </td>
                              <?php } else { ?>
							  <td class="enable-disable-buttons bolder">
                                  <a class="button <?php echo ($product['status'] == $text_enabled) ? 'button_yes' : 'button_cancel'; ?>" data-value="1">
                                      <i class="fa fa-eye"></i>
                                      <span class="<?php echo ($product['status'] == $text_enabled) ? '' : 'hidden '; ?>hidden-large"><?php echo $button_enable; ?></span>
                                  </a>
                                  <a class="button <?php echo ($product['status'] != $text_enabled) ? 'button_no' : 'button_cancel'; ?>" data-value="0">
                                      <i class="fa fa-eye-slash"></i>
                                      <span class="<?php echo ($product['status'] == $text_enabled) ? 'hidden ' : ''; ?>hidden-large"><?php echo $button_disable; ?></span>
                                  </a>
                              </td>
                              <?php } ?>
                            <?php if (!$permissions['inventory_enabled']) { ?>
                            <td class="hidden-large<?php if ($product['expires_soon']) { ?> expires-soon<?php } ?>">
                                <?php echo $product['date_expiration']; ?>
                            </td>
                            <?php } ?>
							  <td class="right">
                                <?php if ($product['selected']) { ?>
								<input type="checkbox" name="selected[]" value="<?php echo $product['product_id']; ?>" checked="checked" />
								<?php } else { ?>
								<input type="checkbox" name="selected[]" value="<?php echo $product['product_id']; ?>" />
								<?php } ?></td>
							</tr>
							<?php } ?>

						  </tbody>
						</table>

						<?php if (!$products) { ?>
						<div class="information text-center">
							<p><?php echo $text_no_results; ?></p>
							<span class="icon"><i class="fa fa-info-circle"></i></span>
						</div>
						<?php } ?>

						</form>

					  <div class="pagination"><?php echo $pagination; ?></div>

				  </div>

                </div>
                <?php echo $content_bottom; ?>
            </div>
        </div>
        <?php echo $column_right; ?>
    </div>
</div>
<?php if ($products) { ?>
<div style="display:none;">
    <div id="transfer-listing-wrapper">
        <form action="<?php echo $transfer; ?>" method="post" enctype="multipart/form-data" id="transfer-listing-form">
            <div class="widget">
                <h6><?php echo $heading_transfer; ?></h6>
                <div class="content">
                    <div class="formbox">
                        <p class="form-label"><strong><?php echo $entry_transfer; ?></strong></p>
                        <input type="text" name="member[member_name]" value="<?php echo (isset($member['member_name']) ? $member['member_name'] : ''); ?>" placeholder="<?php echo $help_member; ?>" />
                        <input type="hidden" name="member[member_id]" value="<?php echo (isset($member['member_id']) ? $member['member_id'] : ''); ?>" />
                        <p><span class="help"><i class="fa fa-info-circle"></i><?php echo $help_member_info; ?></a></span></p>
                    </div>
                </div>
                <div class="buttons">
                    <div class="left">
                        <input id="transfer-listing-form-submit" type="submit" value="<?php echo $button_transfer; ?>" class="button hidden" />
                        <label for="transfer-listing-form-submit" class="button button-submit button-transfer"><i class="fa fa-exchange"></i> <?php echo $button_transfer; ?></label>
                    </div>
                    <div class="right">
                        <span class="terms"><a href="<?php echo $members; ?>" title="<?php echo $text_view_profiles; ?>" target="_blank"><i class="fa fa-list-alt"></i><?php echo $text_view_profiles; ?></a></span>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
<?php } ?>
<script type="text/javascript"><!--
var textImageManager = '<?php echo $text_image_manager; ?>';
var helpTransfer = '<?php echo $help_transfer; ?>';
var textConfirmTransfer = '<?php echo $text_confirm_transfer; ?>';
var textConfirmDelete = '<?php echo $text_confirm; ?>';
var errorMaxImages = '<?php echo $error_max_images; ?>';
var errorNotChecked = '<?php echo $error_notchecked; ?>';
//--></script>
<?php echo $footer; ?>
