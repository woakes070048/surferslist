<?php echo $header; ?>
<div class="container-page">
    <div class="breadcrumb">
        <div class="layout">
            <h1><i class="fa fa-money"></i> <?php echo $heading_title; ?></h1>
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
            <div class="content-page my-account my-sales">
            	<?php echo $notification; ?>
				<?php echo $content_top; ?>

                <div class="widget">

					<h6><?php echo $heading_sub_title; ?></h6>

                    <div class="buttons">
						<div class="left"><a href="<?php echo $continue; ?>" class="button button_alt"><i class="fa fa-undo"></i><?php echo $button_continue; ?></a></div>
                    </div>

					<div class="content">

					<?php if ($sales) { ?>

						<table class="list bordered-bottom">
						  <thead>
							<tr>
							  <th class="center"><?php echo $text_sale_id; ?></th>
							  <th class="left date hidden-very-small"><?php echo $text_date_added; ?></th>
							  <th class="left"><?php echo $text_status; ?></th>
							  <th class="left hidden-small"><?php echo $text_customer; ?></th>
							  <th class="center hidden-small"><?php echo $text_products; ?></th>
							  <th class="right hidden-very-small"><?php echo $text_total; ?></th>
							  <?php if ($this->config->get('member_report_sales_commission')) { ?>
                              <th class="right hidden-small"><?php echo $text_commission; ?></th>
                              <?php } ?>
							  <?php if ($this->config->get('member_report_sales_tax')) { ?>
                              <th class="right hidden-small"><?php echo $text_tax; ?></th>
                              <?php } ?>
							  <?php if ($this->config->get('member_report_sales_commission') || $this->config->get('member_report_sales_tax')) { ?>
                              <th class="right hidden-very-small"><?php echo $text_grand_total; ?> *</th>
                              <?php } ?>
							  <th class="right action"><?php // echo $text_info; ?></th>
							</tr>
						  </thead>
						  <tbody>
							<?php foreach ($sales as $sale) { ?>
							<tr>
							  <td class="center"><b><?php echo $sale['order_id']; ?></b></td>
							  <td class="left date hidden-very-small"><?php echo $sale['date_added']; ?></td>
							  <td class="left"><?php echo $sale['status']; ?></td>
							  <td class="left hidden-small">
                              <?php if ($sale['member_href']) { ?>
                              <a href="<?php echo $sale['member_href']; ?>"><?php echo $sale['name']; ?></a>
                              <?php } else { ?>
                              <?php echo $sale['name']; ?>
                              <?php } ?></td>
							  <td class="center hidden-small"><?php echo $sale['products']; ?></td>
							  <td class="right hidden-very-small"><?php echo $sale['sales']; ?></td>
							  <?php if ($this->config->get('member_report_sales_commission')) { ?>
                              <td class="right hidden-small">- <?php echo $sale['commission']; ?></td>
                              <?php } ?>
							  <?php if ($this->config->get('member_report_sales_tax')) { ?>
							  <?php if ($this->config->get('member_report_sales_tax_add')) { ?>
							  <td class="right hidden-small"><?php echo $sale['tax']; ?></td>
							  <?php } else {?>
							  <td class="right hidden-small">- <?php echo $sale['tax']; ?></td>
							  <?php } ?>
							  <?php } ?>
							  <?php if ($this->config->get('member_report_sales_commission') || $this->config->get('member_report_sales_tax')) { ?><td class="right hidden-very-small"><?php echo $sale['total']; ?></td><?php } ?>
							  <td class="right action"><a href="<?php echo $sale['href']; ?>" class="button smaller"><i class="fa fa-eye"></i><?php echo $button_view; ?></a></td>
							</tr>
							<?php } ?>
							<tr class="hidden-very-small">
							  <td class="center"></td>
							  <td class="left hidden-small"></td>
							  <td class="right" colspan="2"><?php if ($this->config->get('member_report_sales_commission') || $this->config->get('member_report_sales_tax')) { ?>* <?php echo $text_total_calculation; ?><?php } ?></td>
							  <td class="center hidden-small"><b><?php echo $totals['products']; ?></b></td>
							  <td class="right"><b><?php echo $totals['sales']; ?></b></td>
							  <?php if ($this->config->get('member_report_sales_commission')) { ?>
                              <td class="right hidden-small"><b>- <?php echo $totals['commissions']; ?></b></td>
                              <?php } ?>
							  <?php if ($this->config->get('member_report_sales_tax')) { ?>
							  <?php if ($this->config->get('member_report_sales_tax_add')) { ?>
							  <td class="right hidden-small"><b><?php echo $totals['tax']; ?></b></td>
							  <?php } else {?>
							  <td class="right hidden-small"><b>- <?php echo $totals['tax']; ?></b></td>
							  <?php } ?>
							  <?php } ?>
							  <?php if ($this->config->get('member_report_sales_commission') || $this->config->get('member_report_sales_tax')) { ?>
                              <td class="right"><b><?php echo $totals['grand']; ?></b></td>
                              <?php } ?>
							  <td class="right"></td>
							</tr>
						  </tbody>
						</table>

						<div class="pagination"><?php echo $pagination; ?></div>

					<?php } else { ?>

						<div class="information">
							<p><?php echo $text_empty; ?></p>
							<span class="icon"><i class="fa fa-info-circle"></i></span>
						</div>

					<?php } ?>

					</div>

                </div>
                <?php echo $content_bottom; ?>
            </div>
        </div>
        <?php echo $column_right; ?>
    </div>
</div>
<?php echo $footer; ?>
