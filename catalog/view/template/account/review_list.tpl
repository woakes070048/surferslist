<?php echo $header; ?>
<div class="container-page account-review-list-page">
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
            <div class="content-page my-account my-reviews">
            	<?php echo $notification; ?>
				<?php echo $content_top; ?>
                <div class="widget">
					<h6><?php echo $heading_sub_title_list; ?></h6>

                    <div class="my-listings">
    					<div class="buttons">
    						<div class="left">
    							<a href="<?php echo $continue; ?>" class="button button_alt"><i class="fa fa-undo"></i><?php echo $button_back; ?></a>
    						</div>
                            <?php if ($reviews) { ?>
    						<div class="right">
    						  <a onclick="submit_form('<?php echo $enable; ?>');" class="button button_yes"><i class="fa fa-eye"></i><?php echo $button_enable; ?></a>
    						  <a onclick="submit_form('<?php echo $disable; ?>');" class="button button_no"><i class="fa fa-eye-slash"></i><?php echo $button_disable; ?></a>
                              <?php if ($delete) { ?>
    						  <a onclick="submit_form('<?php echo $delete; ?>');" class="button button_trash"><i class="fa fa-trash"></i><?php echo $button_delete; ?></a>
                              <?php } ?>
    						</div>
                            <?php } ?>
    					</div>
                    </div>

					<div class="content">
						<form action="" method="post" enctype="multipart/form-data" id="form" class="account-review-list-form">
						<table class="list bordered-bottom">
						  <thead>
							<tr>
                              <th width="1" style="text-align: center;">
                                  <?php if ($reviews) { ?>
                                  <input type="checkbox" id="check-select-all" title="<?php echo $text_select_all; ?>" rel="tooltip" data-container="body" />
                                  <?php } ?>
                              </th>
							  <th class="left"><?php if ($sort == 'm1.member_account_name') { ?>
								<a href="<?php echo $sort_member; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_member; ?></a>
								<?php } else { ?>
								<a href="<?php echo $sort_member; ?>"><?php echo $column_member; ?></a>
								<?php } ?></th>
							  <th class="left hidden hidden-very-small"><?php if ($sort == 'op.name') { ?>
								<a href="<?php echo $sort_order_product; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_order_product; ?></a>
								<?php } else { ?>
								<a href="<?php echo $sort_order_product; ?>"><?php echo $column_order_product; ?></a>
								<?php } ?></th>
							  <th class="left"><?php if ($sort == 'r.rating') { ?>
								<a href="<?php echo $sort_rating; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_rating; ?></a>
								<?php } else { ?>
								<a href="<?php echo $sort_rating; ?>"><?php echo $column_rating; ?></a>
								<?php } ?></th>
							  <th class="left hidden-small"><?php echo $column_text; ?></th>
							  <th class="left hidden-very-small"><?php if ($sort == 'r.status') { ?>
								<a href="<?php echo $sort_status; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_status; ?></a>
								<?php } else { ?>
								<a href="<?php echo $sort_status; ?>"><?php echo $column_status; ?></a>
								<?php } ?></th>
							  <th class="left date hidden-very-small"><?php if ($sort == 'r.date_added') { ?>
								<a href="<?php echo $sort_date_added; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_date_added; ?></a>
								<?php } else { ?>
								<a href="<?php echo $sort_date_added; ?>"><?php echo $column_date_added; ?></a>
								<?php } ?></th>
							  <th class="right action"><?php // echo $column_action; ?></th>
							</tr>
						  </thead>
						  <tbody>
							<?php if ($reviews) { ?>
							<?php foreach ($reviews as $review) { ?>
							<tr>
                              <td style="text-align: center;">
                                <?php if ($review['selected']) { ?>
                                <input type="checkbox" name="selected[]" value="<?php echo $review['review_id']; ?>" checked="checked" />
                                <?php } else { ?>
                                <input type="checkbox" name="selected[]" value="<?php echo $review['review_id']; ?>" />
                                <?php } ?>
                              </td>
							  <td class="left"><a href="<?php echo $review['member_href']; ?>" target="_blank"><?php echo $review['member']; ?></a></td>
							  <td class="left hidden hidden-very-small"><?php echo $review['order_product']; ?></td>
							  <td class="left">
								<div class="rating">
									<div class="rating-stars view-star<?php echo $review['rating']; ?>" title="<?php echo $text_your_member_rating; ?>" rel="tooltip">
										<i class="fa fa-star icon_star star-color color1"></i><i class="fa fa-star icon_star star-color color2"></i><i class="fa fa-star icon_star star-color color3"></i><i class="fa fa-star icon_star star-color color4"></i><i class="fa fa-star icon_star star-color color5"></i><i class="fa fa-star icon_star star-dark dark1"></i><i class="fa fa-star icon_star star-dark dark2"></i><i class="fa fa-star icon_star star-dark dark3"></i><i class="fa fa-star icon_star star-dark dark4"></i><i class="fa fa-star icon_star star-dark dark5"></i>
									</div>
								</div>
							  </td>
							  <td class="left hidden-small"><?php echo $review['text']; ?></td>
							  <td class="left hidden-very-small <?php echo (($review['status'] == $text_enabled) ? ('enabled') : ('disabled')); ?>"><?php echo $review['status']; ?></td>
							  <td class="left date hidden-very-small"><?php echo $review['date_added']; ?></td>
							  <td class="right action"><?php foreach ($review['action'] as $action) { ?>
								<a href="<?php echo $action['href']; ?>" class="button button_alt smaller"><i class="fa fa-pencil"></i><?php echo $action['text']; ?></a>
								<?php } ?></td>
							</tr>
							<?php } ?>
						  </tbody>
						</table>
							<?php } else { ?>
						  </tbody>
						</table>
							<div class="information">
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
<script type="text/javascript"><!--
var textConfirmDelete = '<?php echo $text_confirm; ?>';
//--></script>
<?php echo $footer; ?>
