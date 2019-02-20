<?php echo $header; ?>
<div class="container-page account-question-list-page">
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
            <div class="content-page my-account my-questions">
            	<?php echo $notification; ?>
				<?php echo $content_top; ?>
                <div class="widget">
					<h6><?php echo $heading_sub_title_list; ?></h6>

					<div class="my-listings">
                        <div class="buttons">
    						<div class="left">
    							<a href="<?php echo $continue; ?>" class="button button_alt"><i class="fa fa-undo"></i><?php echo $button_back; ?></a>
    						</div>
                            <?php if ($questions) { ?>
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
						<form action="" method="post" enctype="multipart/form-data" id="form" class="account-question-list-form">

                            <table class="list bordered-bottom">
                              <thead>
                                <tr>
                                  <th width="1" style="text-align: center;">
                                    <?php if ($questions) { ?>
                                    <input type="checkbox" id="check-select-all" title="<?php echo $text_select_all; ?>" rel="tooltip" data-container="body" />
                                    <?php } ?>
                                  </th>
                                  <th class="left"><?php if ($sort == 'location') { ?>
                                    <a href="<?php echo $sort_location; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_location; ?></a>
                                    <?php } else { ?>
                                    <a href="<?php echo $sort_location; ?>"><?php echo $column_location; ?></a>
                                    <?php } ?></th>
                                  <th class="left hidden-very-small"><?php echo $column_text; ?></th>
                                  <th class="left"><?php if ($sort == 'q.status') { ?>
                                    <a href="<?php echo $sort_status; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_status; ?></a>
                                    <?php } else { ?>
                                    <a href="<?php echo $sort_status; ?>"><?php echo $column_status; ?></a>
                                    <?php } ?></th>
                                  <th class="left date hidden-very-small"><?php if ($sort == 'q.date_added') { ?>
                                    <a href="<?php echo $sort_date_added; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_date_added; ?></a>
                                    <?php } else { ?>
                                    <a href="<?php echo $sort_date_added; ?>"><?php echo $column_date_added; ?></a>
                                    <?php } ?></th>
                                  <th class="right action"><?php // echo $column_action; ?></th>
                                </tr>
                              </thead>
                              <tbody>
                                <?php foreach ($questions as $question) { ?>
                                <tr>
                                  <td style="text-align: center;"><?php if ($question['selected']) { ?>
                                    <input type="checkbox" name="selected[]" value="<?php echo $question['question_id']; ?>" checked="checked" />
                                    <?php } else { ?>
                                    <input type="checkbox" name="selected[]" value="<?php echo $question['question_id']; ?>" />
                                    <?php } ?></td>
                                  <td class="left">
                                      <?php if ($question['product']) { ?>
                                      <a href="<?php echo $question['product_href']; ?>" target="_blank"><?php echo $question['product']; ?></a>
                                      <?php } else { ?>
                                      <a href="<?php echo $question['member_href']; ?>" target="_blank"><?php echo $question['member']; ?></a>
                                      <?php } ?>
                                  </td>
                                  <td class="left hidden-very-small"><?php echo $question['text']; ?></td>
                                  <td class="left <?php echo (($question['status'] == $text_enabled) ? ('enabled') : ('disabled')); ?>"><?php echo $question['status']; ?></td>
                                  <td class="left date hidden-very-small"><?php echo $question['date_added']; ?></td>
                                  <td class="right action"><?php foreach ($question['action'] as $action) { ?>
                                    <a href="<?php echo $action['href']; ?>" class="button button_alt smaller"><i class="fa fa-pencil"></i><?php echo $action['text']; ?></a>
                                    <?php } ?></td>
                                </tr>
                                <?php } ?>
                              </tbody>
                            </table>

                            <?php if (!$questions) { ?>
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
<?php if ($delete) { ?>
<script type="text/javascript"><!--
var textConfirmDelete = '<?php echo $text_confirm; ?>';
//--></script>
<?php } ?>
<?php echo $footer; ?>
